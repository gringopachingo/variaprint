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

//print_r($a_form_vars);

/**/


$search_options[] = "status_cancelled";
$search_options[] = "status_cancelledbybuyer";
$search_options[] = "status_cancelledbyapproval";
$search_options[] = "status_waiting";
$search_options[] = "status_hold";
$search_options[] = "status_ready";
$search_options[] = "status_inprod";
$search_options[] = "status_shipped";
$search_options[] = "ordernumber";
$search_options[] = "paytype_none";
$search_options[] = "cc";
$search_options[] = "pp";
$search_options[] = "po";
$search_options[] = "check";
$search_options[] = "date1_from";
$search_options[] = "date1_to";
$search_options[] = "date2_from";
$search_options[] = "date2_to";
$search_options[] = "customer_field";
$search_options[] = "customer";
$search_options[] = "BilledStatus";
$search_options[] = "max_records";
$search_options[] = "showitems";

if ($a_form_vars["init_search"] == 1) {
	foreach ($search_options as $key) {
		$_SESSION["ordersearch_".$key] = $a_form_vars[$key];
	}
}

$cfg_maxRecords = $_SESSION["ordersearch_max_records"] ;

if ($cfg_maxRecords == "") {
	$cfg_maxRecords = 10;
}

$sel_page = $_SESSION['page'];
if ( $sel_page == "" || !isset($sel_page) || is_array($sel_page)) {  $sel_page = 1;  }
$startrecord = (intval($sel_page)*$cfg_maxRecords)-$cfg_maxRecords;


$content .= "
<script language=\"javascript\">
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
	
	function do_action (obj) {
		var hash = ''
		val = obj[obj.selectedIndex].value
		is_checked = false
		with (document.forms[0]) {
			for (var i = 0; i < elements.length; i++) {
				if (elements[i].name.slice(0,9) == 'checkbox_' && elements[i].checked == true) {
					is_checked = true
					hash += elements[i].name + \"=\" + escape(elements[i].value) + \"&\"
				}
			}
		}
		
		if (!is_checked) {
			alert('You must check at least one order before clicking Go.')
		} else {		
			if (val == \"invoice\") {
				popupWin('order_download_invoice.php?' + hash,'downloadinvoices','width=350,height=100,centered=1')
			} else if (val == \"docket\") {
				popupWin('order_download_docket.php?' + hash,'downloaddocket','width=350,height=100,centered=1')
			} else {
				document.forms[0].submit()
			}
		}
	}
	
</script>
";



// Change status ***********************************************
if ( isset($a_form_vars[action_pd])) {
	
	$new_status = $a_form_vars[action_pd];
	
	$a_checkbox = array_find_key_prefix("checkbox_",$a_form_vars,true);
	if (count($a_checkbox) > 0) {
		$firstpass = true;
		foreach ($a_checkbox as $key=>$val) {
			if (!$firstpass) { 
				$where .= " OR "; 
				$where2 .= " OR "; 
			}
			$where .= "ID='$key'";
			$where2 .= "OrderID='$key'";
			$firstpass = false;
		}
		$sql = "UPDATE Orders SET Status='$new_status' WHERE $where";
		dbq($sql);
		
		
		// Update inventory amounts
		if ($new_status == 50) {
			// Get list of itemIDs, Qty pairs from OrderItems where ID = OrderID
			$sql = "SELECT ItemID,Qty FROM OrderItems WHERE $where2";
			$res = dbq($sql);
			
			// loop through list and reduce inventory amount for item where TrackInventory='true'
			while ($a = mysql_fetch_assoc($res)) {
				if ($a[Qty] == "") {
					$qty = 1;
				} else {
					$qty = number_format($a[Qty],0,".","");
				}
				$sql = "UPDATE Items SET InventoryAmount=InventoryAmount-$qty WHERE ID='$a[ItemID]' AND TrackInventory='true'";
				dbq($sql);
			}
		}
	}
	
	header("Location: vp.php?action=order_view&ms_sid=$ms_sid");
}



// get the item names in an array
$sql = "SELECT Name,ID FROM Items WHERE SiteID='$_SESSION[site]'";
$r_result = dbq($sql);
while ( $a_result = mysql_fetch_assoc($r_result)) {
	$a_items[$a_result['ID']] = $a_result['Name'];
}

function round_up($n) {
	if ($n-intval($n) > 0) {
		return intval($n)+1;
	} else {
		return $n;
	}
}

