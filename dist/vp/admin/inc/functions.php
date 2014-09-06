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

function startTable($width = "") {
	if ($width != "") { $width = " width=\"$width\" "; }
	return "<table $width cellpadding=0 cellspacing=0 border=0 height=25><tr>";
}
function endTable() {
	return "</tr></table>";
}

function formatFormInput($aParent = "", $aChild = "", $firstpass, $obj) { //, $objname = "", $value = ""

//	global $_SESSION;
	$objname = $aChild[attributes][ID] ;
	$inputType = $aChild[attributes][INPUTTYPE] ;
	$fieldType = $aChild[tag] ; 	
	$name = $aChild[attributes][LABEL] ;
	$label = $aChild[attributes][LABEL]; //ereg_replace(" ","&nbsp;",$aChild[attributes][LABEL]) ;
	$aInputOptionsTmp = explode(":",$aChild['attributes'][INPUTOPTIONS]);
	$aInputOptions = ""; while ( list($k,$v) = each($aInputOptionsTmp) ) { list($k2,$v2) = explode(";",$v); $aInputOptions[$k2] = $v2; }
	if ( !isset($aChild[attributes][STYLE]) ) { $style = "text"; } else { $style = $aChild[attributes][STYLE]; }
	$prefill = $obj->attribute_data[$objname] ;//['prefill'];
	$value = $aChild[attributes][VALUE];
		
	if ( $inputType != "" ) {
		
		$counter = 1;
		
		switch ($inputType) {
			case "FontStyle" :
				$formInput =  "<td class=\"$style\" width=\"200\">$label</td> <td>&nbsp;&nbsp;</td><td> <input onChange=\"set_save(false)\" type=\"hidden\" name=\"$objname\" value=\"" . 
				$prefill . "\" style=\"width:50\"><a href=\"javascript:;\" onClick=\"popupWin('edit_font_style.php?css=' + escape(document.forms[0]." . 
				$objname . ".value) + '&obj=$objname&label=" . ereg_replace("&nbsp;","+",$label) . 
				"','$objname','left=100,top=50,width=450,height=350,centered=1')\" class=\"text\">edit font style</a>...</td>" ; 
				break;
			
			case "Note" :
				$formInput = "<td class=\"$style\" width=\"550\" nowrap>$label</td>";
				break;
			
			case "Color" :
				$formInput =  "<td class=\"$style\" width=\"200\">$label</td> <td>&nbsp;&nbsp;</td><td> 
				<input onChange=\"set_save(false);update_color('$objname')\"  type=\"text\" name=\"$objname\" value=\"$prefill\" style=\"width:70\"> </td> 
				<td>&nbsp;&nbsp;</td><td><a href=\"javascript:;\" onClick=\"popupWin('color_picker.php?obj=$objname','colorpicker','width=560,height=180,scrollbars=no,centered=1')\" class=\"text\">
				<img border=\"0\" src=\"images/colorpicker.gif\" name=\"colorpicker_$objname\" width=\"20\" height=\"18\" id=\"colorpicker\" style=\"background-color: $prefill\">
				</a></td>" ; 
				break;
			
			case "File" :
				$formInput =  "<td class=\"$style\" width=\"200\">$label</td> <td>&nbsp;&nbsp;</td><td> 
				<input readonly onFocus=\"this.blur()\" onChange=\"set_save(false)\"  onMouseDown=\"popupWin('file_manager.php?mode=html&obj=$objname','filemanager','width=380,height=450,scrollbars=0,resizable=0,centered=1')\" type=\"text\" name=\"$objname\" value=\"$prefill\" style=\"width:200\"> </td> 
				<td class=\"text\">&nbsp;&nbsp;</td><td><a href=\"javascript:;\" onClick=\"popupWin('file_manager.php?mode=html&obj=$objname','filemanager','width=380,height=450,scrollbars=0,resizable=0,centered=1')\" class=\"text\">choose</a>... &nbsp; <a class=\"text\" href=\"javascript:;\" onClick=\"document.forms[0].$objname.value=''\">clear</a></td>" ; 
				break;
			
			case "Radio" :
				if ( count($aInputOptions) <= 1 ) { 
				//	THIS IS IF THERE ISN'T AN INPUTOPTIONS ARRAY THEN WE'LL JUST USE THE LABEL VALUE (SINCE THERE'S ONLY ONE)
					if ( $value ==  $prefill) {  $selected = " checked " ; }
					$formInput .= "<td class=\"$style\" width=\"1\"><input onChange=\"set_save(false)\"  $selected type=\"radio\" name=\"$objname\" value=\"$value\" ></td><td width=\"1\">&nbsp;</td><td nowrap class=\"$style\">$label&nbsp;&nbsp;&nbsp;</td>";
				} else {
				// THIS IS IF THERE IS AN INPUTOPTIONS ARRAY
					while ( list($v,$k) = each($aInputOptions) ) {
						if ( $prefill == "" && $counter == 1) { $selected = " checked " ; } else {  $selected = " " ; }
						if ( $v == $prefill) {  $selected = " checked " ; } ++$counter;
						$formInput .= "<td class=\"$style\" width=\"1\"><input onChange=\"set_save(false)\"  $selected type=\"radio\" name=\"$objname\" value=\"$v\" ></td>" .
						"<td width=\"1\">&nbsp;</td><td class=\"$style\" nowrap>$k&nbsp;&nbsp;&nbsp;</td>";
					}
				// IF THERE IS A LABEL, WE'LL ADD IT TO THE BEGINNING OF THE LIST
					if ( $label != "") {
						$formInput = "<td class=\"$style\" width=\"200\">$label</td> <td>&nbsp;&nbsp;</td>" . $formInput;
					}
				}
				break;
				
			case "Checkbox" :
				if ( count($aInputOptions) <= 1 ) { 
//					print("$prefill-");
					if ( $prefill ==  "checked") {  $selected = " checked " ; } else {  $selected = " " ; }
					
					$formInput .= "<td class=\"$style\" width=\"1\">
					<input type=\"hidden\" name=\"" . $objname . "\" value=\"\">
					<input onChange=\"set_save(false)\"  $selected type=\"checkbox\" name=\"$objname\" value=\"checked\"></td>
					<td>&nbsp;</td><td class=\"$style\" nowrap>$label$k&nbsp;&nbsp;&nbsp;</td>";
					
				} else {
					while ( list($v,$k) = each($aInputOptions) ) {
						$prefill = $obj->attribute_data[ $objname . "_" . $v ] ;
						if ( $prefill == "" && $counter == 1) { $selected = " checked " ; } else {  $selected = " " ; }
						if ( $prefill == "checked") {  $selected = " checked " ; } else {  $selected = " " ; } ++$counter;
						$formInput .= "<td>
						<input type=\"hidden\" name=\"" . $objname . "_" . $v . "\" value=\"\">
						<input  onChange=\"set_save(false)\" $selected type=\"checkbox\" name=\"". 
						$objname . "_" . $v . "\" value=\"checked\"></td><td>&nbsp;</td><td class=\"$style\">$label$k&nbsp;&nbsp;&nbsp;</td>";
					}
				}
				break;
			case "Pulldown" :
			 	$formInput .= "<td class=\"$style\" width=\"200\">$label</td> <td>&nbsp;&nbsp;</td><td><select  onChange=\"set_save(false)\" name=\"$objname\">\n";
				while ( list($v,$k) = each($aInputOptions) ) {
					if ( ($prefill == "" || !isset($value)) && $counter == 1) { $selected = " selected " ; } else {  $selected = " " ; }
					if ( $v == $prefill) {  $selected = " selected " ; } else { $selected = ""; } ++$counter;
					$formInput .= "<option $selected value=\"$v\" style=\"width:360\">$k</option>";
				}
				$formInput .= "</select></td>\n" ;
				break;
				
			case "List" :
				$formInput = "List";
				break;
			
			case "Text" :
				$formInput =  "<td class=\"$style\" width=\"200\" >$label</td> <td>&nbsp;&nbsp;</td><td align=\"right\"> <input  onChange=\"set_save(false)\" type=\"text\" name=\"$objname\" value=\"$prefill\" style=\"width:360\"> </td>" ; 
				break;
			
			case "Textarea" :
				$formInput =  "<td class=\"$style\" width=\"200\" valign=\"top\">$label</td> <td>&nbsp;&nbsp;</td><td align=\"right\"> <textarea  onChange=\"set_save(false)\" wrap=\"VIRTUAL\" name=\"$objname\" rows=\"4\" style=\"width:360\">$prefill</textarea></td>" ; 
		}
	} else {
		$formInput = "<td class=\"$style\" width=\"200\">$label</td> <td>&nbsp;&nbsp;</td><td align=\"right\"> <input  onChange=\"set_save(false)\" type=\"text\" name=\"$objname\" value=\"$prefill\" style=\"width:360\"></td>" ;
	}
	
	if ($fieldType == "FIELDGROUP") { 
		$formInput = "\n" . startTable("600") . "\n" . $formInput  . "\n" . "<td width=\"100%\"><hr size=\"1\" height=\"1\" noshade width=\"100%\"></td>\n" . endTable() ;
	//	if ( !$firstpass ) { $formInput = "&nbsp;\n<br>" . $formInput; }
	} else if ($aParent[tag] == "FIELDGROUP") {  
		$formInput =  "\n" . startTable() . "\n<td width=20>&nbsp;&nbsp;&nbsp;</td>" . $formInput . "\n" . endTable()  ; 
	} else {
		$formInput =  "\n" . startTable() . "\n" . $formInput . "\n" . endTable()  ; 
	}
	
	return $formInput;
}


