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
require_once("../inc/functions-global.php");

//session_save_path("/www/tmp");
session_name("mssid");
session_start();

if ($_POST['action'] == "save") {
	// READ FROM DB
	$sql = "SELECT * FROM Sites WHERE ID='$_SESSION[site]'";
	$nResult = dbq($sql);
	if ( mysql_num_rows($nResult) == 0 ) {  
		exit("Error. No Site Selected. (config)");  
	} 
	$aSite = mysql_fetch_assoc($nResult);
	$sSiteSettings = $aSite['SettingsTmp'];
	$aXMLTree = xml_get_tree($sSiteSettings);

	print("Saving...");	
	foreach ($a_form_vars as $f_key=>$f_val) {
		$aXMLTree = xml_update_value("properties/property:$f_key","CDATA", $f_val,$aXMLTree);
	}
	
	$sXML = xml_make_tree($aXMLTree);
	$sXML = addslashes($sXML);
		
	$sql = "UPDATE Sites SET SettingsTmp='$sXML' WHERE ID='$_SESSION[site]'";
	dbq($sql, "SAVING SITE PROPERTIES");
	
	print("
	<script language=\"javascript\">
		window.close()
	</script>
	");
	
	exit();
	
	// header("Location: " . $_SERVER['SCRIPT_NAME'] . "?tab=$_SESSION[tab]&action=site_appearance");
}

?><html>
<head>
<title>Save site configuration</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/bkg-groove.gif">
<table width="350" height="92%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><form name="form1" method="post" action="site_save_config.php">
        <p class="text"><strong>You had unsaved changes on the page you just left.</strong><br>
          <br>
          Do you want to save those changes now? </p>
        <p align="right"> 
          <?php
if (is_array($_GET)) {
	foreach($_GET as $k=>$v) {
		$f_hid .= "<input type=\"hidden\" name=\"$k\" value=\"$v\">\n";
	}
}
print($f_hid);
?>
          <input name="action" type="hidden" id="action" value="save">
          <input name="button" type="button" onClick="window.close()" value="Discard">
          <input name="submit" type="submit" value="Save">
          &nbsp;&nbsp;<br>
        </p>
      </form></td>
  </tr>
</table>
</body>
</html>
