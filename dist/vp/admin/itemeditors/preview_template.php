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
	
	require_once("../../inc/config.php");
	require_once("../../inc/functions-global.php");
	require_once("../../inc/iface.php");
	require_once("../../inc/functions_pdf.php");
	require_once("../inc/functions.php");
	if (!$_SESSION["privilege_items_template"]) {
		require_once("../inc/popup_log_check.php");
	}
	
	

	$item_id = $_SESSION['item_id'];
	
	if ($item_id == "") {
		exit("Error: no item id. Please contact your system administrator.");
	}
	
	if (!isset($a_form_vars['page'])) {
		$a_form_vars['page'] = "input";
	}

	$page = $a_form_vars['page'];


	//	LOCAL FUNCTIONS  **************************************************************************************
	function get_prefill ($a_item) {
		$xml_prefill_sets = $a_item['TestData'];
		$a_prefill_tree = xml_get_tree($xml_prefill_sets);

		if ( is_array($a_prefill_tree[0]['children']) ) {
			foreach ($a_prefill_tree[0]['children'] as $v) {
				$id = $v['attributes']['ID']; $val = $v['value'];
				$a_prefill[$id] = $val ;
			}
		}
		
		return $a_prefill;
	}



	//	MAIN PROGRAM  **************************************************************************************
	if ($page == "input") {
		$sql = "SELECT TestTemplate,FieldSections,TestData FROM Items WHERE ID='$item_id'";
		$r_result = dbq($sql);
		$a_item = mysql_fetch_assoc($r_result);
		$a_prefill = get_prefill($a_item);
		
		$a_itemTemplate = xml_get_tree($a_item['TestTemplate']);
		$a_fields = GetFields($a_itemTemplate);
		
		if (!is_array($a_itemTemplate[0]['children'])) {
			print("Could not load template ($item_id).<br><br>");
		}
		
		$a_field_sections = xml_get_tree($a_item['FieldSections']);
		
		if ( !is_array($a_field_sections[0]['children']) ) {
			print("Could not load field sections ($item_id).<br><br>");
		
		} else {
			foreach ($a_field_sections[0]['children'] as $section_node) {
				$id = $section_node['attributes']['ID'];
	
				
				$group = "";
				$name = "";
				
				// Process fields in this section
				if ( is_array($section_node['children']) ) {
					foreach ($section_node['children'] as $field_node) {
					
						$f_id = $field_node['attributes']['ID'];
						
						if (isset($a_fields[$f_id])) {
							// Get input row
							$group .= make_input_row($field_node);
						}
					//	$a_field_used[$f_id] = "yes";
					}
				} // end fields for section
				
				if  ($group != "") {
					if ($id == "NOTSET") { $name = "Other information"; } else { $name = $section_node['attributes']['NAME']; }
					$str_input .= "<tr><td colspan=\"4\" class=\"subhead\"><strong>" . 
						$name . "</strong></td></tr>" . $group ; 
					$str_input .= "<tr><td colspan=\"4\"><img src=\"images/spacer.gif\" height=\"3\" width=\"1\"></td></tr>";
				}
			}
		}
		/*
		$str_other = "";
		foreach ( $a_fields as $k=>$v ) {
			$id = $v['attributes']['ID'];
			
			if ($a_field_used[$id] != "yes" && $id != "" ) {
				$a_f_node = xml_get_node_by_path("GROUPS/GROUP:NOTSET/FIELD:$id/", $a_field_sections);
				if ( !is_array($a_f_node)) { 
					$a_f_node['attributes']['ID'] = $id; $a_f_node['tag'] = "FIELD";
				}
				$str_other .= make_input_row($a_f_node);
			}
		}
		if ($str_other != "") {
			$str_input .= "<tr><td colspan=\"4\" class=\"subhead\"><strong>Other information</strong></td></tr>" . 
			$str_other;
		}*/
		
			
		// Make buttons at the bottom
		$buttons .= "
		<input class=\"button\"  type=\"submit\" value=\"Save &amp; Preview &raquo;\">
		";
		
		$content = "
			<table border=0 cellpadding=6 cellspacing=0 width=590>
				$str_input
				<tr>
					<td></td>
					<td colspan=\"3\" height=\"50\">$buttons </td>
				</tr>
			</table>
			<input type=\"hidden\" name=\"ms_sid\" value=\"$ms_sid\">
			<input type=\"hidden\" name=\"item_id\" value=\"$item_id\">
			<input type=\"hidden\" name=\"page\" value=\"render1\">";
	
	} elseif ($page == "render1") {
		$a_fields = array_find_key_prefix("field_", $a_form_vars, true);
		
		if (count($a_fields) > 0) {
		//	foreach ($a_fields as $k=>$v) {
		//		$a_xml = xml_update_value("FIELDS/FIELD:$k", "CDATA", $v, $a_xml);
		//	}
			$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?"."><fields>";
			foreach ($a_fields as $k=>$v) {
				$xml .= "<field id=\"$k\">".str_replace("<","&lt;",str_replace(">","&gt;",$v))."</field>";
			}
			$xml .= "</fields>";
		
//<?xml version="1.0" encoding="iso-8859-1"? >
//<fields><field id="5">MMMM</field><field id="6">asdf asdf  asdfa    sdfa </field><field id="12">this should be wide</field><field id="14">Luke Miller</field><field id="15">5.99 si! ¡</field><field id="16">adfssdfa</field></fields> testing
		//	$xml = addslashes(xml_make_tree($a_xml));
			$xml = addslashes($xml);
			
			$sql = "UPDATE Items SET TestData='$xml' WHERE ID='$item_id'";
			dbq($sql);
		}

		$content = "
		<table border=0 height=94% align=center><tr><td>
			Rendering Preview...
		</td></tr></table>
		
		<script>  document.location='preview_template.php?item_id=$item_id&page=render2'; </script>
		";
		
	} elseif ($page == "render2") {
		
			
		// MAIN PROGRAM			***************
		$sql = "SELECT Name,TestTemplate,TestData FROM Items WHERE ID='$item_id'";
		$nResult = dbq($sql);
		if ( mysql_num_rows($nResult) == 0 ) {  
			print("Error. No template found."); exit;
		}
		$aItem = mysql_fetch_assoc($nResult);
		
		$a_prefill = get_prefill($aItem);
		
			
		/* Make the PDF */		
		$site_id = $_SESSION[site_id];
		
		$aItem['TestTemplate'] = utf8ToISO_8859_1($aItem['TestTemplate']);
		
		$buf = pdf_create($aItem['TestTemplate'], $a_prefill);
				
		// Output generated image
		if ( ($scale = (400 / $pdf_vars['pagewidth'])*100) > 130 ) { $scale = 130; }
		
		$img = pdf_rasterize( $buf, $scale ); 
							
		$tmp_dir = "/www/tmp/";
		srand((double)microtime()*1000000); $rand = rand(1000000,9999999999);
		
		$File = new File;
		$img_file = $tmp_dir . "tmpraster_" . $rand . ".jpg";
		if ( file_exists($img_file) ) { unlink($img_file) ; }
		if(!$File->write_file( $img_file, $img )) { $error = 1; }
		
		if ($error != 1) { 
			$content = "<script>document.location='preview_template.php?item_id=$item_id&page=preview&i=$rand'; </script>";
		} else {
			exit("error");
		}
		
		
		
		
	} elseif ($page == "preview") {
		$content	= "<img src=\"$GLOBALS[cfg_tmp_dir]tmpraster_" . $a_form_vars[i] . ".jpg\">";
		$content	= iface_add_drop_shadow($content,"#FFFFFF") ;
		
		$content .= "<br><br>
		<a href=\"preview_template.php?page=input&item_id=$item_id\" class=\"text\">edit</a> &nbsp; &nbsp; 
		<a href=\"preview_template.php?page=render1&item_id=$item_id\" class=\"text\">refresh</a> &nbsp; &nbsp; 
		<a href=\"javascript:window.close();\" class=\"text\">close</a>
		";
		
		$content = "
		<table border=0 height=94% align=center><tr><td>
			$content
		</td></tr></table>

		";
		
	} else {
		exit("wrong page selected.");
	}
	
	$page = NULL;

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Preview Template</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
	function popupWin(u,n,o) { // v3
		var ftr = o.split(","); 
		nmv=new Array(); 
		for (i in ftr) {
			x=ftr[i]; 
			p=x.split("=");
			t=p[0]; v=p[1]; 
			nmv[t]=v;
		}
		if (nmv['centered']=='yes' || nmv['centered']==1) {
			nmv['left']=(screen.width-nmv['width'])/2 ; 
			nmv['top']=(eval(screen.height-nmv['height']-72))/2 ; 
			nmv['left'] = (nmv['left']<0)?'0':nmv['left'] ; 
			nmv['top'] = (nmv['top']<0)?'0':nmv['top']; 
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
	
	function findObj(n, d) { //v4.0
	  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
		d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
	  if(!x && document.getElementById) x=document.getElementById(n); return x;
	}

	var last_prefix = ''
	function setPrefix(obj,id) {
		if (obj.value != 'none') {
			f_obj = findObj('field_' + id);
			label = obj[obj.selectedIndex].value
			currval = f_obj.value
			if (last_prefix == '') {
				for (i=0; i<obj.length; i++) {
					if (currval.substr(0,obj[i].value.length) == obj[i].value) {
						repstr = new RegExp(obj[i].value);			
						currval = currval.replace(repstr,'')
					}
				}
			} else {
				repstr = new RegExp(last_prefix);			
				currval = currval.replace(repstr,'')
			}

			f_obj.value = label + currval
			last_prefix = label
		}
	}
</script>
</head>

<body>
<form action="preview_template.php"><? print($content); ?></form>
</body>
</html>
