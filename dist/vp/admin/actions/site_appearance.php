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

$_SESSION['tm'] = "1";

$sel_menu = "site";
$submenu = "
Appearance&nbsp;&nbsp;&nbsp;&nbsp;
<a href=\"vp.php?action=site_settings&user_id=$user_id\">Settings</a>&nbsp;&nbsp;&nbsp;&nbsp;
";

if ( !isset($_SESSION[appearance_tab]) )  $_SESSION[appearance_tab] = "tab_10";

// READ FROM DB
$sql = "SELECT * FROM Sites WHERE ID='$_SESSION[site]'";
$nResult = dbq($sql);
if ( mysql_num_rows($nResult) == 0 ) {
	header("location: ../admin/");
	
	exit();  // Error. No Site Selected. (appearance) 
} 
$aSite = mysql_fetch_assoc($nResult);
$sSiteSettings = $aSite['SettingsTmp'];
$aXMLTree = xml_get_tree($sSiteSettings);

 
/* Get the array ready for prefilling */ 
$aSiteSettingsRaw = $aXMLTree[0][children];

// This is the prefill information
if ( is_array($aSiteSettingsRaw) ) {
	foreach ( $aSiteSettingsRaw as $k=>$node ) {
		$pid = $node['attributes']['ID']; 
		$aSiteSettings[$pid] = $node['value'] ;
	}
}


// ACTION PROCESSED HERE
if ( $_GET['save_action'] == "Discard" ) {
	$sql = "UPDATE Sites SET SettingsTmp=Settings WHERE ID='$_SESSION[site]'";
	dbq($sql, "DISCARDING SITE PROPERTIES");
	header("Location: " . $_SERVER['SCRIPT_NAME'] . "?appearance_tab=$_SESSION[appearance_tab]&action=site_appearance");

} else if ( $_GET['save_action'] == "Publish" ) {
	$sql = "UPDATE Sites SET Settings=SettingsTmp WHERE ID='$_SESSION[site]'";
	dbq($sql, "PUBLISHING SITE PROPERTIES");
	header("Location: " . $_SERVER['SCRIPT_NAME'] . "?appearance_tab=$_SESSION[appearance_tab]&action=site_appearance");

} else if ( $_GET['save_action'] == "Save" ) {
	$thistab = $_SESSION['appearance_tab'];
	foreach ($a_form_vars as $f_key=>$f_val) {
		$aXMLTree = xml_update_value("properties/property:$f_key","CDATA", urldecode($f_val),$aXMLTree);
	}
	
	$sXML = addslashes( xml_make_tree($aXMLTree) );
		
	$sql = "UPDATE Sites SET SettingsTmp='$sXML' WHERE ID='$_SESSION[site]'";
	dbq($sql, "SAVING SITE PROPERTIES");
	
	header("Location: " . $_SERVER['SCRIPT_NAME'] . "?appearance_tab=$_SESSION[appearance_tab]&action=site_appearance");
}

$_SESSION[save_action] = "";
// session_save_vars( $ms_sid, array('save_action' => '') );



// START CONTENT *******************************************************************************************
$content = "
<script language=\"JavaScript\" type=\"text/JavaScript\">
function doAction (obj) {
	if (obj.value == \"Test\") { 
		popupWin('../vp.php?site=$_SESSION[site]&mode=test','test','toolbar=1,location=1,status=1,resizable=1,scrollbars=1,width=780,centered=1') } 
	else {
		document.forms[0].save_action.value = obj.value
		document.forms[0].submit()
	}
}
</script> 

";

$thistab = $_SESSION[appearance_tab];
$prefill = $aSiteSettings;

$site_attributes = new siteAttrib("xml/site_appearance.xml",$prefill,1,"appearance_tab");
foreach($prefill as $k=>$v) {
	if ($k!="") $js_save_elem .= "a_save_elem['$k']=\"\"; \n ";
}


// SET UP TABS
$tabs = makeTabs($site_attributes->GetTabsArray(), "&action=site_appearance", $_SERVER['SCRIPT_NAME'], $thistab, "appearance_tab");
$formfields = $site_attributes->GetContent(); 

// CHECK TO SEE IF CHANGES HAVE BEEN MADE SINCE SITE WAS PUBLISHED
$sql = "SELECT Settings,SettingsTmp FROM Sites WHERE ID='$_SESSION[site]'";
$nResult = dbq($sql);
$aSettings = mysql_fetch_assoc($nResult);
if ($aSettings[Settings] == $aSettings[SettingsTmp]) {  $synced = true; $disabled = " disabled "; }

$content .= "

	<table cellpadding=0 cellspacing=0 border=0 width=\"600\" height=40>
		<tr>
			<td><strong class=\"title\">Edit Site Appearance</strong></td>
			<td align=right>
				<select name=\"publish_revert_test\" >
					<option value=\"Test\"  selected>Test Changes</option>
					<option value=\"Publish\" $disabled>Publish Changes</option>
					<option value=\"Discard\" $disabled>Discard Changes</option>
				</select>&nbsp;<input type=\"button\" value=\"Go\"  onClick=\"doAction(document.forms[0].publish_revert_test)\">
			</td>
		</tr>
	</table>
	
<!-- START TABS //-->
	$tabs
<!-- END TABS //-->
	
	";
$content .= "
	<input type=\"hidden\" name=\"site\" value=\"$_SESSION[site]\">
	<input type=\"hidden\" name=\"appearance_tab\" value=\"$_SESSION[appearance_tab]\">
	<input type=\"hidden\" name=\"save_action\" value=\"Save\">
";
/**/
$content .= "
	<br><div align=\"right\">
	<input type=\"reset\" value=\"Revert\" onClick=\"elem_saved=true;\">
	<input onMouseDown=\"set_save(true);saved_btn_clicked=true\" type=\"submit\" name=\"save\" value=\"Save\">
	</div>
<br>
<!-- START FIELDS //-->
	$formfields
<!-- END FIELDS //-->
	<br><div align=\"right\">
	<input type=\"reset\" value=\"Revert\" onClick=\"elem_saved=true;\">
	<input onMouseDown=\"set_save(true);saved_btn_clicked=true\" type=\"submit\" name=\"save\" value=\"Save\">
	</div>
		";
	 ?>