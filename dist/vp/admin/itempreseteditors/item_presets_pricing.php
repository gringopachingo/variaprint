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

//session_save_path("/www/tmp");
session_name("mssid");
session_start();
$mssid = session_id();

$itemid = $a_form_vars['itemid'];


// ******************* Save here *****************************************************************
if ($a_form_vars['my_action'] == "add" || $a_form_vars['my_action'] == "update") {
 	$type = $a_form_vars['price_type'];
//	$c_first_price = $a_form_vars['c_first_price'];
//	$c_first_amount = $a_form_vars['c_first_amount'];
//	$c_add_price = $a_form_vars['c_add_price'];
//	$c_add_amount = $a_form_vars['c_add_amount'];
//	$c_max_amount = $a_form_vars['c_max_amount'];
	
	$xml = "<" . "?xml version=\"1.0\" encoding=\"iso-8859-1\"?" . ">
<pricing pricetype=\"$type\">
<pairs>
";
//<compute c_first_price=\"$c_first_price\" c_first_amount=\"$c_first_amount\" c_add_price=\"$c_add_price\" c_add_amount=\"$c_add_amount\" c_max_amount=\"$c_max_amount\"/>

	$a_qty = array_find_key_prefix("p_qty_", $a_form_vars, 1) ;
	$a_cost = array_find_key_prefix("p_cost_", $a_form_vars, 1) ;
	
	foreach ( $a_qty as $key => $qty) {
		$cost = number_format(str_replace(" ","",str_replace(",","",$a_cost[$key])), 2, ".","");
	//	$qty = number_format($qty, 0);
		if ($cost != "" && $qty != 0 && $qty != "" && $a_form_vars['deleteItem'] != $key) {
			$xml .= "<pair id=\"$key\" qty=\"$qty\" cost=\"$cost\"/>\n";
		}
	}
		
	$xml .= "</pairs>
	</pricing>";
	$xml = addslashes($xml);
}

if ( $a_form_vars['my_action'] == "add") {
	$sql = "INSERT INTO Pricing SET Definition='$xml', SiteID='$_SESSION[site]', Name='$a_form_vars[style_name]'";
	$nUpdate = dbq($sql);
	
	$a_form_vars['my_action'] == "update";
	$a_form_vars['mode'] = "edit";
	$a_form_vars['price_id'] = db_get_last_insert_id();
// 	print("It should have been created.");
	
} else if ($a_form_vars['my_action'] == "update") {
	$sql = "UPDATE Pricing SET Definition='$xml',Name='$a_form_vars[style_name]' WHERE ID='$a_form_vars[price_id]'";
	$nUpdate = dbq($sql);

}


$mode = $a_form_vars['mode'];
if ( $mode == "" ) $mode = "edit";
$showinput = false;

// ******************* Get the pricing here ******************************************************
$sql = "SELECT ID,Name,Definition FROM Pricing WHERE SiteID='$_SESSION[site]'";
$nResult = dbq($sql);

