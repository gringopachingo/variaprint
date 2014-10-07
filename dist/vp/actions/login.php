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


$sql = "SELECT ID,Username,Password,Status,ApprovalCode,DateLastApprovalNotice FROM Users WHERE Username='$a_form_vars[login_username]' AND SiteID='$_SESSION[site]'";
$r_result = dbq($sql);

if (mysql_num_rows($r_result) > 0) {
	$a_user = mysql_fetch_assoc($r_result);
	if ($a_user['Status']=='WaitingForApproval') {
		$error = 2;
		if ($a_user['DateLastApprovalNotice']+43200>time()) {
			$to_email = $a_site_settings["AccountApprovalEmail"];
			$this_approval_id = $a_user['ApprovalCode'];
			$message = "There is still a buyer account that needs to be approved. To approve go to 
https://{$cfg_secure_url}{$cfg_secure_dir}{$cfg_sub_dir}aa.php?id=$this_approval_id ";
			$sender_name = "Auto-Reminder";
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
			
			$sql = "UPDATE Users SET DateLastApprovalNotice='".time()."' WHERE ID='$a_user[ID]' AND SiteID='$_SESSION[site]'";
			dbq($sql);
		}
		
		$_SESSION['alert_msg'] = "This account is waiting for access approval. You will be notified when your account has been approved if you included a correct email address.";
	} elseif ($a_user['Status']=='Denied') {
		$error = 2;
		$_SESSION['alert_msg'] = "This account has been denied access. If you feel you are getting this message in error, please contact us.";
	} elseif ($a_user['Status']=='Inactive') {
		$error = 2;
		$_SESSION['alert_msg'] = "This account has been inactivated.";
	}
	if ( $a_user['Password'] == encrypt($a_form_vars['login_password'],$a_form_vars['login_password'] ) && $error != 2) {
		$time = time();
		$sql = "UPDATE Users SET DateLastLogin='$time', LastSID='$ossid' WHERE Username='$a_form_vars[login_username]' AND SiteID='$_SESSION[site]'";
		dbq($sql);
		$_SESSION['user_id'] = $a_user['ID'];
		$_SESSION['logged_in'] = 1;
		
		if ( isset($_SESSION[os_page_afterlogin])) {
			$_SESSION['os_page'] = $_SESSION[os_page_afterlogin];
			header("Location: vp.php?site=$_SESSION[site]&ossid=$_SESSION[ossid]&os_page=$_SESSION[os_page_afterlogin]");
		} else {
			$_SESSION['os_page'] = "catalog";
			header("Location: vp.php?os_page=catalog&site=$_SESSION[site]&ossid=$_SESSION[ossid]");
		}
	} elseif ($error!=2)  {
		$error = 1;
	}
	
} else {
	$error = 1;
}	

if ($error == 2) {
	$_SESSION["skiplogin"] = false;
	$_SESSION['os_page'] = "login";
	$_SESSION['show_alert'] = 1;
} elseif ($error == 1) {
	$_SESSION["skiplogin"] = false;
	$_SESSION['os_page'] = "login";
	$_SESSION['alert_msg'] = "Incorrect username or password.";
	$_SESSION['show_alert'] = 1;
}

?>
