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


	if (isset($a_form_vars["reorderid"])) {
		$sql = "SELECT * FROM OrderItems WHERE OrderID='$a_form_vars[reorderid]'";
		$result = dbq($sql);
		
		while ($a_orderitem = mysql_fetch_assoc($result)) {
			$sql = "INSERT INTO Cart SET 
				SessionID='$ossid', 
				SiteID='$_SESSION[site]', 
				Qty='$a_orderitem[Qty]', 
				Cost='$a_orderitem[Cost]',
				Imprint='".addslashes($a_orderitem["Imprint"])."',
				ItemID='$a_orderitem[ItemID]'
			";
			dbq($sql);
			
			$_SESSION[os_page] = "catalog";
			
			// Copy the press & proof images over
			
			
		}
	}

?>
