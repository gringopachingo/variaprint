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

/*
	
	if ($_SESSION['logged_in'] == 1) {
		header("Location: vp.php?os_page=$_SESSION[os_page_afterlogin]&os_sid=$os_sid");
		exit();
	}
*/

	SecureServerOn(true);
	
	$showform = 0;
	$title = "Reset Password"; 
	$description = "Enter your email address or username ";
	$os_sidebar = iface_make_sidebar($title, $description);
	$line = iface_dottedline();

	$prefill_username = $_SESSION['username'];
		
	$content = "
	<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr><td width=\"500\" class=\"text\"><form action=\"vp.php\">
			Enter your username or email address below and we'll email you instructions on how to set a new password.
			<br><br>
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td class=\"text\" nowrap>Username or Email Address<br><input size=50 value=\"$prefill_username\" type=\"text\" name=\"username\"></td>
					<td class=\"text\"> &nbsp;<br><input type=\"submit\" value=\"Reset Password &raquo;\" class=\"button\"></td>
				</tr>
			</table>	
			
			<div class=\"text\">Remembered your password? <a href=\"vp.php?site=$_SESSION[site]&os_page=login&os_sid=$_SESSION[os_sid]\">Login</a>.</div>
			
			<input type=\"hidden\" name=\"os_action\" value=\"resetpassword\">
			<input type=\"hidden\" name=\"site\" value=\"$_SESSION[site]\">
			<input type=\"hidden\" name=\"os_sid\" value=\"$_SESSION[os_sid]\">
			</form>
			
			<br><br>
		</td></tr>
	</table>
	";


	$content = iface_make_box($content,600,100,1);
	$sPage = MakePageStructure($os_sidebar,$content);


?>