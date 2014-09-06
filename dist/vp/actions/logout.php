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


	$_SESSION['show_alert'] = 1;
	if ($_SESSION['require_login'] == 1) { $_SESSION['os_page'] = "login";  }
	$prefill_username = $_SESSION['username'];

	if ($a_form_vars['logout_agent'] == "inactivity") {
		$_SESSION['logout_agent'] = "inactivity";
		$_SESSION['alert_msg'] = "You have been logged out due to inactivity.";
	} else {
		$_SESSION['logout_agent'] = "";
		$_SESSION['alert_msg'] = "You were successfully logged out.";
	}

	$sql = "UPDATE Users SET LastSID='' WHERE ID='$_SESSION[user_id]' AND SiteID='$_SESSION[site]'";
	dbq($sql);

?>