function makeSubMenus () {
	global $cfg_aMenu, $cfg_aSubMenu;
	
	while ( list($k, $v) = each($cfg_aMenu) ) {
		$subMenus .= "<div id=\"submenu_$v\" style=\"width:1px; height: 1px; position:absolute; left:0px; top:0px; z-index:100;  background-color: #dedede; layer-background-color: #dedede; visibility: hidden; border: 0px none #000000;\">
	<table cellpadding=0 cellspacing=0 border=0 bgcolor=\"#EEF012\"><tr><td height=4></td></tr>
	"; 
	//background=\"images/bkg-groove.gif\"
		while ( list($key, $menudata) = each($cfg_aSubMenu[$k]) ) {
			if ($menudata == "divider") {
				$subMenus .= "\t\t<tr><td height=10></td></tr>";
			} else {
				list($menuitem, $action) = explode(":::",$menudata);
				$menuitem = ereg_replace(" ","&nbsp;",$menuitem);
				$subMenus .= "\t\t<tr><td onmousedown=\"$action;\" onmouseover=\"doSubMenu(this, 'on')\" onmouseout=\"doSubMenu(this, 'off')\" height=\"21\" class=\"menu\">&nbsp;&nbsp;&nbsp;&nbsp;$menuitem&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>\n";
			}
		}
		$subMenus .= "<tr><td height=4></td></tr></table></div>\n";
	}
	return $subMenus;
}


