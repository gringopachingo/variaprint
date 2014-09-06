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


function registration_mark($pdf, $centerX, $centerY) {
	PDF_circle($pdf, $centerX, $centerY, 6);
	
	PDF_moveto($pdf, $centerX, $centerY+10);
	PDF_lineto($pdf, $centerX, $centerY-10);
	PDF_stroke($pdf);

	PDF_moveto($pdf, $centerX-10, $centerY);
	PDF_lineto($pdf, $centerX+10, $centerY);
	PDF_stroke($pdf);
}



function createPreview ($siteid, $templateid, $prefillXML, $fileid, $filepath, $type="Render PDF & JPEG") {
	global $pdf_vars;

	if (!function_exists("getmicrotime")) {
	  	function getmicrotime(){
			list($usec, $sec) = explode(" ",microtime());
			return ((float)$usec + (float)$sec);
		}
	}

	if ( strtoupper($siteid) == "AUTO" ) {
		$sql = "SELECT Template,SiteID
		FROM ".TABLE_ITEM_TEMPLATES." 
		WHERE ID='$templateid'";
	} else {
		$sql = "SELECT Template,SiteID
		FROM ".TABLE_ITEM_TEMPLATES." 
		WHERE ID='$templateid' AND SiteID='$siteid'";
	}

	$nResult = dbq($sql);
	if ( mysql_num_rows($nResult) == 0 ) {
		return array("error"=>true,"errorMsg"=>"Error. No template found.\n"); exit;
	}
	$aItem = mysql_fetch_assoc($nResult);
	$siteid = $aItem['SiteID'];
	$aPrefillTree = xml_get_tree($prefillXML);
	$a_prefill = array();
	if ( is_array($aPrefillTree[0]['children']) ) {   
		foreach ( $aPrefillTree[0]['children'] as $v ) {
			if ( $v['value'] != "") { $a_prefill[$v['attributes']['ID']] = $v['value']; }
 		}
	} else {
	//	return array("error"=>true,"errorMsg"=>"no prefill info found.\n"); exit;
	}

	/* Make the PDF */
	// We need a PDF proof, raster proof, and PDF press file
 	$aItem['Template'] = utf8ToISO_8859_1($aItem['Template']);
  	$buf = pdf_create($aItem['Template'],$a_prefill,"",$siteid);
  	$pressbuf = pdf_create($aItem['Template'],$a_prefill,"press",$siteid);

 	// save generated image
 	if ( ($scale = (350 / $pdf_vars['pagewidth'])*100) > 180 ) { $scale = 180; }	

 	$starttime = getmicrotime();
	$img = pdf_rasterize( $buf, $scale );
 	$endtime = getmicrotime();
	$totalrendertime = $endtime - $starttime;

	$File = new File;
	$img_file = $filepath . $fileid . "_preview_raster.jpg";
 	if ( file_exists($img_file) ) { unlink($img_file) ; }
 	if(!$File->write_file( $img_file, $img )) { $error = 1; }

	$pdf_file = $filepath . $fileid. "_preview_pdf.pdf";
	if ( file_exists($pdf_file) ) { unlink($pdf_file) ; }           // Make sure it's not already here
	if(!$File->write_file( $pdf_file, $buf )) { $error = 1; }

	$pdf_file2 = $filepath . $fileid . "_press_pdf.pdf";
	if ( file_exists($pdf_file2) ) { unlink($pdf_file2) ; }         // Make sure it's not already here
	if(!$File->write_file( $pdf_file2, $pressbuf )) { $error = 1; }

	$sql = "INSERT LOW_PRIORITY INTO ProcessTimeLog SET TimeStamp='" . time() . "', ItemID='$fileid', Type='$type', ProcessTime='$totalrendertime'";
	$nInsert = dbq($sql);		

	if ($error) {
		return array("error"=>true,"errorMsg"=>"could not write preview files.\n"); exit;			
	} else {
		return true;
	}

}



/* 
Arrays to maintain variables in for xml processing.
Holds variables like the parent and grandparent attributes.
*/

/*
Initialize PDF


End PDF

*/

$pdf_vars = array();


/*
Set defaults and document settings
*/

$pdf_vars['global_attrib'][] = "forcecase";
$pdf_vars['global_attrib'][] = "font_size";
$pdf_vars['global_attrib'][] = "font_face";
$pdf_vars['global_attrib'][] = "font_fill_color";
$pdf_vars['global_attrib'][] = "font_fill_color_tint";
$pdf_vars['global_attrib'][] = "font_stroke_color";
$pdf_vars['global_attrib'][] = "font_stroke_color_tint";
$pdf_vars['global_attrib'][] = "font_stroke_width";
$pdf_vars['global_attrib'][] = "leading";
$pdf_vars['global_attrib'][] = "textalign";
$pdf_vars['global_attrib'][] = "tracking";
$pdf_vars['global_attrib'][] = "horizontalscale";
$pdf_vars['global_attrib'][] = "text_rendering";
$pdf_vars['global_attrib'][] = "underline";

$pdf_vars['box_attrib'][] = "leftindent";
$pdf_vars['box_attrib'][] = "rightindent";
$pdf_vars['box_attrib'][] = "firstlineindent";
$pdf_vars['box_attrib'][] = "paragraphprefix";
$pdf_vars['box_attrib'][] = "wrap";

$pdf_vars['ifg_attrib'][] = "spacebefore";
$pdf_vars['ifg_attrib'][] = "spaceafter";

$pdf_vars['field_attrib'][] = "keep";
$pdf_vars['field_attrib'][] = "charsbefore";
$pdf_vars['field_attrib'][] = "charsafter";
$pdf_vars['field_attrib'][] = "spacebefore";
$pdf_vars['field_attrib'][] = "spaceafter";

function get_microtime(){ 
	list($usec, $sec) = explode(" ",microtime()); 
	return ((float)$usec + (float)$sec); 
} 
		


function pdf_rasterize2( $pdfbuf, $scale=100, $resolution=72, $aaFactor=2, $outputType="jpeg" ) {
	
	$starttime = get_microtime();
	
	$tmpdir = "/tmp";
	$error = 0;
	$File = new File;
	srand((double)microtime()*1000000);
	$rand = rand(1000000,9999999999);
	$pdffile = $tmpdir . "/tmp$rand.pdf"; 
	$jpegfile = $tmpdir . "/tmp$rand.jpg";

	// make sure we got a unique number
	while (file_exists($pdffile)) {
		$rand = rand(1000000,9999999999);
		$pdffile = $tmpdir . "/tmp$rand.pdf"; 
		$jpegfile = $tmpdir . "/tmp$rand.jpg";	
	}

	if(!$File->write_file( $pdffile, $pdfbuf )) {
		$error = 1;
	}

	$aaFactor = 2;
	$gs_res = " -r".$resolution*$aaFactor;
	
	// rasterize
	if ( file_exists($pdffile) && !$error ) {
		$command = CLI_GS . " -sDEVICE=" . $outputType . " -q " . $gs_geo.$gs_res . " -dBATCH  -dNOPAUSE -dJPEGQ=100 -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -sOutputFile=" . $jpegfile . " " . $pdffile ;
//	print($command);
		$msg = `$command`;
	} else {
		$error = 1;
	}

	$imgdata = getimagesize($jpegfile);
	$px_width = $imgdata[0]/$aaFactor;
	$px_height = $imgdata[1]/$aaFactor;
	$im_geo = "'".$px_width."x".$px_height."' " ;

	// resize image
	if ( file_exists($jpegfile)  && file_exists(CLI_MOGRIFY)) {
//		$command = CLI_CONVERT" -quality 100 -geometry " . $im_geo  . $jpegfile . " " . $jpegfile ;
		$command = CLI_MOGRIFY . " -quality 100 -size 50% -resize 50% $jpegfile";
		$msg .= `$command`;
	} else {
		$error = 1;
	}
	 
	if ( file_exists($jpegfile) && file_exists(CLI_JPEGOPTIM)) {
		$command = CLI_JPEGOPTIM . " -m100 " . $jpegfile; 
		$msg .= `$command`;
	} else if (!file_exists($jpegfile)) {
		$error = 1;
	}
	
	// read file
	$img = $File->read_file($jpegfile);
	
	unlink($jpegfile);
	unlink($pdffile);
	
	$endtime = get_microtime();
	$totalrendertime = $endtime - $starttime;
	$sql = "INSERT LOW_PRIORITY INTO ProcessTimeLog  SET TimeStamp='" . time() . "', Type='pdf_rasterize2()', ProcessTime='$totalrendertime'";	
	dbq($sql);
		
	if ( $error != 1) {
		return $img;
	} else {
		return 0;
	}
}


function pdf_rasterize( $pdfbuf, $scale=20, $aaFactor=1, $outputType="jpeg") {
	$tmpdir = "/tmp";
	if ($scale == "") $scale = 20;
	$error = 0;
	
	$File = new File;

	srand((double)microtime()*1000000); 
	$rand = rand(1000000,9999999999);
	$pdffile = $tmpdir . "/tmp$rand.pdf";
	$jpegfile = $tmpdir . "/tmp$rand.jpg";

	// make sure we got a unique number
	while (file_exists($pdffile)) {
		$rand = rand(1000000,9999999999);
		$pdffile = $tmpdir . "/tmp$rand.pdf"; 
		$jpegfile = $tmpdir . "/tmp$rand.jpg";	
	}
		
	// write pdf to file so ghostscript can read and rasterize it
	if(!$File->write_file( $pdffile, $pdfbuf )) {
		$error = 1;
	}
//	if ($scale<1) $scale = 10;
	
	$px_based = false;
	if (ereg("x",$scale)) {
		$a_scale = explode("x",$scale);
		if ($a_scale[0]>$a_scale[1]) {
			$scale = $a_scale[1];
		} else {
			$scale = $a_scale[0];
		}
		$px_based = true;
	}
	$aaFactor = 2;
	$resizeFactor = $scale/100 ;
	$resolution = $resizeFactor * $aaFactor * 72 ;
	$resizePercent = (100 / $aaFactor) . "% ";
	$sResolution = " -r" . round($resolution);
	if ($px_based) {
		$resizePercent = "'".$a_scale[0]."x".$a_scale[1]."' ";
	}
	
	// rasterize
	if ( file_exists($pdffile) ) {
		$command = CLI_GS . " -sDEVICE=" . $outputType . 
				" -q " . $sResolution . " -dBATCH " . $dimensions . 
				"  -dNOPAUSE -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -sOutputFile=" . $jpegfile . " " . $pdffile ;
	//	print("<!--  $command //-->");


	//	$commands .= $command . "\n";
		$msg = `$command`;

	} else {
		$error = 1;
	}
	
	// resize image
	if ( file_exists($jpegfile)  && file_exists(CLI_MOGRIFY)) {
		$command = CLI_MOGRIFY ." -quality 100 -resize 50% " . $jpegfile;
	//	$commands .= $command . "\n";
		$msg .= `$command`;
	} else {
		$error = 1;
	}

				
	if ( file_exists($jpegfile) && file_exists(CLI_JPEGOPTIM)) {
		$command = CLI_JPEGOPTIM . " -m90 " . $jpegfile; 
	//	$commands .= $command . "\n";
		$msg .= `$command`;
	} else if (!file_exists($jpegfile)) {
		$error = 1;
	}
	// Make it a progressive JPEG
	if ( file_exists($jpegfile) && file_exists(CLI_JPEGTRAN) )  {
		$command = CLI_JPEGTRAN . " -optimize -progressive " . $jpegfile; 
//		$commands .= $command . "\n";

		// NOT USING BECAUSE IT JUST SLOWS DOWNT THE IMAGE RENDER
//		$msg .= `$command`;
	}
	//	copy($output, $output.".backupoffinal");
	//	if (file_exists ($tmpFile)) { unlink($tmpFile); }

	// read file
	$img = $File->read_file($jpegfile);
		
	
	if ( $error != 1) {
		return $img;
	} else {
		return 0;
	}
}


