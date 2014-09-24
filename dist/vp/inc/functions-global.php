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


$fv = $a_form_vars = array_merge($_GET, $_POST) ;


function _session_open ($save_path, $session_name) {
  global $sess_save_path, $sess_session_name;
       
  $sess_save_path = $save_path;
  $sess_session_name = $session_name;
  return(true);
}

function _session_close() {
  return(true);
}

function _session_read ($id) {
  global $sess_save_path, $sess_session_name;

  $sess_file = "$sess_save_path/sess_$id";
  if (file_exists($sess_file)) {
    if($fp = @fopen($sess_file, "r")){
		$sess_data = fread($fp, filesize($sess_file));
  	  	return($sess_data);
	} else {
	  	return("");
	}
  } else {
    return(""); // Must return "" here.
  }

}

function _session_write ($id, $sess_data) {
  global $sess_save_path, $sess_session_name;

  $sess_file = "$sess_save_path/sess_$id";
  	if ($fp = @fopen($sess_file, "w")) {
		return(fwrite($fp, $sess_data));
	} else {
	  	return("");
	}
}

function _session_destroy ($id) {
  global $sess_save_path, $sess_session_name;
       
  $sess_file = "$sess_save_path/sess_$id";
  if (file_exists($sess_file)) {
	  return(@unlink($sess_file));
  }
}

function _session_gc ($maxlifetime) {
	return true;
}

session_set_save_handler ("_session_open", "_session_close", "_session_read", "_session_write", "_session_destroy", "_session_gc");
/**/
//session_start();




function my_error_handler ($errno, $errstr, $errfile, $errline, $errcontent) { 
	if ($errno < 3) { 
	
		
		$msg = "An Error Occured!
       Error Number: $errno<br>
  Error Description: $errstr<br>
      Error In File: $errfile
	  Error On Line: $errline
	  
	           Site: $_SESSION[site]
	  "; 
	  mail($cfg_admin_email,"error on site",$msg);
	}
} 

set_error_handler("my_error_handler"); 



// function to set security of current page
function SecureServerOn($WantOn,$args="") {
  global $cfg_use_security, $cfg_secure_url, $cfg_secure_dir, $cfg_insecure_url, $cfg_insecure_dir;
  
  if ($cfg_use_security) {
  if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=="on") {
    if (!$WantOn) {
      $newURL = "http://" . $cfg_insecure_url . $cfg_insecure_dir . substr($_SERVER['REQUEST_URI'], strlen( $cfg_secure_dir)) . $args;
      Header("Location: $newURL");
	exit;
    } // !WantOn 
  } else { // https on
    if ($WantOn) {
	$newURL = "https://" . $cfg_secure_url . $cfg_secure_dir . substr($_SERVER['REQUEST_URI'], strlen( $cfg_insecure_dir)) . $args;
      Header("Location: $newURL");
      exit;
    } // WantOn
  } //https on
  }
}



function utf8ToISO_8859_1 ($source) { 
	// array used to figure what number to decrement from character order value 
	// according to number of characters used to map unicode to ascii by utf-8 
	$decrement[4] = 240; 
	$decrement[3] = 224; 
	$decrement[2] = 192; 
	$decrement[1] = 0; 
	
	// the number of bits to shift each charNum by 
	$shift[1][0] = 0; 
	$shift[2][0] = 6; 
	$shift[2][1] = 0; 
	$shift[3][0] = 12; 
	$shift[3][1] = 6; 
	$shift[3][2] = 0; 
	$shift[4][0] = 18; 
	$shift[4][1] = 12; 
	$shift[4][2] = 6; 
	$shift[4][3] = 0; 
	
	// Mapping override ***********
 	$a_chars[8734]="";//?
	$a_chars[8218]=chr(44); 	//"?";
	$a_chars[732]=chr(126);		//"?";  
	$a_chars[8364]=chr(128); 	// euro
	$a_chars[402]=chr(131);		//"?"  
	$a_chars[8222]=chr(132); 	//"?";
	$a_chars[8230]=chr(133);	//"?";
	$a_chars[8224]=chr(134); 	//"?";
	$a_chars[8225]=chr(135);	//"?";
	$a_chars[710]=chr(136); 	//"?";  
	$a_chars[8240]=chr(137); 	//"?";
	$a_chars[352]=chr(138); 
	$a_chars[8249]=chr(139);//"?";
	$a_chars[338]=chr(140); // "?";  
	$a_chars[65533]=""; //chr(141) ???? doesn't appear in unicode or iso ????
	$a_chars[381]=chr(142); 
	$a_chars[8216]=chr(145); //"?";
	$a_chars[8220]=chr(147); //"?";
	$a_chars[8217]=chr(146);//"?";
	$a_chars[8221]=chr(148);//"?";
	$a_chars[733]=chr(148);//"?";  
	$a_chars[8226]=chr(149); // bullet
	$a_chars[8211]=chr(150); // n dash
	$a_chars[8212]=chr(151); // m dash"?";
	$a_chars[8482]=chr(153);//"?";
	$a_chars[353]=chr(154); 
	$a_chars[8250]=chr(155);//"?";
	$a_chars[339]=chr(156); //"?";  
	$a_chars[382]=chr(158); 
	$a_chars[376]=chr(159); 
	$a_chars[730]=chr(186); //?
	$a_chars[8260]="/";
	$a_chars[64257]="fi";
	$a_chars[64258]="fl";
	$a_chars[8804]=""; // ?
	$a_chars[8805]=""; // ?
	$a_chars[8800]="";//?
	$a_chars[8721]=""; // ?
	$a_chars[960]="";  //?
	$a_chars[711]="";  // ?
	$a_chars[8719]="";// ?
	$a_chars[8706]="";//?
	$a_chars[729]=""; // ?
	$a_chars[8710]="";//?
	$a_chars[63743]="";//?
	$a_chars[937]="";  //?
	$a_chars[8776]=""; //?
	$a_chars[8730]=""; //?
	$a_chars[8747]=""; //?
	$a_chars[731]="";  //?
	$a_chars[9674]=""; //?
	$a_chars[305]="";  //?
	$a_chars[728]=""; //?
	
	/*
	foreach($a_chars as $uni=>$iso) {
		print("&#".str_pad($uni,"5",0,STR_PAD_LEFT)." :: ".$iso."<br>\n");
	}
	*/
	
	$pos = 0; 
	$len = strlen ($source); 
	$encodedString = ''; 
	while ($pos < $len) { 
		$asciiPos = ord (substr ($source, $pos, 1)); 
		if (($asciiPos >= 240) && ($asciiPos <= 255)) { 
			// 4 chars representing one unicode character 
			$thisLetter = substr ($source, $pos, 4); 
			$pos += 4; 
		}else if (($asciiPos >= 224) && ($asciiPos <= 239)) { 
			// 3 chars representing one unicode character 
			$thisLetter = substr ($source, $pos, 3); 
			$pos += 3; 
		} else if (($asciiPos >= 192) && ($asciiPos <= 223)) { 
			// 2 chars representing one unicode character 
			$thisLetter = substr ($source, $pos, 2); 
			$pos += 2; 
		} else { 
			// 1 char (lower ascii) 
			$thisLetter = substr ($source, $pos, 1); 
			$pos += 1; 
		}
		
		// process the string representing the letter to a unicode entity 
		$thisLen = strlen ($thisLetter); 
		$thisPos = 0; 
		$decimalCode = 0; 
		while ($thisPos < $thisLen) { 
			$thisCharOrd = ord (substr ($thisLetter, $thisPos, 1)); 
			if ($thisPos == 0) { 
				$charNum = intval ($thisCharOrd - $decrement[$thisLen]); 
				$decimalCode += ($charNum << $shift[$thisLen][$thisPos]); 
			} else { 
				$charNum = intval ($thisCharOrd - 128); 
				$decimalCode += ($charNum << $shift[$thisLen][$thisPos]); 
			}
			
			$thisPos++; 
		}
		
		if (isset($a_chars[$decimalCode])) {//$decimalCode > 127  && ord(chr($decimalCode)) != $decimalCode && 
			$encodedLetter = $a_chars[$decimalCode];
		} else {
			$encodedLetter = chr($decimalCode);
		}
		
	//	$encodedString .= "&#".str_pad($decimalCode,5,"0",STR_PAD_LEFT).";   ::   ". $encodedLetter; 
		$encodedString .= $encodedLetter; 
	}
	
	return $encodedString; 
}




