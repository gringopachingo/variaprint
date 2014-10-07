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
//	require_once("inc/iface.php");
//	require_once("inc/session.php");
	
	if (!$_SESSION['privilege_dockets']) {
		require_once("inc/popup_log_check.php");
	}
	
	$status = $a_form_vars['status'];
	$doaction = $a_form_vars['doaction'];
	$a_checked = array_find_key_prefix("checkbox_",$a_form_vars,1);
	
	
	if ($doaction == "true") {
		if (is_array($a_checked)) {
			$fp = true;
			foreach($a_checked as $key=>$crap) {
				if (!$fp) {$where .= " OR ";}
				$where .= "ID='$key'";	
				$fp = false;
			}
			
			$date_completed = strtotime($a_form_vars['date_completed']);
			$sql = "UPDATE Dockets SET Status='$status',DateCompleted='$date_completed' WHERE $where";
			dbq($sql);
			
			print("
			<script language=\"JavaScript\">
				top.opener.location.reload(false);
				top.close();
			</script>
			");
		}
		
		exit;
	}
	
	
	if (is_array($a_checked)) {
		foreach($a_checked as $key=>$crap) {
			$hidden .="<input type=\"hidden\" name=\"checkbox_$key\" value=\"yes\">";
		}
	}

	if ($status == "completed") {
		$date_completed = date("M d y H:i:s",time());
		$content = "
		<input name=\"status\" type=\"hidden\" id=\"status\" value=\"$status\">
        <span class=\"text\"><br>
        Set date completed to:</span> <br>
	<input name=\"date_completed\" type=\"text\" value=\"$date_completed\" size=\"20\"><br>
	$hidden
		";
		
	} elseif ($status == "production") {
		$content = "<span class=\"text\">Are you sure you want to set selected dockets back to &quot;In Production&quot;<br><br></span>";	
	}
		

?><html>
<head>
<title>Change Dockets' Status</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/bkg-groove.gif" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="301" height="96%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="224">      <form name="form1" method="post" action="">
<? print($content); ?>
<input name="doaction" type="hidden" id="action" value="true">
<input type="button" name="" value="Cancel" onClick="top.close()">
<input type="submit" name="Submit" value="Change Status">
        </form>
    </td>
  </tr>
</table>
</body>
</html>
