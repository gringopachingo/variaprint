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

require_once("../inc/config.php");
require_once("../inc/encrypt.php");
require_once("../inc/functions-global.php");

// session_save_path("/www/tmp");
session_name("mssid");
session_start();

if ($a_form_vars[action] == "reset") {

	if ($a_form_vars[username] != "") {
		// lookup email address in user table
		if (ereg("@",$a_form_vars[username])) {
			$where = "Email='$a_form_vars[username]'";
		} else {
			$where = "Username='$a_form_vars[username]'";
		}
			
		$sql = "SELECT * FROM AdminUsers WHERE $where";
		$nResult = dbq($sql);
		$rLength = mysql_num_rows($nResult);
		$a_result = mysql_fetch_assoc($nResult);
		
		$email = $a_result['Email'];
		
		if ($rLength > 0) {
			// create random code to verify by
			srand((double)microtime()*1587315); 
			$resetCode = encrypt($a_form_vars['username'] . rand(100000,9999999999), $key2);		
			$resetCodeEnc = encrypt($resetCode, $key_2);
			
			
			$sql = "UPDATE AdminUsers SET Password='$resetCodeEnc' WHERE $where";
			dbq($sql);
			
			$message = "To reset your password go to:\nhttps://{$cfg_secure_url}{$cfg_secure_dir}{$cfg_sub_dir}admin/resetpswd.php?rc=". urlencode($resetCode);
	
			$recipient = $email ;
			$subject = "Your password" ;
			
			$headers  = "Return-Path: $email\n";
			$headers .= "To: $email\n";
			$headers .= "MIME-version: 1.0\n";
			$headers .= "X-Mailer: The password mailer\n";
			$headers .= "X-Sender: \n";
			
			$headers .= "From: Reset Password <$cfg_system_from_email>\n";
			$headers .= "Content-type: text/plain\n";
			
			// and now mail it
			mail($recipient, $subject, $message, $headers);
			
			$_SESSION['show_alert'] = 1;
			$_SESSION['alert_msg'] = "<b>Congratulations!</b> Your password has been reset successfully. <br><br>
			Check your $email email for instructions on creating your new password.
			";
			
		} else {
			$_SESSION['show_alert'] = 1;
			$_SESSION['alert_msg'] = "We couldn't find your account. <br><br>
			Double check the email address or username you entered, or 
			<a href=\"createaccount.php\">create a new account</a>. ";
		}
		
	} else {
		// You must enter a username or email address
		$_SESSION['show_alert'] = 1;
		$_SESSION['alert_msg'] = "You must enter a username or email address to reset your password.";
	}
/*	*/

} elseif ($a_form_vars["p"] == "reset") {
//	exit("reset");
}


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form1" method="post" action="forgotpswd.php">
  <?php if ($_SESSION['show_alert'] == "1") { 
  $_SESSION['show_alert'] = "0";
  ?>
  <table height="53" border="0" align="center" cellpadding="10" cellspacing="10" bgcolor="#FFCC33">
    <tr> 
      <td class="text"><?php print($_SESSION['alert_msg']); ?></td>
    </tr>
  </table>
  <?php   } 

  ?>
  <br>
  <table width="552" height="39" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr valign="bottom">
      <td width="432" class="text">&laquo; <a href="http://www.variaprint.com/">go to about VariaPrint site</a></td>
      <td width="120" align="right"><img src="images/vp-logo.gif" width="120" height="25"></td>
    </tr>
    <tr>    </tr>
  </table>
  <br>
  <br>
  <table width="560" height="40" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="280"><img src="images/login-topleft.gif" width="320" height="49"></td>
      <td width="10" rowspan="3"><img src="images/blue-dot.gif" width="1" height="178"></td>
      <td width="279" bgcolor="#A7CF40"><img src="images/login-topright.gif" width="304" height="49"></td>
    </tr>
    <tr> 
      <td><img src="images/title-forgotpswd.gif" width="320" height="80"></td>
      <td align="right" bgcolor="#A7CF40"> <table border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td class="text"><strong><font color="#FFFFFF">Username or email address</font></strong></td>
            <td><img src="images/spacer.gif" width="5" height="1"></td>
            <td rowspan="3" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td rowspan="3" align="right"><img src="images/blue-dot.gif" width="1" height="80"></td>
          </tr>
          <tr> 
            <td height="26"> <input name="username" type="text" id="username" size="20" style="width:180"></td>
            <td>&nbsp;
            <input type="submit" name="Submit" value="Reset">            &nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2" valign="bottom"><a href="createaccount.php"><img src="images/login-newaccount.gif" width="164" height="10" vspace="5" border="0"></a>
              <input name="action" type="hidden" id="action" value="reset">              <br>              <br>              <img src="images/spacer.gif" width="270" height="1"></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td><img src="images/login-bottomleft.gif" width="320" height="49"></td>
      <td bgcolor="#A7CF40"><img src="images/login-bottomright.gif" width="304" height="49"></td>
    </tr>
  </table>
<br>
  <br>
  <br>
</form>
</body>
</html>
