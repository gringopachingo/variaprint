<?// *******************************************************// // VariaPrint 1.0 web-to-print system//// Copyright 2001-2014 Luke Miller//// This file is part of VariaPrint, a web-to-print PDF personalization and // ordering system.// // VariaPrint is free software: you can redistribute it and/or modify it under // the terms of the GNU General Public License as published by the Free Software // Foundation, either version 2 of the License, or (at your option) any later // version.// // VariaPrint is distributed in the hope that it will be useful, but WITHOUT ANY // WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR // A PARTICULAR PURPOSE. See the GNU General Public License for more details.// // You should have received a copy of the GNU General Public License along with // VariaPrint. If not, see http://www.gnu.org/licenses/.// //// Forking, porting, updating, and contributing back to this project is welcomed.// // If you find any of this useful, let me know at the address below...//// https://github.com/lukedmiller/variaprint//// http://www.variaprint.com///// *******************************************************session_name("ms_sid");session_start();$ms_sid = session_id();require_once("../inc/config.php");require_once("../inc/functions-global.php");require_once("inc/popup_log_check.php");$_SESSION[site] = $_SESSION['site'];if ($_SESSION[site] == "" || $_SESSION['user_id'] == "" || !isset($_SESSION['user_id']) )  { 	print("		<script language=\"javascript\">			window.opener.location.reload(0) // location.reload(true)			window.close()		</script>	"); 	exit;}if ($a_form_vars['action'] == "save") {	if ($a_form_vars['site_name'] == "" ) { $a_form_vars['site_name'] = "Untitled Order Site"; }	$sql = "UPDATE Sites SET Name='" . addslashes($a_form_vars[site_name]) . "' WHERE ID='$_SESSION[site]'";	dbq($sql);		print("		<script language=\"javascript\">			window.opener.location.reload(0) // location.reload(true)			window.close()		</script>	"); 	exit;}$sql = "SELECT Name FROM Sites WHERE ID='$_SESSION[site]'";$r_result = dbq($sql);$a_result = mysql_fetch_assoc($r_result);$site_name = $a_result['Name'];?><html><head><?print($header_content);?><title>Change Site Name</title><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><link href="style.css" rel="stylesheet" type="text/css"></head><body background="images/bkg-groove.gif"><table width="100%" height="90%" border="0" cellpadding="0" cellspacing="0">  <tr>    <td align="center"><form name="form1" method="post" action="">        <input name="site_name" type="text" class="text" id="site_name" value="<? print($site_name); ?>">        <input name="Submit2" type="button" class="text" onClick="window.close()" value="Cancel">        <input name="Submit" type="submit" class="text" value="Save">        <input name="action" type="hidden" id="action" value="save">      </form></td>  </tr></table></body></html>