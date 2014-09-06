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


	// This is the action that finalizes/places an order
	
	$_SESSION['os_page'] = "confirmorder";
	$order_approved = true;
	
	$sql = "SELECT OrderInfo FROM Sessions WHERE SessionID='$os_sid'";	
	$r_result = dbq($sql);
	$a_order_info_enc = mysql_fetch_assoc($r_result);
	$xml_order_info = $a_order_info_enc['OrderInfo'];
	
	$a_tree = xml_get_tree(decrypt($a_order_info_enc['OrderInfo'],""));
	
	if ( mysql_num_rows($r_result) > 0) {
		
		// Find the buyer and approval email
		if ( is_array($a_tree[0]['children']) ) {
			foreach($a_tree[0]['children'] as $node) {
				$a_order_info[$node['attributes']['ID']] = $node['value'];
				if (strtolower($node['attributes']['ID']) == "email") {
					$email = $node['value'];
				} elseif (strtolower($node['attributes']['ID']) == "approval_id") {
					$approval_id = $node['value'];
				}
			}
		}
		
		
		// See if we're using PayFlow Pro
		if ($a_site_settings['BillingCCUsePayFlow'] == "checked" && $a_order_info['billing_type'] == "cc") {
			require_once("inc/pfpro.php");
			$order_approved = false;
			
			$a_pfp = getPFP();
	
			
			$total = sprintf("%01.2f",$a_order_info['subtotal']+$a_order_info['tax']+$a_order_info['shipping_cost']);
			
			$transaction = array(
				'USER'    => $a_pfp[user],
				'PWD'     => $a_pfp[password],
				'PARTNER' => $a_pfp[partner],
				'VENDOR' => $a_pfp[vendor],
				'TRXTYPE' => 'A',
				'TENDER'  => 'C',
				'COMMENT1'  => 'Ordered through Prevario VariaPrint',
				'AMT'     => $total,
				'ACCT'    => $a_order_info['billing_card_number'],
				'EXPDATE' => date("my",strtotime($a_order_info['billing_card_exp_year']."-".$a_order_info['billing_card_exp_month']."-01")),
				'NAME' => $a_order_info['billing_card_holder'],
				'STREET' => $a_order_info['billing_address1'],
				'ZIP' => $a_order_info['billing_zip']
			); 
			
			$a_order_info['billing_card_number'] = substr($a_order_info['billing_card_number'],-4,4);
			
			$xml_order_info = "";
			foreach($a_order_info as $key=>$val) {
				$xml_order_info .= "<field id=\"$key\">$val</field>";
			}
			
			$xml_order_info = encrypt("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<order>$xml_order_info</order>","");
			
		//	print(decrypt($xml_order_info,""));
					
			if (trim($a_order_info['billing_csc']) != "") {
				$transaction['CVV2'] = trim($a_order_info['billing_csc']);
			}
			if (trim($a_pfp['comment']) != "") {
				$transaction['COMMENT2'] = $a_pfp['comment'];
			}
			
			if ($_SESSION['mode'] == "test") {
				$pfp_proc_server = "test-payflow.verisign.com";
				$mode = "test";
			} else {
				$pfp_proc_server = "payflow.verisign.com";
				$mode = "live";
			}
			
			$cc_result = pfpro_process($transaction,$pfp_proc_server);
			
			$refuse_trans = false;
			if ($cc_result['RESULT'] == "0") {
				if ($a_pfp['csc'] == "checked" && $cc_result['CVV2MATCH'] == "N") {
					$refuse_trans = true;
					$respmsg = "&bull; Incorrect card security code. Please double check.<br><br>";
				} 
				
				if ($a_pfp['avs'] == "checked") {
					if ($cc_result['AVSADDR'] == "N" || $cc_result['AVSZIP'] == "N" ||  $cc_result['IAVS'] == "N") {
						$refuse_trans = true;
						$respmsg .= "&bull; Incorrect address. Please check the address and zip/postal code.<br><br>";
					} 
				} 
			} else {
				$refuse_trans = true;
			}
	
			foreach($cc_result as $arg=>$val) {
				$res_xml .= "<$arg>$val</$arg>\n";
			}
			
			$res_xml .= "<MODE>$mode</MODE>";
			$pfp_result = addslashes("<?xml version=\"1.0\"?>\n<result>\n$res_xml\n</result>");
			
			if ($refuse_trans) {			
				$_SESSION['os_page'] = "checkout";
			
				$_SESSION['show_alert'] = true;
				$_SESSION['alert_msg'] = "<strong>There was an error processing your credit card.</strong><br><br>" . $respmsg;
				
				if ($cc_result['RESULT'] > 0) {
					$_SESSION['alert_msg'] .= "Credit card processor responded: $cc_result[RESPMSG]";
				} elseif ($cc_result['RESULT'] != 0)  {
					mail($cfg_admin_email,"Error processing credit card with PayFlow. ","Site $_SESSION[site]\n\n$pfp_result");
				}
				$order_approved = false;
			} else {
				$order_approved = true;
			}
					
			//header("Location: vp.php?os_page=checkout&os_sid=$_SESSION[os_sid]&site=$_SESSION[site]");//$_SESSION[page] = "checkout";
			//exit();
		}
		
		if ($order_approved) {
			// Notify approval manager
			$sql = "SELECT ApprovalManagers FROM Sites WHERE ID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			
			$a_approval_tree = xml_get_tree($a_result['ApprovalManagers']);
			
			if ( is_array($a_approval_tree[0]['children']) ) {
				foreach ( $a_approval_tree[0]['children'] as $manager) {
					$id = $manager['attributes']['ID'];
					$name = $manager['attributes']['NAME'];
					$app_email = $manager['attributes']['USERNAME'];
					if ($id == $approval_id) {  $approval_email = $app_email; break; }
				}
			}
					
			if (trim($approval_email) != "" && ereg("@",$approval_email) && ereg("\.",$approval_email)) {
				// create a unique id 
				srand((double)microtime()*1000000); $rand = rand(1000000,9999999999);
				$this_approval_id = $rand.time();
				
				$msg = "Dear Approval Manager,
				
There is a new order to approve from $email. Click on the link to view the proof(s).
https://{$cfg_secure_url}{$cfg_secure_dir}{$cfg_sub_dir}ao.php?id=$this_approval_id
	
Thank you.";
				
				// send email
				$to_email = $approval_email;
	
				$message = $msg;
				$sender_name = "VariaPrint";
				$sender_email = $cfg_system_from_email;
				
				$subject = "Auto Notice: Order needs approving";
				$headers  = "Return-Path: $sender_email\n";
				$headers .= "To: $to_email\n";
				$headers .= "MIME-version: 1.0\n";
				$headers .= "X-Mailer: Luke's Mailer\n";
				$headers .= "X-Sender: $sender_email\n";
				$headers .= "From: $sender_name <$sender_email>\n";
				
				// and now mail it 
				mail($to_email, $subject, $message, $headers);
			}
					
			
			// Notify any slaves ***************************************************************************
			$sql = "SELECT VendorManagers FROM Sites WHERE ID=$_SESSION[site]";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_array($r_result);
			
			$a_managers = xml_get_tree($a_result['VendorManagers']);
			
			$fp = true;
			$where = "";
			if (is_array($a_managers[0]['children'])) {
				foreach($a_managers[0]['children'] as $node) {
					// enable, , site, user_browse, [user_po_approve, user_po_notify], 
					// order_notify, order_browse, order_download_invoice, order_change_status, order_download_impositions, order_download_dockets, order_approve
					if ($node['attributes']['ORDER_NOTIFY'] == "true") {
						if (!$fp) $where .= " OR ";
						$fp = false;
						$where .= " Username='".$node['attributes']['EMAIL']."'";
					}
				}
			}
			$sql = "SELECT Name FROM Sites WHERE ID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			$sitename = $a_result["Name"];
			
			$message = "There is a new order on the \"$sitename\" print order site from $email. 

Go to https://{$cfg_secure_url}{$cfg_secure_dir}{$cfg_sub_dir}admin/ and login with your manager username and password to 
download. The order may need to be approved 
before it is ready for production.";
			$sender_name = "VariaPrint";
			$sender_email =  $cfg_system_from_email; 
			
			$subject = "Auto Notice: New Order";
			$headers  = "Return-Path: $sender_email\n";
//			$headers .= "To: $to_email\n";
			$headers .= "MIME-version: 1.0\n";
			$headers .= "X-Mailer: Luke's Mailer\n";
			$headers .= "X-Sender: $sender_email\n";
			$headers .= "From: $sender_name <$sender_email>\n";
	
			if ($where != "") {
				
				$sql = "SELECT Email FROM AdminUsers WHERE $where";
				$r_result = dbq($sql);
				while($a_user = mysql_fetch_assoc($r_result)) {
					// send email to $a_user[Email]
					$to_email = $a_user[Email];
					// and now mail it 
					mail($to_email, $subject, $message, $headers);
				}
			}
			
			// Notify master ***************************************************************************
			$sql = "SELECT MasterUID,NoticeOrder FROM Sites WHERE ID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			
			if ($a_result['NoticeOrder'] != "false") {
				$sql = "SELECT Email FROM AdminUsers WHERE ID='$a_result[MasterUID]'";
				$r_result = dbq($sql);
				$a_result = mysql_fetch_assoc($r_result);
				$to_email = $a_result[Email];
				
				// and now mail it 
				mail($to_email, $subject, $message, $headers);
			}
	
		
		
		
		
			// Create a new order in the Orders table ***************************************************************************
			if (isset($a_site_settings['InitialOrderStatus'])) {
				$init_status = $a_site_settings['InitialOrderStatus'];
			} else {
				$init_status = '20';
			}
			
			$sql = "INSERT INTO Orders SET 
				OrderInfo='" . addslashes($xml_order_info) . "', 
				Email='".addslashes($email)."',
				ApprovalCode='".addslashes($this_approval_id)."',
				ApprovalEmail='".addslashes($approval_email)."',
				DateOrdered='" . time() . "', 
				UserID='$_SESSION[user_id]', 
				SiteID='$_SESSION[site]', 
				PayType='".addslashes($a_order_info[billing_type])."', 
				PFPResult='".addslashes($pfp_result)."', 
				Status='".addslashes($init_status)."'";	
			dbq($sql);
			$order_id = $_SESSION['order_id'] = db_get_last_insert_id();
		
			
			// Move cart items into OrderItems
			$sql = "SELECT * FROM Cart WHERE SessionID='$os_sid' AND SiteID='$_SESSION[site]'";	
			$r_result = dbq($sql);
			
			
			while ( $a_result = mysql_fetch_assoc($r_result) ) {
				$sql = "SELECT Name FROM Items WHERE ID='$a_result[ItemID]' AND SiteID='$_SESSION[site]'";
				$r_result2 = dbq($sql);
				$a_item = mysql_fetch_assoc($r_result2);
				
				$sql = "INSERT INTO OrderItems SET 
					OrderID='$order_id',
					SiteID='$_SESSION[site]', 
					ItemName='".addslashes($a_item[Name])."', 
					ItemID='$a_result[ItemID]', 
					Qty='$a_result[Qty]', 
					Cost='$a_result[Cost]', 
					ApprovalInitials='" . addslashes($a_result[ApprovalInitials]) . "', 
					Imprint='" . addslashes($a_result[Imprint]) . "'";
				dbq($sql);
				
				$cart_id = db_get_last_insert_id();
				
				// Move cart files into the printing area
				$movefrom = $cfg_base_dir . "/_cartpreviews/" . $a_result[ID]  . "_preview_pdf.pdf";
				$moveto = $cfg_base_dir . "_orderpdfs/" . $cart_id  . "_preview_pdf.pdf";
				if ( file_exists($movefrom) ) {
					rename($movefrom, $moveto);
				}
				
				$movefrom = $cfg_base_dir . "/_cartpreviews/" . $a_result[ID]  . "_preview_raster.jpg";
				$moveto = $cfg_base_dir . "_orderpdfs/" . $cart_id  . "_preview_raster.jpg";
				if ( file_exists($movefrom) ) {
					rename($movefrom, $moveto);
				}
				 
				$movefrom = $cfg_base_dir . "/_cartpreviews/" . $a_result[ID]  . "_press_pdf.pdf";
				$moveto = $cfg_base_dir . "_orderpdfs/" . $cart_id  . "_press_pdf.pdf";
				if ( file_exists($movefrom) ) {
					rename($movefrom, $moveto);
				} 
			}
	
			// Delete items from cart for current session
			$sql = "DELETE FROM Sessions WHERE SessionID='$os_sid'";	
			dbq($sql);
			
			$sql = "DELETE FROM Cart WHERE SessionID='$os_sid' AND SiteID='$_SESSION[site]'";	
			dbq($sql);
		}
	
		//	session_destroy();
	}		
	
?>
