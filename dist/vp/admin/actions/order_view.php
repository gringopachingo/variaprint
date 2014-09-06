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

$form_method = "post";

$cfg_maxRecords = 10 ;

$sel_page = $_SESSION['page'];
if ( $sel_page == "" || !isset($sel_page) || is_array($sel_page)) {  $sel_page = 1;  }
$startrecord = (intval($sel_page)*$cfg_maxRecords)-$cfg_maxRecords;
$sel_menu = "browse_orders";

$today = date("Y/m/d",time());
$lastmonth = date("Y/m/d",time() - 2592000) ;

if (!isset($_SESSION[ordersearch_date1_from])) {
	$_SESSION[ordersearch_date1_from] = $lastmonth;
}
if (!isset($_SESSION[ordersearch_date1_to])) {
	$_SESSION[ordersearch_date1_to] = $today;
}


$content .= "
<input type=\"hidden\" name=\"init_search\" value=\"1\">

<script language=\"JavaScript\" type=\"text/JavaScript\">
statusSel = true;
paymentSel = false;

function statusSelect() {
	if (statusSel) { statusSel = false; mode = true; } else { statusSel = true; mode = false; }
	document.forms[0].status_cancelled.checked = mode;
	document.forms[0].status_cancelledbybuyer.checked = mode;
	document.forms[0].status_cancelledbyapproval.checked = mode;
	document.forms[0].status_waiting.checked = mode;
	document.forms[0].status_hold.checked = mode;
	document.forms[0].status_ready.checked = mode;
	document.forms[0].status_inprod.checked = mode;
	document.forms[0].status_shipped.checked = mode;
}

