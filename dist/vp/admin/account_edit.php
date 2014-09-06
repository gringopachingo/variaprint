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


session_name("ms_sid");
session_start();
$ms_sid = session_id();

require_once("../inc/config.php");
require_once("../inc/functions-global.php");
require_once("../inc/encrypt.php");
require_once("inc/functions.php");
require_once("inc/popup_log_check.php");

function is_good_password($p1,$p2="") {
	if ($p1==$p2 || $p2 == "") {
		if (strlen($p1)<6) {
			$error = true;
			$errormsg = "Password must be at least 6 characters.";
		}
	} else {
		$error = true;
		$errormsg = "Passwords didn't match.";
	}
	
	if ($error) {
		return $errormsg;
	} else {
		return true;	
	}
}

if ($a_form_vars['action'] == "save") {
	$a = array_find_key_prefix("field_", $a_form_vars, true);
	
	if ($a["passwordold"] != "" && ($a["passwordnew"] != "" || $a["passwordnew2"] != "")) {
		$sql = "SELECT Password FROM AdminUsers WHERE ID='$_SESSION[user_id]'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		
		$oldpswd = encrypt($a["passwordold"], $a["passwordold"]);
		
		if ($oldpswd != $a_result["Password"]) {
			$show_alert = true;
			$alert_msg = "Incorrect password. Password not updated.";
		} else {
			$goodpswd = is_good_password($a["passwordnew"],$a["passwordnew2"]);
			
			if ($goodpswd === true) {
				$pswd = encrypt($a["passwordnew"],$a["passwordnew"]);
				$sql = "UPDATE AdminUsers SET Password='$pswd' WHERE ID='$_SESSION[user_id]'";
				dbq($sql);
				$show_alert = true;
				$alert_msg = "Password successfully updated.";
			} else {
				$show_alert = true;
				$alert_msg = $goodpswd;
			}
		}
	}
	
	$sql = "UPDATE AdminUsers SET Firstname='$a[firstname]', Lastname='$a[lastname]', Company='$a[company]', Phone='$a[phone]', Email='$a[email]' WHERE ID='$_SESSION[user_id]'";
	dbq($sql);
} 


$sql = "SELECT * FROM AdminUsers WHERE ID='$_SESSION[user_id]'";
$r_result = dbq($sql);
$a_user = mysql_fetch_assoc($r_result);



?>
<html>
<head>
<title>Edit Account</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?
print($header_content);
?>
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body topmargin="0" marginheight="0">
<form name="form1" method="post" action="account_edit.php">
  <? if ($show_alert) { ?>
  <table width="480" border="0" align="center" cellpadding="10" cellspacing="0" bgcolor="#FF9900">
    <tr>
      <td height="25" colspan="4" align="center" class="text"><? print($alert_msg);  ?></td>
    </tr>
  </table>
  <? } ?>
  <br>
  <br>
  <table width="480" border="0" align="center" cellpadding="10" cellspacing="0">
    <tr> 
      <td height="25" colspan="3" class="titlebold">Update your user account information</td>
    </tr>
    <tr> 
      <td class="text"><table width="225" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="81" height="25" class="text">Username:</td>
            <td width="104" height="25" class="text"><strong><? print($a_user['Username']); ?></strong></td>
          </tr>
          <tr> 
            <td height="25" class="text">First name: </td>
            <td height="25"> <input name="field_firstname" type="text" id="field_firstname" value="<? print($a_user['Firstname']); ?>" size="20"></td>
          </tr>
          <tr> 
            <td height="25" class="text">Last name: </td>
            <td height="25"> <input name="field_lastname" type="text" id="field_lastname" value="<? print($a_user['Lastname']); ?>" size="20"></td>
          </tr>
          <tr> 
            <td height="25" class="text">Company:</td>
            <td height="25"> <input name="field_company" type="text" id="field_company" value="<? print($a_user['Company']); ?>" size="20"></td>
          </tr>
          <tr> 
            <td height="25" class="text">Phone: </td>
            <td height="25"> <input name="field_phone" type="text" id="field_phone" value="<? print($a_user['Phone']); ?>" size="20"></td>
          </tr>
          <tr> 
            <td height="25" class="text">Email:*</td>
            <td height="25"> <input name="field_email" type="text" id="field_email" value="<? print($a_user['Email']); ?>" size="20"></td>
          </tr>
          <tr> 
            <td height="25" class="text">&nbsp;</td>
            <td height="25"><input type="submit" name="Submit" value="Update">
              <input name="action" type="hidden" id="action" value="save"></td>
          </tr>
        </table></td>
      <td width="290" valign="top" class="text"> <table width="215" border="0" cellpadding="0" cellspacing="1" bgcolor="#000000">
          <tr>
            <td width="241"> 
              <table width="228" border="0" cellpadding="0" cellspacing="10" bgcolor="#FFFFCC">
                <tr> 
                  <td height="25" colspan="2" class="subhead"><strong>Change Password</strong></td>
                </tr>
                <tr> 
                  <td width="69" height="25" class="text">Old&nbsp;password:</td>
                  <td width="104" height="25"> 
                    <input name="field_passwordold" type="password" id="field_passwordold" size="12"></td>
                </tr>
                <tr> 
                  <td height="25" class="text">New&nbsp;password:</td>
                  <td height="25"> 
                    <input name="field_passwordnew" type="password" id="field_passwordnew" size="12"></td>
                </tr>
                <tr> 
                  <td height="25" class="text">Confirm new password:</td>
                  <td height="25"> 
                    <input name="field_passwordnew2" type="password" id="field_passwordnew2" size="12"></td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
                  <td> 
                    <input type="submit" name="Submit2" value="Update"></td>
                </tr>
              </table></td>
          </tr>
        </table>
        
      </td>
    </tr>
    <tr>
      <td colspan="3" class="text">*An inaccurate email address will result in
        your not receiving email notifications for all your order sites and those
        order sites who list you as a vendor or
  approval manager. </td>
    </tr>
  </table>
</form>
<p class="text">&nbsp;</p>
</body>
</html>
