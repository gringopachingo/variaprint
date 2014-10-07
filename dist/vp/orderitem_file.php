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


	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    			// Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 	// always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");  	// HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");                          			// HTTP/1.0

	require_once("inc/config.php");	
	require_once("inc/functions-global.php");	
	require_once("inc/functions.php");	
	require_once("inc/iface.php");
	
	if (isset($a_form_vars['mssid'])) {
		session_name("mssid");
		session_start();
		$mssid = session_id();
		if ($_SESSION["privilege"] == "owner") {
			include("admin/inc/popup_log_check.php");
		}
		// add security measures here for slave accounts
		
		if ($a_form_vars['mode']=="raster") {
			$img = "_orderpdfs/". $a_form_vars['id'] ."_preview_raster.jpg";
			if (file_exists($img)) {
				header("Content-type: image/jpeg");
				header("Content-Disposition: inline; filename=img.jpg");
				readfile($img);
			} else {
				print("Could not access file.");
			}
		} elseif ($a_form_vars['mode']=="proofpdf") {
			$img = "_orderpdfs/". $a_form_vars['id'] ."_preview_pdf.pdf";
			if (file_exists($img)) {
				header("Content-type: application/octet-stream");
				header("Content-Disposition: inline; filename=$a_form_vars[id]_proof.pdf");
				readfile($img);
			} else {
				print("Could not access file.");
			}
		} elseif ($a_form_vars['mode']=="presspdf") {
			$img = "_orderpdfs/". $a_form_vars['id'] ."_press_pdf.pdf";
			if (file_exists($img)) {
				header("Content-type: application/octet-stream");
				header("Content-Disposition: inline; filename=$a_form_vars[id]_press.pdf");
				readfile($img);
			} else {
				print("Could not access file.");
			}
		}
		
	}elseif(isset($a_form_vars['ossid'])) {
		session_name("ossid");
		session_start();
		$ossid = session_id();
		if ($a_form_vars['type'] == "ordered") {
			$dir = "_orderpdfs/";
		} else {
			$dir = "_cartpreviews/";
		}
		if ($a_form_vars['mode']=="raster") {
			$img = $dir.$a_form_vars['id'] ."_preview_raster.jpg";
			if (file_exists($img)) {
				header("Content-type: image/jpeg");
				header("Content-Disposition: inline; filename=img.jpg");
				readfile($img);
			} else {
				print("Could not access file.");
			}
		} elseif ($a_form_vars['mode']=="proofpdf") {
			$img = $dir.$a_form_vars['id'] ."_preview_pdf.pdf";
			if (file_exists($img)) {
				header("Content-type: application/octet-stream");
				header("Content-Disposition: inline; filename=$a_form_vars[id]_proof.pdf");
				readfile($img);
			} else {
				print("Could not access file.");
			}
		}
	}
	

?>
