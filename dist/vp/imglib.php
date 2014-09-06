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

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    			// Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 	// always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");  	// HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");                          			// HTTP/1.0

	require_once("inc/popup-header.php");
	$background = $bgcolor;
	
	session_name("os_sid");
	session_start();
	$os_sid = session_id();
	
	require_once("inc/config.php");	
	require_once("inc/functions-global.php");	
//	require_once("inc/functions.php");	
//	require_once("inc/iface.php");

	$sql = "SELECT ImageLibraries FROM Sites WHERE ID='$_SESSION[site]'";
	$res = dbq($sql);
	$a_res = mysql_fetch_assoc($res);
	$img_lib = xml_get_tree($a_res["ImageLibraries"]);

	if (!isset($a_form_vars["library"]) || trim($a_form_vars["library"]) == "") {
		$a_form_vars["library"] = $img_lib[0]['children'][0]['attributes']['ID'];
	}

	
	$pd = "";
	if (is_array($img_lib[0]["children"])) 
	{
		foreach ($img_lib[0]["children"] as $key=>$node) 
		{
			if ($node["attributes"]["ID"] == $a_form_vars['library']) { $sel = "selected"; } else { $sel = "";}
			$pd .= "<option value=\"".$node["attributes"]["ID"]."\" $sel>".htmlentities($node["attributes"]["NAME"])."</option>";
		}
	}
	$pd = "<select style=\"width: 130\" name=\"library\" id=\"library\" onChange=\"top.document.location='imglib.php?obj=$a_form_vars[obj]&title=".urlencode($a_form_vars['title'])."&os_sid=$os_sid&site=$_SESSION[site]&library='+this.value\">$pd</select>";

	if (is_array($img_lib[0]["children"])) {
		foreach($img_lib[0]["children"] as $key_libs=>$lib_node) {
			$row = 1;
			$row_list = "";
			if ($lib_node["attributes"]["ID"] == $a_form_vars["library"]) {
				if (is_array($lib_node["children"]) && count($lib_node["children"]) > 0) {
					foreach($lib_node["children"] as $key_file=>$file_node) {
						if ($row%2) { $bgcolor = "#eeeeee"; } else { $bgcolor = "#cccccc"; }
						$id = 	$file_node["attributes"]["ID"];
						$row_list .= "
							<td class=\"text\" valign=bottom>
								<img border=1 src=\"icon.php?img=images/".urlencode($file_node["value"])."\"><br>
								".$file_node[attributes][DESCRIPTION]."<br><br>
								<input type=\"button\"  value=\"Choose...\" onClick=\"choose('".$file_node[value]."')\">
							</td>
						" ;
						
						if ($row%5 == 0) {
							$file_list .= "<tr height=1><td colspan=5><hr size=1 noshade></td></tr><tr>$row_list</tr>";
							$row_list = "";
						} 
						
						++$row;
					}
					if ($row_list != "") {
						$file_list .= "<tr height=1><td colspan=5><hr size=1 noshade></td></tr><tr height=110>$row_list</tr><tr height=1><td colspan=5><hr size=1 noshade></td></tr>";
					} else {
						$file_list .= "<tr height=1><td colspan=5><hr size=1 noshade></td></tr>";
					}
					
					$file_list = "
					<br><br>
					<table width=560 cellpadding=0 cellspacing=0>
					$file_list
					</table><br><br><br><br>
					";
				} else {
					$file_list = "<br><br>
					<span class=\"text\">No images in Library </span>";
				}
			}
		}
	} 	
//	print($file_list);

	$content = "<b class=\"text\">Choose library</b> ".$pd.$file_list;

?>
<html>
<head>
<title>Choose Image ... <? print($a_form_vars['title']); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<? include("inc/style_sheet.php"); ?>
<script language="JavaScript">
function choose(img) {
	top.opener.document.forms[0].<? print($a_form_vars['obj']); ?>.value = img;
	top.close();
}
</script>
</head>

<body bgcolor="#eeeeee">
<? print($content); ?>
</body>
</html>
