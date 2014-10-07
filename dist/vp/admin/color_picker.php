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

if ($_SESSION["privilege"] == "owner"){
	require_once("inc/popup_log_check.php");
}


/*
$a_safe_color[1] = "00"; 
$a_safe_color[2] = "33"; 
$a_safe_color[3] = "66"; 
$a_safe_color[4] = "99"; 
$a_safe_color[5] = "CC"; 
$a_safe_color[6] = "FF"; 

$cntr1 = 0;
while ( $cntr1 < 6 ) {
	++$cntr1;
	$cntr2 = 0;
	while ($cntr2 < 6) {
		$cntr3 = 0;
		++$cntr2;
		$color .= $a_safe_color[$cntr2];
		while ($cntr3 < 6) {
			++$cntr3;
			$colors[] = "#" . $a_safe_color[$cntr1] . $a_safe_color[$cntr2] . $a_safe_color[$cntr3];
		}	
	}
}

$cntr = 0;
foreach ($colors as $k=>$thiscolor) {
	++$cntr;
	$row .= "<td bgcolor=\"$thiscolor\" onClick=\"sel('$thiscolor')\">&nbsp;</td>\n";
	if ( $cntr == 36 ) {
		$cntr = 0;
		$color_table .= "<tr>$row</tr>\n";
		$row = "";
	}
}
//<a href=\"\"><img border=0 src=\"images/spacer.gif\" width=\"15\" height=\"15\"></a>
$color_table = "<table cellspacing=1 cellpadding=0 border=0 width=\"540\">$color_table</table>";
*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php
print($header_content);
?>

<title>Choose a color...</title>
<script language="JavaScript" type="text/JavaScript">

function sel(c) {
	window.opener.document.forms[0].<?php print($_GET['obj']); ?>.value = c
	window.opener.update_color('<?php print($_GET['obj']); ?>')
	window.close()
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body background="images/bkg-groove.gif">
<?php
print($color_table);
?>
<table width="540" border=0 cellpadding=0 cellspacing=1 bgcolor="#CCCCCC">
  <tr>
    <td bgcolor="#000000" onClick="sel('#000000')">&nbsp;</td>
    <td bgcolor="#000000" onClick="sel('#000000')">&nbsp;</td>
    <td bgcolor="#000033" onClick="sel('#000033')">&nbsp;</td>
    <td bgcolor="#000066" onClick="sel('#000066')">&nbsp;</td>
    <td bgcolor="#000099" onClick="sel('#000099')">&nbsp;</td>
    <td bgcolor="#0000CC" onClick="sel('#0000CC')">&nbsp;</td>
    <td bgcolor="#0000FF" onClick="sel('#0000FF')">&nbsp;</td>
    <td bgcolor="#003300" onClick="sel('#003300')">&nbsp;</td>
    <td bgcolor="#003333" onClick="sel('#003333')">&nbsp;</td>
    <td bgcolor="#003366" onClick="sel('#003366')">&nbsp;</td>
    <td bgcolor="#003399" onClick="sel('#003399')">&nbsp;</td>
    <td bgcolor="#0033CC" onClick="sel('#0033CC')">&nbsp;</td>
    <td bgcolor="#0033FF" onClick="sel('#0033FF')">&nbsp;</td>
    <td bgcolor="#006600" onClick="sel('#006600')">&nbsp;</td>
    <td bgcolor="#006633" onClick="sel('#006633')">&nbsp;</td>
    <td bgcolor="#006666" onClick="sel('#006666')">&nbsp;</td>
    <td bgcolor="#006699" onClick="sel('#006699')">&nbsp;</td>
    <td bgcolor="#0066CC" onClick="sel('#0066CC')">&nbsp;</td>
    <td bgcolor="#0066FF" onClick="sel('#0066FF')">&nbsp;</td>
    <td bgcolor="#009900" onClick="sel('#009900')">&nbsp;</td>
    <td bgcolor="#009933" onClick="sel('#009933')">&nbsp;</td>
    <td bgcolor="#009966" onClick="sel('#009966')">&nbsp;</td>
    <td bgcolor="#009999" onClick="sel('#009999')">&nbsp;</td>
    <td bgcolor="#0099CC" onClick="sel('#0099CC')">&nbsp;</td>
    <td bgcolor="#0099FF" onClick="sel('#0099FF')">&nbsp;</td>
    <td bgcolor="#00CC00" onClick="sel('#00CC00')">&nbsp;</td>
    <td bgcolor="#00CC33" onClick="sel('#00CC33')">&nbsp;</td>
    <td bgcolor="#00CC66" onClick="sel('#00CC66')">&nbsp;</td>
    <td bgcolor="#00CC99" onClick="sel('#00CC99')">&nbsp;</td>
    <td bgcolor="#00CCCC" onClick="sel('#00CCCC')">&nbsp;</td>
    <td bgcolor="#00CCFF" onClick="sel('#00CCFF')">&nbsp;</td>
    <td bgcolor="#00FF00" onClick="sel('#00FF00')">&nbsp;</td>
    <td bgcolor="#00FF33" onClick="sel('#00FF33')">&nbsp;</td>
    <td bgcolor="#00FF66" onClick="sel('#00FF66')">&nbsp;</td>
    <td bgcolor="#00FF99" onClick="sel('#00FF99')">&nbsp;</td>
    <td bgcolor="#00FFCC" onClick="sel('#00FFCC')">&nbsp;</td>
    <td bgcolor="#00FFFF" onClick="sel('#00FFFF')">&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#333333" onClick="sel('#333333')">&nbsp;</td>
    <td bgcolor="#330000" onClick="sel('#330000')">&nbsp;</td>
    <td bgcolor="#330033" onClick="sel('#330033')">&nbsp;</td>
    <td bgcolor="#330066" onClick="sel('#330066')">&nbsp;</td>
    <td bgcolor="#330099" onClick="sel('#330099')">&nbsp;</td>
    <td bgcolor="#3300CC" onClick="sel('#3300CC')">&nbsp;</td>
    <td bgcolor="#3300FF" onClick="sel('#3300FF')">&nbsp;</td>
    <td bgcolor="#333300" onClick="sel('#333300')">&nbsp;</td>
    <td bgcolor="#333333" onClick="sel('#333333')">&nbsp;</td>
    <td bgcolor="#333366" onClick="sel('#333366')">&nbsp;</td>
    <td bgcolor="#333399" onClick="sel('#333399')">&nbsp;</td>
    <td bgcolor="#3333CC" onClick="sel('#3333CC')">&nbsp;</td>
    <td bgcolor="#3333FF" onClick="sel('#3333FF')">&nbsp;</td>
    <td bgcolor="#336600" onClick="sel('#336600')">&nbsp;</td>
    <td bgcolor="#336633" onClick="sel('#336633')">&nbsp;</td>
    <td bgcolor="#336666" onClick="sel('#336666')">&nbsp;</td>
    <td bgcolor="#336699" onClick="sel('#336699')">&nbsp;</td>
    <td bgcolor="#3366CC" onClick="sel('#3366CC')">&nbsp;</td>
    <td bgcolor="#3366FF" onClick="sel('#3366FF')">&nbsp;</td>
    <td bgcolor="#339900" onClick="sel('#339900')">&nbsp;</td>
    <td bgcolor="#339933" onClick="sel('#339933')">&nbsp;</td>
    <td bgcolor="#339966" onClick="sel('#339966')">&nbsp;</td>
    <td bgcolor="#339999" onClick="sel('#339999')">&nbsp;</td>
    <td bgcolor="#3399CC" onClick="sel('#3399CC')">&nbsp;</td>
    <td bgcolor="#3399FF" onClick="sel('#3399FF')">&nbsp;</td>
    <td bgcolor="#33CC00" onClick="sel('#33CC00')">&nbsp;</td>
    <td bgcolor="#33CC33" onClick="sel('#33CC33')">&nbsp;</td>
    <td bgcolor="#33CC66" onClick="sel('#33CC66')">&nbsp;</td>
    <td bgcolor="#33CC99" onClick="sel('#33CC99')">&nbsp;</td>
    <td bgcolor="#33CCCC" onClick="sel('#33CCCC')">&nbsp;</td>
    <td bgcolor="#33CCFF" onClick="sel('#33CCFF')">&nbsp;</td>
    <td bgcolor="#33FF00" onClick="sel('#33FF00')">&nbsp;</td>
    <td bgcolor="#33FF33" onClick="sel('#33FF33')">&nbsp;</td>
    <td bgcolor="#33FF66" onClick="sel('#33FF66')">&nbsp;</td>
    <td bgcolor="#33FF99" onClick="sel('#33FF99')">&nbsp;</td>
    <td bgcolor="#33FFCC" onClick="sel('#33FFCC')">&nbsp;</td>
    <td bgcolor="#33FFFF" onClick="sel('#33FFFF')">&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#666666" onClick="sel('#666666')">&nbsp;</td>
    <td bgcolor="#660000" onClick="sel('#660000')">&nbsp;</td>
    <td bgcolor="#660033" onClick="sel('#660033')">&nbsp;</td>
    <td bgcolor="#660066" onClick="sel('#660066')">&nbsp;</td>
    <td bgcolor="#660099" onClick="sel('#660099')">&nbsp;</td>
    <td bgcolor="#6600CC" onClick="sel('#6600CC')">&nbsp;</td>
    <td bgcolor="#6600FF" onClick="sel('#6600FF')">&nbsp;</td>
    <td bgcolor="#663300" onClick="sel('#663300')">&nbsp;</td>
    <td bgcolor="#663333" onClick="sel('#663333')">&nbsp;</td>
    <td bgcolor="#663366" onClick="sel('#663366')">&nbsp;</td>
    <td bgcolor="#663399" onClick="sel('#663399')">&nbsp;</td>
    <td bgcolor="#6633CC" onClick="sel('#6633CC')">&nbsp;</td>
    <td bgcolor="#6633FF" onClick="sel('#6633FF')">&nbsp;</td>
    <td bgcolor="#666600" onClick="sel('#666600')">&nbsp;</td>
    <td bgcolor="#666633" onClick="sel('#666633')">&nbsp;</td>
    <td bgcolor="#666666" onClick="sel('#666666')">&nbsp;</td>
    <td bgcolor="#666699" onClick="sel('#666699')">&nbsp;</td>
    <td bgcolor="#6666CC" onClick="sel('#6666CC')">&nbsp;</td>
    <td bgcolor="#6666FF" onClick="sel('#6666FF')">&nbsp;</td>
    <td bgcolor="#669900" onClick="sel('#669900')">&nbsp;</td>
    <td bgcolor="#669933" onClick="sel('#669933')">&nbsp;</td>
    <td bgcolor="#669966" onClick="sel('#669966')">&nbsp;</td>
    <td bgcolor="#669999" onClick="sel('#669999')">&nbsp;</td>
    <td bgcolor="#6699CC" onClick="sel('#6699CC')">&nbsp;</td>
    <td bgcolor="#6699FF" onClick="sel('#6699FF')">&nbsp;</td>
    <td bgcolor="#66CC00" onClick="sel('#66CC00')">&nbsp;</td>
    <td bgcolor="#66CC33" onClick="sel('#66CC33')">&nbsp;</td>
    <td bgcolor="#66CC66" onClick="sel('#66CC66')">&nbsp;</td>
    <td bgcolor="#66CC99" onClick="sel('#66CC99')">&nbsp;</td>
    <td bgcolor="#66CCCC" onClick="sel('#66CCCC')">&nbsp;</td>
    <td bgcolor="#66CCFF" onClick="sel('#66CCFF')">&nbsp;</td>
    <td bgcolor="#66FF00" onClick="sel('#66FF00')">&nbsp;</td>
    <td bgcolor="#66FF33" onClick="sel('#66FF33')">&nbsp;</td>
    <td bgcolor="#66FF66" onClick="sel('#66FF66')">&nbsp;</td>
    <td bgcolor="#66FF99" onClick="sel('#66FF99')">&nbsp;</td>
    <td bgcolor="#66FFCC" onClick="sel('#66FFCC')">&nbsp;</td>
    <td bgcolor="#66FFFF" onClick="sel('#66FFFF')">&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#999999" onClick="sel('#999999')">&nbsp;</td>
    <td bgcolor="#990000" onClick="sel('#990000')">&nbsp;</td>
    <td bgcolor="#990033" onClick="sel('#990033')">&nbsp;</td>
    <td bgcolor="#990066" onClick="sel('#990066')">&nbsp;</td>
    <td bgcolor="#990099" onClick="sel('#990099')">&nbsp;</td>
    <td bgcolor="#9900CC" onClick="sel('#9900CC')">&nbsp;</td>
    <td bgcolor="#9900FF" onClick="sel('#9900FF')">&nbsp;</td>
    <td bgcolor="#993300" onClick="sel('#993300')">&nbsp;</td>
    <td bgcolor="#993333" onClick="sel('#993333')">&nbsp;</td>
    <td bgcolor="#993366" onClick="sel('#993366')">&nbsp;</td>
    <td bgcolor="#993399" onClick="sel('#993399')">&nbsp;</td>
    <td bgcolor="#9933CC" onClick="sel('#9933CC')">&nbsp;</td>
    <td bgcolor="#9933FF" onClick="sel('#9933FF')">&nbsp;</td>
    <td bgcolor="#996600" onClick="sel('#996600')">&nbsp;</td>
    <td bgcolor="#996633" onClick="sel('#996633')">&nbsp;</td>
    <td bgcolor="#996666" onClick="sel('#996666')">&nbsp;</td>
    <td bgcolor="#996699" onClick="sel('#996699')">&nbsp;</td>
    <td bgcolor="#9966CC" onClick="sel('#9966CC')">&nbsp;</td>
    <td bgcolor="#9966FF" onClick="sel('#9966FF')">&nbsp;</td>
    <td bgcolor="#999900" onClick="sel('#999900')">&nbsp;</td>
    <td bgcolor="#999933" onClick="sel('#999933')">&nbsp;</td>
    <td bgcolor="#999966" onClick="sel('#999966')">&nbsp;</td>
    <td bgcolor="#999999" onClick="sel('#999999')">&nbsp;</td>
    <td bgcolor="#9999CC" onClick="sel('#9999CC')">&nbsp;</td>
    <td bgcolor="#9999FF" onClick="sel('#9999FF')">&nbsp;</td>
    <td bgcolor="#99CC00" onClick="sel('#99CC00')">&nbsp;</td>
    <td bgcolor="#99CC33" onClick="sel('#99CC33')">&nbsp;</td>
    <td bgcolor="#99CC66" onClick="sel('#99CC66')">&nbsp;</td>
    <td bgcolor="#99CC99" onClick="sel('#99CC99')">&nbsp;</td>
    <td bgcolor="#99CCCC" onClick="sel('#99CCCC')">&nbsp;</td>
    <td bgcolor="#99CCFF" onClick="sel('#99CCFF')">&nbsp;</td>
    <td bgcolor="#99FF00" onClick="sel('#99FF00')">&nbsp;</td>
    <td bgcolor="#99FF33" onClick="sel('#99FF33')">&nbsp;</td>
    <td bgcolor="#99FF66" onClick="sel('#99FF66')">&nbsp;</td>
    <td bgcolor="#99FF99" onClick="sel('#99FF99')">&nbsp;</td>
    <td bgcolor="#99FFCC" onClick="sel('#99FFCC')">&nbsp;</td>
    <td bgcolor="#99FFFF" onClick="sel('#99FFFF')">&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC" onClick="sel('#CCCCCC')">&nbsp;</td>
    <td bgcolor="#CC0000" onClick="sel('#CC0000')">&nbsp;</td>
    <td bgcolor="#CC0033" onClick="sel('#CC0033')">&nbsp;</td>
    <td bgcolor="#CC0066" onClick="sel('#CC0066')">&nbsp;</td>
    <td bgcolor="#CC0099" onClick="sel('#CC0099')">&nbsp;</td>
    <td bgcolor="#CC00CC" onClick="sel('#CC00CC')">&nbsp;</td>
    <td bgcolor="#CC00FF" onClick="sel('#CC00FF')">&nbsp;</td>
    <td bgcolor="#CC3300" onClick="sel('#CC3300')">&nbsp;</td>
    <td bgcolor="#CC3333" onClick="sel('#CC3333')">&nbsp;</td>
    <td bgcolor="#CC3366" onClick="sel('#CC3366')">&nbsp;</td>
    <td bgcolor="#CC3399" onClick="sel('#CC3399')">&nbsp;</td>
    <td bgcolor="#CC33CC" onClick="sel('#CC33CC')">&nbsp;</td>
    <td bgcolor="#CC33FF" onClick="sel('#CC33FF')">&nbsp;</td>
    <td bgcolor="#CC6600" onClick="sel('#CC6600')">&nbsp;</td>
    <td bgcolor="#CC6633" onClick="sel('#CC6633')">&nbsp;</td>
    <td bgcolor="#CC6666" onClick="sel('#CC6666')">&nbsp;</td>
    <td bgcolor="#CC6699" onClick="sel('#CC6699')">&nbsp;</td>
    <td bgcolor="#CC66CC" onClick="sel('#CC66CC')">&nbsp;</td>
    <td bgcolor="#CC66FF" onClick="sel('#CC66FF')">&nbsp;</td>
    <td bgcolor="#CC9900" onClick="sel('#CC9900')">&nbsp;</td>
    <td bgcolor="#CC9933" onClick="sel('#CC9933')">&nbsp;</td>
    <td bgcolor="#CC9966" onClick="sel('#CC9966')">&nbsp;</td>
    <td bgcolor="#CC9999" onClick="sel('#CC9999')">&nbsp;</td>
    <td bgcolor="#CC99CC" onClick="sel('#CC99CC')">&nbsp;</td>
    <td bgcolor="#CC99FF" onClick="sel('#CC99FF')">&nbsp;</td>
    <td bgcolor="#CCCC00" onClick="sel('#CCCC00')">&nbsp;</td>
    <td bgcolor="#CCCC33" onClick="sel('#CCCC33')">&nbsp;</td>
    <td bgcolor="#CCCC66" onClick="sel('#CCCC66')">&nbsp;</td>
    <td bgcolor="#CCCC99" onClick="sel('#CCCC99')">&nbsp;</td>
    <td bgcolor="#CCCCCC" onClick="sel('#CCCCCC')">&nbsp;</td>
    <td bgcolor="#CCCCFF" onClick="sel('#CCCCFF')">&nbsp;</td>
    <td bgcolor="#CCFF00" onClick="sel('#CCFF00')">&nbsp;</td>
    <td bgcolor="#CCFF33" onClick="sel('#CCFF33')">&nbsp;</td>
    <td bgcolor="#CCFF66" onClick="sel('#CCFF66')">&nbsp;</td>
    <td bgcolor="#CCFF99" onClick="sel('#CCFF99')">&nbsp;</td>
    <td bgcolor="#CCFFCC" onClick="sel('#CCFFCC')">&nbsp;</td>
    <td bgcolor="#CCFFFF" onClick="sel('#CCFFFF')">&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" onClick="sel('#FFFFFF')">&nbsp;</td>
    <td bgcolor="#FF0000" onClick="sel('#FF0000')">&nbsp;</td>
    <td bgcolor="#FF0033" onClick="sel('#FF0033')">&nbsp;</td>
    <td bgcolor="#FF0066" onClick="sel('#FF0066')">&nbsp;</td>
    <td bgcolor="#FF0099" onClick="sel('#FF0099')">&nbsp;</td>
    <td bgcolor="#FF00CC" onClick="sel('#FF00CC')">&nbsp;</td>
    <td bgcolor="#FF00FF" onClick="sel('#FF00FF')">&nbsp;</td>
    <td bgcolor="#FF3300" onClick="sel('#FF3300')">&nbsp;</td>
    <td bgcolor="#FF3333" onClick="sel('#FF3333')">&nbsp;</td>
    <td bgcolor="#FF3366" onClick="sel('#FF3366')">&nbsp;</td>
    <td bgcolor="#FF3399" onClick="sel('#FF3399')">&nbsp;</td>
    <td bgcolor="#FF33CC" onClick="sel('#FF33CC')">&nbsp;</td>
    <td bgcolor="#FF33FF" onClick="sel('#FF33FF')">&nbsp;</td>
    <td bgcolor="#FF6600" onClick="sel('#FF6600')">&nbsp;</td>
    <td bgcolor="#FF6633" onClick="sel('#FF6633')">&nbsp;</td>
    <td bgcolor="#FF6666" onClick="sel('#FF6666')">&nbsp;</td>
    <td bgcolor="#FF6699" onClick="sel('#FF6699')">&nbsp;</td>
    <td bgcolor="#FF66CC" onClick="sel('#FF66CC')">&nbsp;</td>
    <td bgcolor="#FF66FF" onClick="sel('#FF66FF')">&nbsp;</td>
    <td bgcolor="#FF9900" onClick="sel('#FF9900')">&nbsp;</td>
    <td bgcolor="#FF9933" onClick="sel('#FF9933')">&nbsp;</td>
    <td bgcolor="#FF9966" onClick="sel('#FF9966')">&nbsp;</td>
    <td bgcolor="#FF9999" onClick="sel('#FF9999')">&nbsp;</td>
    <td bgcolor="#FF99CC" onClick="sel('#FF99CC')">&nbsp;</td>
    <td bgcolor="#FF99FF" onClick="sel('#FF99FF')">&nbsp;</td>
    <td bgcolor="#FFCC00" onClick="sel('#FFCC00')">&nbsp;</td>
    <td bgcolor="#FFCC33" onClick="sel('#FFCC33')">&nbsp;</td>
    <td bgcolor="#FFCC66" onClick="sel('#FFCC66')">&nbsp;</td>
    <td bgcolor="#FFCC99" onClick="sel('#FFCC99')">&nbsp;</td>
    <td bgcolor="#FFCCCC" onClick="sel('#FFCCCC')">&nbsp;</td>
    <td bgcolor="#FFCCFF" onClick="sel('#FFCCFF')">&nbsp;</td>
    <td bgcolor="#FFFF00" onClick="sel('#FFFF00')">&nbsp;</td>
    <td bgcolor="#FFFF33" onClick="sel('#FFFF33')">&nbsp;</td>
    <td bgcolor="#FFFF66" onClick="sel('#FFFF66')">&nbsp;</td>
    <td bgcolor="#FFFF99" onClick="sel('#FFFF99')">&nbsp;</td>
    <td bgcolor="#FFFFCC" onClick="sel('#FFFFCC')">&nbsp;</td>
    <td bgcolor="#FFFFFF" onClick="sel('#FFFFFF')">&nbsp;</td>
  </tr>
</table>
<div align="right"><br>
  <input type="button" name="Button" value="Close" onclick="window.close()">
</div>
</body>
</html>
