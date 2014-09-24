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

//session_save_path("/www/tmp");
session_name("ms-sid");
session_start();
$ms_sid = session_id();
$_SESSION['updated'] = " ";

require_once("../inc/config.php");
require_once("../inc/functions-global.php");
if ($_SESSION["privilege"] == "owner") {
	require_once("inc/popup_log_check.php");
}

$mode = $a_form_vars['mode'];

/*
if ($a_form_vars['folder'] != "") {  $a_form_vars['folder'].= "/"; }

if ( isset($a_form_vars['deletefile']) && $a_form_vars['deletefile'] != "" && $a_form_vars['confirmed'] == 1 ) {
	$qualified_file = $cfg_base_dir . "_sites/$_SESSION[site]/images/" . $a_form_vars['folder'] . $a_form_vars['deletefile']; 
	if ( file_exists($qualified_file) ) { 
		unlink($qualified_file);
	} else {
		exit("result=error&file=" . $qualified_file . "&");
	}
}
*/



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>File Manager</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">

function closeWindow(){
	top.window.close()
}

function chooseFile(obj,val) {//
	obj = eval("top.window.opener.document.forms[0]."+obj);
	obj.value = val
//	alert(obj.value);
	top.window.opener.elem_saved = false 
	top.window.opener.addFiles(val)
	top.window.close()
}

function confirmAction (linkobj, msg) {
	is_confirmed = confirm(msg)
	
	if (is_confirmed) {	
		linkobj.href += '&confirmed=1'
	} 
	return is_confirmed
}

</script>
</head>

<body leftmargin="16" topmargin="10" marginwidth="16" marginheight="10">
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="340" height="360">
  <param name="movie" value="file_manager.swf?mode=<?php print($mode); ?>&sm=<?php print($a_form_vars['sm']); ?>&folder=<?php print($a_form_vars['folder']); ?>&obj=<?php print($a_form_vars[obj]); ?>">
  <param name="quality" value="high">
  <embed src="file_manager.swf?mode=<?php print($mode); ?>&folder=<?php print($a_form_vars['folder']); ?>&sm=<?php print($a_form_vars['sm']); ?>&obj=<?php print($a_form_vars[obj]); ?>" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="340" height="360"></embed>
</object>
</body>
</html>
