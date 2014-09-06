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

	if (substr($_GET['file_in'],0,4) == "/tmp") {
		if (file_exists($_GET['file_in'])) {
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=$_GET[file_out]");
			readfile($_GET['file_in']);
			$error = false;
		//	unlink($_GET['file_in']);
		} else {
			$error = true;
		}	
	} else {
		$error = true;
	}
	
	if($error){
		session_name("ms_sid");
		session_start();
		$ms_sid = session_id();
				
		$msg = "
		There was an attempt to download \"$_GET[file_in]\" through download_file.php
		
		User: $_SESSION[username]
		Site: $_SESSION[site]
		IP: $_SERVER[REMOTE_ADDR]
		Agent: $_SERVER[HTTP_USER_AGENT]
		";
//		mail($cfg_admin_email,"hacking attempt",$msg);
		print(" ");
	}
	
?>
