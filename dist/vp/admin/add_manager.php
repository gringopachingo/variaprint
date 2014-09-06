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


	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	
	session_name("ms_sid");
	session_start();
	
	if(!isset($a_form_vars['step'])){
		$a_form_vars['step'] = "1";
	}
	
	$step1content = "
	Enter a username or email address to add.<br>
	<input type=\"text\" name=\"user\" style=\"width: 300\">
	<input type=\"submit\" value=\"Find\">
	<input type=\"hidden\" name=\"step\" value=\"2\">
    <script language=\"JavaScript\">
	<!--
	document.forms[0].elements[0].focus();
	//-->
   	</script> 
	";
	
	switch($a_form_vars['step']) {
		case "1" :
			$content = $step1content;
			break;
		
		case "2" :
			$sql = "SELECT VendorManagers FROM Sites WHERE ID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			
			$a_managers = xml_get_tree($a_result['VendorManagers']);
			
			if(is_array($a_managers[0]['children'])){
				foreach($a_managers[0]['children'] as $node){
					$current_users[$node['attributes']['EMAIL']] = true;
				}
			}
			
			$sql = "SELECT ID,Email,Username,Firstname,Company FROM AdminUsers WHERE Email='$a_form_vars[user]' OR Username='$a_form_vars[user]'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			
			if (mysql_num_rows($r_result) < 1) {
				$content = "
				No manager accounts found with a username or email matching '$a_form_vars[user]'.<br><br>
				" .  $step1content;
			} elseif ($a_result['Username'] == $_SESSION['username']) {
				$content = "
				'$a_result[Username]' is your username. You already have full access to this site.<br><br>
				
				" . $step1content;
			} elseif ($current_users[$a_result[Username]]) {
				$content = "
				'$a_result[Username]' is already a listed manager. Click \"Access / Notification options...\" to edit the settings for this manager.<br><br>
				" . $step1content;
				
			} else {
				$content = "
				Is this the account you want to add?<br><br>
				<strong>username:</strong> $a_result[Username]<br>
				<strong>email:</strong> $a_result[Email]<br>
				<strong>first name:</strong> $a_result[Firstname]<br>
				<strong>company:</strong> $a_result[Company] <br><br>
				<input type=\"button\" value=\"No\" onclick=\"document.location='add_manager.php?step=1'\"> &nbsp; 
				<input type=\"submit\" value=\"Yes\">
				<input type=\"hidden\" name=\"step\" value=\"3\">
				<input type=\"hidden\" name=\"username\" value=\"$a_result[Username]\">
				";
			}
			
			break;
			
		case "3" :
			$sql = "SELECT VendorManagers FROM Sites WHERE ID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			
			$a_managers = xml_get_tree($a_result['VendorManagers']);
			$already = false;
			$high_id = 0;
			if(is_array($a_managers[0]['children'])){
				foreach($a_managers[0]['children'] as $node){
					if ($node['attributes']['EMAIL'] == $a_form_vars['username']) { $already = true; }
					if($node['attributes']['ID'] > $high_id) {
						$high_id = $node['attributes']['ID'];
					}
				}
				++$high_id;
			} else {
				$a_managers[0]['tag'] = "SUPPLIERS";
				$a_managers[0]['attributes'] = array();
				$a_managers[0]['children'] = array();
			}
			
			if (!$already) {
				
				$new_manager['tag'] = "SUPPLIER";
				$new_manager['attributes']['ID'] = $high_id;
				$new_manager['attributes']['EMAIL'] = $a_form_vars['username'];
				
				array_push($a_managers[0]['children'],$new_manager);
				
				$xml = addslashes(xml_make_tree($a_managers));
				print($xml."\n");
			//	print("<".$_SESSION[site].">");
				
				$sql = "UPDATE Sites SET VendorManagers='$xml' WHERE ID='$_SESSION[site]'";
				dbq($sql);
			}
			
			
			$content = "
			<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0\" width=\"500\" height=\"300\">
			  <param name=\"movie\" value=\"manageraccounts.swf?username=$a_form_vars[username]\">
			  <param name=\"quality\" value=\"high\">
			  <embed src=\"manageraccounts.swf?username=$a_form_vars[username]\" quality=\"high\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" width=\"500\" height=\"300\"></embed>
			</object>
			";
			break;
			
		case "4" :
			$content = "";
			break;
			
	}
	

?>
<html>
<head>
<title>Add order site manager</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
function closeWindow() {
	top.close();
	window.opener.location.reload(false);
}
</script>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table cellpadding=0 cellspacing=0 border=0 align=center width="420" height="96%">
  <tr>
    <td>
      <div class="text">
        <form name="form1" method="get" action="">
<?
	print($content);
?>
        </form>
      </div>
    </td>
  </tr>
</table>
</body>
</html>
