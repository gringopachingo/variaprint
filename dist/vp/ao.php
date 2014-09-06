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

// ao.php - external order approval system

require_once("inc/config.php");
require_once("inc/functions-global.php");

// find the order that matches $a_form_vars[id]
$sql = "SELECT ApprovalEmail,SiteID,UserID FROM Orders WHERE ApprovalCode='$a_form_vars[id]'";
$r_result = dbq($sql);
$a_result = mysql_fetch_assoc($r_result);
$approvalemail = $a_result['ApprovalEmail'];
$siteid = $a_result['SiteID'];


$content .= "
<script language=\"javascript\">
	function popupWin(u,n,o) { // v3
		var ftr = o.split(','); 
		nmv=new Array(); 
		for (i in ftr) {
			x=ftr[i]; 
			p=x.split('=');
			t=p[0]; v=p[1]; 
			nmv[t]=v;
		}
		if (nmv['centered']=='yes' || nmv['centered']==1) {
			nmv['left']=(screen.width-nmv['width'])/2 ; 
			nmv['top']=(eval(screen.height-nmv['height']-72))/2 ; 
			nmv['left'] = (nmv['left']<0)?'0':nmv['left'] ; 
			nmv['top'] = (nmv['top']<0)?'0':nmv['top']; 
			delete nmv['centered'];
		}
		o=''; 
		var j=0; 
		for (i in nmv) {
			o+=i+'='+nmv[i]+'\,';
		} 
		o=o.slice(0,o.length-1);
		window.open(u,n,o);
	}

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
			popupWin('admin/sendmessage.php?o=1&site=$siteid&email='+document.forms[0].email.value+'&'+hash, 'message', 'width=550,height=400,scrollbars=yes,resizable=yes,centered=1')
		}
	}
	
</script>
";


$content .= "

	<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		<tr>
			<td width=300><br>
				<strong class=\"title\">
				Orders Waiting to be Approved </strong>
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


$rowOn = 1;
$onRowColor = "#E4E6EF";
$offRowColor = "#F1F3FF";//"#F1F3FF";



if (mysql_num_rows($r_result) < 1 || trim($a_form_vars["id"]) == "") {
	$content = "<p class=\"text\">There are no orders to approve or this code has already expired.</p>";	
} else {
	
	// find all the orders with the same email address where status = 20
	$sql = "SELECT ID,SiteID,DateOrdered,Messages FROM Orders WHERE ApprovalEmail='$approvalemail' AND SiteID='$siteid' AND Status='20'";
	$r_result = dbq($sql);
	
	
	// show a list of all the orders with option to approve, cancel, or send a message to
	while($a = mysql_fetch_assoc($r_result)) {
	
		// Get username
	//	$sql = "SELECT Username FROM Users WHERE ID='$a[UserID]'";
	//	$r_result3 = dbq($sql);
	//	$a_user = mysql_fetch_assoc($r_result3);
	//	$username = $a_user['Username'];
		
		$sql = "SELECT * FROM OrderItems WHERE OrderID='$a[ID]'";
		$r_result2 = dbq($sql);
		$c = 1;
		if (mysql_num_rows($r_result2) > 0){
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
		
			while ( $a_order_items = mysql_fetch_assoc($r_result2) ) {
				if ($rowOn) { $color = $onRowColor; $rowOn = 0; } else {  $color = $offRowColor; $rowOn = 1; }
				
				$itemname = $a_order_items['ItemName'];
				
				$content .= "
				<tr> 
					<td class=\"text\">&nbsp;</td>
		
					<td class=\"text\" colspan=\"3\" bgcolor=\"$color\">
						&bull; $itemname
					</td>
					<td class=\"text\" align=\"right\" bgcolor=\"$color\">
						<a href=\"#\" onClick=\"popupWin('secure/edititem.php?item_id=$a_order_items[ItemID]&site=$siteid&cartitemid=$a_order_items[ID]&os_sid=$_SESSION[os_sid]&mode=ordered','','width=650,height=550,scrollbars=1,resizable=1,status=1,centered=yes')\">edit</a>...&nbsp; 
						<a href=\"#\" onClick=\"popupWin('itempreview.php?itemid=$a_order_items[ItemID]&site=$siteid&cartitemid=$a_order_items[ID]&os_sid=$_SESSION[os_sid]&mode=ordered','','width=600,height=450,scrollbars=1,resizable=1,status=1,centered=yes')\">preview</a>... 
					</td>
				</tr>			
				";
				++$c;
			}
			$content .= "
			</table><br><br>
			";
		}
	}
}


?><html>
<head>
<title>Approve Orders</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="admin/style.css" rel="stylesheet" type="text/css">
</head>

<body><span class=text>
<strong>The orders below have not yet been approved for production.</strong><br><br>
<em>To approve these orders:</em><br>
1) Select the checkbox next to each order you want to approve<br>
2) In the menu under <strong>&quot;With selected orders&quot;</strong> choose <strong>&quot;Approve&quot;</strong> and
click <strong>&quot;Go&quot;</strong><br>
3) Enter a message and then click<strong> &quot;Approve &amp; Send&quot;</strong>. <font color="#FF0000"><strong><br>
Note:
Orders
will NOT be approved
unless you complete all  steps.</strong></font></span><font color="#FF0000"><strong>
</strong></font><br><br>
<form name="form1" method="post" action="">
<?
	print($content);
?>
<input type="hidden" name="email" value="<? print($approvalemail); ?>">
</form>
</body>
</html>
