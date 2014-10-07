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

	
	
	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	$a_form_vars = array_merge($_GET,$_POST);
	
	session_name("mssid");	
	session_start();
	
	if ($a_form_vars['action']=="read") {
		$sql = "SELECT VendorManagers FROM Sites WHERE ID='$_SESSION[site]'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		
		$managers = $a_result['VendorManagers'];
		
		
		print("permissions=".urlencode($managers));
	}elseif($a_form_vars['action']=="write") {
		$sql = "UPDATE Sites SET VendorManagers='$a_form_vars[permissions]' WHERE ID='$_SESSION[site]'";
		dbq($sql);
		exit(" ");
	}

?>
