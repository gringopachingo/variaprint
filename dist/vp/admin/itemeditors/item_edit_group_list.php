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

if (!$_SESSION["privilege_items_properties"]){
	require_once("../inc/popup_log_check.php");
}

//session_save_path("/www/tmp");
session_name("ms_sid");
session_start();
$ms_sid = session_id();
$_SESSION['updated'] = " ";

$_SESSION[site] = $_SESSION['site'];
$item_id = $a_form_vars['item_id'];

if ($_SESSION[site] == "" || $_SESSION['user_id'] == "" || !isset($_SESSION['user_id']) )  { 
	print("
		<script language=\"javascript\">
		window.opener.location.reload(0) // location.reload(true)
		window.close()
		</script>
	"); 
	exit;
}


$sql = "SELECT ItemGroups FROM Sites WHERE ID='$_SESSION[site]'";
$nResult = dbq($sql);
$a_ig = mysql_fetch_assoc($nResult);
$a_item_group_tree = xml_get_tree($a_ig['ItemGroups']);


if ($a_form_vars['action'] == "save" || $a_form_vars['action'] == "addgroup") {
	
	$a_group = array_find_key_prefix("group_", $a_form_vars, 1);
	$a_hidden = array_find_key_prefix("hidden_", $a_form_vars, 1);
	$a_description = array_find_key_prefix("description_", $a_form_vars, 1);

	foreach ($a_group as $k=>$v) {
		if ($v != "") {
//			print("$k - yep<br>");
			if ($a_hidden[$k] == "checked") { $hidden = "Y"; } else {$hidden = "N"; }
			$a_item_group_tree = xml_update_value("itemgroups/itemgroup:$k","NAME",$v,$a_item_group_tree);
			$a_item_group_tree = xml_update_value("itemgroups/itemgroup:$k","DESCRIPTION",$a_description[$k],$a_item_group_tree);
			$a_item_group_tree = xml_update_value("itemgroups/itemgroup:$k","HIDDEN",$hidden,$a_item_group_tree);
		}
	}	
	
	$xml = addslashes(xml_make_tree($a_item_group_tree));
	
	$sql = "UPDATE Sites SET ItemGroups='$xml' WHERE ID='$_SESSION[site]'";
	dbq($sql);
	
	if ($a_form_vars['action'] == "save") {
		print("
			<script language=\"javascript\">
				window.opener.location.reload(true)
		//		window.close()
			</script>
		"); 
	//	exit;
	}
	$a_item_group_tree = xml_get_tree(stripslashes($xml));
}

//print_r($a_item_group_tree);

$content .= "
<tr>
	<td class=\"text\">Name</td>
	<td class=\"text\">Description</td>
	<td width=\"1\" class=\"text\">Hide*</td>
	<td width=\"1\"></td>
<tr>
";

$next_id = 0;

if ( is_array($a_item_group_tree[0]['children']) ) {
	foreach ( $a_item_group_tree[0]['children'] as $node ) {
		if ($node['tag'] == "ITEMGROUP") {
			$name = $node['attributes']['NAME']; $description = $node['attributes']['DESCRIPTION']; $id = $node['attributes']['ID']; 
			if ($id > $next_id) { $next_id = $id; }
			if ( $node['attributes']['HIDDEN'] == "Y") { $hidden = " checked"; } else { $hidden = "";}
			if (count($a_item_group_tree[0]['children']) > 1) { 
				$deletelink = "<a href=\"javascript:;\" onClick=\"popupWin('item_edit_group_delete.php?deleteid=$id','','width=300,height=150,centered=1')\">
				<img border=\"0\" src=\"../images/icon-delete.gif\"></a>"; 
			} else { 
				$deletelink = "" ; 
			}
			$content .= "
			<tr>
				<td><input type=\"text\"  value=\"$name\" name=\"group_$id\" style=\"width: 150\" maxlength=\"20\"></td>
				<td class=\"text\"><input type=\"text\"  value=\"$description\" name=\"description_$id\" style=\"width: 260\" maxlength=\"100\"></td>
				<td width=\"1\"><input name=\"hidden_$id\" type=\"checkbox\" value=\"checked\" $hidden></td>
				<td width=\"1\" align=center>$deletelink</td>
			<tr>
			";
		}
	}
}	

$addlink = "<a href=\"javascript:;\" onClick=\"document.forms[0].submit()\"><img border=\"0\" src=\"../images/icon-add.gif\"></a>"; 

$id = $next_id+1;
$content .= "
		<tr>
			<td height=40><input type=\"text\"  value=\"\" name=\"group_$id\" style=\"width: 150\" maxlength=\"20\"></td>
			<td height=40 class=\"text\"><input type=\"text\"  value=\"\" name=\"description_$id\" style=\"width: 260\" maxlength=\"100\"></td>
			<td height=40 width=\"1\"><input name=\"hidden_$id\" type=\"checkbox\" value=\"checked\"></td>
			<td height=40 width=\"1\" align=center>$addlink</td>
		<tr>
";


/*if ($a_form_vars['action'] == "addgroup") {
	$content .= "<tr><td width=\"1\">&nbsp;</td>
	<td><input type=\"text\" name=\"group_$next_id\" style=\"width: 150\" maxlength=\"20\"></td>
	<td width=\"1\"><input name=\"hidden_$next_id\" type=\"checkbox\" value=\"checked\"></td>
	</tr>";
} */

$content = "<table cellpading=0 cellspacing=0 border=0>$content</table>";



$topbar = $content ;

?><HTML>
<HEAD>
<?
print($header_content);
?>
<TITLE>Edit item groups</TITLE>
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
<p><span class="titlebold"><strong>Edit Item Groups</strong></span><br>
<!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
  <a href="javascript:;" onClick="document.forms[0].action.value='addgroup';document.forms[0].submit()" class="text">Add 
  Group</a></p>//-->
<form name="form1" method="post" action="">
  <p><? print($topbar); ?> 
    <input name="action" type="hidden" id="action" value="save">
    <input name="refreshopener" type="hidden" id="refreshopener" value="<? print($a_form_vars[refreshopener]); ?>">
  </p>
  <input name="Button" type="button" class="text" onClick="window.close()" value="Cancel">
  <input name="Submit2" type="submit" class="text" value="Save">
</form>
<p class="text">*Group will not be displayed in catalog on order site</p>
</BODY>
</HTML>
