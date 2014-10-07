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


SecureServerOn(true);


$form_method = "POST";

$js = "
var iIndex = 0;
var pulldownOptions = new Array();
var shippingCosts = new Array();
var itemCost = new Array();


	$sJSApprovalArray
	$sJSShippingArray
	$sJSItemCost


function getSubgroup(topGroupPldwn, subGroupPldwn, sDef) {
	topGroupPldwn = findObj(topGroupPldwn);
	subGroupPldwn = findObj(subGroupPldwn);
	if (topGroupPldwn != null && subGroupPldwn != null) {
		var mainGroupValue = topGroupPldwn.options[topGroupPldwn.selectedIndex].value ;
		clearBox(subGroupPldwn) ;
		return FillPldwn(pulldownOptions, mainGroupValue, subGroupPldwn, sDef) ;
	} else {
		alert('There was an error.');
	}
}

function clearBox(box) {
	for(i=box.options.length; i>0; i--) {
		box.options[0] = null;
	}
}

function FillPldwn(aOptions, topPldwn, subPldwn, sDef) {
	var maingroupValue
	var subgroupValue
	var subgroupDescription
	var theOptions
	var indexPos1
	var indexPos2
	var selectedIndex = 0
	var Counter = 0

	for(i=0;i<" . "aOptions.length;i++) {
		var stringToCompare = new String(aOptions[i])
		
		indexPos1 = stringToCompare.indexOf(\"|\")
		maingroupValue = stringToCompare.substring(0, indexPos1)
		
		if(maingroupValue == topPldwn) {
			Counter++;
			indexPos2 = stringToCompare.indexOf(\"^\");
			
			subgroupValue = stringToCompare.substring(indexPos1+1,indexPos2);
			subgroupDescription = stringToCompare.substring(indexPos2+1, stringToCompare.length);
			if (subgroupDescription.indexOf('&euro;') > 0) { subgroupDescription = subgroupDescription.replace('&euro;','') + ' Euros'; };
			if (subgroupDescription.indexOf('&yen;') > 0) { subgroupDescription = subgroupDescription.replace('&yen;','') + ' Yen'; }
			if (subgroupDescription.indexOf('&pound;') > 0) { subgroupDescription = subgroupDescription.replace('&pound;','') + ' Pounds'; };
			if(subgroupValue == sDef) {
				selectedIndex = Counter;
				makeOption(subPldwn, subgroupDescription, subgroupValue, true);
			} else {
				makeOption(subPldwn, subgroupDescription, subgroupValue, false);
			}
		}
	}
	
	if(subPldwn.options.length>=1) {
		subPldwn.selectedIndex = selectedIndex;
	} else {
		makeOption(subPldwn, \"No shipping methods available\", \"\", false);
	}
	return selectedIndex;
}

function makeOption(box, text, value, def) {
	var opt = new Option(text, value, def, def)
	var l = box.options.length
	box.options[l] = opt
	return;
}
function use_same_address() {
	f = document.forms[0];
	f.shipping_address1.value = f.billing_address1.value;
	f.shipping_address2.value = f.billing_address2.value;
	f.shipping_city.value = f.billing_city.value;
	f.shipping_state.value = f.billing_state.value;
	f.shipping_zip.value = obj.billing_zip.value;
}
function setAddress(addr_id) {
	f = document.forms[0];
	f.shipping_company.value = addr[addr_id]['company']
	f.shipping_name.value = addr[addr_id]['contact'] 
	f.shipping_address1.value = addr[addr_id]['address1'] 
	f.shipping_address2.value = addr[addr_id]['address2']
	f.shipping_city.value = addr[addr_id]['city'] 
	f.shipping_state.value = addr[addr_id]['state'] 
	f.shipping_zip.value = addr[addr_id]['zip'] 
}
";

	$title = $a_site_settings['CheckoutTitle']; // "Checkout";
	$description = $a_site_settings['CheckoutText']; // "This is the checkout page.";
	$os_sidebar = iface_make_sidebar($title, $description);
	$line = iface_dottedline();
	
	// SET UP PREFILL	
	$sql = "SELECT OrderInfo FROM Sessions WHERE SessionID='$ossid'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	
	$a_tree = xml_get_tree(decrypt($a_result['OrderInfo'],""));
	
	if ( is_array($a_tree[0]['children']) ) {
		foreach($a_tree[0]['children'] as $node) {
			$a_order_prefill[$node['attributes']['ID']] = $node['value'];
		}
	} else {
		$sql = "SELECT * FROM Users WHERE ID='$_SESSION[user_id]' AND SiteID='$_SESSION[site]'";
		$r_result = dbq($sql);
		$a_user = mysql_fetch_assoc($r_result);
		
		$a_tree = xml_get_tree(decrypt($a_user['OrderInfo'],""));
		
		if ( is_array($a_tree[0]['children']) ) {
			foreach($a_tree[0]['children'] as $node) {
				$a_order_prefill[$node['attributes']['ID']] = $node['value'];
			}
		} else {
			// use the information straight from the account
			$a_order_prefill['email'] = $a_user['Email'];
			$a_order_prefill['billing_country'] = $a_user['Country'];
			$a_order_prefill['billing_state'] = $a_user['State'];
			$a_order_prefill['billing_city'] = $a_user['City'];
			$a_order_prefill['billing_zip'] = $a_user['Zip'];
			$a_order_prefill['billing_address1'] = $a_user['Address1'];
			$a_order_prefill['billing_address2'] = $a_user['Address2'];
			$a_order_prefill['billing_card_holder'] = $a_user['FirstName'] . " " . $a_user['LastName'];

			$a_order_prefill['shipping_address1'] = $a_user['Address1'];
			$a_order_prefill['shipping_address2'] = $a_user['Address2'];
			$a_order_prefill['shipping_state'] = $a_user['State'];
			$a_order_prefill['shipping_city'] = $a_user['City'];
			$a_order_prefill['shipping_zip'] = $a_user['Zip'];
		}		
	}

	if (trim($a_order_prefill['email']) == "" || !isset($a_order_prefill['email'])) {
		if (!is_array($a_user)) {
			$sql = "SELECT * FROM Users WHERE ID='$_SESSION[user_id]' AND SiteID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$a_user = mysql_fetch_assoc($r_result);
		}
		$a_order_prefill['email'] = $a_user['Email'];
	}
	
	$checkout_btn = "<input class=\"button\" type=\"submit\" value=\"Continue &raquo;\">";
	
	$payment_pulldown = iface_payment_pulldown($a_site_settings, 
		"onchange=\"document.location = '$script_name?billing_type=' + this.value + '&site=$_SESSION[site]&ossid=$_SESSION[ossid]')\"", 
		$_SESSION['billing_type']);
	
	// Payment type
		switch ($_SESSION['billing_type']) {
			case "pp" :
				$billing_content = $a_site_settings['CheckoutPayPalNote'];
				
				break;
			
			case "cc" :
				// print_r($a_order);
				
				$billing_card_type = iface_billing_card_type_pulldown($a_order_prefill['billing_card_type']);

				$mo[1] = "Jan"; $mo[2] = "Feb"; $mo[3] = "Mar"; $mo[4] = "Apr"; $mo[5] = "May"; $mo[6] = "Jun"; 
				$mo[7] = "Jul"; $mo[8] = "Aug"; $mo[9] = "Sep"; $mo[10] = "Oct"; $mo[11] = "Nov"; $mo[12] = "Dec"; 
				
				for ($i = 1; $i <= 12; ++$i) {
					if ( $a_order_prefill['billing_card_exp_month'] == $i) { $sel = "selected"; } else { $sel = ""; }
					$month_options .= "<option value=\"$i\" $sel>$i - $mo[$i]</option>\n";
				}
				
				$month_pd = "
				<select name=\"billing_card_exp_month\" class=\"text\">
					<option value=\"\">month</option>
					$month_options
				</select>
				";
				
				for ($i=date(Y,time()); $i<=date(Y,time())+8; ++$i) {
					if ( $a_order_prefill['billing_card_exp_year'] == $i) { $sel = "selected"; } else { $sel = ""; }
					$year_options .= "<option value=\"$i\" $sel>$i</option>\n";
				}
				
				$year_pd = "
				<select name=\"billing_card_exp_year\" class=\"text\">
					<option value=\"\">year</option>
					$year_options
				</select>
				";
				
				
				$billing_content = "
					<table cellpadding=0 cellspacing=0 border=0>
						<tr>
							<td class=\"text\">Cardholder's Name<br><input value=\"$a_order_prefill[billing_card_holder]\" type=\"text\" name=\"billing_card_holder\" style=\"width:280\"></td>
							<td> <img src=\"images/spacer.gif\" width=\"1\" height=\"35\"> </td>
							<td class=\"text\">Address (where statement is received)<br><input value=\"$a_order_prefill[billing_address1]\" type=\"text\" name=\"billing_address1\" style=\"width:280\"></td>
						</tr>
						<tr>
							<td class=\"text\">Card Type<br>$billing_card_type</td>
							<td> <img src=\"images/spacer.gif\" width=\"1\" height=\"35\"> </td>
							<td class=\"text\">Address 2<br><input value=\"$a_order_prefill[billing_address2]\" type=\"text\" name=\"billing_address2\" style=\"width:280\"></td>
						</tr>
						<tr>
							<td class=\"text\">Card Number<br><input type=\"text\" name=\"billing_card_number\" style=\"width:280\"></td>
							<td> <img src=\"images/spacer.gif\" width=\"1\" height=\"35\">  </td>
							<td>
								<table cellpadding=0 cellspacing=0 border=0>
									<tr>
										<td class=\"text\">City<br><input value=\"$a_order_prefill[billing_city]\" type=\"text\" name=\"billing_city\" style=\"width:140\"></td>
										<td class=\"text\">State<br><input value=\"$a_order_prefill[billing_state]\" type=\"text\" name=\"billing_state\" style=\"width:50\"></td>
										<td class=\"text\">Postal Code<br><input value=\"$a_order_prefill[billing_zip]\" type=\"text\" name=\"billing_zip\" style=\"width:80\"></td>
									</tr>
								</table
							</td>
						</tr>
						<tr>
							<td>
								<table cellpadding=0 cellspacing=0 border=0 width=\"100%\" class=\"text\"><tr><td>
										Card's Expiration Date<br>$month_pd &nbsp; $year_pd
									</td>
									<td class=\"text\">
										Security Code [<a href=\"javascript:popupWin('help-csc.html','','width=280,height=200,centered=1,resizable=0,scrollbars=0');\">?</a>]<br>
										<input type=\"text\" name=\"billing_csc\" style=\"width:55\" MAXLENGTH=\"4\">
									</td>
									</tr>
								</table>
							</td>
							<td> <img src=\"images/spacer.gif\" width=\"10\" height=\"35\"></td>
							<td class=\"text\">Country<br><input value=\"$a_order_prefill[billing_country]\" type=\"text\" name=\"billing_country\" style=\"width:280\"></td>
						</tr>
					</table>
				";
				break;
				//<input value=\"$a_order_prefill[billing_card_exp]\" type=\"text\" name=\"billing_card_exp\" style=\"width:280\">
				
			case "check" :
				$billing_content = str_replace("\n","<br>",htmlentities($a_site_settings['CheckoutCheckNote']));
				break;
				
				
			case "po" :
			
				// Check to see if there's a PO account set up for this joker
				$sql = "SELECT POID FROM Users WHERE ID='$_SESSION[user_id]' AND SiteID='$_SESSION[site]'";
				$r_result = dbq($sql);
				$a_result = mysql_fetch_array($r_result);
				
				$poid = $a_result[POID];
				
				$sql = "SELECT * FROM PO WHERE ID='$poid'";
				$r_result = dbq($sql);
				
				if (mysql_num_rows($r_result) > 0) {
					// If yes, retrieve account info
					// XML fields: BillCompany, BillName, BillCity, BillStreet, BillStreet2, BillState, BillZip, Phone, Email
					$a_result = mysql_fetch_array($r_result);
					
					$a_tree = xml_get_tree($a_result['Billing']);
					
					if ( is_array($a_tree[0]['children']) ) {
						foreach($a_tree[0]['children'] as $node) {
							$id = $node['attributes']['ID'];
							$details[$id] = $node['value'] ;
						}
					}
					
					
					if ( $a_result['Status'] == "notapproved" ) { $status = "Credit Pending" ; } else {$status = "Credit Approved" ; } 
					
					if ( $a_site_settings[AllowPurchaseOnPending] == "checked" || $a_result['Status'] == "approved") {
						$checkout_btn = "<input type=submit value=\"Continue &raquo;\">";
					} else {
						$checkout_btn = "<input type=button value=\"Continue &raquo;\" onclick=\"alert('You cannot place an order with this PO account until credit has been approved.')\">";
					}
					
					$account = "
						<b>Account details</b><br>
						Status: $status<br>
						$details[BillCompany]<br>
						$details[BillName]<br>
						$details[BillStreet]<br>
						$details[BillStreet2]<br>
						$details[BillCity], $details[BillState] $details[BillZip]<br>
						$details[BillPhone]<br>
						$details[BillEmail]<br>
						
						<input type=\"hidden\" name=\"billing_company\" value=\"$details[BillCompany]\">
						<input type=\"hidden\" name=\"billing_name\" value=\"$details[BillName]\">
						<input type=\"hidden\" name=\"billing_street\" value=\"$details[BillStreet]\">
						<input type=\"hidden\" name=\"billing_street2\" value=\"$details[BillStreet2]\">
						<input type=\"hidden\" name=\"billing_city\" value=\"$details[BillCity]\">
						<input type=\"hidden\" name=\"billing_state\" value=\"$details[BillState]\">
						<input type=\"hidden\" name=\"billing_zip\" value=\"$details[BillZip]\">
						<input type=\"hidden\" name=\"billing_phone\" value=\"$details[BillPhone]\">
						<input type=\"hidden\" name=\"billing_email\" value=\"$details[BillEmail]\">
						<input type=\"hidden\" name=\"billing_poid\" value=\"$poid\">
					";
					
					
				} else {
					// If not, show create an account info
					$account = "You have not setup a PO account yet. <a href=\"javascript:;\" onClick=\"popupWin('applyforpo.php?site=$_SESSION[site]&user_id=$_SESSION[user_id]&ossid=$_SESSION[ossid]','po','width=500,height=400,centered=1')\">Click here</a> to apply for one.";
				
					$checkout_btn = "<input type=button value=\"Continue &raquo;\" onclick=\"alert('You must apply for a PO account or choose a different billing type before you can checkout.')\">";
			
				}			
				
				// At all times show a link to change your POID 
				
				
				
				
				
				$billing_content = "
					$account
					
					<!--PO - <br>
					Sign up for PO account with [company name] or enter another PO account number<br>
					
					- or -<br>
					
					if PO account is already set up, just use that one and give the option to enter another 
					PO account number with checkbox to set new acct number as default.//-->
				";
				break;
								
			case "" :
				$billing_content = "Select a payment type above.";
		}
	
	if ( $a_site_settings[SpecialInstructionsNote] != "" ) {
		$instruction_note = $a_site_settings[SpecialInstructionsNote] . "<br>";
	}
	
	
	// SHIPPING
	$sql = "SELECT ShippingID FROM Sites WHERE ID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);

	$sql = "SELECT * FROM Shipping WHERE ID='$a_result[ShippingID]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	
	$a_shipping_tree = xml_get_tree($a_result['Definition']);
	$order_handling_cost = $a_shipping_tree[0]['attributes']['HANDLINGCOST'];
	
	if ($a_site_settings["IncludeAddressList"] == "checked") { 
		$sql = "SELECT ShippingAddresses FROM Sites WHERE ID='$_SESSION[site]'";
		$res = dbq($sql);
		$a_shipaddr = mysql_fetch_assoc($res);
		$shipaddr = $a_shipaddr['ShippingAddresses'];
		$a_shipaddr = xml_get_tree($shipaddr);
		
		$allowcustom = $a_shipaddr[0]['attributes']['ALLOWCUSTOM'];
		if ($allowcustom=="false") {
			$addr_hobble = " onFocus='this.blur()'";
		} 
		
		$js .= "
addr = new Array();
		";
		if (is_array($a_shipaddr[0]['children'])) {
			foreach($a_shipaddr[0]['children'] as $addrnode) {
				if ($addrnode['attributes']['HIDDEN'] != "true") {
					$id = $addrnode['attributes']['ID'];
					$sel_addr .= "<option value=\"".$id."\">".$addrnode['attributes']['NAME']."</option>\n";
					$js .= "
addr[$id] = new Array();
addr[$id]['company'] = '".addslashes($addrnode['attributes']['COMPANY'])."';
addr[$id]['contact'] = '".addslashes($addrnode['attributes']['CONTACT'])."';
addr[$id]['address1'] = '".addslashes($addrnode['attributes']['ADDRESS1'])."';
addr[$id]['address2'] = '".addslashes($addrnode['attributes']['ADDRESS2'])."';
addr[$id]['city'] = '".addslashes($addrnode['attributes']['CITY'])."';
addr[$id]['state'] = '".addslashes($addrnode['attributes']['STATE'])."';
addr[$id]['zip'] = '".addslashes($addrnode['attributes']['ZIP'])."';
					";
				}
			}
		}
	}
	$sel_addr = "<select onChange=\"setAddress(this.value)\">
	<option value=\"\">Choose address...</option>
	$sel_addr
</select>
";


	// create region pulldown
	$next_region_id = 1;
	$have_region = false;
	$region_pd = "<select class=\"text\" name=\"shipping_region\" onChange=\"iIndex=getSubgroup('shipping_region', 'shipping_method', '')\">\n"; 
	
	$order_weight = $_SESSION['order_weight'];
	$base_handling_cost = $a_shipping_tree[0]['attributes']['HANDLINGCOST'];
	
				
	if ( is_array( $a_shipping_tree[0]['children'] ) ) {
		$cntr = 0; $othershipping = false;
		foreach ($a_shipping_tree[0]['children'] as $a_region) {
			$a_region_attr = $a_region['attributes'] ;
			if ( $region_id == "") { $region_id = $a_region_attr['ID']; }
			if ( $region_id == $a_region_attr['ID']) {  $sel = "selected";   } else {  $sel = "";  } 
			
			$a_method_node = $a_region['children'];
			
			if ($a_region_attr[NAME] != "" || $a_region_attr[ID] == "other") {
				if ($a_order_prefill['shipping_region'] == $a_region_attr[ID]) { $sel = "selected"; } else { $sel = ""; }
				if ($a_region_attr[ID] == "other") { $a_region_attr[NAME] = "[Other]"; $othershipping = true; }
				$region_pd .= "<option value=\"$a_region_attr[ID]\" $sel>$a_region_attr[NAME]</option>\n"; 
				
				
				if ( is_array($a_method_node) ) {	
					foreach ($a_method_node as $a_this_method_node) {
						$method_handling_cost = $a_this_method_node['attributes']['HANDLINGCOST'];
						$id = $a_this_method_node['attributes']['ID']; $name = $a_this_method_node['attributes']['NAME'];
						
						// Find cost based on total weight of order
						$a_weights_node = xml_find_node("WEIGHTS",$a_this_method_node['children']);
						if ( is_array($a_weights_node[0]['children']) ) {
							
							foreach ($a_weights_node[0]['children'] as $weight) {
								$a_weight[] = array('weight' => $weight['attributes']['WEIGHT'], 'cost' => $weight['attributes']['COST'], 'id' => $weight['attributes']['ID']) ;
							}
							array_multisort($a_weight); 

							foreach ($a_weight as $weight) {
								if ($weight['weight'] != "" && $weight['cost'] != "") {
									$this_cost = $weight['cost'];
									if ($order_weight <= $weight['weight']) break;
								}
							}
							unset($a_weight);
						}
						$cost = sprintf("%01.2f",$this_cost + $method_handling_cost + $base_handling_cost);
						// base handling fee + ( item handling fee * number of items ) + ( get_shipping_cost(order_weight) )
						
						$js_options .= "pulldownOptions[$cntr] = \"$a_region_attr[ID]|$id@$cost^$name / ".$currency.$cost."\";\n";
						++$cntr;
					}
				}
			}
			unset($a_method_node);
		}
	}
	
//	$region_pd .= "<option value=\"other\" $sel>[Other]</option>\n</select>\n";
	$method_pd = "<select  class=\"text\" name=\"shipping_method\">\n</select>\n"; 

	if ($othershipping) {
		$otherbox = "If Other, Please Specify<br>
						<input value=\"$a_order_prefill[shipping_other_country]\" type=\"text\" name=\"shipping_other_country\" style=\"width:120\">";
	}
	
	$shipping_content = "
	<table cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td class=\"text\">
				Company/organization to ship order to<br>
				<input $addr_hobble value=\"$a_order_prefill[shipping_company]\" name=\"shipping_company\" type=text style=\"width:280\">
			</td>
			<td class=\"text\">
				
			</td>
			<td class=\"text\">
				<table cellpadding=0 cellspacing=0 border=0>
					<tr>
						<td class=\"text\">Ctry/Rgn<br>$region_pd</td>
						<td class=\"text\">$otherbox</td>
					</tr>
				</table>				
				
			</td>
		</tr>
		<tr>
			<td class=\"text\">
				Contact name<br>
				<input value=\"$a_order_prefill[shipping_name]\" name=\"shipping_name\" type=text style=\"width:280\">
			</td>
			<td class=\"text\">
				
			</td>
			<td class=\"text\">
			Select a Shipping Method<br>$method_pd
				
			</td>
		</tr>
		<tr>
			<td class=\"text\">Address<br><input $addr_hobble value=\"$a_order_prefill[shipping_address1]\" type=\"text\" name=\"shipping_address1\" style=\"width:280\"></td>
			<td> <img src=\"images/spacer.gif\" width=\"1\" height=\"35\"> </td>
			<td>
			</td>
		</tr>
		<tr>
			<td class=\"text\">Address 2<br><input $addr_hobble value=\"$a_order_prefill[shipping_address2]\" type=\"text\" name=\"shipping_address2\" style=\"width:280\"></td>
			<td> <img src=\"images/spacer.gif\" width=\"10\" height=\"35\"> </td>
			<td class=\"text\">
			</td>
		</tr>
		<tr>
			<td class=\"text\">
				<table cellpadding=0 cellspacing=0 border=0>
					<tr>
						<td class=\"text\">City<br><input $addr_hobble value=\"$a_order_prefill[shipping_city]\" type=\"text\" name=\"shipping_city\" style=\"width:140\"></td>
						<td class=\"text\">State<br><input $addr_hobble value=\"$a_order_prefill[shipping_state]\" type=\"text\" name=\"shipping_state\" style=\"width:50\"></td>
						<td class=\"text\">Postal Code<br><input $addr_hobble value=\"$a_order_prefill[shipping_zip]\" type=\"text\" name=\"shipping_zip\" style=\"width:80\"></td>
					</tr>
				</table>
			</td>
			<td> <img src=\"images/spacer.gif\" width=\"1\" height=\"35\"> </td>
			<td class=\"text\"> &nbsp; </td>
		</tr>
	</table>
	";
	
	
	
	// Approval
	$sql = "SELECT ApprovalManagers FROM Sites WHERE ID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
/*	
	$sql = "SELECT * FROM Approval WHERE ID='$a_result[ApprovalID]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
*/	
	$a_approval_tree = xml_get_tree($a_result['ApprovalManagers']);
	
	if ( is_array($a_approval_tree[0]['children']) ) {
		foreach ( $a_approval_tree[0]['children'] as $manager) {
			$id = $manager['attributes']['ID'];
			$name = $manager['attributes']['NAME'];
			$email = $manager['attributes']['USERNAME'];
			if (trim($name) != "" && trim($id) != "") $pd .= "<option value=\"$id\">$name</option>\n";
		}
		
		$approval_pd = "<select class=\"text\" name=\"approval_id\">\n $pd </select>";
	} else {
		$approval_pd = "<i class=\"text\"> &nbsp; &nbsp; [No approval managers found]</i>";
	}

	
	// coupon
	if (isset($_SESSION[order_discount])) {
		$content .= "<input type=\"hidden\" name=\"billing_discount\" value=\"{$_SESSION[order_discount]}\">";
	}
	
	if ($a_site_settings['DontRequirePayment'] != "checked") {
	
	$content .= "
	<input type=\"hidden\" name=\"billing_included\" value=\"Y\">
	<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr><td width=\"500\" class=\"text\">
			<div class=\"subtitle\">Billing </div><br>
			<table cellpadding=0 cellspacing=0 border=0><tr><td class=\"text\">Pay by:&nbsp;</td><td>$payment_pulldown</td></tr></table>
			<br>
		$billing_content
			<br><br>
		</td></tr>
	</table>
	$line
	";
	}

	if ($a_site_settings['ChargeShipping'] == "checked") {
	
	$body_script="onload=\"iIndex=getSubgroup('shipping_region', 'shipping_method', '')\"";
	$content .= "
	<input type=\"hidden\" name=\"shipping_included\" value=\"Y\">
	<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr><td width=\"500\" class=\"text\">
			<table cellpadding=0 cellspacing=0 border=0>
				<tr><td class=\"subtitle\">
					Shipping
					</td><td>
						&nbsp; &nbsp; &nbsp; 
					</td><td>
	";
	
	if ($_SESSION['billing_type'] == "cc") {
		$content .= "<input class=\"button\" type=\"button\" value=\"Use Billing Address\" onClick=\"use_same_address()\">";
	}
	
	
	$content .= "
					</td>
					<td>
					$sel_addr
					</td>
				</tr>
				</table>
				<br>
			$shipping_content<br>
			
			
<!--
				Shipping methods: rush, normal (created by manager)<br>
				- weight tables + base cost<br>
				- price per M + base<br>
				- specific cost assoc. with each qty<br>
				- international shipping tables 			
			<br><br>//-->

		</td></tr>
	</table>
	$line
	";
	}
	
	if ($a_site_settings['IncludeApprovalManager'] == "checked") {
	$content .= "
	<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr><td width=\"500\" class=\"text\">
			<div class=\"subtitle\">Approval</div><!-- <br>
			You can set up a list of locations, persons, or some other category to choose from for approval. 
			You can also just have one individual receive all approval notices so the buyer doesn't have to select this option.<br>//-->
			<br>
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td class=\"text\" nowrap> Select appropriate approval option </td>
					<td> $approval_pd</td>
				</tr>
			</table>
			<br>
		</td></tr>
	</table>
	$line			
	";
	}
	
	if ($a_site_settings['IncludeSpecialInstructions'] == "checked") {
	$content .= "
	<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr><td width=\"500\" class=\"text\">
			<div class=\"subtitle\">Special Instructions </div>
			<br>
			$instruction_note 
			<textarea name=\"special_instructions\" style=\"width:580\" wrap=\"virtual\" rows=\"5\">$a_order_prefill[special_instructions]</textarea>
			<br><br>
		</td></tr>
	</table>
	$line";			
	}
	
	$content .= "
	<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr><td width=\"500\" class=\"text\">
			<div class=\"subtitle\">Your email address</div><br>
			<div class=\"text\">Required for sending invoice and order approval notification</div>
			<input type=\"text\" name=\"email\" style=\"width:200\" value=\"$a_order_prefill[email]\">
			<br><br>
		</td></tr>
	</table>
	$line";			
	
	
	
	
	$content .= "
	<img src=\"images/spacer.gif\" height=\"20\" width=\"1\">
	
	<!-- BUTTONS START HERE //-->
	<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr><td width=\"500\" class=\"subtitle\">
			<a href=\"$script_name?site=$_SESSION[site]&os_action=gotocart&ossid=$_SESSION[ossid]\">
			<input class=\"button\" type=\"button\" value=\"&laquo; Back to cart\" onclick=\"document.location = '$script_name?os_action=gotocart&site=$_SESSION[site]&ossid=$_SESSION[ossid]'\"></a>
			$checkout_btn 
			<input type=\"hidden\" name=\"os_action\" value=\"preconfirmorder\"> 
			<input type=\"hidden\" name=\"sid\" value=\"$ossid\"> 
			<br><br>
		</td></tr>
	</table>

";
$content .= "
<script language=\"javascript\">
var pulldownOptions = new Array();
$js_options
</script>
";


	$content = iface_make_box($content,600,100,1);
	$sPage = MakePageStructure($os_sidebar,$content);



// **************************************************  PRE-CONFIRM ORDER  ************************************************************

?>
