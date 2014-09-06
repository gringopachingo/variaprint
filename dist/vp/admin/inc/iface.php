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

function iface_menu($site="",$user_id="",$tm="",$sel_menu="",$disabled=0) {
	global $ms_sid; 
	
 	$a_managers[1]['name'] = "setup";
 	$a_managers[1]['action'] = "site_appearance";
 	$a_managers[1]['width'] = 2;
	
	$a_managers[2]['name'] = "orders";
	$a_managers[2]['action'] = "order_view";
 	$a_managers[2]['width'] = 3;

//	$a_managers[3] = "order approval";
	
	$a_managers[4]['name'] = "users";
	$a_managers[4]['action'] = "users_list_browse";
 	$a_managers[4]['width'] = 2;
	
//	$a_managers[5]['name'] = "billing";
//	$a_managers[5]['action'] = "billing";
//	$a_managers[5]['width'] = 1;
	
	if ($site == "") {
		$disabled = 1;
		$tm = "";
	}
	
	$a_allow['users_list_poapprove'] = "privilege_user_poapprove";
	$a_allow['users_list_browse'] = "privilege_user_browse";
	$a_allow['order_list_approval'] = "privilege_order_approval";
	$a_allow['order_view'] = "privilege_order_browse";
	$a_allow['order_list_impose'] = "privilege_impositions";
	$a_allow['item_list'] = "privilege_items_browse";
	$a_allow['site_appearance'] = "privilege_site";
	
	$a_menu['site'] = "site_appearance"; 
	$a_menu['items'] = "item_list"; 
	$a_menu[] = "spacer";
	$a_menu['browse_orders'] = "order_view"; 
	$a_menu['impose'] = "order_list_impose"; 
	$a_menu['approve'] = "order_list_approval"; 
	$a_menu[] = "spacer";
	$a_menu['browse_users'] = "users_list_browse"; 
	$a_menu['po_approve'] = "users_list_poapprove"; 

	foreach ($a_menu as $key=>$action) { 
		if ($_SESSION['privilege'] == "owner") {
			if ($action == "spacer") {
				$menu .= "<td width=\"2\"><img src=\"images/menu-spacer.gif\" height=\"60\" border=\"0\"></td>\n";
	
			} elseif ( $key == $sel_menu ) {
				$referrer = $key;
				$menu .= "<td width=\"60\"><img src=\"images/btn-$key-on.gif\" height=\"60\" border=\"0\" title=\"Editing $key\"></td>\n";
			} elseif (!$disabled) {
				$menu .= "<td width=\"60\"><a href=\"vp.php?action=$action\">
							<img src=\"images/btn-$key-off.gif\" height=\"60\" border=\"0\" title=\"Edit $key\"></a></td>\n";
				
			} else {
				$menu .= "<td width=\"60\" background=\"images/menu-bkg.gif\"><img src=\"images/menu-bkg.gif\" height=\"60\" width=\"63\" border=\"0\" title=\"Edit $key. Disabled because there is no site open.\"></td>\n";
				// btn-$key-disabled
			}
		} elseif ($_SESSION['privilege'] == "slave") {
			if ($action == "spacer") {
				$menu .= "<td width=\"2\"><img src=\"images/menu-spacer.gif\" height=\"60\" border=\"0\"></td>\n";
	
			} elseif (!$disabled && $key == $sel_menu && $_SESSION[$a_allow[$action]] ) {
				$menu .= "<td width=\"60\"><img src=\"images/btn-$key-on.gif\" height=\"60\" border=\"0\" title=\"Editing $key\"></td>\n";
			} elseif (!$disabled && $_SESSION[$a_allow[$action]] ) {
				$menu .= "<td width=\"60\"><a href=\"vp.php?action=$action\">
							<img src=\"images/btn-$key-off.gif\" height=\"60\" border=\"0\" title=\"Edit $key\"></a></td>\n";
				
			} else {
				$menu .= "<td width=\"60\" background=\"images/menu-bkg.gif\"><img src=\"images/menu-bkg.gif\" height=\"60\" width=\"63\" border=\"0\" title=\"Edit $key. Disabled because there is no site open.\"></td>\n";
				// btn-$key-disabled
			}
			
		}
	}


	
	if ($site != "") {
		if ($_SESSION['site_status'] != "Live" && $_SESSION['site_status'] != "Inactive") {
			$sql = "SELECT Status FROM Sites WHERE ID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			$_SESSION['site_status'] = $a_result['Status'];
		}
	}
	
	
	$menubar = "
	  <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr bgcolor=\"#2C4781\"> 
	";

	$fp=true;	
	
	foreach ( $a_managers as $id=>$manager ) {
		$width = ($manager['width'] * 63) - 12;
		
		if (!$fp) {
			$menubar .=	" <td bgcolor=\"#959EB8\"><img src=\"images/spacer.gif\" width=\"2\" height=\"1\"></td> \n";	
		}
		$fp=false;	
		if ($tm == $id) {
			$menubar .=	"	
          <td bgcolor=\"#959EB8\"><img src=\"images/spacer.gif\" width=\"10\" height=\"1\"></td>
          <td nowrap bgcolor=\"#959EB8\" class=\"text\" width=\"$width\"><strong class=\"topmenu\">  $manager[name]  </strong></td>
          <td bgcolor=\"#959EB8\"><img src=\"images/spacer.gif\" width=\"10\" height=\"1\"></td>
			";
		} else {
			$action = $manager['action'];
			$menubar .=	"	
          <td><img border=\"0\" src=\"images/spacer.gif\" width=\"10\" height=\"1\"></td>
          <td nowrap class=\"topmenu\" width=\"$width\"><strong>$manager[name]</strong></td>
          <td align=\"right\"><img border=\"0\" src=\"images/spacer.gif\" width=\"10\" height=\"1\"></td>
			";
		}
		
		
	}
	// <a href=\"vp.php?action=$action&tm=$id\" class=\"topmenu\"> </a> 
	$menubar .=	"	
          <td align=\"right\"><img border=\"0\" src=\"images/tab-right-end.gif\" width=\"8\" height=\"18\"></td>
        </tr>
      </table>
	  
      <table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr>
          <td width=\"1\" align=\"left\" valign=\"top\"><img border=\"0\"  src=\"images/menu-end-left.gif\" width=\"5\" height=\"60\"></td>
		$menu
          <td align=\"right\" background=\"images/menu-bkg.gif\"><a href=\"javascript:;\" onClick=\"popupWin('reportbug.php?ref=$referrer','bug','width=450,height=450,centered=1')\"><img src=\"images/icon-bug.gif\" border=0></a></td>
          <td width=\"20\" align=\"right\" background=\"images/menu-bkg.gif\"><a href=\"javascript:;\" onClick=\"popupWin('help/index.php?page=$sel_menu','','centered=1,width=550,height=450')\"><img src=\"images/icon-cont_help.gif\" border=0></a></td>
          <td width=\"11\"><img src=\"images/menu-end.gif\" width=\"11\" height=\"60\"></td>
        </tr>
      </table>
	";
	return $menubar;
}


?>