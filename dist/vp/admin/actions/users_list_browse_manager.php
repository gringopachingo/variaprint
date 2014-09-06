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


if (isset($a_form_vars['delete_user']) && $a_form_vars['confirmed'] == 1) {
	$sql = "SELECT VendorManagers FROM Sites WHERE ID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	
	$a_managers = xml_get_tree($a_result['VendorManagers']);
	
	if(is_array($a_managers[0]['children'])){
		foreach($a_managers[0]['children'] as $k=>$node){
			$username = $node[attributes][EMAIL];
			if ($username == $a_form_vars['delete_user']) {
//				print("found it.");
				array_splice($a_managers[0]['children'],$k,1);
				break;
			}
		}
	}
	$xml = addslashes(xml_make_tree($a_managers));
	$sql = "UPDATE Sites SET VendorManagers='$xml' WHERE ID='$_SESSION[site]'";
	dbq($sql);
	header("location: vp.php?action=users_list_browse_manager");
}


$_SESSION['tm'] = "4";

$content .= "
<script language=\"javascript\">
	function confirmAction (linkobj, msg) {
		is_confirmed = confirm(msg)
		
		if (is_confirmed) {	
			linkobj.href += '&confirmed=1'
		} 
		return is_confirmed
	}
	c = false
	function selectall() {
		el = document.forms[0].elements
		with (document.forms[0]) {
			for (var i = 0; i < elements.length; i++) {
				if ( elements[i].name.slice(0,9) == 'checkbox_') {
					if (c) {
						elements[i].checked = false
					} else {
						elements[i].checked = true
					}
				}
			}
		}
		if (c) { c = false } else { c = true } 
	}
	
</script>
";

if ($_SESSION["privilege"] == "owner") {
	$submenu = "<a href=\"vp.php?action=users_list_browse\">Order site accounts</a> &nbsp; &nbsp; Manager accounts";
}

$cfg_maxRecords = 10;
$sel_page = $_SESSION['page'];

if ( $sel_page == "" ) {  $sel_page = 1;  }
$startrecord = ($sel_page*$cfg_maxRecords)-$cfg_maxRecords;

// $options = "&action=item_list&user_id=$_SESSION[user_id]";

$sql = "SELECT VendorManagers FROM Sites WHERE ID='$_SESSION[site]'";
$r_result = dbq($sql);
$a_result = mysql_fetch_assoc($r_result);

//print($a_result['VendorManagers']);
	$sel_menu = "browse_users";
	$onRowColor = "#E4E6EF";
	$offRowColor = "#ffffff";
	$row_on = false;

	$content .= "
	<span class=\"text\">
		  <table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
			<tr>
			  <td height=\"20\" class=\"title\" valign=\"top\"><strong>Browse Manager Accounts</strong></td>
			  <td height=\"20\" align=\"right\" class=\"text\" valign=\"top\"><a href=\"javascript:;\" onclick=\"popupWin('add_manager.php','addmanager','width=520,height=360,centered=1,scrollbars=0,toolbar=0,resizable=0')\">Add Manager</a>...</td>
			</tr>
		  </table>
		  <table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#E4E6EF\">
			<tr>
			  <td><img src=\"images/spacer.gif\" width=\"1\" height=\"2\"></td>
			</tr>
		  </table>
	
	<table cellpadding=5 cellspacing=0 border=0 width=600>
		<tr bgcolor=\"$color\">
			<td width=\"200\" class=\"text\"> <strong>Username</strong> </td>
			<td>&nbsp;</td>
			<td width=\"20\">&nbsp;</td>
		</tr>
		<tr bgcolor=\"$onRowColor\">
			<td width=\"200\" class=\"text\"> <strong>$_SESSION[username]</strong> &nbsp;  [Yourself]</td>
			<td class=\"text\">
					<a href=\"javascript:;\" onclick=\"popupWin('notice_options.php?step=3&username=$username','addmanager','width=520,height=360,centered=1,scrollbars=0,toolbar=0,resizable=0')\">
					Notification options
					</a>...</td>
			<td width=\"20\">&nbsp;</td>
		</tr>
	";
	
	$a_managers = xml_get_tree($a_result['VendorManagers']);
//	print_r($a_managers);
	
	
	
	if(is_array($a_managers[0]['children'])){
		foreach($a_managers[0]['children'] as $node){
			if ($row_on) {
				$row_on = false;
				$bgcolor = $onRowColor;
			} else {
				$row_on = true;
				$bgcolor = $offRowColor;
			}
			$username = $node[attributes][EMAIL];
			$content .= "
			<tr bgcolor=\"$bgcolor\">
				<td width=\"200\" class=\"text\">$username</td>
				<td class=\"text\">
					<a href=\"javascript:;\" onclick=\"popupWin('add_manager.php?step=3&username=$username','addmanager','width=520,height=360,centered=1,scrollbars=0,toolbar=0,resizable=0')\">
						Access / Notification options</a>...
				</td>
			    <td width=\"20\">
					<a href=\"vp.php?action=users_list_browse_manager&delete_user=$username\" onclick=\"return confirmAction(this, 'WARNING: Deleting a manager cannot be undone.')\">
					<img src=\"images/icon-delete.gif\" border=0>
					</a>
				</td>
			</tr>
			";
//			print_r($node);
		}
	}
		
//			<td width=\"2\" class=\"text\" align=\"center\">&#8730;&nbsp;<a href=\"javascript:;\" onclick=\"selectall()\" onfocus=\"this.blur()\">all</a></td>
	$content .= "
	</table>	";


?>
