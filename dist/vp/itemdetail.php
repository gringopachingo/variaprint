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

	include("inc/popup-header.php");
	
//	session_save_path("/www/tmp");
	session_name("ossid");
	session_start();
	$ossid = session_id();
	
	include("inc/config.php");
	require_once("inc/functions-global.php");
//	require_once("inc/functions.php");

	$item_id = $a_form_vars['item'];
	
	
	$sql = "SELECT Name,Description,SmallIconLink,LargeIconLink,LargeShadow FROM Items WHERE ID='$item_id'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	
	if ($a_result['LargeIconLink'] != "") {
		// Use large icon
		$img = "<img src=\"_sites/" . $_SESSION['site'] . "/images/" . $a_result['LargeIconLink'] . "\">";

	} elseif ($a_result['SmallIconLink'] != "") {
		// Use small icon
		$img = "<img src=\"_sites/" . $_SESSION['site'] . "/images/" . $a_result['SmallIconLink'] . "\">";
	}
	
	if ($a_result["LargeShadow"] == "Y") {
		$img = iface_add_drop_shadow($img, $bgcolor);
	}
	$a_result[Description] = htmlentities(utf8ToISO_8859_1($a_result[Description]));
	$a_result[Description] = str_replace("\r\n","\r",$a_result[Description]);
	$a_result[Description] = str_replace("\n\r","\r",$a_result[Description]);
	
	$content = "
	<span class=\"title\">$a_result[Name]</span><br>
	<span class=\"sidetext\">" . str_replace("\r","<br>",$a_result[Description]) . "</span><br><br>
	$img 
	";
	
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Item Detail</title>

<? include("inc/style_sheet.php"); ?>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="<? print($bgcolor); ?>">
<? print($content);  ?> 
</body>
</html>
