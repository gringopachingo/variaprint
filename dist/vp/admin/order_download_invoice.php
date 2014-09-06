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


	session_name("ms_sid");
	session_start();
	$ms_sid = session_id();

	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	require_once("../inc/encrypt.php");
	require_once("../inc/functions.php");
	require_once("inc/functions.php");
	require_once("inc/iface.php");
	require_once("inc/session.php");
	if (!$_SESSION["privilege_invoices"]) {
		require_once("inc/popup_log_check.php");
	}


	function invoice_csv($order_id) {
		
		// Get orderinfo
		$sql = "SELECT DateOrdered,OrderInfo FROM Orders WHERE ID='$order_id'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		
		$a_tree = xml_get_tree(decrypt($a_result['OrderInfo'],""));
		
		if ( is_array($a_tree[0]['children']) ) {
			foreach($a_tree[0]['children'] as $node) {
				$a[$node['attributes']['ID']] = $node['value'];
			}
		} 			
		
//		print_r($a);
		
		if ($a_result['DateOrdered'] != "") { $date = date("m/d/Y",$a_result['DateOrdered']); }
		
		//	$a contains the current order data -- cc, check, po & shipping
		if ( $a['shipping_region'] != "other" ) { 
			$a['shipping_country'] = $a['shipping_region'] ;
		} else {
			$a['shipping_country'] = $a['shipping_other_country'] ; 
		}
			
		
		// Loop thru items
		$sql = "SELECT ID,ItemID,Qty,Cost FROM OrderItems WHERE OrderID='$order_id'";
		$r_result = dbq($sql); 
		
		while ( $a_item = mysql_fetch_assoc($r_result) ) {
			$sql = "SELECT Name FROM Items WHERE ID='" . $a_item['ItemID'] . "'";
			$r_result2 = dbq($sql);
			$a_item2 = mysql_fetch_assoc($r_result2);
			$order_items .= "Item #: $a_item[ItemID] - $a_item2[Name]; Cost: $a_item[Cost]; Qty: $a_item[Qty]  |  ";		
			$subtotal += sprintf("%01.2f",$a_item['Cost']);
		}
		
		$subtotal = sprintf("%01.2f",$subtotal);
		$shipping_cost = sprintf("%01.2f",$a_order_info['shipping_cost']);
		$tax = sprintf("%01.2f",$a_order_info['tax']);
		$total = sprintf("%01.2f",$subtotal + $shipping_cost + $tax);
		
/*		$csv = "order_number,billing_type,billing_company,billing_name,billing_phone,billing_email,billing_card_type,billing_card_number";
		$csv .= ",billing_card_exp,billing_address1,billing_address2,billing_city,billing_state,billing_zip,billing_country,subtotal,shipping_cost,tax,total";
		$csv .= ",region_name,method_name,shipping_address1,shipping_address2,shipping_city,shipping_state,shipping_zip,shipping_country,shipping_method";
		$csv .= ",special_instructions,order_items";
*/		
		$csv .= "\"$order_id\",\"$date\",\"$a[billing_type]\",\"$a[billing_company]\",\"$a[billing_name]\",\"$a[billing_phone]\",\"$a[billing_email]\",\"$a[billing_card_type]\",\"$a[billing_card_number]\"";
		$csv .= ",\"$a[billing_card_exp]\",\"$a[billing_address1]\",\"$a[billing_address2]\",\"$a[billing_city]\",\"$a[billing_state]\",\"$a[billing_zip]\",\"$a[billing_country]\",\"$subtotal\",\"$shipping_cost\",\"$tax\",\"$total\"";
		$csv .= ",\"$a[shipping_country]\",\"$a[shipping_method]\",\"$a[shipping_address1]\",\"$a[shipping_address2]\",\"$a[shipping_city]\",\"$a[shipping_state]\",\"$a[shipping_zip]\",\"$a[shipping_country]\"";
		$csv .= ",\"$a[special_instructions]\",\"$order_items\"\r";
		
		return $csv;
	}





	$a_orders = array_find_key_prefix("checkbox_",$a_form_vars, true);
	
	
	if ($a_form_vars['action'] == "download") {
		
		if (is_array($a_orders)) {
			switch ($a_form_vars['method']) {
				case "browser" :
					$html = "
<html xmlns:v=\"urn:schemas-microsoft-com:vml\"
xmlns:o=\"urn:schemas-microsoft-com:office:office\"
xmlns:w=\"urn:schemas-microsoft-com:office:word\"
xmlns=\"http://www.w3.org/TR/REC-html40\">

<head>
<style>
<!--
 /* Style Definitions */
@page Section1
	{size:8.5in 11.0in;
	margin:.5in .5in .5in .5in;
	mso-header-margin:.5in;
	mso-footer-margin:.5in;
	mso-paper-source:0;}
div.Section1
	{page:Section1;}
-->
</style>
</head>
<body>
<div class=Section1>
";
					$fp = true;
					foreach ($a_orders as $id=>$v) {
						if (!$fp) $html .= "\n<br clear=ALL style='page-break-before:always'>\n";
						$fp = false;
						$html .= invoice($style="printable",$id);
					}
					$html .= "</div></html>
					<?	
						unlink(\"\$_SERVER[SCRIPT_FILENAME]\");
					?" . ">
					";
					//	htmlwin.document.write('" . htmlentities($html) . "');
					srand((double)microtime()*1000000); 
					$rand = rand(1000,999999999);
					$file_name = "tmp/inv" . $rand . ".php"; 
					
					$File = new File;
					if(!$File->write_file( $file_name, $html )) { 
						$error = 1; 
					} 
					
					print("
					<script language=\"JavaScript\" type=\"text/JavaScript\">
						htmlwin = window.open('$file_name','invoices','width=600,height=450,toolbar=1,resizable=1');
						top.close();
					</script>
					");
					
					exit();
					break;
				case "html" :
					$time = date("Y-m-d @ G.i", time());
					$file_out = "Invoice " . $time . ".doc";
					
					//<html><head>
					
					$html = "<" . "?xml version=\"1.0\" encoding=\"iso-8859-1\"?" . ">

<!-- This isn't really an xml file, but we want the browser to think so, so that it doesn't open it. //-->
<html xmlns:v=\"urn:schemas-microsoft-com:vml\"
xmlns:o=\"urn:schemas-microsoft-com:office:office\"
xmlns:w=\"urn:schemas-microsoft-com:office:word\"
xmlns=\"http://www.w3.org/TR/REC-html40\">

<head>
<style>
<!--
 /* Style Definitions */
@page Section1
	{size:8.5in 11.0in;
	margin:.5in .5in .5in .5in;
	mso-header-margin:.5in;
	mso-footer-margin:.5in;
	mso-paper-source:0;}
div.Section1
	{page:Section1;}
-->
</style>
</head>
<body>
<div class=Section1>
";					$fp = true;
					foreach ($a_orders as $id=>$v) {
						if (!$fp) 
						$html .= "

<span style='font-size:10.0pt;font-family:Times;mso-ansi-language:EN-US;
mso-fareast-language:EN-US'><br clear=ALL style='mso-special-character:line-break;
page-break-before:always'>
</span>

<p class=MsoNormal><![if !supportEmptyParas]>&nbsp;<![endif]><o:p></o:p></p>
						";
						$fp = false;

						$html .= "" . invoice($style="printable",$order_id=$id); 					}
					$html .= "</div></body></html>";
					
					srand((double)microtime()*1000000); 
					$rand = rand(1000,999999999);
					$file_in = "/tmp/inv" . $rand ; // $cfg_base_path . 
					
					$File = new File;
					if(!$File->write_file( $file_in, $html )) { 
						$error = 1; 
					} else {
						header("Location: download_file.php?file_in=$file_in&file_out=$file_out");
					}
					print("
					<script language=\"JavaScript\" type=\"text/JavaScript\">
						top.close();
					</script>
					");
					exit();
					break;
				
				case "csv" :
					$csv = "order_number,order_date,billing_type,billing_company,billing_name,billing_phone,billing_email,billing_card_type,billing_card_number";
					$csv .= ",billing_card_exp,billing_address1,billing_address2,billing_city,billing_state,billing_zip,billing_country,subtotal,shipping_cost,tax,total";
					$csv .= ",region_name,method_name,shipping_address1,shipping_address2,shipping_city,shipping_state,shipping_zip,shipping_country";
					$csv .= ",special_instructions,order_items\r";
		
					// loop through each order
					foreach ($a_orders as $id=>$v) {
						$csv .= invoice_csv($id) ;
					}
					/*$csv .= "
					<?	
						unlink(\"\$_SERVER[SCRIPT_FILENAME]\");
					?" . ">
					";*/
					
					$time = date("Y-m-d @ G.i", time());
					$file_out = "Invoice " . $time . ".csv";
					
					srand((double)microtime()*1000000); 
					$rand = rand(1000,999999999);
					$file_in = "/tmp/inv" . $rand ; // $cfg_base_path . 
					
					$File = new File;
					if(!$File->write_file( $file_in, $csv )) { 
						$error = 1; 
					} else {
						header("Location: download_file.php?file_in=$file_in&file_out=$file_out");
					}
					
					/*print("
					<script language=\"JavaScript\" type=\"text/JavaScript\">
						top.close();
					</script>
					");*/
						
					exit();
					break;
				
				case "xml" :
					// not an option, yet
				
			}
		}
	}
	
	
	
	
	
	if (is_array($a_orders)) {
		foreach($a_orders as $key=>$v) {
			$hidden .= "<input type=\"hidden\" name=\"checkbox_$key\" value=\"$v\">\n";
		}
	}
	
//	print_r($a_orders);
	

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?
print($header_content);
?>
<title>Download Invoices</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/bkg-groove.gif">
<span class="text">Select format: </span> 
<form name="form1" method="post" action="">
  <select name="method" class="text">
    <option value="browser">In Browser (with page breaks in IE)</option>
    <option value="html">MS Word Document</option>
    <option value="csv">MS Access, MS Excel, FileMaker Pro (CSV)</option>
  </select>
  <input name="Button" type="submit" class="text" value="Go">
  <input name="action" type="hidden"  value="download">

<? print($hidden); ?>

</form>
</body>
</html>
