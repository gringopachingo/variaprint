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

// Check to see if we have permission to edit this site
$sql = "SELECT * FROM Sites WHERE ID='$_SESSION[site]'";
$r_result = dbq($sql);
$a_result = mysql_fetch_assoc($r_result);

$mssid = session_id();
$logged_in = 0;

$_SESSION['privilege'] = "";
if ($a_result['MasterUID'] == $_SESSION['user_id'] || $_SESSION[username] == "master") {
	$_SESSION['privilege'] = "owner";
} else {
	$sql = "SELECT * FROM AdminUsers WHERE ID='$_SESSION[user_id]'";
	$r_result = dbq($sql);
	$a_result2 = mysql_fetch_assoc($r_result);
	
	$a_priv = xml_get_tree($a_result['VendorManagers']);
	if ( is_array($a_priv[0]['children']) ) {
		foreach($a_priv[0]['children'] as $node) {
			if ($node['attributes']['EMAIL'] == $a_result2['Email'] && $node['attributes']['ACCESS'] == "checked") {
				$_SESSION['privilege'] = "slave";
				if ($node['attributes']['INVOICES'] == "checked") { $_SESSION['privilege_order_browse'] = 1; } else { $_SESSION['privilege_order_browse'] = 0; }
				if ($node['attributes']['DOCKETS'] == "checked") { $_SESSION['privilege_dockets'] = 1; } else { $_SESSION['privilege_dockets'] = 0; }
				if ($node['attributes']['FILES'] == "checked") {  $_SESSION['privilege_impositions'] = 1; } else {  $_SESSION['privilege_impositions'] = 0; }				
			}
		}
	} 	

	if ( $_SESSION['privilege'] == "" && $_SESSION['site'] != "" ) { 
	//	header("Location: vp.php?action=site_open"); 
		exit("
		<html>
		<head>
		<link href=\"style.css\" rel=\"stylesheet\" type=\"text/css\">
		</head>
		<body>
		<div class=\"text\">Not enough privileges. <a href=\"javascript:;\" onclick=\"top.close()\">Close</a></div>
		</body
		</html>"); //Not enough privileges. <a href=\"vp.php?action=site_open\">Open a different site</a>.
	}
}

if ( (isset($_SESSION['site']) && trim($_SESSION['site']) != "") && ( $_SESSION['privilege'] == "owner" || $_SESSION['privilege'] == "slave") ) {
	$allow = true;
} else if (!isset($_SESSION['site']) ||  trim($_SESSION['site']) == "") {
	$allow = true;
} else {
	$allow = false;
}


// Check login status
$sql = "SELECT DateLastLogin,LastSID,Username FROM AdminUsers WHERE ID='$_SESSION[user_id]'";
$r_result = dbq($sql);
$a_result = mysql_fetch_assoc($r_result);
/*
$sql = "SELECT MasterUID FROM Sites WHERE ID='$_SESSION[site]'";
$r_result = dbq($sql);
$a_result2 = mysql_fetch_assoc($r_result);
*/
//$a_result2['MasterUID'] == $_SESSION['user_id']

//if within last 60 minutes, we're logged in
$logoutinterval = 60 * 60;
$logged_in = 0;

if ( $a_result['DateLastLogin'] > (time() - $logoutinterval) && $a_result['LastSID'] == $mssid && $allow){

/*
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    			// Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 	// always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");  	// HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");                          			// HTTP/1.0
*/
	$_SESSION['logged_in'] = $logged_in = 1;
	$_SESSION['username'] = $a_result['Username'];
	$time = time();
	$sql = "UPDATE AdminUsers SET DateLastLogin='$time' WHERE ID='$_SESSION[user_id]'";
	dbq($sql);
//	<meta http-equiv=\"refresh\" content=\"$cfg_logoutinterval;URL=../admin/\">
	$header_content .= "
	<script language=\"JavaScript\" type=\"text/JavaScript\">
		var minutes
		if (top.window.opener) {
			openerWin = top.window.opener.top		
			minutes = openerWin.minutes
			
			function logoutRefresh() {
				openerWin.minutes = 21;
				openerWin.logoutRefresh();
			}
			logoutRefresh();
		}
	</script>
	";
	
} else {
	$_SESSION['logged_in'] = $logged_in = 0;
	
//	print_r($_SESSION);
//	$_SESSION['show_alert'] = 1;
//	$_SESSION['alert_msg'] = "There was an error verifying that you are logged in.";
	
	print("Seconds until logout: " . $a_result['DateLastLogin'] - (time() - $logoutinterval) . "<br>" . 
	"CurrentSID: " .  $mssid . "<br>" . 
	"LastSID: " . $a_result['LastSID'] . "<br>" . 
	"DateLastLogin: " . $a_result['DateLastLogin'] . "<br>" .
	"UserID: " . $_SESSION[user_id] . "<br>" .
	date("Y m d G:i:s", $a_result['DateLastLogin']) . 
	"<br>" . date("Y m d G:i:s", time() - $logoutinterval)  .
	"<br>" . $logoutinterval ."<br>"
	);
	print("
	<script language=\"JavaScript\" type=\"text/JavaScript\">
		if (window.opener) {  window.opener.top.location.reload(true); } 
		top.close()
	</script>
	");
// location.reload(true) 	
//	header("Location: ../admin/");
	exit("You were logged out.");
}


/*

<?xml version="1.0" encoding="iso-8859-1"?>

<suppliers><supplier id="1" email="" access="checked" notify="checked" invoices="checked" dockets="checked" files="checked"></supplier><supplier id="2" email="-" access="checked" notify="checked" invoices="" dockets="checked" files="checked"></supplier><supplier id="3" email="" access="checked" notify="checked" invoices="" dockets="" files=""></supplier></suppliers>

access="checked" notify="checked" invoices="checked" dockets="checked" files="checked"

*/

?>
