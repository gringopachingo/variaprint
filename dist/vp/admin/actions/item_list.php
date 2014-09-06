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


$_SESSION['tm'] = "1";

if ( $a_form_vars[delete_item] != ""  && $a_form_vars[confirmed] == "1") {
	$sql = "DELETE FROM Items WHERE ID='$a_form_vars[delete_item]'";
	dbq($sql);
	header("Location: vp.php?action=item_list&user_id=$user_id&ms_sid=$ms_sid");
}



$sql = "SELECT ItemGroups FROM Sites WHERE ID='$_SESSION[site]'";
$nResult = dbq($sql);
$aIG = mysql_fetch_assoc($nResult);
$aIGTree = xml_get_tree($aIG['ItemGroups']);
if ( is_array($aIGTree[0]['children']) ) {
	while ( list(,$node) = each($aIGTree[0]['children']) ) {
		if ($node['tag'] == "ITEMGROUP") {
			$name = $node['attributes']['NAME']; $id = $node['attributes']['ID']; 
			if ( $node['attributes']['HIDDEN'] == "Y") { $name .= " [hidden]"; } 
			$aItemGroup[$id] = $name;
		}
	}
}

$content .= "
<script language=\"javascript\">
	function confirmAction (linkobj, msg) {
		is_confirmed = confirm(msg)
		
		if (is_confirmed) {	
			linkobj.href += '&confirmed=1'
		} 
		return is_confirmed
	}
	function editItem(itemid,obj) {
		var action = obj.value;
		popupWin('itemeditors/item_editor.php?edit='+action+'&item_id='+itemid,'edit','scrollbars=0,width=770,height=560,resizable=0,centered=1');
		obj.selectedIndex = 0;
	}
	
</script>
";

$cfg_maxRecords = 20;
$sel_page = $_SESSION['page'];

if ( $sel_page == "" || is_array($sel_page)) {  $sel_page = 1;  }
$startrecord = ($sel_page*$cfg_maxRecords)-$cfg_maxRecords;
// print("sel_page = " . $sel_page);

//$options = "&action=item_list&user_id=$_SESSION[user_id]";
$sql = "SELECT * FROM Items WHERE SiteID='$_SESSION[site]' ORDER BY GroupID,Name LIMIT $startrecord,$cfg_maxRecords";
// print("SQL:" . $sql);
$r_result = dbq($sql);

if (mysql_num_rows($r_result) == 0) {
	$sql = "SELECT * FROM Items WHERE SiteID='$_SESSION[site]' ORDER BY GroupID,Name LIMIT 0,$cfg_maxRecords";
	// print("SQL:" . $sql);
	$r_result = dbq($sql);
	$sel_page = $_SESSION['page'] = 1; 
}

// Create page number selector
if (mysql_num_rows($r_result) > 0) {
	$have_items = true;
	$sql = "SELECT ID FROM Items WHERE SiteID='$_SESSION[site]'";
	$nTotalRecordsResult = dbq($sql);
	$totalRecords = mysql_num_rows($nTotalRecordsResult);
	$totalPages = round(($totalRecords/$cfg_maxRecords) + .4999999, 0);
	
	$pageIndicator = "page $sel_page of $totalPages ";
	$pageCounter = 0;
	
	if ($totalPages > 1) {
		$pageIndicator .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		while($pageCounter < $totalPages) {
			++$pageCounter;
			if ($pageCounter == $sel_page) { 
				$pageSelector .= " $pageCounter ";
				$nextpage = $pageCounter + 1;
				$prevpage = $pageCounter - 1;
			} else {
				$pageSelector .= " <a href=\"$_SERVER[SCRIPT_NAME]?page=$pageCounter$options\">$pageCounter</a> ";
			}
			
			if ($sel_page != 1) $prev = "&laquo; <a href=\"$_SERVER[SCRIPT_NAME]?page=$prevpage$options\">prev</a>&nbsp;&nbsp;";
			if ($sel_page != $totalPages) $next = "&nbsp;&nbsp;<a href=\"$_SERVER[SCRIPT_NAME]?page=$nextpage$options\">next</a> &raquo;";
		}
	}
} else {
	$have_items = false;
	$content = "<span class=\"text\">No items set up. 
	<a href=\"javascript:;\" onClick=\"popupWin('item_new.php','','width=450,height=250,centered=1')\">Add a new item</a>...</span>";
}

$pageSelector = $pageIndicator . $prev . $pageSelector . $next;

$rowOn = 1;
$onRowColor = "#E4E6EF";
$offRowColor = "#ffffff";