function GetFieldsProc($a, &$f, $parent = "") {
	foreach ( $a as $k=>$v ) {
		if ( $v['tag'] == "TEXTFIELD" || $v['tag'] == "GRAPHIC" ) {
			$id = $v['attributes']['ID'];
			if ($v['tag'] == "GRAPHIC") {
				$v['attributes']['WIDTH'] = $parent['attributes']['WIDTH'];
				$v['attributes']['HEIGHT'] = $parent['attributes']['HEIGHT'];
			}
			$f[$id] = array('type' => $v['tag'], 'attributes' => $v['attributes'] ) ;
		}
		if ( is_array($v['children']) ) { 
			GetFieldsProc($v['children'], $f, $v); 
		} 
	}
}


function GetFields($a_itemTemplate) {
	$af = array();
	GetFieldsProc($a_itemTemplate, $af);
	return $af;
}



function make_input_row($field_node) {
	global $a_fields, $a_prefill, $cfg_sub_dir;
	
	$f_id = $field_node['attributes']['ID'];
	$value = $a_prefill[$f_id];
	
	$realtype = strtoupper($a_fields[$f_id]["type"]);
	
//	print($realtype."-<br>");
	
	// Determine type
	$type = strtoupper($field_node['attributes']['TYPE']);
	if ($type == "") { $type = "TEXTFIELD"; } 
	
	if ($type == "GRAPHIC" && $realtype =="TEXTFIELD") {
		$type = "TEXTFIELD";
	} elseif ($type != "GRAPHIC" && $realtype =="GRAPHIC") {
		$type = "GRAPHIC";
	}
	$v = $a_fields[$f_id];
	if (isset($field_node["attributes"]["LABEL"]) && trim($field_node["attributes"]["LABEL"]) != "") {
		$label = $field_node["attributes"]["LABEL"];
	} else {
		$label = $v['attributes']['NAME'];
	}
	
	switch ($type)  {
		case "TEXTFIELD":	$field = "<input type=\"text\" value=\"$value\" name=\"field_$f_id\" style=\"width: 350\">"; break;
		case "TEXTAREA":
			$rows = $field_node['attributes']['TEXT_ROWS'];
			$field = "<textarea name=\"field_$f_id\" style=\"width: 350\" rows=\"$rows\">$value</textarea>"; 
			break;	
		
		case "CHECKBOX" : /*
			// print_r($field_node);	
			$a_imprint_value = xml_find_node("CHECKBOX",$field_node['children']);
			$imprint_value = $a_imprint_value[0]['value'];
							
			if ( $value == $imprint_value || $value == "checked" ) { $checked = "checked";  } 
			$field = "
			<table cellpadding=0 cellspacing=0 border=0>
			<tr>
				<td height=\"25\" valign=\"top\"><input type=\"checkbox\" value=\"" .htmlentities($imprint_value) . "\" name=\"field_$f_id\" $checked></td>
				<td height=\"25\">&nbsp;&nbsp;</td>
				<td height=\"25\" class=\"text\" valign=\"top\">print &quot;" . htmlentities($imprint_value) . "&quot;</td>
			</tr>
			</table>
			";
			*/
			break;
						
		case "GRAPHIC" :
			
		//	print_r($field_node);//['attributes']);
		//	print_r($a_fields[$f_id]);
			$crop_w = $a_fields[$f_id]["attributes"]["WIDTH"];
			$crop_h = $a_fields[$f_id]["attributes"]["HEIGHT"];
			
			if ($field_node['attributes']['METHOD'] == "library") {
				$field = "
					<input class=\"button\" type=\"button\" name=\"choose\" value=\"Library Image...\" onClick=\"popupWin('".$cfg_sub_dir."imglib.php?title=".urlencode($label)."&obj=field_$f_id&site=$_SESSION[site]&os_sid=$_SESSION[os_sid]','library','width=600,height=450,centered=1,resizable=0,scrollbars=1')\">
					<input type=\"hidden\" name=\"field_$f_id\" value=\"$value\" >
				";
				if ($field_node['attributes']['ALLOWCUSTOM']=="true") {
					$field .= " &nbsp; or &nbsp;  
					<input class=\"button\" type=\"button\" name=\"choose\" value=\"Custom Image...\" onClick=\"popupWin('".$cfg_sub_dir."custimg.php?cropw={$crop_w}&croph={$crop_h}&fc=".$field_node['attributes']['FORCECROP']."&title=".urlencode($label)."&obj=field_$f_id&site=$_SESSION[site]&os_sid=$_SESSION[os_sid]','library','width=600,height=450,centered=1,resizable=0,scrollbars=1')\">";
				}
				$field .= "<input class=\"buttn\" type=\"button\" value=\"Clear\" onClick=\"document.forms[0].field_{$f_id}.value=''\">";
				
			} elseif ($field_node['attributes']['METHOD'] == "custom") {
				$field = "
					<input class=\"button\" type=\"button\" name=\"choose\" value=\"Choose Custom Image...\" onClick=\"popupWin('".$cfg_sub_dir."custimg.php?cropw={$crop_w}&croph={$crop_h}&title=".urlencode($label)."&obj=field_$f_id&site=$_SESSION[site]&os_sid=$_SESSION[os_sid]','library','width=600,height=450,centered=1,resizable=0,scrollbars=1')\">
					<input class=\"buttn\" type=\"button\" value=\"Clear\" onClick=\"document.forms[0].field_{$f_id}.value=''\">
					<input type=\"hidden\" name=\"field_$f_id\" value=\"$value\" >
				";
			
			} else { 
				$a_graphics = xml_find_node("GRAPHICS",$field_node['children']);
				if ( is_array($a_graphics[0]['children']) ) {
					foreach($a_graphics[0]['children'] as $graphic) {
						$name = utf8ToISO_8859_1($graphic['attributes']['NAME']);
					//	$id = $graphic['attributes']['ID'];
						$link = $graphic['attributes']['LINK'];
						if ($link == $value) { $sel = "selected"; } else { $sel = ""; }
						$opt .= "<option value=\"" . $link . "\" $sel>$name</option>\n";
					}
				}
				$field = "<select name=\"field_$f_id\" style=\"width: 350\">$opt </select>"; 
			}
			

		//	print_r($a_graphics);
			
			break;	
		
		case "PULLDOWN":	// use the same as radio
			$pd_node = xml_find_node("PULLDOWN_OPTIONS",$field_node['children']) ;
			$a_pd = explode("\n",utf8ToISO_8859_1($pd_node[0]['value']));
			if (is_array($a_pd)) {
				foreach($a_pd as $val) {
					if ($value == $val) { $sel = "selected"; } else { $sel = ""; }
					$opt .= "<option value=\"" . htmlentities($val) . "\" $sel>$val</option>\n";
				}
			}
			$field = "
			<select name=\"field_$f_id\" style=\"width: 350\">
				<option value=\"\">[Blank]</option>
				$opt 
			</select>"; 
			break;
			
		case "RADIO":
			
			$pd_node = xml_find_node("PULLDOWN_OPTIONS",$field_node['children']) ;
			$a_pd = explode("\n",utf8ToISO_8859_1($pd_node[0]['value']));//rtrim(
			if (is_array($a_pd)) {
				foreach($a_pd as $val) {
					if ($value == $val) { $sel = "checked"; } else { $sel = ""; }
					$rows .= "
					<tr>
						<td height=\"25\" valign=\"top\"><input name=\"field_$f_id\" type=\"radio\" value=\"" . htmlentities($val) . "\" $sel></td>
						<td height=\"25\">&nbsp;&nbsp;</td>
						<td height=\"25\" class=\"text\" valign=\"top\">$val</td>
					</tr>";
				}
			}
					
			$field = "
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td height=\"25\" valign=\"top\"><input name=\"field_$f_id\" type=\"radio\" value=\"\" $sel></td>
					<td height=\"25\">&nbsp;&nbsp;</td>
					<td height=\"25\" class=\"text\" valign=\"top\">[Blank]</td>
				</tr>
				$rows 
			</table>
			"; 
			
	} 
	$label = utf8ToISO_8859_1($label);
	$label_prefix_node = xml_find_node("PREFIXES",$field_node['children']) ;
	$prefixes = $label_prefix_node[0]['value'];
	if ( trim($prefixes) != "") {
		$a_label_prefix = explode("\n",$prefixes);
		foreach ($a_label_prefix as $prefix) {
			$opt .= "<option value=\"$prefix\">$prefix</option>";
		}
		$label = "
		<select name=\"prefixes_$f_id\" class=\"text\" onChange=\"setPrefix(this, '$f_id')\" style=\"width: 140\">
			<option value=\"none\">$label prefix...</option>
			<option value=\"\">[none]</option>
			$opt
		</select>
		";
	}

	
	

	$help_node = xml_find_node("HELP",$field_node['children']) ;
	$help_img = $help_node[0]['attributes']['IMAGE'];
	$help_text = $help_node[0]['value'];
	
	if ($help_img != "" || $help_text != "") {
		$help = "<a href=\"javascript:;\" onClick=\"popupWin('itemhelp.php?site=$_SESSION[site]&itemid=$_SESSION[cartitemid]&fieldid=$f_id&os_sid=$_SESSION[os_sid]','helpwin','width=350,height=450,resizable=yes,scrollbars=yes,centered=1')\"><img src=\"_sites/$_SESSION[site]/ifaceimg/icon-help.gif\"></a>";
	} else {
		$help = "";
	}
/**/

	
	$str_input .= "<tr><td class=\"text\" valign=\"top\">".$label."&nbsp;</td><td width=\"350\" class=\"text\" >$field </td><td valign=\"top\"> $help </td><td valign=\"top\"> $alert </td></tr>";

	
// ($label, $field, $help);
	return $str_input;
}




