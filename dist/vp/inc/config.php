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


	$cfg_www_dir = "/www/";
	$cfg_sub_dir = "/vp/";
	$cfg_tmp_dir = "/tmp/";
	$cfg_base_dir = "/www/vp/";

	$cfg_system_from_email = "";
	$cfg_invoice_from_email = "";
	$cfg_admin_email = ""; // comma-delimited list
	$cfg_pdflib_license = "";
	$cfg_DB = "vp";
	$cfg_DB_host = "localhost";
	$cfg_DB_username = "";
	$cfg_DB_password = "";
	$cfg_DB_connection = mysql_connect($cfg_DB_host, $cfg_DB_username, $cfg_DB_password);
	$cfg_DB_select = mysql_select_db($cfg_DB, $cfg_DB_connection);

	// {$cfg_secure_url}{$cfg_secure_dir}{$cfg_sub_dir}
/**

See encrypt.php to set encryption key locations...

*/

	// secure server parameters
	$cfg_secure_url = ""; // hostname or ip only
	$cfg_secure_dir = "";
	$cfg_insecure_url = "			"; // hostname or ip only
	$cfg_insecure_dir = "";
	$cfg_use_security = false; //true;

	define("CLI_MKPSRES","/usr/local/bin/mkpsres");		// Util originally from Adobe, now distr. with X11 for creating PSRes.upr files for fonts 
	define("CLI_GS","/usr/bin/gs"); 			// GhostScript
	define("CLI_CONVERT","/usr/local/bin/convert"); 	// command in ImageMagick CLI
	define("CLI_MOGRIFY","/usr/local/bin/mogrify"); 	// command in ImageMagick CLI
	define("CLI_JPEGOPTIM","/usr/local/bin/jpegoptim"); 	// from Timo Kokkonen: http://www.cc.jyu.fi/~tjko/projects.html
	define("CLI_JPEGTRAN", "/usr/bin/jpegtran");	// used to make jpeg files progressive scan
	
	define("CLI_UNZIP","/usr/bin/unzip");
	define("CLI_CP","cp");
	define("CLI_MKDIR","mkdir");
	define("CLI_RM","rm");
	define("CLI_MV","mv");


?>
