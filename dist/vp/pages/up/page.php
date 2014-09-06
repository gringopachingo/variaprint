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

	SecureServerOn(true);

	$form_method = "post";	

	$enc_code = encrypt($_SESSION[resetpasswordcode], $_SESSION[resetpasswordcode]);
	$sql = "SELECT ID,Username FROM Users WHERE Password='$enc_code'";
	$r_result = dbq($sql);
	
	if (mysql_num_rows($r_result) == 1) {
		$a_result = mysql_fetch_assoc($r_result);
		$username = $a_result['Username'];	
		$resetmode = true;
		$content = "
		<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
			<tr><td width=\"500\" class=\"text\">
				<table cellpadding=0 cellspacing=0 border=0>
					<tr>
						<td class=\"text\" nowrap>
							Username: <b>$username</b>
							<br>
							<br>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class=\"text\" nowrap>
							New Password<br>
							<input size=50 value=\"$prefill_username\" type=\"password\" name=\"password\">
							<br>
							<br>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class=\"text\" nowrap>
							Confirm New Password<br>
							<input size=50 value=\"$prefill_username\" type=\"password\" name=\"password2\">
						</td>
						<td class=\"text\"> &nbsp;<br><input type=\"submit\" value=\"Set New Password &raquo;\" class=\"button\"></td>
					</tr>
				</table>	
							
				<input type=\"hidden\" name=\"os_action\" value=\"updatepassword\">
				<input type=\"hidden\" name=\"userid\" value=\"$a_result[ID]\">
				
			</td></tr>
		</table>
		";
	} else {
		$content = "<span class=\"text\">There was an error. This code may have expired. Try resetting your password again.</span>";
		
		$resetmode = false;
	}
	
	
	$title = "Set New Password"; 
	$description = "Enter a new password for your account.";
	$os_sidebar = iface_make_sidebar($title, $description);
	
	

	$content = iface_make_box($content,600,100,1);
	$sPage = MakePageStructure($os_sidebar,$content);

?>
