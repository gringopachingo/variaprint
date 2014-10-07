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
	
	
	if ( $a_form_vars['pw'] == "baa320d3093c1ef22d49e6dca09e37e8" && trim($_SESSION[site]) != "") {// 

		if ($a_form_vars['action'] == "write")  {
			if (is_array($a_form_vars)) {
				$ShippingID = $a_form_vars[ShippingID];
				foreach($a_form_vars as $key=>$value) {
					if (substr($key,0,5) == "name_") {
						$this_id = str_replace("name_","",$key);
						if ($a_form_vars["update_".$this_id] == 1) {
							$sql = "UPDATE Shipping SET Name='".addslashes($a_form_vars["name_".$this_id])."',Definition='".addslashes($a_form_vars["definition_".$this_id])."' WHERE ID='$this_id' AND SiteID='$_SESSION[site]'";
							dbq($sql);
							$return .= "updatedid_".$this_id."=1&"; 
						} elseif ($a_form_vars["new_".$this_id] == 1) {
							$sql = "INSERT INTO Shipping SET Name='".addslashes($a_form_vars["name_".$this_id])."',Definition='".addslashes($a_form_vars["definition_".$this_id])."',SiteID='$_SESSION[site]'";
							dbq($sql);
							$new_id = db_get_last_insert_id();
							$return .= "oldid_".$this_id."=".$new_id."&"; 
							if ($a_form_vars['ShippingID'] == $this_id) {
								$ShippingID = $new_id;
							} 
						} elseif ( ($a_form_vars["delete_".$this_id] == 1)) {
							$sql = "DELETE FROM Shipping WHERE ID='$this_id' AND SiteID='$_SESSION[site]'";
							dbq($sql);
						}
					}
				}
			}
			$sql = "UPDATE Sites SET ShippingID='$ShippingID' WHERE ID='$_SESSION[site]'";
			dbq($sql);

			print($return);
			print("test=");
		//	print_r($a_form_vars);

//			$sql = "UPDATE Items SET FieldSections='". addslashes($a_form_vars[input]). "' WHERE ID='$_SESSION[item_id]'";
//			dbq($sql);


			//print("error=0");
		} elseif ($a_form_vars['action'] == "read") {
			$sql = "SELECT * FROM Shipping WHERE Template='Y'";
			$r_result = dbq($sql);
			while($a = mysql_fetch_assoc($r_result)) {
				$print .= "istemplate_".$a['ID']."=true"."&";
				$print .= "definition_".$a['ID']."=&";
				$print .= "name_".$a['ID']."=".urlencode($a['Name'])."&";
			}
			
			
			$sql = "SELECT * FROM Shipping WHERE SiteID='$_SESSION[site]' AND Template!='Y'";
			$r_result = dbq($sql);
			while($a = mysql_fetch_assoc($r_result)) {
				$print .= "istemplate_".$a['ID']."=false"."&";
				$print .= "definition_".$a['ID']."=".urlencode($a['Definition'])."&";
				$print .= "name_".$a['ID']."=".urlencode($a['Name'])."&";
			}

			$sql = "SELECT ShippingID FROM Sites WHERE ID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$a = mysql_fetch_assoc($r_result);
			
			$print .= "ShippingID=".$a['ShippingID'];
			
			exit($print);
			
		//	$site_id = $a_result["SiteID"];
			
			// Site ShippingID
			// XML for each profile
			// Name for each profile
			// Whether profile is a preset
		} else {
			exit("error=".urlencode("No action defined..."));
		}
	} else if (count($a_form_vars) > 0 ) {
		print("error=".urlencode("Parameters not included correctly.\n"));
	} else {
		print("error=".urlencode("No variables received."));
	}


?>
