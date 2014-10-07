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


	require_once("../../inc/config.php");
	require_once("../../inc/functions-global.php");
	$a_form_vars = array_merge($_POST,$_GET);
	
	session_name("mssid");
	session_start();
	
	
	if ( isset($_SESSION['item_id']) && $_SESSION['item_id'] != "" ) {
	/*	if ($a_form_vars['action'] == "write")  {
			$sql = "UPDATE Items SET Template='". addslashes($a_form_vars[template]). "' WHERE ID='$a_form_vars[item_id]'";
			dbq($sql);
			print("error=0");
		} elseif ($a_form_vars['action'] == "read") {*/
		
		$sql = "SELECT Template,FieldSections FROM Items WHERE ID='$_SESSION[item_id]'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		if ( $a_result['FieldSections'] == "" ) $a_result['FieldSections'] = "empty";
		exit("fieldgroups=".urlencode($a_result['FieldSections'])."&template=".urlencode($a_result['Template']));
		
/*		} else {
			exit("error=We didn't understand what action to take...");
		}
	} else if (count($a_form_vars) > 0 ) {
		print(urlencode("error=You didn't include all the parameters correctly."));
		print_r($a_form_vars);
	} else {
		print(urlencode("error=It doesn't look like you're sending any form variables."));*/
	}

?>
