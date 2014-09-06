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


if (!IsSet($a_form_vars[sid])) {
	if (IsSet($HTTP_COOKIE_VARS["sid"])) {	
		$a_form_vars[sid] = $HTTP_COOKIE_VARS["sid"];
	} else {
		srand((double)microtime()*1000000); 
		$rand = rand(1000000000,99999999999999);
		$rand2 = rand(1000000000,99999999999999);
		$rand3 = rand(1000000000,99999999999999);
		
		
		$os_sid = $rand . $rand2 . $rand3;	
		if (IsSet($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) {
			$os_sid .=  "_" . $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"] ;//.
		} else {
			$os_sid .= "_" . $HTTP_SERVER_VARS["REMOTE_ADDR"];
		}
		$a_form_vars[sid] = encrypt($os_sid,"encryptit") ;
	}
}

setcookie("sid", $a_form_vars[sid]);
$os_sid = $a_form_vars[sid]  ;//urlencode ()
if ( !ereg("sid=", $HTTP_SERVER_VARS["REQUEST_URI"]) ) {
	if (ereg("\?", $HTTP_SERVER_VARS["REQUEST_URI"]) ) { 
		header ("Location: " . $HTTP_SERVER_VARS[REQUEST_URI] . "&site=$_SESSION[site]&os_sid=$_SESSION[os_sid]");
		exit;
	} else {
		header ("Location: " . $HTTP_SERVER_VARS[REQUEST_URI] . "&site=$_SESSION[site]&os_sid=$_SESSION[os_sid]");
		exit;
	}
}


function session_get_vars($os_sid) {
	$sql = "SELECT * FROM Sessions WHERE SessionID='$os_sid'";
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

function SaveSessionVars($var) {
//	$aSavedVars = session_get_vars($os_sid);
/*
	if ( is_array($aVars) ) {
		while ( list($k, $v) = each($aVars) ) {
			$aSavedVars[$k] = $v;
		}
	}

	$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n<session id=\"$os_sid\" ip=\"$_SERVER[REMOTE_ADDR]\">\n";
	if ( is_array($aSavedVars) ) { 
		while ( list($k, $v) = each($aSavedVars) ) {
			if ($k != "") { $xml .= "<" . "variable id=\"$k\" value=\"" . htmlentities($v) . "\"/>\n"; }
		}
	}
	$xml .= "</session>";
	
	$xml = addslashes($xml);
	*/
	
//	$var = session_encode($var);
	
	$sql = "SELECT ID FROM Sessions WHERE SessionID='$os_sid'"; $nResult = dbq($sql); 
	if ( mysql_num_rows($nResult) == 0) {
		$sql = "INSERT INTO Sessions SET SessionVars='', SessionID='$os_sid'";
	} else {
		$sql = "UPDATE Sessions SET SessionVars='' WHERE SessionID='$os_sid'";
	}
	$nUpdate = dbq($sql);
}


?>