function pdf_place_text($string) {
	global  $pdf_vars ;
	PDF_show($pdf_vars['pdf'], $string);
}

function pdf_set_font_attributes() {
	global $pdf_vars ;
	
	
	$fontlist["Courier"] = "winansi";
	$fontlist["Courier-Bold"] = "winansi";
	$fontlist["Courier-Oblique"] = "winansi";
	$fontlist["Courier-BoldOblique"] = "winansi";
	$fontlist["Helvetica"] = "winansi";
	$fontlist["Helvetica-Bold"] = "winansi";
	$fontlist["Helvetica-Oblique"] = "winansi";
	$fontlist["Helvetica-BoldOblique"] = "winansi";
	$fontlist["Times-Roman"] = "winansi";
	$fontlist["Times-Bold"] = "winansi";
	$fontlist["Times-Italic"] = "winansi";
	$fontlist["Times-BoldItalic"] = "winansi";
	$fontlist["Symbol"] = "builtin";
	$fontlist["ZapfDingbats"] = "builtin";
	
	// Set global attributes, checking field, ifg, box, and defaults
	foreach ( $pdf_vars['global_attrib'] as $index => $property ) {
		$xml_attribname = strtoupper( str_replace("_", "", $property));
		if (!is_array($pdf_vars['field']['attributes'])) { unset($pdf_vars['field']['attributes']); }
		$field_attrib = $pdf_vars['field']['attributes'][$xml_attribname] ;
		if (!is_array($pdf_vars['inlinefieldgroup']['attributes'])) { unset($pdf_vars['inlinefieldgroup']['attributes']); }
		$ifg_attrib = $pdf_vars['inlinefieldgroup']['attributes'][$xml_attribname] ;
		$topbox_attrib = $pdf_vars['contentbox']['attributes'][$xml_attribname] ;

		if ($pdf_vars['field']['attributes']['CUSTOMFORMAT'] == "true") {
			if ( trim($field_attrib) != "") {  			//	FIELD
				$pdf_vars[$property] = $field_attrib;
			} else if ( trim($ifg_attrib) != "") {  		//	INLINE FIELD GROUP (alignment)
				$pdf_vars[$property] = $ifg_attrib;
			} else {								//  DEFAULTS
				$pdf_vars[$property] = $pdf_vars['defaults'][$property];
			}	
		} else {
			
			if ( trim($topbox_attrib) != "") {  			//	TOP BOX	
				$pdf_vars[$property] = $topbox_attrib;
			} else {								//	DEFAULTS	
				$pdf_vars[$property] = $pdf_vars['defaults'][$property];
			}	

		}
		
		if ( isset($fontlist[$pdf_vars["font_face"]])) { 
			$embed = "false"; 
			$encode = $fontlist[$pdf_vars["font_face"]];
		} else {
			$embed = "true" ;
			$encode = "winansi";
		}
	}
	
	// Set box attributes
	foreach ($pdf_vars['box_attrib'] as $index => $property) {
		$xml_attribname = strtoupper( str_replace("_", "", $property));
		$pdf_vars[$property] = $pdf_vars['contentbox']['attributes'][$xml_attribname] ;
	}
	
	// Set IFG attributes
	foreach ($pdf_vars['ifg_attrib'] as $index => $property) {
		$xml_attribname = strtoupper( str_replace("_", "", $property));
		$pdf_vars[$property] = $pdf_vars['inlinefieldgroup']['attributes'][$xml_attribname] ;
	}
	
	// Set field attributes
	foreach ($pdf_vars['field_attrib'] as $index => $property) {
		$xml_attribname = strtoupper( str_replace("_", "", $property));
		$pdf_vars[$property] = $pdf_vars['field']['attributes'][$xml_attribname] ;
	}
	
	
	if ($pdf_vars['font_stroke_color'] != "") {
		$pdf_vars['font_stroke_color_tint'] = ereg_replace("a-zA-Z ", "", $pdf_vars['font_stroke_color_tint']);
		if ($pdf_vars['font_stroke_color_tint'] == "") {
			$pdf_vars['font_stroke_color_tint'] = 1;
		}
		$tint = $pdf_vars['font_stroke_color_tint']/100;
		$colorid = $pdf_vars['font_stroke_color'];
		if (!isset($pdf_vars["color"][$colorid]) || $colorid == "") {
			$colorid = $pdf_vars["color"]["black_id"];
		}
		$c = $pdf_vars["color"][$colorid]["C"]/100;
		$m = $pdf_vars["color"][$colorid]["M"]/100;
		$y = $pdf_vars["color"][$colorid]["Y"]/100;
		$k = $pdf_vars["color"][$colorid]["K"]/100;
		
		if ($pdf_vars["color"][$colorid]["SPOT"] == "true") 
			$cspace = "spot";
		else 
			$cspace = "cmyk";
			
		$colorname = $pdf_vars["color"][$colorid]["NAME"];//str_replace(" ", "",)
		
		if ($cspace == "spot") {
			if ($tint == "") { $tint = 1; }
			$spotVariableName = $colorname;//ereg_replace(" ", "",$colorname);
			
			PDF_setcolor($pdf_vars['pdf'], "fill", "cmyk", $c, $m, $y, $k);
			$spotVariable = PDF_makespotcolor($pdf_vars['pdf'], $spotVariableName);
			PDF_setcolor($pdf_vars['pdf'], "stroke", "spot", $spotVariable, $tint);

		} else if ($cspace == "gray") {
			PDF_setcolor($pdf_vars['pdf'], "stroke", "gray", $c, $m, $y, $k);
		} else {
			$c = $c*$tint; $m = $m*$tint; $y = $y*$tint; $k = $k*$tint; 
			PDF_setcolor($pdf_vars['pdf'], "stroke", "cmyk", $c, $m, $y, $k);
		}
		
	}

	$colorid = $pdf_vars['font_fill_color'];
	if (!isset($pdf_vars["color"][$colorid]) || $colorid == "") {
		$colorid = $pdf_vars["color"]["black_id"];
	}
	$pdf_vars['font_fill_color_tint'] = ereg_replace("a-zA-Z ", "", $pdf_vars['font_fill_color_tint']);
	if ($pdf_vars['font_fill_color_tint'] == "") {
		$pdf_vars['font_fill_color_tint'] = 1;
	}
	$tint = $pdf_vars['font_fill_color_tint']/100;
	
	$c = $pdf_vars["color"][$colorid]["C"]/100;
	$m = $pdf_vars["color"][$colorid]["M"]/100;
	$y = $pdf_vars["color"][$colorid]["Y"]/100;
	$k = $pdf_vars["color"][$colorid]["K"]/100;
	if ($pdf_vars["color"][$colorid]["SPOT"] == "true") 
		$cspace = "spot";
	else 
		$cspace = "cmyk";

	$colorname = $pdf_vars["color"][$colorid]["NAME"];//ereg_replace(" ", "",)
	if ($cspace == "spot") {
		if ($tint == "") { $tint = 1; }
		$spotVariableName = $colorname ;//ereg_replace(" ", "", $colorname);
		
		PDF_setcolor($pdf_vars['pdf'], "fill", "cmyk", $c, $m, $y, $k);
		$spotVariable = PDF_makespotcolor($pdf_vars['pdf'], $spotVariableName);
		PDF_setcolor($pdf_vars['pdf'], "fill", "spot", $spotVariable, $tint);
		
	} else if ($cspace == "gray") {
		PDF_setcolor($pdf_vars['pdf'], "fill", "gray", $c, $m, $y, $k);
	} else {
		$c = $c*$tint; $m = $m*$tint; $y = $y*$tint; $k = $k*$tint; 
		PDF_setcolor($pdf_vars['pdf'], "fill", "cmyk", $c, $m, $y, $k);
	}
	
	if ($pdf_vars['font_stroke_width'] > 0) {
		PDF_setlinewidth($pdf_vars['pdf'], $pdf_vars['font_stroke_width']);
	}

	if ( $pdf_vars['underline'] == "true" ) {
		PDF_set_parameter($pdf_vars['pdf'], "underline", "true");
	} else {
		PDF_set_parameter($pdf_vars['pdf'], "underline", "false");
	}
	
	
	if ( trim($pdf_vars['tracking']) != "" && isset($pdf_vars['tracking']) ) {
		PDF_set_value($pdf_vars['pdf'], "charspacing", $pdf_vars['tracking']/100);
	} else {
		PDF_set_value($pdf_vars['pdf'], "charspacing", 0);
	}
	
	if ( $pdf_vars['text_rendering'] == "outline" ) {
		PDF_set_value($pdf_vars['pdf'], "textrendering", 1);
	} else if ( $pdf_vars['text_rendering'] == "fillandoutline" ) {
		PDF_set_value($pdf_vars['pdf'], "textrendering", 2);
	} else {
		PDF_set_value($pdf_vars['pdf'], "textrendering", 0);
	}
	
	//trim($pdf_vars['horizontalscale']) != "" && isset($pdf_vars['horizontalscale']) && $pdf_vars['horizontalscale'] <= 0
	if ( number_format($pdf_vars['horizontalscale'],11,".","") > 0) {
		if ($pdf_vars['horizontalscale'] <= 0) { $pdf_vars['horizontalscale'] = 100; } 
		PDF_set_value($pdf_vars['pdf'], "horizscaling", $pdf_vars['horizontalscale']);
	} else {
		PDF_set_value($pdf_vars['pdf'], "horizscaling", 100);
	}
	
//	$pdf_vars['font'] = PDF_findfont($pdf_vars['pdf'], $pdf_vars['font_face'], $encode, $embed);
	$pdf_vars['font'] = PDF_load_font($pdf_vars['pdf'], $pdf_vars['font_face'], $encode, "embedding=$embed kerning=true");

	if ($pdf_vars['font_size'] <= 0) {
		$pdf_vars['font_size'] = 10;
	}
	
	PDF_setfont($pdf_vars['pdf'], $pdf_vars['font'], $pdf_vars['font_size']);
	
}

