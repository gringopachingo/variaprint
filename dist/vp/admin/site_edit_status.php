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
$mssid = session_id();

require_once("../inc/config.php");
require_once("../inc/functions-global.php");
require_once("inc/popup_log_check.php");


$_SESSION[site] = $_SESSION['site'];

if ($_SESSION[site] == "" || $_SESSION['user_id'] == "" || !isset($_SESSION['user_id']) )  { 
	print("
		<script language=\"javascript\">
			window.opener.location.reload(0) // location.reload(true)
			window.close()
		</script>
	"); 
	exit;
}

if ($a_form_vars['action'] == "save") {
	if ($a_form_vars['site_status'] == "" ) { $a_form_vars['site_status'] = "Untitled Order Site"; }
	$sql = "UPDATE Sites SET Status='$a_form_vars[site_status]' WHERE ID='$_SESSION[site]'";
	dbq($sql);
	
	$_SESSION['site_status'] = $a_form_vars['site_status'];
	
	print("
		<script language=\"javascript\">
			window.opener.location.reload(0) // location.reload(true)
			window.close()
		</script>
	"); 
	exit;

}





$sql = "SELECT Status FROM Sites WHERE ID='$_SESSION[site]'";
$r_result = dbq($sql);
$a_result = mysql_fetch_assoc($r_result);
$site_status = $a_result['Status'];

if ($site_status == "Live") {
	$live_check = "checked";
} else {
	$inactive_check = "checked";
}

?>
<html>
<head>
<?php
print($header_content);
?>
<title>Change Site Status</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/bkg-groove.gif">
<table width="100%" height="90%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"> 
      <form name="form1" method="post" action="">
        <table width="261" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td colspan="9" class="text"><p><span class="text">Please review your order site and 
                make sure everything it is setup correctly before making it live. 
              </span></p>
              <p>Changes made under the Site tab must be published before taking
              effect on the live site. To publish, go to the Site tab and select
                Publish from the menu to the right of the title and then press
                Go.</p></td>
          </tr>
          <tr> 
            <td colspan="9">&nbsp;</td>
          </tr>
          <tr> 
            <td><input type="radio" name="site_status" value="Live" <?php print($live_check); ?>></td>
            <td class="text">Live</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><input type="radio" name="site_status" value="Inactive" <?php print($inactive_check); ?>></td>
            <td class="text">Inactive</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><input name="Submit2" type="button" class="text" onClick="window.close()" value="Cancel"></td>
            <td>&nbsp;&nbsp;</td>
            <td><input name="Submit" type="submit" class="text" value="Save"></td>
          </tr>
        </table>
        <input name="action" type="hidden" id="action" value="save">
      </form></td>
  </tr>
</table>
</body>
</html>
