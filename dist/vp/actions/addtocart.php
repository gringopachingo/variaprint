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

// ACTION CALLED RIGHT AFTER USER CLICK ON "ADD" IN CATALOG
if ( isset($a_form_vars['itemid']) ) {
	$sql = "SELECT Custom FROM Items WHERE ID='$a_form_vars[itemid]' AND SiteID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	
	if ($a_result["Custom"] != "N") { 

		$_SESSION['os_action'] = "additem";
		$_SESSION['cartitemid'] = "";
	
		$_SESSION['itemincart'] = "0";
		$_SESSION['os_page'] = "input_options";
	
		$costqtyfield = "costqty-" . $a_form_vars['itemid'];
		if ( isset($a_form_vars[$costqtyfield] )  && $a_form_vars[$costqtyfield] != "") {  
			$_SESSION[$costqtyfield] = $a_form_vars[$costqtyfield]; 
		}
		
	} else {
	//	$_SESSION['os_action'] = "additem";
	//	$_SESSION['cartitemid'] = "";
	
		$_SESSION['itemincart'] = "0";
		$_SESSION['os_page'] = "catalog";
	
		$costqtyfield = "costqty-" . $a_form_vars['itemid'];
		if ( isset($a_form_vars[$costqtyfield] )  && $a_form_vars[$costqtyfield] != "") {  
			list($cost,$qty) = explode(":",$a_form_vars[$costqtyfield]);
		}
		
		
		$sql = "INSERT INTO Cart SET SessionID='$os_sid', 
			ItemID='$a_form_vars[itemid]', 
			SiteID='$_SESSION[site]', 
			Cost='$cost', 
			Qty='$qty'";
		$n_update = dbq($sql, "updating optional field sets");
		$_SESSION['cartitemid'] = db_get_last_insert_id();
		$_SESSION['itemincart'] = "1";
	}
}

?>