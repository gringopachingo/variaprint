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



	function getSavedOrders() {	
		$sql = "SELECT * FROM SavedOrders WHERE UserID='$_SESSION[user_id]'";
		$r_result = dbq($sql);
		$num_saved = mysql_num_rows($r_result);
		
		if ($num_saved == 0) {
			return 0;
			
		} else {
			$haveorders = false;
			$line = iface_dottedline();	
			
			while ( $a_saved = mysql_fetch_assoc($r_result)) {	
				$sql = "SELECT * FROM Cart WHERE SavedID='$a_saved[ID]' AND SiteID='$_SESSION[site]'";
				$r_result2 = dbq($sql);
				
				if (mysql_num_rows($r_result2) == 0) {
					$sql = "DELETE LOW_PRIORITY FROM SavedOrders WHERE ID='$a_saved[ID]'";
					dbq($sql);
					
					
				} else {
					
					$haveorders = true;
					$row = "
						<tr>
							<td class=\"subtitle\">
								Order in progress 
							</td>
							<td class=\"text\" align=\"right\">
								<a href=\"vp.php?site=$_SESSION[site]&os_action=continuesavedorder&savedorderid=$a_saved[ID]&os_sid=$_SESSION[os_sid]\">continue order</a> &raquo;
							</td>
						</tr>
					";
					
					while ($a_cart = mysql_fetch_assoc($r_result2)) {
						$sql = "SELECT Name,Custom FROM Items WHERE ID='$a_cart[ItemID]' AND SiteID='$_SESSION[site]'";
						$r_result3 = dbq($sql);
						$a_name = mysql_fetch_assoc($r_result3);
						$this_name = $a_name['Name'];
						
						$row .= "<tr class=\"text\">
						<td width=\"250\" colspan=\"2\">
							$this_name &nbsp; &nbsp; ";
						
						if ($a_name["Custom"] != "N") { 
							$row .= "<a href=\"javascript:;\" onClick=\"popupWin('itempreview.php?site=$_SESSION[site]&cartitemid=$a_cart[ID]&name=" 
								. urlencode(addslashes($this_name)) . "&os_sid=$_SESSION[os_sid]','view','width=600,height=450,centered=1')\" title=\"View preview of item &quot;$name&quot;.\">preview</a>...";
						}
						
						$row .= "
						</td>
						</tr>";
					}

					$rows .= "
					<table cellpadding=0 cellspacing=8 border=0 width=\"500\">
					$row
					</table>
					$line
					";
				}
			}
			if ($haveorders) {	
				return $rows;
			} else {
				return 0;
			}
		}
	}
	
	
	function getUnprocessedOrders() {
		// add item (later), remove item, cancel, modify, reorder, preview

		$sql = "SELECT OrderStatuses FROM Sites WHERE ID='$_SESSION[site]'";
		$result = dbq($sql);
		$a_result = mysql_fetch_assoc($result);
		
		$a_tree = xml_get_tree($a_result['OrderStatuses']);
		
		if (is_array($a_tree[0]['children'])) {
			foreach($a_tree[0]['children'] as $node) {
				$id = $node['attributes']['ID'];
				$a_statuses[$id] = $node['attributes']['NAME'];
			}
		}
		
		
		$sql = "SELECT * FROM Orders WHERE UserID='$_SESSION[user_id]' AND Status<'40' AND SiteID='$_SESSION[site]'";
		$result = dbq($sql);
		
		$haverows = false;
		$line = iface_dottedline();	
					

		while ($a_order = mysql_fetch_assoc($result)) {
			
			$has_production_item = false;
			
			$sql = "SELECT * FROM OrderItems WHERE OrderID='$a_order[ID]'";
			$result2 = dbq($sql);
			
			while ($a_item = mysql_fetch_assoc($result2)) {
				$sql = "SELECT Name,Custom FROM Items WHERE ID='$a_item[ItemID]' AND SiteID='$_SESSION[site]'";
				$result3 = dbq($sql);
				$a_item_props = mysql_fetch_assoc($result3);
				$this_name = $a_item_props['Name'];
				
				$row .= "<tr>
					<td class=\"text\" colspan=\"2\">
						$this_name &nbsp; &nbsp; &nbsp; ";
						
				if ($a_item_props["Custom"] != "N") { 
				
					if ($a_item['Status'] == 30) { 
						$modify_link = "<a href=\"javascript:;\" onclick=\"alert('This item is already in production and cannot be modified.');\">modify</a> &raquo; &nbsp;";
					} elseif ($a_item['Status'] == 29) {
						$modify_link = "<a href=\"vp.php?site=$_SESSION[site]&os_action=modifyorderitem&moditemid=$a_item[ID]&os_sid=$_SESSION[os_sid]&in_prod=1\" onclick=\"return confirmAction(this,'This item has already been downloaded for production. You should contact us and make sure that it hasn\'t been completed. Do you still want to modify this item? ')\">modify</a> &raquo; &nbsp;";
					} else {
						$modify_link = "<a href=\"vp.php?site=$_SESSION[site]&os_action=modifyorderitem&moditemid=$a_item[ID]&os_sid=$_SESSION[os_sid]&in_prod=0\">modify</a> &raquo; &nbsp;";
					}
					$row .= "<a href=\"javascript:;\" onClick=\"popupWin('itempreview.php?site=$_SESSION[site]&cartitemid=$a_item[ID]&name=" 
						. urlencode(addslashes($this_name)) . "&os_sid=$_SESSION[os_sid]&mode=ordered','view','width=600,height=450,centered=1')\" title=\"View preview of item &quot;$name&quot;.\">preview</a>... &nbsp; 
							$modify_link
						 ";
				}
				
				if ($a_item["Status"] != 30) {
					if ($a_item["Status"] == 10) {
						$row .= "<a href=\"vp.php?site=$_SESSION[site]&os_action=restoreorderitem&removeitemid=$a_item[ID]&os_sid=$_SESSION[os_sid]\">restore</a> &raquo;";
					} else {
						$row .= "<a href=\"vp.php?site=$_SESSION[site]&os_action=removeorderitem&removeitemid=$a_item[ID]&os_sid=$_SESSION[os_sid]\">remove</a> &raquo;";
					}
				} else {
					$has_production_item = true;
				}
									
				$row .= "
					</td>
					<td class=\"text\">
					</td>
				</tr>
				";
			}
			
			$row_header = "<tr>
				<td>
					<span class=\"subtitle\">Order # $a_order[ID]</span> &nbsp; &nbsp; <span class=\"text\"><strong>Status: </strong>" . $a_statuses[$a_order["Status"]] . "</span>
				</td>
				<td class=\"text\" align=\"right\">";
				
			if ($a_order["Status"] > 15 && !$has_production_item) {  	
				$row_header .="		<a href=\"vp.php?site=$_SESSION[site]&os_action=cancelorder&cancelorderid=$a_order[ID]&os_sid=$_SESSION[os_sid]\">cancel</a> &raquo;&nbsp; ";
			}
			
			$row_header .="		<a href=\"vp.php?site=$_SESSION[site]&os_action=reorder&reorderid=$a_order[ID]&os_sid=$_SESSION[os_sid]\">reorder</a> &raquo;
				</td>
				</tr>
			";
			
			$row = $row_header.$row;
			
			$rows .= "
			<table cellpadding=0 cellspacing=8 border=0 width=\"590\">
			$row
			</table>
			$line
			";
			$row = "";
			$haverows = true;
		}
		
		if ($haverows){
			return $rows;
		} else {
			return 0;
		}
	}
	
	
	function getInProductionOrders() {
		$sql = "SELECT OrderStatuses FROM Sites WHERE ID='$_SESSION[site]'";
		$result = dbq($sql);
		$a_result = mysql_fetch_assoc($result);
		
		$a_tree = xml_get_tree($a_result['OrderStatuses']);
		
		if (is_array($a_tree[0]['children'])) {
			foreach($a_tree[0]['children'] as $node) {
				$id = $node['attributes']['ID'];
				$a_statuses[$id] = $node['attributes']['NAME'];
			}
		}
		
		
		$sql = "SELECT * FROM Orders WHERE UserID='$_SESSION[user_id]' AND Status>='40' AND Status<'50' AND SiteID='$_SESSION[site]'";
		$result = dbq($sql);
		
		$haverows = false;
		$line = iface_dottedline();	

		while ($a_order = mysql_fetch_assoc($result)) {
			$row = "<tr>
				<td>
					<span class=\"subtitle\">Order # $a_order[ID]</span> &nbsp; &nbsp; <span class=\"text\"><strong>Status: </strong>" . $a_statuses[$a_order["Status"]] . "</span>
				</td>
				<td class=\"text\" align=\"right\">";
					
			$row .="		<a href=\"vp.php?site=$_SESSION[site]&os_action=reorder&reorderid=$a_order[ID]&os_sid=$_SESSION[os_sid]\">reorder</a> &raquo;
				</td>
				</tr>
			";
			
			$sql = "SELECT * FROM OrderItems WHERE OrderID='$a_order[ID]'";
			$result2 = dbq($sql);
			
			while ($a_item = mysql_fetch_assoc($result2)) {
				$sql = "SELECT Name,Custom FROM Items WHERE ID='$a_item[ItemID]' AND SiteID='$_SESSION[site]'";
				$result3 = dbq($sql);
				$a_item_props = mysql_fetch_assoc($result3);
				$this_name = $a_item_props['Name'];
				
				$row .= "<tr>
					<td class=\"text\" colspan=\"2\">
						$this_name &nbsp; &nbsp; &nbsp; ";
						
				if ($a_item_props["Custom"] != "N") { 
					$row .= "<a href=\"javascript:;\" onClick=\"popupWin('itempreview.php?site=$_SESSION[site]&cartitemid=$a_item[ID]&name=" 
						. urlencode(addslashes($this_name)) . "&os_sid=$_SESSION[os_sid]&mode=ordered','view','width=600,height=450,centered=1')\" title=\"View preview of item &quot;$name&quot;.\">preview</a>... &nbsp; ";
				}
				
				$row .= "
					</td>
					<td class=\"text\">
					</td>
					</tr>
				";
			}
			
			$rows .= "
			<table cellpadding=0 cellspacing=8 border=0 width=\"590\">
			$row
			</table>
			$line
			";
			$haverows = true;
		}
		
		if ($haverows){
			return $rows;
		} else {
			return 0;
		}
	}
	
	function getFulfilledOrders() {
		$sql = "SELECT OrderStatuses FROM Sites WHERE ID='$_SESSION[site]'";
		$result = dbq($sql);
		$a_result = mysql_fetch_assoc($result);
		
		$a_tree = xml_get_tree($a_result['OrderStatuses']);
		
		if (is_array($a_tree[0]['children'])) {
			foreach($a_tree[0]['children'] as $node) {
				$id = $node['attributes']['ID'];
				$a_statuses[$id] = $node['attributes']['NAME'];
			}
		}
		
		
		$sql = "SELECT * FROM Orders WHERE UserID='$_SESSION[user_id]' AND Status>='50' AND SiteID='$_SESSION[site]'";
		$result = dbq($sql);
		
		$haverows = false;
		$line = iface_dottedline();	

		while ($a_order = mysql_fetch_assoc($result)) {
			$row = "<tr>
				<td>
					<span class=\"subtitle\">Order # $a_order[ID]</span> &nbsp; &nbsp; <span class=\"text\"><strong>Status: </strong>" . $a_statuses[$a_order["Status"]] . "</span>
				</td>
				<td class=\"text\" align=\"right\">";
					
			$row .="		<a href=\"vp.php?site=$_SESSION[site]&os_action=reorder&reorderid=$a_order[ID]&os_sid=$_SESSION[os_sid]\">reorder</a> &raquo;
				</td>
				</tr>
			";
			
			$sql = "SELECT * FROM OrderItems WHERE OrderID='$a_order[ID]'";
			$result2 = dbq($sql);
			
			while ($a_item = mysql_fetch_assoc($result2)) {
				$sql = "SELECT Name,Custom FROM Items WHERE ID='$a_item[ItemID]' AND SiteID='$_SESSION[site]'";
				$result3 = dbq($sql);
				$a_item_props = mysql_fetch_assoc($result3);
				$this_name = $a_item_props['Name'];
				
				$row .= "<tr>
					<td class=\"text\" colspan=\"2\">
						$this_name &nbsp; &nbsp; &nbsp; ";
						
				if ($a_item_props["Custom"] != "N") { 
					$row .= "<a href=\"javascript:;\" onClick=\"popupWin('itempreview.php?site=$_SESSION[site]&cartitemid=$a_item[ID]&name=" 
						. urlencode(addslashes($this_name)) . "&os_sid=$_SESSION[os_sid]&mode=ordered','view','width=600,height=450,centered=1')\" title=\"View preview of item.\">preview</a>... &nbsp; ";
				}
				
				$row .= "
					</td>
					<td class=\"text\">
					</td>
					</tr>
				";
			}
			
			$rows .= "
			<table cellpadding=\"0\" cellspacing=\"8\" border=\"0\" width=\"590\">
			$row
			</table>
			$line
			";
			$haverows = true;
		}
		
		if ($haverows){
			return $rows;
		} else {
			return 0;
		}
	}






