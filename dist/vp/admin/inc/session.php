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


if (!IsSet($a_form_vars[ms_sid])) {
	if (IsSet($HTTP_COOKIE_VARS["ms_sid"])) {	
		$a_form_vars[ms_sid] = $HTTP_COOKIE_VARS["ms_sid"];
	} else {
		srand((double)microtime()*1000000); 
		$rand = rand(1000000000,99999999999999);
		$rand2 = rand(1000000000,99999999999999);
		$rand3 = rand(1000000000,99999999999999);
		
		
		$ms_sid = $rand . $rand2 . $rand3;	
		if (IsSet($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) {
			$ms_sid .=  "_" . $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"] ;
		} else {
			$ms_sid .= "_" . $HTTP_SERVER_VARS["REMOTE_ADDR"];
		}
		$a_form_vars[ms_sid] = urlencode( encrypt($ms_sid,"encryptit") ) ;
	}
}


setcookie("ms_sid", $a_form_vars[ms_sid],time()+3600*24*90 );
$ms_sid = $a_form_vars[ms_sid]  ;//urlencode ()
if ( !ereg("ms_sid=", $HTTP_SERVER_VARS["REQUEST_URI"]) ) {
	if (ereg("\?", $HTTP_SERVER_VARS["REQUEST_URI"]) ) { 
		header ("Location: " . $HTTP_SERVER_VARS[REQUEST_URI] . "&ms_sid=". $ms_sid);
		exit;
	} else {
		header ("Location: " . $HTTP_SERVER_VARS[REQUEST_URI] . "?ms_sid=". $ms_sid);
		exit;
	}
}


function session_get_vars($ms_sid) {
	$sql = "SELECT * FROM Sessions WHERE SessionID='$ms_sid'";
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

function session_save_vars($ms_sid, $aVars) {
	
	$aSavedVars = session_get_vars($ms_sid);
	
	if ( is_array($aVars) ) {
		while ( list($k, $v) = each($aVars) ) {
			$aSavedVars[$k] = $v;
		}
	}

	$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n<session id=\"$ms_sid\" ip=\"$_SERVER[REMOTE_ADDR]\">\n";
	if ( is_array($aSavedVars) ) { 
		while ( list($k, $v) = each($aSavedVars) ) {
			if ($k != "") { $xml .= "<variable id=\"$k\" value=\"$v\"/>\n"; }
		}
	}
	$xml .= "</session>";
	
	$xml = addslashes($xml);
	
	$sql = "SELECT ID FROM Sessions WHERE SessionID='$ms_sid'"; $nResult = dbq($sql); 
	if ( mysql_num_rows($nResult) == 0) {
		$sql = "INSERT INTO Sessions SET SessionVars='$xml', SessionID='$ms_sid'";
	} else {
		$sql = "UPDATE Sessions SET SessionVars='$xml' WHERE SessionID='$ms_sid'";
	}
	$nUpdate = dbq($sql);
}



?>