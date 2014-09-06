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
	
	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	require_once("../inc/functions_pdf.php");
	require_once("inc/functions.php");
	require_once("inc/popup_log_check.php");

	function make_jpeg ($img,$scale) {
		$tmpdir = "/tmp";
		
		srand((double)microtime()*1000000); $rand = rand(1000000,9999999999);
	
		$jpegfile = $tmpdir . "/tmp$rand.jpg";
		
		$File = new File;
		
		// write pdf to file so ghostscript can read and rasterize it
		if(!$File->write_file( $jpegfile, $img )) {
			$error = 1;
		}
	
		// resize image
		if ( file_exists($jpegfile)  && file_exists("/usr/local/bin/convert")) {
			$command = CLI_CONVERT . " -quality 100 -geometry $scale% " . $jpegfile . " jpeg:" . $jpegfile ;
			$msg .= `$command`;
			
		//	exit($command);
		} elseif (!file_exists($jpegfile)){
			$error = 1;
		}
	
					
		if ( file_exists($jpegfile) && file_exists("/usr/local/bin/jpegoptim")) {
			if ( file_exists("/usr/local/bin/jpegoptim") ) {
				$command = CLI_JPEGOPTIM . " -m60 " . $jpegfile; 
				$msg .= `$command`;
			}
		} elseif (!file_exists($jpegfile)) {
			$error = 1;
		}

		$image = $File->read_file($jpegfile);

		if ( $error != 1) {
			return $image;
		} else {
		//	print($commands);
			return 0;
		}
	}





	$img = $a_form_vars["img"];
	$file = $GLOBALS["cfg_base_dir"] . "_sites/" . $_SESSION[site] . "/images/" . $img;
	
	if (file_exists($file)) {
		
		$image_file = new File;
		$image = $image_file->read_file($file);
		
		$exp_file = explode(".",$img);
		$ext = $exp_file[count($exp_file)-1];

		$page_width = 252;
		$img_width = 252;
		
		if (strtoupper($ext) == "PDF") {
			
			$pdf = PDF_new();
			PDF_open_file($pdf, "");
			PDF_begin_page($pdf, "100", "100");
			$import_pdf_obj = PDF_open_pdi($pdf, $file, "", 0);
			if ($import_pdf_obj != 0) {
				$pdf_img = PDF_open_pdi_page($pdf, $import_pdf_obj, 1, "");
				if ($pdf_img != false) {
					$img_width = PDF_get_pdi_value($pdf, "width", $import_pdf_obj, $pdf_img, 0);
				//	$img_height = PDF_get_pdi_value($pdf, "height", $import_pdf_obj, $pdf_img, 0);
			 	}
			}
			$scale = 100;//($page_width/$img_width)*100;
			PDF_end_page($pdf);
			PDF_close($pdf);
			PDF_get_buffer($pdf);
			PDF_delete($pdf);
			
			$image = pdf_rasterize($image,$scale,2);
		} else {
			$imgdata = getimagesize($file);
			$imgtypeid = $imgdata[2];
			
			$img_width = $imgdata[0];
		//	$img_height = $imgdata[1];
			
			$scale = ($page_width/$img_width)*100;
			$image = make_jpeg($image,$scale);
			
		//	exit("width: ".$img_width);
			
			
		}
		
		$len = strlen($image);
		
		Header("Content-type: image/jpg");
		Header("Content-Length: $len");
		Header("Content-Disposition: inline; filename=img.jpg");
		print($image);
		
	}  else {
		print("file not found.");
	}
?>
