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

	
	session_name("mssid");
	session_start();
	$mssid = session_id();

	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	require_once("../inc/encrypt.php");
	require_once("inc/functions.php");
	require_once("../inc/pfpro.php");

	if (!$_SESSION['privilege_invoices']) {
		require_once("inc/popup_log_check.php");
	}
	
	// Get the order from DB
	$sql = "SELECT * FROM Orders WHERE ID='$a_form_vars[id]' AND SiteID='$_SESSION[site]'";
	$res = dbq($sql);
	$a_order = mysql_fetch_assoc($res);
	
	$a_tree = xml_get_tree(decrypt($a_order['OrderInfo'],""));
	
	
	// Find the buyer and approval email
	if ( is_array($a_tree[0]['children']) ) {
		foreach($a_tree[0]['children'] as $node) {
			$a_order_info[$node['attributes']['ID']] = $node['value'];
		}
	}
	
	
	if ($a_form_vars["action"] == "save") {
		
		if ($a_form_vars["settoshipped"] == "true") {
			$sql_frag = ", Status='50' ";
		}
		
		if ($a_form_vars["type"] == "rec") {
			
			$sql = "UPDATE Orders SET BilledStatus='Received'$sql_frag WHERE ID='$a_form_vars[id]' AND SiteID='$_SESSION[site]'";
			dbq($sql);
			
			print("<script language=\"JavaScript\">
				top.opener.location.reload(false);
				top.close();
			
			</script>");
			
		} elseif ($a_form_vars["type"] == "cf")  {
			// Do the delayed capture
			$a_auth_res = xml_get_tree($a_order["PFPResult"]);
			
			if (is_array($a_auth_res[0]['children'])) {
				foreach($a_auth_res[0]['children'] as $node) {
					$a_auth[$node['tag']] = $node['value'];
				}
			}
			
			$a_pfp = getPFP();
			$transaction = array(
				 'USER'    => $a_pfp['user'],
				 'PWD'     => $a_pfp['password'],
				 'PARTNER' => $a_pfp['partner'],
				 'VENDOR' =>  $a_pfp['vendor'],
				 'TENDER' =>  'C',
				 'TRXTYPE' => 'D',
				 'ORIGID'  => $a_auth['PNREF']
			);
			
			if ($a_auth['MODE'] == "live") {
				$pfpro_server = "payflow.verisign.com";
			} else {
				$pfpro_server = "test-payflow.verisign.com";
			}
			
			$cc_result = pfpro_process($transaction,$pfpro_server);
			
			
			if ($cc_result['RESULT'] == "0") {
				foreach($cc_result as $arg=>$val) {
					$res_xml .= "<$arg>$val</$arg>\n";
				}
				$pfp_result = addslashes("<?xml version=\"1.0\"?>\n<result>\n$res_xml\n</result>");
	
				$sql = "UPDATE Orders SET BilledStatus='Received',PFPResult2='$pfp_result'$sql_frag WHERE ID='$a_form_vars[id]' AND SiteID='$_SESSION[site]'";
				dbq($sql);
				print("<script language=\"JavaScript\">
					top.opener.location.reload(false);
					top.close();
				
				</script>");
			} else {
				$content = "<span class=\"text\">There was an error capturing funds ($cc_result[RESULT]). $cc_result[RESPMSG]<br><br>
				<input type=\"submit\" value=\"Cancel\" onClick=\"top.close()\">
				<input type=\"submit\" value=\"Mark Funds as Received\">
				</span>";
				
				$a_form_vars[type] = "rec";
			}
			
		}
		
	} else {
	
		
		
		
	//	print('test'.mysql_num_rows($res));
	//	print_r($a_order);
		
		if ($a_order['Status'] != "50") {
			// show " option
			$content .= "<input type=\"checkbox\" value=\"true\" name=\"settoshipped\"> <span class=\"text\">Set status to shipped.</span>
			<br><br>
			";
		}
		
		$total = sprintf("%01.2f",$a_order_info['subtotal']+$a_order_info['tax']+$a_order_info['shipping_cost']);
		$content .= "<span class=text>Order total: <b>$".$total."</b></span><br><br>";
		$content .= "<input type=\"button\" value=\"Cancel\" onClick=\"top.close()\">";
		
		if ($a_form_vars['type'] == "cf") {
			// capture funds
			$content .= "<input type=\"submit\" value=\"Capture Funds\">";
			//<input type=\"submit\" value=\"Funds Already Captured\">
		} elseif ($a_form_vars['type'] == "rec") {
			// funds received
			$content .= "<input type=\"submit\" value=\"Payment Received\">";
		}
	}
	
?><html>
<head>
<title>Finalize Order</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body><table width="311" height="96%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="260"><form action="finishorder.php" method="post">
      <?php print($content); ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="type" value="<?php print($a_form_vars[type]); ?>">
      <input type="hidden" name="id" value="<?php print($a_form_vars[id]); ?>">
      <input type="hidden" name="amt" value="<?php print($total); ?>">
    </form></td>
  </tr>
</table>
</body>
</html>
