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
require_once("inc/functions.php");

if (!$_SESSION["privilege_items_browse"]) {
	require_once("inc/popup_log_check.php");
}

if ( $a_form_vars['action'] == "create" ) {
	
	if ( $a_form_vars['item_based_on'] != "default" ) {
		$sql = "SELECT * FROM Items WHERE ID='$a_form_vars[item_based_on]'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_array($r_result);
	} else {
		$a_result[Template] = "<" . "?xml version=\"1.0\" encoding=\"iso-8859-1\"?" . ">
<item ID=\"2\" pagewidth=\"252\" pageheight=\"144\"><template></template>
<color_library><color name=\"Black\" id=\"0\" c=\"0\" m=\"0\" y=\"0\" k=\"100\"/></color_library></item>";
	}	
	
	if ($a_form_vars['item_name'] !="") { 
		$name = $a_form_vars['item_name'] ;
	} else {	
		$name ="Untitled Item"; 
	}
	
	if ($a_form_vars["customizable"] == "Yes") {
		$custom = "Y";
	} else {
		$custom = "N";
	}
	
	$sql ="INSERT INTO Items SET 
		Name='". addslashes($name) ."', 
		Custom='" . addslashes($custom) . "',
		Description='" . addslashes($a_result[Description]) . "',
		GroupID='" . addslashes($a_result[GroupID]) . "', 
		MasterUID='" . addslashes($a_form_vars[master_uid]) . "', 
		SiteID='" . addslashes($_SESSION[site]) . "',
		ImpositionID='" . addslashes($a_result[ImpositionID]) . "', 
		Pricing='" . addslashes($a_result[Pricing]) . "', 
		Template='" . addslashes($a_result[Template]) . "',
		VendorUsername='" . addslashes($a_result[VendorUsername]) . "',
		Weight='" . addslashes($a_result[Weight]) . "',
		SmallIconLink='" . addslashes($a_result[SmallIconLink]) . "',
		SmallShadow='" . addslashes($a_result[SmallShadow]) . "',
		LargeIconLink='" . addslashes($a_result[LargeIconLink]) . "',
		LargeShadow='" . addslashes($a_result[LargeShadow]) . "',
		PDFProof='" . addslashes($a_result[PDFProof]) . "',
		ReqApproval='" . addslashes($a_result[ReqApproval]) . "',
		Prefill='" . addslashes($a_result[Prefill]) . "',
		FieldSections='" . addslashes($a_result[FieldSections]) . "',
		TestData='" . addslashes($a_result[TestData]) . "'
		";
	
	dbq($sql);	
	print("
	<script language=\"javascript\">
		window.opener.location.href = 'vp.php?action=item_list'
		window.close()
	</script>
	"
	); 
	exit; 
}





//**************************

$sql ="SELECT Name FROM Sites WHERE ID='$_SESSION[site]'"; 
$r_result = dbq($sql);
$a_result = mysql_fetch_array($r_result); 
$site_name = $a_result['Name']; 
$sql = "SELECT ID,Name FROM Items WHERE SiteID='$_SESSION[site]'"; 
$r_result = dbq($sql);
$sql = "SELECT ID,Name FROM Items WHERE IsTemplate='true' ORDER BY Name"; 
$r_result2 = dbq($sql);
$pd = "<select name=\"item_based_on\" style=\"width:180\">\n";
//$pd .= "<option value=\"default\">[Blank Item]</option>\n";

while ( $a_item = mysql_fetch_array($r_result2) ) {
	$pd .= "<option value=\"$a_item[ID]\">[".$a_item[Name]."]</option>\n";
}
while ( $a_item = mysql_fetch_array($r_result) ) {
	$pd .= "<option value=\"$a_item[ID]\">$a_item[Name]</option>\n";
}
$pd .= "</select>\n";

?>
<html>
<head>
<?
print($header_content);
?>
<title>Create a New Item</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body background="images/bkg-groove.gif" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form1" method="post" action="">
<table width="98%" height="80%" border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
  <td align="center">
  <table border="0" cellpadding="5" cellspacing="0">
    <tr>
      <td colspan="2" nowrap class="title"><strong> Create a new item in order
          site "<? print($site_name); ?>"</strong></td>
    </tr>
    <tr>
      <td align="right" class="text">New item name:</td>
      <td>
        <input name="item_name" type="text" id="item_name2">
      </td>
    </tr>
    <tr>
      <td class="text">&nbsp;</td>
      <td class="text"><input name="customizable" type="checkbox" id="customizable" value="Yes" checked>
        Customizable by buyer</td>
    </tr>
    <tr>
      <td align="right" class="text">Based on item:</td>
      <td><? print($pd); ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="button" value="Cancel" onClick="window.close()">
      <input type="submit" name="Submit" value="Create Item">
        <input name="action" type="hidden" id="action" value="create">
        <input name="master_uid" type="hidden" id="master_uid" value="<? print($_SESSION['user_id'] ); ?>">
      </td>
    </tr>
  </table>
</form>
</body>
</html>
