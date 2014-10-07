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

if (!$_SESSION["privilege_items_template"]) {
	require_once("../inc/popup_log_check.php");
}

$itemid = $a_form_vars['itemid'];

if ($a_form_vars['action'] == "Save") {
	$sql = "UPDATE Items SET Template='$a_form_vars[template]' WHERE ID='$itemid'";
	$nUpdate = dbq($sql);
	
	print("
	<script language=\"JavaScript\" type=\"text/javascript\">
		window.close()
	</script>	");
}

$sql = "SELECT * FROM Items WHERE ID='$itemid'";
$nResult = dbq($sql);

$aItem = mysql_fetch_assoc($nResult);

$content = "<textarea name=\"template\" rows=\"42\" style=\"width:735 ; height:300\">$aItem[Template]</textarea>";

?>
<html>
<head>
<?
print($header_content);
?>
<title>Edit Item</title>
<script language="JavaScript" type="text/JavaScript">

function windowClose(){
	filewindow.window.close();
}

function popupWin(u,n,o) { // v3
	var ftr = o.split(","); 
	nmv=new Array(); 
	for (i in ftr) {
		x=ftr[i]; 
		t=x.split("=")[0]; 
		v=x.split("=")[1]; 
		nmv[t]=v;
	}
	if (nmv['centered']=='yes' || nmv['centered']==1) {
		nmv['left']=(screen.width-nmv['width'])/2 ; 
		nmv['top']=(screen.height - nmv['height']-72)/2 ; 
		nmv['left'] = (nmv['left']<0)?'0':nmv['left'] ; 
		nmv['top']=(nmv['top']<0)?'0':nmv['top']; 
		delete nmv['centered'];
	}
	o=""; 
	var j=0; 
	for (i in nmv) {
		o+=i+"="+nmv[i]+"\,";
	} 
	o=o.slice(0,o.length-1);
	filewindow = window.open(u,n,o);
	return filewindow;
}

function previewTemplate (item_id,mssid) {
	var url = 'preview_template.php?mssid=<? print($mssid); ?>&item_id='+item_id;
	template = popupWin(url,'preview','width=620,height=520,scrollbars=1,resizable=1,centered=1');
	template.window.focus();
}

function openFileWindow(u) {
	filewindow = popupWin(u,'filemanager','height=480,width=360,centered=1');
}
function loadProperties(){
	top.location = 'item_editor.php?edit=properties&item_id=<? print($a_form_vars[itemid]); ?>';
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table align="center" border="0" cellpadding="0" cellspacing="0">
  <tr><td><img src="../images/spacer.gif" height="5"></td></tr>
  <tr><td>
      <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="760" height="520">
        <param name="movie" value="variaprint.swf">
		<param name="quality" value="high">
		<param name="menu" value="false"> 
        <embed src="variaprint.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="760" height="520"></embed>
      </object>
  </td></tr>
</table>
</body>
</html>
