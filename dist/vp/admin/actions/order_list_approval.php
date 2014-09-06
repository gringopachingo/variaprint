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


$_SESSION['tm'] = "2";

$cfg_maxRecords = 20 ;

$sel_page = $_SESSION['page'];
if ( $sel_page == "" || is_array($sel_page)) {  $sel_page = 1;  }

$startrecord = ($sel_page*$cfg_maxRecords)-$cfg_maxRecords;

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
	
	function do_action(obj) {
		var hash = ''
		
		action_obj = document.forms[0].action_pd
		action = action_obj[action_obj.selectedIndex].value
		hash += 'action=' + action + '&'
		
		is_checked = false
		with (document.forms[0]) {
			for (var i = 0; i < elements.length; i++) {
				if (elements[i].name.slice(0,9) == 'checkbox_' && elements[i].checked == true) {
					is_checked = true
					hash += elements[i].name + '=' + escape(elements[i].value) + '&'
				}
			}
		}
		
		if (!is_checked) {
			alert('You must check at least one order before clicking Go.')
		} else {		
			popupWin('sendmessage.php?' + hash, 'message', 'width=550,height=400,scrollbars=yes,resizable=yes,centered=1')
		}
	}
	
</script>
";



// get the item names in an array
$sql = "SELECT Name,ID FROM Items WHERE SiteID='$_SESSION[site]'";
$r_result = dbq($sql);
while ( $a_result = mysql_fetch_assoc($r_result)) {
	$a_items[$a_result['ID']] = $a_result['Name'];
}

$sel_status = 20;



// select the orders with selected order status
$sql = "SELECT * FROM Orders WHERE SiteID='$_SESSION[site]' AND status='$sel_status' LIMIT $startrecord,$cfg_maxRecords";
$r_result = dbq($sql);

/*
print(mysql_num_rows($r_result));

while($a = mysql_fetch_assoc($r_result)){
	print("a\n");
	//print_r($a);
}
*/

// $sql = "SELECT * FROM Items WHERE SiteID='$_SESSION[site]' ORDER BY GroupID,Name ";
// $nResult = dbq($sql);

$sel_menu = "approve";

if (mysql_num_rows($r_result) == 0) {
	// 
	$content = "<span class=\"text\">No orders are waiting for approval.</span>";
	
} else {
	
	// Create page number selector
	$sql = "SELECT ID FROM Orders WHERE SiteID='$_SESSION[site]' AND status='$sel_status'";
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
	$offRowColor = "#FFFFFF";//"#F1F3FF";
	
//	$submenu = "<a href=\"javascript:;\" onClick=\"popupWin('','searchorders','width=450,height=250,centered=1')\">Search</a>...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	
	
	$content .= "
	
		<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
			<tr>
				<td width=300><br>
					<strong class=\"title\">
					Approve Orders</strong>
				</td>
				<td width=300 class=\"text\">With selected orders:<br> 
					<select name=\"action_pd\" class=\"text\">
						<option value=\"approve\">Approve</option>
						<option value=\"message\">Send message</option>
						<option value=\"cancel\">Cancel</option>
					</select>
					
					<input type=\"button\" value=\"Go\" onClick=\"do_action()\" class=\"text\">
				</td>
			</tr>
		</table>
		
		<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
			<tr>
			  <td height=\"30\" class=\"text\">&#8730;&nbsp;<a href=\"javascript:;\" onclick=\"selectall()\" onfocus=\"this.blur()\">all</a></td>
			  <td height=\"30\" align=\"right\" class=\"text\">$pageSelector</td>
			</tr>
		</table>
	";
	
	while ( $a = mysql_fetch_assoc($r_result) ) {
		
		// Get username
/*		$sql = "SELECT Username FROM Users WHERE ID='$a[UserID]'";
		$r_result3 = dbq($sql);
		$a_user = mysql_fetch_assoc($r_result3);
		$username = $a_user['Username'];
*/		
		$sql = "SELECT * FROM OrderItems WHERE OrderID='$a[ID]'";// AND Imprint!=''
		$r_result2 = dbq($sql);
		$c = 1;
		
		if(mysql_num_rows($r_result2) > 0){
	
			$groupid = $a[GroupID];
			$rowOn = 1;
			$groupname = ereg_replace(" ","&nbsp;",$aItemGroup[$groupid]);
			if ( strlen($a['Name']) > 35 ) { $name = substr($a['Name'], 0, 35) . "...";  } else { $name = $a['Name']; }
			$date = date("M d, Y", $a[DateOrdered]);
			
			$content .= "
			<table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#E4E6EF\">
				<tr>
				  <td><img src=\"images/spacer.gif\" width=\"1\" height=\"2\"></td>
				</tr>
			</table>
			
			<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
				<tr> 
					<td class=\"text\" width=\"2\">
						<input type=\"checkbox\" name=\"checkbox_$a[ID]\" value=\"yes\">
					</td>
			
					<td class=\"subhead\" width=\"150\">
						Order # <strong>$a[ID]</strong>
					</td>
								
					<td class=\"text\" colspan=2>
						Ordered on <strong>$date</strong>
					</td>			
					
				</tr>";
	//					by \"<a href=\"javascript:;\" onClick=\"alert('Not implemented')\">$username</a>\"
			
			while ( $a_order_items = mysql_fetch_assoc($r_result2) ) {
				if ($rowOn) { $color = $onRowColor; $rowOn = 0; } else {  $color = $offRowColor; $rowOn = 1; }
				
				$itemname = $a_order_items['ItemName'];
				
				if ($a_order_items["Imprint"] != "") {
					$link = "<a href=\"javascript:;\" onClick=\"popupWin('../_orderpdfs/" . $a_order_items['ID'] . "_preview_raster.jpg','','width=500,height=400,centered=yes')\">preview</a>...";	
				} else {
					$link = "not a custom item";
				}
				$content .= "
				<tr> 
					<td class=\"text\">&nbsp;</td>
		
					<td class=\"text\" colspan=\"3\" bgcolor=\"$color\">
						&bull; $itemname
					</td>
					<td class=\"text\" align=\"right\" bgcolor=\"$color\">
						$link
					</td>
				</tr>			
				";
				++$c;
			}
		/*				<!--&nbsp;&nbsp;&nbsp; 
						<a href=\"javascript:;\">edit</a> &raquo; &nbsp;&nbsp;&nbsp;
						<a href=\"javascript:;\">approve</a> &raquo; //-->
		*/	
				$content .= "
				
				";
			
			$content .= "
			</table><br><br>
			";
		}
	}
}
?>
