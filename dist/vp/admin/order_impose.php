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


function xml_imposition($imp_id,$a_impose) {
//	global $a_form_vars;
	
	$rl[1] = "A";	$rl[2] = "B";	$rl[3] = "C";	$rl[4] = "D";
	$rl[5] = "E";	$rl[6] = "F";	$rl[7] = "G";	$rl[8] = "H";
	$rl[9] = "I";	$rl[10] = "J";	$rl[11] = "K";	$rl[12] = "L";
	$rl[13] = "M";	$rl[14] = "N";	$rl[15] = "O";	$rl[16] = "P";
	$rl[17] = "Q";	$rl[18] = "R";	$rl[19] = "S";	$rl[20] = "T";
	$rl[21] = "U";	$rl[22] = "V";	$rl[23] = "W";	$rl[24] = "X";
	$rl[25] = "Y";	$rl[26] = "Z";	$rl[27] = "AA";	$rl[28] = "AB";
	$rl[29] = "AC";	$rl[30] = "AD";	$rl[31] = "AE";	$rl[32] = "AF";
	$rl[33] = "AG";	$rl[34] = "AH";	$rl[35] = "AI";	$rl[36] = "AJ";
	$rl[37] = "AK";	$rl[38] = "AL";	$rl[39] = "AM";	$rl[40] = "AN";
	
	
	if (is_array($a_impose)) {
		foreach($a_impose as $item_id){
			if (!$already[$item_id]) {
				$sql = "SELECT OrderID FROM OrderItems WHERE ID='$item_id'";
				$r_result = dbq($sql);
				$a_result = mysql_fetch_assoc($r_result);
				$orderid = $a_result["OrderID"];

				$a_order_ids[$item_id] = $orderid;
			}
			$already[$item_id] = true;
		}
	}

	$sql = "SELECT * FROM Imposition WHERE ID='$imp_id'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	
	$a_tree = xml_get_tree($a_result['Definition']);
	
	if (is_array($a_tree[0]['children'])) {
		foreach($a_tree[0]['children'] as $node) {
			$val = $node['attributes']['VALUE'];
			$id = $node['attributes']['ID'];
			$a_imposition[$id] = $val;			
		}
	}

	$columns 		= $a_imposition['item_across'];
	$rows	 		= $a_imposition['item_down'];
	$row_cntr = 1;

	$pages = count($a_impose)/($rows*$columns);	
	$these_pages = $pages;
	$itemcntr = 0;
	while ($these_pages > 0) {
		$xml .= "<page>";
		$row_counter = 1;
		while ($row_counter <= $rows) {
			$col_counter = 1;
			$xml .= "<row>";
			while ($col_counter <= $columns) {
				if (isset($a_order_items[$a_impose[$itemcntr]])){
					$a_order_items[$a_impose[$itemcntr]] .= ", ";
				}
				$a_order_items[$a_impose[$itemcntr]] .= $rl[$col_counter].$row_cntr;
				$xml .= "<item position=\"".$rl[$col_counter].$row_cntr."\" order_id=\"".$a_order_ids[$a_impose[$itemcntr]]."\" item_id=\"". $a_impose[$itemcntr]. "\"/>";
				++$itemcntr;
				++$col_counter;
			}
			$xml .= "</row>";
			++$row_counter;
			++$row_cntr;
		}
		$xml .= "</page>";
		++$pagecntr;
		--$these_pages;
	}
	
	$a_xml["imp"] = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<imposition>$xml</imposition>";

$xml_doc = "";
if(is_array($a_order_items)){
	foreach($a_order_items as $order_item_id=>$positions) {
		if ($order_item_id != "") {
			$xml_doc .= "<orderitem impose_pos=\"$positions\" order_id=\"".$a_order_ids[$order_item_id]."\" item_id=\"". $order_item_id. "\"/>";
		}
	}
}
	$a_xml["doc"] = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<docket>$xml_doc</docket>";
	
	return $a_xml;
}