function strip($f, $s) {
	$counter = 0;
	while ( $fil = $f{$counter} ) {
		$s = ereg_replace($fil, "", $s);
		++$counter;
	}
	return $s;
}

function array_find_key_prefix($prefix, $aArray, $strip=0) {
	$aReturn = array();
	while (list($key, $val) = each($aArray)) {
		if (substr($key, 0, (strlen ($prefix))) == $prefix) {
			if ($strip) { $key = str_replace($prefix, "", $key); }
			$aReturn[$key] = $val;
		} 
	}
	return $aReturn;
}


function dbq($sql, $error = "") {
  	global $cfg_DB, $cfg_DB_connection; 
	$r_result = mysql_query($sql); 
	if ($r_result <= 0) {
		die("$sql
			<br><br><br>Database Error <strong>$error</strong>: " . mysql_error());
	}
	return $r_result;
}


function db_get_last_insert_id() {
	$sql = "SELECT LAST_INSERT_ID()";
	$r_result = dbq($sql);
	$aID = mysql_fetch_array($r_result);
	$last_insert_id = $aID[0];
	return $last_insert_id;
}

// ************************************************************************************************
// XML ********************************************************************************************
// ************************************************************************************************

function xml_get_children ($vals, &$i) {
    while (++$i < count($vals))  {
      	switch ($vals[$i]['type']) {
            case 'cdata':
                    $children[] = $vals[$i]['value'];
                    break;
            case 'complete':
                    $children[] = array('tag' => $vals[$i]['tag'], 'level' => $vals[$i]['level'], 'attributes' => $vals[$i]['attributes'], 'value' => $vals[$i]['value']) ;
                    break;
            case 'open':
                    $children[] = array('tag' => $vals[$i]['tag'], 'level' => $vals[$i]['level'], 'attributes' => $vals[$i]['attributes'],'children' => xml_get_children($vals, $i)) ;
                    break;
            case 'close':
                    return $children;
        }
    }
}

function xml_get_tree ($data, $readfile = false) {
    if ( $readfile )  { $data = implode("",file($data)); }
	$data = eregi_replace(">"."[[:space:]]+"."<","><",$data);
    $p = xml_parser_create();
    xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 0);
    xml_parse_into_struct($p, $data, $vals, $index); // removed & from $vals and $index
    xml_parser_free($p);
	
    $tree = array();  $i = 0;
    array_push($tree, array('tag' => $vals[$i]['tag'], 'level' => $vals[$i]['level'], 'attributes' => $vals[$i]['attributes'], 'children' => xml_get_children($vals, $i)));
	return $tree;
}

