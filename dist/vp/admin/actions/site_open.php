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

$_SESSION['tm'] = "";
$_SESSION['site_status'] = "" ;

$formOptions = "name=\"form1\" target=\"_top\" method=\"get\" action=\"vp.php\"";
$showform = 0; // $aUser = getAdminUserRecord($HTTP_COOKIE_VARS[adminlogin]);
// LIST OF SITES
$sql = "SELECT ID,Name FROM Sites WHERE MasterUID='$_SESSION[user_id]' AND Status!='Deleted' ORDER BY Name"; 
$nResult = dbq($sql);

if ( mysql_num_rows($nResult) > 0) { 
	//ondblclick=\"document.forms.form1.submit()\"
	$sitelist = "
	<span class=text>Your Sites</span><br>
	<select  onClick=\"document.forms[0].opensite.focus()\" name=\"site\"  size=\"8\" style=\"width:250\" >\n";
	while ( $aSite = mysql_fetch_assoc($nResult) ) {
		$sitelist .= "<option value=\"$aSite[ID]\" >$aSite[Name]</option>
		";
	}
	$sitelist .= "</select>	";
	$havesites = true;
} else {
	$havesites = false;
}

exit(print_r($_SESSION));
$sql = "SELECT Username FROM AdminUsers WHERE ID='$_SESSION[user_id]'"; 
$nResult = dbq($sql);
$a_result = mysql_fetch_assoc($nResult);
$username = $a_result['Username'];

if ($username != "master") { 
	$sql = "SELECT ID,Name,MasterUID FROM Sites WHERE VendorManagers LIKE '%=\"$username\"%' ORDER BY Name"; 
	$nResult = dbq($sql);
	while ( $aSite = mysql_fetch_assoc($nResult) ) {
		if ($aSite['MasterUID'] != $_SESSION['user_id']) {
			$sitelist2 .= "<option value=\"$aSite[ID]\" >$aSite[Name]</option>\n";
			$haveother = true;
		}
	}
} else {
	$sql = "SELECT ID,Name FROM Sites ORDER BY ID"; //
	$nResult = dbq($sql);
	while ( $aSite = mysql_fetch_assoc($nResult) ) {
		if ($aSite['MasterUID'] != $_SESSION['user_id']) {
			$sitelist2 .= "<option value=\"$aSite[ID]\" >$aSite[Name]   ($aSite[ID])</option>\n";
			$haveother = true;
		}
	}
}

if ($haveother) {
	//ondblclick=\"document.forms.form1.submit()\"
	if ($username=="master") {
		$size = "16"; $width = "590";
	} else {
		$size = "8"; $width = "250"; $title = "Other Sites";
	}
	
	$sitelist2 = "
	<span class=text>$title</span><br>
	<select name=\"site\"  size=\"$size\" style=\"width:$width\" >
	$sitelist2
	</select>
	<input type=\"hidden\" name=\"user_id\" value=\"$_SESSION[user_id]\">
	<input type=\"hidden\" name=\"action\" value=\"home\">
<br><br>
	<input type=\"submit\" value=\"Open\">
	";
}

$action = "home";
$sel_menu = "";

if ($username=="master") {
	$content =
	"
	<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
	  <tr>
		<td> <strong class=\"title\">Select an order site to open/edit</strong><br></td>
		<td> &nbsp;</td>
	  </tr>
	  <tr> 
		  <td width=\"300\" valign=\"top\">
				<form name=\"sites\" target=\"_top\" method=\"get\" action=\"vp.php\">
				$sitelist2
				</form>
		</td>
	  </tr>
	</table>
	";
} elseif ($havesites || $haveother) {
	if (!$havesites) {	
		$sitelist = "<img src=\"images/createyourown.gif\"> <br><br>";
	}
	
	$content =
	"
<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr>
    <td> <strong class=\"title\">Select an order site to open/edit</strong><br></td>
    <td> &nbsp;</td>
  </tr>
  <tr> 
    <td width=\"300\" valign=\"top\">
	<form >
		$sitelist
      <br><strong><a href=\"javascript:;\" onClick=\"popupWin('site_new.php','','width=450,height=300,centered=1')\" class=\"text\">Create a new order site</a></strong>
		<br><br>
		<input type=\"hidden\" name=\"user_id\" value=\"$_SESSION[user_id]\">
		<input type=\"hidden\" name=\"action\" value=\"home\">
		
        <input type=\"submit\" value=\"Delete\" name=\"delete_site\">
        <input type=\"submit\" value=\"Open\" name=\"opensite\">

	</form>
      </td>
	  <td width=\"300\" valign=\"top\">
	<form name=\"othersites\" target=\"_top\" method=\"get\" action=\"vp.php\">
		$sitelist2
	</form>
    </td>
  </tr>
</table>
";

} else {
	$content = "
	<img src=\"images/firstsite-message.gif\" border=\"0\" usemap=\"#Map1\">
	 <br><br><br><br><br>
	<a href=\"javascript:;\" onClick=\"popupWin('tutorial.html','tutorial','width=620,height=464,centered=1,toolbars=0,toolbars=0,scrolling=0,resizable=0');\"><img src=\"images/overview.gif\" border=\"0\"></a>
	<map name=\"Map1\">
	  <area shape=\"rect\" coords=\"175,72,425,97\" href=\"javascript:;\" onClick=\"popupWin('site_new.php','','width=450,height=300,centered=1')\">
	</map>
";

/*	<div class=\"text\">No order sites set up yet. 
	<a href=\"javascript:;\" onClick=\"popupWin('site_new.php','','width=450,height=300,centered=1')\">Click here</a> to create one.<br></div>\n
*/
}



?>