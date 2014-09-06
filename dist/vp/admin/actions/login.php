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


$error = 0;
$sql = "SELECT * FROM AdminUsers WHERE Username='$a_form_vars[username]'";// AND SiteID='$_SESSION[site]'
$nResult = dbq($sql, "Logging In");

$a_result = mysql_fetch_assoc($nResult);
$cnt = mysql_num_rows($nResult);

if ($cnt != "1") { 
	$error = 1;
	
	// header("Location: index.php?err=account+not+found");
	// exit();
} 

if ( $a_result[Password] == encrypt($a_form_vars[password],$a_form_vars[password]) && !$error) {
	if ($a_result[DateLastLogin]+(60*20) > time() && $a_result[LastSID] != "" && $a_form_vars['override'] != "true") {
		$_SESSION['already_logged_in'] = 1;
		$_SESSION['show_alert'] = 1;
		$_SESSION['alert_msg'] = "You are already logged in. This is either because 1) someone else is using this account or 2) you didn't log out properly. <br>
<br>
To login, check the &quot;Override login&quot; box and login again.";
		header("Location: ../admin/");
		exit();
		
	} else {
	
		$_SESSION['site'] = "";
		$_SESSION['logged_in'] = 1;
		$_SESSION['user_id'] = $a_result['ID'];
		$_SESSION['username'] = $a_result['Username'];
		$time = time();
		$sql = "UPDATE AdminUsers SET DateLastLogin='$time', LastSID='$ms_sid' WHERE ID='$a_result[ID]'";
		dbq($sql);
		
		setcookie("adminuser",$_SESSION['username'],time()+60*60*24*90,"/vp/admin/");
		header("Location: vp.php?ms_sid=$ms_sid&action=site_open");
		exit();
	}
} else {
	$error = 1;
}

if ($error) {
	$_SESSION['show_alert'] = 1;
	$_SESSION['alert_msg'] = "Incorrect username or password.";
	
	header("Location: ../admin/");
	exit();
}

?>