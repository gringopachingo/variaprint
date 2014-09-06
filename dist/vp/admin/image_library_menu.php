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
	
	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	if ($_SESSION["privilege"] == "owner") {
		require_once("inc/popup_log_check.php");
	}
	
	
	$mode = $a_form_vars['mode'];
	
	$sql = "SELECT ImageLibraries FROM Sites WHERE ID='$_SESSION[site]'";
	$res = dbq($sql);
	$a_res = mysql_fetch_assoc($res);
	$img_lib = xml_get_tree($a_res["ImageLibraries"]);
	
	if ($img_lib[0]["tag"] == "") {
		$img_lib[0]["tag"] = "LIBRARIES";
	}
	
	if (count($img_lib[0]["children"]) == 0) {
		$img_lib[0]["children"][0]["tag"] = "library";
		$img_lib[0]["children"][0]["children"] = "";
		$img_lib[0]["children"][0]["value"] = "";
		$img_lib[0]["children"][0]["attributes"]["NAME"] = "Untitled Library";
		$img_lib[0]["children"][0]["attributes"]["ID"] = 1;
		$img_lib_xml = addslashes(xml_make_tree($img_lib));
		$sql = "UPDATE Sites SET ImageLibraries='$img_lib_xml' WHERE ID='$_SESSION[site]'";
		dbq($sql);
	}
	
	if ($a_form_vars['library'] == "") {
		$a_form_vars['library'] = $img_lib[0]["children"][0]['attributes']['ID'];
	}
	
	if (count($img_lib[0]["children"]) == 1) {
		$showDelete = false;
	} else {
		$showDelete = true;
	}
	
	$pd = "";
	if (is_array($img_lib[0]["children"]))
	foreach ($img_lib[0]["children"] as $key=>$node) {
		if ($node["attributes"]["ID"] == $a_form_vars['library']) { $sel = "selected"; } else { $sel = "";}
		$pd .= "<option value=\"".$node["attributes"]["ID"]."\" $sel>".htmlentities($node["attributes"]["NAME"])."</option>";
	}
	$pd = "<select style=\"width: 130\" name=\"library\" id=\"library\" onChange=\"top.document.location='image_library.php?library='+this.value\">$pd</select>";

	
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Image Libraries</title>
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
function addFiles(files) {
	lib = document.forms[0].library;
	lib_val = lib.options[lib.selectedIndex].value;
	url = 'image_library_files.php?action=addfiles&files='+files
	url += '&library='+lib_val
	top.mainFrame.location = url.replace('#','%23');
}
function confirmAction (linkobj, msg) {
	is_confirmed = confirm(msg)
	
	if (is_confirmed) {	
		linkobj.href += '&confirmed=1'
	} 
	return is_confirmed
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/filemanager-top-bkg.gif" leftmargin="16" topmargin="5" marginwidth="16" marginheight="5">
<form name="form1" method="post" action="">
  <input type="hidden" name="hiddenField">
  <input type="hidden" name="elem_saved">
  <table width="99%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td nowrap class="text">View image library:</td>
      <td width="1">&nbsp;</td>
      <td width="1">&nbsp;</td>
      <td width="1" nowrap class="text">&nbsp;</td>
      <!--    <td width="100" nowrap class="text">Move&nbsp;selected&nbsp;files&nbsp;to:</td>
    <td width="35%">&nbsp;</td>
    <td width="1%" nowrap class="text">&nbsp;</td> //-->
      <td width="1">&nbsp;</td>
      <td width="3%">&nbsp;</td>
    </tr>
    <tr>
      <td><? print($pd); ?>&nbsp;</td>
      <td width="1" nowrap class="text"><? if ($showDelete) { ?><a href="image_library_files.php?action=deletelibrary&library=<? print($a_form_vars["library"]); ?>" onClick="return confirmAction(this, 'Are you sure you want to delete the selected library? This is not undoable.')" target="mainFrame" class="text">Delete&nbsp;library</a>
        <? } ?>
      </td>
      <td width="1"><img src="images/spacer.gif" width="16" height="1"></td>
      <td width="1" nowrap class="text"><a href="image_library_files.php?action=addlibrary" target="mainFrame" class="text">Add&nbsp;library</a></td>
      <!--    <td width="100"><? print($pd2); ?></td>
    <td><img src="images/btn-move.gif" width="43" height="19"></td> //-->
      <td width="1"><img src="images/spacer.gif" width="16" height="1"></td>
      <td align="right" nowrap class="text"><a href="javascript:;" onClick="popupWin('file_manager.php?folder=library&sm=1&mode=html&obj=hiddenField','filemanager','width=380,height=450,scrollbars=0,resizable=0,centered=1')" class="text">Add&nbsp;images
          to library</a>...</td>
    </tr>
  </table>
</form>
</body>
</html>
