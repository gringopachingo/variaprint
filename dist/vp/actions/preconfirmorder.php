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


	// This is the action that validates/saves checkout data just before going to the order preconfirm page

	$a_card_fields = array_find_key_prefix("card_",$a_form_vars);
	$a_billing_fields = array_find_key_prefix("billing_",$a_form_vars);
	$a_ship_fields = array_find_key_prefix("shipping_",$a_form_vars);
	$a_inst	= array('special_instructions' => $a_form_vars['special_instructions']);
	
	$a_order_info = array_merge($a_card_fields, $a_billing_fields, $a_ship_fields, $a_inst);
	$a_order_info["billing_card_number"] = cc_trim($a_order_info["billing_card_number"]);
	$a_order_info['email'] = $a_form_vars['email'];
	$a_order_info['approval_id'] = $a_form_vars['approval_id'];
	
	// VALIDATE *********************
	// Billing
	if ($a_order_info["billing_type"] == "cc") {
		if (trim($a_order_info["billing_card_holder"]) == "") {
			$error = true; $alert .= "- Card holder's name is required.<br>";
		}
		if (trim($a_order_info["billing_address1"]) == "") {
			$error = true; $alert .= "- Billing address is required.<br>";
		}
		if (trim($a_order_info["billing_city"]) == "") {
			$error = true; $alert .= "- Billing city is required.<br>";
		}
		if (trim($a_order_info["billing_state"]) == "") {
			$error = true; $alert .= "- Billing state is required.<br>";
		}
		if (trim($a_order_info["billing_zip"]) == "") {
			$error = true; $alert .= "- Billing zip is required.<br>";
		}
		if (trim($a_order_info["billing_card_number"]) == "") {
			$error = true; $alert .= "- Credit card number is required.<br>";
		} elseif ($a_order_info["billing_card_type"] != "DC" && !cc_validate($a_order_info["billing_card_number"], $a_order_info["billing_card_type"])) {
			$error = true; $alert .= "- Credit card number is incorrect. Make sure you have selected the correct type of card.<br>";
		}
		if ($a_order_info["billing_card_exp_year"] == "") {
			$error = true; $alert .= "- Credit card expiration year needs to be selected.<br>";
				}
		if ($a_order_info["billing_card_exp_month"] == "") {
			$error = true; $alert .= "- Credit card expiration month needs to be selected.<br>";
		}
	} elseif ($a_order_info["billing_type"] == "po") {

	}
	
	if ($a_order_info["shipping_included"] == "Y") {
		if (trim($a_order_info["shipping_address1"]) == "") {
			$error = true; $alert .= "- Shipping address is required.<br>";
		}
		if (trim($a_order_info["shipping_city"]) == "") {
			$error = true; $alert .= "- Shipping city is required.<br>";
		}
		if (trim($a_order_info["shipping_state"]) == "") {
			$error = true; $alert .= "- Shipping state is required.<br>";
		}
		if (trim($a_order_info["shipping_zip"]) == "") {
			$error = true; $alert .= "- Shipping postal code is required.<br>";
		}
		
		if ($a_order_info["shipping_region"] == "other" && trim($a_order_info["shipping_other_country"]) == "") {
			$error = true; $alert .= "- Other shipping country is required.<br>";
		}
		
	}
	
	if (trim($a_order_info["email"]) == "") {
		$error = true; $alert .= "- Email address is required.<br>";
	} elseif (!ereg("@",$a_order_info["email"])) {
		$error = true; $alert .= "- Email address is invalid.<br>";
	}
	
	
	if (!$error) {
		$_SESSION['os_page'] = "preconfirmorder";
	} else {
		$_SESSION['show_alert'] = true;
		$_SESSION['alert_msg'] = $alert;
		$_SESSION['os_page'] = "checkout";
	}
	
	// SAVE *************************
	
	
	// We need to get the site settings for this action
	$a_site_settings = GetSiteAttributes($_SESSION[site], $_SESSION[mode]);


	// Validate required fields / cc number 
	
	
	
	
	// Save appropriate notes
	// General
	$a_order_info['general_note'] = $a_site_settings['InvoiceNote'];
		
	switch ($a_order_info['billing_type']) {
		case "pp" :
			$a_order_info['billing_note'] = $a_site_settings['InvoicePayPalNote'];
			$a_order_info['billing_paypalemail'] = $a_site_settings['PayPalEmail'];
			$a_order_info['billing_paypalprefix'] = $a_site_settings['PayPalPrefix'] ;			
			$a_order_info['billing_paypalitems'] = "";
			
			
			break;
			
		case "cc" :
			$a_order_info['billing_note'] = $a_site_settings['CCNote'];
			$bill_state = $a_order_info['billing_state'];
			break;
			
		case "po" :
			$a_order_info['billing_note'] = $a_site_settings['PONote'];
			$bill_state = $a_order_info['billing_state'];
			break;
			
		case "check" :
			$a_order_info['billing_note'] = $a_site_settings['InvoiceCheckNote'];			
			$bill_state = $a_order_info['shipping_state'];
			break;
			
		case "" :
			$a_order_info['billing_note'] = $a_site_settings['NoPaymentNote'];			
	}
	
	// Calculate tax
	if ( $a_site_settings['ChargeTax'] == "checked" ) {
		// Get tax out of DB
		$sql = "SELECT Taxes FROM Sites WHERE ID='$_SESSION[site]'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		$a_tree = xml_get_tree($a_result['Taxes']);
		if ( is_array($a_tree[0]['children']) ) {
			foreach($a_tree[0]['children'] as $node) {
				if (strtoupper($node['attributes']['NAME']) == strtoupper($bill_state)) {  
					$a_order_info['tax'] = sprintf("%01.2f", ($node['attributes']['TAX']/100) * $_SESSION['order_subtotal']) ;  
					break; 
				} 
			}
		}
	}
	
	$a_order_info['seller_address'] = $a_site_settings['InvoiceAddress'];
	$a_order_info['general_note'] = $a_site_settings['InvoiceNote'];
	
	$a_order_info['subtotal'] = $_SESSION['order_subtotal'];
	
	// Add shipping method name and cost to order info
	$sql = "SELECT ShippingID FROM Sites WHERE ID='$_SESSION[site]'";	$r_result = dbq($sql);		$a_result = mysql_fetch_assoc($r_result);
	$sql = "SELECT * FROM Shipping WHERE ID='$a_result[ShippingID]'";	$r_result = dbq($sql);		$a_result = mysql_fetch_assoc($r_result);
	$a_shipping_tree = xml_get_tree($a_result['Definition']);
	
	list($method_id,$shipping_cost) = explode("@",$a_order_info[shipping_method]);
	
	$region_node = xml_get_node_by_path("SHIPPING/REGION:$a_order_info[shipping_region]",$a_shipping_tree);
	$method_node = xml_get_node_by_path("SHIPPING/REGION:$a_order_info[shipping_region]/METHOD:$method_id",$a_shipping_tree);
	
	$a_order_info['region_name'] = $region_node['attributes']['NAME'];
	$a_order_info['method_name'] = $method_node['attributes']['NAME'];
	$a_order_info['shipping_cost'] = $shipping_cost;
	
	
	// Make XML object to save in the DB
	$a_order_tree = array();
	if ( is_array($a_order_info) ) {
		foreach($a_order_info as $k=>$v) {
			$a_order_tree = xml_update_value("ORDER/FIELD:$k","CDATA",$v,$a_order_tree);
		}
	}
	
	
	$a_order_tree2 = xml_delete_node("ORDER/FIELD:card_number",$a_order_tree);
	if (!$a_order_tree2) { $a_order_tree2 = $a_order_tree; }
	
	$xml = addslashes(encrypt(xml_make_tree($a_order_tree),"")); 
	
	$xml2 = addslashes(encrypt(xml_make_tree($a_order_tree2),""));
	
	$sql = "SELECT ID FROM Sessions WHERE SessionID='$os_sid'";	
	$r_result = dbq($sql);
	if (mysql_num_rows($r_result) == 0) {
		$sql = "INSERT INTO Sessions SET OrderInfo='$xml', SessionID='$os_sid'";	
		dbq($sql);
	} else {
		$sql = "UPDATE Sessions SET OrderInfo='$xml' WHERE SessionID='$os_sid'";	
		dbq($sql);
	}
	
	
	// Add info to user
	$sql = "UPDATE Users SET OrderInfo='$xml2' WHERE ID='$_SESSION[user_id]' AND SiteID='$_SESSION[site]'";	
	dbq($sql);
	
//	print_r($a_order_info);

?>