$sel_menu = "items";
$submenu = "<a href=\"javascript:;\" onClick=\"popupWin('item_new.php','','width=450,height=250,centered=1')\">New item</a>...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href=\"javascript:;\" onclick=\"popupWin('itemeditors/item_edit_group_list.php?mode=new','edititemgrouplist','centered=1,width=550,height=300,scrollbars=yes')\">
		Edit item groups</a>...&nbsp;&nbsp;
		&nbsp; &nbsp; &nbsp;
		Styles:&nbsp;&nbsp;
		<a href=\"javascript:;\" onClick=\"popupWin('itempreseteditors/item_presets_editor.php?edit=pricing','style','width=550,height=465,centered=1')\">Pricing</a>...&nbsp;
		<a href=\"javascript:;\" onClick=\"popupWin('itempreseteditors/item_presets_editor.php?edit=imposition','style','width=550,height=465,centered=1')\">Imposition</a>...&nbsp;
		<a href=\"javascript:;\" onClick=\"popupWin('itempreseteditors/item_presets_editor.php?edit=shipping','style','width=550,height=465,centered=1')\">Shipping</a>...&nbsp; 
		";

//		<a href=\"javascript:;\" onClick=\"popupWin('itempreseteditors/item_presets_editor.php?edit=supplier','style','width=550,height=465,centered=1')\"><img  border=\"0\" src=\"images/icon-supplier.gif\" width=\"17\" height=\"17\" align=\"absmiddle\"></a>&nbsp;

if ($have_items) {
	$content .= "
	<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr>
          <td height=\"30\" class=\"title\"><strong>Edit Items</strong></td>
          <td height=\"30\" align=\"right\" class=\"text\">$pageSelector</td>
        </tr>
      </table>
      <table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#E4E6EF\">
        <tr>
          <td><img src=\"images/spacer.gif\" width=\"1\" height=\"2\"></td>
        </tr>
      </table>


	<table cellpadding=5 cellspacing=0 border=0 width=600>
		<tr bgcolor=\"$color\">
			<td class=\"text\"><strong>Name</strong></td>
			<td align=\"right\" class=\"text\">&nbsp;</td>
			<td width=\"20\" class=\"text\"><strong>Inventory</strong> 
			<td width=\"20\" class=\"text\"><strong>Group</strong> 
			</td>
			<td width=\"2\" class=\"text\"></td>
		</tr>";

	while ( $a = mysql_fetch_assoc($r_result) ) {
		if ($rowOn) { $color = $onRowColor; $rowOn = 0; } else {  $color = $offRowColor; $rowOn = 1; }
		$groupid = $a[GroupID];
		if (strlen($aItemGroup[$groupid]) > 15) { $groupname = ereg_replace(" ","&nbsp;",substr($aItemGroup[$groupid],0,13)) . "..."; } else { $groupname = ereg_replace(" ","&nbsp;",$aItemGroup[$groupid]); }
		if ( strlen($a['Name']) > 35 ) { $name = substr($a['Name'], 0, 33) . "...";  } else { $name = $a['Name']; }
		if ($a["Custom"] == "N") {$custom=" <font color=\"$999999\">(non-custom)</font>";} else {$custom="";}
		if ($a["TrackInventory"] == "true") { 
			$inventory = $a['InventoryAmount']." &nbsp; <a href=\"javascript:;\" onClick=\"popupWin('item_inventory.php?item_id=$a[ID]','inventory$a[ID]','width=500,height=250,centered=1')\">edit</a>...";
			
		} else {
			$inventory = "<a href=\"javascript:;\" onClick=\"popupWin('item_inventory.php?item_id=$a[ID]','inventory$a[ID]','width=500,height=250,centered=1')\">enable</a>...";
		}
		$content .= "
		<tr bgcolor=\"$color\">
			<td class=\"text\" width=\"280\">$name $custom</td>
			<td class=\"text\"  nowrap>

				<select name=\"sel_$a[ID]\" class=\"text\" onChange=\"editItem($a[ID],this)\">
					<option value=\"\">Edit:</option>
";
					
		if ($a["Custom"] != "N") { 
			$content .= "
					<option value=\"template\">Template...</option>
			";
		}
/*					<option value=\"name\">Name, description &amp; images...</option>
					<option value=\"group\">Item group...</option>
					<option value=\"input\">Input fields &amp; groups...</option>
					<option value=\"prefill\">Prefilled field information...</option>
					<option value=\"pricing\">Pricing...</option>
					<option value=\"imposition\">Imposition style...</option>
					<option value=\"supplier\">Supplier...</option>
					<option value=\"shipping\">Shipping weight...</option>*/
			$content .= "
					<option value=\"properties\">Properties...</option>
				</select>
			</td>
			<td class=\"text\" nowrap >
			$inventory
				
			</td>
			<td class=\"text\" nowrap  width=\"100\">

				$groupname&nbsp;&nbsp;&nbsp;
			</td>
			<td class=\"text\" width=\"2\">
				<a href=\"vp.php?action=item_list&user_id=$user_id&delete_item=$a[ID]\" onclick=\"return confirmAction(this, 'WARNING: Deleting an item cannot be undone. This will permanently delete all the information you entered for this item.')\">
				<img border=\"0\" src=\"images/icon-delete.gif\" border=\"0\">
				</a>
			</td>
		</tr>";
	}
	$content .= "</table><br>
	<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr>
          <td height=\"30\">&nbsp;</td>
          <td height=\"30\" align=\"right\" class=\"text\">$pageSelector</td>
        </tr>
      </table>
	";
}
?>
