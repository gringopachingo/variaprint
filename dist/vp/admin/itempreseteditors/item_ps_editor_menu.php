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


require_once("../../inc/config.php");
require_once("../../inc/functions-global.php");
require_once("../inc/functions.php");
if (!$_SESSION["privilege_items_properties"]) {
	require_once("../inc/popup_log_check.php");
}

//session_save_path("/www/tmp");
session_name("mssid");
session_start();
$mssid = session_id();

	$item_id = $a_form_vars[item_id];

	$edit  	 = $a_form_vars[edit];
	
//	$a_editors[] = "template";
//	$a_editors[] = "help";
	$a_editors[] = "pricing";
	$a_editors[] = "imposition";
//	$a_editors[] = "supplier";
	$a_editors[] = "shipping";
	
	foreach ($a_editors as $editor) {
		if ($edit == $editor) {
			$menu .= "<td><img title=\"You are editing $editor\" src=\"../images/tab-$editor-on.gif\" border=\"0\"></td>\n";
		} else {
			$menu .= "<td><a title=\"Edit $editor\" href=\"item_presets_editor.php?edit=$editor\" target=\"_top\"><img src=\"../images/tab-$editor-off.gif\" border=\"0\"></a></td>\n";
		}
	}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?
print($header_content);
?>
<title>Edit Item</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#eeeeee" background="../images/tab-bkg.gif" leftmargin="10" topmargin="0" marginwidth="10" marginheight="0">
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
<? print($menu); ?>
  </tr>
</table>
</body>
</html>
