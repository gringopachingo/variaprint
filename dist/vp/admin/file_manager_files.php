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

//session_save_path("/www/tmp");
session_name("ms_sid");
session_start();
$ms_sid = session_id();
$_SESSION['updated'] = " ";

require_once("../inc/config.php");
require_once("../inc/functions-global.php");
if ($_SESSION["privilege"] == "owner") {
	require_once("inc/popup_log_check.php");
}


	
	if ($a_form_vars['folder'] != "") {  $a_form_vars['folder'].= "/"; }
	$basedir = $cfg_base_dir . "_sites/" . $_SESSION["site"] . "/images/" . $a_form_vars["folder"]; // Base directory
	
	if ($a_form_vars[action] == "delete") {
		// Check to see if if we need to delete a file
		if ( isset($a_form_vars['deletefile']) && $a_form_vars['deletefile'] != "" && $a_form_vars['confirmed'] == 1 ) {
			$qualified_file = $basedir . $a_form_vars['deletefile']; 
			if ( file_exists($qualified_file)) { 
				unlink($qualified_file);
				exit("result=success"); // flash uses this to know if it was a success
			} else {
				exit("result=error"); // flash uses this to display error
			}
		}
		
	} else if ($a_form_vars[action] == "list"){
	
		$dir_files = $dir_subdirs = array();
	
		// Change to directory
		chdir($basedir);
		
		// Open directory;
		$handle = @opendir($basedir . "/") or die("Directory \"$dir\"not found.");
		
		// Loop through all directory entries, construct
		// two temporary arrays containing files and sub directories	
		while($entry = readdir($handle))
		{
			if (!is_dir($entry) && $entry != ".." && $entry != "." && !ereg("^\.{1,}",$entry) )
			{
				$dir_files[] = $entry;
			}
		}
		
		natcasesort($dir_files);
		
		$fp = true;
		if (count($dir_files) > 0)
		{
			foreach($dir_files as $i=>$name)
			{
				if (!$fp) $file_list .= "&";
				$file_list .= "f".$i."=".$name ;
				$fp = false;
			}
		}
				
		print($file_list); 
	}
?>