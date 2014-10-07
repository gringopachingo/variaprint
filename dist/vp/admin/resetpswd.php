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

$enc_code = encrypt($a_form_vars[rc], $a_form_vars[rc]);
$sql = "SELECT ID,Username FROM AdminUsers WHERE Password='$enc_code'";
$r_result = dbq($sql);
$resetmode = false;
if (mysql_num_rows($r_result) == 1) {
	$a_result = mysql_fetch_assoc($r_result);
	$username = $a_result['Username'];	
	$mode = "reset";
} else {
	$mode = "error";
}



if ($a_form_vars[action] == "reset") {
	
	if ($a_form_vars["password"] == $a_form_vars["password2"]) {
		if (strlen($a_form_vars["password"]) >= 6) {
			$sql = "SELECT ID,Username FROM AdminUsers WHERE Password='$enc_code'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			$user_id = $a_result["ID"] ;
			if ($user_id != "") { 
				$newpassword = encrypt($a_form_vars["password"],$a_form_vars["password"]);
				$sql="UPDATE AdminUsers SET Password='$newpassword' WHERE ID='$user_id'";
				dbq($sql);
				$mode="success";
			} else {
				$show_alert = "1";
				$alert_msg = "There was an unknown error.";
				$mode="error";
				
				print_r($a_form_vars);
			
			}
		} else {
			$show_alert = "1";
			$alert_msg = "Your password must be at least 6 characters long.";
			$mode="reset";
		}
	} else {
		$show_alert = "1";
		$alert_msg = "Your passwords didn't match. Please try again.";
		$mode="reset";
	}
	
	
} 


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form1" method="post" action="resetpswd.php">
  <?php if ($show_alert == "1") { 
  ?>
  <table height="53" border="0" align="center" cellpadding="10" cellspacing="10" bgcolor="#FFCC33">
    <tr> 
      <td class="text"><?php print($alert_msg); ?></td>
    </tr>
  </table>
  <?php   } 

  ?>
    <br>
  <table width="552" height="39" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr valign="bottom">
      <td width="432" class="text">&laquo; <a href="http://www.variaprint.com/">go to about VariaPrint site</a></td>
      <td width="120" align="right"><img src="images/vp-logo.gif" width="120" height="53"></td>
    </tr>
    <tr>    </tr>
  </table>
  <br>
  <br>
  <table width="560" height="40" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="280"><img src="images/login-topleft.gif" width="320" height="49"></td>
      <td width="10" rowspan="3"><img src="images/blue-dot.gif" width="1" height="230"></td>
      <td width="279" bgcolor="#A7CF40"><img src="images/login-topright.gif" width="304" height="49"></td>
    </tr>
    <tr> 
      <td><img src="images/title-resetpswd.gif" width="320" height="132"></td>
      <td align="right" bgcolor="#A7CF40">
	  <?php if ($mode=="reset") { ?>	  <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="30" class="text"><strong><font color="#FFFFFF">Username</font></strong></td>
            <td height="30" class="text"><font color="#FFFFFF"><?php print($username); ?></font></td>
            <td>&nbsp;</td>
            <td rowspan="5" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td rowspan="5" align="right"><img src="images/blue-dot.gif" width="1" height="132"></td>
          </tr>
          <tr>
            <td height="30" class="text"><strong><font color="#FFFFFF">New&nbsp;password</font></strong></td>
            <td height="30"><input name="password" type="password" id="username4" size="10" style="width:90"></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="30" class="text"><strong><font color="#FFFFFF">Confirm&nbsp;password</font></strong></td>
            <td height="30"><input name="password2" type="password" id="username3" size="10" style="width:90">
              <input name="action" type="hidden" id="action7" value="reset">
              <input name="rc" type="hidden" id="rc" value="<?php print($a_form_vars[rc]); ?>">
</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td class="text">&nbsp;</td> 
            <td height="26"><input type="submit" name="Submit" value="Set password">
            </td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td height="1" colspan="3" valign="bottom"><img src="images/spacer.gif" width="270" height="1"></td>
          </tr>
        </table>
	  <?php } elseif ($mode=="success") { ?>
	  <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="26" class="text"><p><strong><font color="#FFFFFF">Your
                  password was  successfully updated.</font></strong><font color="#FFFFFF"><br>
                  <br>
                  Click here to <a href="index.php">login</a>.
                  </font></p>
            </td>
          <td rowspan="2" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td rowspan="2" align="right"><img src="images/blue-dot.gif" width="1" height="132"></td>
        </tr>
        <tr>
          <td height="1" valign="bottom"><img src="images/spacer.gif" width="270" height="1"></td>
        </tr>
      </table>
	  
	  
	  <?php } elseif ($mode=="error") { ?>
	  		
	  <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="26" class="text"><p><strong><font color="#FFFFFF">There was an error. This code may have
            expired. </font></strong></p>
            <p><font color="#FFFFFF">Try <a href="forgotpswd.php">resetting your
            password</a> again.</font></p></td>
          <td rowspan="2" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td rowspan="2" align="right"><img src="images/blue-dot.gif" width="1" height="132"></td>
        </tr>
        <tr>
          <td height="1" valign="bottom"><img src="images/spacer.gif" width="270" height="1"></td>
        </tr>
      </table>
	  <?php } ?>
	  </td>
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
