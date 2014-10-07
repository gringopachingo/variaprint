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

	if (!$_SESSION["privilege_items_properties"] && $a_form_vars["edit"] == "properties") {
		require_once("../inc/popup_log_check.php");
	}

	if (!$_SESSION["privilege_items_template"] && $a_form_vars["edit"] == "template") {
		require_once("../inc/popup_log_check.php");
	}
	
	if ($a_form_vars[item_id] == "" || !isset($a_form_vars[item_id])) {
		$item_id = $_SESSION["item_id"] ; 
	} else {
		$item_id = $a_form_vars[item_id] ;
		$_SESSION["item_id"]  = $item_id; 
	}
	
	$edit  	 = $a_form_vars[edit];
	
	$sql = "SELECT Name FROM Items WHERE ID='$item_id'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	$item_name = $a_result['Name'];
	
	$editor_to_load = "item_edit_" . $a_form_vars['edit'] . ".php?edit=$edit&itemid=$item_id&item_name=$item_name";
	$menu_page = "item_editor_menu.php?edit=$edit&item_id=$item_id";
	
	if ($a_form_vars['edit'] == "template") { $scroll = "scrolling=\"NO\"";  }
	
	if ($item_id == "") {
		exit("Error. Item ID not found.");
	}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<?
print($header_content);
?>
<title>Edit Item &quot;<? print($item_name) ; ?>&quot;</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<frameset rows="30,*" frameborder="NO" border="0" framespacing="0">
  <frame src="<? print($menu_page) ?>" name="topFrame" scrolling="NO" noresize >
  <frame src="<? print($editor_to_load) ?>" name="main" scrolling="NO">
</frameset>
<noframes><body>

</body></noframes>
</html>
