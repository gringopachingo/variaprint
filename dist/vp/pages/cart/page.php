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

	SecureServerOn(true);
	
	$_SESSION["skiplogin"] = false;
	$title = $a_site_settings['CartTitle'];//"Cart";
	$description = $a_site_settings['CartText'];//"This is your cart. This text&mdash;like most text&mdash;is editable";
	$os_sidebar = iface_make_sidebar($title, $description);

	
	

	$sql = "SELECT ID, ItemID, ApprovalInitials, Qty, Cost FROM Cart WHERE SessionID='$ossid' AND SiteID='$_SESSION[site]'";
	$r_result = dbq($sql); 
	
	if ( mysql_num_rows($r_result) > 0) {
		
		$show_totals = true;
		
		// Create the appropriate label
		if ($a_site_settings['DontRequirePayment'] == "checked" ) {
			if ( $a_site_settings['HideCost'] == "Show" ) {
				$costqty_label = "Cost / Quantity";
			} elseif ( $a_site_settings['HideCost'] == "Cost" ) {
				$costqty_label = "Quantity";
				$show_totals = false;
			} else {
				$show_totals = false;
			}
		} else {
			$costqty_label = "Cost / Quantity";
		}
		
		$cart .= "
		<!-- START CART  //-->
		<table cellpadding=6 cellspacing=0 border=0 >
		<tr>
			<td><img src=\"images/spacer.gif\" width=\"46\" height=\"1\"></td>
			<td><img src=\"images/spacer.gif\" width=\"348\" height=\"1\"></td>
			<td><img src=\"images/spacer.gif\" width=\"1\" height=\"1\"></td>
		</tr>
		<tr>
			<td width=\"46\" class=\"text\"><img src=\"images/spacer.gif\" width=\"46\" height=\"1\"></td>
			<td width=\"348\" class=\"text\"><strong>Item Description</strong></td>
			<td class=\"text\"><strong>$costqty_label</strong></td>
		</tr>
		</table>
		" . iface_dottedline() ;
		$_SESSION['order_subtotal'] = $subtotal = 0;
		// Display each item in cart
		while ( $a_item = mysql_fetch_assoc($r_result) ) {
			$sql = "SELECT Name,Custom,Weight,ReqApproval FROM Items WHERE ID='" . $a_item['ItemID'] . "' AND SiteID='$_SESSION[site]'";
			$r_result2 = dbq($sql);
			$a_item2 = mysql_fetch_assoc($r_result2);
			
			$weight += $a_item2['Weight'] * $a_item['Qty']/1000;
			
			
			if ( trim($a_item['ApprovalInitials']) == "" && $a_item2["Custom"] != "N" && $a_item2["ReqApproval"] != "false") {
				$missingapproval = true;
				$approved = false;
			} else {
				$approved = true;
			}
			
			$qtypulldown = make_costqty_menu( $a_item['ItemID'], 
				$a_item['Qty'], 
				false, 
				"onchange=\"document.location='vp.php?cartitemid=$a_item[ID]&os_action=updatecostqty&costqty=' + this.value + '&site=$_SESSION[site]&ossid=$_SESSION[ossid]'\"");
			$cart .= iface_make_cart_row($a_item2['Name'], $qtypulldown, $a_item['ID'], $a_item['ItemID'], $ossid, $approved) . iface_dottedline();
			$subtotal += $a_item['Cost'];
		}
		
		$_SESSION['order_weight'] = $weight;
		$_SESSION['order_subtotal'] = $subtotal = sprintf("%01.2f", $subtotal);
		$shortline = iface_dottedline("173");
		$payment_pulldown = iface_payment_pulldown($a_site_settings, "", $_SESSION['billing_type']);
		
		if ($payment_pulldown != NULL) {
				$select_payment_type = "
				<td class=\"text\" align=\"right\">Pay&nbsp;by:</td>
				<td width=\"100\">$payment_pulldown</td>
				";
		} else {
			$select_payment_type = "
			<td class=\"text\" align=\"right\"></td>
			<td width=\"100\"></td>
			";
		}
		
		if ($missingapproval) {
			$checkoutbtn = "<input type=\"button\" onClick=\"alert('Please approve all items above by clicking on &quot;approve...&quot; next to each item before checking out.')\" class=\"button\" value=\"Checkout &raquo;\">";
		} else {	
			$checkoutbtn = "<input type=\"submit\" class=\"button\" value=\"Checkout &raquo;\">";
		}
		
		
		if ($show_totals) {
			if ($a_site_settings['ShowCoupon'] == "checked") {
				if (!empty($fv[coupon])) $_SESSION['coupon'] = $fv[coupon];
				$sql = "SELECT * FROM DiscountCoupons WHERE Code='$_SESSION[coupon]' AND SiteID='$_SESSION[site]'";
				$res = dbq($sql);
				$a_coupon = mysql_fetch_assoc($res);
			//	print_r($a_coupon);
				if ($a_coupon['ExpirationDate'] > time()) {
					if ($a_coupon['Type']=="Percent") {
						$discount = sprintf("%01.2f", $subtotal*($a_coupon['Amount']/100));
					} else {
						$discount = sprintf("%01.2f", ($a_coupon['Amount']));
						if ($subtotal<$discount) $discount = $subtotal ;
					}
				} else {
					$discount = "0.00";
				}
				$_SESSION['order_discount'] = $discount;

				$coupon_box = "
				<table cellpadding=6 cellspacing=0 border=0>
					<tr>
						<td align=\"right\" nowrap class=text>
							Enter Discount Coupon Code <input value=\"{$_SESSION[coupon]}\" type=text size=8 name=coupon> 
							<input onClick=\"document.location = 'vp.php?os_action=updatecostqty&coupon='+document.forms[0].coupon.value\" type=\"button\" value=\"Add Coupon &raquo;\">
						</td>
						<td width=\"1\">&nbsp;</td>
						<td width=\"50\" class=\"text\" align=\"left\"><strong>Discount</strong></td>
						<td width=\"90\" class=\"text\" align=\"right\">".$currency.$discount."</td>
						<td width=\"1\">&nbsp;</td>
					</tr>
				</table>
				$shortline
				";
			}

			$total = sprintf("%01.2f", $subtotal - $discount);
		
			$cart .= "
			<div align=\"right\">
			<input type=\"hidden\" name=\"weight\" value=\"$weight\">
			<table cellpadding=6 cellspacing=0 border=0 width=180>
				<tr>
					<td  class=\"text\" align=\"left\">Subtotal</td>
					<td  class=\"text\" align=\"right\">".$currency.$subtotal."</td>
					<td width=\"1\">&nbsp;</td>
				</tr>
			</table>
			$shortline
$coupon_box
			<table cellpadding=6 cellspacing=0 border=0 width=180>
				<tr>
					<td class=\"text\" align=\"left\">Shipping</td>
					<td class=\"text\" align=\"right\">to&nbsp;be&nbsp;calculated</td>
					<td width=\"1\">&nbsp;</td>
				</tr>
			</table>
			$shortline
			<table cellpadding=6 cellspacing=0 border=0 width=180>
				<tr>
					<td class=\"text\" align=\"left\">Tax</td>
					<td class=\"text\" align=\"right\">to&nbsp;be&nbsp;calculated</td>
					<td width=\"1\">&nbsp;</td>
				</tr>
			</table>
			$shortline

			<table cellpadding=6 cellspacing=0 border=0>
				<tr>
					$select_payment_type
					<td align=\"left\" width=200>
						$checkoutbtn
						<input type=\"hidden\" name=\"os_action\" value=\"checkout\">
					</td>
					<td width=\"50\" class=\"text\" align=\"left\"><strong>Total</strong></td>
					<td width=\"90\" class=\"text\" align=\"right\">".$currency.$total."</td>
					<td width=\"1\">&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><img src=\"images/spacer.gif\" height=\"1\" width=\"200\"></td>
					<td><img src=\"images/spacer.gif\" height=\"1\" width=\"50\"></td>
					<td><img src=\"images/spacer.gif\" height=\"1\" width=\"90\"></td>
					<td width=\"1\">&nbsp;</td>
				</tr>
			</table>
			
			<img src=\"images/spacer.gif\" height=\"16\" width=\"1\">
			</div>
			";
		} else {
			$cart .= "
			<br><br><div align=\"right\">
			<table cellpadding=8 cellspacing=0 border=0>
				<tr>
					$select_payment_type
					<td align=\"right\" width=\"200\">
						$checkoutbtn
						<input type=\"hidden\" name=\"os_action\" value=\"checkout\">
					</td>
				</tr>
			</table></div>";
			
		}
				
	} else { // no items in cart
		$cart .= "
		<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr>
			<td width=\"500\" class=\"text\">No items in cart.</td>
		</tr>
		</table>";
	}

	$content = iface_make_box($cart,600,100,1);
	$sPage = MakePageStructure($os_sidebar,$content);


?>
