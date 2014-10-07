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

//	$_SESSION['require_login'] = 0;

	SecureServerOn(false);
	$form_method = "POST";

	function get_prefill ($a_item) {
		global $_SESSION;
		$xml_prefill_sets = $a_item['Prefill'];
		$a_prefill_tree = xml_get_tree($xml_prefill_sets);
					
		if ($a_prefill_tree[0]['attributes']['SHOW'] == "Y" || $a_prefill_tree[0]['attributes']['ENABLE'] == "true") {
			$a_prefill_node = xml_find_node("SET",$a_prefill_tree[0]['children'],$_SESSION['prefill_set']);
			if ( is_array($a_prefill_node[0]['children']) ) {
				foreach ($a_prefill_node[0]['children'] as $field_prefill_node) {
					$id = $field_prefill_node['attributes']['ID']; $name = $field_prefill_node['value'];
					$a_prefill[$id] = $name ;
				}
			}
		}
		
		return $a_prefill;
	}
	
		
	
	$sql = "SELECT Name,Template,Prefill,FieldSections FROM Items WHERE ID='$_SESSION[itemid]' AND SiteID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_item = mysql_fetch_assoc($r_result);
	
	$itemname = $a_item[Name];
	
	
	// GET PREFILL AND TEMPLATE FIELDS INFO
	if ( $_SESSION['os_action'] == "edititem" ) {
		if ($_SESSION["modifyitem"]) {
			$title = "Modify Data" ;
		} else {
			$title = "Edit Data" ;
		}
		if ($_SESSION['overwrite_prefill'] != "1") {
			$a_prefill = cart_get_imprint($_SESSION['cartitemid']);
		} else {
			$a_prefill = get_prefill($a_item);
		}
	} elseif ($_SESSION['os_action'] == "additem") {
		$title = "Enter Data " ;
		$costqtyfield = "costqty-" . $_SESSION[itemid];
		list($cost,$qty) = explode(":",$_SESSION[$costqtyfield]);
		
		if ($_SESSION['prefill_set'] != "") {
			$a_prefill = get_prefill($a_item);
		}
	} else {
		$_SESSION["show_alert"] = true;
		$_SESSION["alert_msg"] = "Error. No action found when going to input screen.";
	}
	
	
	
	
	
	
	
	// Make sure this item is still in the cart if the cartitemid is set to some number
	$item_deleted = false;
	if ($_SESSION[cartitemid] != "" && intval($_SESSION[cartitemid]) > -1) {
		$sql = "SELECT ID FROM Cart WHERE ID='$_SESSION[cartitemid]' AND SiteID='$_SESSION[site]'";
		$res = dbq($sql);
		$item_in_cart = mysql_num_rows($res);
		
		if (!$item_in_cart) {
			$content = "
				<table cellpadding=8 cellspacing=0 border=0 width=\"590\">
					<tr>
						<td class=\"text\">
						This item was deleted. <a href=\"vp.php?site=$_SESSION[site]&ossid=$_SESSION[ossid]&os_page=catalog\">Go to catalog</a>.
						</td>
					</tr>
				</table>
			";
			
			$item_deleted = true;
		}	
	}
	
	if (!$item_deleted) {
		
		// Get the array of fields from template	
		$a_itemTemplate = xml_get_tree($a_item['Template']);
		$a_fields = GetFields($a_itemTemplate);
		
	//	print_r($a_fields);
		
		// Get the array of field sections 
		$a_field_sections = xml_get_tree($a_item['FieldSections']);
		
		
		// Get the array of which optional items are selected
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
	
	
		// Layout fields based on field sections
		if ( is_array($a_field_sections[0]['children']) ) {
			foreach ($a_field_sections[0]['children'] as $section_node) {
				$id = $section_node['attributes']['ID'];
				if ( ($section_node['attributes']['OPTIONAL'] == "Y" && $a_selected_options[$id] == "yes") || $section_node['attributes']['OPTIONAL'] != "Y") {
					$show_section = true;
				} else {
					$show_section = false;
				}


				$section = "";
				
				// Process fields in this section
				if ( is_array($section_node['children']) ) {
					foreach ($section_node['children'] as $field_node) {
						$f_id = $field_node['attributes']['ID'];

					//	$field_node['template_attributes'] = $a_fields[$f_id];
						// Get input row
						if ($show_section && isset($a_fields[$f_id])) {
							$section .= make_input_row($field_node);
						}
						
						$a_field_used[$f_id] = "yes";
					}
				} // end fields for section
				if ($section != "") {
					if ($id == "NOTSET") { $name = "&nbsp;"; } else { $name = $section_node['attributes']['NAME']; }

					$str_input .= "<tr><td colspan=\"4\" class=\"subtitle\">" . $name . "</td></tr>" .
					$section . "<tr><td colspan=\"4\"><img src=\"images/spacer.gif\" height=\"3\" width=\"1\"></td></tr>";
				}
			}
		}
	
		
		// Make buttons at the bottom
		if ($_SESSION['input_options'] != "0") {
			$buttons = "<a href=\"$script_name?site=$_SESSION[site]&os_page=input_options&ossid=$_SESSION[ossid]\">
			<input class=\"button\"  type=\"button\" value=\"&laquo; Change options\" ".
				"onClick=\"document.location = '$script_name?os_page=input_options&site=$_SESSION[site]&ossid=$_SESSION[ossid]')\"></a>&nbsp;&nbsp;";
		} elseif ($_SESSION["modifyitem"]) {
			$buttons = "<a href=\"$script_name?site=$_SESSION[site]&os_page=account&ossid=$_SESSION[ossid]\">
			<input class=\"button\" type=\"button\" value=\"&laquo; Cancel\" onClick=\"document.location='$script_name?os_page=account&site=$_SESSION[site]&ossid=$_SESSION[ossid]'\"></a>&nbsp;&nbsp;";
		} else {
			$buttons = "<a href=\"$script_name?site=$_SESSION[site]&os_page=catalog&ossid=$_SESSION[ossid]\">
			<input class=\"button\" type=\"button\" value=\"&laquo; Cancel\" onClick=\"document.location='$script_name?os_page=catalog&site=$_SESSION[site]&ossid=$_SESSION[ossid]'\"></a>&nbsp;&nbsp;";
		}
		
		$buttons .= "
		<input class=\"button\"  type=\"submit\" value=\"Save &amp; Preview &raquo;\">
		";
			
		$content = "
			<table border=0 cellpadding=6 cellspacing=0 width=590>
			$str_input
				<tr>
					<td>
					</td>
					<td colspan=\"3\" height=\"50\">
						$buttons 
					</td>
				</tr>
			</table>
			<input type=\"hidden\" name=\"sid\" value=\"$ossid\">
			<input type=\"hidden\" name=\"page\" value=\"preview\">";
				
		if ( $_SESSION['os_action'] == "additem") {
			$content .= "
				<input type=\"hidden\" name=\"os_action\" value=\"save_and_preview\">
				<input type=\"hidden\" name=\"itemid\" value=\"$_SESSION[itemid]\">
				<input type=\"hidden\" name=\"qty\" value=\"$qty\">
				<input type=\"hidden\" name=\"cost\" value=\"$cost\">";
		} else if ($_SESSION['os_action'] == "edititem") {
			$content .= "
				<input type=\"hidden\" name=\"os_action\" value=\"update_and_preview\">
				<input type=\"hidden\" name=\"cartitemid\" value=\"$_SESSION[cartitemid]\">";
		}
		
		$content .= "
		
		<script language=\"JavaScript\" type=\"text/JavaScript\">
			var last_prefix = ''
			function setPrefix(obj,id) {
				if (obj.value != 'none') {
					f_obj = findObj('field_' + id);
					label = obj[obj.selectedIndex].value
					currval = f_obj.value
					if (last_prefix == '') {
						for (i=0; i<obj.length; i++) {
							if (currval.substr(0,obj[i].value.length) == obj[i].value) {
								repstr = new RegExp(obj[i].value);			
								currval = currval.replace(repstr,'')
							}
						}
					} else {
						repstr = new RegExp(last_prefix);			
						currval = currval.replace(repstr,'')
					}
					f_obj.value = label + currval
					last_prefix = label
				}
			}
		</script>
		
		";
	}
				
	$atab		= array('0' => $itemname );
	$tabs 		= iface_make_tabs($atab, '0', "", '600');
	$content 	= iface_make_box($content,600,100,0);
	$os_sidebar 	= iface_make_sidebar($title, "Enter your information on the right and click &quot;save&quot; to add this item to your cart");
	if ($_SESSION["modifyitem"] != true) {
		$os_sidebar .= iface_make_cart_sidebar("Cart",$ossid);
	}
	$sPage 		= MakePageStructure($os_sidebar,$content,$tabs);

?>
