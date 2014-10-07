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

	SecureServerOn(true);

	if ($_SESSION['logged_in'] == 1 ) {
		header("Location: vp.php?os_page=$_SESSION[os_page_afterlogin]&ossid=$ossid");
		exit();
	}

	
//	if (($_SESSION["os_page_afterlogin"]=="checkout" && $a_site_settings["RequireAccount"] != "checked") || $a_site_settings["CatalogRequireLogin"] == "No") {
		if ($_SESSION["os_page_afterlogin"]=="checkout" && $a_site_settings["CatalogRequireLogin"]=="No") {//!isset($a_site_settings["CatalogRequireLogin"]) || 
			$skiptitle = "<span class=\"subtitle\">Skip Login</span>";
			$skipbutton = "&nbsp;<br><a href=\"vp.php?site=$_SESSION[site]&os_action=skiplogin&ossid=$_SESSION[ossid]\">
			<input type=\"button\" value=\"Skip Login &raquo;\" class=\"button\" onClick=\"document.location='vp.php?site=$_SESSION[site]&os_action=skiplogin&ossid=$_SESSION[ossid]'\"></a>";
			$skipbox = "
				&nbsp; <strong>Advantages of having an account:</strong><br>
					&bull; Check order status online<br>
					&bull; Modify / cancel orders online<br>
					&bull; Reorder based on a previous order
			";
		}
//	}
	
	
	
	$showform = 0;
	$title = $a_site_settings['LoginTitle']; 
	$description = $a_site_settings['LoginText']; 
	$os_sidebar = iface_make_sidebar($title, $description);
	$line = iface_dottedline();
	$prefill_username = $_SESSION['username'];
	$line = iface_dottedline("596");

/*			If you have an account, log in below to retrieve your account information. 
			Otherwise, you can create a new account which will let you check your order status and login to retrieve information for future orders.
			<br><br>
	*/
	$content = "
	<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr><td width=\"500\" class=\"text\"><form action=\"vp.php\" method=\"post\">
			
			
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td nowrap> <span class=\"subtitle\">Login</span></td>
					<td nowrap>&nbsp;</td>
					<td nowrap> $skiptitle </td>
				</tr>
				<tr>
					<td class=\"text\" nowrap> Username<br><input value=\"$prefill_username\" type=\"text\" name=\"login_username\" style=\"width:280\"><br>
						Password<br> <input type=\"password\" name=\"login_password\" style=\"width:280\">
						<div class=\"text\">Forgot your password? <a href=\"vp.php?site=$_SESSION[site]&os_page=resetpswd&ossid=$_SESSION[ossid]\">Reset it</a>.</div></td>
					<td> <img src=\"images/spacer.gif\" width=\"10\" height=\"1\"> </td>
					<td class=\"text\">$skipbox &nbsp; </td>
				</tr>
				<tr>
					<td class=\"text\" align=\"right\" width=\"280\"> &nbsp;<br><input type=\"submit\" value=\"Login &raquo;\" class=\"button\"></td>
					<td> &nbsp;</td>
					<td class=\"text\" align=\"right\" width=\"280\"> $skipbutton </td>
				</tr>
			</table>	
			
			
			<input type=\"hidden\" name=\"os_action\" value=\"login\">
			</form>
			<br><br>
		</td></tr>
	</table>
	$line
	<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr><td width=\"500\" class=\"text\">
			<form action=\"vp.php\">
			<br>
			<span class=\"subtitle\">Create a New Account</span>
			
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td class=\"text\">Your First Name<br><input type=\"text\" name=\"create_first_name\" style=\"width:280\" tabindex=1 value=\"$a_form_vars[create_first_name]\"> </td>
					<td> <img src=\"images/spacer.gif\" width=\"10\" height=\"35\"> </td>
					<td class=\"text\">Create a Username<br><input type=\"text\" name=\"create_username\" style=\"width:280\" tabindex=5> </td>
				</tr>
				<tr>
					<td class=\"text\">Your Last Name<br><input type=\"text\" name=\"create_last_name\" style=\"width:280\" tabindex=2 value=\"$a_form_vars[create_last_name]\"> </td>
					<td> <img src=\"images/spacer.gif\" width=\"1\" height=\"35\"> </td>
					<td class=\"text\">Create a Password<br><input type=\"password\" name=\"create_password\" style=\"width:280\" tabindex=6> </td>
				</tr>
				<tr>
					<td class=\"text\">Your Email Address<br><input type=\"text\" name=\"create_email\" style=\"width:280\" tabindex=3 value=\"$a_form_vars[create_email]\"> </td>
					<td> <img src=\"images/spacer.gif\" width=\"1\" height=\"35\"> </td>
					<td class=\"text\">Verify Password<br><input type=\"password\" name=\"create_password2\" style=\"width:280\" tabindex=7> </td>
				</tr>
				<tr>
					<td class=\"text\">Your Phone Number<br><input type=\"text\" name=\"create_phone\" style=\"width:280\" tabindex=4 value=\"$a_form_vars[create_phone]\"> </td>
					<td> <img src=\"images/spacer.gif\" width=\"1\" height=\"35\"> </td>
					<td class=\"text\" align=\"right\">&nbsp;<br><input type=\"submit\" value=\"Create Account &raquo;\" class=\"button\"></td>
				</tr>
			</table>
			<input type=\"hidden\" name=\"os_action\" value=\"create_account\">
			
			<input type=\"hidden\" name=\"site\" value=\"$_SESSION[site]\">
			<input type=\"hidden\" name=\"ossid\" value=\"$_SESSION[ossid]\">
			
			</form>
			<br><br>
		</td></tr>
	</table>
	";


	$content = iface_make_box($content,600,100,1);
	$sPage = MakePageStructure($os_sidebar,$content);


?>
