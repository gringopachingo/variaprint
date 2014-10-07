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
	
	
	if ( isset($_SESSION['item_id']) && $_SESSION['item_id'] != "" && $a_form_vars['pw'] == "baa320d3093c1ef22d49e6dca09e37e8" ) {

		if ($a_form_vars['action'] == "write")  {
			$sql = "UPDATE Items SET 
				Name='$a_form_vars[Name]',
				Description='". addslashes($a_form_vars[Description]) . "',
				GroupID='$a_form_vars[GroupID]',
				ImpositionID='$a_form_vars[ImpositionID]',
				VendorUsername='". addslashes($a_form_vars[VendorUsername]) . "',
				Weight='" . addslashes($a_form_vars[Weight]) . "',
				SmallIconLink='" . addslashes($a_form_vars[SmallIconLink]) . "',
				SmallShadow='" . addslashes($a_form_vars[SmallShadow]) . "',
				LargeIconLink='" . addslashes($a_form_vars[LargeIconLink]) . "',
				LargeShadow='". addslashes($a_form_vars[LargeShadow]) . "',
				Pricing='" . addslashes($a_form_vars[Pricing]) . "',
				PDFProof='$a_form_vars[PDFProof]',
				ReqApproval='$a_form_vars[ReqApproval]'
				WHERE ID='$_SESSION[item_id]' AND SiteID='$_SESSION[site]'";
			dbq($sql);

//			$sql = "UPDATE Items SET FieldSections='". addslashes($a_form_vars[input]). "' WHERE ID='$_SESSION[item_id]'";
//			dbq($sql);


			print("error=0");
		} elseif ($a_form_vars['action'] == "read") {
			$sql = "SELECT 
				SiteID,
				Name, 	 
				Description,	 
				GroupID,	 
				ImpositionID, 	 
				VendorUsername, 	 
				Weight, 	 
				SmallIconLink, 	 
				SmallShadow, 	 
				LargeIconLink, 	 
				LargeShadow, 	 
				Pricing,	 
				PDFProof,	 
				ReqApproval	 
				FROM Items WHERE ID='$_SESSION[item_id]' AND SiteID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			
			$site_id = $a_result["SiteID"];
			
			$print = "Name=".urlencode($a_result['Name'])."&";
			$print .= "Description=".urlencode(str_replace("\n\r","\n",$a_result['Description']))."&";
			$print .= "GroupID=".$a_result['GroupID']."&";
			$print .= "ImpositionID=".$a_result['ImpositionID']."&";
			$print .= "VendorUsername=".$a_result['VendorUsername']."&";
			$print .= "Weight=".urlencode($a_result['Weight'])."&";
			$print .= "SmallIconLink=".urlencode($a_result['SmallIconLink'])."&";
			$print .= "SmallShadow=".urlencode($a_result['SmallShadow'])."&";
			$print .= "LargeIconLink=".urlencode($a_result['LargeIconLink'])."&";
			$print .= "LargeShadow=".urlencode($a_result['LargeShadow'])."&";
			$print .= "Pricing=".urlencode($a_result['Pricing'])."&";
			$print .= "PDFProof=".urlencode($a_result['PDFProof'])."&";
			$print .= "ReqApproval=".urlencode($a_result['ReqApproval'])."&";
			
			$sql = "SELECT 
				ItemGroups,
				ApprovalManagers,
				VendorManagers
				FROM Sites WHERE ID='$a_result[SiteID]'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			
			$print .= "ItemGroups=".urlencode($a_result['ItemGroups'])."&";
			$print .= "ApprovalManagers=".urlencode($a_result['ApprovalManagers'])."&";
			$print .= "VendorManagers=".urlencode($a_result['VendorManagers'])."&";
			
			// Send imposition styles
			$sql = "SELECT Name,ID FROM Imposition WHERE Template='Y'";
			$r_result = dbq($sql);
			$imp = "<?xml?><imps>";
			while ($a_result = mysql_fetch_assoc($r_result)) {
				$imp .= "<imp id=\"$a_result[ID]\" name=\"$a_result[Name]\"/>";
			}
			$imp .= "</imps>";
			$print .= "DefaultImpositions=".urlencode($imp)."&";
						
		//	$sql = "SELECT Name,ID FROM Imposition WHERE MasterUID='$_SESSION[user_id]'";
			$sql = "SELECT Name,ID FROM Imposition WHERE SiteID='$site_id'";
			$r_result = dbq($sql);
			$imp = "<?xml?><imps>";
			while ($a_result = mysql_fetch_assoc($r_result)) {
				$imp .= "<imp id=\"$a_result[ID]\" name=\"$a_result[Name]\"/>";
			}
			$imp .= "</imps>";
			$print .= "CustomImpositions=".urlencode($imp)."&";
			
			// Send pricing styles
			$sql = "SELECT Name,ID FROM Pricing WHERE SiteID='$site_id'";
			$r_result = dbq($sql);
			$price = "<?xml?><pricestyles>";
			while ($a_result = mysql_fetch_assoc($r_result)) {
				$price .= "<style id=\"$a_result[ID]\" name=\"$a_result[Name]\"/>";
			}
			$price .= "</pricestyles>";
			
			$print .= "PriceStyles=".urlencode($price);

			exit($print);
		} else {
			exit("error=We didn't understand what action to take...");
		}
	} else if (count($a_form_vars) > 0 ) {
		print(urlencode("error=You didn't include all the parameters correctly."));
	} else {
		print(urlencode("error=It doesn't look like you're sending any form variables."));
	}


?>
