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


	require_once("../../inc/config.php");
	require_once("../../inc/functions-global.php");
	$a_form_vars = array_merge($_POST,$_POST);
	
	session_name("mssid");
	session_start();

	require_once("../inc/popup_log_check.php");

//	require_once("../inc/popup_log_check.php");
	
	
	$user_dir = $cfg_base_dir . "_users/" . $_SESSION["user_id"] . "/";
	$font_dir = $user_dir . "fonts/";
	
	if (!file_exists($user_dir)){
		`CLI_MKDIR $user_dir`;
	}
	if (!file_exists($font_dir) && $_SESSION["user_id"] != ""){
		`CLI_MKDIR $font_dir`;
	}
	

	if ($a_form_vars['step'] == "3" && $_SESSION["user_id"] != "") {
		$path = $font_dir;					
				
		$error = $_FILES['userfile']['error'];	
		
		// MAIN PROGRAM *************************************************************************
		if ($error == 0) {
			$fileName = $_FILES['userfile']['name'];			
			move_uploaded_file($_FILES['userfile']['tmp_name'], $path.$fileName);
			chmod($path.$fileName, 0755);
			
			$a_fname = explode(".",$fileName);
			if (strtoupper($a_fname[count($a_fname)-1]) == "ZIP") {
				chdir($font_dir);
				$unzip_cmd = CLI_UNZIP . "\"$fileName\"";
				`$unzip_cmd`;
				unlink($path.$fileName);
			}
			
			chdir($font_dir);
			if (file_exists("PSres.upr")) 
				unlink("PSres.upr");
			$mkpsres_cmd = CLI_MKPSRES . "\"$font_dir\"";
			`$mkpsres_cmd`;
			
			$file = new File;
			
			$psres = $file->read_file("PSres.upr");
			
			
			$haveAFMsection = false;
			$a_psres = explode(".\n",$psres);
			$a_sections = explode("\n",$a_psres[0]);
			if (is_array($a_sections)) {
				foreach ($a_sections as $k=>$section) {
					if (trim($section) == "FontFamily") {
						$fontfamilykey = $k;
						//break;
					}
					if (trim($section) == "FontAFM") {
						$haveAFMsection = true;
					}		
				}
			}
			
			$fontfamily = $a_psres[$fontfamilykey];
			$fontfamily = str_replace("\\\n","",$fontfamily);
			$a_fontfamily = explode("\n",$fontfamily);
			$len = count($a_fontfamily);
			if (trim(strtolower($a_fontfamily[0])) == "fontfamily") {
				$pos = 1;
			} elseif (trim(strtolower($a_fontfamily[1])) == "fontfamily") {
				$pos = 2;
			} elseif (trim(strtolower($a_fontfamily[2])) == "fontfamily") {
				$pos = 3;
			}

			for($i=$pos;$i<=$len-$pos+1;$i++) {
				if (trim($a_fontfamily[$i]) != "") {
					$a_font = explode("=",$a_fontfamily[$i]);
					$a_face["family"] = $a_font[0];
					$a_faces = explode(",",$a_font[1]);
					$face_len = count($a_faces);
					$cntr = 0;
					while ($face_len > $cntr) {
						$face["real_name"] = $a_faces[$cntr];
						$face["sys_name"] = $a_faces[$cntr+1];
						
						$a_face["faces"][] = $face;
						
						$cntr += 2;
					}
					$a_fonts[] = $a_face;
					unset($a_face);
				}
			}
			
			if (is_array($a_fonts)) {
				foreach ($a_fonts as $font) {
					$xml .= "      <fontfamily name=\"$font[family]\">\n";
					$fontsinstalled .= "<strong>$font[family]</strong><br>\n";
					if (is_array($font["faces"])) {
						foreach ($font["faces"] as $face) {
							$realname = trim(trim($face[real_name],"-")) ;
							$xml .= "         <style name=\"$realname\" familyname=\"$face[sys_name]\"/>\n";
					$fontsinstalled .= "&bull; &nbsp; $realname<br>\n";
						}
					}
					$xml .= "      </fontfamily>\n";
					$fontsinstalled .= "<br>\n";
				}
			}
			
			$xml = "<" . "?xml version=\"1.0\"?" . ">
    <fonts>
$xml
    </fonts>";
			
			
//			print($xml);
			
			$sql = "UPDATE AdminUsers SET Fonts='$xml' WHERE ID=$_SESSION[user_id]";
			dbq($sql);

			
		//	$a_psres = explode(".\n",$psres);
		//	$a_sections = explode("\n",$a_psres[0]);
			$lastSection = count($a_psres)-2;
			if (is_array($a_sections)) {
				foreach ($a_psres as $k=>$section) {
					if ($k == 0 && !$haveAFMsection) {
						$section .= "FontAFM\n";
					}
					if (trim($section) != "") {// && 
						$str_psres .= $section.".\n";
						if ($k > 0 && $k < $lastSection) { 
							$str_psres .= "//www/vp/_users/$_SESSION[user_id]/fonts\n";
						}
					}
				}
			}
			
			if (!$haveAFMsection) {
				$str_psres .=
				"FontAFM
ThisFontIsHereAsAWorkaroundForPDFlibAndItsApparentNeedForAFM=stupid.afm
.";		
			}
			
			
			if (file_exists("PSres.upr")) 
				unlink("PSres.upr");
			$file->write_file("PSres.upr",$str_psres);
			
			
		//	print_r($_SESSION);
			
/*			print("
			<script language=\"javascript\">
			//	top.opener.top.mainFrame.location.reload(0) 
			//	top.close()
			</script>
			");*/
		//	exit();
		} else {
			// There was an error
			switch ($error) {
				case "1": $err_msg = "The file was too big to upload."; break;
				case "2": $err_msg = "The file was too big to upload."; break;
				case "3": $err_msg = "It seems the transfer was interrupted."; break;
				case "4": $err_msg = "It doesn't look like a file was selected to upload."; break;
			}
			
			exit("<span class=\"text\">Error uploading file ($error). $err_msg <a href=\"javascript:window.close();\">close</a></span>");
		}/**/
		
		} elseif ($_SESSION[site] == "") {
		
		exit("Error. File was too large.  <a href=\"javascript:window.close();\">close</a>");	
	}
	
	

