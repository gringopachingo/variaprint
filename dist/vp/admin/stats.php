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

if($_GET["p"] == "crazymonkey") {
        include("../inc/config.php");
        include("../inc/functions-global.php");
        
        $sql = "SELECT Username,Email,DateCreated,DateLastLogin FROM AdminUsers ORDER BY DateLastLogin DESC";
        $r_result = dbq($sql);
        
        $list = "<table cellpadding=\"5\">";
        $list .= "<tr><td colspan=\"3\"><b>".mysql_num_rows($r_result)." accounts</b></td></tr>\n";
        while ($arr = mysql_fetch_assoc($r_result)) {
                $list .= "<tr><td>$arr[Username]</td><td>$arr[Email]</td><td>".date("M d Y H:i",$arr[DateCreated])."</td><td>".date("M d Y H:i",$arr[DateLastLogin])."</td></tr>\n";
        }
        $list .= "<table>";
} else {
        $list = "denied";
}
?><html>
<head>
<title>Stats</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<? print($list); ?>
</body>
</html>
