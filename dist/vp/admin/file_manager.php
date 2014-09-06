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


session_name("ms_sid");
session_start();
$ms_sid = session_id();

if (!isset($_SESSION[site]) || trim($_SESSION[site]) == "") {
	exit("There was an error determining which site is opened. <br><br>You may need to log out and log back in or reopen the current site by clicking on &quot;open order site&quot; at the top left of the VariaPrint&trade; Manager main screen.");
}
//exit($_SESSION[site]);
require_once("../inc/config.php");
require_once("../inc/functions-global.php");
if ($_SESSION["privilege"] == "owner") {
	require_once("inc/popup_log_check.php");
}

$mode = $a_form_vars['mode'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<?
print($header_content);
?>
<title>Choose File...</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<frameset rows="50,*" cols="*" framespacing="0" frameborder="NO" border="0">
  <frame src="file_manager_menu.php?mode=<? print($mode); ?>&folder=<? print($_GET['folder']); ?>&sm=<? print($_GET['sm']); ?>&obj=<? print($_GET['obj']); ?>" name="topFrame" scrolling="NO" noresize>
  <frame src="file_manager_flash.php?mode=<? print($mode); ?>&folder=<? print($_GET['folder']); ?>&sm=<? print($_GET['sm']); ?>&obj=<? print($_GET['obj']); ?>" name="mainFrame">
</frameset>
<noframes><body>

</body></noframes>
</html>