function place_graphic ($graphic="") {
	global $pdf_vars;
	
	if (trim($graphic)=="") { 
		return false;
	} else {
		$place_size = 1;
	
		$img = $GLOBALS['cfg_base_dir'] . "_sites/" . $pdf_vars[siteid] . "/images/" . $graphic; 
	
		$fit_style = $pdf_vars['contentbox']['attributes']['GRAPHIC_FIT'];						
		$fit_width = $pdf_vars['contentbox']['attributes']['WIDTH'];
		$fit_height = $pdf_vars['contentbox']['attributes']['HEIGHT'];
		
		if ( file_exists($img) ) {
			$file_parts = explode(".",$graphic);
			$ext = $file_parts[count($file_parts)-1];
		
			//list(,$ext) =  
			$ext = strtolower($ext);
	
			// Check to see if it's a PDF
			if ($ext == "pdf") {
			
				$import_pdf_obj = PDF_open_pdi($pdf_vars['pdf'], $img, "", 0);
				if (!$import_pdf_obj) {
					$error += "Couldn't open input file".$infile;
				} else {
				
					$pdf_img = PDF_open_pdi_page($pdf_vars['pdf'], $import_pdf_obj, 1, "");
					if (!$pdf_img) {
						$error += "Couldn't open page 1 in ".$infile;
					} else {
														
						# get the dimensions of the imported form
						$img_width = PDF_get_pdi_value($pdf_vars['pdf'], "width", $import_pdf_obj, $pdf_img, 0);
						$img_height = PDF_get_pdi_value($pdf_vars['pdf'], "height", $import_pdf_obj, $pdf_img, 0);
					
						switch ($fit_style) {
							case "":
								$place_size = 1;
								break;
							
							case "width":
								if ($img_width <= 0) {
									$img_width = 50;
								} 
								if ($img_height <= 0) {
									$img_height = 50;
								} 
								$place_size = $fit_width/$img_width;
								$move_down = $fit_height/$img_height;
								break;
								
							case "height" :
								if ($img_width <= 0) {
									$img_width = 50;
								} 
								$place_size = $fit_height/$img_height;
								
								break;
								
							case "scaleto" :
								if ($img_width <= 0) {
									$img_width = 50;
								} 
								if ($img_height <= 0) {
									$img_height = 50;
								}
	
								$width_scale = $fit_width/$img_width;
								$height_scale = $fit_height/$img_height;
								
								if ($width_scale > $height_scale) {
									$place_size = $height_scale;
								} else {
									$place_size = $width_scale;
								}
						}
						
						$move_down = -1*$place_size*$img_height;
						
						PDF_translate($pdf_vars['pdf'], 0, $move_down);
						
						PDF_place_pdi_page($pdf_vars['pdf'], $pdf_img, 0, 0, $place_size, $place_size);
						PDF_close_pdi_page($pdf_vars['pdf'], $pdf_img);
						PDF_close_pdi($pdf_vars['pdf'], $import_pdf_obj);
					}
				}
				
			} else { // We'll see if it's a raster graphic
					
				$imgdata = getimagesize($img);
				$imgtypeid = $imgdata[2];
				
				$img_width = $imgdata[0];
				$img_height = $imgdata[1];
				
			
				
				if ( $ext == "jpg" || $ext == "jpeg" || $imgtypeid == 2) { $imgtype = "jpeg";  }
				elseif ( $ext == "tif" || $ext == "tiff"  || $imgtypeid == 7 || $imgtypeid == 8) { $imgtype = "tiff";  }
				elseif ( $ext == "gif" || $imgtypeid == 1) { $imgtype = "gif";  }
				elseif ( $ext == "png" || $ext == "ping"  || $imgtypeid == 3) { $imgtype = "png";  }
											
				if ( isset($imgtype) ) {
					$imghndl = PDF_open_image_file($pdf_vars['pdf'], $imgtype, $img, "", 0);
				}
				
			

			
					
				if ($fit_style == "width") {
					if ($img_width <= 0) {
						$img_width = 50;
					}
					if ($img_height <= 0) {
						$img_height = 50;
					} 
					$place_size = $fit_width/$img_width;
					$move_down = $fit_height/$img_height;
						
				} elseif ($fit_style == "height") {
					if ($img_height <= 0) {
						$img_height = 50;
					} 
					$place_size = $fit_height/$img_height;
				
						
				} elseif ($fit_style == "scaleto") {
					if ($img_width <= 0) {
						$img_width = 50;
					} 
					if ($img_height <= 0) {
						$img_height = 50;
					}

					$width_scale = $fit_width/$img_width;
					$height_scale = $fit_height/$img_height;
					
					if ($width_scale > $height_scale) {
						$place_size = $height_scale;
					} else {
						$place_size = $width_scale;
					}
				} else {
					if ($imghndl != 0) {
						/* query the dpi values which may be present in the image file */
						$dpi_x = PDF_get_value($pdf_vars['pdf'], "resx", $imghndl);
						
						/* calculate place factor from the dpi values */
						if ($dpi_x > 0) { 
							$place_size = 72.0 / $dpi_x;
						} else { 
							$place_size = 1.0;
						}
					}
				}
				
				
				$move_down = -1*$place_size*$img_height;
				
				PDF_translate($pdf_vars['pdf'], 0, $move_down);
				
				// Image type IDs
				// 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel byte order), 
				// 8 = TIFF (motorola byte order), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF. 
							
				if ( $imghndl != 0 ) {
					if ($place_size <= 0) {
						$place_size = 1;
					}
					PDF_place_image($pdf_vars['pdf'], $imghndl, 0, 0, $place_size);
					PDF_close_image($pdf_vars['pdf'], $imghndl);
				}
			}
			
		} else { // File didn't exist at all
			$font = PDF_findfont($pdf_vars['pdf'], "Helvetica", "winansi", 0);
			PDF_setfont($pdf_vars['pdf'], $font, "6");
			PDF_set_text_pos($pdf_vars['pdf'], 0, -6);
			PDF_show($pdf_vars['pdf'], "[ IMAGE NOT"); 
			PDF_continue_text($pdf_vars['pdf'], " FOUND");
			PDF_continue_text($pdf_vars['pdf'], "FOR SITE $pdf_vars[siteid] ]");
		}
	}
}

function str_force_case($s,$style) {
	if ($s != "") {
		switch (strtoupper($style)) {
			case "UPPER" :
				$s = strtoupper($s);
				break;
			case "LOWER" :
				$s = strtolower($s);
				break;
			case "SENTENCE" :
				
				break;
			case "TITLE" :
				$a = explode(" ",$s);
				foreach ($a as $word) {
					if ( ctype_upper($word) ) { // If the word's all upper case, don't change it
						$newstring .= $word . " ";
					} else {
						$word = strtolower($word);
						$newstring .= ucfirst($word) . " ";
					}
				}
				$s = $newstring;
				break;
		}
	}
	return $s;
}


function pdf_set_ifg_field_font_settings ($this_field) {
	global $pdf_vars;
	if ($this_field['attributes']['CUSTOMFORMAT'] == "true") {
		$ret = $this_field['attributes'];
		foreach($pdf_vars['global_attrib'] as $attrib) {
			$xml_attribname = strtoupper( str_replace("_", "", $attrib));
			$val = $pdf_vars['contentbox']['attributes'][$xml_attribname];
			$fval = $this_field['attributes'][$xml_attribname];
			if ($fval == "") {
				$ret[$xml_attribname] = $val;
			}
		}
	} else {
		foreach($pdf_vars['global_attrib'] as $attrib) {
			$xml_attribname = strtoupper( str_replace("_", "", $attrib));
			$ret[$xml_attribname] = $pdf_vars['contentbox']['attributes'][$xml_attribname];
		}
		$ret['CUSTOMFORMAT'] = "true"; 
	}

	return $ret;
}