function xml_array_to_string($a,$removefromkey="") {	
	$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?" . ">\n<fields>";
	foreach ( $a as $k => $val ) {
		$id = str_replace($removefromkey,"",$k);
		$val = str_replace("<","&lt;",str_replace(">","&gt;",str_replace("&","&amp;",$val)));
		$val = str_replace("'","&apos;",str_replace("\"","&quot;",$val));
		$xml .= "\n\t<field id=\"$id\">" . $val . "</field>";
	}
	$xml .= "\n</fields>";
	return $xml;
}


function xml_make_children($a) {
	if ( is_array($a) ) {
		foreach ($a as $node) {
			$tag = strtolower($node['tag']);
			if ( is_array($node['attributes']) ) {
				foreach($node['attributes'] as $key => $val) {
					$val = str_replace("<","&lt;",str_replace(">","&gt;",str_replace("&","&amp;",$val)));
					$val = str_replace("'","&apos;",str_replace("\"","&quot;",$val));
					$attributes .= " " . strtolower($key) . "=\"" . $val . "\"";
				}
			}
			
			$val = str_replace("<","&lt;",str_replace(">","&gt;",str_replace("&","&amp;",$node['value'])));
			$val = str_replace("'","&apos;",str_replace("\"","&quot;",$val));
			if ($tag != "") $tree .= "<$tag" . "$attributes>" . $val . xml_make_children($node['children'])  . "</$tag>";
			$attributes = ""; unset($tag); unset($node); unset($key); unset($val);  
		}
	}
	return $tree;
}

function xml_make_tree($a) {
	$tree = xml_make_children($a);
	$tree = "<?" . "xml version=\"1.0\" encoding=\"iso-8859-1\"?" . ">\n" . $tree ;
	return $tree;	
}

