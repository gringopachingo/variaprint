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
if ($_SESSION["privilege"] == "owner") {
	require_once("inc/popup_log_check.php");
}

// $_SESSION[site] = $a_form_vars['site'];

if (trim($_SESSION[site]) == "" || !isset($_SESSION[site])) {
	exit("There was an error determining which site is opened. <br><br>You may need to log out and log back in or reopen the current site by clicking on &quot;open order site&quot; at the top left of the VariaPrint&trade; Manager main screen.");
}


if ($a_form_vars['action'] == "save") {
	
	$sql = "UPDATE Items SET TrackInventory='$a_form_vars[enabled]',InventoryAmount='$a_form_vars[amount]' WHERE ID='$a_form_vars[item_id]' AND SiteID='$_SESSION[site]'";
	dbq($sql);
	
	print("
	<script language=\"JavaScript\">
		window.opener.location.reload();
		top.close();
	</script>
	");
	
	exit("saving");
}


$sql = "SELECT Name,TrackInventory,InventoryAmount FROM Items WHERE ID='$a_form_vars[item_id]' AND SiteID='$_SESSION[site]'";
$res = dbq($sql);
if (mysql_num_rows($res) == 0) {
	exit("error");
}

$item = mysql_fetch_assoc($res);

if ($item['TrackInventory'] == "true") {
	$track_true = "checked";
} else {
	$track_false = "checked";
}
if ($item['InventoryAmount'] == "") {
	$amount = 0;
} else {
	$amount = $item['InventoryAmount'];
}
?>
<html>
<head>
<title>Item Inventory</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/bkg-groove.gif" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="98%" border="0" cellpadding="0" cellspacing="10">
  <tr>
    <td class="text"><form name="form1" method="post" action="">
      <p class="title"><strong>Edit Inventory
        Settings  for Item &quot;<? print($item['Name']); ?>&quot; </strong></p>
      <p>If inventory tracking is enabled, the system will reduce the inventory
        amount  by the amount of
        the item ordered when an order's status is changed to Shipped. If the
        inventory is preprinted shells, multiply the number of shells printed
        times the number of items printed on the shell.</p>
      <p>
        <input type="radio" name="enabled" value="true" <? print($track_true); ?>>
        Inventory Tracking    <strong>Enabled</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="enabled" value="false" <? print($track_false); ?>>
    Inventory Tracking <strong>Disabled</strong> </p>
      <p>Change current inventory amount to
          <input name="amount" type="text" id="amount" value="<? print($amount); ?>" size="10" >
    units</p>
      <p>
        <input type="button" name="Button" value="Cancel" onClick="top.close()">
        <input type="submit" name="Submit2" value="Save">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="item_id" value="<? print($a_form_vars['item_id']); ?>">
      </p>
    </form></td>
  </tr>
</table>
</body>
</html>
