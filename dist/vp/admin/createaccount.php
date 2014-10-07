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


session_name("mssid");
session_start();
//session_destroy();

//session_name("createaccount");
//session_start();

$_SESSION = array_merge($_SESSION, $_POST);

if (isset($_POST['name']) && !isset($_SESSION[firstname]) && !isset($_SESSION[lastname]))
	list($_SESSION[firstname], $_SESSION[lastname]) = explode(" ",$_POST['name']);

//print_r($_SESSION);

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Create Account</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form1" method="post" action="create_account.php">
  <?php if ($_SESSION['show_alert'] == "1") { ?>
  <table height="53" border="0" align="center" cellpadding="10" cellspacing="10" bgcolor="#FFCC33">
    <tr> 
      <td class="text"><?php print($_SESSION['alert_msg']); ?></td>
    </tr>
  </table>
  <?php
  	$_SESSION['show_alert'] = 0;
  	$_SESSION['alert_msg'] = "";
	} ?>
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
      <td width="10" rowspan="3"><img src="images/blue-dot.gif" width="1" height="418"></td>
      <td width="279" bgcolor="#A7CF40"><img src="images/login-topright.gif" width="304" height="49"></td>
    </tr>
    <tr> 
      <td><img src="images/createaccount-title.gif" width="320" height="320"></td>
      <td align="right" bgcolor="#A7CF40"> <table border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="128" height="1" valign="bottom" class="text"><font color="#FFFFFF"><strong>First 
              name</strong></font></td>
            <td width="8" height="1">&nbsp;</td>
            <td width="128" height="1" valign="bottom" class="text"><font color="#FFFFFF"><strong>Last 
              name</strong></font></td>
            <td width="22" rowspan="18" align="right">&nbsp;&nbsp;&nbsp;&nbsp; </td>
            <td width="1" rowspan="18" align="right"><img src="images/blue-dot.gif" width="1" height="320"></td>
          </tr>
          <tr> 
            <td height="1"> <input name="create_first_name" type="text" id="create_first_name5" style="width:120" value="<?php print($_SESSION['firstname']); ?>" size="14"> 
            </td>
            <td height="1">&nbsp;&nbsp;</td>
            <td height="1"><input name="create_last_name" type="text" id="create_last_name5" style="width:120" value="<?php print($_SESSION['lastname']); ?>" size="14"></td>
          </tr>
          <tr> 
            <td height="1" colspan="3" class="text"><img src="images/spacer.gif" width="1" height="5"></td>
          </tr>
          <tr> 
            <td height="1" valign="bottom" class="text"><font color="#FFFFFF"><strong>Company</strong></font></td>
            <td height="1">&nbsp;</td>
            <td height="1" valign="bottom" class="text"><font color="#FFFFFF"><strong>Email</strong></font></td>
          </tr>
          <tr> 
            <td height="1"><input name="create_company" type="text" id="create_company5" style="width:120" value="<?php print($_SESSION['company']); ?>" size="14"></td>
            <td height="1">&nbsp;</td>
            <td height="1"><input name="create_email" type="text" id="create_email5" style="width:120" value="<?php print($_SESSION['email']); ?>" size="14"></td>
          </tr>
          <tr> 
            <td height="1" colspan="3" class="text"><img src="images/spacer.gif" width="1" height="5"></td>
          </tr>
          <tr> 
            <td height="1" valign="bottom" class="text"><font color="#FFFFFF"><strong>Phone</strong></font></td>
            <td height="1">&nbsp;</td>
            <td height="1">&nbsp;</td>
          </tr>
          <tr> 
            <td height="1"><input name="create_phone" type="text" id="create_phone5" style="width:120" value="<?php print($_SESSION['phone']); ?>" size="14"></td>
            <td height="1">&nbsp;</td>
            <td height="1">&nbsp;</td>
          </tr>
          <tr> 
            <td height="1" colspan="3"><img src="images/spacer.gif" width="1" height="5"></td>
          </tr>
          <tr> 
            <td height="1" colspan="3" class="text"><img src="images/spacer.gif" width="1" height="10"></td>
          </tr>
          <tr> 
            <td height="1" valign="bottom" class="text"><font color="#FFFFFF"><strong>Username</strong></font></td>
            <td height="1">&nbsp;</td>
            <td height="1" valign="bottom" class="text"><font color="#FFFFFF"><strong>Password</strong></font></td>
          </tr>
          <tr> 
            <td height="1"><input name="create_username" type="text" id="create_username6" style="width:120" value="<?php print($_SESSION['username']); ?>" size="14"></td>
            <td height="1">&nbsp;</td>
            <td height="1"><input name="create_password" type="password" id="create_password8" style="width:120" size="14"></td>
          </tr>
          <tr> 
            <td height="1" colspan="3" class="text"><img src="images/spacer.gif" width="1" height="5"></td>
          </tr>
          <tr> 
            <td height="1" valign="bottom" class="text">&nbsp;</td>
            <td height="1">&nbsp;</td>
            <td height="1" valign="bottom" class="text"><font color="#FFFFFF"><strong>Confirm&nbsp;password</strong></font></td>
          </tr>
          <tr> 
            <td height="1">&nbsp;</td>
            <td height="1">&nbsp;</td>
            <td height="1"><input name="create_password2" type="password" id="create_password25" style="width:120" size="14"></td>
          </tr>
          <tr> 
            <td height="1" colspan="3"><img src="images/spacer.gif" width="1" height="5"></td>
          </tr>
          <tr> 
            <td height="1">&nbsp;</td>
            <td height="1">&nbsp;</td>
            <td height="1"><input type="submit" name="Submit2" value="Create Account"> 
              <input name="action" type="hidden" id="action6" value="create_account"></td>
          </tr>
          <tr> 
            <td height="1" colspan="3" valign="bottom"><a href="index.php"><img src="images/createaccount-loginlink.gif" width="120" height="24" border="0"></a></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td><img src="images/login-bottomleft.gif" width="320" height="49"></td>
      <td bgcolor="#A7CF40"><img src="images/login-bottomright.gif" width="304" height="49"></td>
    </tr>
  </table>
  <p>&nbsp; </p>
</form>
</body>
</html>