function pdf_create_ifg($ifg,$a_prefill) {
	// This function creates an IFG array object with all the data needed
	// to determine the interaction of all IFGs in a box and to place the text appropriately

	global $pdf_vars;
	
	$ifg_is_multiline = false;
	$start_ifg = true;
	$ifg_is_blank = true;
	$start_field_on_next_line = false;
	
	$total_rows = 0;
	$highest_row_leading = 0;
	$total_line_length = 0; 	// Reset the total line length
	$total_line_height = 0; 	// Reset the total line length
	$startParagraph = true;		// Set startParagraph to true so we know whether to include paragraph prefixes
	
	$pdf_vars['inlinefieldgroup']['attributes'] = $ifg_attrib = $a_ifg['attributes'] = $ifg['attributes'];

	$row = 0;
	$sub_row = 0;
	
	// Find out how many fields we are going through so we know when we reach the end and need to place text even if the line isn't long enough
	$total_fields = count($ifg['children']);
	$field_counter = 0;
	
	// DO CHARACTER REPLACEMENT FOR  CHARSBEFORE AND CHARSAFTER
	foreach ( $ifg['children'] as $field_array_id=>$this_field) 
	{
		$ifg['children'][$field_array_id]['attributes']['CHARSBEFORE'] = str_replace("<br>", "\n", $ifg['children'][$field_array_id]['attributes']['CHARSBEFORE']);
		$ifg['children'][$field_array_id]['attributes']['CHARSAFTER'] = str_replace("<br>", "\n", $ifg['children'][$field_array_id]['attributes']['CHARSAFTER']);
	}

	// Loop through each field in this IFG			*****************************************************
	// Each field is placed on the same row unless there's a line break
	foreach ( $ifg['children'] as $field_array_id=>$this_field ) {
		
		
			
		++$field_counter;

		$field_id = $this_field['attributes']['ID'];
		$pdf_vars['field']['attributes'] = $this_field['attributes'];
		
		pdf_set_font_attributes();
		
		
			
		// Get the text the user input
		$display_text = $a_prefill[$field_id];
		
		if (trim($display_text) == "") {
			// we may need to do something here later
			
		} else { 
			$ifg_is_blank = false;
			
			
			// ********************************
			// REDO THE PREFIX / SUFFIX SECTION
			// ********************************
			
			
			$next_field = $ifg['children'][$field_array_id+1];
			$next_field_id = $next_field["attributes"]["ID"];
			$next_display_text = $a_prefill[$next_field_id];
			$chars = $next_field['attributes']['CHARSBEFORE'];
			if ($chars != "" && trim($next_display_text) != "") {
				$next_display_text = $chars . $next_display_text;
			}
			$chars = $next_field['attributes']['CHARSAFTER'];
			if ($chars != "" && trim($next_display_text) != "") {
				$next_display_text = $next_display_text . $chars;
			} 
			$next_display_text = str_replace("\n"," \n ",trim($next_display_text) );
			$next_display_text = str_replace("\r","",$next_display_text);
			$a_next_display_text = explode(" ", $next_display_text);
			
			$field_cntr = $field_array_id+1;
			
			$addSpaceToEndOfField = true;
			
			//
			
			/*
			
			while ( isset($ifg['children'][$field_cntr]) ) {
				$loop_field = $ifg['children'][$field_cntr];
				$loop_field_id = $loop_field["attributes"]["ID"];
				$loop_display_text = $a_prefill[$loop_field_id];
				$loop_chars = $loop_field['attributes']['CHARSBEFORE'];
				if (trim($loop_display_text) != "" && $loop_chars != "") {
					$addSpaceToEndOfField = false;
					break;
				}
				++$field_cntr;
			}*/
			
			// Don't add space if next field has a prefix
			$prev_field = $ifg['children'][$field_array_id+1];
			$prev_field_id = $next_field["attributes"]["ID"];
			$prev_display_text = $a_prefill[$next_field_id];
			$chars = $prev_field['attributes']['CHARSBEFORE'];
			if (trim($prev_display_text) != "" && !empty($chars)) {
			//	$addSpaceToEndOfField = false;
			}

			// Add prefix ...
			$chars = $this_field['attributes']['CHARSBEFORE'];
			if ($chars != "" && trim($display_text) != "") {
				$temp_field = $ifg['children'][$field_array_id-1];
				$temp_field_id = $temp_field["attributes"]["ID"];
				$temp_display_text = $a_prefill[$temp_field_id];
				//omitprefix="false" omitsuffix="false"
				if ($this_field['attributes']['OMITPREFIX'] == "true" && trim($temp_display_text) == "") {
					// do nothing
				} else {
					$display_text = $chars . $display_text;
				//	$addSpaceToEndOfField = false;
				}
				
			}
			
			// ...and suffix
			$chars = $this_field['attributes']['CHARSAFTER'];
			if ($chars != "" && trim($display_text) != "") {
				$temp_field = $ifg['children'][$field_array_id+1];
				$temp_field_id = $temp_field["attributes"]["ID"];
				$temp_display_text = $a_prefill[$temp_field_id];
				if ($this_field['attributes']['OMITSUFFIX'] == "true" && trim($temp_display_text) == "") {
					// do nothing
				} else {
					$display_text = $display_text . $chars;
					if (substr($chars,strlen($chars)-1,strlen($chars)-1) != " ") {
						$addSpaceToEndOfField = false;
					}
				}
			} 
			
			// Split up the field $display_text by word
			$display_text = ereg_replace("\n"," \n ",trim($display_text) );
			$display_text = ereg_replace("\r","",$display_text);
			$a_display_text = explode(" ", $display_text);
			$last_word = count($a_display_text)-1;
	
			$lastLine = false;
			$endField = false;
			$startField = true;
			$paragraphprefixadded = false;
			
			if ($pdf_vars['font'] != 0) {
				$space_width = PDF_stringwidth($pdf_vars['pdf'], " ", $pdf_vars['font'], $pdf_vars['font_size']);
				$break_width = PDF_stringwidth($pdf_vars['pdf'], "\n", $pdf_vars['font'], $pdf_vars['font_size']);
			} 
			
			$textwidth = $pdf_vars['column_width'] ;
					
			// Loop through each word in the field 		****************************************************
			// We're just creating an array with the data in it to go back and place
			if (is_array($a_display_text)) {
				foreach ( $a_display_text as $word_index => $this_word ) {
					
					if ($word_index >= $last_word && $field_counter >= $total_fields) {
						$lastLine = true;
					}
					
					if ($word_index >= $last_word) {
						$endField =true;
					}
					
					// Set case
					$this_word = str_force_case($this_word, $pdf_vars['forcecase']);
					
					// Check and retrieve indenting settings for the current box
					$indent = $pdf_vars['leftindent'] + $pdf_vars['rightindent'] ;
				
					if ($startParagraph) { 
						$indent += $pdf_vars['firstlineindent']; 
					} 
					
					// Check for placing a paragraph prefix
					if ( !$paragraphprefixadded 
						&& trim($this_word) != ""
						&& $startParagraph 
						&& isset($pdf_vars['paragraphprefix'])
						&& trim($pdf_vars['paragraphprefix']) != ""
					) 
					{ 
						$this_word = $pdf_vars['paragraphprefix'] . $this_word	; 	
						$paragraphprefixadded = true;
					}
					
					// Compute string width and if we'd go over with the next word ...
					$nextIndex = $word_index + 1;
					if (isset($a_display_text[$nextIndex])) {
						$next_word = $a_display_text[$nextIndex];
					} else {
						$next_word = $a_next_display_text[0];
					}
		
					$addSpace = true;
					if (!$addSpaceToEndOfField && $endField) {
						$addSpace = false;
					}
										
					if ($pdf_vars['font'] != 0) {
						// We reset $total_line_length at the end of the line 
						if ($addSpace && $this_word != "\n")  {
							$thisString = $this_word . " ";
						} elseif ($this_word != "\n") {
							$thisString	= $this_word ; 
						} else {
							$thisString = "";
						}
						if ($thisString != "") {
							$total_line_length += PDF_stringwidth($pdf_vars['pdf'], $thisString, $pdf_vars['font'], $pdf_vars['font_size']);
						}
						$nextWordLen = PDF_stringwidth($pdf_vars['pdf'], trim($next_word), $pdf_vars['font'], $pdf_vars['font_size']);
					} else {
						$error .= "We don't have a handle on font $pdf_vars[fontface].  ";
					}
					
					
		
					// Check for line break 
					// If we've filled a line all the way up
					if ($total_line_length + $nextWordLen > $textwidth - $indent && $next_word != "\n") { 
						$endLine = true;	
						$endedby = "full length";
					}
					// -or- if this is simply the last line
					if ( $lastLine ) {
						$endLine = true;
						$endedby = "last line";
					}
					// -or- check for paragraph break 
					if ( $this_word == "\n" ) { 
						$endParagraph = true;
						$lastendedby = "";
						$endLine = true;	
						$endedby = "paragraph";
					}
					
					if ($this_word != "\n" ) {
						$place_text .= $this_word ;
					}
					
					if ($endLine && $ifg_attrib["FORCEFIT"] != "true") {
						$addSpace = false;
					}
					if ($ifg_attrib["FORCEFIT"] == "true" && $lastLine) {
						$addSpace = false;
					}
					if ($addSpace) {
						$place_text .= " ";
					}
	
					
					// See if it's time to add a new entry in our array ***************************************************************
					if (($endLine && $ifg_attrib["FORCEFIT"] != "true") || $this_word == "\n" || $lastLine) { 
					
						$this_row_text .= $place_text;
						if (trim($this_row_text) != "" || $this_word == "\n") {
							if ($pdf_vars["leading"] > $highest_row_leading) {
								$highest_row_leading = $pdf_vars["leading"];
							}
							
							$ifg_is_multiline = true;
							
							$total_line_height += $highest_row_leading;
							$a_ifg[$row]['attributes']['total_line_height'] = $highest_row_leading;
							$a_ifg[$row]['attributes']['length'] = $total_line_length-$space_width;
							
							
							/* 
							THIS NEXT LINE IS AN UGLY HACK BECAUSE FIELDS THAT ENDED WITH PREFIX TEXT GETTING 
							THE SHOVED TO THE RIGHT BY A SPACE WHEN USING RIGHT ALIGNMENT
							*/
							if ($lastLine && !empty($this_field['attributes']['CHARSAFTER'])) { $a_ifg[$row]['attributes']['length'] += $space_width; }
							
							$a_ifg[$row]['attributes']['endedby'] = $endedby;
							if ($startParagraph && $this_word != "\n") { $a_ifg[$row]['attributes']['startparagraph'] = true; }
							if (trim($place_text) == "") { $a_ifg[$row]['attributes']['empty'] = true; }
							if (!isset($a_ifg[$row]['attributes']['textalign'])) {
								$a_ifg[$row]['attributes']['textalign'] = $pdf_vars['textalign'];
							}
							$a_ifg[$row][$sub_row]['string'] = $place_text;//."."; 
	
							$a_ifg[$row][$sub_row]['attributes'] = pdf_set_ifg_field_font_settings($this_field);
							$a_ifg[$row][$sub_row]['attributes']['length'] = PDF_stringwidth($pdf_vars['pdf'], $place_text, $pdf_vars['font'], $pdf_vars['font_size']);//$total_line_length-$space_width; 
							if ($ifg_attrib["FORCEFIT"] == "true") {
								// add leading in here once
								$a_ifg[$row]['attributes']['force_fit'] = true;
								$a_ifg[$row]['attributes']['fit_first_method'] = $ifg_attrib['FITFIRSTMETHOD'];
								$a_ifg[$row]['attributes']['fit_min_pts']= $ifg_attrib['FITMINPTS'];
								if ($a_ifg[$row]['attributes']['fit_min_pts'] == "") {
									$a_ifg[$row]['attributes']['fit_min_pts'] = 6;
								}
								$a_ifg[$row]['attributes']['fit_min_tracking'] = $ifg_attrib['FITMINTRACKING'];
								if ($a_ifg[$row]['attributes']['fit_min_tracking'] == "") {
									$a_ifg[$row]['attributes']['fit_min_tracking'] = -20;
								}
								$a_ifg[$row]['attributes']['fit_min_hscale'] = $ifg_attrib['FITMINHSCALE'];
								if ($a_ifg[$row]['attributes']['fit_min_hscale'] == "") {
									$a_ifg[$row]['attributes']['fit_min_hscale'] = 80;
								}
							}
							++$row;
						}
						if ($pdf_vars["wrap"] == "truncate") {
							break(2); 
						}
						$sub_row = 0;
						$place_text = "";
						$total_line_length = 0;
						
						$endLine = false;
						$startParagraph = false;
						$lastendedby = $endedby;
						$endedby = "";
						$this_row_text = "";
						$highest_row_leading = 0;
						$start_field_on_next_line = false;
						$start_ifg = false;
					} else if ($endField) { // we're not ending the line (row) but the field ended
						$a_ifg[$row]['attributes']['length'] = $total_line_length;//-$space_width; 	// This variable will be updated later if there's more text on the same line 
						$a_ifg[$row]['attributes']['textalign'] = $pdf_vars['textalign'];
						if (trim($place_text) != "") {
							$a_ifg[$row][$sub_row]['string'] = $place_text;
							$this_row_text .= $place_text;
							if ($pdf_vars["leading"] > $highest_row_leading) {
								$highest_row_leading = $pdf_vars["leading"];
							}
						} else {
							$a_ifg[$row][$sub_row]['string'] = "";
						}
						
						
						if ($startParagraph) { $a_ifg[$row]['attributes']['startparagraph'] = true; }
						
						$a_ifg[$row]['attributes']['endedby'] = "No2"; 
						
						// Take care of leading
						if ($pdf_vars["leading"] > $highest_row_leading) {
							$highest_row_leading = $pdf_vars["leading"];
						}
						$a_ifg[$row]['attributes']['total_line_height'] = $highest_row_leading;
						
						$a_ifg[$row][$sub_row]['attributes'] = pdf_set_ifg_field_font_settings($this_field);
						
						$a_ifg[$row][$sub_row]['attributes']['length'] = PDF_stringwidth($pdf_vars['pdf'], $place_text , $pdf_vars['font'], $pdf_vars['font_size']);
												
						++$sub_row;
					//	$a_ifg[$row]['attributes']['subrow_count'] = $sub_row;
						$place_text = "";
						$startParagraph = false;
					}
					
					$startField = false;
					if ($endParagraph) {
						$endParagraph = false;
						$startParagraph = true;
						$paragraphprefixadded = false;
					}
				}
			}
		}
	}
			
	if ($ifg_attrib["FORCEFIT"] == "true") {
		// add leading in here once
		if (!$ifg_is_multiline) {
			$total_line_height = $highest_row_leading;
		}
		$a_ifg[$row]['attributes']['total_line_height'] = $highest_row_leading;
		$a_ifg[$row]['attributes']['force_fit'] = true;
		$a_ifg[$row]['attributes']['fit_first_method'] = $ifg_attrib['FITFIRSTMETHOD'];
		$a_ifg[$row]['attributes']['fit_min_pts']= $ifg_attrib['FITMINPTS'];
		if ($a_ifg[$row]['attributes']['fit_min_pts'] == "") {
			$a_ifg[$row]['attributes']['fit_min_pts'] = 6;
		}
		$a_ifg[$row]['attributes']['fit_min_tracking'] = $ifg_attrib['FITMINTRACKING'];
		if ($a_ifg[$row]['attributes']['fit_min_tracking'] == "") {
			$a_ifg[$row]['attributes']['fit_min_tracking'] = -30;
		}
		$a_ifg[$row]['attributes']['fit_min_hscale'] = $ifg_attrib['FITMINHSCALE'];
		if ($a_ifg[$row]['attributes']['fit_min_hscale'] == "") {
			$a_ifg[$row]['attributes']['fit_min_hscale'] = 80;
		}
		
	//	++$row;
	}
	
	if ($ifg_is_blank && $ifg_attrib["OMITEXTRALEADING"] == "true") {
		// do nothing
		$a_ifg["attributes"]["SPACEBEFORE"] = 0;
		$a_ifg["attributes"]["SPACEAFTER"] = 0;
	} else { 
		if (isset($ifg_attrib['SPACEBEFORE'])) { $total_line_height += $ifg_attrib['SPACEBEFORE']; }
		if (isset($ifg_attrib['SPACEAFTER'])) {	$total_line_height += $ifg_attrib['SPACEAFTER']; }
	}
	
	
	$a_ifg['height'] = $total_line_height;
	$a_ifg['totalrows'] = $row;
	
	return $a_ifg;
	
}

