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
			alert('You must select at least one buyer account before clicking Go.')
		} else {		
			popupWin('aa_send.php?o=1&email='+document.forms[0].email.value+'&'+hash, 'message', 'width=600,height=400,scrollbars=yes,resizable=yes,centered=1')
		}
	}
	
</script>
";


$content .= "
<br><br>
	<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		<tr>
			<td width=300 valign=\"top\">
				<strong class=\"title\">
				Approve Buyer Accounts</strong>
			</td>
			<td width=300 class=\"text\" valign=\"top\">With selected accounts:  
				<select name=\"action_pd\" class=\"text\">
					<option value=\"approve\">Approve</option>
					<option value=\"cancel\">Deny</option>
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


// find the order that matches $a_form_vars[id]
$sql = "SELECT ApprovalEmail,SiteID FROM Users WHERE ApprovalCode='$a_form_vars[id]'";
$r_result = dbq($sql);
$a_result = mysql_fetch_assoc($r_result);

if (mysql_num_rows($r_result) < 1 || !isset($a_form_vars[id]) || trim($a_form_vars[id]) == "") {
	$content = "
		<p class=\"text\">There are no user accounts to approve or this code has already expired.</p>
	";
	
} else {

	$approvalemail = $a_result['ApprovalEmail'];
	$siteid = $a_result['SiteID'];
	
	
	// find all the orders with the same email address 
	$sql = "SELECT * FROM Users WHERE ApprovalEmail='$approvalemail' AND Status='WaitingForApproval' AND SiteID='$siteid'";
	$r_result = dbq($sql);
	
	
	// show a list of all the orders with option to approve, cancel, or send a message to
	while($a = mysql_fetch_assoc($r_result)) {
		
			if ( strlen($a['Username']) > 35 ) { $name = substr($a['Username'], 0, 35) . "...";  } else { $name = $a['Username']; }
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
			
					<td class=\"text\" width=\"150\">
						Username: <strong>$a[Username]</strong>
					</td>
					<td class=\"text\" colspan=\"2\">
						Date created: <strong>".date("M d, Y - h:i a", $a[DateCreated])."</strong>
					</td>			
				</tr>
				<tr> 
					<td class=\"text\">&nbsp;</td>
					<td class=\"text\" width=\"150\">
						Name: <strong>".$a[FirstName]." ".$a[LastName]."</strong>
					</td>
					<td class=\"text\" width=\"220\">
						Email: <strong><a href=\"mailto:".$a[Email]."\">".$a[Email]."</a></strong>
					</td>			
					<td class=\"text\">
						Phone: <strong>".$a[Phone]."</strong>
					</td>			
				</tr>			
			";
				
			$content .= "
			</table><br><br>
			";
	//	}
		
	}
}


?><html>
<head>
<title>Approve Orders</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="admin/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
<?
	print($content);
?>
<input type="hidden" name="email" value="<? print($approvalemail); ?>">
</form>
</body>
</html>
