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


function date_diff($date1, $date2) {
	$sec = $date2-$date1;
	$day=intval($sec/86400); 
	$sec-=$day*86400;
	$hour = intval($sec/3600);
	$sec -= $hour*3600;
	$min = intval($sec/60); 
	$sec -= $min*60;

	$day = round((strtotime(date("d F Y", $date2))-strtotime(date("d F Y", $date1)))/86400);
	return array("day"=>$day,"hour"=>$hour,"min"=>$min,"sec"=>$sec);
}


	$content .= "
	<script language=\"JavaScript\">
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
		function do_action(action) {
			var hash = ''
						
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
				alert('You must select at least one docket.')
			} else {
				if (action == \"completed\" || action == \"production\") {
					popupWin('docket_change_status.php?mssid=$mssid&status='+action+'&'+hash,'changedocketstatus','width=400,height=150,scrollbars=yes,resizable=yes,centered=1')
				} else {
					document.location='vp.php?mssid=$mssid&action=order_search_dockets&'+hash ;
				}	
			}
		
		}

		</script>
	";


	$submenu = "
	<a href=\"vp.php?action=order_list_impose&mssid=$mssid\">Create Impositions</a> 
	&nbsp; &nbsp; Download Dockets / Impositions
	&nbsp; &nbsp; <a href=\"javascript:;\" onClick=\"popupWin('itempreseteditors/item_presets_editor.php?edit=imposition','style','width=550,height=465,centered=1')\">Imposition Styles</a>...
	";
	$sel_menu = "impose";
	

/**/
	if ($_SESSION['privilege'] == "owner") {
		
		// [All]
		if ($_SESSION['sel_imp_vendor'] == "all") { $sel = "selected"; } else {$sel = ""; }
		$supplierMenu .= "<option value=\"all\" $sel>[All]</option>\n";
		
		// [Yourself]
		if ($_SESSION['sel_imp_vendor'] == "self") { $sel = "selected"; } else {$sel = ""; }
		$supplierMenu .= "<option value=\"self\" $sel>[Yourself]</option>\n";

		// List of vendors
		$sql = "SELECT VendorManagers FROM Sites WHERE ID='$_SESSION[site]'";
		$res = dbq($sql);
		$a_result = mysql_fetch_assoc($res);
		$a_vendors = xml_get_tree($a_result['VendorManagers']);
		if (is_array($a_vendors[0]['children'])) {
			foreach($a_vendors[0]['children'] as $vendor) {
				if ($vendor['attributes']['ORDER_DOWNLOAD_IMPOSITIONS'] != "true") {
					$disabled = "  (disabled)";
				} else {
					$disabled = "";
				}
				$user = $vendor['attributes']['EMAIL'];
				if ($_SESSION['sel_imp_vendor'] == $user) { $sel = "selected"; } else {$sel = ""; }
				$supplierMenu .= "<option value=\"".$user."\" $sel>".$user.$disabled."</option>\n";
			}
		}
		
		$supplierMenu = "
			<select name=\"sel_imp_vendor\" style=\"width: 130\" onChange=\"document.location='vp.php?sel_imposition=$_SESSION[sel_imposition]&sel_imp_vendor=' + this.value + '&mssid=$mssid''\">
			$supplierMenu</select>
		";
		if (!isset($_SESSION['sel_imp_vendor']) || $_SESSION['sel_imp_vendor'] == "all") {
			$whereVendor = "";
		} else if ($_SESSION['sel_imp_vendor'] == "self") {
			$whereVendor = "AND (VendorUsername='self' OR VendorUsername='') ";
		} else {
			$whereVendor = "AND VendorUsername='$_SESSION[sel_imp_vendor]'";
		}
	} else {
		$whereVendor = "AND VendorUsername='$_SESSION[username]'";
	}


	if ($_SESSION['sel_docket_view'] == "completed") {
		$completesel = "selected";
		$productionsel = "";
		$statusWhere = "AND Status='Completed'";		
		$button = "<input  class=\"text\" type=\"button\" onClick=\"do_action('production')\" value=\"Mark as &quot;In Production&quot;\">";
		$date2title = "Date Completed";
	} else {
		$completesel = "";
		$productionsel = "selected";
		$statusWhere = "";	
		$button = "<input  class=\"text\" type=\"button\" onClick=\"do_action('completed')\" value=\"Mark as &quot;Completed&quot;\">";
		$statusWhere = "AND Status!='Completed'";		
		$date2title = "Date Created";
	}

	$button = "<input  class=\"text\" type=\"button\" onClick=\"do_action('finddockets')\" value=\"View Orders\">
	&nbsp;&nbsp;$button";
		
			
	$sql = "SELECT * FROM Dockets WHERE SiteID='$_SESSION[site]' $statusWhere $whereVendor ORDER BY DateDue";
	$r_result = dbq($sql);
	$count = mysql_num_rows($r_result);
	$len = 0;
	
	if ($count == 0) {
		$button = "";
	}
	
	$onRowColor = "#E4E6EF";
	$offRowColor = "#FFFFFF";
	
	
	$content .= "
		<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
			<tr>
				<td width=\"180\">
					<span class=\"text\">Show dockets with status: </span><br>