function pdf_place_ifg($a_ifg) {
	global $pdf_vars;

	$pdf_vars['inlinefieldgroup']['attributes'] = $a_ifg['attributes'];
	
	if ($a_ifg['attributes']['SPACEBEFORE'] > 0) {
		$pdf_vars[posy] -= $a_ifg['attributes']['SPACEBEFORE'];
	}
	
	$first_pass = true;
	
	$addline = true;
	
	if (is_array($a_ifg)) {
		foreach($a_ifg as $i=>$row) {
			if (strtolower($i) != "height" && strtolower($i) != "attributes") { // It's a row, then
				if (is_array($row)) {
					
					
					$row_height = $row['attributes']['total_line_height'];
				//	$row_length = $row['attributes']['length'];
					$row_align  = strtolower($row['attributes']['textalign']) ;
					$force_fit = $row['attributes']['force_fit'];
					
				/*
					print_r($row);
					
					print("
					<br>-----------------------------------------------------------------------------<br>
					");
						*/

					if ($row['attributes']['startparagraph']) {
						$startparagraph = true;
					} else {
						$startparagraph = false;
					}
					if ($row['attributes']['empty']) {
						$empty = true;
					} else {
						$empty = false;
					}

					// Calculate column position
					$column_full = false;
					$column_full = $pdf_vars['posy']-$row_height < -1*$pdf_vars['column_height_pts'];

					if ($column_full && $pdf_vars['current_column']+1 == $pdf_vars["column_count"]) {
						$pdf_vars["boxfull"] = true;
						
						// print a mark here to indicate the text didn't fit
						
						break;
					}
					
					if (!$empty && $column_full && $pdf_vars['column_count'] > 1 && !($pdf_vars['current_column'] > $pdf_vars['column_count']-2)) {
						++$pdf_vars['current_column'];
						$pdf_vars['posy'] = 0;
						$pdf_vars['posx'] += $pdf_vars['column_width'] + $pdf_vars['column_gutterwidth'];
						$startcolumn = true;
					}
					
					
					
					
					
					
					// force it to fit *****************************************************************
					if ($force_fit) {
						$cntr = 0;
						
						
						if ($pdf_vars['column_width'] < $row['attributes']['length']) {
							
							// Set 
						//	$pdf_vars['inlinefieldgroup']['attributes'] = $row['attributes'];
							
							foreach($row as $j=>$sub_row) { 
								if (strtolower($j) != "length" && strtolower($j) != "attributes" && $sub_row['attributes']['locked'] != true) {
									$pdf_vars['field']['attributes'] = $row[$j]['attributes'];

									pdf_set_font_attributes();

									$row[$j]['attributes']['FONTSIZE'] = $pdf_vars['font_size'] ;
									$row[$j]['attributes']['TRACKING'] = $pdf_vars['tracking'] ;
									$row[$j]['attributes']['HORIZONTALSCALE'] = $pdf_vars['horizontalscale'] ;							
							
								}
							}

									/**/					
							while($row['attributes']['length'] > $pdf_vars['column_width']-0.005)  {
								// loop through each sub row
								if (is_array($row)) {
									// loop through all subrows and see if minimums have been met, "locking" subrows that have met minimums
									// "locking" entails setting the attribute "locked" to true and setting the attribute editable_sub_rows to count of editable sub_rows 
									// and editable_rows_length to sum of non-locked sub rows 
									$break = true;
									$row['attributes']['size_editable_sub_rows'] = 0;
									$row['attributes']['size_editable_rows_length'] = 0;
									$row['attributes']['size_non_editable_rows_length'] = 0;
									$row['attributes']['track_editable_sub_rows'] = 0;
									$row['attributes']['track_editable_rows_length'] = 0;
									$row['attributes']['track_non_editable_rows_length'] = 0;
									$row['attributes']['hscale_editable_sub_rows'] = 0;
									$row['attributes']['hscale_editable_rows_length'] = 0;
									$row['attributes']['hscale_non_editable_rows_length'] = 0;
									$total_length = 0;
									if ($row['attributes']['length']-0.005 > $pdf_vars['column_width']) {
										foreach($row as $j=>$sub_row) { 
											if (strtolower($j) != "length" && strtolower($j) != "attributes" && $sub_row['attributes']['locked'] != true) {
												$total_length += $row[$j]['attributes']['length'];
												// Set default values to use if there aren't any
												if ($row[$j]['attributes']['TRACKING'] == "" || !isset($row[$j]['attributes']['TRACKING'])) {
													$row[$j]['attributes']['TRACKING'] = 0;
												}
												if ($row[$j]['attributes']['FONTSIZE'] == "" || !isset($row[$j]['attributes']['FONTSIZE'])) {
													$row[$j]['attributes']['FONTSIZE'] = 10;
												}
												if ($row[$j]['attributes']['HORIZONTALSCALE'] == "" || !isset($row[$j]['attributes']['HORIZONTALSCALE'])) {
													$row[$j]['attributes']['HORIZONTALSCALE'] = 100;
												}
												
												// Check to see if we've maxxed out the options
												$lock = true;
												if ($row[$j]['attributes']['FONTSIZE'] > $row['attributes']['fit_min_pts'] ) {
													++$row['attributes']['size_editable_sub_rows'];
													$row['attributes']['size_editable_rows_length'] += $row[$j]['attributes']['length'];
													$row[$j]['attributes']['size_locked'] = false;
													$break = false;
												} else {
													$row[$j]['attributes']['size_locked'] = true;
													$row['attributes']['size_non_editable_rows_length'] += $row[$j]['attributes']['length'];
												}
												
												if ($row[$j]['attributes']['TRACKING'] > $row['attributes']['fit_min_tracking']) {
													++$row['attributes']['track_editable_sub_rows'];
													$row['attributes']['track_editable_rows_length'] += $row[$j]['attributes']['length'];
													$row[$j]['attributes']['track_locked'] = false;
													$break = false;
												} else {
													$row[$j]['attributes']['track_locked'] = true;
													$row['attributes']['track_non_editable_rows_length'] += $row[$j]['attributes']['length'];
												}

												if ($row[$j]['attributes']['HORIZONTALSCALE'] > $row['attributes']['fit_min_hscale'] ) {
													++$row['attributes']['hscale_editable_sub_rows'];
													$row['attributes']['hscale_editable_rows_length'] += $row[$j]['attributes']['length'];
													$row[$j]['attributes']['hscale_locked'] = false;
													$break = false;
												} else {
													$row[$j]['attributes']['hscale_locked'] = true;
													$row['attributes']['hscale_non_editable_rows_length'] += $row[$j]['attributes']['length'];
												}
											}
										}
									}

									if ($break) { break; }
									
									foreach($row as $j=>$sub_row) { 
										$is_subrow = false;
										if (strtolower($j) != "length" && strtolower($j) != "attributes" && $sub_row['attributes']['locked'] != true) {	// It's a sub_row!
											$is_subrow = true;
										}
										
										if ($is_subrow) {	
											switch ($row['attributes']['fit_first_method']) {
												
												case "size" :	// ********************************************* Do size-preferred scaling
													
													if ( ($sub_row['attributes']['FONTSIZE'] > $row['attributes']['fit_min_pts']) ) {
														$size_fit_into = $pdf_vars['column_width']-$row['attributes']['size_non_editable_rows_length'];
														$scale = $size_fit_into / $row['attributes']['size_editable_rows_length'] ;
														if ($sub_row['attributes']['FONTSIZE']*$scale < $row['attributes']['fit_min_pts'] ) {
															$row[$j]['attributes']['FONTSIZE'] = $row['attributes']['fit_min_pts'];
														} else {
															$row[$j]['attributes']['FONTSIZE'] = $sub_row['attributes']['FONTSIZE']*$scale;
														}
													} elseif ($row['attributes']['size_editable_sub_rows'] == 0) { 
														// we've already done all the sizing we're allowed to do so...
														// try tracking and hscale
														$track_fit_into = $pdf_vars['column_width']-$row['attributes']['track_non_editable_rows_length'];
														$hscale_fit_into = $pdf_vars['column_width']-$row['attributes']['hscale_non_editable_rows_length'];
														$track_diff_pts = $row['attributes']['track_editable_rows_length']-$track_fit_into;
														$hscale_diff_pts = $row['attributes']['hscale_editable_rows_length']-$hscale_fit_into;
														
														if ($track_fit_into <= 0 && $hscale_fit_into <= 0) { 
															break(3);
														}
														
														if ($row['attributes']['track_editable_rows_length'] == 0) {
															$hscale_remove_amt = $hscale_diff_pts;
															$hscale_remove_amt_inverse = 0;
														} else {
															$hscale_remove_amt = $hscale_diff_pts/2;
															$hscale_remove_amt_inverse = $hscale_remove_amt;
														}
														if ($row['attributes']['hscale_editable_rows_length'] == 0) {
															$track_remove_amt = $track_diff_pts ;
														} else {
															$track_remove_amt = $track_diff_pts/2;
														}
														
														if ($row['attributes']['track_editable_sub_rows'] > 0) {
															// tracking by factor
															$chars = strlen($sub_row['string'])-1;
															$new_tracking = $row[$j]['attributes']['TRACKING']-($track_remove_amt/$chars)*100;
															
														
															if ($new_tracking < $row['attributes']['fit_min_tracking'] ) {
																$row[$j]['attributes']['TRACKING'] = $row['attributes']['fit_min_tracking'];
															} else {
																$row[$j]['attributes']['TRACKING'] = $new_tracking;
															}
														}

														// hscale by factor
														if ($row['attributes']['hscale_editable_sub_rows'] > 0) {
															$len = $row['attributes']['hscale_editable_rows_length'];
															$new_hscale = (($row[$j]['attributes']['HORIZONTALSCALE']/100)*($hscale_fit_into/($len-$hscale_remove_amt_inverse)))*100;
														
															if ($new_hscale < $row['attributes']['fit_min_hscale'] ) {
																$row[$j]['attributes']['HORIZONTALSCALE'] = $row['attributes']['fit_min_hscale'];
															} else {
																$row[$j]['attributes']['HORIZONTALSCALE'] = $new_hscale;
															}
														}
																												
													}
													break;
													
												case "tracking" : // *********************************************
												
													if ( ($sub_row['attributes']['TRACKING'] > $row['attributes']['fit_min_tracking']) ) {
														// do the tracking
														$track_fit_into = $pdf_vars['column_width']-$row['attributes']['track_non_editable_rows_length'];
														$track_diff_pts = $row['attributes']['track_editable_rows_length']-$track_fit_into;
														
														$chars = strlen($sub_row['string'])-1;
														if ($chars == 0) { $chars = 1; }
														$new_tracking = $row[$j]['attributes']['TRACKING']-($track_diff_pts/$chars)*100;
														
														if ($new_tracking < $row['attributes']['fit_min_tracking'] ) {
															$row[$j]['attributes']['TRACKING'] = $row['attributes']['fit_min_tracking'];
														} else {
															$row[$j]['attributes']['TRACKING'] = $new_tracking;
														}

													} elseif ($row['attributes']['track_editable_sub_rows'] == 0) { 
														// we've already done all the tracking we're allowed to do so...
														
														// tracking screws everything up so we need a tracking factor
														//$chars = strlen($row[$j]['string']);
														//$extra_length = 2*($chars)*($row[$j]['attributes']['TRACKING']/100);
														
														
														if ($row[$j]['attributes']['TRACKING'] < -20) {
															$track_factor = 1.00;//.02;	//;$extra_length/$row['attributes']['size_editable_rows_length']/50;
														}
														
														$width1 = $pdf_vars['column_width'];
														$width2 = $row['attributes']['size_editable_rows_length'];
														if ($row['attributes']['hscale_editable_sub_rows'] > 0 && $width2 > 0) {
															// we only want half a factor
															$size_factor = pow($width1/$width2,1/2)/1; 
														} else {
															if ($width2 > 0) {
																$size_factor = $width1/$width2;
															} else {
																$size_factor = 0;
															}
														}
														
														
														$width2 = $row['attributes']['hscale_editable_rows_length'];
														if ($row['attributes']['size_editable_sub_rows'] > 0 && $width2 > 0) {
															$hscale_factor = pow($width1/$width2,1/2)/1 ;
														} else {
															if ($width2 > 0) {
																$hscale_factor = $width1/$width2 ;
															} else {
																$hscale_factor = 0;
															}
														}
																											
														if ($row['attributes']['size_editable_sub_rows'] > 0) {
															$new_size = $row[$j]['attributes']['FONTSIZE'] * $size_factor;													
															if ($new_size < $row['attributes']['fit_min_pts']) {
																$row[$j]['attributes']['FONTSIZE'] = $row['attributes']['fit_min_pts'];
															} else {
																$row[$j]['attributes']['FONTSIZE'] = $new_size;
															}
														}

														if ($row['attributes']['hscale_editable_sub_rows'] > 0) {
															$new_hscale = (($row[$j]['attributes']['HORIZONTALSCALE']/100)*$hscale_factor)*100;
															if ($new_hscale < $row['attributes']['fit_min_hscale'] ) {
																$row[$j]['attributes']['HORIZONTALSCALE'] = $row['attributes']['fit_min_hscale'];
															} else {
																$row[$j]['attributes']['HORIZONTALSCALE'] = $new_hscale;
															}
														} 
													}
													
													break;
													
												case "hscale" :
													if ( ($sub_row['attributes']['HORIZONTALSCALE'] > $row['attributes']['fit_min_hscale']) ) {
														
														$width1 = $pdf_vars['column_width'];
														$width2 = $row['attributes']['hscale_editable_rows_length'];
														
														if ($width2 > 0) {
															$hscale_factor = $width1/$width2 ;
														} else {
															$hscale_factor = 0;
														}
														
														if ($row['attributes']['hscale_editable_sub_rows'] > 0) {
															$new_hscale = (($row[$j]['attributes']['HORIZONTALSCALE']/100)*$hscale_factor)*100;
															if ($new_hscale < $row['attributes']['fit_min_hscale'] ) {
																$row[$j]['attributes']['HORIZONTALSCALE'] = $row['attributes']['fit_min_hscale'];
															} else {
																$row[$j]['attributes']['HORIZONTALSCALE'] = $new_hscale;
															}
														} 
														

													} elseif ($row['attributes']['hscale_editable_sub_rows'] == 0) {
														
														// do sizing
														$width1 = $pdf_vars['column_width'];
														$width2 = $row['attributes']['size_editable_rows_length'];
														if ($row['attributes']['track_editable_sub_rows'] > 0) {
															// we only want half a factor
															$size_factor = pow($width1/$width2,1/2)/1; 
															
														} else {
															if ($width2 > 0) {
																$size_factor = $width1/$width2;
															} else {
																$size_factor = 0;
															}
														}
																												
														if ($row['attributes']['size_editable_sub_rows'] > 0) {
															$new_size = $row[$j]['attributes']['FONTSIZE'] * $size_factor;													
															if ($new_size < $row['attributes']['fit_min_pts']) {
																$row[$j]['attributes']['FONTSIZE'] = $row['attributes']['fit_min_pts'];
															} else {
																$row[$j]['attributes']['FONTSIZE'] = $new_size;
															}
														}

														
														// do the tracking
													//	$track_fit_into = $pdf_vars['column_width']-$row['attributes']['track_non_editable_rows_length'];
													//	$track_diff_pts = $row['attributes']['track_editable_rows_length']-$track_fit_into;
																												
														if ($row['attributes']['hscale_editable_rows_length'] == 0) {
															$track_remove_amt = $track_diff_pts ;
														} else {
															$track_remove_amt = $track_diff_pts/2;
														}
														
														if ($row['attributes']['track_editable_sub_rows'] > 0) {
															// tracking by factor
															$chars = strlen($sub_row['string'])-1;
															$new_tracking = $row[$j]['attributes']['TRACKING']-($track_remove_amt/$chars)*100;
														
															if ($new_tracking < $row['attributes']['fit_min_tracking'] ) {
																$row[$j]['attributes']['TRACKING'] = $row['attributes']['fit_min_tracking'];
															} else {
																$row[$j]['attributes']['TRACKING'] = $new_tracking;
															}
														}
														/**/
													}
													
													break;
													
												case "all" :
													$factor = pow($scale,1/3)/1; 
												
											}
											
											
										}
									}
									
									
									// compute the new line length and each sub row length 
									$newlength = 0;
									foreach ($row as $j=>$sub_row) {
										$is_subrow = false;
										if (strtolower($j) != "length" && strtolower($j) != "attributes") {	
											$is_subrow = true;
										}
										
										if ($is_subrow) {	
											$pdf_vars['field']['attributes']['FONTSIZE'] = $row[$j]['attributes']['FONTSIZE'];
											$pdf_vars['field']['attributes']['TRACKING'] = $row[$j]['attributes']['TRACKING'];
											$pdf_vars['field']['attributes']['HORIZONTALSCALE'] = $row[$j]['attributes']['HORIZONTALSCALE'];

											pdf_set_font_attributes();
											
											$str_width = PDF_stringwidth($pdf_vars['pdf'], $sub_row["string"], $pdf_vars['font'], $pdf_vars['font_size']);
											$newlength += $str_width;
											
											
											$row[$j]['attributes']['length'] = $str_width;
											
										}
									}
									
									$row['attributes']['length'] = $newlength;
									$break = true;
									
									++$cntr;
									if ($cntr > 30) {
										break;
									}
								}
							}
						}
					}
					
					// Calcaluate accurate top position for text to begin
					if ($startcolumn || $pdf_vars["startbox"]) {
						
						$new_row_height = 0;
						if (is_array($row)) {
							foreach ($row as $j=>$sub_row) {
								
								
								$key = intval($j);
								if (strtolower($j) != "length" && strtolower($j) != "attributes") {	// $key > 0
									$is_subrow = true;
								} else {
									$is_subrow = false;
								}
								if ($is_subrow) {
									
									$pdf_vars['field']['attributes'] = $sub_row['attributes'];
									pdf_set_font_attributes();
									$this_row_height = PDF_get_value($pdf_vars["pdf"], "capheight", $pdf_vars["font"]) * $pdf_vars["font_size"];
									
									if ($this_row_height > $new_row_height) { 
										$new_row_height = $this_row_height;
									}
								}
							}
						}
						
						if ($pdf_vars['current_column'] == 0) {
							$pdf_vars["column_height_pts"] -= $row['attributes']['total_line_height']-$new_row_height;
						}
						
						$row_height = $row['attributes']['total_line_height'] = $new_row_height;
					}
					$pdf_vars["startbox"] = false;
					
								
					// Calculate alignment
					if ( $row_align == "center" ) {
						$x =  ( ($pdf_vars['column_width'] - $row['attributes']['length']) / 2 )  + $pdf_vars['posx']; 
					} else if ( $row_align == "right" ) {
						$x =  ($pdf_vars['column_width'] - $row['attributes']['length']) + $pdf_vars['posx']; 
					} else { // It's left!
						$x = $pdf_vars['posx'] ;
					}
					
					// Calculate indenting
					if ($first_pass || $startparagraph)  {
						$indent = $pdf_vars['leftindent'] + $pdf_vars['firstlineindent']; 
					} else {
						$indent = $pdf_vars['leftindent'] ; 
					}
					$x += $indent;

					
					// Update position to place text at - this comes before we place any text so we don't start above our box
					if ($empty && $startcolumn) {
						$addline = false;
					} else {
						$addline = true;
						$pdf_vars['posy'] -= $row_height;
					}
					
					$startcolumn = false;
					$startparagraph = false;
					
					if ($addline) {
						// Set position
						PDF_set_text_pos($pdf_vars['pdf'], $x, $pdf_vars['posy']);
						/*
						if () {
						
						}
						*/
						// Place each subrow
						foreach($row as $j=>$sub_row) { 
	
							if (strtolower($j) != "length" && strtolower($j) != "attributes") {	// It's a sub_row!
								// Set sub row attributes
								if (is_array($sub_row['attributes'])) {
									$pdf_vars['field']['attributes'] = $sub_row['attributes'];
								} else {
									unset($pdf_vars['field']['attributes']);
								}
								
								if (trim($sub_row['string']) != "") {
									pdf_set_font_attributes();
									PDF_show($pdf_vars['pdf'], $sub_row['string']);// . "."
								}
							}
						}
					}
				}
				$first_pass = false ;
			}
		}
	}
	if ($a_ifg['attributes']['SPACEAFTER'] > 0) {
		$pdf_vars[posy] -= $a_ifg['attributes']['SPACEAFTER'];
	}
}


