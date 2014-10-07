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




// *********************************************************************************************
function pad_it($it,$w="100%") {
	$table = "
	<table cellpadding=8 cellspacing=0 border=0 width=\"$w\">
		<tr>
			<td width=\"500\" class=\"text\">
				$it 
			</td>
		</tr>
	</table>
	";
	return $table;
}

function pad_it2($it,$it2,$w="200") {
	$table = "
	<table cellpadding=8 cellspacing=0 border=0 width=\"$w\">
		<tr>
			<td class=\"text\">
				$it 
			</td>
			<td class=\"text\" align=right>
				$it2
			</td>
		</tr>
	</table>
	";
	return $table;
}

function invoice_item_row($it,$it2,$w="540") {
	$table = "
	<table cellpadding=8 cellspacing=0 border=0 width=\"$w\">
		<tr>
			<td class=\"text\">
				$it 
			</td>
			<td width=\"186\" class=\"text\" align=\"right\">
				$it2
			</td>
		</tr>
	</table>
	";
	return $table;
}

function invoice($style="regular",$order_id="") {
	// Flavors: regular, pre, & printable
	global $a_site_settings, $ossid, $currency;
	
	$emailinvoice = false;
	if (strtolower($style)=="email") {
		$emailinvoice = true;
		$style = "printable";
	}
	
	if ( $style == "regular" || $style == "printable" ) {
		$title = "Invoice # ".$order_id;


		// Get current orderinfo
		$sql = "SELECT DateOrdered,OrderInfo FROM Orders WHERE ID='$order_id'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		
		$a_tree = xml_get_tree(decrypt($a_result['OrderInfo'],""));
		
		if ( is_array($a_tree[0]['children']) ) {
			foreach($a_tree[0]['children'] as $node) {
				$a_order_info[$node['attributes']['ID']] = $node['value'];
			}
		} else {
		//	exit("Order information not found. ");
		}
		
		
		
	} elseif ($style == "pre") {
		$title = "Pre-invoice confirmation";
		
		// Get current orderinfo
		$sql = "SELECT OrderInfo FROM Sessions WHERE SessionID='$ossid'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		
		$a_tree = xml_get_tree(decrypt($a_result['OrderInfo'],""));
		
		if ( is_array($a_tree[0]['children']) ) {
			foreach($a_tree[0]['children'] as $node) {
				$a_order_info[$node['attributes']['ID']] = $node['value'];
			}
		} else {
		//	exit("Pre-order information not found. ");
		}
	} 
	
	switch ($style) {
		case "printable" :
			$line = "<hr size=1 noshade width=540 align=\"left\">";
			$shortline = "<hr size=1 width=200 noshade align=\"right\">";
			$width = 540;
			break;
			
		case "regular" :
		case "pre" :
			$line = iface_dottedline();
			$shortline = iface_dottedline("192");
			$width = 596;
			
	}



	// Loop thru items
	if ($style == "pre") {
		$sql = "SELECT ID,ItemID,Qty,Cost FROM Cart WHERE SessionID='$ossid' AND SiteID='$_SESSION[site]'";
		$r_result = dbq($sql); 
	} else {
		$sql = "SELECT ID,ItemID,Qty,Cost FROM OrderItems WHERE OrderID='$order_id'";
		$r_result = dbq($sql); 
	}
		
	while ( $a_item = mysql_fetch_assoc($r_result) ) {
		$sql = "SELECT Name,Weight FROM Items WHERE ID='" . $a_item['ItemID'] . "'";
		$r_result2 = dbq($sql);
		$a_item2 = mysql_fetch_assoc($r_result2);
		$cost = "";	$qty = ""; $middle = "";
		if (trim($a_item['Cost']) != "") {
			$cost = $currency . $a_item['Cost'] ;
		}
		if (trim($a_item['Qty']) != "") {
			$qty = $a_item['Qty'];
		}
		if ($qty != "" && $cost != "") {
			$middle = " / ";
		}
		$invoice_items .=	invoice_item_row("$a_item2[Name]", $cost . $middle . $qty, $width) . $line;
	
		$subtotal += $a_item['Cost'];
	}

	
	// Totals
	$subtotal = sprintf("%01.2f",$subtotal);
	$shipping_cost = sprintf("%01.2f",$a_order_info['shipping_cost']);
	$tax = sprintf("%01.2f",$a_order_info['tax']);
	
	$total = sprintf("%01.2f",$subtotal + $shipping_cost + $tax);


	
	if ($a_result['DateOrdered'] != "") { $date = date("M d, Y",$a_result['DateOrdered']); }
	
	$billingtitle = "Billing";
	//	$a_order_info contains the current order data -- cc, check, po

	$show_totals = false;
	if ($a_order_info[billing_included] == "Y") {
		$show_totals = true;
		if ($a_order_info[billing_type] == "pp") {
			if ($style != "pre") {
				$billing_info = "<br>
				<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"paypalwin\"> 
				<input type=\"hidden\" name=\"cmd\" value=\"_xclick\"> 
				<input type=\"hidden\" name=\"business\" value=\"".$a_order_info[billing_paypalemail]."\"> 
				<input type=\"hidden\" name=\"item_name\" value=\"".$a_order_info[billing_paypalprefix]." Invoice #$order_id\"> 
				<input type=\"hidden\" name=\"amount\" value=\"".$total."\"> 
				<input type=\"submit\" value=\"Pay Now with PayPal!\">
				</form>
				<br>
				";
//				<input type=\"hidden\" name=\"item_number\" value=\"Post Item Number(s)/Order Number Here (optional)\"> 
			}
		} elseif ($a_order_info[billing_type] == "cc") {
			if ($_SESSION['privilege'] == "owner") {
				$billing_info = $a_order_info[billing_card_type] . ":  " . $a_order_info[billing_card_number] . " &#8212;  exp: " . $a_order_info[billing_card_exp_month] . "/" .  $a_order_info[billing_card_exp_year] . "<br>" .
				$a_order_info[billing_address1] . "<br>";
			} else {
				$billing_info = $a_order_info[billing_card_type] . ": ...." . substr($a_order_info[billing_card_number], -4) . " &#8212;  exp: " . $a_order_info[billing_card_exp_month] . "/" .  $a_order_info[billing_card_exp_year] . "<br>" .
				$a_order_info[billing_address1] . "<br>";
				
			}
			if ( trim($a_order_info[billing_address2]) != "" ) { $billing_info .= $a_order_info[billing_address2] . "<br>"; }
			$billing_info .=	$a_order_info[billing_city] . ", " . $a_order_info[billing_state] . "  " . $a_order_info[billing_zip] ."<br>";
			if ( trim($a_order_info[billing_country]) != "" ) { $billing_info .= $a_order_info[billing_country] . "<br>"; }
			
		} elseif ($a_order_info[billing_type] == "check") {
			$billing_info = "";
			
		} elseif ($a_order_info[billing_type] == "po") {
			$billing_info = "<i>Paid by Purchase Order</i><br>" . 
			$a_order_info['billing_company'] . "<br>" .
			$a_order_info['billing_name'] . "<br>" .
			$a_order_info['billing_street'] . "<br>" .
			$a_order_info['billing_street2'] . "<br>" .
			$a_order_info['billing_city'] . ", " . $a_order_info['billing_state'] . " " . $a_order_info['billing_zip'] . "<br>" .
			$a_order_info['billing_phone'] . "<br>" .
			$a_order_info['billing_email'] . "<br>"	;

		} 	
	} else {
		$billingtitle = "";	
	}
	

	$billing_info .= "<br>" . htmlentities($a_order_info['billing_note']);
	
	$shippingtitle = "Shipping";
	// Lookup shipping info
	if ($a_order_info[shipping_included] == "Y") {
		
		$region_name = $a_order_info['region_name'];
		$method_name = $a_order_info['method_name'];
		
		//
		if ($a_order_info['shipping_company'] != "") {
			$shipping_info .= $a_order_info['shipping_company'] . "<br>";
		}
		if ($a_order_info['shipping_name'] != "") {
			$shipping_info .= $a_order_info['shipping_name'] . "<br>";
		}
		
		$shipping_info .= $a_order_info['shipping_address1'] . "<br>" ;
		if ( trim($a_order_info['shipping_address2'] ) != "" ) { $shipping_info .= $a_order_info['shipping_address2'] . "<br>" ;  }
		$shipping_info .= $a_order_info['shipping_city'] . ", " . $a_order_info['shipping_state'] . " " . $a_order_info['shipping_zip'] . "<br>";
		if ( $a_order_info['shipping_region'] != "other" ) { 
			$shipping_info .= $region_name . "<br>" ; 
		} else {
			$shipping_info .= $a_order_info['shipping_other_country'] . "<br>" ; 
		}	
		$shipping_info .= "<br>";
		$shipping_info .= "Method: " . $method_name;
	
	} else {
		$shippingtitle = "";
	}
	
	// coupon
	if (isset($a_order_info['billing_discount'])) {
		$discount_section = pad_it2("Discount:","- ".$currency.$a_order_info['billing_discount']) . $shortline;
		$total = sprintf("%01.2f",$total-$a_order_info['billing_discount']);
	}
	
	
	// START LAYING OUT INVOICE ***************************************************************************
	
	
	// Title and date
	$invoice = pad_it("
	<b class=\"subtitle\">$title</b> &nbsp; $date<br>
		" . str_replace("\n","<br>",htmlentities($a_order_info['seller_address'])) . "<br>
		<em>". htmlentities($a_order_info[general_note]) ."</em><br>") ; 
	
	if ( trim($a_order_info[special_instructions]) != "") {
		$invoice .= $line . pad_it("<strong>Special Instructions:</strong> " . $a_order_info[special_instructions]) ;
	}
	$invoice .=	$line ;
	
	if ($a_order_info[shipping_included] == "Y" || $a_order_info[billing_included] == "Y" ) {
	// Billing and shipping
	$invoice .=	 "
		<table cellpadding=8 cellspacing=0 border=0 width=$width>
			<tr>
				<td width=50% valign=top class=text>
					<strong>$billingtitle</strong><br>
					$billing_info
				</td>
				<td width=50% valign=top class=text>
					<strong>$shippingtitle</strong><br>
					$shipping_info 
				</td>
			</tr>
		</table>";
	}
	
	// Items titles
	$invoice .=	 
		"<br>
		<table cellpadding=8 cellspacing=0 border=0 width=$width>
			<tr>
				<td width=398 class=text><strong>Item description</strong></td>
				<td class=text align=\"right\"><strong>Cost / Quantity</strong></td>
			</tr>
		</table>
		" . $line ;
	
	$invoice .= $invoice_items;
	
	if ($show_totals) {
		
		$invoice .= "<div align=\"right\">" .
			pad_it2("Subtotal:", $currency . $subtotal) . $shortline  .
			$discount_section.
			pad_it2("Tax:", $currency . $tax) . $shortline .
			pad_it2("Shipping:", $currency . $shipping_cost) . $shortline  .
			pad_it2("<strong>Total:", $currency . $total . "</strong>") . $shortline .
			"</div>" ;
	}
	
	$invoice = "<table cellpadding=0 cellspacing=0 border=0 width=540><tr><td>" . $invoice . "</td></tr></table>";

	if ($emailinvoice) {
		$to_email = $a_order_info["email"];
		
//		print("sending mail to: ".$to_email) ;

		$message = "<html><body>".$invoice."</body></html>";
		$sender_name = "Invoice";
		$sender_email = $cfg_invoice_from_email;
		
		$subject = "Invoice" ;
		$headers  = "Return-Path: $to_email\n";
//		$headers .= "To: $to_email\n";
		$headers .= "MIME-version: 1.0\n";
		$headers .= "X-Mailer: invoice mailer\n";
		$headers .= "X-Sender: $sender_email\n";
		$headers .= "From: $sender_name <$sender_email>\n";
		$headers .= "Content-type: text/html\n";
		
		// and now mail it 
		mail($to_email, $subject, $message, $headers);

	} else {
		return $invoice;
	}
}

function alert_msg($msg) {
//	session_register();
	$_SESSION['show_alert'] = 0;
	$_SESSION['alert_msg'] = "";
	
	if ($msg != "") {
		return iface_make_box(" 
			<table cellpadding=8 cellspacing=0 border=0>
			<tr>
				<td class=\"text\" valign=\"top\"><img src=\"_sites/$_SESSION[site]/ifaceimg/icon-alert.png\"></td>
				<td class=\"text\">
				$msg
			</td></tr></table>
			
		") . "<br><br>";
	}
}


function make_costqty_menu($cartid, $selqty="", $addtitle=true, $action="") {
	global $a_site_settings, $currency;
	
	$pref = $a_site_settings;
	/*
	$currency = "$";
	switch(strtolower($a_site_settings[Currency])) {
		case "dollar" : $currency = "$"; break;
		case "euro" : $currency = "&euro;"; break;
		case "pound" : $currency = "&pound;"; break;
		case "yen" : $currency = "&yen;"; break;
	}
	*/
//	if ($pref['DontRequirePayment'] == "checked") {
//			$pricingoption = NULL;
//	} else {
		
		if ($pref['HideCost'] == "CostQty" && $pref['DontRequirePayment'] == "checked") {
			$pricingoption = NULL;
		} else {
			$sql = "SELECT Pricing FROM Items WHERE ID='$cartid'";
			$nResult = dbq($sql); 
			$a = mysql_fetch_assoc($nResult);
			$a = xml_get_tree($a['Pricing']);
			$passone = true;
			
			if ($pref['HideCost'] == "Show" || $pref['DontRequirePayment'] != "checked") {
				$slash = " / ";
			}
		
			$type = $a[0]['attributes']['PRICETYPE'];
			if ($type == "link") {
			//	$link_node = xml_find_node("LINK",$a[0]['children']);
				$link_id = $a[0]['attributes']['LINKVALUE'];
				
				// get linked xml pricing definition
				$sql = "SELECT * FROM Pricing WHERE ID='$link_id'";
				$nResult = dbq($sql); 
				$a = mysql_fetch_assoc($nResult);
				$a = xml_get_tree($a['Definition']);
				
				$type = $a[0]['attributes']['PRICETYPE']; 
			} 
			
			if ( $type == "compute" ) {
				$compute_node = xml_find_node("COMPUTE",$a[0]['children']);
				// print_r();
				$first_prc = $compute_node[0]['attributes']['C_FIRST_PRICE'];
				$first_amt = $compute_node[0]['attributes']['C_FIRST_AMOUNT'];
				$add_amt = $compute_node[0]['attributes']['C_ADD_AMOUNT'];
				$add_prc = $compute_node[0]['attributes']['C_ADD_PRICE'];
				$max_qty = $compute_node[0]['attributes']['C_MAX_AMOUNT'];
				if ($max_qty == "") { $max_qty = $add_amt*5; }
				
				$pricingoptions .= "<option value=\"$first_prc:".str_replace(",","",$first_qty)."\">" . $currency . $first_prc . " / " . $first_amt . "</option>";
				$cntr = 0; $this_amt = $first_amt + $add_amt ; $this_prc = $first_prc + $add_prc;
				while ($cntr < $max_qty) {
					if (str_replace(",","",$this_amt) == $selqty) { $selected = "selected"; } else { $selected = "";  }
					if ($passone) { $defaultqty = $this_amt; $defaultcost = $this_prc; $passone = false; }
					if ($pref['HideCost'] == "Show" || $pref['DontRequirePayment'] != "checked") { 
						$this_prc_prt = $currency . $this_prc ; 
					} else {
						$this_prc = "";
						$hidecost = true;
					}
					$pricingoptions .= "<option value=\"$this_prc:".str_replace(",","",$this_amt)."\" $selected>" . $this_prc_prt . $slash . $this_amt . "</option>";
					$this_prc += $add_prc ;
					$this_amt += $add_amt ;
					$cntr += $add_amt;
				}
				
					
			} else if ( $type == "pairs" ) {
				$pairs_node = xml_find_node("PAIRS",$a[0]['children']);
				
				if ( is_array($pairs_node[0]['children']) ) {
					foreach ($pairs_node[0]['children'] as $thisnode) {
						if ($thisnode['attributes']['COST'] != "" && $thisnode['attributes']['QTY']) {
							$cost = $thisnode['attributes']['COST']; $qty = $thisnode['attributes']['QTY'];
							if (str_replace(",","",$qty) == $selqty) { $selected = "selected"; } else { $selected = "";  }
							if ($passone) { $defaultqty = $qty; $defaultcost = $cost; $passone = false; }
							if ($pref['HideCost'] == "Show" || $pref['DontRequirePayment'] != "checked") { 
								$this_prc_prt = $currency . $cost ; 
							} else {
								$cost = "";
								$hidecost = true;
							}
							$pricingoptions .= "<option value=\"$cost:".str_replace(",","",$qty)."\" $selected>" . $this_prc_prt . $slash . $qty . "</option>";
						}
					}
				}
			}
			if ($hidecost) {
				$defaultcost = "";
			}
			if ($pref['HideCost'] == "Show" || $pref['DontRequirePayment'] != "checked") { $this_prc_prt = "price" ; }
			if ($addtitle) { 
				$pricingoptions = "<option value=\"".$defaultcost.":".str_replace(",","",$defaultqty)."\">".$this_prc_prt.$slash."qty</option>".$pricingoptions ; 
			}
			$pricingoption .= "<select class=\"text\" name=\"costqty-$cartid\" $action>$pricingoptions</select>";
	
		}
//	}
	return $pricingoption;
}

function cart_get_imprint($cartid,$convertUTF=false) {
	$sql = "SELECT Imprint FROM Cart WHERE ID='$cartid' AND SiteID='$_SESSION[site]'";
	$nResult = dbq($sql);
	$aResult = mysql_fetch_assoc($nResult);
	$xmlPrefill = $aResult['Imprint'];
	$aPrefillTree = xml_get_tree($xmlPrefill);
	if ( is_array($aPrefillTree[0]['children']) ) {
		foreach ( $aPrefillTree[0]['children'] as $v ) {
		//	$value = ($convertUTF) ? utf8ToISO_8859_1($v['value']) : $v['value'] ;	
		//	print ("<!-- ".$v['attributes']['ID'].": ".utf8ToISO_8859_1($v['value'])." //-->");
			if ( $v['value'] != "") { $aRet[$v['attributes']['ID']] = ($convertUTF) ? utf8ToISO_8859_1($v['value']) : $v['value'] ; }
		}
		return $aRet;
	} else {
		return false;
	}
}


function ModifyColor($color, $amt) {
	if ( ereg("[0-9a-fA-F]{6}",$color) ) {
		$counter = 1;
		$rc = "#";
		while ( $c = substr($color, $counter, 2) ) {
			$c = hexdec($c); $c = $c + $amt; if ($c > 255) { $c = 255; } else if ($c < 0) {  $c = 0; }
			$hex = dechex($c); if (hexdec($hex) < 10) { $hex = "0" . $hex; }
			$rc .= $hex;
			$counter += 2;
		}
		return $rc;
	} else {
		return $color;
	}
}



function parseProperties($s) {
	$aTmp = explode("\n",$s);
	while ( list($k, $v) = each($aTmp) ) {
		list($k2, $v2) = explode("=",  $v);
		$aProperties[$k2] = $v2;
	}
	return $aProperties;
}






?>
