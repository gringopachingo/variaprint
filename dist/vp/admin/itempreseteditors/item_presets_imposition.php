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



session_name("mssid");
session_start();
$mssid = session_id();


require_once("../../inc/config.php");
require_once("../../inc/functions-global.php");
require_once("../inc/functions.php");
if (!$_SESSION["privilege_items_properties"]) {
	require_once("../inc/popup_log_check.php");
}

$mode = $a_form_vars['mode'];

$a_form_vars['master_uid'] = "1"; 
	
$showinput = false;

// *********** Save information ********************************	
/*		<field id=\"general_info\" value=\"$a_form_vars[general_info]\"/>
		<field id=\"order_info\" value=\"$a_form_vars[order_info]\"/>
*/	
	$definition = "<" . "?xml version=\"1.0\" encoding=\"iso-8859-1\"?" . ">
	<imposition>
		<field id=\"color_bars\" value=\"$a_form_vars[color_bars]\"/>
		<field id=\"registration\" value=\"$a_form_vars[registration]\"/>
		<field id=\"trim_marks\" value=\"$a_form_vars[trim_marks]\"/>
		<field id=\"item_lr_bleed\" value=\"$a_form_vars[item_lr_bleed]\"/>
		<field id=\"item_tb_bleed\" value=\"$a_form_vars[item_tb_bleed]\"/>
		<field id=\"item_across\" value=\"$a_form_vars[item_across]\"/>
		<field id=\"item_down\" value=\"$a_form_vars[item_down]\"/>
		<field id=\"item_width\" value=\"$a_form_vars[item_width]\"/>
		<field id=\"item_height\" value=\"$a_form_vars[item_height]\"/>
		<field id=\"item_left\" value=\"$a_form_vars[item_left]\"/>
		<field id=\"item_top\" value=\"$a_form_vars[item_top]\"/>
		<field id=\"imposed_width\" value=\"$a_form_vars[imposed_width]\"/>
		<field id=\"imposed_height\" value=\"$a_form_vars[imposed_height]\"/>
		<field id=\"name\" value=\"$a_form_vars[name]\"/>
	
	</imposition>
	";
	$definition = addslashes($definition);

	if ( $a_form_vars[name] == "" ) { $name = "Untitled Imposition"; } else { $name = $a_form_vars[name]; }
	
	if ( $a_form_vars['action'] == "create" ) {
		
		$sql = "INSERT INTO Imposition SET
			SiteID='$_SESSION[site]',
			Name='" . addslashes($name) . "',
			Template='N',
			Definition='$definition'
			 ";
		$r_result = dbq($sql);
		$a_form_vars['mode'] = "edit";
		$a_form_vars['imp_id'] = db_get_last_insert_id();
		
	} else if ( $a_form_vars['action'] == "save" ) {
		$sql = "UPDATE Imposition SET
			Name='" . addslashes($name) . "',
			Definition='$definition'
			WHERE ID='$a_form_vars[imp_id]' ";
		
		$r_result = dbq($sql);
	}
	
//	$showinput = true; 
	