if ( mysql_num_rows($nResult) == 0 && $mode != "new") {
	
	$content = "<div  class=\"text\">There aren't any pricing styles set up yet. <a href=\"item_presets_pricing.php?mode=new\"  class=\"text\">Click here</a> to add a new one.</div>";
	$showinput = false;
	
} else {
	
	if (mysql_num_rows($nResult) > 0) {
		$pd = "<select class=\"text\" name=\"price_id\" onchange=\"document.location='item_presets_pricing.php?mode=edit&price_id=' + this.value\">";
		if ( $mode == "new") { $pd .= "<option value=\"\">Select pricing preset to edit...</option>\n"; }
		while ( $a_price = mysql_fetch_assoc($nResult) ) {
			if ( !isset($a_form_vars['price_id']) &&  $mode != "new") $a_form_vars['price_id'] = $a_price['ID'];
			
			if ($a_price['ID'] == $a_form_vars['price_id']) { 
				$sel = "selected"; 
				$presetname = $a_price['Name']; 
				$a_this_price = $a_price;
			} else { $sel = ""; }
			$pd .= "<option value=\"$a_price[ID]\" $sel>" . $a_price[Name] . "</option>\n";
		}
		$pd .= "</select>&nbsp;&nbsp; ";
	}
	
	$topbar = $pd . "<a href=\"item_presets_pricing.php?mode=new\" class=\"text\">add pricing style</a> " ;
	
	if ( $mode == "new" ) {
		$title = "Create New Pricing Style";
		$my_action = "add";
		$showinput = true;
	} else if ($a_form_vars['price_id'] != "" && $mode == "edit") {
		$title = "Edit Pricing Style \"" . $presetname . "\"";
		$my_action = "update";
		$showinput = true;
	}
	

	// $aItem = mysql_fetch_assoc($nResult);
	
	// $content = "<textarea name=\"pricing\" rows=\"10\" style=\"width:730; height:80\">$aItem[Pricing]</textarea><br><br>";
	
	$a_price_tree = xml_get_tree($a_this_price['Definition']);
	
	if ( $a_price_tree[0]['attributes']['TYPE'] == "pairs") {
		$p_checked = "checked";
	} else  { 
		$c_checked = "checked";
	} 

	$row_counter = 1;
	if ( is_array($a_price_tree[0]['children']) ) {
		// create qty/cost pairs
		
		$a_pairs = xml_find_node("pairs",$a_price_tree[0]['children']);	
		if ( is_array($a_pairs[0]['children']) ) {
			foreach ( $a_pairs[0]['children'] as $pair ) {
				$cost = $pair['attributes']['COST']; $qty = $pair['attributes']['QTY'];
				if ( $cost != "" || $qty != "") {
				$p_row.= "
				  <tr> 

					<td height=\"20\"> <input class=\"text\" name=\"p_qty_$row_counter\" type=\"text\" size=\"10\" value=\"$qty\"></td>
					<td width=\"20\" height=\"20\" align=\"right\"  class=\"text\">$&nbsp;</td>
					<td height=\"20\"> <input class=\"text\" name=\"p_cost_$row_counter\" type=\"text\" size=\"10\" value=\"". number_format($cost, 2, ".", ",") ."\"></td>
					<td height=\"20\" class=\"text\">
						<a href=\"javascript:;\" onClick=\"deletePrice('$row_counter')\">
						<img border=\"0\" src=\"../images/icon-delete.gif\">
						</a>
					</td>
					<td height=\"20\">&nbsp;</td>
				  </tr>
				";
				++$row_counter;
				}
			}
		}
		
		/*	
		// get values for computed cost
		$a_compute = xml_find_node("compute",$a_price_tree[0]['children']);
		$a_attrib =  $a_compute[0]['attributes']; //[0]['children']['attributes']
		$c_first_price	= $a_attrib['C_FIRST_PRICE'];
		$c_first_amount	= $a_attrib['C_FIRST_AMOUNT'];
		$c_add_price	= $a_attrib['C_ADD_PRICE'];
		$c_add_amount	= $a_attrib['C_ADD_AMOUNT'];
		$c_max_amount	= $a_attrib['C_MAX_AMOUNT'];
		*/
	}
	// $row_counter) 
	$p_row.= "
		  <tr> 

			<td height=\"40\"> <input class=\"text\" name=\"p_qty_$row_counter\" type=\"text\" size=\"10\"></td>
			<td width=\"20\" height=\"30\" align=\"right\"  class=\"text\">$&nbsp;</td>
			<td height=\"40\"> <input  class=\"text\" name=\"p_cost_$row_counter\" type=\"text\" size=\"10\"></td>
			<td height=\"30\" class=\"text\">
				<a href=\"javascript:;\" onClick=\"document.forms[0].submit()\">
					<img border=\"0\" src=\"../images/icon-add.gif\">
				</a>
			</td>
		  </tr>
	";
}


?><html>
<head>
<?
print($header_content);
?>
<title>Edit Item</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
function deletePrice(itemtodelete) {
	document.forms[0].deleteItem.value = itemtodelete
	top.mainFrame.document.form1.submit()
}

</script>


<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="20" topmargin="30" marginwidth="20" marginheight="30">
<form name="form1" action="<? print($_SERVER['SCRIPT_NAME']); ?>" method="post">
  <? 
print($topbar);
?>
  <? 
print($content);
if ($showinput) { ?>
  <table width="517" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td colspan="2">&nbsp; </td>
    </tr>
    <tr> 
      <td colspan="2"> <span class="titlebold"><strong><? print($title); ?> &nbsp; 
        </strong></span> <hr size="1" noshade></td>
    </tr>
    <tr> 
      
    <td height="30" colspan="2"> <table width="517" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td class="text">Name: 
            <input name="style_name" type="text" class="text" id="style_name2" value="<? print($presetname); ?>"> 
          </td>
          <td colspan="2" align="right"> <input name="price_id" type="hidden" id="price_id3" value="<? print($a_form_vars['price_id']); ?>"> 
            <input name="Submit" type="submit" class="text" value="Save"> </td>
        </tr>
      </table>
      <table width="517" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td height="30"> <table border="0" cellspacing="0" cellpadding="1">
              <tr> 
                <td height="25" class="text">qty</td>
                <td width="20" height="25" align="right">&nbsp;</td>
                <td height="25" class="text">cost</td>
                <td width="20" height="25" align="right">&nbsp;</td>
                <td height="25">&nbsp;</td>
                <td height="25">&nbsp;</td>
              </tr>
              <? print($p_row); ?> 
            </table>
            <input name="my_action" type="hidden" id="my_action" value="<? print($my_action); ?>"> 
            <input name="deleteItem" type="hidden" id="deleteItem" value="nothing"> 
            <input name="mode" type="hidden" id="mode" value="<? print($mode); ?>"> 
            <input name="price_type" type="hidden" id="price_type" value="pairs"> 
          </td>
        </tr>
        <tr> 
          <td height="30" align="right">&nbsp; </td>
        </tr>
      </table>
  <? } ?>
</form>
</body>
</html>
