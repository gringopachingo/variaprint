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
require_once("inc/functions.php");
require_once("inc/popup_log_check.php");





if ( $a_form_vars['action'] == "Create" ) {
	print("creating new site...<br><br>");
	
	if ( $a_form_vars[site_name] == "" ) { $a_form_vars[site_name] = "Untitled Order Site" ; }
	
	$sql = "SELECT * FROM Sites WHERE ID='$a_form_vars[site_based_on]'";
	$r_result = dbq($sql);
	$a_site_based_on = mysql_fetch_assoc($r_result);
	
	$sql = "INSERT INTO Sites SET 
		Status='Inactive', 
		Name='" . addslashes($a_form_vars[site_name]) . "', 
		MasterUID='$_SESSION[user_id]', 
		Settings='" . addslashes($a_site_based_on[Settings]) . "',
		SettingsTmp='" . addslashes($a_site_based_on[SettingsTmp]) . "',
		ItemGroups='" . addslashes($a_site_based_on[ItemGroups]) . "',
		OrderStatuses='" . addslashes($a_site_based_on[OrderStatuses]) . "',
		ShippingID='" . addslashes($a_site_based_on[ShippingID]) . "',
		ApprovalID='" . addslashes($a_site_based_on[ApprovalID]) . "'
	";

	dbq($sql);
	
	$last_insert_id = db_get_last_insert_id();
	
	if ( $a_form_vars['site_copy_items'] == "yes") {
		$sql = "SELECT * FROM Items WHERE SiteID='$a_form_vars[site_based_on]'";
		print("copying items...<br><br>");
		$r_result = dbq($sql);
		while ( $a_item = mysql_fetch_assoc($r_result) ) {
			$sql = "INSERT INTO Items SET 
				SiteID='$last_insert_id', 
				Name='" . addslashes($a_item[Name]) . "',
				GroupID='" . addslashes($a_item[GroupID]) . "',
				MasterUID='" . addslashes($a_item[MasterUID]) . "',
				ImpositionID='" . addslashes($a_item[ImpositionID]) . "',
				Pricing='" . addslashes($a_item[Pricing]) . "',
				Template='" . addslashes($a_item[Template]) . "'
			";
			dbq($sql);
		}
	}
	
	$olddir = $cfg_base_dir . "_sites/$a_form_vars[site_based_on]";
	
	if ( !file_exists($olddir) ) { $olddir = $cfg_base_dir . "_sites/500"; }
	$cmd = CLI_CP." -R $olddir $cfg_base_dir" . "_sites/$last_insert_id";
	$cp = `$cmd`;
	
	if ( !file_exists( $cfg_base_dir . "_sites/" . $last_insert_id) ) {
		exit("Error creating new order site directory. Please contact Prevario to correct this. 
		Your new order site will not work properly until this is corrected.");
	}
	
	print("
	<script language=\"javascript\">
		window.opener.location.href = 'vp.php?site=$last_insert_id&action=home'
		window.close()
	</script>
	");
	
	exit;
}

// $_SESSION[user_id] = "1";

$sql = "SELECT ID,Name FROM Sites WHERE MasterUID='$_SESSION[user_id]'";
$r_result = dbq($sql);

$sql = "SELECT ID,Name FROM Sites WHERE Template='true' ORDER BY Name";
$r_result2 = dbq($sql);

$site_select = "
<select name=\"site_based_on\" id=\"site_based_on\">
";
while ( $a_site = mysql_fetch_assoc($r_result2) ) { $site_select .= "<option value=\"$a_site[ID]\">[".$a_site[Name]."]</option>\n"; }
//<option value=\"500\">[Default] </option>\n
while ( $a_site = mysql_fetch_assoc($r_result) ) { $site_select .= "<option value=\"$a_site[ID]\">$a_site[Name]</option>\n"; }
$site_select .= "</select>";

?>
<html>
<head>
<?php
print($header_content);
?>
<title>New Order Site</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/bkg-groove.gif">
<table width="100%" height="90%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center">
<form name="form1" method="get" action="<?php print($_SERVER['SCRIPT_NAME']) ; ?>">
        <table width="342" border="0" cellspacing="0" cellpadding="5">
          <tr> 
            <td colspan="2" nowrap class="title"><strong>Create new order site</strong></td>
          </tr>
          <tr> 
            <td width="122" nowrap class="text">Name:</td>
            <td width="200"><input name="site_name" type="text" id="site_name2"></td>
          </tr>
          <tr> 
            <td nowrap class="text">Based on order site:</td>
            <td><?php print($site_select); ?></td>
          </tr>
          <tr> 
            <td colspan="2" class="text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input name="site_copy_items" type="checkbox" id="site_copy_items" value="yes">
              Copy items from selected order site</td>
          </tr>
          <tr align="right"> 
            <td colspan="2"><input name="button" type="button" onClick="window.close()" value="Cancel">
            <input type="submit" name="action" value="Create"></td>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
</body>
</html>
