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

	if ($_SESSION['logged_in'] == 0) {
		header("Location: vp.php?os_page_afterlogin=account&os_page=login&ossid=$_SESSION[ossid]&site=$_SESSION[site]");
	}

	
	
	// SAVING HAPPENS HERE *****************************************************
	if ($a_form_vars['action'] == "saveaccount") {
		$_SESSION[alert_msg] = "";
		// print_r($a_form_vars);
		$a = $a_form_vars;
		//$a[old_password] != "" && 
		if ( $a[new_password] != "" && $a[new_password2] != "") {
			$sql = "SELECT Password FROM Users WHERE ID='$_SESSION[user_id]' AND SiteID='$_SESSION[site]'";
			$r_result = dbq($sql);
			$a_result = mysql_fetch_assoc($r_result);
			
			
			// trying to update password
			$oldpass = $a_result['Password'];
			$newpass = encrypt($a['old_password'],$a['old_password']);
			if ($oldpass == $newpass && $a['new_password'] == $a['new_password2'] ) {
				$update = "Password='" . encrypt($a['new_password'],$a['new_password']) . "'";
				$_SESSION[alert_msg] = "Your password was successfully updated.<br><br>";
				$_SESSION[show_alert] = 1;
			} else {
				$_SESSION[alert_msg] = "Your passwords were entered incorrectly. Could not update password. Please try again. (0) -- $oldpass / $newpass<br><br>";
				$_SESSION[show_alert] = 1;
			}
			
		} elseif ($a[new_password] != "" || $a[new_password2] != "") {
			// we need all the passwords filled in to update password
			$_SESSION[alert_msg] = "Your passwords were entered incorrectly. Could not update password. Please try again. (1)<br><br>";
			$_SESSION[show_alert] = 1;
		}
		
		$a_fields[FirstName] = "firstname";
		$a_fields[LastName] = "lastname"; 
		$a_fields[Address1] = "address"; 
		$a_fields[Address2] = "address2";
		$a_fields[Email] = "email";
		$a_fields[City] = "city";
		$a_fields[State] = "state";
		$a_fields[Zip] = "zip"; 
		$a_fields[Phone] = "phone";
		$a_fields[Country] = "country";
		
		foreach ($a_fields as $k=>$k2) {
			++$cntr; 
			if ( $a[$k2] != "" ) {
				if ($update != "") {$update .= ",";}
				$update .= "$k='". addslashes($a[$k2]) . "' ";
			}
		} 
		
		$sql = "UPDATE Users SET $update WHERE ID='$_SESSION[user_id]' AND SiteID='$_SESSION[site]'";
		dbq($sql);

		$_SESSION[alert_msg] .= "Your profile was successfully updated.<br><br>";
		$_SESSION[show_alert] = 1;
	}


?>
