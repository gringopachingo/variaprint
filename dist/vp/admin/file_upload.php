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
if ($_SESSION["privilege"] == "owner") {
	require_once("inc/popup_log_check.php");
}

// $_SESSION[site] = $a_form_vars['site'];

if (trim($_SESSION[site]) == "" || !isset($_SESSION[site])) {
	exit("There was an error determining which site is opened. <br><br>You may need to log out and log back in or reopen the current site by clicking on &quot;open order site&quot; at the top left of the VariaPrint&trade; Manager main screen.");
}

$success = false;

if ($a_form_vars['action'] == "upload" && $_SESSION[site] != "") {
	// this is the path on your server to upload to. For windows use d:\\path\to\www\directory\ for unix use format as shown		
	$path = $cfg_base_dir . "_sites/" . $_SESSION[site] . "/images/" . $a_form_vars['folder'] . "/";
	
	$result = move_uploaded_files($path);

	if ($result=="1") $result="File uploaded successfully."; 
	$success = true;
} 



?>
<html>
<head>
<?php
print($header_content);
?>
<title>Upload a file</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; }
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function startUpload() {
	MM_showHideLayers('alertmsg','','show');
	MM_swapImage('progress','','images/progress-anim.gif',1);
	document.forms[0].uploadbtn.disabled=true;
	document.forms[0].submit();
}
//-->
</script>
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body background="images/bkg-groove.gif" leftmargin="10" topmargin="10" marginwidth="10" marginheight="10" onLoad="MM_preloadImages('images/progress-anim.gif')">
<?php if ($success) { ?>
<table width="401" height="177" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="391" height="144" class="text"><?php if ($has_dir) { ?><p>
		<strong>WARNING: DIRECTORIES HAVE BEEN IGNORED. RE-UPLOAD WITHOUT DIRECTORIES.</strong>
	</p>
	<?php } ?>
	<p><?php=$result?> </p>
      <p>You must click
        the &quot;Refresh&quot; button
      on the right of the file list to update.
      </p>      <p align="right">        
        <input name="ok" type="button" id="ok" style="width: 100" value="OK" onClick="top.close()" >
      </p></td>
  </tr>
</table>
<p class="text">&nbsp;</p>
<p>  <?php } else { ?>
</p>
<div id="alertmsg" style="position:absolute; left:12px; top:111px; width:380px; height:18px; z-index:1; visibility: hidden;" class="text"><font color="#FF0000"><strong>Do 
  not close this window or your file upload will be terminated.</strong></font></div>
<div id="Layer1" style="position:absolute; left:12px; top:129px; width:388px; height:103px; z-index:2">
  <p><span class="text"><strong>Note: </strong> Zipped files will be
      automatically unzipped once uploaded. This can be useful for uploading
      mutiple files. <em>Do not include directories.</em></span>
  <hr size="1" noshade>
  <span class="text">    </span><span class="title"><strong>Accepted
          graphic formats</strong></span><span class="text"><strong><br>
      Template and library graphics: </strong> PDF, JPEG, PNG and
      GIF*. <strong><br>
      Site graphics** (including item icons): </strong> RGB
    JPEG and GIF. </span></p>
  <p><span class="text">*<em><font color="#990000"> GIF images must
                    contain at least 128 colors.</font></em><br>
    <em>**               Site graphics will be displayed as-is, including image
    size.</em></span></p>
</div>
<form enctype="multipart/form-data" name="form1" method="post" action="file_upload.php" onSubmit="">
  <table height="100" border="0" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="36" colspan="3"> <input name="userfile" type="file" style="width:360" size="35">
      </td>
    </tr>
    <tr> 
      <td height="32" nowrap ><img src="images/spacer.gif" name="progress" width="243" height="17" id="progress"></td>
      <td align="right" nowrap ><input name="cancel" type="button" id="cancel" value="Cancel" onClick="top.close()" ></td>
      <td align="right" nowrap ><input name="site" type="hidden" id="site" value="<?php print($_SESSION[site]); ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
        <input name="action" type="hidden" id="action" value="upload">
        <input name="folder" type="hidden" id="folder" value="<?php print($_GET['folder']);  ?>">
        <input name="uploadbtn" type="button" value="Upload" onClick="startUpload()">
      </td>
    </tr>
  </table>
</form>
<?php } ?>
</body>
</html>
