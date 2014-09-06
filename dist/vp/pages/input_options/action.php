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

	$a_site_settings = GetSiteAttributes($_SESSION[site], $_SESSION[mode]);
	
	if ($_SESSION['logged_in'] == 0 && ($a_site_settings['CatalogRequireLogin'] == "Add" ||  $a_site_settings['CatalogRequireLogin'] == "Catalog")) {
		header("Location: vp.php?os_page_afterlogin=input_options&os_page=login&os_sid=$_SESSION[os_sid]&site=$_SESSION[site]");
	}
//	print("action: ". $_SESSION['os_action'] );
	if ($_SESSION['os_action'] == "edititem" && $_SESSION['cartitemid'] == "") {
		header("Location: vp.php?os_page=catalog&os_sid=$os_sid");
	}	

	
?>
