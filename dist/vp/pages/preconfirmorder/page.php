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

	$title = $a_site_settings['PreInvoiceTitle']; // "Confirm Order";
	$description = $a_site_settings['PreInvoiceText'];// "This is the checkout page.";
	$os_sidebar = iface_make_sidebar($title, $description);
	$invoice = "
	<script language=\"javascript\">
	function confirmOrder(btnObj) {
		document.forms[0].submit();
		btnObj.disabled=true;
	}
	</script>
	";
	
	$invoice .= invoice("pre") . 
	pad_it("
	<a href=\"$script_name?site=$_SESSION[site]&os_action=checkout&os_sid=$_SESSION[os_sid]\"><input class=\"button\" type=\"button\" value=\"&laquo; Back\" ".
		"onclick=\"document.location = '$script_name?os_action=checkout&site=$_SESSION[site]&os_sid=$_SESSION[os_sid]'\"></a>
			<input class=\"button\" type=\"submit\" value=\"Confirm &raquo;\"> 
			<input type=\"hidden\" name=\"os_action\" value=\"confirmorder\"> 
			<br><br>
		")	;
	
	$content = iface_make_box($invoice,600,100,1);
	$sPage = MakePageStructure($os_sidebar,$content);


?>
