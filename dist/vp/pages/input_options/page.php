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

	
	SecureServerOn(false);
	$form_method = "POST";
	
	if ( $_SESSION['os_action'] == "edititem" ) {
		$sql = "SELECT Name,Prefill,FieldSections FROM Items WHERE ID='$_SESSION[itemid]' AND SiteID='$_SESSION[site]'";
		$r_result = dbq($sql);
		$a_item = mysql_fetch_assoc($r_result);
		$a_prefill = cart_get_imprint($_SESSION['cartitemid']);
		$itemname = $a_item[Name];
		$title = "Edit Select Options " ;
	} else if ($_SESSION['os_action'] == "additem") {
		$sql = "SELECT Name,Prefill,FieldSections FROM Items WHERE ID='$_SESSION[itemid]' AND SiteID='$_SESSION[site]'";
		$r_result = dbq($sql);
		$a_item = mysql_fetch_assoc($r_result);
		$itemname = $a_item[Name];
		$title = "Select Options " ;
		$costqtyfield = "costqty-" . $_SESSION[itemid];
		list($cost,$qty) = explode(":",$_SESSION[$costqtyfield]);
	} else {
		print("Error. No action found when going to input page.");
	}

	
	$a_itemTemplate = xml_get_tree($a_item['Template']);
	$aFields = GetFields($a_itemTemplate);
	
	$xml_prefill_sets = $a_item['Prefill'];
	$xml_option_sets = $a_item['FieldSections'];
	
	$a_prefill_tree = xml_get_tree($xml_prefill_sets);
	$a_option_tree = xml_get_tree($xml_option_sets);
	
	$have_prefill = false;
	$have_optionalfields = false;
	