function pdf_start($mode="") {
	global $pdf_vars, $cfg_base_dir;
	
	$sql = "SELECT MasterUID FROM Sites WHERE ID='$pdf_vars[siteid]'";

//	print("\n\n\n\n<!-- $sql //-->\n\n\n\n\n");

	$result = dbq($sql);
	$a_result = mysql_fetch_assoc($result);
	$uid = $a_result["MasterUID"];
	
	$pdf_vars['pdf'] = PDF_new();

	if (!empty($cfg_pdflib_license)) 
		PDF_set_parameter($pdf_vars['pdf'], "license", $cfg_pdflib_license);
	PDF_set_parameter($pdf_vars['pdf'], "compatibility", "1.4");
	
	$psres = $cfg_base_dir."_users/".$uid."/fonts/PSres.upr";
//	print("\n\n\n\n<!-- $psres //-->\n\n\n\n\n");
	if (file_exists($psres)) {
		PDF_set_parameter($pdf_vars["pdf"], "resourcefile", $psres);
	}

	PDF_open_file($pdf_vars['pdf'], "");
	
	PDF_set_info($pdf_vars['pdf'], "Creator", "VariaPrint(TM) PDF Engine");
	PDF_set_info($pdf_vars['pdf'], "Author", "Luke Miller");
	PDF_set_info($pdf_vars['pdf'], "Title", "Dynamic PDF File");
	
	if ($mode == "press") {
		$pageWidth = $pdf_vars['pagewidth']+($pdf_vars['bleedLR']*2);
		$pageHeight = $pdf_vars['pageheight']+($pdf_vars['bleedTB']*2);
	}else{
		$pageWidth = $pdf_vars['pagewidth'];
		$pageHeight = $pdf_vars['pageheight'];
	}


	PDF_begin_page($pdf_vars['pdf'], $pageWidth, $pageHeight);

	if ($mode == "press") {
		PDF_set_value($pdf_vars['pdf'], "BleedBox/llx", $pdf_vars['bleedLR']);
		PDF_set_value($pdf_vars['pdf'], "BleedBox/lly", $pdf_vars['bleedTB']);
		PDF_set_value($pdf_vars['pdf'], "BleedBox/urx", $pdf_vars['pagewidth'] + $pdf_vars['bleedLR']);
		PDF_set_value($pdf_vars['pdf'], "BleedBox/ury", $pdf_vars['pageheight'] + $pdf_vars['bleedTB']);
	}
	
	PDF_save($pdf_vars['pdf']);
}

