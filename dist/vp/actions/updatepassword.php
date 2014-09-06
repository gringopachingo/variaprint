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


if ($a_form_vars[password] == $a_form_vars[password2]) {
	if (strlen($a_form_vars[password]) > 5) {
		$newpassword = encrypt($a_form_vars[password],$a_form_vars[password]);
		$sql = "UPDATE Users SET Password='$newpassword' WHERE ID='$a_form_vars[userid]'";
		dbq($sql);
		$os_page = "login";
	} else {
		$_SESSION[show_alert] = 1;
		$_SESSION[alert_msg] = "Your new password must be at least 6 characters long. Please try again.";
	}
} else {
	$_SESSION[show_alert] = 1;
	$_SESSION[alert_msg] = "Your passwords didn't match. Please try again.";	
}

?>