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


	SecureServerOn(true);
	
	$title = $a_site_settings['InvoiceTitle'];//"Order Confirmed";
	$description = $a_site_settings['InvoiceText'];//"This is the checkout page.";
	$os_sidebar = iface_make_sidebar($title, $description);

	$content = "
	<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr><td width=\"500\" class=\"text\">
		<!--	 email invoice<br> //-->
			<a href=\"javascript:;\" onClick=\"popupWin('print_invoice.php?site=$_SESSION[site]&order_id=$_SESSION[order_id]&os_sid=$_SESSION[os_sid]','','width=650,toolbar=1,resizable=1,centered=1')\">view printable invoice</a><br>
			<br><br>
		</td></tr>
	</table>
	";
	
	$invoice = $content . invoice("regular",$_SESSION['order_id']);
	// Send an email invoice
	invoice("email",$_SESSION['order_id']); 
		
	
	$content = iface_make_box($invoice,600,100,1);
	$sPage = MakePageStructure($os_sidebar,$content);


?>