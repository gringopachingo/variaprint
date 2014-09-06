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


SecureServerOn(false);

function item_row($a_item) {

	if ($a_item['Description'] != "" || $a_item['LargeIconLink'] != "") {
		// Show button
		$detail_btn = "<a href=\"javascript:;\" onClick=\"popupWin('itemdetail.php?site=$_SESSION[site]&item=$a_item[ID]&os_sid=$_SESSION[os_sid]','','width=500,height=450,resizable=1,scrollbars=1,centered=1')\"><img src=\"_sites/$_SESSION[site]/ifaceimg/icon-info.gif\" border=\"0\"></a>";
	} else {
		$detail_btn = "<img src=\"images/spacer.gif\" border=\"0\" width=\"17\">";
	}
	
	$items =  "
				<table cellpadding=6 cellspacing=0 border=0 width=\"595\">
					<tr> 
						<td width=\"11\">$detail_btn</td>
						<td class=\"text\" bgcolor=\"$rowhilitecolor\">" . $a_item['Name'] . "</td>
						<td width=\"110\">$a_item[qtypulldown]</td>
						<td width=\"150\" align=\"right\">
						<input type=\"button\" onclick=\"document.forms[0].itemid.value='$a_item[ID]'; document.forms[0].submit() \" value=\"$a_item[button]\" class=\"button\"> 
						</td>
					</tr>
				</table> 
	";
	
	return $items;
}

