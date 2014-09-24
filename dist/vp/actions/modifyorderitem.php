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


	if (isset($a_form_vars["moditemid"])) {
		$_SESSION["os_page"] = "input_options";
		$_SESSION["os_action"] = "edititem";
		$_SESSION["moditemid"] = $a_form_vars["moditemid"];
		$_SESSION["modifyitem"] = true; // Flag that tells all the pages to modify
		$_SESSION["show_alert"] = true;
		$_SESSION["alert_msg"] = "Your order is now on hold until you finish modifying this item or click cancel below.";
		
		// Copy orderitem to cart for temp storage and assign cartitemid to session
		$sql = "SELECT OrderID,Imprint,ItemID FROM OrderItems WHERE ID='$a_form_vars[moditemid]'";
		$result = dbq($sql);
		$a_result = mysql_fetch_assoc($result);
		
		$sql = "
		INSERT INTO Cart SET
			SessionID='$ms_sid',
			SiteID='$_SESSION[site]', 
			ItemID='$a_result[ItemID]',
			Imprint='" . addslashes($a_result["Imprint"]) . "'
		"; 
		dbq($sql);
		
		unset($_SESSION["costqty-".$a_result[ItemID]]);
		$_SESSION["itemid"] = $a_result[ItemID];
		$_SESSION["cartitemid"] = db_get_last_insert_id();
		$_SESSION["modorderid"] = $a_result["OrderID"];
		
		// Put order on hold
		$sql = "SELECT Status FROM Orders WHERE ID='$a_result[OrderID]'";
		$result = dbq($sql);
		$a_result2 = mysql_fetch_assoc($result);
		$_SESSION["originalorderstatus"] = $a_result2["Status"];
		
		$sql = "UPDATE Orders SET Status='30' WHERE ID='$a_result[OrderID]' AND SiteID='$_SESSION[site]'";
		dbq($sql);
		

	//	$sql = "UPDATE Cart SET SessionID='$os_sid' WHERE SavedID='$a_form_vars[savedorderid]'";
	//	dbq($sql);
		
	
	}

?>