function getAdminUserRecord ($name) {
	$sql = "SELECT * FROM AdminUsers WHERE Username='$name'";
	$nResult = dbq($sql, "GETTING ADMIN USER");
	$len = mysql_num_rows($nResult);
	if ($len > 0) {
		return mysql_fetch_assoc($nResult);
	} else {
		return null;
	}
}

function makeTabs($aTabs, $options="", $link = "", $selectedTab="",$tabkey="tab") {
	if ($link == "") { $link = $_SERVER['SCRIPT_NAME']; }
	$content .= "
	<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
	  <tr>
	 ";
	$counter = 0;
	if ( is_array($aTabs) ) {
		foreach ( $aTabs as $tabvalue  => $tabname) {
			++$counter;
			$tabtext = ereg_replace(" ","&nbsp;",$tabname);
			if ($_SESSION[$tabkey] == $tabvalue) {
				$content .= "
				<td width=\"1\"><img border=\"0\" src=\"images/tab-on_left.gif\" width=\"8\" height=\"23\"></td>
				<td width=\"1\" nowrap background=\"images/tab-on_middle.gif\" class=\"tab\">$tabtext</td>
				<td width=\"1\"><img src=\"images/tab-on_right.gif\" width=\"11\" height=\"23\"></td>
				";
			} else {
				$content .= "
				<td width=\"1\"><img src=\"images/tab-off_left.gif\" width=\"8\" height=\"23\"></td>
				<td width=\"1\" nowrap background=\"images/tab-off_middle.gif\"><a class=\"taboff\" href=\"$link?$tabkey=$tabvalue$options\">$tabtext</a></td>
				<td width=\"1\"><img src=\"images/tab-off_right.gif\" width=\"11\" height=\"23\"></td>
				";
			}
		}
	}
	$content .= "
		<td width=\"100%\" background=\"images/tab-extender.gif\">&nbsp;</td>
	  </tr>
	</table>
	
	"; 
	return $content;
}