function xml_update_value($path,$attribute,$val,$a_parent) {
	// usage : xml_update_value("parent/child1:child1_id/child2:child2_id","ATTRIBUTENAME","valuetoinsert",$a_xml_tree);
	// if $attribute = "CDATA" the value is inserted as such
	$r_val = true;
	$path = trim($path,"/");
	$a_path = explode("/",$path);
	$this_arr = $a_parent;
	$exists = true;
	$ct = count($a_path); $ctr = 1;
	foreach($a_path as $pathkey => $tag_and_id) {
		list($tag,$id) = explode(":",$tag_and_id); 
		$this_key = xml_find_node_key($tag,$id,$this_arr);
		
		if ( $this_key === "empty" ) {
			$exists = false;
			$this_key = count($this_arr) ;// + 1;
		} 
		
		if (!$exists && $attribute != "XMLNODE") {
			$eval_string .= "\$a_parent" . "$s_pathtoarray" . "[$this_key]['tag']" . " = \"$tag\";  \n";
			if ($id != "") { 
				$eval_string .= "\$a_parent" . "$s_pathtoarray" . "[$this_key]['attributes']['ID']" . " = \"$id\";  \n"  ; 
			}
		}
			
		if ($ctr == $ct) {
			if (!$exists && $attribute != "XMLNODE") {
				$s_pathtoarray_id = $s_pathtoarray . "[$this_key]['attributes']['ID']";
				$s_pathtoarray_tag = $s_pathtoarray . "[$this_key]['tag']";
				$eval_string .= "\$a_parent" . "$s_pathtoarray_id" . " = \"$id\";  \n\$a_parent" . "$s_pathtoarray_tag" . " = \"$tag\";  \n";
			}
			if ($attribute == "XMLNODE") { 
				$s_pathtoarray .= "[$this_key]";
			} elseif ($attribute == "CDATA") { 
				$s_pathtoarray .= "[$this_key]['value']" ; 
			} else { 
				$s_pathtoarray .= "[$this_key]['attributes']['$attribute']" ; 
			}
			
			$eval_string .= "\$a_parent" . "$s_pathtoarray" . " = \$val; \n";
			eval($eval_string) ;
			$r_val = false;
			break;
		} else {
			$this_arr = $this_arr[$this_key]['children'];
			$s_pathtoarray .= "[$this_key]['children']" ;
		}
		
		++$ctr;
	}
	
	if ($r_val) return false;
	else return $a_parent;
}

function xml_find_node_key($tag,$id=NULL,$arr) {
	$found = false;
	if ( is_array($arr) ) {
		foreach ( $arr as $k => $v ) {
			$att_id = $v['attributes']['ID'];
			$this_tag = $v['tag'];
			if ( (strtoupper($this_tag) == strtoupper($tag)) && ($id == NULL || $id == $att_id) ) { //
				$key = $k ;
				$found = true;
				break;
			}
		}
	} 
	
	if ($found) {
		return $key;
	} else {
		return "empty";
	} 
} 


function xml_find_node_path2($tag,$arr,$id,&$a_parent,&$rval) {
	foreach ( $arr as $v ) {

		if ( strtoupper($v['tag']) == strtoupper($tag) && $v['attributes']['ID'] == $id ) {
			// update what the current level is before we return the value
			$a_parent[$v['level']]['tag'] = $v['tag'];
			$a_parent[$v['level']]['id'] = $v['attributes']['ID'];

			$rval = $a_parent;

			return $rval;
			break;
		} elseif ( is_array($v['children'])) {

			// we have to first unset anything higher than the current level
			$cntr =  count($a_parent);
			while ( $cntr > $v['level'] ) {
				unset($a_parent[$cntr]);
				$cntr--;
			}
			
			// then update what the current level is
			$a_parent[$v['level']]['tag'] = $v['tag'];
			$a_parent[$v['level']]['id'] = $v['attributes']['ID'];
			
			xml_find_node_path2($tag,$v['children'],$id,$a_parent,$rval);
		}
	}
}

function xml_find_node_path($tag,$arr,$id) {
	if ( is_array($arr) ) {
		$a_parent = array();
		$rval = array();
		xml_find_node_path2($tag,$arr,$id,$a_parent,$rval);
	}
	
	$str_rval = NULL;
	$len = count($rval); $cntr = 1;
	if ( is_array($rval) ) {
		foreach($rval as $node) {
			if ($len != $cntr) { // This "if" is to make sure we don't return the actual object reference at the end of the path
				$id = $node['id']; $tag = $node['tag'];
				$str_rval .= "$tag";
				if ($id != "") $str_rval .= ":$id";
				$str_rval .= "/";
			}
			++$cntr;
		}
	}
	
	return $str_rval;
}


function xml_find_node($tag,$arr,$id="") {
	$found = false;
	if ( is_array($arr) ) {
		foreach ( $arr as $v ) {
			if ( strtoupper($v['tag']) == strtoupper($tag) && ($id == "" || $v['attributes']['ID'] == $id) ) {
				$r[] = $v ;
				$found = true;
			}
		}
		if ($found) {
			return $r;
		} else {
			return false;
		}
	} else {
		return false;
	}
}


