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


require("inc/config.php");
require("inc/image.class.php");

/*
require_once("inc/functions-global.php");

$oFile = new File;

foreach($_POST as $key=>$val) {
	$it .= "$key=$val\n";
}
$oFile->write_file("testlog",$it);
*/


switch ($_POST['action']) {
	case 'crop':
		$src = $_POST['src'];
		$filename = $_POST['filename'];
		$dest = $_POST['dest'];
		$img = new Image($src, $filename);
		$img->crop($dest, $_POST['width'], $_POST['height'], $_POST['x'], $_POST['y']);
		$o_file = new File;

		$resolution = 300;
		$res_factor = $resolution/72;
		$image_data = $o_file->read_file($dest.$filename);
		$scale_w = 100;
		$scale_h = 100;
		if ($_POST['width'] > 0) {
			$scale_w = ($_POST['cropw']/$_POST['width'] ) * $res_factor * 100;  
		} 
		if ($_POST['height'] > 0) {
			$scale_h = ($_POST['croph']/$_POST['height'] ) * $res_factor * 100 ;
		}
	//	if ($scale_w > $scale_h) $scale = $scale_w;  else $scale = $scale_h;
		$scale = ($scale_w > $scale_h) ? $scale_w : $scale_h;  
		$image_data = make_jpeg ($image_data,$scale,false);
		if (file_exists($dest.$filename)) unlink ($dest.$filename);
		$o_file->write_file($dest.$filename,$image_data);

		break;
}

?>
