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


	require_once("inc/popup-header.php");
	require_once("inc/functions-global.php");
	require_once("inc/functions_pdf.php");

	session_name("ossid");
	session_start();
	$ossid = session_id();
	
	$hash = "";
	if (is_array($fv)) {
		foreach ($fv as $k=>$v) {
			$hash .= "&".$k."=".urlencode($v);
		}
	}
	
	require_once("inc/check-login.php");

	$basedir = $cfg_base_dir."_sites/".$_SESSION['site']."/userimages/".$_SESSION['user_id']."/";
	if (!file_exists($cfg_base_dir."_sites/".$_SESSION['site']."/userimages"))  mkdir($cfg_base_dir."_sites/".$_SESSION['site']."/userimages"); 
	if (!file_exists($basedir)) mkdir($basedir);

	if (!file_exists($basedir."/cropped")) mkdir($basedir."/cropped");
	if (!file_exists($basedir."/cropdata")) mkdir($basedir."/cropdata");
	if (!file_exists($basedir."/thumbs")) mkdir($basedir."/thumbs");
	if (!file_exists($basedir."/original")) mkdir($basedir."/original");
	

	
	if ($a_form_vars["action"] == "delete") {
		if (!empty($fv['img']) &&  file_exists($basedir.$fv['img'])) {
			unlink($basedir.$fv['img']);
		}
	}
	
	if ($a_form_vars["action"] == "upload") {
		$uploaded = move_uploaded_files($basedir);
		$fv['img'] = $_FILES['userfile']['name'];
		$hash .= "&img=".$fv['img'];
		if ($uploaded != 1) {
			$stage = "error";
			$err_msg = $uploaded;
		} else {
			$stage = "crop";
		}
	} else {
			
		// is user logged in? if no, show alert and use tmp folder to upload to. if yes...
		if (!$_SESSION['logged_in']) {
			$stage = "login";
		} else {
			
			// does user folder exist? if no, create. if yes, then skip to next step...
	
			// are there any images in user account? if no, upload. if yes, show images with button to upload another one.
			
			$dir_files = $dir_subdirs = array();
			// Change to directory
			chdir($basedir);
			// Open directory;
			$handle = @opendir($basedir . "/") or die("Directory \"$dir\"not found.");
			// Loop through all directory entries, construct
			// two temporary arrays containing files and sub directories	
			while($entry = readdir($handle)) {
				if (!is_dir($entry) && $entry != ".." && $entry != "." && !ereg("^\.{1,}",$entry) ) {
					$dir_files[] = $entry;
				}
			}
			natcasesort($dir_files);
			$fp = true;
			$have_files = false;
			if (count($dir_files) > 0) {
				$have_files = true;
				$stage = "browse";
				$row = 1;
				$row_list = "";
				foreach($dir_files as $i=>$name) {
					if ($row%2) { $bgcolor = "#eeeeee"; } else { $bgcolor = "#cccccc"; }
					$row_list .= "
						<td class=\"text\" valign=bottom>
							<img border=1 src=\"icon.php?img=".urlencode("userimages/".$_SESSION[user_id]."/".$name)."&ossid=".$_SESSION[ossid]."&s=s.jpg\"><br>
							".$name."<br><br>
							<table cellpadding=0 cellspacing=0 border=0><tr><td>
							<a href=\"javascript:;\" onClick=\"sel_img(this,'".$name."')\">
							<img src=\"images/btn-choose.gif\" border=\"0\">
							</a>
							</td>
							<td>&nbsp;</td>
							<td>
							<a href=\"javascript:;\" onClick=\"del_img(this,'".$name."')\">
							<img src=\"images/btn-delete.gif\" border=\"0\">
							</a>
							</td></tr>
							</table>
						</td>
					";
					
					if ($row%4 == 0) {
						$file_list .= "<tr height=1><td colspan=5><hr size=1 noshade></td></tr><tr>$row_list</tr>";
						$row_list = "";
					} 
					
					++$row;
				
				}
				if ($row_list != "") {
					$file_list .= "<tr height=1><td colspan=5><hr size=1 noshade></td></tr><tr height=110>$row_list</tr>";//<tr height=1><td colspan=5><hr size=1 noshade></td></tr>
				} else {
				//	$file_list .= "<tr height=1><td colspan=5><hr size=1 noshade></td></tr>";
				}
				$file_list = "
				<table width=480 cellpadding=0 cellspacing=0>
				$file_list
				</table>
				";
	
				
				
				//	$file_list .= $name."<br>" ;
				//	$fp = false;
				
			} else {
				$stage = "upload";
				$have_files = 0;
			}
		} 		
	}
	
	chdir($cfg_base_dir);
	
	if (isset($a_form_vars["stage"])) {
		$stage = $a_form_vars["stage"];
	}
	
	// on upload/selection, open image in crop tool with forcecrop flag and image dimensions. 
	
	
	// once cropped, copy to tmp site folder and send image name back to input page.
	
	
	

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<? require_once("inc/style_sheet.php"); ?> 
<title>Choose a Custom Image</title>
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

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function startUpload() {
	MM_showHideLayers('alertmsg','','show');
	MM_swapImage('progress','','admin/images/progress-anim.gif',1);
	document.forms[0].uploadbtn.value = "Uploading...do not close window";
	document.forms[0].uploadbtn.disabled=true;
	document.forms[0].submit();
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
//-->
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body {
	background-color: #EEEEEE;
}
-->
</style>
<script language="JavaScript">
function sel_img(o_link,img) {
	url='custimg.php?<?=$hash?>&stage=crop&img='+img;
	o_link.href=url;
//	alert(url);
}
function del_img(o_link,img) {
	dodelete = false;
	dodelete = confirm("Are you sure you want to delete \""+img+"\"?");
	if (dodelete) {
		url='custimg.php?<?=$hash?>&action=delete&img='+img;
		o_link.href=url;
	}
}
function finish_crop(img) {
	top.opener.document.forms[0].<?=$a_form_vars['obj']?>.value = "userimages/<?=$_SESSION[user_id]?>/cropped/" + img;
//	top.close();
	document.location='custimg.php?stage=success&obj=<?=$a_form_vars['obj']?>';
}

</script>
</head>

<body onLoad="MM_preloadImages('admin/images/progress-anim.gif')" >
<table width="500" border="0" align="center" cellpadding="0" cellspacing="10">
  <tr>
    <td width="480" align="right" nowrap ><span class="text"> <a href="javascript:;" onClick="top.close()" >Cancel</a></span></td>
  </tr>
</table>
<? if ($stage == "upload") { 
if ($have_files) {
	$browse_btn = "<input class=\"text\" type=\"button\" name=\"Button\" value=\"Back to Your Images\" onClick=\"document.location='custimg.php?<?=$hash?>&stage=browse'\">";
}
?>

<span class="text"></span>
<form enctype="multipart/form-data" name="form1" method="post" action="custimg.php" onSubmit="">
  <table width="522" height="292" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#666666">
    <tr>
      <td width="536" valign="top" bgcolor="#EEEEEE"><table width="500" border="0" align="center" cellpadding="0" cellspacing="10">
        <tr>
          <td colspan="2" class="title"><? print($a_form_vars[title]); ?></td>
          <td align="right" class="title"><?=$browse_btn?></td>
        </tr>
        <tr>
          <td height="36" colspan="3" class="subtitle">Upload</td>
          </tr>
        <tr>
          <td width="161" class="text"><?

$min_width = round(($a_form_vars['cropw']/72)*250);
$min_height = round(($a_form_vars['croph']/72)*250);

?><span class="subtitle">Step 1:  </span>Select
            an <strong>RGB JPEG<!-- or PDF--></strong> image on your computer or network that you want to
            use. <br><font color=red><b><i> Note: Image should be at least <?=$min_width?> pixels wide by <?=$min_height?> pixels high.</i></b></font></td>
          <td width="42" class="text">&nbsp;</td>
          <td width="257" height="36" valign="top" class="text"><p>
              <input name="userfile" type="file" style="width:200" size="20">
          </p></td>
        </tr>
        <tr>
          <td class="text"><span class="subtitle">Step 2:</span> Click &quot;Upload&quot; once. </td>
          <td nowrap class="text">&nbsp;</td>
          <td height="32" valign="top" nowrap class="text"><p><input name="uploadbtn" type="button" value="Upload" onClick="startUpload()">            &nbsp;&nbsp;&nbsp;
      <input name="site" type="hidden" id="site" value="<? print($_SESSION[site]); ?>">
      <input name="ossid" type="hidden" id="ossid" value="<? print($ossid); ?>">
      <input type="hidden" name="MAX_FILE_SIZE" value="10000000">      
      <input name="action" type="hidden" id="action" value="upload">
      <input name="folder" type="hidden" id="folder" value="<? print($_GET['folder']);  ?>">
      <input name="title" type="hidden" id="title" value="<? print($a_form_vars['title']);  ?>">
      <input name="fc" type="hidden" id="fc" value="<? print($a_form_vars['fc']);  ?>">
      <input name="obj" type="hidden" id="obj" value="<? print($a_form_vars['obj']);  ?>">
      <input name="cropw" type="hidden" id="cropw" value="<? print($a_form_vars['cropw']);  ?>">
      <input name="croph" type="hidden" id="croph" value="<? print($a_form_vars['croph']);  ?>">

<!--custimg.php?stage=crop&title=<? print($a_form_vars["title"]); ?>&obj=<? print($a_form_vars["obj"]); ?>&fc=<? print($a_form_vars['fc']); ?>&img='+img//-->
      </td>
        </tr>
        <tr>
          <td height="32" colspan="3" nowrap ><span class="text"><img src="images/spacer.gif" name="progress" width="243" height="17" id="progress"></span> </td>
        </tr>
      </table></td>
    </tr>
  </table>
</form>
<? } elseif ($stage == "browse") { ?>
<form name="form2" method="post" action="">
  <table width="522" height="292" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#666666">
    <tr>
      <td width="536" valign="top" bgcolor="#EEEEEE"><table width="500" border="0" align="center" cellpadding="0" cellspacing="10">
          <tr>
            <td class="title"><? print($a_form_vars[title]); ?></td>
            <td align="right"><input class="text" type="button" name="Button" value="Upload Another Image" onClick="document.location='custimg.php?<?=$hash?>&stage=upload'"></td>
          </tr>
          <tr>
            <td height="36" colspan="2" class="subtitle">Your Images </td>
          </tr>
          <tr>
            <td height="36" colspan="2" class="text"><? print($file_list); ?></td>
          </tr>
      </table></td>
    </tr>
  </table>
</form>

<? } elseif ($stage == "crop" && !(bool)$a_form_vars['cached'] ) { ?>

<table width="100%" height="300"><tr><td class="subtitle" align="center">Loading Image...</td></tr></table>

<script language="javascript">
document.location='<?=$_SERVER['SCRIPT_NAME']?>?<?=$hash?>&stage=crop&cached=1'
</script>

<? } elseif ($stage == "crop") { 

include('inc/image.class.php');

$imageName = $a_form_vars['img'];
$path = "_sites/" . $_SESSION[site] . "/userimages/".$_SESSION[user_id]."/";
if (!file_exists($path ."cache/")) 
	mkdir($path ."cache/");
$cached = $path ."cache/". str_replace("field_","",$a_form_vars['obj']) . $imageName;
$orig = $path . $imageName;
cache_image($orig,$cached);

if (file_exists($orig)) {
	$imgdata = getimagesize($orig);

	//$imgtypeid = $imgdata[2];

	$img_width = $imgdata[0];
	$img_height = $imgdata[1];
} else {
	print("WARNING: IMAGE MISSING<br><br>");
}

if ($_GET['fc'] != "false") {
	$_GET['fc'] = "true";
}

?>
<div align="center">
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="550" height="400" id="cropmovie">
  <param name="movie" value="cropMe.swf">
  <param name="quality" value="high">
  <param name="BGCOLOR" value="#EEEEEE">
  <param name="FlashVars" value="img_w=<?=$img_width?>&img_h=<?=$img_height?>&cropw=<?=$fv[cropw]?>&croph=<?=$fv[croph]?>&myImg=<?=$imageName?>&myCroppedImg=<? 
		print(str_replace("field_","",$a_form_vars['obj']).$imageName); 
	?>&path=<?=$path?>&fc=<?=$_GET['fc']?>">
  <embed swLiveConnect="true" name="cropmovie" src="cropMe.swf" FlashVars="img_w=<?=$img_width?>&img_h=<?=$img_height?>&cropw=<?=$fv[cropw]?>&croph=<?=$fv[croph]?>&myImg=<?=$imageName?>&myCroppedImg=<? 
print(str_replace("field_","",$a_form_vars['obj']).$imageName); 
?>&path=<?=$path?>&fc=<?=$_GET['fc']?>" width="550" height="400" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" bgcolor="#EEEEEE"></embed> </object></div> <? } elseif ($stage == "login") { ?>
<table width="522" height="292" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#666666">
  <tr>
    <td width="536" valign="top" bgcolor="#EEEEEE"><table width="500" height="102" border="0" align="center" cellpadding="0" cellspacing="10">
        <tr>
          <td height="36" class="title">Login required </td>
        </tr>
        <tr>
          <td height="36" class="text"><span class="subtitle">Please <a href="javascript:window.close()">close
                this window</a> and click on &quot;Login&quot; on the right side
                of the menu. Then login with your existing account or create
              a new account to upload images to. </span></td>
        </tr>
    </table></td>
  </tr>
</table>
<? } elseif ($stage == "error") { ?>
<table width="522" height="292" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#666666">
  <tr>
    <td width="536" valign="top" bgcolor="#EEEEEE"><table width="500" height="102" border="0" align="center" cellpadding="0" cellspacing="10">
        <tr>
          <td height="36" class="title">Error</td>
        </tr>
        <tr>
          <td height="36" class="text"><? print($err_msg);  ?>&nbsp;</td>
        </tr>
    </table></td>
  </tr>
</table>
<? } elseif ($stage=="success") { 

//print("<!-- ".$cfg_base_dir."_sites/".$_SESSION[site]."/images //-->");

chdir($cfg_base_dir."_sites/".$_SESSION[site]."/images");
if (!file_exists("userimages")) {
	symlink("../userimages", "userimages");
}

?>
<table width="522" height="292" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#666666">
  <tr>
    <td width="536" valign="top" bgcolor="#EEEEEE"><table width="500" height="102" border="0" align="center" cellpadding="0" cellspacing="10">
        <tr>
          <td height="36" class="title">Image Saved </td>
        </tr>
        <tr>
          <td height="36" class="text">You can now <a href="javascript:top.close()">close this window</a> and click &quot;Save &amp; Preview&quot; to view your custom image in your document. &nbsp;</td>
        </tr>
    </table></td>
  </tr>
</table>

<? } ?>
</body>
</html>