function xml_move_node($arr,$from_path,$to_path,$place="bottom") {

	// Array, From path, To path, [top, bottom, afterto]
	
	// Get the node we are moving
	$a_move_node = xml_get_node_by_path($from_path,$arr);
	
	// Full array with just the node to move deleted
	$a_deleted = xml_delete_node($from_path,$arr);
	
	$a_to_path = explode("/",rtrim($to_path,"/"));
	
	// Get and split the parent and child paths -- $to_path_parent, $to_path_child
	$to_path_child = array_pop($a_to_path);
	foreach($a_to_path as $v) { $to_path_parent .= $v . "/"; }

	list($after_to_tag,$after_to_id) = explode(":",$to_path_child);
	
	// Parent node that we're modifying a child in
	$a_replace_node = xml_get_node_by_path($to_path_parent,$a_deleted);
	
			
	if (is_array($a_replace_node['children']))  {
		
		switch ($place) {
			case "bottom" : 
				$a_replace_node['children'][] = $a_move_node;
				$a_return = xml_update_value($to_path_parent,"XMLNODE",$a_replace_node,$a_deleted);
				break;
				
			case "top" : 
				$a_move[0] = $a_move_node;
				$a_new_order = array_merge($a_move,$a_replace_node[children]); // put $a_move_node at the top
				$a_replace_node['children'] = $a_new_order;
				$a_return = xml_update_value($to_path_parent,"XMLNODE",$a_replace_node,$a_deleted);
				break;
				
			case "after":
				$a_replace_node_children = $a_replace_node['children'];
				
				$after_node_key = xml_find_node_key($after_to_tag,$after_to_id,$a_replace_node_children);
				
				if ($after_node_key === "empty") {
					$a_replace_node['children'][] = $a_move_node;
					$a_return = xml_update_value($to_path_parent,"XMLNODE",$a_replace_node,$a_deleted);
				} else {
					$a_replace_node_children1 = array_slice($a_replace_node_children,0,$after_node_key+1);
					$a_replace_node_children2 = array_slice($a_replace_node_children,$after_node_key+1);
					$a_move[0] = $a_move_node;
					$a_new_order = array_merge($a_replace_node_children1,$a_move,$a_replace_node_children2);
					$a_replace_node['children'] = $a_new_order;
					$a_return = xml_update_value($to_path_parent,"XMLNODE",$a_replace_node,$a_deleted);
				}
		}

	} else {
		// Just update the value
		$a_replace_node['children'][] = $a_move_node;
		$a_return = xml_update_value($to_path_parent,"XMLNODE",$a_replace_node,$a_deleted);
	}
	
	return $a_return;
}

function xml_delete_node($path,$a_parent) {
	$r_val = true;
	$path = trim($path, "/");
	$a_path = explode("/",$path);
	$this_arr = $a_parent;
	$cntr = 1;
	$exists = true;
	$ct = count($a_path);
	foreach($a_path as $pathkey => $tag_and_id) {
		list($tag,$id) = explode(":",$tag_and_id); 
		$this_key = xml_find_node_key($tag,$id,$this_arr);
		if ( $this_key === "empty" ) {
			$exists = false;
		} 
		
		
		if ($cntr == $ct && $exists) {
			$eval_string .= "unset (\$a_parent" . $s_pathtoarray . "[$this_key]); ";//htmlentities()
			eval($eval_string) ;
			$r_val = false;
			break;
		} else {
			++$cntr;
			$this_arr = $this_arr[$this_key]['children'];
			$s_pathtoarray .= "[$this_key]['children']" ;
		}
	}
	
	if ($r_val) return false;
	else return $a_parent;
}


function xml_get_node_by_path($path,$this_arr) {
	if ($path != "") {
		$path = trim($path, "/");
		$a_path = explode("/",$path);
		$r_val = true;
		$ct = count($a_path); $ctr = 1;
		foreach($a_path as $pathkey => $tag_and_id) {
			list($tag,$id) = explode(":",$tag_and_id); 
			$this_key = xml_find_node_key($tag,$id,$this_arr);
									
			if ($this_arr[$this_key]['level'] == $ct) {
				$a_node = $this_arr[$this_key];
				$r_val = false;
				break;
			} else {
				$this_arr = $this_arr[$this_key]['children'];
			}
		}
	}
	if ($r_val) { 
		return false;
	} else {
		return $a_node;
	}
}

function xml_show_tree($tree, $depth) { 
	foreach($tree as $key => $value) { 
		if (is_array($value)) { 
			xml_show_tree($value, ++$depth); 
		} else { 
			for($i=0; $i<$depth; $i++) {  echo "&nbsp;&nbsp;&nbsp;&nbsp;";  } 
			echo "tree[\"$key\"]==\"$value\" \n<br>"; 
		} 
	} 
}