// select the order statuses
$sql = "SELECT OrderStatuses FROM Sites WHERE ID='$_SESSION[site]'";
$r_result = dbq($sql);
$a_result = mysql_fetch_assoc($r_result);
$a_xml = xml_get_tree( $a_result['OrderStatuses'] ) ;
if ( is_array($a_xml[0]['children']) ) {
	foreach ( $a_xml[0]['children'] as $status ) {
		$name = $status['attributes']['NAME'];
		$id = $status['attributes']['ID'];
		$a_statuses[$id] = $name;
		$action_pd .= "<option value=\"$id\">Change Status To &quot;$name&quot;</option>\n";
	}
}


// Set the order statuses
$wherestatus = "";
if ($_SESSION["ordersearch_status_cancelled"] == 1) {
	$wherestatus = "Status='0' ";
}
if ($_SESSION["ordersearch_status_cancelledbybuyer"] == 1) {
	if ($wherestatus != "") { $wherestatus .= " OR "; }
	$wherestatus .= "Status='10' ";
}
if ($_SESSION["ordersearch_status_cancelledbyapproval"] == 1) {
	if ($wherestatus != "") { $wherestatus .= " OR "; }
	$wherestatus .= "Status='15' ";
}
if ($_SESSION["ordersearch_status_waiting"] == 1) {
	if ($wherestatus != "") { $wherestatus .= " OR "; }
	$wherestatus .= "Status='20' ";
}
if ($_SESSION["ordersearch_status_hold"] == 1) {
	if ($wherestatus != "") { $wherestatus .= " OR "; }
	$wherestatus .= "Status='30' ";
}
if ($_SESSION["ordersearch_status_hold"] == 1) {
	if ($wherestatus != "") { $wherestatus .= " OR "; }
	$wherestatus .= "Status='30' ";
}
if ($_SESSION["ordersearch_status_ready"] == 1) {
	if ($wherestatus != "") { $wherestatus .= " OR "; }
	$wherestatus .= "Status='35' ";
}
if ($_SESSION["ordersearch_status_inprod"] == 1) {
	if ($wherestatus != "") { $wherestatus .= " OR "; }
	$wherestatus .= "Status='40' ";
}
if ($_SESSION["ordersearch_status_shipped"] == 1) {
	if ($wherestatus != "") { $wherestatus .= " OR "; }
	$wherestatus .= "Status='50' ";
}
if ($wherestatus != "") {
	$wherestatus = "AND (" . $wherestatus . ")";
} 


if (trim($_SESSION["ordersearch_ordernumber"]) != "") {
	$a_ordnums = explode(",",$_SESSION[ordersearch_ordernumber]) ;
	$whereordernumber = "";
	$fp = true;
	foreach ($a_ordnums as $ordnum) {
		if (!$fp) { $whereordernumber .= " OR ";  } $fp = false;
		$whereordernumber .= "ID='". trim($ordnum) . "'";
	}
	$whereordernumber = " AND (" . $whereordernumber . ")";
}

$wherepaytype = "";
if (trim($_SESSION["ordersearch_paytype_none"]) == 1) {
	$wherepaytype .= " PayType=''";
}
if (trim($_SESSION["ordersearch_cc"]) == 1) {
	if ($wherepaytype != "") { $wherepaytype .= " OR "; }
	$wherepaytype .= " PayType='cc'";
}
if (trim($_SESSION["ordersearch_pp"]) == 1) {
	if ($wherepaytype != "") { $wherepaytype .= " OR "; }
	$wherepaytype .= " PayType='pp'";
}
if (trim($_SESSION["ordersearch_po"]) == 1) {
	if ($wherepaytype != "") { $wherepaytype .= " OR "; }
	$wherepaytype .= " PayType='po'";
}
if (trim($_SESSION["ordersearch_check"]) == 1) {
	if ($wherepaytype != "") { $wherepaytype .= " OR "; }
	$wherepaytype .= " PayType='check'";
}
if ($wherepaytype != "") {
	$wherepaytype = "AND (" . $wherepaytype . ")";
} 

