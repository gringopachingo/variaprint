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
	require_once("inc/popup_log_check.php");
	
	// Get current tables
	$sql = "SELECT Taxes FROM Sites WHERE ID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_result = mysql_fetch_assoc($r_result);
	$a_tree = xml_get_tree($a_result['Taxes']);
	
	$a_tree[0]['tag'] = "TAXES";
	
	
	if ( $a_form_vars['action'] == "save" || $a_form_vars['action'] == "add" ) {
		$a_states = array_find_key_prefix("state_", $a_form_vars, true);
		$a_tax = array_find_key_prefix("tax_", $a_form_vars, true);
		
		foreach ( $a_states as $k=>$v ) {
			if ( $v != "" ) {
				$a_tree = xml_update_value("TAXES/STATE:$k","NAME", $v, $a_tree);
				$a_tree = xml_update_value("TAXES/STATE:$k","TAX", $a_tax[$k], $a_tree);
			}
		}
		
		$xml = addslashes(xml_make_tree($a_tree));
		
		$sql = "UPDATE Sites SET Taxes='$xml' WHERE ID='$_SESSION[site]'";
		dbq($sql);
		//print_r($a_tree);
		if ($a_form_vars['action'] == "save") {
			print("<script language=javascript> top.close() </script>");
			exit("saving");
		}
	} else if ($a_form_vars['action'] == "delete") {
	
		$a_tree = xml_delete_node("TAXES/STATE:$a_form_vars[id]",$a_tree);
		
		$xml = addslashes(xml_make_tree($a_tree));
		
		$sql = "UPDATE Sites SET Taxes='$xml' WHERE ID='$_SESSION[site]'";
		dbq($sql);	
	}
	
	
	
	$next_id = 0;
	
	if ( is_array($a_tree[0]['children']) ) {
		foreach ($a_tree[0]['children'] as $node) {
			$id = $node['attributes']['ID'] ;
			if ( $id > $next_id ) { $next_id = $id; }
			$tax = $node['attributes']['TAX'];
			$name = $node['attributes']['NAME'];
			
			$rows .= "
	  <tr>
		<td><input type=text name=state_$id value=\"$name\"></td>
		<td><input type=text name=tax_$id value=\"$tax\"></td>
		<td> <a href=\"edit_tax_tables.php?action=delete&id=$id\"><img border=\"0\" src=\"images/icon-delete.gif\"></a> </td>
	  </tr>
			";
			
		}
	}
	
	$next_id++;
	
	$rows .= "
	  <tr>
		<td height=35><input type=text name=state_$next_id></td>
		<td height=35><input type=text name=tax_$next_id></td>
		<td height=35> <a href=\"javascript:;\" onClick=\"document.forms[0].action.value='add';document.forms[0].submit()\"><img border=\"0\" src=\"images/icon-add.gif\"></a> </td>
	  </tr>
	";
	
	$content = 
	"
	<table width=\"300\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
	  <tr>
		<td ><b class=\"text\">State (abbreviation)</b></td>
		<td><b class=\"text\">Sales Tax</b></td>
		<td>&nbsp;</td>
	  </tr>
$rows
	</table>
	";

	$next_id = 0;
	
//	print_r($a_tree);

?><HTML>
<HEAD>
<?
print($header_content);
?>
<TITLE>Edit Tax Table</TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</HEAD>

<BODY background="images/bkg-groove.gif">
<form name="form1" method="get" action="">
  <?
print($content);

?>
  <p> 
    <input type="submit" name="save" value="Save">
    <input name="action" type="hidden" id="action" value="save">
  </p>
</form>
<p>&nbsp;</p>
</BODY>
</HTML>