function pdf_end() {
	global $pdf_vars;
	
	PDF_restore($pdf_vars['pdf']);	
	PDF_end_page($pdf_vars['pdf']);
	PDF_close($pdf_vars['pdf']);
}




// *********************************************************************************************************************
// MAIN ENGINE THAT PROCESSES THE BOX STRUCTURE STARTS HERE		       **************************************
// *********************************************************************************************************************

function pdf_process_template($a, $a_prefill, $parent = "") {
	global $pdf_vars, $site_id; 
	if ( is_array($a) ) {
		while ( list(,$node) = each($a) ) {
			$id = $node['attributes']['ID'];
			switch ($node['tag']) {
				case "CONTENTBOX" : // *****************************************************************************
					if ($parent['tag'] == "CONTENTBOX") { 	// sub box
						// SET SUBCONTENTBOX ATTRIBUTES 
						
						// $pdf_vars['subcontentbox']['attributes'] = $node['attributes'];
	
					} else {  								// top box
						// SET CONTENTBOX ATTRIBUTES 
						$pdf_vars['contentbox']['attributes'] = $node['attributes'];
						
						PDF_restore($pdf_vars['pdf']);		// restore back to the defaults
						PDF_save($pdf_vars['pdf']);			// and save for the next box 
						
						$boxX = $node['attributes']['POSITIONX']; 		
						$boxY = $node['attributes']['POSITIONY']; 
						if ( $boxX == "") { $boxX = "10"; }
						if ( $boxY == "") { $boxY = $pdf_vars['pageheight'] - 10; }
						
						PDF_translate($pdf_vars['pdf'], $boxX, $boxY);

						$rotation = $node['attributes']['ROTATE'];
						if ($rotation != "0" && $rotation != "") {
							PDF_rotate($pdf_vars['pdf'], $rotation);
						}
						
						$pdf_vars['posy'] = 0;    	// Reset Y coordinate to place content at.
						$pdf_vars['posx'] = 0; 		// Reset X coordinate to place content at.
						
					}
					
					$pdf_vars['current_column'] = 0;
					
					// These need to be set even if there's only 1 column because they are used in placing paragraph text.
					if ( isset($node['attributes']['COLUMNGUTTERWIDTH']) ) { 
						$pdf_vars['column_gutterwidth'] = $node['attributes']['COLUMNGUTTERWIDTH'] ; 
					} else { 
						$pdf_vars['column_gutterwidth'] = 18 ; 
					}
					if ( isset($node['attributes']['COLUMNCOUNT']) && $node['attributes']['COLUMNCOUNT'] > 0) { 
						$pdf_vars['column_count'] = $node['attributes']['COLUMNCOUNT'] ; 
					} else { 
						$pdf_vars['column_count'] = 1 ; 
					}
					
					
					
					//Figure out where to get the width from and then compute $pdf_vars['column_width']
					if ( isset($node['attributes']['WIDTH']) ) {
						$colwidth = $node['attributes']['WIDTH'];
					} else if ( $parent['tag'] == "contentbox" && isset($parent['attributes']['WIDTH']) ) {
						$colwidth = $parent['attributes']['WIDTH'];
					} else {
						$colwidth = $pdf_vars['pagewidth'] - ( $pdf_vars['posx'] + 14);
					}
				
					$pdf_vars['column_width'] = ($colwidth - (($pdf_vars['column_count'] - 1) * $pdf_vars['column_gutterwidth']) ) / $pdf_vars['column_count']  ;	
					
					$pdf_vars['total_text_height'] = 0; 	// Reset the height that all the text takes in this box
					
					unset($a_ifg_obj);
					
					
					//foreach ( $pdf_vars['global_attrib'] as $index => $property ) {
					//}

					
					// Create IFG objects for this box
					$a_ifgs = xml_find_node("inlinefieldgroup", $node['children']);
					if (is_array($a_ifgs)) {	
						foreach ($a_ifgs as $ifg) {
							$a_ifg_obj[] = pdf_create_ifg($ifg, $a_prefill);	
						}
					}
					
					
									
					$pdf_vars['total_rows'] = 0;
					// Determine total height of text in IFGs in this box
					$pdf_vars['total_rows'] = 0;
					if (is_array($a_ifg_obj)) {
						foreach ($a_ifg_obj as $ifg_obj) {
							if (is_array($ifg_obj)) {
								$pdf_vars['total_text_height'] += $ifg_obj['height'];
								$pdf_vars['total_rows'] += $ifg_obj['totalrows'];
							}
						}
					}
										
					if ($pdf_vars['total_rows'] == 0) $pdf_vars['total_rows'] = 1;
					
					// HANDLE COLUMNS -- Determine attributes for placing IFGs in the correct column 
					if ( $pdf_vars['column_count'] > 1 ) { 
					
						// Determine if we need to balance the columns
						if ($node['attributes']['BALANCECOLUMNS'] != "false") {
							$avglineheight = ($pdf_vars['total_text_height']/$pdf_vars['total_rows']);
							$pdf_vars['column_height_pts'] = ($pdf_vars['total_text_height'] / $pdf_vars['column_count']) + ($avglineheight*.75);
							
							
							// ************************
							// NEED TO WRITE A BETTER ALORITHM THAT ANALYZES THE FULL CONTENTS 
							// OF THE BOX TO DETERMINE THE BREAKING PATTERN FOR COLUMN BALANCING
							// ************************

						
							
						
						//	exit($pdf_vars['total_text_height'] . " cols:" . $pdf_vars['column_count']. " avgLH:".$avglineheight. " totR:" .$ifg_obj['totalrows']);
						} else {
							$pdf_vars['column_height_pts'] = $node['attributes']['HEIGHT'];
						}
						if ($pdf_vars['column_height_pts'] > $pdf_vars['contentbox']['attributes']['HEIGHT']) {
							$pdf_vars['column_height_pts'] = $pdf_vars['contentbox']['attributes']['HEIGHT'];
						}
						
					} else {
						// This is where we need to set any vars for 1 column boxes
						$pdf_vars['column_height_pts'] = $pdf_vars['contentbox']['attributes']['HEIGHT'];
					}
					
					// Vertical alignment for Bryan
					if ($pdf_vars['contentbox']['attributes']['VALIGN'] == "center") {
						$pdf_vars['posy'] = ($pdf_vars['total_text_height']-$pdf_vars['column_height_pts'])/2;
					} elseif ($pdf_vars['contentbox']['attributes']['VALIGN'] == "bottom") {
						$pdf_vars['posy'] = $pdf_vars['total_text_height']-$pdf_vars['column_height_pts'];
					}
					
				//	print_r($a_ifg_obj);
					
					// Place all IFGs in this box ******************************************************************
					$fp = true;
					if (is_array($a_ifg_obj)) {
						foreach ($a_ifg_obj as $ifg_obj) {
							if ($fp) $pdf_vars["startbox"] = true; else $pdf_vars["startbox"] = false;
							$fp = false;
							$pdf_vars["boxfull"] = false;
							
							pdf_place_ifg($ifg_obj);
							
						}
					}
					
					break;
				
				case "INLINEFIELDGROUP": // *************************************************************************
					// No longer used. All IFGs are now processed when a box is hit.

					break;
				
				
				
				case "GRAPHIC": // **********************************************************************************

					if ( $a_prefill[$id] != "" ) {
						place_graphic($a_prefill[$id]);
					} 
										
					break;
					
			}
			
			
			// REPEAT ...
			if ( is_array($node['children']) && ($node['tag'] == "CONTENTBOX" || $node['tag'] == "") ) { 
				pdf_process_template($node['children'], $a_prefill, $node); 
			}
			
		}
	}
}

