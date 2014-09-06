<!--
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
-->
<html>
<head>
<title>Search Orders</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
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
</script>
</head>

<body>
<form name="form1" method="post" action="">
<table width="600" border="0" cellspacing="0" cellpadding="5">
  <tr valign="top">
    <td height="40"><span class="titlebold">View orders</span></td>
    <td height="40" colspan="2" align="right"><input name="Submit" type="submit" class="text" value="View Orders">
    </td>
  </tr>
  <tr bgcolor="#E4E6EF">
    <td width="203" height="24" class="text"><strong>Status</strong> is&nbsp;&nbsp;&nbsp; <input name="button2" type="button" class="text" onClick="statusSelect(true)" value="Select All">
</td>
    <td width="196" height="24" bgcolor="#C4C9D7" class="text"><input name="status_cancelled" type="checkbox" id="cc4" value="1">
      Cancelled&nbsp;&nbsp;&nbsp;      </td>
    <td width="171" bgcolor="#C4C9D7" class="text"><input name="status_cancelledbybuyer" type="checkbox" id="status_cancelledbybuyer" value="1">
Cancelled by Buyer</td>
  </tr>
  <tr bgcolor="#E4E6EF">
    <td height="24" class="text">&nbsp;</td>
    <td height="24" bgcolor="#C4C9D7" class="text"><input name="status_cancelledbyapproval" type="checkbox" value="1">
Cancelled by Approval Mngr&nbsp;&nbsp;&nbsp;</td>
    <td height="24" bgcolor="#C4C9D7" class="text"><input name="status_waiting" type="checkbox" id="status_waiting3" value="1">
Waiting for Approval&nbsp;&nbsp;</td>
  </tr>
  <tr bgcolor="#E4E6EF">
    <td height="24" class="text">&nbsp;</td>
    <td height="24" bgcolor="#C4C9D7" class="text">
<input name="status_hold" type="checkbox" id="status_hold" value="1">
On Hold&nbsp;&nbsp;&nbsp;</td>
    <td height="24" bgcolor="#C4C9D7" class="text"><input name="status_ready" type="checkbox" id="status_ready3" value="1" checked>
Ready for Production&nbsp;&nbsp;&nbsp;</td>
  </tr>
  <tr bgcolor="#E4E6EF">
    <td height="24" class="text">&nbsp;</td>
    <td height="24" bgcolor="#C4C9D7" class="text"><input name="status_inprod" type="checkbox" id="status_inprod4" value="1">
In Production&nbsp;</td>
    <td height="24" bgcolor="#C4C9D7" class="text"><input name="status_shipped" type="checkbox" id="status_shipped3" value="1">
Shipped</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td height="35" class="text"><strong>Order number</strong> is</td>
    <td height="35" colspan="2"><input name="textfield" type="text" size="20">
      <span class="text">(leave empty to find all)</span>    </td>
  </tr>
  <tr bgcolor="#E4E6EF">
    <td height="35" class="text"><strong>Payment type</strong> is&nbsp;&nbsp;&nbsp;      <input name="button" type="button" class="text" onClick="paymentSelect(true)" value="Select All">
      </td>
    <td height="35" colspan="2" bgcolor="#C4C9D7" class="text"><input name="cc" type="checkbox" id="cc" value="1" checked>
      Credit Card &nbsp;&nbsp; <input name="pp" type="checkbox" id="pp" value="1" checked>
      PayPal&nbsp;&nbsp;&nbsp;      <input name="po" type="checkbox" id="po4" value="1" checked>
PO&nbsp;&nbsp;
<input name="check" type="checkbox" id="check2" value="1" checked>
Check </td>
  </tr>
  <tr>
    <td height="35" nowrap class="text"><strong>Date ordered</strong> is (yyyy-mm-dd)</td>
    <td height="35" colspan="2" class="text">from
      <input name="textfield2" type="text" size="11" maxlength="11"> 
      to
      <input name="textfield22" type="text" size="11" maxlength="11">
      <span class="text">(leave empty to find all)</span></td>
  </tr>
  <tr bgcolor="#E4E6EF">
    <td height="35" nowrap class="text"><strong>Date approved</strong> is (yyyy-mm-dd)</td>
    <td height="35" colspan="2" bgcolor="#C4C9D7" class="text">from
      <input name="textfield23" type="text" size="11" maxlength="11">
to
<input name="textfield222" type="text" size="11" maxlength="11">
<span class="text">(leave empty to find all)</span></td>
  </tr>
  <tr>
    <td height="35" nowrap class="text"><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="text">Customer's&nbsp;</td>
        <td><select name="select" class="text">
          <option value="email">Email</option>
          <option value="username">Username</option>
        </select></td>
        <td class="text">&nbsp;is</td>
      </tr>
    </table>
      </td>
    <td height="35" colspan="2"><input name="textfield32" type="text" size="20">
      <span class="text">(leave empty to find all)</span></td>
  </tr>
  <tr bgcolor="#E4E6EF">
    <td height="35" nowrap class="text"><strong>Payment is captured</strong> /
      received*</td>
    <td height="35" colspan="2" bgcolor="#C4C9D7" class="text"><input name="BilledStatus" type="radio" value="any" checked>
      Any&nbsp;&nbsp;&nbsp;&nbsp;
      <input type="radio" name="BilledStatus" value="1">
Yes &nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="BilledStatus" value="0">
No</td>
  </tr>
  <tr>
    <td height="35" nowrap class="text">Number of <strong>orders to show</strong> per page</td>
    <td height="35" colspan="2"><select name="select2" class="text">
    <option value="10">10
    <option value="20">20
    <option value="30">30
    <option value="50">50
    <option value="75">75
    <option value="100">100    
      </select></td>
  </tr>
  <tr bgcolor="#E4E6EF">
    <td height="35" nowrap class="text"><strong>Show order items</strong> in list?</td>
    <td height="35" colspan="2" bgcolor="#C4C9D7" class="text"><input name="showitems" type="radio" value="1" checked>
Yes &nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="showitems" value="0">
No</td>
  </tr>
  <tr>
    <td height="24" colspan="3" class="text"><!--In the future:
      <input name="showitems" type="radio" value="1" checked>
      Show this as the first page &nbsp;&nbsp;     <input name="showitems" type="radio" value="1">
      Show
      the search results as the first page .//--></td>
    </tr>
  <tr>
    <td height="24" class="text">&nbsp;</td>
    <td height="24" colspan="2" align="right"><input name="Submit" type="submit" class="text" value="View Orders">
      <input name="action" type="hidden" id="action" value="search"></td>
  </tr>
</table>
<p class="text">*Based on orders that have been captured or marked as payment received in
  the VariaPrint&#8482; Manager.</p>
</form>
</body>
</html>