// ***************************************************************************
	$title = $a_site_settings['AccountTitle'];
	$description = $a_site_settings['AccountText'];
	$os_sidebar = iface_make_sidebar($title, $description);
	
	$atabs = array('0' => 'Orders',  '2' => 'Profile');//'1' => 'Billing',
	
	if ( isset($_SESSION[accounttab]) ) { $ontab = $_SESSION[accounttab]; } else { $ontab = 0; }
	switch ($ontab) {
		case "0" : // Order history
			SecureServerOn(false);

			
			// MAKE MENU
		//	$a_order_types[0] = "All";
			$a_order_types[1] = "Saved";
			$a_order_types[2] = "Unprocessed";
			$a_order_types[3] = "In Production";
			$a_order_types[4] = "Fulfilled";
			
			if (!isset($_SESSION['ordertab'])) { $ontab2 = 1; } else { $ontab2 = $_SESSION['ordertab']; }
			
			$fp = true;
			foreach ($a_order_types as $k=>$type) {
				if (!$fp) { $menu .= " | " ; }
				if ($k == $ontab2) { $menu .= "$type" ; } else { $menu .= "<a href=\"vp.php?site=$_SESSION[site]&os_page=account&orderpage=1&ordertab=$k&os_sid=$_SESSION[os_sid]\">$type</a>" ; }
				$fp = false;
			}
			
			$rows = 0;
			// Get saved orders
			if ($ontab2 == 0) {
				$rows = getSavedOrders();
				
			} elseif ($ontab2 == 1) {
				$rows = getSavedOrders();
			
			} elseif ($ontab2 == 2) {
				$rows = getUnprocessedOrders();
				
				
			} elseif ($ontab2 == 3) {
				$rows = getInProductionOrders();
				// reorder, preview
			
			} elseif ($ontab2 == 4) {
				$rows = getFulfilledOrders();
				// reorder, preview
				
			
			}
			
			if ($rows === 0) {
				$rows = "<br>
					<table cellpadding=8 cellspacing=0 border=0 width=\"300\">
					<tr><td class=\"text\">
						<b class=\"text\">No previous orders in this status.</b>
					</td></tr>
					</table>
				";
			}
			
			
			$line = iface_dottedline();	
			
			$content = "
				<table cellpadding=8 cellspacing=0 border=0 width=\"300\">
				<tr>
				<td class=\"text\">
					$menu
				</td></tr>
				</table><img src=\"images/spacer.gif\" height=\"15\">
				$line
			
				$rows
			"; 
			break;
		
		case "1" :
			$content = "
				<table cellpadding=6 cellspacing=0 border=0 width=\"300\">
				<tr><td class=\"text\">No billing preferences to setup.</td></tr>
				</table>
			"; 
			break;

		case "2" :
			SecureServerOn(true);
			
			$sql = "SELECT * FROM Users WHERE ID='$_SESSION[user_id]' AND SiteID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$a_user = mysql_fetch_assoc($r_result);
			
			$content = "
				<input type=\"hidden\" name=\"action\" value=\"saveaccount\">
				
				<table cellpadding=6 cellspacing=0 border=0 width=\"594\">
				<tr><td class=\"text\">
				
					<table cellpadding=0 cellspacing=0 border=0 width=\"100%\">
						<tr>
							<td class=\"text\">Profile for: <strong>&quot;$_SESSION[username]&quot;</strong></td>
							<td align=\"right\"><input type=\"submit\" value=\"Save\" class=\"button\"></td>
						</tr>
					</table>
							
							<br>
					
					<div class=\"subtitle\">Change Password</div>
					<table cellpadding=0 cellspacing=0 border=0>
						<tr>
							<td class=\"text\">Old password<br><input type=\"password\" name=\"old_password\" style=\"width:280\"></td>
							<td> <img src=\"images/spacer.gif\" width=\"10\" height=\"35\"> </td>
							<td class=\"text\">New password<br><input type=\"password\" name=\"new_password\" style=\"width:135\"></td>
							<td class=\"text\">Verify new password<br><input type=\"password\" name=\"new_password2\" style=\"width:135\"></td>
						</tr>
					</table>
					<br><br>
					<div class=\"subtitle\">Contact Information</div>
					
					<table cellpadding=0 cellspacing=0 border=0>
						<tr>
							<td class=\"text\">First name<br><input value=\"$a_user[FirstName]\" type=\"text\" name=\"firstname\" style=\"width:280\"></td>
							<td> <img src=\"images/spacer.gif\" width=\"1\" height=\"35\"> </td>
							<td class=\"text\">Address<br><input value=\"$a_user[Address1]\" type=\"text\" name=\"address\" style=\"width:280\"></td>
						</tr>
						<tr>
							<td class=\"text\">Last Name<br><input value=\"$a_user[LastName]\" type=\"text\" name=\"lastname\" style=\"width:280\"></td>
							<td> <img src=\"images/spacer.gif\" width=\"1\" height=\"35\"> </td>
							<td class=\"text\">Address 2<br><input value=\"$a_user[Address2]\" type=\"text\" name=\"address2\" style=\"width:280\"></td>
						</tr>
						<tr>
							<td class=\"text\">Email<br><input value=\"$a_user[Email]\" type=\"text\" name=\"email\" style=\"width:280\"></td>
							<td><img src=\"images/spacer.gif\" width=\"1\" height=\"35\">  </td>
							<td>
								<table cellpadding=0 cellspacing=0 border=0>
									<tr>
										<td class=\"text\">City<br><input value=\"$a_user[City]\" type=\"text\" name=\"city\" style=\"width:140\"></td>
										<td class=\"text\">State<br><input value=\"$a_user[State]\" type=\"text\" name=\"state\" style=\"width:50\"></td>
										<td class=\"text\">Postal Code<br><input value=\"$a_user[Zip]\" type=\"text\" name=\"zip\" style=\"width:80\"></td>
									</tr>
								</table
							</td>
						</tr>
						<tr>
							<td class=\"text\">Phone<br><input value=\"$a_user[Phone]\" type=\"text\" name=\"phone\" style=\"width:280\"></td>
							<td> <img src=\"images/spacer.gif\" width=\"10\" height=\"35\">  </td>
							<td class=\"text\">Country<br><input value=\"$a_user[Country]\" type=\"text\" name=\"country\" style=\"width:280\"></td>
						</tr>
					</table>


				</td></tr>
				</table>
			"; 
			break;
	}
	
	$tabs = iface_make_tabs($atabs, $ontab, 'accounttab', '600');
	$content =  iface_make_box($content,600,100,0);
	$sPage = MakePageStructure($os_sidebar,$content,$tabs);


?>