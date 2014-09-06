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

$_SESSION['tm'] = "";
$_SESSION['site_status'] = "" ;

if ($a_form_vars["delete_site"] == "Delete") {

	$site = $_SESSION['site'];
	header("Location: delete_site.php?id=".$site);
	$_SESSION[site] = "";
	exit();
} else {
	
	$submenu = "<span align=\"bottom\"><img src=\"images/intro-top.gif\"></span>";
	
	//		<option selected value=\"document.location='vp.php?action=site_properties'\">	&#8226; Edit Site Properties</option>
	
	$content = "<img src=\"images/intro-bottom.gif\">
	 <br><br><br><br><br>
	<a href=\"javascript:;\" onClick=\"popupWin('tutorial.html','tutorial','width=620,height=464,centered=1,toolbars=0,toolbars=0,scrolling=0,resizable=0');\"><img src=\"images/overview.gif\" border=\"0\"></a>

	";
}

?>