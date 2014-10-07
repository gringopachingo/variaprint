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
require_once("../inc/popup_log_check.php");

//session_save_path("/www/tmp");
session_name("mssid");
session_start();
$mssid = session_id();
$_SESSION['updated'] = " ";

$_SESSION[site] = $_SESSION['site'];
$item_id = $a_form_vars['item_id'];
$deleteid = $a_form_vars['deleteid'];

if ($_SESSION[site] == "" || $_SESSION['user_id'] == "" || !isset($_SESSION['user_id']) )  { 
	print("
		<script language=\"javascript\">
			top.opener.location.reload(0) // location.reload(true)
			top.close()
		</script>
	"); 
	exit;
}



if ( !isset($a_form_vars['deleteid']) || $a_form_vars['deleteid'] == "") {
	exit("No delete id.");
}

if ( $a_form_vars['action'] == "delete" ) {
	$sql = "SELECT ItemGroups FROM Sites WHERE ID='$_SESSION[site]'";
	$nResult = dbq($sql);
	$a_ig = mysql_fetch_assoc($nResult);
	$a_item_group_tree = xml_get_tree($a_ig['ItemGroups']);
	
	if ( is_array($a_item_group_tree[0]['children']) ) {
	
		foreach ( $a_item_group_tree[0]['children'] as $k=>$node ) {
			$id = $node['attributes']['ID'];
			if ($id == $a_form_vars['deleteid']) {
				unset($a_item_group_tree[0]['children'][$k]);
				break;
			}
		}
		$xml = addslashes(xml_make_tree($a_item_group_tree));
		
		$sql = "UPDATE Sites SET ItemGroups='$xml' WHERE ID='$_SESSION[site]'";
		dbq($sql);
		
		$sql = "UPDATE Items SET GroupID='$a_form_vars[group_id]' WHERE GroupID='$deleteid' AND SiteID='$_SESSION[site]'";
		dbq($sql);

		print("
			<script language=\"javascript\">
				top.opener.location.reload(0) // location.reload(true)
				top.close()
			</script>
		"); 
		exit;
	}
	
}

$sql = "SELECT ItemGroups FROM Sites WHERE ID='$_SESSION[site]'";
$nResult = dbq($sql);
$a_ig = mysql_fetch_assoc($nResult);
$a_item_group_tree = xml_get_tree($a_ig['ItemGroups']);

if ( is_array($a_item_group_tree[0]['children']) ) {
	$pd .= "<select name=\"group_id\">";

	foreach ( $a_item_group_tree[0]['children'] as $node ) {
		if ($node['tag'] == "ITEMGROUP") {
			$name = $node['attributes']['NAME']; $id = $node['attributes']['ID'];
			if ( $node['attributes']['HIDDEN'] == "Y") { $name .= " [hidden]"; } 
			if ( $deleteid == $id) { 
				$group_name = $name;
			} else {  
				$pd .= "<option value=\"$id\"$sel>$name</option>";
			} 		
		}
	}
	
	$pd .= "</select>";
}


$topbar = "Which group do you want to move the &quot;$group_name&quot; group items into?<br>
$pd
";



?><HTML>
<HEAD>
<?
print($header_content);
?>
<TITLE>Delete item group</TITLE>
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
<link href="../style.css" rel="stylesheet" type="text/css">
</HEAD>

<BODY background="../images/bkg-groove.gif">
<form name="form1" method="post" action="">
<span class="text">
  <p><? print($topbar); ?> 
    <input name="action" type="hidden" id="action" value="delete">
    <input name="deleteid" type="hidden" id="deleteid" value="<? print($deleteid); ?>">
  </p>
  <input name="Button" type="button" class="text" onClick="window.close()" value="Cancel">
  <input name="Submit2" type="submit" class="text" value="Delete Group">
</span></form>
</BODY>
</HTML>