// *********** Start getting information ********************************	
	$sql = "SELECT ID,Name FROM Imposition WHERE SiteID='$_SESSION[site]' ";// OR MasterUID='$a_form_vars[master_uid]'
	$r_result = dbq($sql);
	
	if ( mysql_num_rows($r_result) > 0 ) {
		
		$content .= "
		<span class=\"text\">Select:</span>
		<select class=\"text\" name=\"\" onchange=\"document.location='item_presets_imposition.php?imp_id=' + this.value\">\n";
		while ( $a_imp = mysql_fetch_array($r_result) ) {
			if ( !isset($a_form_vars['imp_id']) || $a_form_vars['imp_id'] == "" ) { 
				if ( $mode == "new" ) {
					$content .= "<option value=\"\" selected>  </option>\n"; 
				} else {
					$a_form_vars['imp_id'] = $a_imp['ID'];
				}
			} else { 
				$showinput = true; 
			}
			if ( $a_imp['ID'] == $a_form_vars['imp_id'] ) { $sel = "selected"; $edititemname = $a_imp['Name']; $showinput = true; } else { $sel = ""; }
			$content .= "<option value=\"$a_imp[ID]\" $sel>$a_imp[Name]</option>\n";
		}
		$content .= "
		</select>&nbsp;&nbsp;
		<a href=\"item_presets_imposition.php?mode=new\" target=\"_self\" class=\"text\">add new imposition</a><br>
	<br>";
		
		
	} else if ($mode != "new") {
		$content .= "<span class=\"text\">No imposition styles set up. 
		<a href=\"item_presets_imposition.php?mode=new\">Click here</a> to create a new one.</span>";
	}
		
		
		
	$sql = "SELECT ID, Name, Definition, Template FROM Imposition WHERE Template='Y' OR SiteID='$_SESSION[site]' ORDER BY Template DESC";
	$r_result = dbq($sql);
	
	$based_on = "<select class=\"text\" name=\"basedon\" onchange=\"choosebase(this.value)\">\n";
		$based_on .= "<option value=\" \">Select ...</option>\n";
	while ( $a_result = mysql_fetch_array($r_result) ) {
		$str_base_js .= "abase[$a_result[ID]] = new Array()\n" ;
		$a_xml_tree = xml_get_tree($a_result['Definition']);
		if (is_array($a_xml_tree[0]['children'])) {
			foreach($a_xml_tree[0]['children'] as $a) {
				if ( $a[attributes][ID] != "" && $a[attributes][ID] != "name") { 
					$str_base_js .= "abase[" . $a_result[ID] . "]['" . $a[attributes][ID] . "'] = \"" . $a['attributes']['VALUE'] . "\"\n" ; 
				}
			}
		}
		
		if ($a_result['Template'] == "Y") { $rbracket = "]"; $lbracket = "["; } else {$rbracket = ""; $lbracket = ""; }
		
		$based_on .= "<option value=\"$a_result[ID]\">$lbracket$a_result[Name]$rbracket</option>\n";
	}
	$based_on .= "</select>\n";




	if ( $a_form_vars[mode] == "new" ) { 
		$title = "Create new imposition style";
		$button_name = "Create"; 
		$action = "create"; 
		$showinput = true;
	} else { 
		$title = "Edit imposition style \"" . $edititemname . "\""; 
		$button_name = "Save"; 
		$action = "save"; 

		$sql = "SELECT Name, Definition FROM Imposition WHERE ID='$a_form_vars[imp_id]'";
		$r_result = dbq($sql);
		$a_imp = mysql_fetch_array($r_result);
		$a_prefill[name] = $a_imp[Name];
		
		$a_xml_tree = xml_get_tree($a_imp['Definition']);
		if (is_array($a_xml_tree[0]['children'])) {
			foreach($a_xml_tree[0]['children'] as $a) {
				$a_prefill[$a['attributes']['ID']] = $a['attributes']['VALUE'];
			}
		}
		
		if ($a_prefill[color_bars] == "true") { $order_info_check = "checked"; }
		if ($a_prefill[registration] == "true") { $registration_check = "checked"; }
		if ($a_prefill[trim_marks] == "true") { $trim_marks_check = "checked"; }
		
	}
	
//	print($mode);
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><? print("Bottom"); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.menu {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
}

-->
</style>

<script language="JavaScript" type="text/JavaScript">
<!--
abase = new Array()
<? print($str_base_js); ?>

function choosebase(base) {
	document.forms[0].cancelbtn.disabled = false
	for (i in abase[base]) {
		obj = findObj(i);
		if (obj != null && obj != 'undefined') {
			obj.value = abase[base][i];
		}
	}
}
function enablerevert() {
	document.forms[0].cancelbtn.disabled = false
}
function findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}


//-->
</script>
<?
print($header_content);
?>
<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="20" topmargin="30" marginwidth="20" marginheight="30">
<form name="form1" method="get" action="">
  <?
