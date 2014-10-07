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


if ($a_form_vars[username] != "") {
	// lookup email address in user table
	if (ereg("@",$a_form_vars[username])) {
		$where = "Email='$a_form_vars[username]' AND SiteID='$_SESSION[site]'";
	} else {
		$where = "Username='$a_form_vars[username]' AND SiteID='$_SESSION[site]'";
	}
		
	$sql = "SELECT * FROM Users WHERE $where";
	$nResult = dbq($sql);
	$rLength = mysql_num_rows($nResult);
	$a_result = mysql_fetch_assoc($nResult);
		
	$email = $a_result['Email'];
	
	if ($rLength == 1) {
		// create random code to verify by
		srand((double)microtime()*1587315); 
		$resetCode = $_SESSION['username'] . rand(100000000,9999999999999);		
		$resetCodeEnc = encrypt($resetCode, $key_2);
				
		$sql = "UPDATE Users SET Password='$resetCodeEnc' WHERE $where";
		dbq($sql);
		
		$message = "To reset your password go to:\nhttps://{$cfg_secure_url}{$cfg_secure_dir}{$cfg_sub_dir}vp.php?os_page=up&rc=$resetCode&ossid=$ossid";

		$recipient = $email ;
		$subject = "Your password" ;
		
		$headers  = "Sender: $email\n";
		$headers  = "Return-Path: $email\n";
		$headers .= "To: $email\n";
		$headers .= "MIME-version: 1.0\n";
		$headers .= "X-Mailer: The password mailer\n";
		$headers .= "X-Sender: $cfg_system_from_email\n";
		
		$headers .= "From: Reset Password <$cfg_system_from_email>\n";
		$headers .= "Content-type: text/plain\n";
		
		// and now mail it
		$sent = mail($recipient, $subject, $message, $headers);
		
		$_SESSION['show_alert'] = 1;
		if ($sent) {
			$_SESSION['alert_msg'] = "<b>Congratulations!</b> Your password has been reset successfully. <br><br>
			Check your $email email for instructions on creating your new password.
			";
		} else {
			$_SESSION['alert_msg'] = "<b>Error.</b> The mail could not be sent.
			";
			
		}
		
	} else {
		$_SESSION['show_alert'] = 1;
		$_SESSION['alert_msg'] = "We couldn't find your account. <br><br>
		Double check the email address or username you entered, or 
		<a href=\"vp.php?site=$_SESSION[site]&os_page=login&ossid=$_SESSION[ossid]\">create a new account</a>. ";
	}
	
} else {
	// You must enter a username or email address
	$_SESSION['show_alert'] = 1;
	$_SESSION['alert_msg'] = "You must enter a username or email address to reset your password.";
}


?>
