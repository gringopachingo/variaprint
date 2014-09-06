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

$subMenus = makeSubMenus();

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><? print("Bottom"); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require_once("../style.css"); ?>
<script language="JavaScript" type="text/JavaScript">
<!--
var submenu

function doSubMenu(menu, io) {
	if (io == "on") {
		menu.style.backgroundColor = "darkblue"
		menu.style.color = "white"
		menu.style.cursor = "default"
	} else if (io =="off") {
		menu.style.backgroundColor = ""
		menu.style.color = "black"
		menu.style.cursor = "default"
	}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_setTextOfLayer(objName,x,newText) { //v4.01
  if ((obj=MM_findObj(objName))!=null) with (obj)
    if (document.layers) {document.write(unescape(newText)); document.close();}
    else innerHTML = unescape(newText);
}

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; }
}

<? print($headScript); ?>
//-->
</script>
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" <? print($bodyTagScript); ?> onmousedown="if (parent.frames.menu.menuStatus=='on') {  parent.frames.menu.menuStatus='off';  parent.frames.menu.doMenu('',''); }" onscroll="if (parent.frames.menu.menuStatus=='on') { parent.frames.menu.menuStatus='off';  parent.frames.menu.doMenu('',''); }">
<form <? 
if(!isset($formOptions)){ 
	$formOptions = "action=\"" . $HTTP_SERVER_VARS['SCRIPT_NAME'] . "\"";
}  
print($formOptions); ?>>
<?
print($content);

print($subMenus);
?>

</form>
</body>
</html>
