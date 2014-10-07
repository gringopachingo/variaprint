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

	
	$_SESSION['tm'] = "2";
	$sel_menu = "impose";



	$content .= "
	<script language=\"javascript\">
		function impose(obj) {
			var hash = ''
			
			hash += 'imposition_id=' + document.forms[0].sel_imposition.value + '&run=' + document.forms[0].impose_qty.value + '&'
			
			is_checked = false
			with (document.forms[0]) {
				for (var i = 0; i < elements.length; i++) {
					if (elements[i].name.slice(0,9) == 'checkbox_' && elements[i].checked == true) {
						is_checked = true
						hash += elements[i].name + '=' + escape(elements[i].value) + '&'
					}
				}
			}
			
			if (!is_checked) {
				alert('You must check at least one item before clicking Go.')
			} else {
				doimpose = true;
				sel_imp_vendor = findObj('sel_imp_vendor');

				if (!sel_imp_vendor) {

				} else {
					if (sel_imp_vendor.value == 'all' ) {
						doimpose = confirm('You have selected all vendors. The resulting imposition may contain items from more than one vendor. OK to continue?');		
					}
				}
				if (doimpose) {
					popupWin('order_impose.php?mssid=$mssid&' + hash,'downloadinvoices','width=600,height=450,scrollbars=yes,resizable=yes,centered=1')
				}
			}
		}
	
		function confirmAction (linkobj, msg) {
			is_confirmed = confirm(msg)
			
			if (is_confirmed) {	
				linkobj.href += '&confirmed=1'
			} 
			return is_confirmed
		}
		c = false
		function selectall() {
			el = document.forms[0].elements
			with (document.forms[0]) {
				for (var i = 0; i < elements.length; i++) {
					if ( elements[i].name.slice(0,9) == 'checkbox_') {
						if (c) {
							elements[i].checked = false
						} else {
							elements[i].checked = true
						}
					}
				}
			}
			if (c) { c = false } else { c = true } 
		}
		
	</script>
	";
	
	
	$submenu = "
	Create Impositions 
	&nbsp; &nbsp; <a href=\"vp.php?action=order_list_imp_hist&mssid=$mssid\">Download Dockets / Impositions</a>
	&nbsp; &nbsp; <a href=\"javascript:;\" onClick=\"popupWin('itempreseteditors/item_presets_editor.php?edit=imposition','style','width=550,height=465,centered=1')\">Imposition Styles</a>...
	";
		
	// Check the current user: Master can see everything, else only show items where 
	if ($_SESSION['privilege'] == "owner") {
		
		// [All]
		if ($_SESSION['sel_imp_vendor'] == "all") { $sel = "selected"; } else {$sel = ""; }
		$supplierMenu .= "<option value=\"all\" $sel>[All]</option>\n";
		
		// [Yourself]
		if ($_SESSION['sel_imp_vendor'] == "self") { $sel = "selected"; } else {$sel = ""; }
		$supplierMenu .= "<option value=\"self\" $sel>[Yourself]</option>\n";

		// List of vendors
		$sql = "SELECT VendorManagers FROM Sites WHERE ID='$_SESSION[site]'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		$a_vendors = xml_get_tree($a_result['VendorManagers']);
		if (is_array($a_vendors[0]['children'])) {
			foreach($a_vendors[0]['children'] as $vendor) {
				if ($vendor['attributes']['ORDER_DOWNLOAD_IMPOSITIONS'] != "true") {
					$disabled = "  (disabled)";
				} else {
					$disabled = "";
				}
				$user = $vendor['attributes']['EMAIL'];
				if ($_SESSION['sel_imp_vendor'] == $user) { $sel = "selected"; } else {$sel = ""; }
				$supplierMenu .= "<option value=\"".$user."\" $sel>".$user.$disabled."</option>\n";
			}
		}
		
		$supplierMenu = "
			Show items for vendor:<br>
			<select name=\"sel_imp_vendor\" style=\"width: 130\" onChange=\"document.location='vp.php?sel_imposition=$_SESSION[sel_imposition]&sel_imp_vendor=' + this.value + '&mssid=$mssid''\">
			$supplierMenu</select>
		";
		$whereUser1 = "";
		if (!isset($_SESSION['sel_imp_vendor']) || $_SESSION['sel_imp_vendor'] == "all") {
			$whereUser1 = "";
			$whereUser2 = "";
		} else {
			$sql = "SELECT ID FROM Items WHERE VendorUsername='$_SESSION[sel_imp_vendor]'";
			$r_result = dbq($sql);
			$fp = true;
			while($a_id = mysql_fetch_assoc($r_result)) {
				if (!$fp) { $whereUser1 .= " OR "; }
				$whereUser1 .= "ItemID='$a_id[ID]' ";
				$fp = false;
			}
			
			if ($whereUser1 != "") {
				$whereUser1 = " AND ($whereUser1)";
			}
			
			$whereUser2 = "AND VendorUsername='$_SESSION[sel_imp_vendor]'";
			
		}
		
		
	} elseif ($_SESSION['privilege'] == "slave") {
		$sql = "SELECT ID FROM Items WHERE VendorUsername='$_SESSION[username]'";
		$r_result = dbq($sql);
		$fp = true;
		$whereUser1 = "";
		while($a_id = mysql_fetch_assoc($r_result)) {
			if (!$fp) { $whereUser1 .= " OR "; }
			$whereUser1 .= "ItemID='$a_id[ID]' ";
			$fp = false;
		}
		
		if ($whereUser1 != "") {
			$whereUser1 = " AND ($whereUser1)";
		}
			
		$whereUser2 = "AND VendorUsername='$_SESSION[username]'";
		$supplierMenu = "&nbsp;";
		$supplierMenu = "<hidden value=\"noval\" name=\"sel_imp_vendor\">";
	} else {
		exit("no privilege");
	}

	// select impositions for this site
	$sql = "SELECT * FROM Imposition WHERE SiteID='$_SESSION[site]' OR Template='Y'";
	$r_result = dbq($sql);
	
	
	// make pulldown of impositions
	$pd = "<select name=\"sel_imposition\" class=\"text\" onchange=\"document.location='vp.php?sel_imposition=' + this.value + '&mssid=$mssid'\" style=\"width: 180\">\n";
	$sel_imp = $_SESSION['sel_imposition'];
	while ( $a_result = mysql_fetch_assoc($r_result) ) {
		if ($sel_imp == "") { $sel_imp = $a_result[ID]; }
		if ($sel_imp == $a_result[ID]) { $sel = "selected"; } else {  $sel = "";  }
		$pd .= "<option value=\"$a_result[ID]\" $sel>$a_result[Name]</option>\n";
	}
	$pd .= "</select>\n";

	// Find items that use the selected imposition
	$sql = "SELECT ID,Name FROM Items WHERE SiteID='$_SESSION[site]' AND ImpositionID='$sel_imp' $whereUser2";
	$r_result = dbq($sql);
	$fp = true;

	while ( $a = mysql_fetch_assoc($r_result) ) {
		$a_item[$a['ID']] = $a[Name] ;
		if (!$fp) { $where .= " OR "; }
		$where .= " ItemID='$a[ID]'";
		$fp = false;
	}

	// Find the orders that are ready for production
	$sql = "SELECT ID FROM Orders WHERE Status='35'";
	$r_result = dbq($sql);
	$fp = true;
	
	while ( $a = mysql_fetch_assoc($r_result) ) {
		if (!$fp) { $where2 .= " OR "; }
		$where2 .= " OrderID='$a[ID]'";
		$fp = false;
	}
	
	
	
	
	if ( $where != "" && $where2 != "" ) { 
	
		$cfg_maxRecords = 100;
		$sel_page = $_SESSION['page'];
		
		if ( $sel_page == "" || is_array($sel_page)) {  $sel_page = 1;  }
		$startrecord = (intval($sel_page)*intval($cfg_maxRecords))-intval($cfg_maxRecords);

		$sql = "SELECT * FROM OrderItems WHERE ($where) AND ($where2) AND Status<'10' AND Imprint!='' $whereUser1 LIMIT $startrecord,$cfg_maxRecords";
		$r_result = dbq($sql);
				
		if ($r_result == 0) {
			$sql = "SELECT * FROM OrderItems WHERE ($where) AND ($where2) AND Status<'10' AND Imprint!='' $whereUser1 LIMIT 0,$cfg_maxRecords";
			$r_result = dbq($sql);
	
			$sel_page = $_SESSION['page'] = 1;
		}
			 
		$num_orderitems = mysql_num_rows($r_result) ;
		
		// Create page number selector
		if ( $num_orderitems > 0 ) {
			$sql = "SELECT ID FROM OrderItems WHERE ($where) AND ($where2)";
			$nTotalRecordsResult = dbq($sql);
			$totalRecords = mysql_num_rows($nTotalRecordsResult);
			$totalPages = 1+intval($totalRecords/$cfg_maxRecords);
			
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
					if ($sel_page != $totalPages) $next = "&nbsp;&nbsp;<a href=\"$_SERVER[SCRIPT_NAME]?page=$nextpage\">next</a> &raquo;";
				}
			}
		}
	
		$pageSelector = $pageIndicator . $prev . $pageSelector . $next;
	}
	
	$rowOn = 1;
	$onRowColor = "#E4E6EF";
	$offRowColor = "#ffffff";
	
	
	
	$content .= "
		<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
			<tr>
				<td width=\"210\">
					<span class=\"text\">Show items with imposition style: </span><br>
					$pd
				</td>
				<td class=\"text\">
				$supplierMenu
				</td>
				<td  align=\"right\" valign=\"bottom\">
					<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
						<tr>
							<td class=\"text\">Impose&nbsp;a&nbsp;print&nbsp;run&nbsp;of&nbsp;</td>
							<td><input type=\"text\" name=\"impose_qty\" value=\"500\" size=\"5\" class=\"text\" style=\"width: 35\"></td>
							<td>&nbsp;&nbsp;&nbsp;</td>
							<td><input  class=\"text\" type=\"button\" onClick=\"impose()\" value=\"Go\"></td>
						<tr>
					</table>
				</td>
			</tr>
		</table>
	";
	
