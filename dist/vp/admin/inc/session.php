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


if (!IsSet($a_form_vars['mssid'])) {
	if (IsSet($HTTP_COOKIE_VARS['mssid'])) {	
		$a_form_vars['mssid'] = $HTTP_COOKIE_VARS['mssid'];
	} else {
		srand((double)microtime()*1000000); 
		$rand = rand(1000000000,99999999999999);
		$rand2 = rand(1000000000,99999999999999);
		$rand3 = rand(1000000000,99999999999999);
		
		$mssid = $rand . $rand2 . $rand3;	
		if (IsSet($_SERVER["HTTP_X_FORWARDED_FOR"])) {
//			$mssid .=  "_" . $_SERVER["HTTP_X_FORWARDED_FOR"] ;

		} else {
//			$mssid .= "_" . $_SERVER["REMOTE_ADDR"];
		}
		$a_form_vars['mssid'] = urlencode( encrypt($mssid,"encryptit") ) ;
		$a_form_vars['mssid'] = $mssid;
	}
}


setcookie("mssid", $a_form_vars['mssid'],time()+3600*24*90 );
$mssid = $a_form_vars['mssid']  ;//urlencode ()
if ( !ereg("mssid=", $_SERVER["REQUEST_URI"]) ) {
	if (ereg("\?", $_SERVER["REQUEST_URI"]) ) { 
		header ("Location: " . $_SERVER['REQUEST_URI'] . "&mssid=". $mssid);
		exit;
	} else {
		header ("Location: " . $_SERVER['REQUEST_URI'] . "?mssid=". $mssid);
		exit;
	}
}


function session_get_vars($mssid) {
	$sql = "SELECT * FROM Sessions WHERE SessionID='$mssid'";
	$nResult = dbq($sql);
	$aSession = mysql_fetch_assoc($nResult);
	$aSessionVars = xml_get_tree($aSession['SessionVars']);
	if ( is_array($aSessionVars[0]['children']) ) {
		while ( list($k, $v) = each($aSessionVars[0]['children']) ) {
			$aSavedVars[ $v['attributes']['ID'] ] = $v['attributes']['VALUE'];
		}
	}
	return $aSavedVars;
}

function session_save_vars($mssid, $aVars) {
	
	$aSavedVars = session_get_vars($mssid);
	
	if ( is_array($aVars) ) {
		while ( list($k, $v) = each($aVars) ) {
			$aSavedVars[$k] = $v;
		}
	}

	$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n<session id=\"$mssid\" ip=\"$_SERVER[REMOTE_ADDR]\">\n";
	if ( is_array($aSavedVars) ) { 
		while ( list($k, $v) = each($aSavedVars) ) {
			if ($k != "") { $xml .= "<variable id=\"$k\" value=\"$v\"/>\n"; }
		}
	}
	$xml .= "</session>";
	
	$xml = addslashes($xml);
	
	$sql = "SELECT ID FROM Sessions WHERE SessionID='$mssid'"; $nResult = dbq($sql); 
	if ( mysql_num_rows($nResult) == 0) {
		$sql = "INSERT INTO Sessions SET SessionVars='$xml', SessionID='$mssid'";
	} else {
		$sql = "UPDATE Sessions SET SessionVars='$xml' WHERE SessionID='$mssid'";
	}
	$nUpdate = dbq($sql);
}



?>