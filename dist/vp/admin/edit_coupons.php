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
	require_once("inc/iface.php");
//	require_once("inc/session.php");
	require_once("inc/popup_log_check.php");
	
	
	function make_coupon_code($min=5,$max=6) {
		for($i=0;$i<rand($min,$max);$i++){ 
			$num=rand(48,122);
			if(($num > 97 && $num < 122)){
				$pwd.=chr($num);
			}else if(($num > 65 && $num < 90)){ 
				$pwd.=chr($num); 
			}else if(($num >48 && $num < 57)){ 
				$pwd.=chr($num); 
			}else if($num==95){ 
				$pwd.=chr($num); 
			}else{ 
				$i--; 
			} 
		}
		return $pwd;	
	}

	// SAVE IT! **************************
	if ($a_form_vars['action'] == "save") {
		// Get form data
		$a_code = array_find_key_prefix("code_",$a_form_vars,true);
		$a_amount = array_find_key_prefix("amount_",$a_form_vars,true);
		$a_type = array_find_key_prefix("type_",$a_form_vars,true);
		$a_expdate = array_find_key_prefix("expdate_",$a_form_vars,true);
		if (is_array($a_code)) {
			foreach ($a_code as $key=>$val) {
				$sql_frag = "Code='{$a_code[$key]}',
					Amount='{$a_amount[$key]}',
					Type='{$a_type[$key]}',
					ExpirationDate='".strtotime($a_expdate[$key])."'
				";
				$sql = "";
				if ($key != "new") {
					$sql = "UPDATE DiscountCoupons SET {$sql_frag}
						WHERE ID='$key' AND SiteID='$_SESSION[site]'";
					dbq($sql);
				} elseif (!empty($a_amount[$key])) {
					$sql = "INSERT INTO DiscountCoupons SET {$sql_frag}, SiteID='$_SESSION[site]'";
					dbq($sql);
				}
			}
		}
		
	} elseif ($a_form_vars['action'] == "delete") {
		$sql = "DELETE FROM DiscountCoupons WHERE ID='$fv[deleteid]' AND SiteID='$_SESSION[site]'";
		dbq($sql);
	}
	
	// Get Coupon Codes for this site
	$sql = "SELECT * FROM DiscountCoupons WHERE SiteID='$_SESSION[site]'";
	$res = dbq($sql);

	while ($a = mysql_fetch_assoc($res) ) {
		$id = $a[ID];
		$code = $a[Code];
		$amount = $a[Amount];
		if ($a[Type] == "Percent") { $sel_per = "selected"; $sel_dol = ""; } else { $sel_per = ""; $sel_dol = "selected";   }
		$expdate = date("m/d/Y",$a[ExpirationDate]);
		$list .= "
		<tr>
			<td height=\"20\"><input type=\"text\" name=\"code_$id\" size=\"7\" value=\"{$code}\"></td>
			<td height=\"20\"><input type=\"text\" name=\"amount_$id\" size=\"5\" value=\"{$amount}\"></td>
			<td height=\"20\">
				<select name=\"type_$id\">
					<option value=\"Percent\" $sel_per>Percent</option>
					<option value=\"Dollars\" $sel_dol>Dollars</option>
				</select>
			</td>
			<td height=\"20\"><input type=\"text\" name=\"expdate_{$id}\" size=\"16\" value=\"{$expdate}\"></td>
			<td height=\"20\">&nbsp;&nbsp;</td>
			<td height=\"20\"><a href=\"edit_coupons.php?action=delete&deleteid={$id}\"><img border=\"0\" src=\"images/icon-delete.gif\"></a></td>
		</tr>
		";
	}
	
	$next_id++;
	
	$list = "
		<tr class=\"text\"> 
		  <td><strong>Code</strong></td>
		  <td colspan=2><strong>Amount</strong></td>
		  <td><strong>Exp. Date (mm/dd/yyyy)</strong></td>
		  <td colspan=2>&nbsp;</td>
		</tr>

	$list

		<tr class=\"text\">
			<td height=\"40\"><input type=\"text\" name=\"code_new\" size=\"7\" value=\"".make_coupon_code()."\"></td>
			<td height=\"40\"><input type=\"text\" name=\"amount_new\" size=\"5\"></td>
			<td height=\"40\">
				<select name=\"type_new\">
					<option value=\"Percent\">Percent</option>
					<option value=\"Dollars\">Dollars</option>
				</select>
			</td>
			<td height=\"40\"><input type=\"text\" name=\"expdate_new\" size=\"16\" value=\"".date("m/d/Y",time()+86400)."\"></td>
			<td height=\"40\">&nbsp;&nbsp;</td>
			<td height=\"40\"><a href=\"javascript:;\" onClick=\"document.forms[0].submit()\"><img border=0 src=\"images/icon-add.gif\"></a></td>
		</tr>";
		

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php
print($header_content);
?>
<title>Discount Coupon Codes</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/bkg-groove.gif">
<form name="form1" method="post" action="edit_coupons.php">
  <p class="title"><strong>Discount Coupon Codes</strong></p>
  <table border="0" cellspacing="0" cellpadding="0">
	
	<?php print($list); ?>
	
  </table>
  <br>
  <table width="433" border="0" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="355" height="30">&nbsp;</td>
      <td width="46" align="right"> <input type="submit" name="Submit2" value="Save"></td>
    </tr>
  </table>
  <p><span class="text"></span></p>
  <input type="hidden" value="save" name="action" id="action">
</form>
</body>
</html>
