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


session_name("mssid");
session_start();
$mssid = session_id();


require_once("../../inc/config.php");
require_once("../../inc/functions-global.php");
//require_once("../inc/popup_log_check.php");



if ($_GET['action'] == "send") {
	
	$message = $_GET['message'];

	$sender_email = $_GET['sender_email'];
	$to_email = "Tech Support <$cfg_admin_email>";
	$subject = "A Question From VP Manager";
	
	$headers  = "Return-Path: $sender_email\n";
	$headers .= "To: $to_email\n";
	$headers .= "MIME-version: 1.0\n";
	$headers .= "X-Mailer: VariaPrint Mailer\n";
	$headers .= "X-Sender: $sender_email\n";
	$headers .= "From: $sender_email\n";
	$headers .= "Content-type: text/plain\n";
	
	// and now mail it 
	mail($to_email, $subject, $message, $headers);
	
	exit("Question sent. We will contact you with an answer as soon as possible. <a href=\"javascript:top.close();\">Close window</a>.");
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?
print($header_content);
?>
<title>Get Help</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body class="text">
<a target="_blank" href="http://www.github.com/lukedmiller/variaprint/issues">http://www.github.com/lukedmiller/variaprint/issues</a>
</body>
</html>
