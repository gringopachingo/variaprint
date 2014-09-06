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

	$_SESSION['require_login'] = 0;

		
	// MAKE DECISIONS BASED ON SITE SETTINGS
	switch ($a_site_settings['HomePageStyle']) {

		case "Standard":
			if ($a_site_settings[HomePageLogin] == "Y")
				SecureServerOn(true);
			else 
				SecureServerOn(false);

			$hptitle		= $a_site_settings['HomePageTitle'];
			$hptext 		= ereg_replace("\n", "<br>", $a_site_settings['HomePageIntroText']) ;
/*			
			$bgcolor 		= $a_site_settings['HomePageBkgColor'] ;
			$bannerbgcolor 	= $a_site_settings['HomePageBannerBkgColor'];
			$menubarcolor 	= $a_site_settings['HomePageMenuBgColor'];
			$menubevel		= $a_site_settings['HomePageBevelMenuBar'];
*/
			$hpcontent = "
            <table width=\"98%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">
              <tr><td width=\"370\"><div class=\"title\">$hptitle</div> <div class=\"text\">$hptext</div>
			  </td></tr></table>" ;
			
			
			// Login **************
			if ($a_site_settings[HomePageLogin] == "Y" && $_SESSION['logged_in'] != "1") {
				$hpcontent .= iface_dottedline() . "<table width=\"98%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">
					  <tr><td width=\"370\" class=\"text\">
							<span class=\"subtitle\">Login</span>
					<table cellpadding=0 cellspacing=0 border=0>
						<tr>
							<td class=\"text\" nowrap> Username<br><input value=\"$_SESSION[username]\" type=\"text\" name=\"login_username\" size=\"20\"></td>
							<td class=\"text\"> Password<br> <input type=\"password\" name=\"login_password\" size=\"20\"></td>
							<td class=\"text\"> &nbsp;<br><input type=\"submit\" value=\"Login &raquo;\" class=\"button\"></td>
						</tr>
					</table>
				<!--	Create a new account		//-->
					<input type=\"hidden\" name=\"os_action\" value=\"login\">
			  </td></tr></table>
			" ;
			} elseif ($a_site_settings[HomePageLogin] == "Y" && $_SESSION['logged_in'] == "1") {
				$hpcontent .= iface_dottedline() . "<table width=\"98%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">
					  <tr><td width=\"370\" class=\"text\">
							<span class=\"subtitle\">You are currently logged in as &quot;$_SESSION[username]&quot;.</span>
			  
			  
			  </td></tr></table>
			" ;
			}
			
			
			
			$quickmenu = "<table width=\"98%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">
				  <tr><td width=\"150\">
				  	<div class=\"subtitle\">Quick menu ...</div>
					<div class=\"text\">&bull; <a href=\"$script_name?site=$_SESSION[site]&os_page=catalog&os_sid=$_SESSION[os_sid]\" class=\"text\">Place an order</a></div>
					<div class=\"text\">&bull; <a href=\"$script_name?site=$_SESSION[site]&os_page=catalog&os_sid=$_SESSION[os_sid]\" class=\"text\">View catalog</a></div>
					<div class=\"text\">&bull; <a href=\"$script_name?site=$_SESSION[site]&os_page=account&accounttab=0&os_sid=$_SESSION[os_sid]\" class=\"text\">Check order status</a></div>
					<div class=\"text\">&bull; <a href=\"$script_name?site=$_SESSION[site]&os_page=account&accounttab=0&ordertab=1&os_sid=$_SESSION[os_sid]\" class=\"text\">Resume saved order</a></div>
					<div class=\"text\">&bull; <a href=\"$script_name?site=$_SESSION[site]&os_page=account&accounttab=0&os_sid=$_SESSION[os_sid]\" class=\"text\">View order history</a></div>
					<div class=\"text\">&bull; <a href=\"$script_name?site=$_SESSION[site]&os_page=account&accounttab=2&os_sid=$_SESSION[os_sid]\" class=\"text\">Edit account profile</a></div>
				</td></tr></table>";
			
			$mainbox = iface_make_box($hpcontent,400,'');
			$menubox = iface_make_box($quickmenu,160,'');
			
			$custombutton = false;
			
			if ($a_site_settings["HomeCatalogButton"] != "") {
				$link = "_sites/" . $_SESSION[site] . "/images/" . $a_site_settings["HomeCatalogButton"];
				if (file_exists($link)) {
					$custombutton = true;
					$button = "<a href=\"$script_name?site=$_SESSION[site]&os_page=catalog&os_sid=$_SESSION[os_sid]\"><img src=\"$link\" border=\"0\"></a>";
				} 
			} 
			
			if (!$custombutton) {
				$link = "_sites/" . $_SESSION[site] . "/ifaceimg/view_catalog.gif";
				$button = "<a href=\"$script_name?site=$_SESSION[site]&os_page=catalog&os_sid=$_SESSION[os_sid]\"><img src=\"$link\" border=\"0\"></a>";
			}

			$sPage = "<table width=\"768\" height=\"175\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
			<tr><td width=\"167\" valign=\"top\" align=\"center\">" . $button . "</td>
				<td width=\"400\" valign=\"top\">" . $mainbox . "</td>
				<td width=\"40\" align=\"right\" class=\"menu\">&nbsp;</td>
				<td width=\"161\" valign=\"top\">" . $menubox   .   "</td>
			  </tr></table>";
			break;

		case "Link": 
			if ($_SERVER['HTTP_REFERRER'] == $a_site_settings['hpURL'] || $a_form_vars['os_page'] != "home") {
				header("Location: vp.php?os_page=catalog&os_sid=$os_sid");
			} else {
				header("Location: ". strip("\t\r\n", $a_site_settings['hpURL']) );
			}
			break;
		
		case "NoPage":
			header("Location: vp.php?os_page=catalog&os_sid=$os_sid");
			exit();
	}

?>