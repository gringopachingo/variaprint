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

	//session_save_path("/www/tmp");
	session_name("mssid");
	session_start();
	$mssid = session_id();
	
	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	if ($_SESSION["privilege"] == "owner") {
		require_once("inc/popup_log_check.php");
	}
	
	
	$lib_dir = $cfg_base_dir . "_sites/" . $_SESSION["site"] . "/images/library" ; // Base directory
	if (!file_exists($lib_dir)) {
		mkdir($lib_dir);
	}

	$sql = "SELECT ImageLibraries FROM Sites WHERE ID='$_SESSION[site]'";
	$res = dbq($sql);
	$a_res = mysql_fetch_assoc($res);
	$img_lib = xml_get_tree($a_res["ImageLibraries"]);
	
	if (!isset($a_form_vars["library"]) || trim($a_form_vars["library"]) == "") {
		$a_form_vars["library"] = $img_lib[0]['children'][0]['attributes']['ID'];
	}
	
	if ($a_form_vars["action"]=="deletelibrary" && $a_form_vars['confirmed'] == 1) {
		$found = false;
		if (is_array($img_lib[0]["children"])) {
			foreach($img_lib[0]["children"] as $key=>$node) {
				if ($node["attributes"]["ID"] == $a_form_vars["library"]) {
					unset($img_lib[0]["children"][$key]);
					$found = true;
					break;
				}
			}
		}
		if ($found) {
			$img_lib_xml = addslashes(xml_make_tree($img_lib));
			$sql = "UPDATE Sites SET ImageLibraries='$img_lib_xml' WHERE ID='$_SESSION[site]'";
			dbq($sql);
			
			print ("
			<script language=\"javascript\">
				top.document.location = \"image_library.php\"
			</script>
			");
			exit;
		}
		
	} elseif ($a_form_vars["action"]=="addlibrary") {
		$i = 0;
		foreach($img_lib[0]["children"] as $k=>$node) {
			if ($i<$node["attributes"]["ID"]) { $i = $node["attributes"]["ID"]; }
		}
		$i++;
		$tmp["tag"] = "library";
		$tmp["children"] = "";
		$tmp["value"] = "";
		$tmp["attributes"]["NAME"] = "Untitled Library";

		$tmp["attributes"]["ID"] = $i;
		$img_lib[0]["children"][] = $tmp;
		
		$img_lib_xml = addslashes(xml_make_tree($img_lib));
		$sql = "UPDATE Sites SET ImageLibraries='$img_lib_xml' WHERE ID='$_SESSION[site]'";
		dbq($sql);
		
		print ("
		<script language=\"javascript\">
			top.document.location = \"image_library.php?library=$i\"
		</script>
		");
		exit;
	} elseif ($a_form_vars["action"]=="addfiles") {
		
		
		$tmpid = 0;
		if (is_array($img_lib[0]["children"])) {
			foreach($img_lib[0]["children"] as $key=>$node) {
				if ($node["attributes"]["ID"] == $a_form_vars["library"]) {
					$this_library_key = $key;
					if (is_array($node['children'])) {
						foreach ($node['children'] as $k=>$filenode) {
							if ($filenode['attributes']['ID'] > $tmpid) {
								$tmpid = $filenode['attributes']['ID'] ;
							}
							$a_files[$filenode["value"]] = true;
						}
					}
					break;
				}
			}
		}
		++$tmpid;
				
		$files_explode = explode("><",$a_form_vars["files"]);
		if (is_array($files_explode)) {
			foreach($files_explode as $k=>$filename) {
				if ($a_files[$filename] != true) {
					$f1 = explode("/",$filename);
					$f2 = explode(".",$f1[count($f1)-1]);
					$f3 = str_replace("_"," ",$f2[0]);
					$tmp_img["attributes"]["DESCRIPTION"] = $f3;
					$tmp_img["attributes"]["ID"] = $tmpid;
					++$tmpid;
					$tmp_img["value"] = $filename;
					$tmp_img["tag"] = "FILE";
					$img_lib[0]["children"][$this_library_key]["children"][] = $tmp_img;
				}
			}
		}
		$img_lib_xml = addslashes(xml_make_tree($img_lib));
		$sql = "UPDATE Sites SET ImageLibraries='$img_lib_xml' WHERE ID='$_SESSION[site]'";
		dbq($sql);
	
	} 
	
	
	if ($a_form_vars['action1'] == "save" || $a_form_vars['action1'] == "deleteimages") {
		$descriptions = array_find_key_prefix("description_",$a_form_vars,1);
		
	//	print_r($descriptions);
		
		$selected = array_find_key_prefix("checkbox_",$a_form_vars,1);

		if (is_array($img_lib[0]["children"])) {
			foreach($img_lib[0]["children"] as $key_libs=>$lib_node) {
				if ($lib_node["attributes"]["ID"] == $a_form_vars["library"]) {
					if (is_array($lib_node['children'])) {
						
						if (trim($a_form_vars['libname']) == "") {
							$a_form_vars['libname'] = "Untitled Library";
						}
						$refresh_name = false;
						if ($img_lib[0]["children"][$key_libs]['attributes']['NAME'] != $a_form_vars['libname']) {
							$img_lib[0]["children"][$key_libs]['attributes']['NAME'] = $a_form_vars['libname'] ;
							$refresh_name = true;
						}
						
						foreach ($lib_node['children'] as $k=>$filenode) {
							$id = $filenode['attributes']['ID'];
							$img_lib[0]["children"][$key_libs]['children'][$k]['attributes']['DESCRIPTION'] = $descriptions[$id];
							if ($selected[$id] == 1 && $a_form_vars['action1'] == "deleteimages") {
								unset($img_lib[0]["children"][$key_libs]["children"][$k]);
							}
						}
					}
					break;
				}
			}
		}
		
		$img_lib_xml = addslashes(xml_make_tree($img_lib));
		$sql = "UPDATE Sites SET ImageLibraries='$img_lib_xml' WHERE ID='$_SESSION[site]'";
		dbq($sql);
		if ($refresh_name) {
			print ("
			<script language=\"javascript\">
				top.document.location = \"image_library.php?library=$a_form_vars[library]\"
			</script>
			");
			exit;
		}
	}


	if (is_array($img_lib[0]["children"])) {
		foreach($img_lib[0]["children"] as $key_libs=>$lib_node) {
			$row = 0;
			if ($lib_node["attributes"]["ID"] == $a_form_vars["library"]) {
				if (is_array($lib_node["children"]) && count($lib_node["children"]) > 0) {
					foreach($lib_node["children"] as $key_file=>$file_node) {
						if ($row%2) { $bgcolor = "#eeeeee"; } else { $bgcolor = "#cccccc"; }
						$id = 	$file_node["attributes"]["ID"];
						$file_list .= "\n<tr height=48 bgcolor=\"$bgcolor\"><td><input type=\"checkbox\" value=\"1\" name=\"checkbox_".$id."\"></td><td class=\"text\" height=48><img border=1 src=\"../icon.php?mode=admin&img=images/".urlencode($file_node["value"])."\"</td><td class=\"text\">".
						$file_node["value"]
						."</td><td><input type=\"text\" style=\"width: 200\" name=\"description_".$id."\" value=\"".$file_node["attributes"]["DESCRIPTION"]."\"></td></tr>" ;
						++$row;
					}
					
					$file_list = "
					<br>
					<table width=450 cellpadding=0 cellspacing=0>
						<tr>
							<td>
								<input type=\"submit\" onClick=\"document.forms[0].action1.value='deleteimages'\" value=\"Delete Selected\"> 
							</td> 
							<td width=\"80\">
							&nbsp;
							</td>
							<td>
								<input type=\"textfield\" name=\"libname\" value=\"".$lib_node["attributes"]["NAME"]."\">
								<input type=\"hidden\" name=\"action1\" id=\"action1\" value=\"save\">
								<input type=\"hidden\" name=\"library\" id=\"library\" value=\"".$a_form_vars["library"]."\">
							</td>
							<td>
								<input type=\"submit\" onClick=\"document.forms[0].action1.value='save'\" value=\"Save\"> 
							</td>
						</tr>
					</table>
					<br>
					<table width=450 cellpadding=5 cellspacing=0><tr ><td class=\"text\">&#8730;&nbsp;<a href=\"javascript:;\" onclick=\"selectall()\" class=\"text\">All</a></td><td class=\"text\"></td><td class=\"text\"><strong>File Name</strong></td><td class=\"text\"><strong>Description</strong></td></tr>$file_list</table>";
				} else {
					$file_list = "<br>
					<span class=\"text\">No images in Library. <br><br>Click &quot;Add images to library...&quot; above.</span>";
				}
			}
		}
	} 	
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Image Libraries</title>
<script language="JavaScript" type="text/JavaScript">
function selectall() {
	el = document.forms[0].elements
	with (document.forms[0]) {
		for (var i = 0; i < elements.length; i++) {
			if ( elements[i].name.slice(0,9) == 'checkbox_') {
				if (c) {
					elements[i].checked = false
				} else {
					elements[i].checked = true
				}
			}
		}
	}
	if (c) { c = false } else { c = true } 
}

function popupWin(u,n,o) { // v3
	var ftr = o.split(","); 
	nmv=new Array(); 
	for (i in ftr) {
		x=ftr[i]; 
		t=x.split("=")[0]; 
		v=x.split("=")[1]; 
		nmv[t]=v;
	}
	if (nmv['centered']=='yes' || nmv['centered']==1) {
		nmv['left']=(screen.width-nmv['width'])/2 ; 
		nmv['top']=(screen.height - nmv['height']-72)/2 ; 
		nmv['left'] = (nmv['left']<0)?'0':nmv['left'] ; 
		nmv['top']=(nmv['top']<0)?'0':nmv['top']; 
		delete nmv['centered'];
	}
	o=""; 
	var j=0; 
	for (i in nmv) {
		o+=i+"="+nmv[i]+"\,";
	} 
	o=o.slice(0,o.length-1);
	window.open(u,n,o);
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="16" topmargin="5" marginwidth="16" marginheight="5">
<form>
<? print($file_list); ?>
</form>

</body>
</html>