function parseSiteAttribs ($aParent, &$obj, $firstpass = 1) {

	if ( is_array($aParent[children]) ) {
		while ( list($k, $v) = each($aParent[children]) ) {
			if ( $v[tag] == "TAB") {
				$obj->aTabs[$v[attributes][ID]] = $v[attributes][NAME]; 
				$obj->currentTab = $v[attributes][ID];
			} else if ($v[tag] == "FIELDGROUP") {
				$thisid = $v['attributes']['ID'];
				$obj->addContent( formatFormInput($aParent, $v, $firstpass, $obj) ); // & from $obj
				$firstpass = 0;
				if ( isset($v[children]) ) { 
					parseSiteAttribs ($v, $obj, $counter); // removed & from $obj
				}
				
			} else if ($v[tag] == "FIELD") {
				$thisid = $v['attributes']['ID'];
				$v['prefill'] =  $obj->attribute_data[$thisid];
				$obj->addContent(  formatFormInput($aParent, $v, $firstpass, $obj) ); // & from $obj
				if ( isset($v[children]) ) { 
					parseSiteAttribs ($v, $obj, $firstpass); // & $obj
				}
			}
			
			// ONLY PROCESS ATTRIBUTES FOR CURRENT TAB
			$tabkey = $obj->tabkey;
			if ( !isset($_SESSION[$tabkey]) ) { $_SESSION[$tabkey] = $v[attributes][ID]; }
			if ( $_SESSION[$tabkey] == $v[attributes][ID] ) {
				if ( isset($v[children]) ) { 
					parseSiteAttribs ($v, $obj, $firstpass); // removed & from $obj
				}
			}
			
		}
	}
}

class siteAttrib {
	var $content;
	var $aTabs;
	var $tabkey;
	var $currentTab;
	var $attribute_data; // Used for prefilling fields
	
	function siteAttrib($site_attributes, $attribute_data, $readfile = 0, $tabkey="tab") {
		$aSiteDef = xml_get_tree($site_attributes, $readfile);
		$this->attribute_data = $attribute_data;
		$this->tabkey = $tabkey;
		parseSiteAttribs($aSiteDef[0], $this); // removed & from $this 
	}
	
	function AddContent($c) { 
		$this->content .= $c;
	}
	
	function GetContent() {
		return $this->content;
	}
	
	function GetAttributeData() {
		return $this->attribute_data;
	}
	
	function GetTabsArray() {
		return $this->aTabs;
	}
}

?>