?>
<html>
<head>
<title>Add Font</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="400" height="94%"><tr><td>
<form action="addfont.php" method="post" enctype="multipart/form-data" name="form1">
  <? if (!isset($_POST["step"])) { ?>
  <p><span class="text"><strong>Note:</strong> Any fonts that you add 
      must be appropriately licensed for your use including font embedding. The
      fonts you add will only be accessible for use in your documents. </span> </p>
  <p>
    <input type="button" value="Cancel" onClick="top.close()">
    <input type="submit" value="OK">
    <input name="step" type="hidden" id="step" value="2">
  </p>
  <? } elseif ($_POST["step"] == "2") { ?>
  <span class="text"><strong>Upload a Zip file of your PC Type 1 fonts.*</strong> <br>
  <br>
  There will be
  two files per font: either a pair with the
  extensions &quot;afm&quot; and &quot;pfa&quot; <strong>-or-</strong> a pair
  with &quot;pfm&quot; and &quot;pfb&quot;.</span><br>
  <br>
  <table width="384" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="107" height="30" class="text">Select Zip File</td>
      <td width="277" height="30" align="right"><input name="userfile" type="file" id="userfile">
      </td>
    </tr>
    <tr>
      <td height="30" class="text">&nbsp;</td>
      <td height="30" align="right">&nbsp;</td>
    </tr>
    <tr>
      <td height="30" class="text">&nbsp;</td>
      <td height="30" align="right">        <input type="hidden" name="MAX_FILE_SIZE" value="10000000"> 
<input name="button" type="button" onClick="top.close()" value="Cancel">        <input name="step" type="hidden" id="step" value="3">        <input type="submit" name="Submit" value="Add"></td>
    </tr>
  </table>
  <br>
  <? } elseif ($_POST["step"] == "3") { ?>
<span class="text"><strong>Note: Reload the template editor for font changes
to
take
effect.</strong><br>
<br>
All fonts currently installed: <br>
<br>
<? print($fontsinstalled); ?>
<br><br>
</span>
    <input type="submit" value="Upload More Fonts">
    <input type="button" value="Done" onClick="top.close()">
    <input name="step" type="hidden" id="step" value="2">
  <br><br><br><br>
  <? } 
  if ($_POST["step"] == "2") { 
  ?>
<span class="text"> *Convert any Mac, TrueType, OpenType, or MM fonts to Unix
    Type 1 (.pfa and .afm) and compress them into a Zip file before uploading. 
</span>
	
<? } ?>
</form>
</td></tr></table>
</body>
</html>
