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

if ( isset($a_form_vars['itemid']) ) {
	$aformfields = array_find_key_prefix("field_",$a_form_vars);

	$xml = xml_array_to_string($aformfields,"field_");

	if ($_SESSION['itemincart'] == "1") { 
		$sql = "UPDATE Cart SET Imprint='". addslashes($xml) . "' WHERE ID='$_SESSION[cartitemid]' AND SiteID='$_SESSION[site]'";
		$n_update = dbq($sql, "updating optional field sets");
	} else {
		$sql = "INSERT INTO Cart SET 
			SessionID='$ossid', 
			SiteID='$_SESSION[site]', 
			ItemID='$a_form_vars[itemid]', 
			Cost='$a_form_vars[cost]', 
			Qty='$a_form_vars[qty]', 
			Imprint='" . addslashes($xml) . "'";
		dbq($sql, "updating optional field sets");
		$_SESSION['cartitemid'] = db_get_last_insert_id();
		$_SESSION['itemincart'] = "1";
	}


	$_SESSION['itemid'] = $a_form_vars['itemid'];
//	$_SESSION['cartitemid'] = $last_insert_id;
	$_SESSION['os_action'] = "edititem";
	$_SESSION['os_page'] = "preview_gen";	

}

?>
