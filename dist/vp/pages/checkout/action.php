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

	
	if (($a_site_settings["ShowAccount"] == "checked" && $_SESSION["skiplogin"] != true) || ($a_site_settings["CatalogRequireLogin"]) == "Checkout") {
		$_SESSION["show_login"] = true;
	} else {
		$_SESSION["show_login"] = false;	
	}

	if (($_SESSION['logged_in'] == 0 && $_SESSION['show_login']) || ($_SESSION['logged_in'] == 0 && $_SESSION['billing_type'] == "po")) {
		header("Location: vp.php?os_page_afterlogin=checkout&os_page=login&os_sid=$_SESSION[os_sid]&site=$_SESSION[site]");
		exit();
	}

?>
