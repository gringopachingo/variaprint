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


require_once("../inc/config.php");

$_POST = array_merge($_POST, $_GET);
if ($_POST['create'] == 1) {


	$width = number_format($_POST['width'],0,".","");
	$height = number_format($_POST['height'],0,".","");
	$res = number_format($_POST['res'],0,".","");
	$scale = $res/72;
	$page_width = intval($scale*$width);
	$page_height = intval($scale*$height);

	if ($res == 0)  {
		$res = 300;
	}

	if ($width == 0 || $height == 0) {
		exit("Invalid width or height. Unable to create file.");
	}

	$pdf = PDF_new();
	PDF_set_parameter($pdf, "license", $cfg_pdflib_license);
	PDF_open_file($pdf, "");
	PDF_set_info($pdf, "Creator", "VariaPrint(tm)");
	PDF_set_info($pdf, "Title", "PDF JPEG wrapper");

//	PDF_open_image(*p, *type, *source, *data, length, width, height, components, bpc, *params);
	$imghndl = PDF_open_image($pdf, "jpeg", "url", $_POST['url'], 0, $width, $height, $_POST['components'], 8, "");
	
	PDF_begin_page($pdf, $page_width, $page_height);
	if ($imghndl != false) {
		PDF_place_image($pdf, $imghndl, 0, 0, $scale);
		PDF_close_image($pdf, $imghndl);
	} else {
		PDF_close_image($pdf, $imghndl);
		exit("Error creating PDF wrapper file.") ;
	}

	PDF_end_page($pdf);
	PDF_close($pdf);

	$buf = PDF_get_buffer($pdf);
	PDF_delete($pdf);
	
	$len = strlen($buf);
	header("Content-Type: application/octet-stream");
//	header("Content-type: attachment/pdf");
	header("Content-Disposition: attachment; filename=wrapper.pdf");
	header("Content-Length: $len");
	print($buf);
	exit;
		
}

// URL, height, width, [Grayscale:1,RGB:3,CMYK:4], resolution
	
?>
<html>
<head>
<title>Create PDF Wrapper</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="form1" method="get" action="">
  <table width="374" border="0" align="center" cellpadding="8" cellspacing="0">
    <tr>
      <td height="30" colspan="4"><p><span class="text"><strong>How it works:</strong> <em>This
              utility will create a PDF file that contains a URL reference to
              an actual hi-res JPEG file located on your local network. This
              PDF wrapper file can then be uploaded as a template background
              (this is the only thing it can be used for.) This will allow your
              press files that contain raster images to be much smaller and faster
              to download since the PDF wrapper file will only be about 2k in
              size. When the press file is opened in Acrobat 4, 5, or 6 (full
              version required), the hi-res image file will be automatically
              opened for printing.</em></span></p>
        <p><span class="text"><em><strong>NOTE:</strong> If any values are entered
              incorrectly below, the PDF wrapper will not work and will return
              an error in Acrobat. The resolution parameter is, however, optional.</em></span>
        <hr size="1" noshade>
        <span class="title"><strong>Create PDF wrapper file for JPEG Image</strong></span><strong></strong>
        </p>
      </td>
    </tr>
    <tr>
      <td height="35" align="right" class="text">URL* </td>
      <td height="35" colspan="3" class="text"><input name="url" type="text" id="url3" value="http://" size="45" maxlength="250">
      </td>
    </tr>
    <tr>
      <td width="39" height="35" align="right" class="text">Width</td>
      <td width="100" class="text"><table width="76" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="64"><input name="width" type="text" id="width5" size="7" maxlength="5">
            </td>
            <td width="12" class="text">px</td>
          </tr>
        </table>
      </td>
      <td width="69" align="right" class="text">Mode**</td>
      <td width="102" class="text"><select name="components" id="components">
          <option value="1">Grayscale</option>
          <option value="3">RGB</option>
          <option value="4" selected>CMYK</option>
        </select>
      </td>
    </tr>
    <tr>
      <td height="35" align="right" class="text"><p>Height</p>
      </td>
      <td height="35" class="text">
        <table width="76" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="64">
              <input name="height" type="text" id="height4" size="7" maxlength="5">
            </td>
            <td width="12" class="text">px</td>
          </tr>
        </table>
      </td>
      <td height="35" align="right" class="text">Resolution</td>
      <td height="35" class="text">
        <table width="81" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="64">
              <input name="res" type="text" id="res4" value="300" size="7" maxlength="3">
            </td>
            <td width="17" nowrap class="text">dpi</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr align="right">
      <td height="35" colspan="4" class="text"><input name="create" type="hidden" id="create" value="1">
        <input type="submit" name="Submit" value="Create">
      </td>
    </tr>
    <tr>
      <td height="35" colspan="4" class="text">&nbsp;</td>
    </tr>
    <tr>
      <td height="35" colspan="4" class="text"><p>*URL must be accessible from
          the workstation that the final press file is printed from and contain
          the complete host and file name (e.g http://10.0.0.5/MyFile.jpg).<strong> It
          may NOT contain any parameters after the end of the filename.</strong></p>
        <p>**PhotoShop CMYK files must be inverted.</p>
      </td>
    </tr>
  </table>
</form>
</body>
</html>
