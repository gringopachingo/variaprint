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

//session_save_path("/www/tmp");
session_name("ms_sid");
session_start();
$ms_sid = session_id();

require_once("../inc/config.php");
require_once("../inc/functions-global.php");
if ($_SESSION["privilege"] == "owner") {
	require_once("inc/popup_log_check.php");
}


$mode = $a_form_vars['mode'];


$basedir = $cfg_base_dir . "_sites/" . $_SESSION[site] . "/images/"; // Base directory

/*if ( !file_exists($basedir) && $_SESSION[site] != "") {
	mkdir($basedir);
} */

$dir_files = $dir_subdirs = array();

// Change to directory
chdir($basedir);

// Open directory;
$handle = @opendir($basedir) or die("Directory \"$dir\"not found.");

// Loop through all directory entries, construct
// two temporary arrays containing files and sub directories
while($entry = readdir($handle))
{
	if(is_dir($entry) && $entry != ".." && $entry != ".")
	{
		$dir_subdirs[] = $entry;
	}
}

for($i=0; $i<count($dir_subdirs); $i++)
{
	if ($_GET['folder'] == $dir_subdirs[$i]) { 
		$sel = " selected"; 
	} else { 
		$sel = ""; 
		$pd2 .= "<option value=\"$dir_subdirs[$i]\">$dir_subdirs[$i]</option>\n";
	}
	$pd1 .= "<option value=\"$dir_subdirs[$i]\" $sel>$dir_subdirs[$i]</option>\n";
}

$pd1 = "<select class=\"text\" name=\"1\" onchange=\"top.location='file_manager.php?sm=$_GET[sm]&mode=$mode&obj=$_GET[obj]&folder=' + this.value;\">
<option value=\"\">[Main]</option>$pd1</select>";
$pd2 = "<select class=\"text\" name=\"2\"><option value=\"\"></option>$pd2</select>";
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>File Manager</title>
<script language="JavaScript" type="text/JavaScript">
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
	window.open(u,n,o);
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/filemanager-top-bkg.gif" leftmargin="16" topmargin="5" marginwidth="16" marginheight="5">
<table width="99%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="46%" nowrap class="text">Show files in folder:</td>
    <td width="1%" nowrap class="text">&nbsp;</td>
<!--    <td width="100" nowrap class="text">Move&nbsp;selected&nbsp;files&nbsp;to:</td>
    <td width="35%">&nbsp;</td>
    <td width="1%" nowrap class="text">&nbsp;</td> //-->
    <td width="3%">&nbsp;</td>
    <td width="3%">&nbsp;</td>
  </tr>
  <tr> 
    <td><? print($pd1); ?>&nbsp;</td>
    <td width="1%"><img src="images/spacer.gif" width="20" height="1"></td>
<!--    <td width="100"><? print($pd2); ?></td>
    <td><img src="images/btn-move.gif" width="43" height="19"></td> //-->
    <td width="1%"><img src="images/spacer.gif" width="20" height="1"></td>
    <td align="right" class="text"><a href="javascript:;" onClick="popupWin('file_upload.php?folder=<? print($_GET['folder']); ?>','fileupload','width=450,height=320,resizable=0,centered=1')" class="text">Add&nbsp;a&nbsp;file</a>...</td>
  </tr>
</table>
</body>
</html>