<select name=\"sel_docket_view\" class=\"text\" onchange=\"document.location='vp.php?sel_docket_view='+this.value+'&mssid=$mssid'\" style=\"width: 120\">
<option value=\"production\" $productionsel>In Production</option>
<option value=\"completed\" $completesel>Completed</option>
</select>

				</td>
				<td  class=\"text\" valign=\"bottom\">
					<span class=\"text\">Show dockets for vendor: </span><br>
				$supplierMenu
				</td>
				<td  align=\"right\" valign=\"bottom\">
				$button
				</td>
			</tr>
		</table>
	";

	if ($count > 0) {
		
		
		$content .= "	
	  <table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		<tr>
		  <td height=\"30\" class=\"title\"><strong>Download Dockets / Impositions</strong></td>
		  <td height=\"30\" align=\"right\" class=\"text\">&nbsp;</td>
		</tr>
	  </table>

		<table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#E4E6EF\">
			<tr>
			  <td><img src=\"images/spacer.gif\" width=\"1\" height=\"2\"></td>
			</tr>
		</table>
		<table cellpadding=5 cellspacing=0 border=0 width=600>
			<tr bgcolor=\"$bgcolor\">
				<td width=\"2\" class=\"text\" align=\"center\">&#8730;&nbsp;<a href=\"javascript:;\" onclick=\"selectall()\" onfocus=\"this.blur()\">all</a></td>
				<td class=\"text\" width=\"20\"><strong>Docket&nbsp;#</strong></td>
				<td class=\"text\" width=\"75\"><strong>Priority</strong></td>
				<td class=\"text\"><strong>Date Due</strong></td>
				<td class=\"text\"><strong>$date2title</strong></td>
				<td class=\"text\" width=\"60\">&nbsp;</td>
				<td class=\"text\" width=\"60\">&nbsp;</td>
			</tr>
		
	";
	}
	
	while($a_result = mysql_fetch_assoc($r_result)){
		if ($len%2){
			$bgcolor = $offRowColor;
		} else {
			$bgcolor = $onRowColor;
		}
		
		if ($_SESSION['sel_docket_view'] == "completed") {
			$datedue = date("M j, Y",$a_result["DateDue"]);// 'y
			$date2 = date("M j, Y @ g:i a",$a_result["DateCompleted"]);// 'y
			
		} else {
			// DateDue - calculate business days until due
			$datedue = date("M j",$a_result["DateDue"]);// 'y
			$dateduediff = date_diff(time(), $a_result["DateDue"]);
			if ($dateduediff[day] < 0) {
				$datedue .= " &nbsp; <font color=\"red\">".abs($dateduediff[day])." days ago</font>";
			} else {
				$datedue .= " &nbsp; <font color=\"#888888\">In $dateduediff[day] days</font>";
			}
			
			if ($dateduediff[day] == -1) {
				$datedue = "<font color=\"red\">Yesterday</font>";
			} elseif ($dateduediff[day] == 0) {
				$datedue = "<font color=\"red\">Today</font>";
			} elseif ($dateduediff[day] == 1) {
				$datedue = "Tomorrow";
			}
			
			
	
			// DateCreated - calculate number of days since it was created
			$datecreated = date("M j",$a_result["DateCreated"]);// 'y
			$datecreateddiff = date_diff($a_result["DateCreated"], time());
			$datecreated .= " &nbsp; <font color=\"#888888\">$datecreateddiff[day] days ago</font>";
			if ($datecreateddiff[day] == 0) {
				$datecreated = "Today";
			} elseif ($datecreateddiff[day] == 1) {
				$datecreated = "Yesterday";
			}
			$date2 = $datecreated;
		}
		
		// Priority
		$priority = $a_result["Priority"];
		
		// Download imposition
		// &impose_0=20006&impose_1=20006&impose_2
		$cntr = 0;
		$hash = "";
		$a_imp = xml_get_tree($a_result[ImpositionLayout]);
		if(is_array($a_imp[0]['children'])){
			foreach($a_imp[0]['children'] as $page) {
				if(is_array($page['children'])) {
					foreach($page['children'] as $row){
						if (is_array($row['children'])){
							foreach($row['children'] as $item){
								$hash .= "&impose_".$cntr."=".$item['attributes']['ITEM_ID'];
								++$cntr;
							}
						}
					}
				}
			}
		}
		$page = NULL;
		
		if ($a_result['ImpositionID'] != 0) {
			$imposition = "<a href=\"imposition.php?imposition_id=".$a_result['ImpositionID'].$hash."\">Imposition</a>...";
		} else {
			$imposition = "";
		}
		
		// Download docket
		$docket = "<a href=\"javascript:;\" onClick=\"popupWin('docket.php?id=".$a_result["ID"]."','','width=780,height=550,centered=1,toolbar=1,scrollbars=1,resizable=1')\">Docket</a>...";
		
		$content .= "
			<tr bgcolor=\"$bgcolor\">
				<td class=\"text\"><input type=\"checkbox\" name=\"checkbox_".$a_result["ID"]."\" value=\"yes\"></td>
				<td class=\"text\">".$a_result["ID"]."</td>
				<td class=\"text\" width=\"75\">$priority</td>
				<td class=\"text\">$datedue</td>
				<td class=\"text\">$date2</td>
				<td class=\"text\" width=\"60\">$imposition</td>
				<td class=\"text\" width=\"60\">$docket</td>
			</tr>
		";
		++$len;
	}
	
	if ($count > 0) {
		$content .= "</table>";
	}
	
	
	
	
	
	
	if($count==0){
		$content .= "
		
		<br><br><span class=\"text\">No dockets found with this status.</span>";
	}

?>