$wheredates = "";
if (trim($_SESSION["ordersearch_date1_from"]) != "") {
	$wheredates = " AND DateOrdered>'".strtotime(trim($_SESSION["ordersearch_date1_from"]))."' ";
}
if (trim($_SESSION["ordersearch_date1_to"]) != "") {
	$date = strtotime(trim($_SESSION["ordersearch_date1_to"]))+86400;
	$wheredates .= " AND DateOrdered<'".$date."' ";
}
if (trim($_SESSION["ordersearch_date2_from"]) != "") {
	$wheredates .= " AND DateApproved>'".strtotime(trim($_SESSION["ordersearch_date2_from"]))."' ";
}
if (trim($_SESSION["ordersearch_date2_to"]) != "") {
	$date = strtotime(trim($_SESSION["ordersearch_date2_to"]))+86400;
	$wheredates .= " AND DateApproved<'".$date."' ";
}

$wherecustomer = "";
if (trim($_SESSION["ordersearch_customer"]) != "") {
	if ($_SESSION["ordersearch_customer_field"] == "email") {
		$sql = "SELECT ID FROM Users WHERE Email='$_SESSION[ordersearch_customer]' AND SiteID='$_SESSION[site]'";
		$result = dbq($sql);
		$a_result = mysql_fetch_assoc($result);
		$userID = $a_result[ID];
		if ($userID != "") {
			$wherecustomer = " UserID='$userID' ";
		}
		if ($wherecustomer != "") { $wherecustomer .= " OR "; }
		$wherecustomer .= " Email='". trim($_SESSION[ordersearch_customer]) ."'";
		$wherecustomer = " AND (" .$wherecustomer. ") ";
	} else {
		$sql = "SELECT ID FROM Users WHERE Username='$_SESSION[ordersearch_customer]' AND SiteID='$_SESSION[site]'";
		$result = dbq($sql);
		$a_result = mysql_fetch_assoc($result);
		$userID = $a_result[ID];
		if ($userID != "") {
			$wherecustomer = " AND UserID='$userID' ";
		} else {
			$wherecustomer = " AND UserID='0' ";
		}
	}
}

$wherebilledstatus = "";
if ($_SESSION["ordersearch_BilledStatus"] == "1") {
	$wherebilledstatus = " AND BilledStatus='Received'";
} elseif ($_SESSION["ordersearch_BilledStatus"] == "0") {
	$wherebilledstatus = " AND BilledStatus!='Received'";
} 
if ($_SESSION["ordersearch_BilledStatus"] == "any") {
	$wherebilledstatus = "";
}

//print("billed status: ".$_SESSION["ordersearch_BilledStatus"]);


// select the orders with selected search options
$base_sql = "FROM Orders WHERE SiteID='$_SESSION[site]' $wherestatus $whereordernumber $wherepaytype $wheredates $wherecustomer $wherebilledstatus ORDER BY ID DESC ";
//print("<br><br>&quot;".$base_sql."&quot;<br><br>");
$sql = "SELECT * ". $base_sql . " LIMIT $startrecord,$cfg_maxRecords";
$r_result = dbq($sql);

if (mysql_num_rows($r_result) == 0) {
	$sql = "SELECT * ". $base_sql . " LIMIT 0,$cfg_maxRecords";
	$r_result = dbq($sql);
	
	$sel_page = $_SESSION['page'] = 1; 
}


$num_orders = mysql_num_rows($r_result);

// $sql = "SELECT * FROM Items WHERE SiteID='$_SESSION[site]' ORDER BY GroupID,Name ";
// $nResult = dbq($sql);

// Create page number selector
if (mysql_num_rows($r_result) > 0) {
	$sql = "SELECT ID ". $base_sql;
	$nTotalRecordsResult = dbq($sql);
	$totalRecords = mysql_num_rows($nTotalRecordsResult);
	if ($totalRecords > 5000) {
		$totalRecords = 5000;
	}
	$totalPages = round_up(($totalRecords/$cfg_maxRecords));
	
	$pageIndicator = "page $sel_page of $totalPages ";
	$pageCounter = 0;
	
	if ($totalPages > 1) {
		$pageIndicator .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		while($pageCounter < $totalPages) {
			++$pageCounter;
			if ($pageCounter == $sel_page) { 
			//	$pageSelector .= " $pageCounter ";
				$pageSelector .= "<option selected>$pageCounter </option>\n";
				$nextpage = $pageCounter + 1;
				$prevpage = $pageCounter - 1;
			} else {
			//	$pageSelector .= " <a href=\"$_SERVER[SCRIPT_NAME]?page=$pageCounter$options\">$pageCounter</a> ";
				$pageSelector .= "<option value=\"$pageCounter$options\">$pageCounter</option>\n ";
			}
			
			if ($sel_page != 1) $prev = "&laquo; <a href=\"$_SERVER[SCRIPT_NAME]?page=$prevpage$options\">prev</a>&nbsp;&nbsp;";
			if ($sel_page != $totalPages) $next = "&nbsp;&nbsp;<a href=\"$_SERVER[SCRIPT_NAME]?page=$nextpage$options\">next</a> &raquo;";
		}
		$pageSelector = "
		<select onChange=\"document.location='$_SERVER[SCRIPT_NAME]?page='+this.value\">
		$pageSelector
		</select>";
	}
}
$pageSelector = $pageIndicator . $prev . $pageSelector . $next;