function item_icon($a_item) {
	global $bgcolor;
	$exists = true;
	
	
	if ($a_item['SmallIconLink'] != "") {
		$path = "_sites/" . $_SESSION['site'] . "/images/"; $img = $path . $a_item['SmallIconLink'];
		if ( file_exists($img) ) {  $img_link = "<img src=\"$img\" border=\"0\">"; } else { $exists = false; }
	} else {
		$exists = false;
	}
	if (!$exists) {
		$img_link = "<br><br>&nbsp;&nbsp;[&nbsp;image&nbsp;not&nbsp;found&nbsp;]&nbsp;&nbsp;<br><br><br>";
	}

	if ($a_item["SmallShadow"] != "N") {
		$img = iface_add_drop_shadow($img_link,"#eeeeee");
	} else {
		$img = $img_link;
	}
	
	if ($a_item['Description'] != "" || $a_item['LargeIconLink'] != "") {
		// Show button
		$detail_btn = "<a href=\"javascript:;\" onClick=\"popupWin('itemdetail.php?site=$_SESSION[site]&item=$a_item[ID]&os_sid=$_SESSION[os_sid]','','width=500,height=450,resizable=1,scrollbars=1,centered=1')\"><img src=\"_sites/$_SESSION[site]/ifaceimg/icon-info.gif\" align=\"absmiddle\" border=\"0\"></a>";
		$img = "<a href=\"javascript:;\" onClick=\"popupWin('itemdetail.php?site=$_SESSION[site]&item=$a_item[ID]&os_sid=$_SESSION[os_sid]','','width=500,height=450,resizable=1,scrollbars=1,centered=1')\">"
			.$img."</a>";
	} 
		
	$items = "
			<td valign=\"top\" width=\"182\" class=\"text\" ><br>
				" . $img . "
				
				<br><br>
				$detail_btn
				" . $a_item[Name] . "<br><br>
					$a_item[qtypulldown]<br><br>
					<input type=\"button\" onclick=\"document.forms[0].itemid.value='$a_item[ID]'; document.forms[0].submit() \" value=\"$a_item[button]\" class=\"button\">
				<div align=\"right\">
				</div>
			</td>
			<td width=\"16\">&nbsp;</td>						 
	";
	return $items;
}

	$itemtextstyle = $a_site_settings['CatalogItemTextStyle'];
	$rowhilitecolor = $a_site_settings['CatalogRowHiliteColor'];
	$title = $a_site_settings['CatalogTitle'];
	$description = $a_site_settings['CatalogText'];
	$os_sidebar = iface_make_sidebar($title, $description) . iface_make_cart_sidebar("Cart",$os_sid);
		
	// Get item groups
	$sql = "SELECT ItemGroups FROM Sites WHERE ID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	
	$a_xmlgroups = xml_get_tree($a_result['ItemGroups']);

	$a_itemNames = array(); $a_itemDescriptions = array();
	
	if ( is_array($a_xmlgroups[0]['children']) ) {
		foreach($a_xmlgroups[0]['children'] as $group) {
			$id = $group['attributes']['ID'];
			$sql = "SELECT ID FROM Items WHERE GroupID='$id' AND SiteID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$num_items = mysql_num_rows($r_result);
			
			if ($group['attributes']['HIDDEN'] != "Y" && $num_items > 0) {
				$a_tab_ids[] = $id;
				$a_group_names[$group['attributes']['ID']]= $group['attributes']['NAME'];
				$a_group_descriptions[$id] = $group['attributes']['DESCRIPTION'];
			}
		}
	}
	
	if ( count($a_group_names) > 0 ) {
		
		// TAB VIEW
		if ($a_site_settings['CatalogItemGroupStyle'] != "Stacked") {
			if ( isset($_SESSION['catalogtab']) && isset($a_group_names[$_SESSION['catalogtab']])) { 
				$ontab = $_SESSION['catalogtab']; 
			} else { 
				$ontab = $a_tab_ids[0]; 
			}
			
			$tabs = iface_make_tabs($a_group_names, $ontab, 'catalogtab', '600', 'catalog');
			
			// Group description
			$items = "
				<table cellpadding=6 cellspacing=0 border=0 width=\"595\"><tr><td class=\"text\">
					$a_group_descriptions[$ontab]
				</td></tr></table>
				";
			
			$sql = "SELECT ID,Name,Description,Custom,SmallIconLink,SmallShadow,LargeIconLink FROM Items WHERE GroupID='$ontab' AND SiteID='$_SESSION[site]'  ORDER BY Name";
			$r_result = dbq($sql); 
			
			$num_items = mysql_num_rows($r_result);
			$counter = 0;
			$colcounter = 0;
			
			// Go through each item and create a row or icon
			while ($a_item = mysql_fetch_assoc($r_result) ) {
				++$counter;
				
				$a_item['qtypulldown'] = make_costqty_menu( $a_item['ID'] );
												
				$sql = "SELECT ID FROM Cart WHERE ItemID='$a_item[ID]' AND SessionID='$os_sid' AND SiteID='$_SESSION[site]'";
				$r_result2 = dbq($sql);
				$numrows = mysql_num_rows($r_result2) ;
				if ($a_item['Custom'] != "N") {
					if ( $numrows > 0 ) { 
						$a_item['button'] = "Create another &raquo;"; 
					} else { 
						$a_item['button'] = "Create &raquo;"; 
					}
				} else {
					if ( $numrows > 0 ) { 
						$a_item['button'] = "Add another &raquo;"; 
					} else { 
						$a_item['button'] = "Add to cart &raquo;"; 
					}
				}
				
				
				// create list row 
				if ( strtoupper($a_site_settings['CatalogItemDisplayStyle']) == "LIST") {
					$items .= iface_dottedline() . item_row($a_item);
				
				// -or- icon row
				} else {
					
					$this_row .= item_icon($a_item);
					$colcounter++;
					if ($colcounter == 3 || $num_items == $counter) {
						$items .= iface_dottedline() .	"
						<table cellpadding=6 cellspacing=0 border=0>
							<tr> $this_row </tr>
						</table><br><br>
						";
						$this_row = "";
						$colcounter = 0;
					}
				}
			}
			
			// hidden fields
			$items .=	"
					<input type=\"hidden\" name=\"os_action\" value=\"addtocart\">
					<input type=\"hidden\" name=\"itemid\" value=\"0\">";
	
			// $script_name?os_action=addtocart&itemid=" . $a_item['ID'] . "&sid=" . $os_sid . "
			$items .= iface_dottedline();
			$mainbox = iface_make_box($items,600,100,0);
			$content = $mainbox;
	
	
			
		// STACKED VIEW
		} else {
			
			if ( is_array($a_itemGroups) ) {
				foreach ( $a_itemGroups as $k => $v ) {
					$atab[$k] = $v; 
					$tabs = iface_make_tabs($atab, $k, 'catalogtab', '600') ;
					$content .= iface_make_box("",600,100,0) . "<br><br>";
					unset($atab);
				}
			}	
			$content .= "Stacked View";
		} 
			
	} else {
		$content = "<span class=\"text\">No items are available for purchase at this time.</span>";
	}	

	
	$sPage = MakePageStructure($os_sidebar, $content, $tabs);

	


?>
