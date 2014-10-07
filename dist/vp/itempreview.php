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


	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    			// Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 	// always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");  	// HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");                          			// HTTP/1.0

	include("inc/popup-header.php");
	session_name("ossid");
	session_start();
	$ossid = session_id();
	
	require_once("inc/config.php");	
	require_once("inc/functions-global.php");	
	require_once("inc/functions.php");	
	require_once("inc/iface.php");
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Item Preview: <? print($a_form_vars[name]); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<? include("inc/style_sheet.php"); ?>

</head>

<body bgcolor="#EEEEEE">
<?

	if (isset($a_form_vars["site"]) && trim($a_form_vars["site"]) != "" && $a_form_vars["mode"] == "ordered") {
		$site = $a_form_vars["site"];
	} else {
		$site = $_SESSION["site"];
	}

	$sql = "SELECT PDFProof FROM Items WHERE ID='$a_form_vars[itemid]' AND SiteID='$site'";
	$r_result = dbq($sql);
	$a_item = mysql_fetch_assoc($r_result);


	if ($a_item["PDFProof"] == "true") {
		$pdf_proof	="
			<a href=\"orderitem_file.php?type=".$a_form_vars['mode']."&id=".$a_form_vars[cartitemid]."&mode=proofpdf&ossid=".$ossid."\" target=\"_blank\">View PDF proof</a>...
			<br><br>
			<a href=\"http://www.adobe.com/products/acrobat/readstep2.html\" target=\"_blank\">Download Adobe Acrobat Reader for PDF</a>...
		";
	}



	$time = time();
	if ($a_form_vars['mode'] == "ordered") {
		$img_link = "orderitem_file.php?type=ordered&id=".$a_form_vars[cartitemid]."&mode=raster&ossid=".$ossid ;//"_orderpdfs/". "_preview_raster.jpg"_preview_pdf.pdf
		$img = "<img src=\"$img_link\">"; 
	} else {
		$img_link = "orderitem_file.php?id=".$a_form_vars[cartitemid]."&mode=raster&ossid=".$ossid ;
		$img = "<img src=\"$img_link\">"; 
	}

	
	$content	= "$img";
	$content	= iface_add_drop_shadow($content,"#EEEEEE") ;
	$content	= "
	<span class=\"subtitle\">$a_form_vars[name]</span><br><br>
	
	<table width=\"570\" cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td valign=\"top\" width=\"430\">
				$content
			</td>
			<td valign=\"top\" class=\"text\" width=\"140\">
				$pdf_proof 
			</td>
		</tr>
	</table>	
	";
	
	
	print($content);
?>

</body>
</html>