$rowOn = 1;
$onRowColor = "#E4E6EF";
$offRowColor = "#FFFFFF";//"#F1F3FF";

$sel_menu = "browse_orders";
$submenu = "&laquo; <a href=\"vp.php?action=order_view\">Search again</a>";

$content .= "
	<img src=\"images/spacer.gif\" height=\"10\"><br>
	<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr>
          <td class=\"titlebold\" valign=\"top\">
				Search results
		  </td>
          <td class=\"text\" align=\"right\">";
		  
		  	if ($_SESSION["privilege"] == "owner" || $_SESSION["privilege_invoices"] || $_SESSION["privilege_order_status"]) {
				$content .= "With selected orders: &nbsp;
					<select name=\"action_pd\" class=\"text\" style=\"width:200\">";
				  	if ($_SESSION["privilege"] == "owner" || $_SESSION["privilege_invoices"]){
					 $content .=  "<option value=\"invoice\">Download Invoices</option>\n";
				}

				if ($_SESSION["privilege"] == "owner" || $_SESSION["privilege_order_status"]){
					  $content .= $action_pd;
				}
				$content .= "
				</select>
				<input type=\"button\" value=\"Go\" class=\"text\" onclick=\"do_action(document.forms[0].action_pd)\">";
			}
			
			$content .= "
		  </td>
        </tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
    </table>
";


