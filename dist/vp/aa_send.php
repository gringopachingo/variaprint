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


	require_once("inc/config.php");
	require_once("inc/functions-global.php");
	require_once("inc/encrypt.php");

	session_name("mssid");
	session_start();

	//	require_once("inc/session.php");
	if ($a_form_vars['o'] != "1" && !$_SESSION["privilege_order_approval"]){
		require_once("inc/popup_log_check.php");
	}
	
	$a_orders = array_find_key_prefix("checkbox_",$a_form_vars, true);
	
	
	foreach($a_orders as $k=>$v) { 
		$hidden .= "<input type=\"hidden\" name=\"checkbox_$k\" value=\"1\">\n";
	}
	reset($a_orders);
	$send_msg = false;
	$action = $a_form_vars['action'];
	// Processing actions
	$fp = true;
	if (is_array($a_orders)) {
		foreach ($a_orders as $k=>$v) {
			if (!$fp) {
				$where .= " OR ";
			}
			$where .= " ID='$k' ";
			$fp =false;
		}
	}
	switch ($action) {
		case "send_approval" :
			// Change selected orders' status to approved
			$sql = "UPDATE Users SET Status='Active',ApprovalCode='',DateApproved='".time()."' WHERE $where";
			dbq($sql);
			reset($a_orders);
			
			$send_msg = true;
			break;
			
		case "send_cancel" :
			// Change selected accounts' status to denied
				
			$sql = "UPDATE Users SET Status='Denied',ApprovalCode='',DateCanceled='".time()."' WHERE $where";
			dbq($sql);
			reset($a_orders);
			
			$send_msg = true;
			break;
			
	}
	
	if ($send_msg) {

		foreach ($a_orders as $k=>$v) {
			$sql = "SELECT Messages,Email FROM Orders WHERE ID='$k'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			
			$to_email = $a_result['Email'];
			
			
			
			$message = $a_form_vars['message'];
			$sender_email = $a_form_vars['sender_email'];
			$subject = "Re: Your Order #$k";

			//Send message
			if ($to_email != "") {
				
				$headers  = "Return-Path: $sender_email\n";
				$headers .= "To: $to_email\n";
				$headers .= "MIME-version: 1.0\n";
				$headers .= "X-Mailer: VariaPrint Mailer\n";
				$headers .= "X-Sender: $sender_email\n";
				$headers .= "From: $sender_email\n";
				
				// and now mail it 
				mail($to_email, $subject, $message, $headers);
				
						
			}
			
			// Log message *********
			/*
			$a_messages = xml_get_tree($a_result['Messages']);
			$a_messages[0]['tag'] = "messages";
			$a_messages[0]['attributes']['LAST_UPDATED'] = time();
			
			$a_message["attributes"]["DATE_SENT"] = time();
			$a_message["attributes"]["SENT_TO"] = $to_email;
			$a_message["attributes"]["SENT_BY"] = $sender_email;
			$a_message["attributes"]["SUBJECT"] = $subject;
			$a_message["value"] = $message;
			$a_message["tag"] = "message";
			
			$a_messages[0]['children'][] = $a_message;
			
			$xml_messages = addslashes(xml_make_tree($a_messages));
			
			$sql = "UPDATE Orders SET Messages='$xml_messages' WHERE ID='$k'";
			dbq($sql);
			*/
		}
		
		exit("<script language=\"JavaScript\">
		top.opener.location.reload();
		top.close();
		</script>");
	} else {
	
		
		// First actions
		if ($action == "approve") {
			$title = "Approve Selected Buyer Accounts";
			$button = "Approve &amp; Send";
			$action = "send_approval";
			$prefill = "Your order site account has been approved. You may now login.";
		} else if ($action == "cancel") {
			$title = "Deny Selected Buyer Accounts";
			$button = "Deny &amp; Send"; 
			$action = "send_cancel";
			$prefill = "Your request for an order site account has been denied. ";
		}
		
		if (isset($a_form_vars['email'])) {
			$email = $a_form_vars['email'];
		} 
				
		$content = "
			<strong class=\"subhead\">$title</strong>
			<br><br>
		
			Your email address<br>
			<input type=\"text\" name=\"sender_email\" value=\"$email\"><br><br>
			
			Subject of message will be &quot;Re: Your Account Request&quot;<br><br>
			
			Message to Send to Customers<br>
			<textarea name=\"message\" cols=80 rows=10>$prefill</textarea><br><br>
			
			<input type=\"submit\" value=\"$button\">
			<input type=\"hidden\" name=\"action\" value=\"$action\">
			$hidden
		";
	}
	
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?
print($header_content);
?>
<title><? print($title); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="admin/style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/bkg-groove.gif">
<form name="form1" method="get" action="">
<span class="text">
  <?

print($content);

?>
<input type="hidden" name="o" value="<? print($a_form_vars[o]); ?>">
</span>
</form>
</body>
</html>
