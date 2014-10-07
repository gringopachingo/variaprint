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


require_once("../../inc/config.php");
require_once("../../inc/functions-global.php");
require_once("../inc/functions.php");
if (!$_SESSION["privilege_items_properties"]) {
	require_once("../inc/popup_log_check.php");
}

//session_save_path("/www/tmp");
session_name("mssid");
session_start();
$mssid = session_id();
	
//	$itemid = $a_form_vars['itemid'];
	
	session_name("mssid");
	session_start();
	$mssid = session_id();

	
	$sql = "SELECT VendorManagers FROM Sites WHERE ID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	
	$a_xml = xml_get_tree($a_result['VendorManagers']);
	$a_xml[0][tag] = "SUPPLIERS";
	
	// Path:		SUPPLIERS/SUPPLIER 
	// Attributes:	EMAIL, ACCESS, NOTIFY, INVOICES, DOCKETS, FILES

	if ($a_form_vars['action'] == "save") {
	//	exit('saving');
		$a_email = array_find_key_prefix("email_", $a_form_vars, 1);
		$a_access = array_find_key_prefix("access_", $a_form_vars, 1);
		$a_notify = array_find_key_prefix("notify_", $a_form_vars, 1);
		$a_invoices = array_find_key_prefix("invoices_", $a_form_vars, 1);
		$a_dockets = array_find_key_prefix("dockets_", $a_form_vars, 1);
		$a_files = array_find_key_prefix("files_", $a_form_vars, 1);
				
		foreach ($a_email as $key=>$email) {
			if ($email != "") {
				$a_xml = xml_update_value("SUPPLIERS/SUPPLIER:$key", "EMAIL", $email, $a_xml);
				$a_xml = xml_update_value("SUPPLIERS/SUPPLIER:$key", "ACCESS", $a_access[$key], $a_xml);
				$a_xml = xml_update_value("SUPPLIERS/SUPPLIER:$key", "NOTIFY", $a_notify[$key], $a_xml);
				$a_xml = xml_update_value("SUPPLIERS/SUPPLIER:$key", "INVOICES", $a_invoices[$key], $a_xml);
				$a_xml = xml_update_value("SUPPLIERS/SUPPLIER:$key", "DOCKETS", $a_dockets[$key], $a_xml);
				$a_xml = xml_update_value("SUPPLIERS/SUPPLIER:$key", "FILES", $a_files[$key], $a_xml);
			}
		}
		$xml = addslashes(xml_make_tree($a_xml));
//		exit($xml);
		$sql = "UPDATE Sites SET VendorManagers='$xml' WHERE ID='$_SESSION[site]'";
		dbq($sql);
	}	


	$next_id = 0;
	if (is_array($a_xml[0]['children'])) {
		foreach($a_xml[0]['children'] as $supplier) {
			$id = $supplier['attributes']['ID'];
			$email = $supplier['attributes']['EMAIL'];
			$access = $supplier['attributes']['ACCESS'];
			$notify = $supplier['attributes']['NOTIFY'];
			$invoices = $supplier['attributes']['INVOICES'];
			$dockets = $supplier['attributes']['DOCKETS'];
			$files = $supplier['attributes']['FILES'];
			
			if ($next_id < $id) $next_id = $id;
			$rows .= "
  <tr> 
    <td><input type=\"text\" name=\"email_$id\" value=\"$email\"></td>
    <td align=\"center\"><input type=\"checkbox\" name=\"access_$id\" value=\"checked\" $access></td>
    <td align=\"center\"><input type=\"checkbox\" name=\"notify_$id\" value=\"checked\" $notify></td>
    <td align=\"center\"><input type=\"checkbox\" name=\"invoices_$id\" value=\"checked\" $invoices></td>
    <td align=\"center\"><input type=\"checkbox\" name=\"dockets_$id\" value=\"checked\" $dockets></td>
    <td align=\"center\"><input type=\"checkbox\" name=\"files_$id\" value=\"checked\" $files></td>
    <td><img src=\"../images/icon-delete.gif\" width=\"17\" height=\"17\"></td>
  </tr>
			";
		}
	}
	
	$id = $next_id+1;
	
	$rows .= "
  <tr> 
    <td height=50><input  type=\"text\" name=\"email_$id\"></td>
    <td height=50 align=\"center\"><input type=\"checkbox\" name=\"access_$id\" value=\"checked\"></td>
    <td height=50 align=\"center\"><input type=\"checkbox\" name=\"notify_$id\" value=\"checked\"></td>
    <td height=50 align=\"center\"><input type=\"checkbox\" name=\"invoices_$id\" value=\"checked\"></td>
    <td height=50 align=\"center\"><input type=\"checkbox\" name=\"dockets_$id\" value=\"checked\"></td>
    <td height=50 align=\"center\"><input type=\"checkbox\" name=\"files_$id\" value=\"checked\"></td>
    <td height=50><img src=\"../images/icon-add.gif\" width=\"17\" height=\"17\"></td>
  </tr>
";
	
	

?>
<HTML>
<HEAD>
<?
print($header_content);
?>
<TITLE>Suppliers Style Editor</TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../style.css" rel="stylesheet" type="text/css">
</HEAD>

<BODY>
<form name="form1" method="get" action="">
  <table width="547" border="0" cellpadding="4" cellspacing="0">
    <tr> 
      <td width="200" valign="bottom" class="text">Username</td>
      <td width="56" align="center" valign="bottom" class="text">Access<br>
        Enabled</td>
      <td width="48" align="center" valign="bottom" class="text">Notify&nbsp;on<br>
        Order</td>
      <td width="56" align="center" valign="bottom" class="text">Download<br>
        Invoices</td>
      <td width="56" align="center" valign="bottom" class="text">Download<br>
        Dockets</td>
      <td width="56" align="center" valign="bottom" class="text">Download<br>
        Print Files</td>
      <td width="19">&nbsp;&nbsp;</td>
    </tr>
    <?
	
	print($rows);
	
?>
    <tr> 
      <td colspan="7" valign="bottom" class="text"><br>
        <br>
        <input type="submit" name="Submit" value="Save">
        <input name="action" type="hidden" id="action" value="save"></td>
    </tr>
  </table>
</form>
</BODY>
</HTML>