function layout_imposition($imp_id, $a_items, $run){

	
	$fp = true;
	if (is_array($a_items)) {
		foreach ($a_items as $k=>$v) {
			if (!$fp) { $where .= " OR " ; }
			$where .= " ID='$k' ";
			$fields .= "<input type=\"hidden\" name=\"checkbox_$k\" value=\"yes\">";
			$fp = false;
		}
		
		$sql = "SELECT * FROM OrderItems WHERE $where";
		$r_result = dbq($sql);
		// Now make an array 
		$cntr2 = 0;
		while ( $a_item = mysql_fetch_assoc($r_result) ) {
			/*
			$sql = "SELECT Name FROM Items WHERE ID='$a_item[ItemID]'";
			$r_result2 = dbq($sql);
			$a_result2 = mysql_fetch_assoc($r_result2);
			$item_name = $a_result2['Name'];
			*/
			$item_name = $a_item['ItemName'];
			
			if ($a_item[Qty] == "" || $a_item[Qty] < 1) { $a_item[Qty] = 1; }
			$times = $a_item[Qty]/$run;
			$this_time = $times;
	
			$cntr = 1;
			while ($this_time > 0) {
				if ($times > 1) { $ind = " [$cntr]"; $pre = "*"; } else { $ind = ""; $pre = ""; }
				if ( strlen($item_name) > 20 ) { $item_name = trim(substr($item_name, 0, 18)) . "...";  } 
				$a_impose[$cntr2]['name'] =  $a_item[OrderID] . $ind . "   -   $item_name" . $pre;// :: $item_name
				$a_impose[$cntr2]['id'] = "$a_item[ID]";
				$this_time--;
				$cntr++;
				$cntr2++;
			}
		}
		
		$cntr = 0;
		$cnt = count ($a_impose);	
		$fp = true;
		while ($cntr < $cnt) {
			foreach ($a_impose as $id=>$a_val) {
				if ($fp) {
					$extra_impose_pd .= "\n<option value=\"$a_val[id]\">$a_val[name]</option>";
				}
				if ($cntr == $id) $sel = " selected"; else $sel =""; 
				$a_impose_pd[$cntr] .= "\n<option value=\"$a_val[id]\"$sel>$a_val[name]</option>";
			}
			$fp = false;
			$a_impose_pd[$cntr] = "<select name=\"impose_$cntr\" class=\"text\">\n<option value=\"\"></option>\n$a_impose_pd[$cntr]\n</select>";
			reset($a_impose);
			++$cntr;
		}	
	
		$extra_impose_pd = "<option value=\"\"></option>" . $extra_impose_pd;
		
		// Get imposition settings
		$sql = "SELECT * FROM Imposition WHERE ID='$imp_id'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		
		$a_tree = xml_get_tree($a_result['Definition']);
		
		if (is_array($a_tree[0]['children'])) {
			foreach($a_tree[0]['children'] as $node) {
				$val = $node['attributes']['VALUE'];
				$id = $node['attributes']['ID'];
				$a_imposition[$id] = $val;			
			}
		}
	
	//	print("<br><br>...and here".$a_result['Definition']);
		
	//	print_r($a_tree);
		
		$columns 	= $a_imposition['item_across'];
		$rows	 	= $a_imposition['item_down'];
		if ($columns == 0 || $rows == 0) exit("Missing data for imposition. Please edit the imposition style and try again."); 
		
		if ($columns > 20) $columns = 20;
		if ($rows > 20) $rows = 20;
	
		$pages = count($a_impose)/($rows*$columns);
			
		
		$these_pages = $pages;
		$itemcntr = 0;
		$pagecntr = 1;
		
		// Loop through pages
		while ($these_pages > 0) {
			$content .= "
			<table cellpadding=10 cellspacing=5>
				<tr>
					<td colspan=\"$columns\"><strong class=\"text\">Page $pagecntr</strong></td>
				</tr>
			";
			
			// Loop through rows
			$these_rows = $rows;
			while ($these_rows > 0) {
				
				$content .= "<tr>\n"; // start row
				
				$these_columns = $columns;
				// Loop through columns
				while ($these_columns > 0) {
					if ( isset($a_impose_pd[$itemcntr]) ) { 
						$pd = $a_impose_pd[$itemcntr]; 
					} else { 
						$pd = "<select class=\"text\" name=\"impose_$itemcntr\">$extra_impose_pd\n</select>";
					}
					
					$content .= "<td height=\"50\" width=\"250\" bgcolor=\"#cccccc\">$pd</td>";
					
					++$itemcntr;
					--$these_columns;
					
				}
				
				$content .= "</tr>"; // end row
				--$these_rows;
			
			}
			
			$content .= "</table><br><br><br>"; // end table and add some space before next page
			++$pagecntr;
			--$these_pages;
		}
		
		$a_return["content"] = $content;
		$a_return["fields"] = $fields;
		return $a_return;
	} else {
		return false;
	}
}








	session_name("ms-sid");
	session_start();
	$ms_sid = session_id();

	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	require_once("../inc/encrypt.php");
	require_once("inc/functions.php");
	require_once("inc/iface.php");
	require_once("inc/session.php");
	
	if (!$_SESSION['privilege_impositions']) {
		require_once("inc/popup_log_check.php");
	}

	
	
		
		
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script language="JavaScript">
var page;
function doUnload() {
//	if (page=="2") {
	//	alert('The imposition file you just created has been discarded. There will be no record of it in the VariaPrint system.');
//	}
}
</script>
<?php
print($header_content);

