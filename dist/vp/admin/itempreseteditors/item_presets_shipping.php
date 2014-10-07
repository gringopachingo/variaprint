<!--
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
-->
<HTML>
<HEAD>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<TITLE>shipping</TITLE>
</HEAD>
<BODY bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<!-- URL's used in the movie-->
<!-- text used in the movie-->
<!--Loading...Edit shipping profilesRegionsMethodsShipping costs by weight0.001How shipping is calculated:-->
<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
 codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
 WIDTH="530" HEIGHT="415" id="shipping" ALIGN="">
 <PARAM NAME=movie VALUE="shipping.swf"><PARAM NAME=menu VALUE="false"> <PARAM NAME=quality VALUE=high> <PARAM NAME=bgcolor VALUE=#FFFFFF> <EMBED src="shipping.swf" quality=high bgcolor=#FFFFFF  WIDTH="530" HEIGHT="415" NAME="shipping" ALIGN=""
 TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>
</OBJECT>
</BODY>
</HTML>


<?
exit();
/*
session_name("mssid");
session_start();
$mssid = session_id();


require_once("../../inc/config.php");
require_once("../../inc/functions-global.php");
require_once("../inc/functions.php");
if (!$_SESSION["privilege_items_properties"]) {
	require_once("../inc/popup_log_check.php");
}


$action = $a_form_vars['action'];
$mode = $a_form_vars['mode'];
$profile_id = $a_form_vars['profile_id'];
$region_id = $a_form_vars['region_id'];
$method_id = $a_form_vars['method_id'];
$region_name = $a_form_vars['region_name'];
$method_name = $a_form_vars['method_name'];
if ($mode == "" ) $mode = "edit";



if ( $action == "create" || $action == "update") {
	// prepare xml
	$sql = "SELECT Definition FROM Shipping WHERE ID='$profile_id'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	$a_xml_tree = xml_get_tree($a_result['Definition']);

	$a_xml_tree = xml_update_value("shipping/region:$region_id/method:$method_id","HANDLINGCOST",$a_form_vars['item_handling_cost'],$a_xml_tree);			
	$a_xml_tree = xml_update_value("shipping/region:$region_id/method:$method_id","NAME",$method_name,$a_xml_tree);			
	$a_xml_tree = xml_update_value("shipping/region:$region_id","NAME",$region_name,$a_xml_tree);			
	$a_xml_tree = xml_update_value("shipping","HANDLINGCOST",$a_form_vars['order_handling_cost'],$a_xml_tree);			


	$a_weights = array_find_key_prefix("weight_",$a_form_vars,1);
	$a_costs = array_find_key_prefix("cost_",$a_form_vars,1);
	
	if ( is_array($a_weights) ) {
	//	asort($a_weights);
		foreach ($a_weights as $key => $weight) {
			$cost = $a_costs[$key];
			//if ($weight != "")  
			$a_xml_tree = xml_update_value("shipping/region:$region_id/method:$method_id/weights/weight:$key","WEIGHT",$weight,$a_xml_tree);			
			//if ($cost != "") 
			$a_xml_tree = xml_update_value("shipping/region:$region_id/method:$method_id/weights/weight:$key","COST",$cost,$a_xml_tree);			
		}
	}
	
	$xml = addslashes(xml_make_tree($a_xml_tree)); 
	
} else if ($action == "delete") {
	$sql = "SELECT Definition FROM Shipping WHERE ID='$profile_id'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	$a_xml_tree = xml_get_tree($a_result['Definition']);
	
	$a_xml_tree = xml_delete_node("shipping/region:$region_id/method:$method_id/weights/weight:$a_form_vars[delete_id]",$a_xml_tree);
	$xml = addslashes(xml_make_tree($a_xml_tree));
	
	$sql = "UPDATE Shipping SET Definition='$xml' WHERE ID='$profile_id'";
	dbq($sql);
	
}


if ( $action == "create" ) {
	$sql = "UPDATE Sites SET OrderHandlingCost='$a_form_vars[orderhandlingcost]', RushOrderHandlingCost='$a_form_vars[rushorderhandlingcost]' WHERE ID='$_SESSION[site]'";
//	$n_update = dbq($sql);
	
	$sql = "INSERT INTO Shipping SET Name='" . addslashes($a_form_vars[profile_name]) . "', Definition='$xml', SiteID='$_SESSION[site]'";
	$n_insert = dbq($sql);
	$profile_id = db_get_last_insert_id();

	if ($a_form_vars['default_profile'] == "yes") { 
		$sql = "UPDATE Sites SET ShippingID='$profile_id' WHERE ID='$_SESSION[site]'";
		dbq($sql);
	}

} else if ( $action == "update" ) {
	
	$sql = "UPDATE Sites SET OrderHandlingCost='$a_form_vars[orderhandlingcost]', RushOrderHandlingCost='$a_form_vars[rushorderhandlingcost]' WHERE ID='$_SESSION[site]'";
//	$n_update = dbq($sql);

	$sql = "UPDATE Shipping SET Name='$a_form_vars[profile_name]', Definition='$xml' WHERE ID='$profile_id'";
	dbq($sql);

	if ($a_form_vars['default_profile'] == "yes") { 
		$sql = "UPDATE Sites SET ShippingID='$profile_id' WHERE ID='$_SESSION[site]'";
		$n_update = dbq($sql);
	}
} else if ( $action == "updatepreset" ) {
	if ($a_form_vars['default_profile'] == "yes") { 
		$sql = "UPDATE Sites SET ShippingID='$profile_id' WHERE ID='$_SESSION[site]'";
		dbq($sql);
	}

}








// Main program *************************************************************************

$sql = "SELECT ShippingID FROM Sites WHERE ID='$_SESSION[site]'";
$n_result = dbq($sql);
$a_result = mysql_fetch_array($n_result);

if ( $profile_id == "" && $mode != "new" ) { 
	$profile_id = $a_result['ShippingID']; 
}
if ( $a_result['ShippingID'] == $profile_id ) { $default_profile = " checked"; }

$sql = "SELECT SiteID,Template FROM Shipping WHERE ID='$profile_id'";
$n_result = dbq($sql);
$a_result = mysql_fetch_assoc($n_result);
if ($a_result['Template'] != "Y" && $a_result['SiteID'] != $_SESSION[site]) { $profile_id = "" ; }

$sql = "SELECT ID,Name FROM Shipping WHERE SiteID='$_SESSION[site]'";
$n_result = dbq($sql);
$custom_len = mysql_num_rows($n_result);
//if ( mysql_num_rows($n_result) == 0 && $mode != "new") {
//	$topbar = "No shipping region profiles set up yet. <a href=\"item_presets_shipping.php?mode=new\" class=\"text\" >Click here</a> to set up a new one.";
	
//} else {


	// PROFILE MENU - From DB
	$profile_pd = "<select class=\"text\" name=\"profile\" onchange=\"document.location='item_presets_shipping.php?mode=edit&profile_id=' + this.value\">\n"; 
	if ( $profile_id == "" && $mode != "new") { 
		$profile_pd .= "<option value=\"\">Select a shipping region to edit...</option>\n"; 
	}
	$sql = "SELECT ID,Name FROM Shipping WHERE Template='Y'";
	$n_result2 = dbq($sql);
	while  ( $a_profile = mysql_fetch_assoc($n_result2) ) {  
		if ( $profile_id == $a_profile['ID'] || (trim($profile_id) == "" && $mode != "new")) { 
			$sel = "selected";  
			$profile_id = $a_profile['ID']; 
			$profile_name = $a_profile['Name']; 
			$preset_profile = 1;
			$mode = "editpreset"; 
		} else { 
			$sel = ""; 
		} 
		$profile_pd .= "<option value=\"$a_profile[ID]\" $sel>[$a_profile[Name]]</option>\n";
		 
	}
	
		while ( $a_profile = mysql_fetch_assoc($n_result) ) {
			if ( $profile_id == $a_profile['ID']) { 
				$sel = "selected";  
				$profile_name = $a_profile['Name']; 
			} else { 
				$sel = ""; 
			} 
			$profile_pd .= "<option value=\"$a_profile[ID]\" $sel>$a_profile[Name]</option>\n";
		}
		if ($mode == "new") {
			$profile_name = "Untitled Profile";
			$profile_pd .= "<option value=\"\" selected>$profile_name</option>\n";
		}
		$profile_pd .= "</select>&nbsp;&nbsp;&nbsp;";
		$topbar = $profile_pd . "<a href=\"item_presets_shipping.php?mode=new\" class=\"text\">add</a>";
		
			
	if ( $custom_len > 0 && !$preset_profile) {
		// Set up region & shipping method pulldown
		$sql = "SELECT Definition FROM Shipping WHERE ID='$profile_id'";
		$n_result = dbq($sql);
		$a_result = mysql_fetch_assoc($n_result);
		$a_shipping_tree = xml_get_tree($a_result['Definition']);
		
		$order_handling_cost = $a_shipping_tree[0]['attributes']['HANDLINGCOST'];
	
		// create region pulldown
		$next_region_id = 1;
		$have_region = false;
		$region_pd = "<select size=4 style=\"width:220\" class=\"text\" name=\"region\" onchange=\"document.location='item_presets_shipping.php?profile_id=$profile_id&mode=edit&region_id=' + this.value\">\n"; 
					
		if ( is_array( $a_shipping_tree[0]['children'] ) ) {
			foreach ($a_shipping_tree[0]['children'] as $a_region) {
				$a_region_attr = $a_region['attributes'] ;
				if ($region_id == "") { $region_id = $a_region_attr['ID']; }
				if ( $region_id == $a_region_attr['ID']) { 
					$sel = "selected";  
					$region_name = $a_region_attr['NAME']; 
					$a_method_node = $a_region['children'];
				} else { 
					$sel = ""; 
				} 
				if ($a_region_attr[NAME] != "") {
					$region_pd .= "<option value=\"$a_region_attr[ID]\" $sel>$a_region_attr[NAME]</option>\n"; 
					$have_region = true;
					if ( $next_region_id <= $a_region_attr['ID']) { $next_region_id = $a_region_attr['ID']+1; }
				}
			}
		}
		
		if (!$have_region || $action == "addregion") { 
			$region_name = "Untitled";
			$region_id = $next_region_id;
			$region_pd .= "<option value=\"$region_id\" selected>$region_name</option>\n";
		}
		
		if ($region_id == "other") { $sel = "selected"; } else { $sel = ""; } 
		$region_pd .= "<option value=\"other\" $sel>[All Other Countries/Regions]</option>\n</select>\n";
		
		
		// create method pulldown
		$method_pd = "<select  size=4 style=\"width:220\" class=\"text\" name=\"region\" onchange=\"document.location='item_presets_shipping.php?profile_id=$profile_id&mode=edit&region_id=$region_id&method_id=' + this.value\">\n"; 
		$a_method_node = xml_find_node("METHOD",$a_method_node);
		$next_method_id = 1;
		$have_method = false;
		
		if ( is_array($a_method_node) ) {	
			foreach ($a_method_node as $a_this_method_node) {
				$id = $a_this_method_node['attributes']['ID']; 
				$name = $a_this_method_node['attributes']['NAME'];
				if (trim($id) != "" && $id > 0) {
					if ( $method_id == "" && $action != "addmethod") { $method_id = $id; }				
					if ( $method_id == $id) { 
						$sel = "selected"; 
						$method_name = $name; 
						$a_weights_node = $a_this_method_node['children']; 
						$item_handling_cost = $a_this_method_node['attributes']['HANDLINGCOST']; 
					} else { 
						$sel = "";  
					} 
					$method_pd .= "<option value=\"$id\" $sel>$name</option>\n";
					$have_method = true;
					if ( $next_method_id <= $id) { $next_method_id = $id+1; }
				}
			}
		}
		
		if (!$have_method || $action == "addmethod") { 
			$method_name = "Untitled";
			$method_id = $next_method_id;
			$method_pd .= "<option value=\"$next_method_id\" selected>$method_name</option>\n";
		}
		$method_pd .= "</select>"; 
	
		
		// set up weight table
		$a_weights_node = xml_find_node("WEIGHTS",$a_weights_node);
		
		$nextid = 1;
		if ( is_array($a_weights_node[0]['children']) ) {
			foreach ($a_weights_node[0]['children'] as $weight) {
				$a_weight[] = array('weight' => $weight['attributes']['WEIGHT'], 'cost' => $weight['attributes']['COST'], 'id' => $weight['attributes']['ID']) ;
			}
			array_multisort($a_weight); 
			
			foreach ($a_weight as $weight) {
				$lbs = $weight['weight'];
				$cost = $weight['cost'] ;
				$id = $weight['id'];
				if ($lbs != "") {
					if ($id >= $nextid) { $nextid = $id+1; }
					
					$weights .= "
			  <tr> 
				<td height=\"20\" class=\"text\">up to</td>
				<td height=\"20\">&nbsp;&nbsp;</td>
				<td height=\"20\"><input class=\"text\" name=\"weight_$id\" type=\"text\" size=\"10\" value=\"$lbs\"> </td>
				<td height=\"20\">&nbsp;&nbsp;</td>
				<td height=\"20\" class=\"text\">lbs&nbsp;charge&nbsp;$</td>
				<td height=\"20\">&nbsp;</td>
				<td height=\"20\"><input class=\"text\" name=\"cost_$id\" type=\"text\" size=\"10\" value=\"$cost\"></td>
				<td height=\"20\">&nbsp;</td>
				<td height=\"20\" class=\"text\"><a href=\"item_presets_shipping.php?profile_id=$profile_id&mode=edit&region_id=$region_id&method_id=$method_id&delete_id=$id&action=delete\"><img border=\"0\" src=\"../images/icon-delete.gif\"></a> </td>
			  </tr>
				  ";
				}
			}
		}
		 $weights .= "
			  <tr> 
				<td height=\"30\" class=\"text\">up to</td>
				<td height=\"30\">&nbsp;&nbsp;</td>
				<td height=\"30\"><input class=\"text\" name=\"weight_$nextid\" type=\"text\" size=\"10\"> </td>
				<td height=\"30\">&nbsp;&nbsp;</td>
				<td height=\"30\" class=\"text\">lbs&nbsp;charge&nbsp;$</td>
				<td height=\"30\">&nbsp;</td>
				<td height=\"30\"><input class=\"text\" name=\"cost_$nextid\" type=\"text\" size=\"10\"></td>
				<td height=\"30\">&nbsp;</td>
				<td height=\"30\" class=\"text\"><a href=\"javascript:;\" onClick=\"if (document.forms[0].cost_$nextid.value==''||document.forms[0].weight_$nextid.value=='') { alert('Please fill in a weight and cost.'); } else { document.forms[0].submit(); }\"><img border=\"0\" src=\"../images/icon-add.gif\"></a></td>
			  </tr>
		";		
	}
	
//}	

// Set up mode specific variables
if ( $mode == "new" ) {
	$showinput = true;
	$action = "create";
	$title = "Create a custom shipping profile";
	
} else if ( $mode == "edit" ) {
	$showinput = true;
	$action = "update";
	$title = "Edit shipping profile for \"" . $profile_name . "\"";
	
} else if ($mode == "editpreset") {
	$showinput = false;
	$action = "updatepreset";
//	$title = "Edit shipping profile for \"" . $profile_name . "\"";
}

//print($mode . " " . $profile_id);
*/
?><html>
<head>
<?
print($header_content);
?>
<title>Edit Item Shipping</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="20" topmargin="30" marginwidth="20" marginheight="30">
<form action="<? print($_SERVER['SCRIPT_NAME']) ; ?>" method="post">
  <table width="500" border="0" cellspacing="0" cellpadding="0">
    <tr class="text"> 
      <td><? print($topbar); 
	?></td>
      <td align="right"> 
        <input name="default_profile" type="checkbox" id="default_profile" value="yes" <? print($default_profile); ?>>
        Use this profile for this order site.</td>
    </tr>
  </table>

  <input name="method_id" type="hidden" id="method_id" value="<? print($method_id); ?>">
  <input name="profile_id" type="hidden" id="profile_id" value="<? print($profile_id); ?>">
  <input name="mode" type="hidden" id="mode3" value="edit">
  <input name="region_id" type="hidden" id="region_id" value="<? print($region_id); ?>">
  <input name="action" type="hidden" id="action" value="<? print($action); ?>">
  <input name="site" type="hidden" id="site" value="<? print($_SESSION[site]); ?>">
  <?  