function paymentSelect() {
	if (paymentSel) { paymentSel = false; mode = true; } else { paymentSel = true; mode = false; }
	document.forms[0].po.checked = mode;
	document.forms[0].pp.checked = mode;
	document.forms[0].cc.checked = mode;
	document.forms[0].check.checked = mode;
	
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
	
	function do_action (obj) {
		var hash = ''
		val = obj[obj.selectedIndex].value
		is_checked = false
		with (document.forms[0]) {
			for (var i = 0; i < elements.length; i++) {
				if (elements[i].name.slice(0,9) == 'checkbox_' && elements[i].checked == true) {
					is_checked = true
					hash += elements[i].name + \"=\" + escape(elements[i].value) + \"&\"
				}
			}
		}
		
		if (!is_checked) {
			alert('You must check at least one order before clicking Go.')
		} else {		
			if (val == \"invoice\") {
				popupWin('order_download_invoice.php?' + hash,'downloadinvoices','width=350,height=100,centered=1')
			} else if (val == \"docket\") {
				popupWin('order_download_docket.php?' + hash,'downloaddocket','width=350,height=100,centered=1')
			} else {
				document.forms[0].submit()
			}
		}
	}
	
</script>



<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr valign=\"top\">
    <td height=\"40\"><span class=\"titlebold\">Find orders</span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span class=\"text\">a maximum of 5000 orders will be returned</span></td>
    <td height=\"40\" colspan=\"2\" align=\"right\"><input name=\"Submit\" type=\"submit\" class=\"text\" value=\"View Orders\">
    </td>
  </tr>
</table>
<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
  <tr bgcolor=\"#E4E6EF\">
    <td width=\"203\" height=\"24\" class=\"text\"><strong>Status</strong> is&nbsp;&nbsp;&nbsp; <input name=\"button2\" type=\"button\" class=\"text\" onClick=\"statusSelect(true)\" value=\"Select All\">
</td>
    <td width=\"196\" height=\"24\" bgcolor=\"#C4C9D7\" class=\"text\"><input name=\"status_cancelled\" type=\"checkbox\" id=\"status_cancelled\" value=\"1\">
      Cancelled&nbsp;&nbsp;&nbsp;      </td>
    <td width=\"171\" bgcolor=\"#C4C9D7\" class=\"text\"><input name=\"status_cancelledbybuyer\" type=\"checkbox\" id=\"status_cancelledbybuyer\" value=\"1\">
Cancelled by Buyer</td>
  </tr>
  <tr bgcolor=\"#E4E6EF\">
    <td height=\"24\" class=\"text\">&nbsp;</td>
    <td height=\"24\" bgcolor=\"#C4C9D7\" class=\"text\"><input name=\"status_cancelledbyapproval\" type=\"checkbox\" value=\"1\">
Cancelled by Approval Mngr&nbsp;&nbsp;&nbsp;</td>
    <td height=\"24\" bgcolor=\"#C4C9D7\" class=\"text\"><input name=\"status_waiting\" type=\"checkbox\" id=\"status_waiting3\" value=\"1\">
Waiting for Approval&nbsp;&nbsp;</td>
  </tr>
  <tr bgcolor=\"#E4E6EF\">
    <td height=\"24\" class=\"text\">&nbsp;</td>
    <td height=\"24\" bgcolor=\"#C4C9D7\" class=\"text\">
<input name=\"status_hold\" type=\"checkbox\" id=\"status_hold\" value=\"1\">
On Hold&nbsp;&nbsp;&nbsp;</td>
    <td height=\"24\" bgcolor=\"#C4C9D7\" class=\"text\"><input name=\"status_ready\" type=\"checkbox\" id=\"status_ready3\" value=\"1\" checked>
Ready for Production&nbsp;&nbsp;&nbsp;</td>
  </tr>
  <tr bgcolor=\"#E4E6EF\">
    <td height=\"24\" class=\"text\">&nbsp;</td>
    <td height=\"24\" bgcolor=\"#C4C9D7\" class=\"text\"><input name=\"status_inprod\" type=\"checkbox\" id=\"status_inprod4\" value=\"1\">
In Production&nbsp;</td>
    <td height=\"24\" bgcolor=\"#C4C9D7\" class=\"text\"><input name=\"status_shipped\" type=\"checkbox\" id=\"status_shipped3\" value=\"1\">
Shipped</td>
  </tr>
  <tr bgcolor=\"#FFFFFF\">
    <td height=\"35\" class=\"text\"><strong>Order number</strong> is</td>
    <td height=\"35\" colspan=\"2\"><input name=\"ordernumber\" type=\"text\" size=\"15\" value=\"$_SESSION[ordersearch_ordernumber]\">
      <span class=\"text\">(separate multiple numbers with a comma)</span>    </td>
  </tr>
  <tr bgcolor=\"#E4E6EF\">
    <td height=\"35\" class=\"text\"><strong>Payment type</strong> is&nbsp;&nbsp;&nbsp;      <input name=\"button\" type=\"button\" class=\"text\" onClick=\"paymentSelect(true)\" value=\"Select All\">
      </td>
    <td height=\"35\" colspan=\"2\" bgcolor=\"#C4C9D7\" class=\"text\">
		<input name=\"paytype_none\" type=\"checkbox\" id=\"paytype_none\" value=\"1\" checked>No Pay Type&nbsp;&nbsp; 
		<input name=\"cc\" type=\"checkbox\" id=\"cc\" value=\"1\" checked>Credit Card&nbsp;&nbsp;
		<input name=\"pp\" type=\"checkbox\" id=\"pp\" value=\"1\" checked>PayPal&nbsp;&nbsp;
		<input name=\"po\" type=\"checkbox\" id=\"po\" value=\"1\" checked>PO&nbsp;&nbsp;
		<input name=\"check\" type=\"checkbox\" id=\"check\" value=\"1\" checked>Check
</td>
  </tr>
  <tr>
    <td height=\"35\" nowrap class=\"text\"><strong>Date ordered</strong> is (yyyy/mm/dd)</td>
    <td height=\"35\" colspan=\"2\" class=\"text\">from
      <input name=\"date1_from\" value=\"$_SESSION[ordersearch_date1_from]\" type=\"text\" size=\"12\" maxlength=\"11\"> 
      to
      <input name=\"date1_to\"  value=\"$_SESSION[ordersearch_date1_to]\" type=\"text\" size=\"12\" maxlength=\"11\">
      <span class=\"text\">(leave empty to find all)</span></td>
  </tr>
  <tr bgcolor=\"#E4E6EF\">
    <td height=\"35\" nowrap class=\"text\"><strong>Date approved</strong> is (yyyy/mm/dd)</td>
    <td height=\"35\" colspan=\"2\" bgcolor=\"#C4C9D7\" class=\"text\">from
      <input name=\"date2_from\"  value=\"$_SESSION[ordersearch_date2_from]\"  type=\"text\" size=\"12\" maxlength=\"11\">
to
<input name=\"date2_to\"  value=\"$_SESSION[ordersearch_date2_to]\"  type=\"text\" size=\"12\" maxlength=\"11\">
<span class=\"text\">(leave empty to find all)</span></td>
  </tr>
  <tr>
    <td height=\"35\" nowrap class=\"text\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
      <tr>
        <td class=\"text\">Customer's&nbsp;</td>
        <td><select name=\"customer_field\" class=\"text\">
          <option value=\"email\">Email</option>
          <option value=\"username\">Username</option>
        </select></td>
        <td class=\"text\">&nbsp;is</td>
      </tr>
    </table>
      </td>
    <td height=\"35\" colspan=\"2\"><input name=\"customer\" type=\"text\" size=\"20\" value=\"$_SESSION[ordersearch_customer]\">
      <span class=\"text\">(leave empty to find all)</span></td>
  </tr>
  <tr bgcolor=\"#E4E6EF\">
    <td height=\"35\" nowrap class=\"text\"><strong>Payment is captured</strong> /
      received*</td>
    <td height=\"35\" colspan=\"2\" bgcolor=\"#C4C9D7\" class=\"text\"><input name=\"BilledStatus\" type=\"radio\" value=\"any\" checked>
      Any&nbsp;&nbsp;&nbsp;&nbsp;
      <input type=\"radio\" name=\"BilledStatus\" value=\"1\">
Yes &nbsp;&nbsp;&nbsp;&nbsp;
<input type=\"radio\" name=\"BilledStatus\" value=\"0\">
No</td>
  </tr>
  <tr>
    <td height=\"35\" nowrap class=\"text\">Number of <strong>orders to show</strong> per page</td>
    <td height=\"35\" class=\"text\" colspan=\"2\"><select name=\"max_records\" class=\"text\">
    <option value=\"10\">10 
    <option value=\"20\">20 
    <option value=\"30\">30
    <option value=\"50\">50 
    <option value=\"75\">75
    <option value=\"100\">100  
      </select> </td>
  </tr>
  <tr bgcolor=\"#E4E6EF\">
    <td height=\"35\" nowrap class=\"text\"><strong>Show order items</strong> in list?</td>
    <td height=\"35\" colspan=\"2\" bgcolor=\"#C4C9D7\" class=\"text\"><input name=\"showitems\" type=\"radio\" value=\"1\" checked>
Yes &nbsp;&nbsp;&nbsp;&nbsp;
<input type=\"radio\" name=\"showitems\" value=\"0\">
No</td>
  </tr>
  <tr>
    <td height=\"24\" colspan=\"3\" class=\"text\"><!--In the future:
      <input name=\"showitems\" type=\"radio\" value=\"1\" checked>
      Show this as the first page &nbsp;&nbsp;     <input name=\"showitems\" type=\"radio\" value=\"1\">
      Show
      the search results as the first page .//--></td>
    </tr>
</table>
<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr valign=\"top\">
    <td height=\"40\"></td>
    <td height=\"40\" colspan=\"2\" align=\"right\"><input name=\"Submit\" type=\"submit\" class=\"text\" value=\"View Orders\">
    </td>
  </tr>
</table>

<input type=\"hidden\" name=\"action\" value=\"order_search_results\">

<p class=\"text\">
*Based on orders that have been captured or marked as &quot;payment received&quot; in
the VariaPrint&#8482; Manager.</p>
";

?>