function pdf_initialize_color($color_node) {
	global $pdf_vars;
	
	if (is_array($color_node)) {
		foreach ($color_node as $k=>$v) {
			$color_id = $color_node[$k]["attributes"]["ID"];
			if ($color_id != "") {
				$pdf_vars['color'][$color_id] = $color_node[$k]["attributes"];
			}
			if ( strtoupper($color_node[$k]["attributes"]["NAME"]) == "BLACK" ) { $pdf_vars['color']["black_id"] = $color_id ; }
			if ($next_id < $color_id) {$next_id = $color_id;}
		}
	}
	++$next_id;
	
	// Make sure we have black
	if (!isset($pdf_vars['color']["black_id"])) {
		$pdf_vars['color']["black_id"] = $next_id;
		$pdf_vars['color'][$next_id] = array("NAME"=>"Black", "ID"=>$next_id, "C"=>0, "M"=>0, "Y"=>0, "K"=>100);
	}		
}

function pdf_watermark ($text) {
	
	if (trim($text) != "") {
		global $pdf_vars;
		
		PDF_save($pdf_vars["pdf"]);
		PDF_rotate($pdf_vars["pdf"], (atan2($pdf_vars["pageheight"], $pdf_vars["pagewidth"]) * 180 / M_PI));
		PDF_setcolor($pdf_vars['pdf'], "stroke", "cmyk", 0, 0, 0, 0.2);
		$font = PDF_findfont($pdf_vars['pdf'], "Helvetica-Bold", "winansi", 0);
		PDF_setlinewidth($pdf_vars['pdf'], .5);
	
		$diagonal = sqrt(($pdf_vars["pagewidth"]  * $pdf_vars["pagewidth"]  + $pdf_vars["pageheight"]  * $pdf_vars["pageheight"] ));
		$len = PDF_stringwidth($pdf_vars["pdf"], $text, $font, 1.0);
		$fontsize = $diagonal * 0.8 / $len;
		
		$ascender = PDF_get_value($pdf_vars["pdf"], "ascender", $font) * $fontsize;
		$descender = PDF_get_value($pdf_vars["pdf"], "descender", $font) * $fontsize;
		
		$y = -1*($ascender+$descender)/2;
		$x = ($diagonal*0.1);
		
		PDF_translate($pdf_vars['pdf'], $x, $y);
		
		PDF_set_value($pdf_vars['pdf'], "textrendering", 1);
		PDF_setfont($pdf_vars['pdf'], $font, $fontsize);
		PDF_show($pdf_vars['pdf'], $text);
		
		PDF_restore($pdf_vars["pdf"]);
		
	}		
}

$pdf_vars['defaults']['leading'] = 12;
$pdf_vars['defaults']['font_size'] = 10;
$pdf_vars['defaults']['font_face'] = "Helvetica";
$pdf_vars['defaults']['font_fill_color'] = 0;
$pdf_vars['defaults']['font_fill_color_tint'] = 100;
$pdf_vars['defaults']['font_stroke_color'] = 0;
$pdf_vars['defaults']['font_stroke_color_tint'] = 100;
$pdf_vars['defaults']['font_stroke_width'] = 1;
$pdf_vars['defaults']['underline'] = "N";
$pdf_vars['defaults']['leftindent'] = 0;
$pdf_vars['defaults']['rightindent'] = 0;
$pdf_vars['defaults']['firstlineindent'] = 0;
$pdf_vars['defaults']['wrap'] = "wrap";




// **************** MAIN EXTERNAL FUNCTION ********************************************
function pdf_create($xml,$a_prefill,$mode="",$siteid="AUTO") {
	global $pdf_vars;

	if ($siteid=="AUTO") {
		$pdf_vars['siteid'] = $_SESSION['site'];
	} else {
		$pdf_vars['siteid'] = $siteid;
	}

	if (is_array($a_prefill)) {
		foreach ($a_prefill as $key=>$val) {
			$a_prefill[$key] = str_replace("<br>", "\n",$val);
		}
	}

	$a_item_xml  = xml_get_tree($xml);
	$a_item =  $a_item_xml[0]['children'];//xml_find_node("item",$a_item_xml);
	$a_item_attrib = $a_item_xml[0]['attributes'];
	
	$template = xml_find_node("template",$a_item);
	if ( !$template ) {  
		print("Error. No template node found."); exit;
	}
	$template = $template[0];
	$a_page = xml_find_node("page",$template['children']);
	$a_page = $a_page[0];
	$a_page_attrib = $a_page["attributes"];
		
	/*
	Set defaults and document settings
	*/
	if ( is_array($a_item_attrib) && $a_item_attrib['PAGEHEIGHT'] != "") {
		if ($a_item_attrib['PAGEWIDTH'] != "" && $a_item_attrib['PAGEWIDTH'] > 0 ) {
			$pdf_vars['pagewidth'] = $a_item_attrib['PAGEWIDTH'];
		} else {
			$pdf_vars['pagewidth'] = 612; // 8.5"
		}
		if ($a_item_attrib['PAGEHEIGHT'] != "" && $a_item_attrib['PAGEHEIGHT'] > 0 ) {
			$pdf_vars['pageheight'] = $a_item_attrib['PAGEHEIGHT'];
		} else {
			$pdf_vars['pageheight'] = 792; // x 11"
		}
	} 

	$bleedTB = $a_page_attrib["BLEEDTB"];
	$bleedLR = $a_page_attrib["BLEEDLR"];
	$pdf_vars['bleedTB'] = $bleedTB;
	$pdf_vars['bleedLR'] = $bleedLR;
		
	$a_color = xml_find_node("color_library",$a_item);

	pdf_initialize_color($a_color[0]['children']);

	pdf_start($mode);
	
	// Translate offset for bleed
	if ($mode=="press") {		
		PDF_translate($pdf_vars['pdf'], $bleedLR, $bleedTB);
	}
	
//	PDF_save($pdf_vars['pdf']);
	
	
	// Add background
	if ($mode=="press") {
		$bkg = $a_page_attrib["PRESSIMAGE"];
	} else {
		$bkg = $a_page_attrib["PROOFIMAGE"];
	}
	$bkg_img = $a_page_attrib[strtoupper($bkg)];
	PDF_save($pdf_vars['pdf']);
	PDF_translate($pdf_vars['pdf'], $bleedLR*-1, $pdf_vars['pageheight']+$bleedTB);
	$pdf_vars['contentbox']['attributes']['GRAPHIC_FIT'] = "scaleto";						
	$pdf_vars['contentbox']['attributes']['WIDTH'] = $pdf_vars['pagewidth']+($bleedLR*2);
	$pdf_vars['contentbox']['attributes']['HEIGHT'] = $pdf_vars['pageheight']+($bleedTB*2);
	place_graphic($bkg_img) ;
//	PDF_restore($pdf_vars['pdf']);
	
	
	pdf_process_template($a_page['children'],$a_prefill);
	
	PDF_restore($pdf_vars['pdf']);
	// Add watermark
	if ($mode!="press") {
		pdf_watermark($a_page_attrib["PROOFWATERMARK"]);
	}
	
	pdf_end();
	
	$buf = PDF_get_buffer($pdf_vars['pdf']);
	PDF_delete($pdf_vars['pdf']);

	return $buf;

}


?>
