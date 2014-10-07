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


	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	
	session_name("mssid");
	session_start();
	
	if (trim($_SESSION[site] ==""))
		exit("Error: No site selected. You may need to logout and log back in.");	

?><html>
<head>
<title>Add order site manager</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
function closeWindow() {
	top.close();
	window.opener.location.reload(false);
}
</script>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table cellpadding=0 cellspacing=0 border=0 align=center width="420" height="96%">
  <tr>
    <td>
      <div class="text">
        <form name="form1" method="get" action="">
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="500" height="420">
			  <param name="movie" value="shipping_address_list.swf?site=<?php print($_SESSION[site]); ?>">
			  <param name="quality" value="high">
			  <embed src="shipping_address_list.swf?site=<?php print($_SESSION[site]); ?>" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="500" height="300"></embed>
			</object>
        </form>
      </div>
    </td>
  </tr>
</table>
</body>
</html>
