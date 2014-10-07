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

	$aformfields = array_find_key_prefix("field_",$a_form_vars);
	$xml = xml_array_to_string($aformfields,"field_");

/*	
print("<!--");
print_r($xml);
print("//-->");
//exit;
*/

	//$a_tree = xml_get_tree($xml);
	

	$sql = "SELECT Name FROM Items WHERE ID='$_SESSION[itemid]' AND SiteID='$_SESSION[site]'";
	$result = dbq($sql);
	$a_res = mysql_fetch_assoc($result);
	
//	$a_tree = xml_update_value("FIELDS","ITEMNAME", $a_res['Name'], $a_tree);
	
	if ($_SESSION['modifyitem']) {
		$where = "WHERE ID='$_SESSION[cartitemid]'";
	} else {
		$where = "WHERE SessionID='$ossid' AND ID='$_SESSION[cartitemid]'";	
	}
	
	//$xml = xml_make_tree($a_tree);
	
	$sql = "UPDATE Cart SET Imprint='" . addslashes($xml) . "' " . $where . " AND SiteID='$_SESSION[site]'";
	$nUpdate = dbq($sql);

	$_SESSION['os_page'] = "preview_gen";
	$_SESSION['overwrite_prefill'] = 0;	

?>
