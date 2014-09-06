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

	session_name("ms_sid");
	session_start();
	$ms_sid = session_id();
	
	
	require_once("../../inc/config.php");
	require_once("../../inc/functions-global.php");
	require_once("../inc/functions.php");
	if (!$_SESSION["privilege_items_properties"] && $a_form_vars[edit] == "properties") {
		require_once("../inc/popup_log_check.php");
	} elseif(!$_SESSION["privilege_items_template"] && $a_form_vars[edit] == "template") {
		require_once("../inc/popup_log_check.php");
	}
	

	$item_id = $a_form_vars[item_id];
	$edit  	 = $a_form_vars[edit];
	
	$sql = "SELECT Custom FROM Items WHERE ID='$item_id'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	
	$custom = "no";

//	$a_editors[] = "name";
//	$a_editors[] = "group";
	if ($a_result["Custom"] != "N") {
		$custom = "yes";
		
		$a_editors[] = "template";
//		$a_editors[] = "input";
//		$a_editors[] = "prefill";
	}
//	$a_editors[] = "pricing";
//	$a_editors[] = "imposition";
//	$a_editors[] = "supplier";
//	$a_editors[] = "shipping";
	$a_editors[] = "properties";
	
	foreach ($a_editors as $editor) {
		if ($edit == $editor) {
			$menu .= "<td width=\"100\">$editor</td>\n";
		} else {
			$menu .= "<td width=\"100\"><a title=\"Edit $editor\" href=\"item_editor.php?edit=$editor&item_id=$item_id\" target=\"_top\">$editor</a></td>\n";
		}
	}
//	<img border=\"0\" src=\"../images/tab-$editor-off.gif\" border=\"0\"><img title=\"You are editing $editor\"  src=\"../images/tab-$editor-on.gif\" border=\"0\">
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script language="JavaScript">
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
}

function open_help() {
	popupWin('../help/index.php?page=items','','centered=1,width=550,height=450');
}

</script>
<?
print($header_content);
?>
<title>Edit Item</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#eeeeee" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="800" height="30">
  <param name="movie" value="menu.swf?editor=<? print($edit); ?>&custom=<? print($custom); ?>">
  <param name="quality" value="high"><param name="menu" value="false">
  <embed src="menu.swf?editor=<? print($edit); ?>&custom=<? print($custom); ?>" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="800" height="30"></embed>
</object>
</body>
</html>
