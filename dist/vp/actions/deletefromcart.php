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


if ( isset($a_form_vars['deleteitemid']) ) {
	if ($a_form_vars[confirmed] == "1") {
		$sql = "DELETE FROM Cart WHERE ID='$a_form_vars[deleteitemid]' AND SiteID='$_SESSION[site]'";
		dbq($sql);
		
		if ($a_form_vars['deleteitemid'] == $_SESSION['deleteitemid']) {
			$_SESSION['cartitemid'] = "";
			header("Location: vp.php?os_page=catalog&site=$_SESSION[site]&os_sid=$_SESSION[os_sid]");
		}
	} else {
		//	display alert msg on page
		$_SESSION[show_alert] = 1;
		$_SESSION[alert_msg] = "<a href=\"vp.php?site=$_SESSION[site]&os_action=deletefromcart&cartitemid=$a_form_vars[deleteitemid]&os_sid=$_SESSION[os_sid]&confirmed=1\">Click here</strong> to confirm that you want to delete this item from your cart.";
	}
}
?>