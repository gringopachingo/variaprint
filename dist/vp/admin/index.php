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


//session_save_path("/www/tmp");
session_name("ms_sid");
session_start();
//session_destroy();

require_once("../inc/functions-global.php");

SecureServerOn(true);

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">

<script language="JavaScript">
<!--
function ff() { 
			<? if (isset($_COOKIE[adminuser])) { ?>
				document.form1.password.focus();
			<? } else { ?>
				document.form1.username.focus();
			<? } ?>
}
//-->


function logoutRefresh(childWindow) {
	childWindow.close();
}



</script> 


</head>

<body onLoad="ff()" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form1" method="post" action="vp.php">
<input type="hidden" name="minutes" value="0">
  <? if ($_SESSION['show_alert'] == "1") { ?>
  <table height="53" border="0" align="center" cellpadding="10" cellspacing="10" bgcolor="#FFCC33">
    <tr> 
      <td class="text"><? print($_SESSION['alert_msg']); $_SESSION['alert_msg'] = ""; $_SESSION['show_alert'] = 0;?></td>
    </tr>
  </table>
  <br>
  <? } ?>
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
      <td width="10" rowspan="3"><img src="images/blue-dot.gif" width="1" height="198"></td>
      <td width="279" bgcolor="#A7CF40"><img src="images/login-topright.gif" width="304" height="49"></td>
    </tr>
    <tr> 
      <td><img src="images/login-title.gif" width="320" height="100"></td>
      <td align="right" bgcolor="#A7CF40"> <table border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td><img src="images/login-lbl-username.gif" width="58" height="10"></td>
            <td><img src="images/spacer.gif" width="5" height="1"></td>
            <td colspan="3"><img src="images/login-lbl-password.gif" width="57" height="10"></td>
            <td rowspan="3" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td rowspan="3" align="right"><img src="images/blue-dot.gif" width="1" height="100"></td>
          </tr>
          <tr> 
            <td> <input name="username" type="text" id="username" style="width:90" value="<? print($_COOKIE[adminuser]); ?>" size="10"></td>
            <td>&nbsp;&nbsp;</td>
            <td> <input name="password" type="password" id="password" size="10" style="width:90"></td>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td>              <input name="imageField" type="image" src="images/login-button.gif" width="48" height="20" border="0"> 
            </td>
          </tr>
          <tr> 
            <td colspan="5" valign="bottom">
            	<input name="action" type="hidden" id="action" value="login">
			<? if ($_SESSION['already_logged_in']) { 
				$_SESSION['already_logged_in'] = 0;
			?>
				<input type="checkbox" value="true" name="override"> <span class="text">Override login</span>
			<? } else { ?>
				<a href="createaccount.php"><img src="images/login-newaccount.gif" width="164" height="10" vspace="5" border="0"></a>
			  <br>
            <a href="forgotpswd.php"><img src="images/forgotpswd.gif" width="141" height="12" hspace="1" border="0"></a><br>
			<img src="images/spacer.gif" width="270" height="1">
			<? } ?> </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td><img src="images/login-bottomleft.gif" width="320" height="49"></td>
      <td bgcolor="#A7CF40"><img src="images/login-bottomright.gif" width="304" height="49"></td>
    </tr>
  </table>
  <p>&nbsp;</p>
  <p>
  </p>
</form>
</body>
</html>
