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
	require_once("inc/functions.php");
	require_once("inc/iface.php");
//	require_once("inc/session.php");
	require_once("inc/popup_log_check.php");
	

	// Get XML from DB
	$sql = "SELECT ApprovalManagers FROM Sites WHERE ID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	
	$a_tree = xml_get_tree($a_result['ApprovalManagers']);
	
	// SAVE IT! **************************
	if ($a_form_vars['action'] == "save") {
		$default_username = $a_form_vars['default_username'];
		$a_tree = xml_update_value("APPROVAL/MANAGER:DEFAULT","USERNAME",$default_username,$a_tree);

		// Get form data
		$a_names = array_find_key_prefix("description_",$a_form_vars,true);
		$a_users = array_find_key_prefix("user_",$a_form_vars,true);
//.		print_r($a_users);
		$a_tree[0]['tag'] = "APPROVAL";
		foreach($a_names as $k=>$name) {
			if ($name != "") {
				$a_tree = xml_update_value("APPROVAL/MANAGER:$k","NAME",$name,$a_tree);
				$a_tree = xml_update_value("APPROVAL/MANAGER:$k","USERNAME",$a_users[$k],$a_tree);
			}
		}
		
		
//		print_r($a_tree);
		
		$xml = addslashes(xml_make_tree($a_tree));
		
		$sql = "UPDATE Sites SET ApprovalManagers='$xml' WHERE ID='$_SESSION[site]'";
		dbq($sql);
		
		// exit("saving...");
	} elseif ($a_form_vars['action'] == "delete") {
		$a_tree = xml_delete_node("APPROVAL/MANAGER:$a_form_vars[deleteid]",$a_tree);
		$xml = addslashes(xml_make_tree($a_tree));
		
		$sql = "UPDATE Sites SET ApprovalManagers='$xml' WHERE ID='$_SESSION[site]'";
		dbq($sql);
		
	}
	
	
	// Get the default manager username
	$default_node = xml_get_node_by_path("APPROVAL/MANAGER:DEFAULT", $a_tree);
	$default_username = $default_node['attributes']['USERNAME'];
	
//	print_r($default_node);
	
	$next_id = 0;
	
	// Display all of the managers in the list
	if ( is_array($a_tree[0]['children']) ) {
		foreach ($a_tree[0]['children'] as $manager_node) {
			$id = $manager_node['attributes'][ID];
			if ($id != "DEFAULT") {
				if ($id > $next_id) $next_id = $id;
				$name = $manager_node[attributes][NAME];
				$username = $manager_node[attributes][USERNAME];
				$managers .= "
				<tr>
					<td height=\"20\"><input type=\"text\" name=\"description_$id\" value=\"$name\"></td>
					<td>&nbsp;</td>
					<td height=\"20\"><input type=\"text\" name=\"user_$id\" value=\"$username\"></td>
					<td height=\"20\"><a href=\"approval_managers.php?action=delete&deleteid=$id\"><img border=\"0\" src=\"images/icon-delete.gif\"></a></td>
				</tr>";
			}
		}
	}
	
	$next_id++;
	
	$managers .= "
				<tr>
					<td height=\"40\"><input type=\"text\" name=\"description_$next_id\"></td>
					<td>&nbsp;</td>
					<td height=\"40\"><input type=\"text\" name=\"user_$next_id\"></td>
					<td height=\"40\"><a href=\"javascript:;\" onClick=\"document.forms[0].submit()\"><img border=0 src=\"images/icon-add.gif\"></a></td>
				</tr>";
		

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?
print($header_content);
?>
<title>Approval Managers</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/bkg-groove.gif">
<form name="form1" method="post" action="">
  <p class="title"><strong>Approval Managers</strong></p>
  <p class="title"><strong><em>Default manager</em></strong> &nbsp; <span class="text">This 
    default manager will be sent a notice if no approval manager is selected from 
    the list at checkout.</span></p>
  <table width="316" border="0" cellpadding="0" cellspacing="0">
    <tr class="text"> 
      <td width="355"><strong>Email*</strong></td>
      <td width="46"> &nbsp;&nbsp;&nbsp;</td>
    </tr>
    <tr> 
      <td height="30"><input name="default_username" type="text" id="default_username" value="<? print($default_username); ?>">
        <input name="action" type="hidden" id="action" value="save"></td>
      <td align="right"> <input type="submit" name="Submit" value="Save"></td>
    </tr>
  </table>
  <hr size="1" noshade>
  <p class="title"><em><strong>Approval manager list</strong> &nbsp; </em><span class="text">These 
    managers will appear in a list for users to select at checkout if the &quot;Include 
    Order-Approval Manager Selection&quot; option is selected in the site settings. 
    <em>(Sample descriptions: West Office, Mid-west, Northeast Office, Southeast 
    Office.) </em> </span></p>
  <table border="0" cellspacing="0" cellpadding="0">
    <tr class="text"> 
      <td><strong>Description</strong></td>
      <td></td>
      <td><strong>Email*</strong></td>
    </tr>
	
	<? print($managers); ?>
	
  </table>
  <br>
  <table width="316" border="0" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="355" height="30">&nbsp;</td>
      <td width="46" align="right"> <input type="submit" name="Submit2" value="Save"></td>
    </tr>
  </table>
  <p><span class="text">*A message will be sent to the appropriate approval
      manager with a link to approve the order. Only the person receiving this
      email or the master site manager (yourself) will be able to approve the
      ordered items.</span></p>
</form>
</body>
</html>