//	print_r($a_prefill_tree);
	
	// Create selection menu for prefill sets
	if ($a_prefill_tree[0]['attributes']['ENABLE'] == "true" || $a_prefill_tree[0]['attributes']['SHOW'] == "Y") {
		$prefill_set_label = $a_prefill_tree[0]['attributes']['DESCRIPTION'];
		$option_selector = "<select name=\"prefill_set\" size=\"6\" style=\"width:250\">\n";
				if ( is_array($a_prefill_tree[0]['children']) ) {
				$have_prefill = true;
				foreach ($a_prefill_tree[0]['children'] as $prefill_node) {
					$id = $prefill_node['attributes']['ID'];
					$name = $prefill_node['attributes']['NAME'];
					if ($id == $_SESSION['prefill_set']) { $sel = "selected"; } else { $sel = ""; }
					$option_selector .= "<option value=\"$id\" $sel>$name</option>\n";
				}
			}
		$option_selector .= "</select>\n";
	}
	
	
	// Create checkbox list of optional field sections
	if ( is_array($a_option_tree[0]['children']) ) {
		$options_set_label = $a_option_tree[0]['attributes']['DESCRIPTION'];
		
		$sql = "SELECT OptionalFieldSets FROM Cart WHERE ID='$_SESSION[cartitemid]' AND SiteID='$_SESSION[site]'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		$xml_options = $a_result['OptionalFieldSets'];
		$a_options_tree = xml_get_tree($xml_options);
		if ( is_array($a_options_tree[0]['children']) ) {
			foreach ($a_options_tree[0]['children'] as $this_node) { 
				$id = $this_node['attributes']['ID'];
				$a_selected_options[$id] = "yes";
			}
		}
		
		$options .=	"
				<table cellpadding=0 cellspacing=0 border=0>\n";

		foreach ($a_option_tree[0]['children'] as $option_node) {
			if ( $option_node['attributes']['OPTIONAL'] == "Y") { 
				$have_optionalfields = true;
				$id = $option_node['attributes']['ID'];
				$name = $option_node['attributes']['NAME'];
				if ( $a_selected_options[$id] == "yes" ) { $sel = "checked"; } else { $sel = ""; }
				$options .= "<tr><td width=\"22\" height=\"30\"><input type=\"checkbox\" name=\"optional_fieldset_$id\" value=\"yes\" $sel></td><td class=\"text\" height=\"30\"> $name  <td></tr> \n";
			}		
			
		}
		$options .=	"
				<tr><td colspan=\"2\"></td></tr>
				</table>\n";
		
	}
	
	
	$_SESSION['no_options'] = false;
	
	// CHECK TO SEE IF WE EVEN NEED TO DISPLAY THIS PAGE
	if (!$have_prefill && !$have_optionalfields) {
		$_SESSION['no_options'] = true;
		header("Location: vp.php?input_options=0&os_page=input&prefill_set=&ossid=$ossid");
		exit;
	} else {

		// CREATE THE CONTENT FOR THE PAGE
		if ($_SESSION['os_action'] == "edititem") {
			$overwrite = "
				<table cellpadding=0 cellspacing=0 border=0 height=30><tr>
					<td width=\"22\">
						<input name=\"overwrite_prefill\" type=\"hidden\" value=\"0\">
						<input name=\"overwrite_prefill\" type=\"checkbox\" value=\"1\">
					</td>
					<td class=\"text\"> Replace information with defaults. </td>
				</tr></table>
			";
		} else {
			$overwrite = "<input name=\"overwrite_prefill\" type=\"hidden\" value=\"0\">"; 
		}
			
		$content = "
            <table cellpadding=6 cellspacing=0 border=0 width=\"590\">
				<tr><td class=\"subtitle\">
				<br>
				<table cellpadding=0 cellspacing=0 border=0 width=\"560\">
					<tr>
						<td width=\"280\" valign=\"top\">";
			
		if ($have_prefill) {	
			$content .= "
							<span class=\"subtitle\">$prefill_set_label</span><br><br>
							
							$option_selector
				"; 
		}
		$content .= "			
						</td>
						<td>&nbsp;&nbsp;&nbsp;</td>
						<td width=\"280\" class=\"text\"  valign=\"top\">
						";
		if ($have_optionalfields) {
			$content .= "
							<span class=\"subtitle\">$options_set_label</span><br><br>
							
							$options
						";
		}
			
		$content .= "
						</td>
					</tr>
				</table>
				$overwrite 
				
				<br><br>
				<a href=\"$script_name?site=$_SESSION[site]&os_page=catalog&ossid=$_SESSION[ossid]\">
				<input class=\"button\" type=\"button\" value=\"&laquo; Cancel\" ".
					"onClick=\"document.location='$script_name?os_page=catalog&site=$_SESSION[site]&ossid=$_SESSION[ossid]'\"></a>&nbsp;&nbsp;
				<input type=\"submit\" class=\"button\" value=\"Continue &raquo;\">
				
				</td></tr>
			</table>
			
			<input type=\"hidden\" name=\"sid\" value=\"$ossid\">
			<input type=\"hidden\" name=\"input_options\" value=\"1\">
			<input type=\"hidden\" name=\"os_page\" value=\"input\">";
	
	
		// HIDDEN FIELDS TO SET PROPERTIES FOR NEW PAGE
		if ( $_SESSION['os_action'] == "additem") {
			$content .= "
				<input type=\"hidden\" name=\"os_action\" value=\"input\">
				<input type=\"hidden\" name=\"itemid\" value=\"$_SESSION[itemid]\">
				<input type=\"hidden\" name=\"qty\" value=\"$qty\">
				<input type=\"hidden\" name=\"cost\" value=\"$cost\">";
		} else if ($_SESSION['os_action'] == "edititem") {
			$content .= "
				<input type=\"hidden\" name=\"os_action\" value=\"input\">
				<input type=\"hidden\" name=\"cartitemid\" value=\"$_SESSION[cartitemid]\">";
		}
	
			
		$atab		= array('0' => $itemname );
		$tabs		= iface_make_tabs($atab, '0', "", '600');
		$content 	= iface_make_box($content,600,100,0);		
		$os_sidebar = iface_make_sidebar($title, "Select options for this item.")  ;
		if ($_SESSION["modifyitem"] != true) {
			$os_sidebar .= iface_make_cart_sidebar("Cart",$ossid);
		}
		$sPage 		= MakePageStructure($os_sidebar,$content,$tabs);
	}
?>