Class File {

	var $ERROR = "";
	var $BUFFER = -1;
	var $STATCACHE = array();
	var $TEMPDIR = '/tmp';
	var $REALUID = -1;
	var $REALGID = -1;

	function File ()
	{
		global $php_errormsg;
		return;
	}
	
	function get_error() {
		return $this->ERROR;
	}

	function clear_cache()
	{
		unset($this->STATCACHE);
		$this->STATCACHE = array();
		return true;
	}

	function is_sane($fileName = "", $must_exist = 0, $noSymLinks = 0, $noDirs = 0)
	{
		$exists = false;

		if(empty($fileName)) {	return false; }
		if($must_exist != 0)
		{
			if(!file_exists($fileName))
			{
				$this->ERROR = "is_sane: [$fileName] does not exist";
				return false;
			}
			$exists = true;
		}
		if($exists)
		{
			if(!is_readable($fileName))
			{
				$this->ERROR = "is_sane: [$fileName] not readable";
				return false;
			}

			if($noDirs != 0)
			{
				if(is_dir($fileName))
				{
					$this->ERROR = "is_sane: [$fileName] is a directory";
					return false;
				}
			}

			if($noSymLinks != 0)
			{
				if(is_link($fileName))
				{
					$this->ERROR = "is_sane: [$fileName] is a symlink";
					return false;
				}
			}

		} // end if exists

		return true;		
	}


//	**************************************************************

	function read_csvfile ($fileName = "" )
	{
		$contents = "";

		if(empty($fileName))
		{
			$this->ERROR = "read_file: No file specified"; 
			return false;
		}
		if(!$this->is_sane($fileName,1,0,1))
		{
			// Preserve the is_sane() error msg
			return false;
		}
		$fd = @fopen($fileName,"r");

		if( (!$fd) || (empty($fd)) )
		{
			$this->ERROR = "read_file: File error: [$php_errormsg]";
			return false;
		}
		
		while ($data = fgetcsv($fd, 3000) )
		{
			$contents[] = $data;
		}

		fclose($fd);

        return $contents;
	}
	
	
//	**************************************************************

	function read_file ($fileName = "" )
	{
		$contents = "";

		if(empty($fileName))
		{
			$this->ERROR = "read_file: No file specified"; 
			return false;
		}
		if(!$this->is_sane($fileName,1,0,1))
		{
			// Preserve the is_sane() error msg
			return false;
		}
		$fd = @fopen($fileName,"r");

		if( (!$fd) || (empty($fd)) )
		{
			$this->ERROR = "read_file: File error: [$php_errormsg]";
			return false;
		}

		$contents = fread($fd, filesize($fileName) );

		fclose($fd);

        return $contents;
	}

//	**************************************************************
//	Read a file via fgetss(), which strips all php/html
//	from the file.

	function strip_read ($fileName = "", $strip_cr = 0)
	{
		if(empty($fileName))
		{
			$this->ERROR = "strip_read: No file specified"; 
			return false;
		}
		if(!$this->is_sane($fileName,1,0,1))
		{
			// Preserve the error
			return false;
		}
		if($this->BUFFER > 0)
		{
			$buffer = $this->BUFFER;
		} else {
			$buffer = filesize($fileName);
		}

		$contents = "";

		$fd = @fopen($fileName,"r");

		if( (!$fd) || (empty($fd)) )
		{
			$this->ERROR = "strip_read: File error: [$php_errormsg]";
			return false;
		}
		while(!feof($fd))
		{
			$contents .= fgetss($fd,$buffer);
		}
		fclose($fd);
        return $contents;
	}

//	**************************************************************
	function write_file ($fileName,$Data)
	{
		$tempDir = $this->TEMPDIR;
		$tempfile   = tempnam( $tempDir, "cdi" );

		if(!$this->is_sane($fileName,0,1,1))
		{
			return false;
		}

		if (file_exists($fileName))
		{
			if (!copy($fileName, $tempfile))
			{
				$this->ERROR = "write_file: cannot create backup file [$tempfile] :  [$php_errormsg]";
				return false;
			}
		}

		$fd = @fopen( $tempfile, "a" );

		if( (!$fd) or (empty($fd)) )
		{
			$myerror = $php_errormsg;
			unlink($tempfile);
			$this->ERROR = "write_file: [$tempfile] access error [$myerror]";
			return false;
		}

		fwrite($fd, $Data);

		fclose($fd);

		if (!copy($tempfile, $fileName))
		{
			$myerror = $php_errormsg;   // Stash the error, see above
			unlink($tempfile);
			$this->ERROR = "write_file: Cannot copy file [$fileName] [$myerror]";
			return false;
		}

		unlink($tempfile);

		if(file_exists($tempfile))
		{
			// Not fatal but it should be noted
			$this->ERROR = "write_file: Could not unlink [$tempfile] : [$php_errormsg]";
		}
		return true;
	}

//	**************************************************************
	function copy_file ($oldFile = "", $newFile = "")
	{
		if(empty($oldFile))
		{
			$this->ERROR = "copy_file: oldFile not specified";
			return false;
		}
		if(empty($newFile))
		{
			$this->ERROR = "copy_file: newFile not specified";
			return false;
		}
		if(!$this->is_sane($oldFile,1,0,1))
		{
			// preserve the error
			return false;
		}
		if(!$this->is_sane($newFile,0,1,1))
		{
			// preserve it
			return false;
		}

		if (! (@copy($oldFile, $newFile)))
		{
			$this->ERROR = "copy_file: cannot copy file [$oldFile] [$php_errormsg]";
			return false;
		}

		return true;
	}

//	**********************************************

	function get_files ($root_dir, $fileExt = 'ALL_FILES')
	{
		$fileList = array();

		if(!is_dir($root_dir))
		{
			$this->ERROR = "get_files: Sorry, [$root_dir] is not a directory";
			return false;
		}

		if(empty($fileExt))
		{
			$this->ERROR = "get_files: No file extensions specified";
			return false;
		}

		$open_dir = @opendir($root_dir);

		if( (!$open_dir) or (empty($open_dir)) )
		{
			$this->ERROR = "get_files: Failed to open dir [$root_dir] : $php_errormsg";
			return false;
		}

		$fileCount = 0;

		while ( $file = readdir($open_dir))
		{
			if( (!is_dir($file)) and (!empty($file)) )
			{
				if($fileExt == 'ALL_FILES')
				{
					$fileList[$fileCount] = $file;
					$fileCount++;
				}
				else
				{
					if(eregi(".\.($fileExt)$",$file))
					{
						$fileList[$fileCount] = $file;
						$fileCount++;
					}
				}
			}
		}

		closedir($open_dir);
		return $fileList;

	}	// end get_files

	function is_owner($fileName, $uid = "")
	{
		if(empty($uid))
		{
			if($this->REALUID < 0)
			{
				$tempDir = $this->TEMPDIR;
				$tempFile = tempnam($tempDir,"cdi");
				if(!touch($tempFile))
				{
					$this->ERROR = "is_owner: Unable to create [$tempFile]";
					return false;
				}
				$stats = stat($tempFile);
				unlink($tempFile);
				$uid = $stats[4];
			}
			else
			{
				$uid = $this->REALUID;
			}
		}
		$fileStats = stat($fileName);
		if( (empty($fileStats)) or (!$fileStats) )
		{
			$this->ERROR = "is_owner: Unable to stat [$fileName]";
			return false;
		}

		$this->STATCACHE = $fileStats;

		$owner = $fileStats[4];
		if($owner == $uid)
		{
			return true;
		}

		$this->ERROR = "is_owner: Owner [$owner] Uid [$uid] FAILED";
		return false;
	}

	function is_inGroup($fileName, $gid = "")
	{
		if(empty($gid))
		{
			if($this->REALGID < 0)
			{
				$tempDir = $this->TEMPDIR;
				$tempFile = tempnam($tempDir,"cdi");
				if(!touch($tempFile))
				{
					$this->ERROR = "is_inGroup: Unable to create [$tempFile]";
					return false;
				}
				$stats = stat($tempFile);
				unlink($tempFile);
				$gid = $stats[5];
			}
			else
			{
				$gid = $this->REALGID;
			}
		}
		$fileStats = stat($fileName);
		if( (empty($fileStats)) or (!$fileStats) )
		{
			$this->ERROR = "is_inGroup: Unable to stat [$fileName]";
			return false;
		}

		$this->STATCACHE = $fileStats;

		$group = $fileStats[5];
		if($group == $gid)
		{
			return true;
		}

		$this->ERROR = "is_inGroup: Group [$group] Gid [$gid] FAILED";
		return false;
	}

	function get_real_uid()
	{
		$tempDir = $this->TEMPDIR;
		$tempFile = tempnam($tempDir,"cdi");
		if(!touch($tempFile))
		{
			$this->ERROR = "is_owner: Unable to create [$tempFile]";
			return false;
		}
		$stats = stat($tempFile);
		unlink($tempFile);
		$uid = $stats[4];
		$gid = $stats[5];
		$this->REALUID = $uid;
		$this->REALGID = $gid;
		return $uid;
	}

	function get_real_gid()
	{
		$uid = $this->get_real_uid();
		if( (!$uid) or (empty($uid)) )
		{
			return false;
		}
		return $this->REALGID;
	}

}	// end class File

