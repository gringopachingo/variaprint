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
	$submenu = "Order site accounts &nbsp; &nbsp; <a href=\"vp.php?action=users_list_browse_manager\">Manager accounts</a>";
}

$cfg_maxRecords = 20;
$sel_page = $_SESSION['page'];

if ( $sel_page == "" ) {  $sel_page = 1;  }
$startrecord = ($sel_page*$cfg_maxRecords)-$cfg_maxRecords;

// $options = "&action=item_list&user_id=$_SESSION[user_id]";

$sql = "SELECT * FROM Users WHERE SiteID='$_SESSION[site]'  LIMIT $startrecord,$cfg_maxRecords";
$r_result = dbq($sql);

if ($r_result == 0) {
	$sql = "SELECT * FROM Users WHERE SiteID='$_SESSION[site]'  LIMIT 0,$cfg_maxRecords";
	$r_result = dbq($sql);
	
	$sel_page = $_SESSION['page'] = 1; 
}

$sel_menu = "browse_users";

if (mysql_num_rows($r_result) <= 0) {

	$content = "<div class=\"text\"><strong>No users for this order site, yet. </strong>
	<br><br>
	If you want users to sign up for accounts, make sure that accounts are required in the site settings.</div>";

} else {
	$sql = "SELECT ID FROM Users WHERE SiteID='$_SESSION[site]'";
	$nTotalRecordsResult = dbq($sql);
	$totalRecords = mysql_num_rows($nTotalRecordsResult);
	$totalPages = round(($totalRecords/$cfg_maxRecords) + .4999999, 0);
	
	$pageIndicator = "page $sel_page of $totalPages ";
	$pageCounter = 0;
	
	if ($totalPages > 1) {
		$pageIndicator .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		while($pageCounter < $totalPages) {
			++$pageCounter;
			if ($pageCounter == $sel_page) { 
				$pageSelector .= " $pageCounter ";
				$nextpage = $pageCounter + 1;
				$prevpage = $pageCounter - 1;
			} else {
				$pageSelector .= " <a href=\"$_SERVER[SCRIPT_NAME]?page=$pageCounter$options\">$pageCounter</a> ";
			}
			
			if ($sel_page != 1) $prev = "&laquo; <a href=\"$_SERVER[SCRIPT_NAME]?page=$prevpage$options\">prev</a>&nbsp;&nbsp;";
			if ($sel_page != $totalPages) $next = "&nbsp;&nbsp;<a href=\"$_SERVER[SCRIPT_NAME]?page=$nextpage$options\">next</a> &raquo;";
		}
	}

	$pageSelector = $pageIndicator . $prev . $pageSelector . $next;
	
	$rowOn = 1;
	$onRowColor = "#E4E6EF";
	$offRowColor = "#ffffff";
	
	
	/*Show items with imposition style: </span><br>
	$pd
	*/
	
	
	$content .= "
	<span class=\"text\"><table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
			<tr>
			  <td height=\"20\" class=\"title\" valign=\"top\"><strong>Browse User Accounts</strong></td>
			  <td height=\"20\" align=\"right\" class=\"text\" valign=\"top\">$pageSelector</td>
			</tr>
		  </table>
		  <table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#E4E6EF\">
			<tr>
			  <td><img src=\"images/spacer.gif\" width=\"1\" height=\"2\"></td>
			</tr>
		  </table>
	
	
	<table cellpadding=5 cellspacing=0 border=0 width=600>
		<tr bgcolor=\"$color\">
		<!--	<td width=\"2\" class=\"text\" align=\"center\">&#8730;&nbsp;<a href=\"javascript:;\" onclick=\"selectall()\" onfocus=\"this.blur()\">all</a></td> //-->
			<td width=\"60\" class=\"text\"> <strong>Username</strong> </td>
			<td class=\"text\"><strong>Name</strong></td>
			<td width=\"60\" class=\"text\"><strong>Email</strong></td>
			<td class=\"text\"><strong>Phone</strong></td>
			<td class=\"text\"><strong>Address</strong></td>
			<td class=\"text\"><strong>Created</strong></td>
			<td class=\"text\"><strong>Last&nbsp;Login</strong></td>
		<!--	<td class=\"text\"><strong>Orders</strong></td> //-->
		</tr>";
	
	
	while ( $a = mysql_fetch_assoc($r_result) ) {
		if ($rowOn) { $color = $onRowColor; $rowOn = 0; } else {  $color = $offRowColor; $rowOn = 1; }
		$groupid = $a[GroupID];
		$groupname = ereg_replace(" ","&nbsp;",$aItemGroup[$groupid]);
		if ( strlen($a['Name']) > 35 ) { $name = substr($a['Name'], 0, 35) . "...";  } else { $name = $a['Name']; }
		$rowheight = "25";
		
		$address = "";
		if ($a[Address1] != "") $address = $a[Address1];  
		if ($a[Address2] != "" && $address != "") $address .= " &bull;  $a[Address2]";
		if (($a[City] != "" || $a[State] != "" || $a[Zip]) && $address != "") $address .= " &bull;";
		if ($a[City] != "" && $address != "") $address .= " $a[City],";
		if ($a[State] != "" && $address != "") $address .= " $a[State]";
		if ($a[Zip] != "" && $address != "") $address .= "  $a[Zip]"; 
		if ($a[DateLastLogin] == "") {  $DateLastLogin = "-";  } else { $DateLastLogin = date("m/d/y", $a[DateLastLogin]); }

		$content .= "
		<tr bgcolor=\"$color\">
		<!--	<td class=\"text\" width=\"2\" height=\"$rowheight\">
				<input type=\"checkbox\" name=\"checkbox_$a[ID]\" value=\"yes\">
			</td> //-->
			
			<td class=\"text\" nowrap height=\"$rowheight\">
				<strong>$a[Username]</strong>
			</td>
			
			<td class=\"text\" height=\"$rowheight\">
			$a[FirstName] $a[LastName]
			
	<!--		Name      Username      Email       Phone       Date Created        Last Login     Number of Orders Placed //-->
	
			</td>
			
			
			<td class=\"text\" height=\"$rowheight\" nowrap>
				<a href=\"mailto:$a[Email]\" title=\"Send mail to $a[Email]\">" . substr($a[Email],0,7) . "</a>...
			</td>
			
			<td class=\"text\" height=\"$rowheight\">
				$a[Phone]
			</td>

			<td class=\"text\" height=\"$rowheight\">
				 <span title=\"$address\">" . substr($address,0,16) . "...</span>
			</td>
			
			<td class=\"text\" height=\"$rowheight\">
			" .	date("m/d/y", $a[DateCreated]) . "
			</td>
					
			<td class=\"text\" height=\"$rowheight\">
			" . $DateLastLogin . "
			</td>
			
			
		<!--	<td class=\"text\" height=\"$rowheight\">
				$a[Orders]
			</td>//-->
	
		</tr>";
	}
			
	$content .= "</table>
	";
}

?>
