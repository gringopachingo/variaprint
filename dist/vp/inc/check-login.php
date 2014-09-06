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


$sql = "SELECT * FROM Users WHERE ID='$_SESSION[user_id]' AND SiteID='$_SESSION[site]'";
$r_result = dbq($sql);
$a_result = mysql_fetch_assoc($r_result);
$logoutinterval = 3600;

//if within last 60 minutes, we're logged in
if ( $a_result['DateLastLogin'] > (time() - $logoutinterval) && $a_result['LastSID'] == $os_sid) {
	$_SESSION['logged_in'] = 1;
	$_SESSION['username'] = $a_result['Username'];
	$time = time();
	$sql = "UPDATE Users SET DateLastLogin='$time' WHERE ID='$_SESSION[user_id]' AND SiteID='$_SESSION[site]'";
	dbq($sql);
	$header_content .= "<meta http-equiv=\"refresh\" content=\"$logoutinterval;URL=vp.php?os_action=logout&logout_agent=inactivity&os_page_afterlogin=$_SESSION[os_page]&os_sid=$_SESSION[os_sid]&site=$_SESSION[site]\">";
} else {
	$_SESSION['logged_in'] = 0;
}


?>