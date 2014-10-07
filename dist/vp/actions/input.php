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


	$_SESSION['os_page'] = "input";
	$a_optional_fields = array_find_key_prefix("optional_fieldset_", $a_form_vars, 1);
	
	if ( is_array($a_optional_fields) ) {
		$c = 0;
		$a_optional[0]['tag'] = "options";
		foreach ($a_optional_fields as $key=>$val) {
			$a_optional[0]['children'][$c]['attributes']['ID'] = $key;
			$a_optional[0]['children'][$c]['tag'] = "option";
			++$c;
		}
		$xml = addslashes(xml_make_tree($a_optional));
	}
	
	if ($_SESSION['itemincart'] == "1") { 
		$sql = "UPDATE Cart SET OptionalFieldSets='$xml' WHERE ID='$_SESSION[cartitemid]' AND SiteID='$_SESSION[site]'";
		$n_update = dbq($sql, "updating optional field sets");
	} else {
		list($cost,$qty) = explode(":",$_SESSION['costqty-' . $_SESSION['itemid']]);
		$sql = "INSERT INTO Cart SET 
			OptionalFieldSets='$xml',
			SiteID='$_SESSION[site]', 
			SessionID='$ossid',
			ItemID='$_SESSION[itemid]',
			Cost='$cost',
			Qty='$qty'";
		$n_update = dbq($sql, "updating optional field sets");
		$_SESSION['cartitemid'] = db_get_last_insert_id();
		$_SESSION['itemincart'] = "1";

	}
?>
