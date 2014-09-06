<?

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


//print_r($a_form_vars);

$a_dockets = array_find_key_prefix("checkbox_", $a_form_vars, 1);

//print_r($a_dockets);

if (is_array($a_dockets)) {
	foreach($a_dockets as $k=>$yes) {
		$sql = "SELECT OrderItems FROM Dockets WHERE ID='$k' AND SiteID='$_SESSION[site]'";
		$res = dbq($sql);
		$a_res = mysql_fetch_assoc($res);
		$a_xml = xml_get_tree($a_res['OrderItems']);
		
		if (is_array($a_xml[0]['children'])) {
			foreach($a_xml[0]['children'] as $imp_item) {
				$order_ids[$imp_item['attributes']['ORDER_ID']] = true;
			}
		}
	}
	
	$fp = true;
	
	if (is_array($order_ids)) {
		foreach($order_ids as $key=>$blank) {
			if (!$fp) {
				$where .= ",";
			}
			$where .= "$key";
			$fp = false;
		}
		header("Location: vp.php?action=order_search_results&init_search=1&s&ordernumber=". urlencode($where));
		exit();
	} else {
		print("error");
	}
	
//	print_r($order_ids);


/*
		
<?xml version="1.0" encoding="iso-8859-1"? >
<docket><orderitem impose_pos="A1, B1, C1" order_id="20006" item_id="20006"/><orderitem impose_pos="A2, B2, C2" order_id="20023" item_id="20027"/><orderitem impose_pos="A3, B3, C3" order_id="20024" item_id="20028"/><orderitem impose_pos="A4, B4, C4" order_id="20026" item_id="20030"/></docket>	}

*/

}

?>