print($content);
?>
  <? if ($showinput) { ?>
  <span class="title"><strong> <? print($title); ?> &nbsp;&nbsp;</strong></span><font size="+1"><strong>&nbsp; 
  </strong></font> 
  <hr align="left" width="500" size="1" noshade>
  <table width="501" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td class="text">Name 
        <input name="name" type="text" class="text" id="name2" value="<? print($a_imp[Name]); ?>"></td>
      <td height="25" align="right" class="text">
<input onchange="enablerevert()"  name="cancelbtn" type="reset" disabled class="text" id="cancelbtn2" onClick="this.disabled = true" value="Revert to saved"> 
        <input name="Submit" type="submit" class="text" value="<? print($button_name); ?>"> 
        <input name="master_uid" type="hidden" id="master_uid" value="<? print($a_form_vars['master_uid']); ?>"> 
        <input name="mode2" type="hidden" id="mode2" value="<? print($a_form_vars['mode']); ?>"> 
        <input name="action" type="hidden" id="action" value="<? print($action); ?>"> 
        <input name="imp_id" type="hidden" id="imp_id" value="<? print($a_form_vars['imp_id']); ?>"></td>
    </tr>
  </table>
  <table height="56" border="0" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="56" nowrap class="subhead"><em>Base on imposition style:&nbsp;</em></td>
      <td height="56"><?print($based_on); ?></td>
    </tr>
  </table>
  <table width="501" border="0" cellpadding="3" cellspacing="0">
    <tr> 
      <td colspan="6" class="text"><strong><span class="subhead">Master press
            sheet settings</span></strong></td>
    </tr>
    <tr> 
      <td align="right" class="text">Master page height</td>
      <td> 
        <input onchange="enablerevert()" name="imposed_height" type="text" class="text" id="imposed_height" value="<? print($a_prefill[imposed_height]); ?>" size="7"></td>
      <td class="text">pt</td>
      <td align="right" class="text">Master page width</td>
      <td> 
        <input onchange="enablerevert()"  name="imposed_width" type="text" class="text" id="imposed_width" value="<? print($a_prefill[imposed_width]); ?>" size="7"></td>
      <td class="text">pt</td>
    </tr>
    <tr class="text"> 
      <td colspan="6"> 
        <input name="trim_marks" onchange="enablerevert()"  type="checkbox" value="true" <? print($trim_marks_check); ?>>
        Trim marks&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="registration"  onchange="enablerevert()" type="checkbox" value="true" <? print($registration_check); ?>>
        Registration marks
		<!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
        <input name="color_bars"  type="checkbox" id="color_bars" onchange="enablerevert()" value="true" <? print($order_info_check); ?>> 
        Color bars //-->      </td>
    </tr>
    <tr> 
      <td colspan="6" class="text">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="6" class="text"><strong><span class="subhead">Imposed item 
        settings</span></strong></td>
    </tr>
    <tr> 
      <td width="154" align="right" class="text">Top position to start items </td>
      <td width="43"> 
        <input onchange="enablerevert()"  name="item_top" type="text" class="text" id="item_top" value="<? print($a_prefill[item_top]); ?>" size="7"></td>
      <td class="text">pt</td>
      <td width="166" align="right" class="text">Left postion to start items</td>
      <td width="40"> 
        <input onchange="enablerevert()"  name="item_left" type="text" class="text" id="item_left" value="<? print($a_prefill[item_left]); ?>" size="7"></td>
      <td class="text">pt</td>
    </tr>
    <tr> 
      <td align="right" class="text">Item page height</td>
      <td> 
        <input onchange="enablerevert()"  name="item_height" type="text" class="text" id="item_height" value="<? print($a_prefill[item_height]); ?>" size="7"></td>
      <td class="text">pt</td>
      <td align="right" class="text">Item page width</td>
      <td> 
        <input onchange="enablerevert()"  name="item_width" type="text" class="text" id="item_width" value="<? print($a_prefill[item_width]); ?>" size="7"></td>
      <td class="text">pt</td>
    </tr>
    <tr> 
      <td align="right" class="text">Items down</td>
      <td> 
        <input onchange="enablerevert()"  name="item_down" type="text" class="text" id="item_down" value="<? print($a_prefill[item_down]); ?>" size="7"></td>
      <td class="text">pt</td>
      <td align="right" class="text">Items across</td>
      <td> 
        <input onchange="enablerevert()"  name="item_across" type="text" class="text" id="item_across" value="<? print($a_prefill[item_across]); ?>" size="7"></td>
      <td class="text">pt</td>
    </tr>
    <tr> 
      <td align="right" class="text">Top/bottom item bleed</td>
      <td> 
        <input onchange="enablerevert()"  name="item_tb_bleed" type="text" class="text" id="item_tb_bleed" value="<? print($a_prefill[item_tb_bleed]); ?>" size="7"></td>
      <td class="text">pt</td>
      <td align="right" class="text">Left/right edge item bleed</td>
      <td> 
        <input onchange="enablerevert()"  name="item_lr_bleed" type="text" class="text" id="item_lr_bleed" value="<? print($a_prefill[item_lr_bleed]); ?>" size="7"></td>
      <td class="text">pt</td>
    </tr>
  </table>
</form>
<? } 
/* http://127.0.0.1/vp/admin/item_presets_imposition.php?
basedon=&name=Silly&
imposed_height=1234&
imposed_width=5634&
item_top=80&
item_left=100&
item_height=234&
item_width=456&
item_down=1&
item_across=2&
item_tb_bleed=18&
item_lr_bleed=18&
trim_marks=yes&
registration=yes&
order_info=radiobutton&
general_info=yes&
Submit=Save&
master_uid=1&
mode2=&
site=500&
action=save&
imp_id=6 */
?>
</body>
</html>
