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

	
	include("../inc/config.php");
	include("../inc/functions-global.php");
	
	if ($a_form_vars["action"] == "delete_order_site") {
		$sql = "UPDATE Sites SET Status='Deleted' WHERE ID='$a_form_vars[id]'";
		dbq($sql);
		
		header("Location: vp.php?action=site_open");
		
	} else {
		
		$sql = "SELECT Name FROM Sites WHERE ID='$a_form_vars[id]'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		
		$sitename = $a_result['Name'];
	}
	
?>
<html>
<head>
<title>Delete Order Site <? print($sitename);  ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="300" height="96%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="300" border="0" cellpadding="25" cellspacing="1" bgcolor="#000000">
      <tr>
        <td bgcolor="#FF9933"><p><span class="titlebold">WARNING: </span><span class="title">There
              is a $50 charge to restore an order site. </span></p>
            <p><span class="title">Are you sure you want to delete the order
                site &quot;<? print($sitename); ?>&quot;?</span></p>
            <form name="form1" enctype="multipart/form-data" method="post" action="">
              <input type="button" name="Submit2" value="Cancel" onClick="document.location='vp.php?action=site_open'">
              <input type="submit" name="Submit" value="Delete">
              <input type="hidden" name="id" value="<? print($a_form_vars['id']); ?>">
              <input type="hidden" name="action" value="delete_order_site">
<strong></strong>            </form>
        </td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