if ( $num_orderitems == 0 ) {
	$content .= "<br><br>
	<div class=\"text\">There are no order items ready for production that use this imposition.</div>
	";


} else {
	
	$content .= "
	  <table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		<tr>
		  <td height=\"30\" class=\"title\"><strong>Select Items to Create Imposition</strong></td>
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
			<td width=\"2\" class=\"text\" align=\"center\">&#8730;&nbsp;<a href=\"javascript:;\" onclick=\"selectall()\" onfocus=\"this.blur()\">all</a></td>
			<td class=\"text\"><strong>Item</strong></td>
			<td class=\"text\"><strong>Ordered By</strong></td>
			<td class=\"text\"><strong>Previews</strong></td>
		<!--	<td></td> 	//-->
			<td class=\"text\"><strong>Qty</strong></td>
			<td width=\"60\" class=\"text\"><strong>Order&nbsp;#</strong></td>
		<!--	<td class=\"text\"><strong>Description</strong></td>//-->
		</tr>";

	
	while ( $a = mysql_fetch_assoc($r_result) ) {
	//	print_r($a);
			$sql = "SELECT UserID,Email FROM Orders WHERE ID='$a[OrderID]'";
			$order_result = dbq($sql);
			$a_order = mysql_fetch_assoc($order_result);
			$order_email = ""; 
			
			if (trim($a_order['Email']) != "") {
				$order_email = "<a href=\"mailto:$a_order[Email]\">$a_order[Email]</a>";
			} else {
				$sql = "SELECT Email FROM Users WHERE ID='$a_order[UserID]'";
				$user_result = dbq($sql);
				$a_user = mysql_fetch_assoc($user_result);
				if ($a_user['Email'] != "") {
					$order_email = "<a href=\"mailto:$a_user[Email]\">$a_user[Email]</a>";
				} else {
					$order_email = "-";
				}
			}
						
	//	if ($a["Status"] < 10) {
			if ($rowOn) { $color = $onRowColor; $rowOn = 0; } else {  $color = $offRowColor; $rowOn = 1; }
			$groupid = $a[GroupID];
			$groupname = ereg_replace(" ","&nbsp;",$aItemGroup[$groupid]);
			if ( strlen($a['Name']) > 30 ) { $name = substr($a['Name'], 0, 28) . "...";  } else { $name = $a['Name']; }
			$rowheight = "25";
			$content .= "
			<tr bgcolor=\"$color\">
				<td class=\"text\" width=\"2\" height=\"$rowheight\">
					<input type=\"checkbox\" name=\"checkbox_$a[ID]\" value=\"yes\">
				</td>
				<td class=\"text\" height=\"$rowheight\">" . $a_item[ $a['ItemID'] ] . "</td>
				<td class=\"text\" height=\"$rowheight\">" . $order_email . "</td>
				<td width=\"120\" class=\"text\">
				<a href=\"javascript:;\" onFocus=\"this.blur()\" title=\"View raster proof\" onClick=\"popupWin('itempreview.php?id=$a[ID]&mssid=$mssid','','width=600,height=500,centered=yes')\">proof</a>... &nbsp;  
				<a href=\"javascript:;\" onFocus=\"this.blur()\" title=\"View PDF proof\" onClick=\"alert('Note: Do not edit PDF press files in Illustrator. This may lead to unexpected results.'); popupWin('../orderitem_file.php?id=".$a[ID]."&mode=presspdf&mssid=".$mssid."','','width=400,height=400,centered=yes')\">press</a>...
				</td>
			<!--	
				<td class=\"text\" width=\"20\" nowrap align=\"right\" height=\"$rowheight\">
					<a href=\"javascript:;\">docket</a>...
				</td> //-->
				<td class=\"text\" width=\"10\" >" . $a['Qty'] . "</td>
				
				<td class=\"text\" width=\"20\" height=\"$rowheight\" nowrap>
					<a href=\"vp.php?mssid=$mssid&action=order_search_results&init_search=1&s&ordernumber=$a[OrderID]\">".$a[OrderID]."</a>
				</td>
				<!--
				<td class=\"text\" height=\"$rowheight\">
					 Description 
				</td>//-->
			</tr>";
	}
			
	$content .= "</table>
	";
}

?>