if ($num_orders == 0) {
	$content .= "<br><br>
		<div class=\"text\">No orders found with the selected criteria. <a href=\"vp.php?action=order_view\">Search again</a>.</div>
	";
	
} else {
	$content .=	"
	<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		<tr>
		  <td height=\"30\" class=\"text\">&#8730;&nbsp;<a href=\"javascript:;\" onclick=\"selectall()\" onfocus=\"this.blur()\">all</a></td>
		  <td height=\"30\" align=\"right\" class=\"text\">$pageSelector</td>
		</tr>
	</table>
	";

	while ( $a = mysql_fetch_assoc($r_result) ) {
		$groupid = $a[GroupID];
		$rowOn = 1;
		$groupname = ereg_replace(" ","&nbsp;",$aItemGroup[$groupid]);
		if ( strlen($a['Name']) > 35 ) { $name = substr($a['Name'], 0, 35) . "...";  } else { $name = $a['Name']; }
		$date = date("m/d/y", $a[DateOrdered]); //D, 
		$status = $a['Status'];
		
		$rowheight = "25";
		
	//	$sql = "SELECT Username FROM Users WHERE ID='$a[UserID]'";
	//	$r_user = dbq($sql);
	//	$a_user = mysql_fetch_assoc($r_user);
	//	$username = $a_user['Username'];

		if ($_SESSION["privilege_invoices"] || $_SESSION['privilege'] == "owner"){
			$invoice_link = "
					<input type=\"button\" class=\"text\" onClick=\"popupWin('order_download_invoice.php?checkbox_$a[ID]','downloadinvoices','width=350,height=100,centered=1')\" value=\"Invoice...\">
			";
			
			if ($a['BilledStatus'] != "Received" && $a['Status'] >= 30)  {
				if ($a['PayType'] == "cc" && $a["PFPResult"] != "") {
					$bill_link = "
					<td>&nbsp;</td>
					<td class=\"text\" align=\"right\">
						<input class=\"text\" onClick=\"popupWin('finishorder.php?id=$a[ID]&type=cf&ms_sid=$ms_sid','','width=350,height=200,centered=1')\" type=\"button\" value=\"Capture Funds...\">	
					</td>
					";
				} else {
					$bill_link = "
					<td>&nbsp;</td>
					<td class=\"text\" align=\"right\">
						<input class=\"text\" onClick=\"popupWin('finishorder.php?id=$a[ID]&type=rec&ms_sid=$ms_sid','','width=350,height=200,centered=1')\" type=\"button\" value=\"Pmt Rec'd...\">
					</td>
					";
				}
			}
			
			if ($a['BilledStatus'] == "Received") {
					$bill_link = "
					<td width=\"20\">&nbsp;</td>
					<td class=\"text\" align=\"right\">
						<strong><font color=\"#009900\">&#8730; PAID</font></strong>
					</td>
					";
				
			}
			
			$billing = "
				<table cellpadding=0 cellspacing=0 border=0 >
					<td class=\"text\" align=\"right\">
						$invoice_link
					</td>
					$bill_link
				</table>
			";
		} else {
			$billing = "";
		}
		
		
		$status_desc =  $a_statuses[$status];
		if (strlen($status_desc) > 13) {
			$status_desc = substr($status_desc,0,12) . "...";
		}
		
		$email =  $a['Email'];
		if (strlen($email) > 10) {
			$email = substr($email,0,9) ;
		} 
		$email = "<a href=\"mailto:$a[Email]\" title=\"Send mail to $a[Email]\">$email</a>...";
		
		$content .= "
		<table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#E4E6EF\">
			<tr>
			  <td><img src=\"images/spacer.gif\" width=\"1\" height=\"2\"></td>
			</tr>
		</table>
		
		<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
			<tr> 
				<td class=\"text\" width=\"14\">
					<input type=\"checkbox\" name=\"checkbox_$a[ID]\" value=\"1\">
				</td>
		
				<td class=\"subhead\" width=\"90\">
					Order #<strong>$a[ID]</strong>
				</td>
							
				<td class=\"text\"  width=\"95\">
					<em>Ord'd:</em>&nbsp;<strong>$date</strong>
				</td>			
							
				<td class=\"text\"  width=\"80\">
					$status_desc
				</td>
				
				<td class=\"text\"  width=\"50\">
					$email
				</td>
							
				<td class=\"text\" align=\"right\">
					$billing
				</td>
			</tr>";
		
		$sql = "SELECT * FROM OrderItems WHERE OrderID='$a[ID]'";
		$r_result2 = dbq($sql);
		
		$c = 1;
		if ($_SESSION["ordersearch_showitems"] != "0") {
			while ( $a_order_items = mysql_fetch_assoc($r_result2) ) {
				if ($rowOn) {  $color = $onRowColor; $rowOn = 0;  } else {  $color = $offRowColor; $rowOn = 1;  }
				
				if ($a_order_items["Status"] == "10") { $msg = " &nbsp; <strong>(removed from order)</strong>"; } elseif($a_order_items["Status"] == "30" ||$a_order_items["Status"] == "29") { $msg = " &nbsp; <strong>(imposed)</strong>"; } else { $msg = ""; }
				
				$itemname = $a_items[$a_order_items['ItemID']];
				$id = $a_order_items['ID'];
				$content .= "
				<tr> 
					<td class=\"text\" colspan=\"1\" height=\"$rowheight\" >
						 &nbsp;
					</td>
					<td class=\"text\" colspan=\"4\" height=\"$rowheight\"  bgcolor=\"$color\">
						 $c)  $itemname ($a_order_items[ItemID]) $msg
					</td>
					<td class=\"text\" align=\"right\" height=\"$rowheight\"  bgcolor=\"$color\">
					";
					
					if ( $a_order_items['Imprint'] != "") {
				$content .= "
				<a href=\"javascript:;\" onFocus=\"this.blur()\" title=\"View raster proof\" onClick=\"popupWin('itempreview.php?id=".$id."&ms_sid=$ms_sid','','width=600,height=500,centered=yes')\">proof</a>... &nbsp;  
				<a href=\"javascript:;\" onFocus=\"this.blur()\" title=\"View PDF proof\" onClick=\"popupWin('../orderitem_file.php?id=".$id."&mode=presspdf&ms_sid=".$ms_sid."','','width=400,height=400,centered=yes')\">press</a>...
					";
					}
					
				$content .= "
					&nbsp;
					</td>
				</tr>			
				";
				++$c;
			}
		}
		
		
		$content .= "
		</table>
		";

		if ($_SESSION["ordersearch_showitems"] != "0") {
			$content .= "<br><br>";
		}
	}
}
?>
