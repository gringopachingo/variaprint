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

$a_apprv = array_find_key_prefix("checkbox_", $a_form_vars, 1);

if ( is_array($a_apprv) && count($a_apprv) ) {
	$fp = true;
	foreach($a_apprv as $acct_id=>$null) {
		if (!$fp) { $where .= " OR "; }
		$fp = false;
		$where .= " ID='$acct_id' ";
	}
	$sql = "UPDATE PO SET Status='approved' WHERE $where";
	dbq($sql);
}

$tm = 4;

$sel_menu = "po_approve";

$sql = "SELECT * FROM PO WHERE Status='notapproved' AND SiteID='$_SESSION[site]'";
$r_result = dbq($sql);

$rowOn = 1;
$onRowColor = "#E4E6EF";
$offRowColor = "#ffffff";

if ( mysql_num_rows($r_result) == 0 ) {
	$content .= "<span class=text>No pending PO account requests.</span>";
	
} else {
//	$content = "<b class=title>Approve PO Accounts</b><br><br>";
	$content .= "
	  <table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		<tr>
		  <td height=\"20\" class=\"title\" valign=\"top\"><strong>Approve PO Accounts</strong></td>
		  <td height=\"20\" align=\"right\" class=\"text\" valign=\"top\"><input type=\"submit\" value=\"Approve Selected Accounts\"></td>
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
			<td class=\"text\" width=\"60\"><strong>Username</strong></td>
			<td class=\"text\" width=\"60\"><strong>Company</strong></td>
			<td class=\"text\" width=\"80\"><strong>Name</strong></td>
			<td class=\"text\"><strong>Email</strong></td>
			<td class=\"text\"><strong>Phone</strong></td>
			<td class=\"text\"><strong>Address</strong></td>
		</tr>";

	
	while ( $a = mysql_fetch_assoc($r_result) ) {
		if ($rowOn) { $color = $onRowColor; $rowOn = 0; } else {  $color = $offRowColor; $rowOn = 1; }
	//	$groupid = $a[GroupID];
	//	$groupname = ereg_replace(" ","&nbsp;",$aItemGroup[$groupid]);
	//	if ( strlen($a['Name']) > 30 ) { $name = substr($a['Name'], 0, 28) . "...";  } else { $name = $a['Name']; }
		$rowheight = "25";
		
		$a_poxml = xml_get_tree($a['Billing']);
		
		if (is_array($a_poxml[0]['children'])) {
			foreach($a_poxml[0]['children'] as $val) {
				$a_po[$val['attributes']['ID']] = $val['value'];
			}
		}
		
		$sql = "SELECT Username FROM Users WHERE ID='$a_po[user_id]' AND SiteID='$_SESSION[site]'";
		$res = dbq($sql);
		$a_user = mysql_fetch_assoc($res);
		$username = $a_user['Username'];
		
		$company = $a_po['BillCompany'];
		$name = $a_po['BillName'];
		$address = $a_po['BillStreet'] . "<br>";
		if (trim($a_po['BillStreet2']) != "") { $address .= $a_po['BillStreet2'] . "<br>"; }
		$address .= $a_po['BillCity'] . ", " . $a_po['BillState'] . " " . $a_po['BillZip'];
		$email = "<a href=\"mailto:$a_po[Email]\" title=\"send email to $a_po[Email]\">".substr($a_po[Email], 0, 10) ."</a>...";
		$phone = $a_po['Phone'];
		
		
	//	print_r($a_po);
		
		$content .= "
		<tr bgcolor=\"$color\">
			<td class=\"text\" width=\"2\" height=\"$rowheight\">
				<input type=\"checkbox\" name=\"checkbox_$a[ID]\" value=\"yes\">
			</td>
			<td class=\"text\" height=\"$rowheight\">$username</td>
			<td class=\"text\" height=\"$rowheight\">$company</td>
			<td class=\"text\" height=\"$rowheight\">$name</td>
			<td class=\"text\" height=\"$rowheight\">$email</td>
			<td class=\"text\" height=\"$rowheight\">$phone</td>
			<td class=\"text\" height=\"$rowheight\">$address</td>
		</tr>";
	}
	$content .= "</table>";
//	while ( $a_po = mysql_fetch_assoc($r_result) ) {
//		$content .= "<span class=text>-</span><br>";		
//	}
}

?>