function GetSiteAttributes($site, $mode = "") {
	if ($mode == "test") { $settings = "SettingsTmp"; } else {  $settings = "Settings"; }
	// RETRIEVE AND PARSE SITE SETTINGS
	$sql = "SELECT * FROM Sites WHERE ID='$site'";
	$nResult = dbq($sql);
	if ( mysql_num_rows($nResult) == 0 ) {  return NULL;  } 
	$aSite = mysql_fetch_assoc($nResult);
	$sSiteSettings = $aSite[$settings];
	if ( $sSiteSettings != "" ) {
		$aXMLTree = xml_get_tree($sSiteSettings);
	} 
	
	$aSiteSettingsRaw = array();
	$aSiteSettingsRaw = $aXMLTree[0][children];
	
	if ( is_array($aSiteSettingsRaw) ) {
		foreach ( $aSiteSettingsRaw as $k => $node ) {
			$pid = $node['attributes']['ID'];
			$aSiteSettings[$pid] = $node['value'] ;
		}
	}
	return $aSiteSettings;
}


function move_uploaded_files($path) {
	$error = $_FILES['userfile']['error'];	
	
	if ($error == 0) {
		$fileName = $_FILES['userfile']['name']; 
		//str_replace("#","_",str_replace("<","_",str_replace(">","_",str_replace(".php",".html",))));

		srand((double)microtime()*1000000); 
		$rand = rand(1000000,9999999999);
		$mkdir_cmd = CLI_MKDIR . " \"/tmp/".$rand."\"";		
		`$mkdir_cmd`;
		chdir("/tmp/".$rand);
		move_uploaded_file($_FILES['userfile']['tmp_name'], "/tmp/".$rand."/".$fileName); 
		
		$a_fname = explode(".",$fileName);
		if (strtoupper($a_fname[count($a_fname)-1]) == "ZIP") {
			`CLI_UNZIP "$fileName"`;
			`CLI_RM -rf /tmp/$rand/$fileName`;
		}
		$has_dir = false;
		$handle = @opendir("/tmp/".$rand . "/") or die("Directory \"$dir\"not found.");
		while($fileName = readdir($handle))
		{
			if (!is_dir($fileName) && $fileName != ".." && $fileName != "." && !ereg("^\.{1,}",$fileName) )
			{
				$new_name = str_replace("#","_",str_replace("<","_",str_replace(">","_",str_replace(".php",".html",$fileName))));
				if (file_exists($path.$new_name)) {
					$counter = 1;
					$tmp_fileName = $counter . "-" . $new_name ;
					while (file_exists($path.$tmp_fileName)) {
						++$counter;
						$tmp_fileName = $counter . "-" . $new_name;
					}
					$new_name = $tmp_fileName;
				}
				$mv_cmd = CLI_MV . " \"/tmp/".$rand."/".$fileName."\" \"".$path.$new_name."\"";
				`$mv_cmd`;
				
				chmod($path.$new_name, 0755);
			} elseif (is_dir($fileName) && $fileName != ".." && $fileName != "." && !ereg("^\.{1,}",$fileName)) {
				$has_dir = true;
			}
		}
		
		$rm_cmd = CLI_RM . " \"/tmp/".$rand."/\"";
		`$rm_cmd`;

		$success = true;
		return 1;
		
	} else {
		// There was an error
		switch ($error) {
			case "1": $err_msg = "The file was too big to upload."; break;
			case "2": $err_msg = "The file was too big to upload."; break;
			case "3": $err_msg = "It seems the transfer was interrupted."; break;
			case "4": $err_msg = "It doesn't look like a file was selected to upload."; break;
		}
		
		return "Error uploading file ($error). $err_msg";//  <a href=\"javascript:window.close();\">close</a>
	}
}


if (0==1) {
?>
<html></html>
<?php } ?>
