<?php

// *******************************************************
// 
// VariaPrint 1.0 web-to-print system
//
// Copyright 2001-2014 Luke Miller
//
// This file is part of VariaPrint, a web-to-print PDF personalization and 
// ordering system.
// 
// VariaPrint is free software: you can redistribute it and/or modify it under 
// the terms of the GNU General Public License as published by the Free Software 
// Foundation, either version 2 of the License, or (at your option) any later 
// version.
// 
// VariaPrint is distributed in the hope that it will be useful, but WITHOUT ANY 
// WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR 
// A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License along with 
// VariaPrint. If not, see http://www.gnu.org/licenses/.
// 
//
// Forking, porting, updating, and contributing back to this project is welcomed.
// 
// If you find any of this useful, let me know at the address below...
//
// https://github.com/lukedmiller/variaprint
//
// http://www.variaprint.com/
//
// *******************************************************


	$error = 0;
	if (
		!IsSet($a_form_vars[create_last_name]) || $a_form_vars[create_last_name] == "" ||
		!IsSet($a_form_vars[create_first_name]) || $a_form_vars[create_first_name] == "" ||
		!IsSet($a_form_vars[create_email]) || $a_form_vars[create_email] == "" ||
		!IsSet($a_form_vars[create_username]) || $a_form_vars[create_username] == "" ||
		!IsSet($a_form_vars[create_password]) || $a_form_vars[create_password] == "" ||
		!IsSet($a_form_vars[create_password2]) || $a_form_vars[create_password2] == "" 
	//	!IsSet($a_form_vars[create_phone]) || $a_form_vars[create_phone] == "" 
	) 
	{
		$error = 1;
		// $_SESSION['alert_msg'] .= "<br><br>";
		$_SESSION['alert_msg'] .= "You must fill in all fields to create an account. <br><br>";
	}
	
	
	
	if ( $a_form_vars['create_password'] != $a_form_vars['create_password2']) {
		$error = 1;
		$_SESSION['alert_msg'] .= "Your passwords don't match.<br><br>";
	
	} else { 
		if ( ereg(" ",$a_form_vars['create_password'])) {
			$error = 1;
			$_SESSION['alert_msg'] .= "You cannot have any spaces in your password.<br><br>";
		}
		if ( strlen($a_form_vars['create_password']) < 6 ) {
			$error = 1;
			$_SESSION['alert_msg'] .= "Your password must be more than six characters long.<br><br>";
		} 
	}
	
	if ( ereg(" ",$a_form_vars['create_username'])) {
		$error = 1;
		$_SESSION['alert_msg'] .= "You cannot have any spaces in your username.<br><br>";
	} elseif ($a_form_vars['create_username'] == "") {
		$error = 1;
		$_SESSION['alert_msg'] .= "You must provide a username.<br><br>";
	
	}
		
	// see if the username is already used
	$sSQL = "SELECT * FROM Users WHERE Username='$a_form_vars[create_username]' AND SiteID='$_SESSION[site]'";
	$nResult = dbq($sSQL);
	$nLength = mysql_num_rows($nResult);
	if ($nLength != 0) {
		$error = 1;
		$_SESSION['alert_msg'] .= "The username \"$a_form_vars[create_username]\" is already used. Please enter a new username.<br><br>";
	} 
	
		
	// see if the account already exists
	$sSQL = "SELECT * FROM Users WHERE email='$a_form_vars[create_email]' AND SiteID='$_SESSION[site]'";
	$nResult = dbq($sSQL);
	$nLength = mysql_num_rows($nResult);
	
	if ($nLength != 0) {
		$error = 1;
		$_SESSION['alert_msg'] .= "An account with this email already exists. Click the forgot password link to get login information emailed to you.<br><br>";
	}
	
	// if no errors, create the account and log em in
	if (!$error) {
		
		if ($a_site_settings["RequireAccountApproval"] == "checked") {
			$status = "WaitingForApproval";
			// create a unique id 
			srand((double)microtime()*1000000); $rand = rand(1000000,9999999999);
			$this_approval_id = $rand.time();

			$approval_email = $to_email = $a_site_settings["AccountApprovalEmail"];

			$message = "There is a new buyer account that needs to be approved. To approve go to 
https://{$cfg_secure_url}{$cfg_secure_dir}{$cfg_sub_dir}aa.php?id=$this_approval_id ";
			$sender_name = "Auto-Notice";
			$sender_email = $cfg_system_from_email;
			
			$subject = "Approve Buyer Account" ;
			$headers  = "Return-Path: $sender_email\n";
			$headers .= "To: $to_email\n";
			$headers .= "MIME-version: 1.0\n";
			$headers .= "X-Mailer: PHP Mailer\n";
			$headers .= "X-Sender: $sender_email\n";
			$headers .= "From: $sender_name <$sender_email>\n";

			// and now mail it 
			mail($to_email, $subject, $message, $headers);
			
		//	$this_approval_id = encrypt($this_approval_id,$this_approval_id);
			
			$_SESSION['alert_msg'] = "User accounts must be approved before accessing this order site. You will receive an email notice when your account is approved.";
			$_SESSION['show_alert'] = 1;
		} else {
			$status = "Active";
			
		}

		// encrypt password before writing to DB
		$password = $a_form_vars[create_password];
		$password = encrypt($password, $password);
		$now = time();
		$sql = "INSERT INTO Users 
			SET DateCreated='$now', 
			SiteID='$_SESSION[site]', 
			Status='$status', 
			DateLastApprovalNotice='$now',
			ApprovalCode='$this_approval_id', 
			ApprovalEmail='$approval_email', 
			Username='" . addslashes($a_form_vars[create_username]) . "', 
			Password='" . addslashes($password) . "', 
			Firstname='" . addslashes($a_form_vars[create_first_name]) . "', 
			Lastname='" . addslashes($a_form_vars[create_last_name]) . "', 
			Phone='" . addslashes($a_form_vars[create_phone]) . "', 
			Email='" . addslashes($a_form_vars[create_email]) . "'";
		dbq($sql);
//		mail(
		if ($a_site_settings["RequireAccountApproval"] != "checked") {
			//get user ID
			$sql = "SELECT * FROM Users WHERE Username='$a_form_vars[create_username]' AND SiteID='$_SESSION[site]'";
			$nResult = dbq($sql);
			$nLength = mysql_num_rows($nResult);
			$aUser = mysql_fetch_array($nResult);

			$_SESSION['user_id'] = $userID = $aUser[ID];
			$_SESSION['logged_in'] = 1;
			// $_SESSION['user_id'] = $userID;
		
			// login  here
			$time = time();
			$sql = "UPDATE Users SET LastSID='$ossid', DateLastLogin='$time' WHERE ID='$userID'";
			$nUpdate = dbq($sql);
		}
	} else {
	//	exit("there was an error. $_SESSION[alert_msg]");
		$_SESSION['show_alert'] = 1;
	}
?>