$duedate = date("Y-m-d",time()+(60*60*24*14));
?>
<title>Download Imposition</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#eeeeee" background="images/bkg-groove.gif" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php

if(!isset($a_form_vars["action"])) {
	
	$a_items = array_find_key_prefix("checkbox_",$a_form_vars, true);
	
	$run = $a_form_vars[run];
	if ($run == "") {
		// change this to the lowest item qty, later
		$run = 500;
	}
	
	
	
	// Loop through each itemID and create a list of items to pull from orderitems
	if ( count($a_items) > 0 ) {
		
		$a_content = layout_imposition($a_form_vars[imposition_id], $a_items, $run);
				
		$content = $a_content["content"];
		$fields = $a_content["fields"]; 
		
	

?>
<table border="0" cellspacing="5" cellpadding="10">
  <tr>
    <td width="250">
<p class="titlebold">Impose Items</p></td>
    <td width="250" align="right" class="text"><form name="form1" method="get" action="">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td align="right" class="text"><input name="imposition_id" type="hidden" id="imposition_id" value="<?php print($a_form_vars[imposition_id]); ?>"> 
              <?php print($fields); ?>Change print run to &nbsp; </td>
            <td align="center"> <input name="run" type="text" class="text" id="run" value="<?php  print($run); ?>" size="5"></td>
            <td align="center"> <input name="Submit" type="submit" class="text" value="Go"></td>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
<form name="form2" method="get" action="">
<table cellpadding="0" cellspacing="10" border="0"><tr><td>
  <?php print($content); ?> 
  <input type="submit" name="Submit2" value="Create PDF Imposition">
  <input name="action" type="hidden" id="action" value="download">
  <input name="run" type="hidden" id="run" value="<?php print($run); ?>">
  <input name="imposition_id" type="hidden" id="imposition_id" value="<?php print($a_form_vars[imposition_id]); ?>">
</form>
</td></tr></table>
<?php

	} else {
		exit("none found.");
	}

} elseif ($a_form_vars[action] == "download") {// Check to see if we want to download
		$a_impose = array_find_key_prefix("impose_",$a_form_vars, true);
		
		foreach($a_impose as $k=>$id) {
			$hash .= "impose_$k=$id&";
		}
		
		$fp = true;
		foreach($a_impose as $k=>$id) {
			if (!$fp) $where.= " OR ";
			$fp = false;
			$where .= "ID='$id'";
		}
		$sql = "SELECT ItemID FROM OrderItems WHERE $where GROUP BY ItemID";
		$res = dbq($sql);
		$where = "";
		$fp = true;
		while ($a = mysql_fetch_assoc($res)) {
			if (!$fp) $where.= " OR ";
			$where .= "ID='$a[ItemID]'";
//			print("+");	
//			print_r($a);
			$fp = false;
		}
		
		$sql = "SELECT VendorUsername FROM Items WHERE $where GROUP BY VendorUsername";
		$res = dbq($sql);
		$vendorName = "self";
		if (mysql_num_rows($res) > 1) {
			$vendorStatus = "multiple";
		} elseif (mysql_num_rows($res) == 1) {
			$a = mysql_fetch_assoc($res) ;
			if ($a['VendorUsername'] == "self" || trim($a['VendorUsername']) == "") {
				$vendorStatus = "self";
			} else {
				$vendorStatus = "single";
				$vendorName = $a['VendorUsername'];
			}
		}
		$a_xml = xml_imposition($a_form_vars["imposition_id"],$a_impose);
		$xml_imp = $a_xml["imp"];
		$xml_doc = $a_xml["doc"];
		
		$hidden = "<input type=\"hidden\" name=\"xml_imp\" value=\"".urlencode($xml_imp)."\">\n";
		$hidden .= "<input type=\"hidden\" name=\"xml_doc\" value=\"".urlencode($xml_doc)."\">\n";

		// List of vendors
		$sql = "SELECT VendorManagers FROM Sites WHERE ID='$_SESSION[site]'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		$a_vendors = xml_get_tree($a_result['VendorManagers']);
		$supplierMenu = "";
		if (is_array($a_vendors[0]['children'])) {
			foreach($a_vendors[0]['children'] as $vendor) {
				if ($vendor['attributes']['ORDER_DOWNLOAD_IMPOSITIONS'] != "true") {
					$disabled = "  (disabled)";
				} else {
					$disabled = "";
				}
				$user = $vendor['attributes']['EMAIL'];
				
				if ($vendorName == $user) { $sel = "selected"; $msg = " (all items in imposition use this vendor)"; } else { $sel = ""; $msg = ""; }
				$supplierMenu .= "<option value=\"".$user."\" $sel>".$user.$disabled.$msg."</option>\n";
			}
		}
		if ($supplierMenu != "") { 
			$supplierMenu = "<select name=\"vendor_2\"><option>choose...</option>$supplierMenu</select>";
			$hideSupplierRadio = false;
		} else {
			$supplierMenu = "<em>[none set up]</em>";
			$hideSupplierRadio = true;
		}
		
/*

none or self -- display as-is
mixed vendors -- display warning at top of page
single vendor -- select "send notice" and vendor name in list with (vendor for all items)

master -- show "send notice" box
slave -- hide "send notice" box

*/
		
?>
<br>
<br>
<br>
<table width="440" height="99" border="0" align="center" cellpadding="10" cellspacing="2" bgcolor="#999999">
  <tr>
    <td bgcolor="#FFFFFF">
      <form name="form1" method="post" action="order_impose.php">
        <p><span class="title">&raquo; <a href="imposition.php?imposition_id=<?php print($a_form_vars[imposition_id] . "&"  . $hash ) ; ?>" class="title">Click
        here</a> to download imposition</span>&nbsp;&nbsp;&nbsp;&nbsp;          <span class="text">
          <input name="button2" type="button" onClick="history.go(-1)"value="Edit Layout">
          </span>
          <br>
          <br>
          <span class="text"><strong>Note: Do not edit the imposition file in
        Illustrator. This may lead to unexpected results.</strong></span></p>
        <p><span class="text"><strong>We recommend outputting PDF files from
              Acrobat 5 or later.</strong></span>
          <?php if ($vendorStatus == "multiple") { ?>
          <span class="text">        </span><br>
          </span><span class="text"><br>
          <strong><span class="text"><font color="#990000">WARNING: This
            imposition contains items from multiple vendors so you will need to
            download and send the PDF
              imposition file to your vendor manually or include it as
          an attachment in the vendor notification email. </font></span></strong> </span>	    </p>
        <p class="text"><strong><font color="#990000">Your manager account is
              the only account that will be able to access this  docket and
              imposition on the Download Dockets / Impositions page.
              <?php } ?>
        </font></strong></p>
        <hr size="1" noshade>
        <span class="titlebold"><strong>Create Docket</strong></span>        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="3" class="text">&nbsp;</td>
          </tr>
          <tr>
            <td height="30" colspan="3" class="text"><strong>Set imposition options
                (appears on docket):</strong></td>
          </tr>
          <tr>
            <td width="24%" height="30" class="text">Priority</td>
            <td width="5%">&nbsp;</td>
            <td width="71%" height="30"><select name="priority" id="priority">
                <option value="High">High</option>
                <option value="Standard" selected>Standard</option>
                <option value="Low">Low</option>
              </select>
            </td>
          </tr>
          <tr>
            <td height="30" class="text">Due date (YYYY-MM-DD)</td>
            <td>&nbsp;</td>
            <td height="30"><input name="datedue" type="text" id="datedue" value="<?php print($duedate); ?>">
            </td>
          </tr>
          <tr>
            <td height="25" colspan="3"><strong><span class="text">            </span></strong></td>
          </tr>
          <tr>
            <td height="30" colspan="3" class="text"><p><strong>
                <span class="text">
                <script language="JavaScript" type="text/JavaScript">
function checkedAllow(show) {
if (show) {
alert('Note: THIS IS NOT RECOMMENDED. Use only if you are showing the customer a proof and may need to have them modify the order after they review your proof. Generally, the proof a customer reviews when placing the order should be the final proof. If you select this option you MUST manually set the status of orders in this imposition to In Production BEFORE it is printed or the items will remain editable. You will receive an email notice if an item is modified. \n\nTIP: You can select all the orders in an imposition by going to the Download Dockets / Impositions page, selecting the dockets and clicking the View Orders button. You can then select all orders and change the status.')
}
}
                </script>
                </span>
                <input type="checkbox" name="allow_modify" value="1" onClick="checkedAllow(this.checked)">
                Allow customer(s) to  still modify items in this imposition </strong> [<a href="javascript:;" onClick="checkedAllow(true)">?</a>]<strong><br>
                </strong></p>
            </td>
          </tr>
          <tr>
            <td height="25" colspan="3">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td height="30" colspan="3" class="text">
			<?php
			 	if ($_SESSION['privilege'] == "owner") {
			 ?>
           
<table width="412" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="text"><strong>
      <input name="record_action" type="radio" value="1" <?php if ($vendorStatus != "single") print("checked"); ?>>
    </strong></td>
    <td class="text"><strong>Just record  this imposition in the imposition history</strong></td>
  </tr>
  <tr class="text">
    <td colspan="2">&nbsp;</td>
    </tr>
	<?php 
	if ($vendorStatus!="multiple") {
	?>
  <tr>
    <td width="24" class="text"><strong>
	<?php if (!$hideSupplierRadio) { ?>
      <input type="radio" name="record_action" value="2" <?php if ($vendorStatus == "single") print("checked"); ?>>
	  <?php } else { ?>
	  		&nbsp; -
	  <?php } ?>
</strong></td>
    <td width="388" class="text"><strong>Record and assign docket / imposition
        access to this vendor</strong></td>
  </tr>
  <tr>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td class="text"><table width="387" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="25" nowrap class="text"><?php print($supplierMenu); ?>&nbsp;</td>
          </tr>
        <tr>
          <td height="25" nowrap class="text"><input name="send_email_2" type="checkbox" id="send_email_2" value="1" checked>
Send email notice<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name="attach_2" type="checkbox" id="attach_2" value="1">
Attach PDF imposition file to email [<a href="javascript:;" onClick="alert('If the PDF file is not attached the vendor will need to log in and download the imposition from the Download Dockets / Imposition page.')">?</a>]</td>
          </tr>
      </table>
        </td>
  </tr>
  <tr class="text">
    <td colspan="2">&nbsp;</td>
    </tr>
	<?php
	}
	?>
  <tr>
    <td class="text"><strong>
      <input type="radio" name="record_action" value="3" <?php if ($vendorStatus == "single2") print("checked"); ?>>
</strong></td>
    <td class="text"><strong>Record and email the imposition to this supplier</strong></td>
  </tr>
  <tr>
    <td><strong><span class="text">
      <script language="JavaScript" type="text/JavaScript">
function checkedAttach(hide) {
	if (!hide) {
		alert('If the PDF file is not attached you will need to manually send the imposition file to the supplier.')
	}
}
                </script>
    </span></strong></td>
    <td class="text"><table width="100" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td height="25" nowrap class="text">Send to email&nbsp;</td>
        <td height="25"><input name="email_3" type="text" id="email_3" style="width:150" size="16">
</td>
      </tr>
    </table>
      <input name="attach_3" type="checkbox" id="attach_3" value="1" checked onChange="checkedAttach(this.checked)">
Attach PDF imposition file to email [<a href="javascript:;" onClick="checkedAttach(false)">?</a>]</td>
  </tr>
</table>			 
<?php
			 	} 
				
			  ?>
			  
<br>
<span class="text">
              <input name="button" type="button" onClick="if(confirm('This will discard the imposition file you just created. There will be no record of it in the VariaPrint&#8482; system. Are you sure you want to continue?')){top.close();}"value="Discard">
              <input name="submit" type="submit" value="Create and Record Docket">
              <?php print($hidden);  ?>
<input name="run" type="hidden" id="run" value="<?php print($a_form_vars["run"]); ?>">
<input name="action" type="hidden" id="action" value="record">
</span>
<input name="imposition_id" type="hidden" value="<?php print($a_form_vars['imposition_id']); ?>">
</p>
<input name="docket_owner" type="hidden" value="<?php print($vendorName); ?>">
</td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
<br>
<br>
<br>
<script language="JavaScript" type="text/JavaScript">
//document.location='imposition.php?imposition_id=<?php print($a_form_vars[imposition_id] . "&"  . $hash ) ; ?>' ;
</script>
<?php

} elseif ($a_form_vars["action"] == "record") {
	//print("recording...\n\n");
		
	//	print_r($a_form_vars);
	$suppress_display = true;
	require_once("imposition.php");
	require_once("docket.php");
	require_once("../inc/functions_pdf.php");
	
	
	function mail_imposition($sender_email,$to_email,$docket_id,$include_imp) {
		global $a_form_vars;
		// Get docket
		$docket = make_docket($docket_id);
		
		if ($include_imp) {
			// Get imposition
			$sql = "SELECT ImpositionLayout FROM Dockets WHERE SiteID='$_SESSION[site]' AND ID='$docket_id'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			$cntr = 0;
			$hash = "";
			$a_imp = xml_get_tree($a_result[ImpositionLayout]);
			if(is_array($a_imp[0]['children'])){
				foreach($a_imp[0]['children'] as $page) {
					if(is_array($page['children'])) {
						foreach($page['children'] as $row){
							if (is_array($row['children'])){
								foreach($row['children'] as $item){
									$a_impose[$cntr] = $item['attributes']['ITEM_ID'];
									++$cntr;
								}
							}
						}
					}
				}
			}
		//	$a_impose = array_find_key_prefix("impose_",$a_form_vars, true);
			$imposition = make_imposition($a_impose,$a_form_vars[imposition_id]);
			
			// Base64 encode imposition file
			$imp_attach_raw = base64_encode($imposition);
			$i = 0;
			$len = strlen($imp_attach_raw);
			while($i <= $len) {
				if ($len-$i < 72) {
					$thisLen = $len-$i;
				} else {
					$thisLen = 72;
				}
				$imp_attach .= substr($imp_attach_raw,$i,$thisLen) . "\n";
				$i+=72;
			}
		//	print ($len/72);
		//	print ($imp_attach);
		}
		
		$sql = "SELECT Email FROM AdminUsers WHERE Username='$_SESSION[username]'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		$sender_email = $a_result['Email'];

		
		// Format email
		$headers  = "Return-Path: $sender_email\n";
		$headers .= "To: $to_email\n";
		$headers .= "MIME-version: 1.0\n";
		$headers .= "X-Mailer: VariaPrint Mailer\n";
		$headers .= "X-Sender: $sender_email\n";
		$headers .= "From: $sender_email\n";
		$headers .= "Content-type: multipart/mixed; boundary=B_3142839170_6250039\n";


$message = "		
> This message is in MIME format. Since your mail reader does not understand
this format, some or all of this message may not be legible.

--B_3142839170_6250039
Content-type: multipart/alternative; boundary=\"B_3142839170_6190713\"


--B_3142839170_6190713
Content-type: text/plain; charset=\"US-ASCII\"
Content-transfer-encoding: 7bit

> This message is in HTML format. Since your mail reader does not understand
this format, some or all of this message may not be visible.

--B_3142839170_6190713
Content-type: text/html; charset=\"US-ASCII\"
Content-transfer-encoding: quoted-printable

$docket

--B_3142839170_6190713--

";

		if ($include_imp) {
$message .= "--B_3142839170_6250039
Content-type: application/octet-stream; name=\"imposition $docket_id.pdf\"
Content-disposition: attachment
Content-transfer-encoding: base64

$imp_attach
";

		$message .= "
--B_3142839170_6250039--";
			$subject = "Imposition file";
		} else {
			$subject = "Docket file";
			$message = "You will need to login to your manager account at http://{$cfg_secure_url}{$cfg_secure_dir}{$cfg_sub_dir}admin/ to download the imposition file.<br><br><br>" . $message;
		}
			
		
		// Send email
		$sentmail = mail($toemail,$subject,$message,$headers);
		
		if (!$sentmail) {
			print("error sending email to vendor. ");//$message
		}
		
	} // end mail_imposition
	
	
	
	
	$xml_imp = addslashes(urldecode($a_form_vars[xml_imp]));
	$xml_doc = addslashes(urldecode($a_form_vars[xml_doc]));
	$datecreated = time();
	$datedue = strtotime($a_form_vars['datedue']);
	$priority = $a_form_vars['priority'];
	$allow_modify = $a_form_vars['allow_modify'];
	$mail = false;
	if ($a_form_vars["record_action"] == "2") {
		$docket_owner = $a_form_vars['vendor_2'];
		if ($a_form_vars['send_email_2'] == 1) {
			$attach = false;
			if ($a_form_vars['attach_2'] == 1) {
				$attach = true;
			}
			$mail = true;
		}
	} elseif ($a_form_vars["record_action"] == "3") {
		$docket_owner = "self";
		$attach = false;
		if ($a_form_vars['attach_3'] == 1) {
			$attach = true;
		}
		$to_email = $a_form_vars["email_3"];
		$mail = true;
	}
	if ($_SESSION['privilege'] == "slave") {
		$docket_owner = $_SESSION['username'];
	}
	
	$from_email = $cfg_system_from_email; 
	
	$sql = "INSERT INTO Dockets SET
		SiteID='$_SESSION[site]',
		VendorUsername='$docket_owner',
		DateCreated='$datecreated',
		DateDue='$datedue',
		Priority='$priority',
		PressRun='$a_form_vars[run]',
		ImpositionID='$a_form_vars[imposition_id]',
		ImpositionLayout='$xml_imp',
		OrderItems='$xml_doc'
	";
	dbq($sql);
	$docket_id = db_get_last_insert_id();
	if ($mail) { mail_imposition($from_email,$to_email,$docket_id,$attach); }
	if ($a_form_vars['allow_modify'] == 1) {
		$status = 29;
	} else {
		$status = 30;
	}
	$a_order_items = xml_get_tree(urldecode($a_form_vars[xml_doc]));
	if (is_array($a_order_items[0]['children'])) {
		$fp = true;
		foreach($a_order_items[0]['children'] as $order_item) {
			if (!$fp){
				$where .= " OR ";
			}
			$fp = false;
			$where .= " ID='".$order_item['attributes']['ITEM_ID']."' ";
		}
		$sql = "UPDATE OrderItems SET Status='$status' WHERE $where";
		dbq($sql);
		if ($status==30) {
			$sql = "SELECT OrderID FROM OrderItems WHERE $where GROUP BY OrderID";
			$res = dbq($sql);
			$fp = true;
			$where = "";
			while ($a = mysql_fetch_assoc($res)) {
				if (!$fp){
					$where .= " OR ";
				}
				$fp = false;
				$where .= " ID='$a[OrderID]' ";
			}
			$sql = "UPDATE Orders SET Status='40' WHERE $where";
			dbq($sql);
		}
	}
	
	
	
	?>
	
<script language="JavaScript">
top.opener.top.location.reload();
</script>	
<br>
<br>
<br>
<table width="440" height="99" border="0" align="center" cellpadding="10" cellspacing="2" bgcolor="#999999">
  <tr>
    <td bgcolor="#FFFFFF">      <span class="text">
		A docket has been created. <a href="docket.php?id=<?php print($docket_id); ?>">Click here</a> to view it.
		<br>
        <br>
	   The docket and imposition file can also be accessed later under "Download&nbsp;Dockets
	   / Impositions" in the &quot;Impose&quot; submenu.
        </span>
		<br>
		<br>
		<input type="button" value="Close" onClick="top.close()">
    </td>
  </tr>
</table>
	
	<?php
}// endif
	
?>
