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


	require_once("inc/popup-header.php");
	require_once("inc/config.php");
	require_once("inc/encrypt.php");
	require_once("inc/functions-global.php");
	require_once("inc/functions.php");
	require_once("inc/iface.php");
	
	session_name("ossid");
	session_start();
	$ossid = session_id();
	
	if ($a_form_vars['action'] == "save") {
		
		$a_app = array();
		foreach( $a_form_vars as $k=>$v ) {
			$a_app = xml_update_value("PO/BILLING:$k", "CDATA", $v, $a_app);
		}
		
		$xml = addslashes(xml_make_tree($a_app));
		$time = time();
		$sql = "INSERT INTO PO SET SiteID='$_SESSION[site]', MasterID='$_SESSION[user_id]', Billing='$xml', DateCreated='$time', DateModified='$time', Status='notapproved'";
		dbq($sql);
		$insert_id = db_get_last_insert_id();
		$sql = "UPDATE Users SET POID='$insert_id' WHERE ID='$_SESSION[user_id]'";
		dbq($sql);
		
//		print_r($a_app);
		
		print("
			<script language=\"javascript\">
				top.close()
				top.opener.location.reload(false);
			</script>
		");
		
		exit();
	}

?>
<html>
<head>
<title>Apply for a PO account</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<? require_once("inc/style_sheet.php"); ?>

</head>

<body bgcolor="<? print($bgcolor); ?>">
<strong class="title">Apply for PO account</strong> <br>
<br>
<!-- BillCompany, BillName, BillCity, BillStreet, BillStreet2, BillState, BillZip, Phone, Email<br> //-->
<form name="form1" method="post" action="">
  <table width="400" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td height="30" class="sidetext">Bill Company</td>
      <td height="30"> <input name="BillCompany" type="text" id="BillCompany"></td>
    </tr>
    <tr> 
      <td height="30" class="sidetext">Bill Name</td>
      <td height="30"> <input name="BillName" type="text" id="BillName"></td>
    </tr>
    <tr> 
      <td height="30" class="sidetext">Bill City</td>
      <td height="30"> <input name="BillCity" type="text" id="BillCity"></td>
    </tr>
    <tr> 
      <td height="30" class="sidetext">Bill Street</td>
      <td height="30"> <input name="BillStreet" type="text" id="BillStreet"></td>
    </tr>
    <tr> 
      <td height="30" class="sidetext">Bill Street 2</td>
      <td height="30"> <input name="BillStreet2" type="text" id="BillStreet2"></td>
    </tr>
    <tr> 
      <td height="30" class="sidetext">Bill State</td>
      <td height="30"> <input name="BillState" type="text" id="BillState"></td>
    </tr>
    <tr> 
      <td height="30" class="sidetext">Bill Zip</td>
      <td height="30"> <input name="BillZip" type="text" id="BillZip"></td>
    </tr>
    <tr> 
      <td height="30" class="sidetext">Phone</td>
      <td height="30"> <input name="Phone" type="text" id="Phone"></td>
    </tr>
    <tr> 
      <td height="30" class="sidetext"> Email </td>
      <td height="30"> <input name="Email" type="text" id="Email">
      </td>
    </tr>
    <tr>
      <td height="30" class="sidetext">&nbsp;</td>
      <td height="30"><input type="submit" name="Submit" value="Apply">
        <input name="action" type="hidden" id="action" value="save"></td>
    </tr>
  </table>
  </form>
</body>
</html>
