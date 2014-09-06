<?

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

	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	require_once("../inc/encrypt.php");
	require_once("inc/functions.php");
	require_once("inc/iface.php");

	session_name("ms_sid");
	session_start();
	$ms_sid = session_id();

	
	foreach ($_SESSION as $k=>$v) session_unset($_SESSION[$k]);
	
	
	$error = 0;
	if (
		!IsSet($a_form_vars[create_last_name]) || $a_form_vars[create_last_name] == "" ||
		!IsSet($a_form_vars[create_first_name]) || $a_form_vars[create_first_name] == "" ||
		!IsSet($a_form_vars[create_email]) || $a_form_vars[create_email] == "" ||
		!IsSet($a_form_vars[create_username]) || $a_form_vars[create_username] == "" ||
		!IsSet($a_form_vars[create_password]) || $a_form_vars[create_password] == "" ||
		!IsSet($a_form_vars[create_password2]) || $a_form_vars[create_password2] == "" 
	) 
	{
		$error = 1;
		$alert_msg .= "You must fill in all fields to create an account. <br><br>";
	}
	
	
	if ( $a_form_vars['create_password'] != $a_form_vars['create_password2']) {
		$error = 1;
		$alert_msg .= "Your passwords don't match.<br><br>";
	}
	
		
	// see if the username is already used
	$sSQL = "SELECT * FROM AdminUsers WHERE Username='$a_form_vars[create_username]'";
	$nResult = dbq($sSQL);
	$nLength = mysql_num_rows($nResult);
	//	print("Length: " . $nLength);
	if ($nLength != 0) {
		$error = 1;
		$alert_msg .= "The username \"$a_form_vars[create_username]\" is already used. 
		Please enter a new username.<br><br>";
	} 
	
		
	// see if the account already exists
	$sSQL = "SELECT * FROM AdminUsers WHERE email='$a_form_vars[create_email]'";
	$nResult = dbq($sSQL);
	$nLength = mysql_num_rows($nResult);
	
	if ($nLength != 0) {
		$error = 1;
		$alert_msg .= "An account with this email already exists. 
		Click the forgot password link to get login information emailed to you.<br><br>";
	}
	
	
	
	// if no errors, create the account and log em in
	if (!$error) {

		// encrypt password before writing to DB
		$password = $a_form_vars[create_password];
		$password = encrypt($password, $password);
		$now = time();
		$sSQL = "INSERT INTO AdminUsers 
			SET DateCreated='$now', 
			Username='" . addslashes($a_form_vars[create_username]) . "', 
			Company='" . addslashes($a_form_vars[create_company]) . "', 
			Password='" . addslashes($password) . "', 
			Firstname='" . addslashes($a_form_vars[create_first_name]) . "', 
			Lastname='" . addslashes($a_form_vars[create_last_name]) . "', 
			Phone='" . addslashes($a_form_vars[create_phone]) . "', 
			Email='" . addslashes($a_form_vars[create_email]) . "'";
		dbq($sSQL);
		
		//get user ID
		$sSQL = "SELECT * FROM AdminUsers WHERE Username='$a_form_vars[create_username]'";
		$nResult = dbq($sSQL);
		$nLength = mysql_num_rows($nResult);
		$aUser = mysql_fetch_array($nResult);
		$userID = $aUser[ID];

		// login  here
		$time = time();
		$sql = "UPDATE AdminUsers SET LastSID='$ms_sid', DateLastLogin='$time' WHERE ID='$userID'";
		$nUpdate = dbq($sql);
		
		$_SESSION['user_id'] = $userID;
		$_SESSION['username'] = $a_form_vars[create_username];
		header("Location: vp.php?action=site_open");
	} else {
	//	session_destroy();
		session_name("createaccount");
		session_start();
		$_SESSION['username'] = $a_form_vars['create_username'];
		$_SESSION['company'] = $a_form_vars['create_company'];
		$_SESSION['firstname'] = $a_form_vars['create_first_name'];
		$_SESSION['lastname'] = $a_form_vars['create_last_name'];
		$_SESSION['phone'] = $a_form_vars['create_phone'];
		$_SESSION['email'] = $a_form_vars['create_email'];

		$_SESSION['show_alert'] = 1;
		$_SESSION['alert_msg'] = $alert_msg; 
		header("Location: createaccount.php");
		exit();
	}
?>