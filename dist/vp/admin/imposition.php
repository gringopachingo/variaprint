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
	
	
	$time = date("Y-m-d@G-i", time());
	
	if ($suppress_display != true) { // only do this if the imposition is being downloaded
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$time.pdf");
		
		session_name("mssid");
		session_start();
		$mssid = session_id();

		// This is a workaround to IE 5 for Mac so that the headers work.
		// It doesn't seem to corrupt the PDF!?
		print(" ");
	}

	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	require_once("../inc/functions_pdf.php");
	require_once("../inc/encrypt.php");
	require_once("inc/functions.php");
	require_once("inc/iface.php");
	
	if (!$_SESSION['privilege_dockets']) {
		require_once("inc/popup_log_check.php");
	}
	
	
	function make_imposition($a_impose,$imp_id) {
		global $cfg_base_dir;
		// Get imposition settings
		$sql = "SELECT * FROM Imposition WHERE ID='$imp_id'";
		$r_result = dbq($sql);
		$a_result = mysql_fetch_assoc($r_result);
		
		$a_tree = xml_get_tree($a_result['Definition']);
		
		if (is_array($a_tree[0]['children'])) {
			foreach($a_tree[0]['children'] as $node) {
				$val = $node['attributes']['VALUE'];
				$id = $node['attributes']['ID'];
				$a_imposition[$id] = $val;			
			}
		}
		
	//	$general_info 	= $a_imposition['general_info'];
	//	$order_info	 	= $a_imposition['order_info'];
		$color_bars		= $a_imposition['color_bars'];
		$registration 	= $a_imposition['registration'];
		$trim_marks		= $a_imposition['trim_marks'];
	
		$columns 		= $a_imposition['item_across'];
		$rows	 		= $a_imposition['item_down'];
		$top_bleed 		= $a_imposition['item_tb_bleed']*2;
		$side_bleed		= $a_imposition['item_lr_bleed']*2;
		$item_width		= $a_imposition['item_width'];
		$item_height	= $a_imposition['item_height'];
		$start_left		= $a_imposition['item_left'];
		$start_top		= $a_imposition['item_top'];
		$page_width		= $a_imposition['imposed_width'];
		$page_height	= $a_imposition['imposed_height'];
	
		$impose_block_width		= ($columns*$item_width)+(($columns-1)*$side_bleed);
		$impose_block_height	= ($rows*$item_height)+(($rows-1)*$top_bleed);
		
		
		// Loop through each order and impose	
				
		$pages = count($a_impose)/($rows*$columns);
		
		
		$these_pages = $pages;
		$itemcntr = 0;
		$pagecntr = 1;
		
		// Loop through pages
	
		// Start PDF
		$pdf = PDF_new();
	
		PDF_set_parameter($pdf, "license", $cfg_pdflib_license);
		PDF_set_parameter($pdf, "compatibility", "1.4");
		PDF_open_file($pdf, "");
		
		$sql = "SELECT Lastname,Firstname,Company FROM AdminUsers WHERE Username='$_SESSION[username]'";
		$res = dbq($sql);
		$a_user = mysql_fetch_assoc($res);
		
		PDF_set_info($pdf, "Creator", "VariaPrint(TM) Imposer");
		PDF_set_info($pdf, "Author", $a_user[Company] . ": " . $a_user['Firstname'] . " " . $a_user['Lastname']);
		PDF_set_info($pdf, "Title", strtoupper($a_result['Name']) . " IMPOSITION" );
		PDF_set_info($pdf, "Subject", "Dynamic PDF Imposition File");
		
		
	
		while ($these_pages > 0) {
			
	
			// Start a new page
			PDF_begin_page($pdf, $page_width, $page_height);
			
			// Set color to print on all plates
			PDF_setcolor($pdf, "fill", "cmyk", 1, 1, 1, 1);
			$spotVariable = PDF_makespotcolor($pdf, "All");
			PDF_setcolor($pdf, "stroke", "spot", $spotVariable, 1);
			
			$top_edge = $page_height - $start_top - $item_height;
			
			if ($registration == "true") {
				// top
				$centerX =  ($impose_block_width/2)+$start_left;
				$centerY = $page_height - $start_top +24;
				registration_mark($pdf, $centerX, $centerY);
				// right
				$centerX =  $impose_block_width+$start_left+24;
				$centerY = $page_height - $start_top - ($impose_block_height/2);
				registration_mark($pdf, $centerX, $centerY);
				// bottom
				$centerX =  ($impose_block_width/2)+$start_left;
				$centerY = $page_height - $start_top - $impose_block_height -24;
				registration_mark($pdf, $centerX, $centerY);
				// left
				$centerX =  $start_left - 24;
				$centerY = $page_height - $start_top - ($impose_block_height/2);
				registration_mark($pdf, $centerX, $centerY);
			}
			
			
			if ($color_bars == "true") {
				
			}
			
			
			if ($trim_marks == "true") {
				
				// Place crop marks on the page
				// Horizontal
				for ($j=0; $j<2; $j++) {
					$pos = $page_height - $start_top + $top_bleed; 
					$increment = $item_height + $top_bleed;
					if ($j==0) {
						$left = $start_left - 9 ;
					} elseif ($j==1) {
						$left = ($columns*$item_width) + ($side_bleed*($columns-1)) + $start_left + 21 ;
					}
						
					$fp = true;
					for ($i=0; $i<$rows+1; $i++) {
						if (!$fp || ($top_bleed <= 0 && $fp)) {
							PDF_moveto($pdf, $left, $pos);
							PDF_lineto($pdf, $left-12, $pos);
							PDF_stroke($pdf);
						}
						$fp = false;
						
						if ($top_bleed > 0 && $i!=$rows) {
							PDF_moveto($pdf, $left, $pos-$top_bleed);
							PDF_lineto($pdf, $left-12, $pos-$top_bleed);
							PDF_stroke($pdf);
						}
						$pos -= $increment ;
					}
				}
				
				// Vertical
				for ($j=0; $j<2; $j++) {
					$pos = $start_left - $side_bleed; 
					$increment = $item_width + $side_bleed;
		
					if ($j==0) {
						$bottom = $page_height - $start_top + 9 ;
					} elseif ($j==1) {
						$bottom = $page_height - $start_top - 21 - ($item_height*$rows) - ($top_bleed*($rows-1)) ;
					}
					$fp = true;
					for ($i=0; $i<$columns+1; $i++) {
						if (!$fp || ($side_bleed <= 0 && $fp)) {
							PDF_moveto($pdf, $pos, $bottom);
							PDF_lineto($pdf, $pos, $bottom+12);
							PDF_stroke($pdf);
						}
						$fp = false;
						if ($side_bleed > 0 && $i!=$columns) {
							PDF_moveto($pdf, $pos+$side_bleed, $bottom);
							PDF_lineto($pdf, $pos+$side_bleed, $bottom+12);
							PDF_stroke($pdf);
						}
						$pos += $increment ;
					}
					
				}
			} // end trim marks
			
			
			
			
			// Loop through rows and place items
			$these_rows = $rows;
			while ($these_rows > 0) {
				
				$left_edge = $start_left;
						
				$these_columns = $columns;
				// Loop through columns
				while ($these_columns > 0) {
					
					// Place the item
					$infile = $cfg_base_dir . "_orderpdfs/" . $a_impose[$itemcntr] . "_press_pdf.pdf";
					
					
					$infiles .= $infile . "<br>\n";
					/*
					PDF_set_parameter($pdf, "pdiusebox", "bleed");
					$test_item = PDF_open_pdi($pdf, $infile, "", 0);
					if ($item != false) {
						$test_item_page = PDF_open_pdi_page($pdf, $test_item, 1, "");
						if ($test_item_page != false) {
							$bleed_width = PDF_get_pdi_value($pdf, "width", $test_item, $test_item_page, 0);
							$bleed_height = PDF_get_pdi_value($pdf, "height", $test_item, $test_item_page, 0);
						}
						PDF_close_pdi_page($pdf, $test_item_page);
					}
					
					PDF_set_parameter($pdf, "pdiusebox", "media");
					PDF_set_parameter($pdf, "pdiwarning", "false");			
					*/	
					
					$item = PDF_open_pdi($pdf, $infile, "", 0);
					if ($item != false) {
						$this_id = $a_impose[$itemcntr];
						if (!isset($item_page[$this_id])) {
							$item_page[$this_id]['pdf'] = PDF_open_pdi_page($pdf, $item, 1, "");
							if ($item_page[$this_id]['pdf'] != false) {
								$item_page[$this_id]['bleed_lr'] = PDF_get_pdi_value($pdf, "/BleedBox/llx", $item, $item_page[$this_id]['pdf'], 0);
								$height = PDF_get_pdi_value($pdf, "height", $item, $item_page[$this_id]['pdf'], 0);
								$item_page[$this_id]['bleed_tb'] = $height-PDF_get_pdi_value($pdf, "/BleedBox/ury", $item, $item_page[$this_id]['pdf'], 0);
								
							//	print($height . " :: " . $item_page[$this_id]['bleed_tb']. "\n");
								
								if ($item_page[$this_id]['bleed_lr'] == false) { $item_page[$this_id]['bleed_lr'] = 0; }
								if ($item_page[$this_id]['bleed_tb'] == false) { $item_page[$this_id]['bleed_tb'] = 0; }
							}
						}
						if ($item_page[$this_id]['pdf'] != false) {
							$bleed_tb = $item_page[$this_id]['bleed_tb'];
							$bleed_lr = $item_page[$this_id]['bleed_lr'];
							
							
							PDF_place_pdi_page($pdf, $item_page[$this_id]['pdf'], $left_edge-$bleed_lr, $top_edge-$bleed_tb, 1, 1);
						}
					} 
									
					// Move the left edge over a column and increment
					$left_edge += $item_width + $side_bleed;
					++$itemcntr;
					--$these_columns;
					
				}
				
				// Drop the top edge down a row and increment
				$top_edge -= ($item_height + $top_bleed);
				--$these_rows;
			
			}
			
			// End the page
			PDF_end_page($pdf);
			++$pagecntr;
			--$these_pages;
		}
		
		if (is_array($item_page)) {
			foreach ($item_page as $k=>$page) { 
				PDF_close_pdi_page($pdf, $item_page[$k]['pdf']);
			}
		}
		
		PDF_close($pdf);
	
		$file = PDF_get_buffer($pdf);
		PDF_delete($pdf);
		return $file;
	} // end make_imposition()
	
	$len = strlen($file);
	$time = date("Y-m-d @ G.i", time());
	
//	exit($infiles);
//	exit("$content");
//	header("Cache-control: private");
//	header("Content-Length: $len");
//	header("Content-Disposition: attachment; filename=Imposed_time.pdf");//$
//	header("Content-Type: application/octet-stream");

	if ($suppress_display != true) { // only do this if the imposition is being downloaded
		$a_impose = array_find_key_prefix("impose_",$a_form_vars, true);
		$file = make_imposition($a_impose,$a_form_vars[imposition_id]);
		print $file;
	}

?>