if ($preset_profile) { 
	print("  <br> <input class=\"text\" type=\"submit\" name=\"Submit\" value=\"Save\"><br><br>
<br>
<div  class=\"text\">Preset profiles are not editable.</div>");
 
  } else if ($showinput) { ?>
  <font size="+1"><strong><br>
  <span class="titlebold">
  <?
	print($title);
	?>
  &nbsp;</span> </strong></font> 
  <hr size="1" noshade>
  <table width="500" border="0" cellspacing="0" cellpadding="0">
    <tr class="text"> 
      <td height="25" colspan="2">Profile name 
        <input name="profile_name" type="text" class="text" id="profile_name" value="<? print($profile_name); ?>"></td>
    </tr>
    <tr class="text"> 
      <td height="25" colspan="2">Base order handling fee $ 
        <input name="order_handling_cost" type="text" class="text" id="orderhandlingcost2" value="<? print($order_handling_cost); ?>" size="10"> 
      </td>
    </tr>
    <tr class="text"> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr class="text"> 
      <td height="30" colspan="2"> 
        <table border="0" cellspacing="0" cellpadding="0">
          <tr class="text"> 
            <td height="22">Regions&nbsp;&nbsp;&nbsp;<a href="<? print("item_presets_shipping.php?profile_id=$profile_id&region_id=$region_id&action=addregion&mode=edit"); ?>">add</a></td>
            <td height="22">&nbsp;</td>
            <td height="22">Methods&nbsp;&nbsp;&nbsp;<a href="<? print("item_presets_shipping.php?profile_id=$profile_id&region_id=$region_id&action=addmethod&mode=edit"); ?>">add</a></td>
          </tr>
          <tr> 
            <td><? print($region_pd); ?></td>
            <td><strong>&nbsp;&raquo;&nbsp;</strong></td>
            <td><strong><? print($method_pd); ?> </strong></td>
          </tr>
        </table></td>
    </tr>
    <tr class="text"> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr class="text"> 
      <td height="25">&nbsp;</td>
      <td height="25"> Region&nbsp;name 
        <input name="region_name" type="text" class="text" id="region_name" value="<? print($region_name); ?>" size="15"> 
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Method name 
        <input name="method_name" type="text" class="text" id="method_name" value="<? print($method_name); ?>" size="15"></td>
    </tr>
    <tr class="text"> 
      <td height="25">&nbsp;</td>
      <td width="484" height="25">Item handling cost $ 
        <input name="item_handling_cost" type="text" class="text" id="item_handling_cost" value="<? print($item_handling_cost); ?>" size="10"></td>
    </tr>
    <tr class="text"> 
      <td width="16">&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="text"> 
      <td height="20">&nbsp;</td>
      <td height="20"><strong>Shipping costs by weight</strong></td>
    </tr>
    <tr class="text"> 
      <td height="30">&nbsp;</td>
      <td height="30"> 
        <table border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td height="22">&nbsp;</td>
            <td height="22">&nbsp;</td>
            <td height="22" class="text"><strong>Weight</strong></td>
            <td height="22">&nbsp;</td>
            <td height="22">&nbsp;</td>
            <td height="22">&nbsp;</td>
            <td height="22" class="text"><strong>Cost</strong></td>
            <td height="22">&nbsp;</td>
            <td height="22">&nbsp;</td>
          </tr>
          <!-- 
		  List goes here
		  //-->
		  <? print($weights); ?>
        </table>
      </td>
    </tr>
    <tr class="text"> 
      <td width="16" height="30">&nbsp;</td>
      <td height="30" align="right"> 
        <input name="button" type="submit" class="text" id="button" value="Save"> </td>
    </tr>
  </table>
  <p  class="text"><br>
    Shipping is calculated by adding<br>
    <strong>the base order handling cost</strong> (added to an order's shipping 
    cost only once)<br>
    <strong>+ the item's handling cost</strong> (added to an order's shipping 
    cost for each item), <br>
    + <strong>the shipping cost entered</strong> in the weight/cost table. 
    <? } ?>
  </p>
  </form>
</body>
</html>
