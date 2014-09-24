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


	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	
	session_name("ms-sid");
	session_start();

	$sql = "SELECT Email FROM AdminUsers WHERE Username='$_SESSION[username]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	$email = $a_result['Email'];
	
	$sql = "SELECT NoticePO,NoticeOrder,NoticeOrderApproved FROM Sites WHERE ID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	$po = $a_result['NoticePO'];
	$order = $a_result['NoticeOrder'];
	$orderapproved = $a_result['NoticeOrderApproved'];
	
	if ($po != "false") {
		$po = "checked";
	}
	if ($order != "false") {
		$order = "checked";
	}
	if ($orderapproved == "true") {
		$orderapproved = "checked";
	}
	
	
	
	if ($a_form_vars['action'] == "save") {
		if ($a_form_vars['po'] != "true") $a_form_vars['po'] = "false";
		if ($a_form_vars['order'] != "true") $a_form_vars['order'] = "false";
		$sql = "UPDATE Sites SET NoticeOrderApproved='$a_form_vars[orderapproved]',NoticePO='$a_form_vars[po]', NoticeOrder='$a_form_vars[order]' WHERE ID='$_SESSION[site]'";
		dbq($sql);
		
		print("
		<script language=\"JavaScript\">
			top.close();
		</script>
		");
	}

?><html>
<head>
<title>Notification Options</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table cellpadding=0 cellspacing=0 border=0 align=center width="420" height="96%">
  <tr>
    <td>
      <div class="text">
        <form name="form1" method="get" action="">
        <table width="420" border="0" cellspacing="0" cellpadding="3">
          <tr>
            <td colspan="2" class="text"> Send an email notification to you <strong>(<? print($email); ?>)</strong>
              when:</td>
            </tr>
          <tr>
            <td width="1">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td width="1"><input name="po" type="checkbox" id="po" value="true" <? print($po); ?>></td>
            <td width="384" class="text">PO account is requested</td>
          </tr>
          <tr>
            <td width="1"><input name="order" type="checkbox" id="order" value="true" <? print($order); ?>></td>
            <td class="text">A new order is placed </td>
          </tr>
          <tr>
            <td><input name="orderapproved" type="checkbox" id="orderapproved" value="true" <? print($orderapproved); ?>></td>
            <td class="text"> An order is approved </td>
          </tr>
          <tr>
            <td width="1">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><input name="Button" type="button" value="Cancel" onClick="top.close()">
              <input name="submit" type="submit" value="Save">
              <input name="action" type="hidden" id="action" value="save">
</td>
            </tr>
        </table>
        <br>
	<br>
	<br>
        </form>
      </div>
    </td>
  </tr>
</table>
</body>
</html>
