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


?><HTML>
<HEAD>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<TITLE>Edit Item Properties</TITLE>
<script language="JavaScript">

function popupWin(u,n,o) { // v3
	var ftr = o.split(","); 
	nmv=new Array(); 
	for (i in ftr) {
		x=ftr[i]; 
		p=x.split("=");
		t=p[0]; v=p[1]; 
		nmv[t]=v;
	}
	if (nmv['centered']=='yes' || nmv['centered']==1) {
		nmv['left']=(screen.width-nmv['width'])/2 ; 
		nmv['top']=(eval(screen.height-nmv['height']-72))/2 ; 
		nmv['left'] = (nmv['left']<0)?'0':nmv['left'] ; 
		nmv['top'] = (nmv['top']<0)?'0':nmv['top']; 
		delete nmv['centered'];
	}
	o=""; 
	var j=0; 
	for (i in nmv) {
		o+=i+"="+nmv[i]+"\,";
	} 
	o=o.slice(0,o.length-1);
	newwin = window.open(u,n,o);
	return newwin;
}

function openWindow(u) {
	popupWin(u,'style','height=480,width=550,centered=1');
}
function openFileWindow(u) {
	filewindow = popupWin(u,'filemanager','height=480,width=360,centered=1');
}
function file_windowClose(){
	filewindow.window.close();
}
function loadEditor(){
	top.location = 'item_editor.php?edit=template&item_id=<? print($a_form_vars[itemid]); ?>';
}
</script>
</HEAD>
<BODY bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
 codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
 WIDTH="745" HEIGHT="500" id="item_properties" ALIGN="">
 <PARAM NAME=movie VALUE="item_properties.swf"> <PARAM NAME=quality VALUE=high> <param name="menu" value="false"> <PARAM NAME=bgcolor VALUE=#FFFFFF> <EMBED src="item_properties.swf" quality=high bgcolor=#FFFFFF  WIDTH="745" HEIGHT="500" NAME="item_properties" ALIGN=""
 TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>
</OBJECT>
</BODY>
</HTML>
