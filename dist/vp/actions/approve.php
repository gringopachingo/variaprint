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


	$sql = "SELECT ReqApproval FROM Items WHERE ID='$_SESSION[itemid]' AND SiteID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_item = mysql_fetch_assoc($r_result);
	
	if ( trim($a_form_vars['approve_initials']) != "" || $a_item['ReqApproval'] == "false") {
		
		if ($a_item['ReqApproval'] == "false") {
			$a_form_vars[approve_initials] = "[n/a]";
		}
			
		// Save to DB
		$sql = "UPDATE Cart SET ApprovalInitials='". addslashes($a_form_vars[approve_initials]) . "' WHERE ID='$_SESSION[cartitemid]'";
		dbq($sql);
		
		if ($_SESSION["modifyitem"]) {
			$_SESSION["modifyitem"] = false;
			// move the item from the cart back to the orderitems table
			$sql = "SELECT Imprint FROM Cart WHERE ID='$_SESSION[cartitemid]' AND SiteID='$_SESSION[site]'";
			$result = dbq($sql);
			$a_result = mysql_fetch_assoc($result);
			$sql = "UPDATE OrderItems SET Imprint='".addslashes($a_result["Imprint"])."' WHERE ID='$_SESSION[moditemid]'";
			dbq($sql);
			
			// set the order status back to what it was
			$sql = "UPDATE Orders SET Status='$_SESSION[originalorderstatus]' WHERE ID='$_SESSION[modorderid]'";
			dbq($sql);
			
			// remove the cartitem
			$sql = "DELETE FROM Cart WHERE ID='$_SESSION[cartitemid]' AND SiteID='$_SESSION[site]'";
			dbq($sql);
			
			// Move cart files into the printing area
			$movefrom = $cfg_base_dir . "/_cartpreviews/" . $_SESSION[cartitemid]  . "_preview_pdf.pdf";
			$moveto = $cfg_base_dir . "_orderpdfs/" . $_SESSION[moditemid]  . "_preview_pdf.pdf";
			if ( file_exists($movefrom) ) {
				if (file_exists($moveto) ) {
					unlink($moveto);
				}
				rename($movefrom, $moveto);
			}
			
			$movefrom = $cfg_base_dir . "/_cartpreviews/" . $_SESSION[cartitemid]  . "_preview_raster.jpg";
			$moveto = $cfg_base_dir . "_orderpdfs/" . $_SESSION[moditemid]  . "_preview_raster.jpg";
			if ( file_exists($movefrom) ) {
				if (file_exists($moveto) ) {
					unlink($moveto);
				}
				rename($movefrom, $moveto);
			}
			 
			$movefrom = $cfg_base_dir . "/_cartpreviews/" . $_SESSION[cartitemid]  . "_press_pdf.pdf";
			$moveto = $cfg_base_dir . "_orderpdfs/" . $_SESSION[moditemid]  . "_press_pdf.pdf";
			if ( file_exists($movefrom) ) {
				if (file_exists($moveto) ) {
					unlink($moveto);
				}
				rename($movefrom, $moveto);
			} 
			
			$_SESSION['cartitemid'] = "";
			
			$_SESSION['os_page'] = "account";
			if ($_SESSION['in_prod'] == 1) {
				$_SESSION['alert_msg'] = "The item has been modified. Since it has already been downloaded for production, you should contact us to verify that it will be printed with your most recent changes.";
			} else {
				$_SESSION['alert_msg'] = "The item has been modified and will now be printed with your most recent changes.";
			}
			$_SESSION['show_alert'] = 1;
			
			// send notice email
			mail($cfg_admin_email,"item modified by buyer","Site $_SESSION[site]  Order Item ID: $_SESSION[moditemid]");
			
		} else {
	
			$_SESSION['os_page'] = "catalog";
			if ($a_item['ReqApproval'] == "false") {
				$_SESSION['alert_msg'] = "You may add another item to this order from the catalog below or 
				<a href=\"vp.php?site=$_SESSION[site]&os_action=gotocart&os_sid=$os_sid\">checkout</a> and complete your order.";

			} else {
				$_SESSION['alert_msg'] = "The item was approved with the initials &quot;$a_form_vars[approve_initials]&quot;. <br><br>
				You may add another item to this order from the catalog below or 
				<a href=\"vp.php?site=$_SESSION[site]&os_action=gotocart&os_sid=$os_sid\">checkout</a> and complete your order.
				";
			}
			$_SESSION['show_alert'] = 1;
		}	
	} else {
		$_SESSION['alert_msg'] = "You must enter your initials to indicate that you have reviewed this proof.";
		$_SESSION['show_alert'] = 1;
	}
?>
