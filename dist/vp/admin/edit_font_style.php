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


require_once("../inc/config.php");

require_once("../inc/functions-global.php");
require_once("inc/functions.php");
require_once("inc/popup_log_check.php");

if ($a_form_vars[save] == "yes") {
	while( list($k,$v) = each($a_form_vars) ) {
		if ($v != "") {
			$v = ereg_replace("px","",$v);
			switch ($k) {
				case  "font" :
					$css .= "font-family:" . $v . "; " ; break;
				case  "size" :
					$css .= "font-size:" . $v . "px; " ;  break;
				case  "lineheight" :
					$css .= "line-height:" . $v . "px; " ;  break;
				case  "weight" : 
					$css .= "font-weight:" . $v . "; " ;  break;
				case  "style" : 
					$css .= "font-style:" . $v . "; " ;  break;
				case  "color" : 
					$css .= "color:" . $v . "; " ;  break;
				case  "decoration" : 
					$css .= "text-decoration:" . $v . "; " ;  break;
			}
		}
	}
	print("<script language=\"javascript\"> 
		window.opener.document.forms[0].$a_form_vars[obj].value = \"" . $css . "\" ; 
		window.opener.elem_saved = false;
		window.close(); 
	</script>");
}

$aCSStmp = explode(";",$a_form_vars['css']);
while ( list($k, $v) = each($aCSStmp) ) {
	list($k2, $v2) = explode(":",$v);
	$k2 = strip(" \t\r\n", $k2);
	$aCSS[$k2] = ereg_replace("px","", $v2);
}

$aWeight[0] = "";
$aWeight[1] = "normal";
$aWeight[2] = "bold";
$aWeight[3] = "bolder";
$aWeight[4] = "lighter";
$aWeight[5] = "100";
$aWeight[6] = "200";
$aWeight[7] = "300";
$aWeight[8] = "400";
$aWeight[9] = "500";
$aWeight[10] = "600";
$aWeight[11] = "700";
$aWeight[12] = "800";
$aWeight[13] = "900";

$aStyle[0] = "";
$aStyle[1] = "normal";
$aStyle[2] = "italic";
$aStyle[3] = "oblique";

$fWeight = " <select name=\"weight\" id=\"weight\">";
while ( list($k, $v) = each($aWeight) ) {
	if ($aCSS['font-weight'] == $v) { $sel = " selected "; } else { $sel = ""; }
	$fWeight .= "<option value=\"$v\" $sel>$v</option>\n";
}
$fWeight .= "</option>";

$fStyle = " <select name=\"style\" id=\"weight\">";
while ( list($k, $v) = each($aStyle) ) {
	if ($aCSS['font-style'] == $v) { $sel = " selected "; } else { $sel = ""; }
	$fStyle .= "<option value=\"$v\" $sel>$v</option>\n";
}
$fStyle .= "</option>";

?><html>
<head>
<?php
print($header_content);
?>
<title>Edit <?php print($a_form_vars[label]); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
function findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}
function initialize() {
	oPicker = findObj('colorpicker_color')
	oColor = findObj('color')
	if (oColor.value != "") {	oPicker.style.backgroundColor = oColor.value }
}
function update_color(c) {
	initialize()
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
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/bkg-groove.gif" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="initialize()" onblurry="window.focus()">
<form name="form1" method="post" action="<?php print($_SERVER['SCRIPT_NAME']) ; ?>">
  <table width="370" height="98%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td><table width="370" border="0" align="center" cellpadding="0" cellspacing="8">
        <tr>
          <td height="20" colspan="4" class="subhead">Edit<strong> &quot;<?php print($a_form_vars[label]); ?>&quot;<br>
            </strong>
              <hr size="1" noshade>
          </td>
        </tr>
        <tr>
          <td width="59" height="20" align="right" class="text">Font:&nbsp;</td>
          <td height="20" colspan="3">
            <table width="70" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="58" height="25">
                  <input name="font" type="text" id="font" style="width:260" value="<?php print($aCSS['font-family']); ?>">
                </td>
                <td width="12" height="25" class="text">&nbsp;</td>
              </tr>
              <tr>
                <td height="25"><select name="selector" id="selector" style="width:260; border:solid black 1px" onChange="document.forms[0].font.value = this.value">
                    <option> </option>
                    <option value="Arial, Helvetica, san-serif">Arial, Helvetica,
                    san-serif</option>
                    <option value="Times New Roman, Times, serif">Times New Roman,
                    Times, serif</option>
                    <option value="Courier New, Courier, mono">Courier New, Courier,
                    mono</option>
                    <option value="Georgia, Times New Roman, Times, serif">Georgia,
                    Times New Roman, Times, serif</option>
                    <option value="Verdana, Helvetica, Arial, san-serif">Verdana,
                    Helvetica, Arial, san-serif</option>
                    <option value="Geneva, Helvetica, Arial, san-serif">Geneva,
                    Helvetica, Arial, san-serif</option>
                  </select>
                </td>
                <td height="25" class="text">&nbsp;</td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td height="20" align="right" class="text">Size:&nbsp;</td>
          <td width="126" height="20" class="text">
            <table width="70" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="58">
                  <input name="size" type="text" id="size" style="width:50" value="<?php print($aCSS['font-size']); ?>">
                </td>
                <td width="12" class="text">px</td>
              </tr>
            </table>
          </td>
          <td width="39" height="20" align="right" class="text">Weight:&nbsp;</td>
          <td width="106" height="20"><?php print($fWeight) ;?> </td>
        </tr>
        <tr>
          <td height="20" align="right" class="text">Line&nbsp;Height:&nbsp;</td>
          <td height="20">
            <table width="70" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="58" height="25">
                  <input name="lineheight" type="text" id="lineheight" style="width:50" value="<?php print($aCSS['line-height']); ?>">
                </td>
                <td width="12" height="25" class="text">px</td>
              </tr>
            </table>
          </td>
          <td height="20" align="right" class="text">Style:&nbsp;</td>
          <td height="20"> <?php print($fStyle) ;?> </td>
        </tr>
        <tr>
          <td height="20" align="right" valign="top" nowrap class="text">
            <table height="25" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td class="text">Decoration:&nbsp;</td>
              </tr>
            </table>
          </td>
          <td height="20" class="text">
            <table width="106" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td height="22"><input type="radio" name="decoration" <?php if ( $aCSS['text-decoration'] == "" || !isset($aCSS['decoration']) ) { print(" checked "); }  ?>>
                </td>
                <td height="22" class="text">Default</td>
              </tr>
              <tr>
                <td width="26" height="22">
                  <input type="radio" name="decoration" value="none"  <?php if ( strip(" ", $aCSS['text-decoration']) == "none" ) { print(" checked "); }  ?>>
                </td>
                <td width="80" height="22" class="text">none</td>
              </tr>
              <tr>
                <td height="22"><input type="radio" name="decoration" value="underline" <?php if ( strip(" ", $aCSS['text-decoration']) == "underline" ) { print(" checked "); }  ?>>
                </td>
                <td height="22" class="text">underline</td>
              </tr>
            </table>
          </td>
          <td height="20" align="right" valign="top" class="text">
            <table height="25" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td class="text">Color:&nbsp;</td>
              </tr>
            </table>
          </td>
          <td height="20" valign="top"><table width="83" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="25" height="25">
                  <input onChange="document.all.colorpicker_color.style.backgroundColor=this.value" name="color" type="text" id="color" style="width:70" value="<?php print($aCSS['color']); ?>">
                </td>
                <td width="58" height="25" class="text"><a href="javascript:;" onClick="popupWin('color_picker.php?obj=color','colorpicker','width=560,height=180,scrollbars=no,centered=1')"><img src="images/colorpicker.gif" name="colorpicker_color" width="20" height="18" border="0" id="colorpicker_color" style="background-color: #cccccc"></a></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td height="20" colspan="4" valign="top" nowrap class="text">&nbsp;</td>
        </tr>
        <tr align="right">
          <td height="20" colspan="4" nowrap class="text"><input name="save" type="hidden" id="save3" value="yes">
              <input name="obj" type="hidden" id="obj" value="<?php print($a_form_vars[obj]); ?>">
              <input type="button" name="Submit2" value="Cancel" onclick="window.close()">
&nbsp;&nbsp;
      <input name="submit" type="submit" value="Save">
          </td>
        </tr>
      </table></td>
    </tr>
  </table>
</form>
</body>
</html>
