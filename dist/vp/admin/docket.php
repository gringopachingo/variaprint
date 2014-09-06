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

	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	require_once("../inc/encrypt.php");
	require_once("inc/functions.php");
	require_once("inc/iface.php");
	require_once("inc/session.php");

	if ($suppress_display != true) { // only do this if the imposition is being downloaded
		session_name("ms_sid");
		session_start();
		$ms_sid = session_id();

		if (!$_SESSION['privilege_dockets']) {
			require_once("inc/popup_log_check.php");
		}
	}

	

	
	
	function make_docket($docket_id) {
		$sql = "SELECT * FROM Dockets WHERE ID='$docket_id' AND SiteID='$_SESSION[site]'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		
		if (mysql_num_rows($r_result) < 1) {
			return false;
		}
		
		$run = $a_result["PressRun"];
		$priority = $a_result["Priority"];
		$datedue = date("M d, Y (D)",$a_result["DateDue"]);
		$datecreated = date("M d, Y (D)",$a_result["DateCreated"]);
		
		$xml_imp = xml_get_tree($a_result["ImpositionLayout"]);
		$xml_doc = xml_get_tree($a_result["OrderItems"]);
		
		// IMPOSITION INFORMATION
		// page, row, item
		$page_number = 1;
		if(is_array($xml_imp[0]['children'])) {
			$imp = "
			<table width=\"690\" cellpadding=\"0\" border=\"0\" cellspacing=\"0\">
				<tr>
					<td class=\"title\">Imposition layout</td>
				</tr>
			";
			foreach($xml_imp[0]['children'] as $page){
				$imp .= "
				<tr>
					<td class=\"text\">Page $page_number</td>
				</tr>
				
				";
				$fp = true;
				if (is_array($page['children'])) {
					foreach($page['children'] as $row) {
						$imp .= "<tr>\n";
						
						if (is_array($row['children'])) {
							foreach($row['children'] as $item) {
								$position = $item['attributes']['POSITION'];
								$order_id = $item['attributes']['ORDER_ID'];
								$item_id = $item['attributes']['ITEM_ID'];
								
								if ($order_id != "") {
									$item_content = "<strong>$position<br>Order #$order_id</strong><br>Order item #$item_id";
								} else {
									$item_content = "<strong>$position</strong><br>blank";
								}						
								$items .= "<td class=\"text\" valign=\"top\">$item_content</td>\n";
								$divider .= "<td height=\"20\"><img src=\"images/blue-dot.gif\" width=\"100%\" height=\"1\"></td>";
							}
						}
						if ($fp) { 
						$imp .= "
						<tr height=\"20\">$divider</tr>
						";
						}
						$imp .= "<tr>$items</tr>
						<tr height=\"20\">$divider</tr>
						";
						$divider = "";
						$items = "";
						$fp = false;
					}
				}
				$imp .= "<tr><td>&nbsp;<br>&nbsp;<br></td></tr>";
				++$page_number;
			}
			$imp .= "</table><br><br>";
			$page = null;
		}
	
//	print_r($xml_doc);	
	
	
	// ORDER INFORMATION
		if (is_array($xml_doc[0]['children'])) {
			$doc .= "<table width=\"690\" cellpadding=0 cellspacing=0 border=0>
			<tr>
				<td class=\"title\">Order information</td>
			</tr>
			
			";
			
			
			$fp = true;
			foreach ($xml_doc[0]['children'] as $order) {
				if ($fp) {
				$doc .= "
				<tr>
					<td colspan=\"2\" height=\"20\"><img src=\"images/blue-dot.gif\" width=\"690\" height=\"1\"></td>
				</tr>
				";
				}
				$fp = false;
				$order_id = $order["attributes"]["ORDER_ID"];
				$item_id = $order["attributes"]["ITEM_ID"];
				$positions = $order["attributes"]["IMPOSE_POS"];
	
				$sql = "SELECT ID FROM OrderItems WHERE OrderID='$order_id'";
				$r_result = dbq($sql);
				$total_orders = mysql_num_rows($r_result);
				
				$sql = "SELECT Qty,ItemName,Imprint,ApprovalInitials FROM OrderItems WHERE ID='$item_id'";
				$r_result = dbq($sql);
				$a_result = mysql_fetch_assoc($r_result);
				
				$qty = $a_result["Qty"];
				$itemname = $a_result["ItemName"];
				$approval_initials = $a_result["ApprovalInitials"];
		//		$approval_initials = $a_result["ApprovalInitials"];
	
				$sql = "SELECT OrderInfo,Email FROM Orders WHERE ID='$order_id'";
				$r_result = dbq($sql);
				$a_result = mysql_fetch_assoc($r_result);
				
				$order_info = xml_get_tree(decrypt($a_result[OrderInfo],""));
				
				if (is_array($order_info[0]['children'])) {
					foreach($order_info[0]['children'] as $node) {
						$a_order_info[$node['attributes']['ID']] = $node['value'];
					}
				}
				$ordered_by = "<strong>Ordered by: </strong><a href=\"mailto:$a_result[Email]\">$a_result[Email]</a>";
				$ordered_by .= "<br><strong>Approval Initials: </strong>$approval_initials";
				
//				print_r($a_order_info);
				$shipping = "";
				if ($a_order_info[shipping_included] == "Y") { 
					$shipping .= (!empty($a_order_info['shipping_company'])) ? $a_order_info['shipping_company'] . "<br>" : "" ;
					$shipping .= (!empty($a_order_info['shipping_name'])) ? $a_order_info['shipping_name'] . "<br>" : "" ;
					$shipping .= $a_order_info[shipping_address1]."<br>";
					$shipping .= (!empty($a_order_info[shipping_address2])) ? $a_order_info[shipping_address2]."<br>" : "" ; 
					$shipping .= $a_order_info[shipping_city].", ".$a_order_info[shipping_state]." ".$a_order_info[shipping_zip];
					$shipping .= "<br><br>";
					$shipping .= "<strong>Shipping method:</strong> ".$a_order_info[method_name];
					
					
				} else {
					$shipping = "[none]"; 
				}
	/*
		[method_name] => test
		[shipping_cost] => 5.00
		[shipping_included] => Y
		[shipping_address1] => 1812 N Eaglet Ct
		[shipping_region] => 2
		[shipping_other_country] => 
		[shipping_address2] => 
		[shipping_city] => Nampa
		[shipping_state] => ID
		[shipping_zip] => 83651
	*/
				$special_instructions = $a_order_info["special_instructions"];
				
				$a_order_info = NULL;
	
				$doc .= "
				<tr>
					<td class=\"text\" valign=\"top\" width=\"460\">
						<strong>Order item #$item_id</strong> &#8212; Imposed at: $positions
						<br>
						Order #$order_id &#8212; Total items in order: $total_orders<br>
						
							Quantity: $qty<br>
							Item: $itemname <br>
						
					</td>
					<td class=\"text\" valign=\"top\" width=\"230\">
						<strong>Shipping address</strong><br>
						$shipping
						<br><br>
						$ordered_by
					 </td>
				</tr>
				";
				
		//	Customer special instructions<br>
				if (trim($special_instructions) != "") {
				$doc .= "
				<tr>
					<td colspan=\"2\" class=\"text\"><br>
					<strong>Special instructions from customer: </strong>$special_instructions
					</td>
				</tr>
				";
					
				}
		//	Manager processing instructions	
				
				$doc .= "
				<tr>
					<td colspan=\"2\" height=\"20\"><img src=\"images/blue-dot.gif\" width=\"690\" height=\"1\"></td>
				</tr>";
			}	
			$doc .= "</table>";
		}
		
$docket = "
<html>
<head>
<title>Docket #$docket_id </title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"style.css\" rel=\"stylesheet\" type=\"text/css\">
</head>
<body>
<span class=\"titlebold\">Docket #$docket_id</span><br>
<table width=\"268\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr>
    <td width=\"93\" nowrap class=\"text\"><strong>Date created:</strong></td>
    <td width=\"175\" class=\"text\">".$datecreated."</td>
  </tr>
  <tr>
    <td class=\"text\"><strong>Date due:</strong></td>
    <td class=\"text\">".$datedue."</td>
  </tr>
  <tr>
    <td class=\"text\"><strong>Priority:</strong></td>
    <td class=\"text\">".$priority."</td>
  </tr>
  <tr>
    <td class=\"text\"><strong>Press run:</strong></td>
    <td class=\"text\">".$run."</td>
  </tr>
</table>
<span class=\"text\"> <br>
</span><br>
".$imp.$doc."
</body>
</html>";


		return $docket;
	}
	
	if ($suppress_display != true) { // only do this if the imposition is being downloaded
		$docket = make_docket($a_form_vars["id"]);
		print($header_content);
		print($docket);
	}
	
?>
