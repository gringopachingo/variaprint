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

	include("inc/popup-header.php");


//	session_save_path("/www/tmp");
	session_name("os_sid");
	session_start();
	$os_sid = session_id();
	
	include("inc/config.php");
	require_once("inc/functions-global.php");
	require_once("inc/functions.php");

	// Read it in
	$sql = "SELECT FieldSections FROM Items WHERE ID='$itemid'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	$a_tree = xml_get_tree($a_result['FieldSections']);
	$path = xml_find_node_path("FIELD",$a_tree,$fieldid) . "FIELD:$fieldid/HELP";
	$help_node = xml_get_node_by_path($path, $a_tree);
	$help_img = $help_node['attributes']['IMAGE'];
	$help_text = str_replace("\n", "<br>\n", $help_node['value']);
	$link = "_sites/$_SESSION[site]/images/$help_img";
	if (trim($help_img) != "" && file_exists($link)) {
		$img = "<img src=\"$link\">";
	}


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Item help</title>

<? include("inc/style_sheet.php"); ?>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="<? print($bgcolor); ?>">
<span class="sidetext">
<? print($help_text); ?>
</span> 
<br><br>
<? print($img); ?>
</body>
</html>
