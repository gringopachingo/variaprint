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


$cfg_encrypt_key1_path = "/home/www-data/sites.cplitho.com/vp/key_1.php"; // don't include keys in publicly accessible directory
$cfg_encrypt_key2_path = "/home/www-data/sites.cplitho.com/vp/key_2.php";

require_once($cfg_encrypt_key1_path);
require_once($cfg_encrypt_key2_path);

function lookfor($a, $v)
{ 
  $arrylen = count($a);
  for ($j=0; $j<$arrylen; $j++) {
    if ($a[$j] == $v) return $j;
  }
  return -1;
}

function reorder($acount) 
{
  for ($i=0; $i<$acount; $i++) {
    do {
      $nextnumber = rand(0,$acount - 1);
    } while (lookfor($aorder, $nextnumber)>=0);
    $aorder[$i] = $nextnumber;
  }

  return $aorder;
}


function encrypt($data, $passwd)
{
  if (trim($data) == "") {
    return "";
  }
 
  $cryptomime = new HCEMD5($passwd, $key_1);
  $encrypt = $cryptomime->hce_block_encode_mime($data);
  return $encrypt;
}

function decrypt($data, $passwd)
{
  if (trim($data) == "") {
    return "";
  }
 
  $cryptomime = new HCEMD5($passwd, $key_1);
  $encrypt = $cryptomime->hce_block_decode_mime($data);
  return $encrypt;
}

  
function cc_trim($ccnum) 
{
  // removes any characters that are not numeric
  $newval = "";
  $len = strlen($ccnum);
  for ($i=0; $i < $len; $i++) {
    $ch = substr($ccnum, $i, 1);
    if ($ch >= "0" && $ch <= "9") { 
      $newval = $newval . $ch;
    }
  }
  return $newval;
}

function cc_validate($ccnum, $cctype) {
  // valid types are VISA, MC, AMEX, DISC
  // this assumes the ccnum is truncated by above function
  $cclen = strlen($ccnum);
  $cctype = strtoupper($cctype);
  
  // check the company digit and number of digits
  if ($cctype == "VISA") {
    if ($cclen!=13 && $cclen!=16) return FALSE; 
    if (substr($ccnum,0,1)!="4") return FALSE;
  } elseif ($cctype == "MC") {
    if ($cclen!=16) return FALSE;
    if (substr($ccnum,0,1)!="5") return FALSE;
  } elseif ($cctype == "AMEX") {
    if ($cclen!=15) return FALSE;
    if (substr($ccnum,0,2)!="37") return FALSE;
  } elseif ($cctype == "DISC") {
    if ($cclen!=16) return FALSE;
    if (substr($ccnum,0,1)!="6") return FALSE;
  } else {
    if ($cclen!=12) return FALSE;
    if (substr($ccnum,0,1)!="3") return FALSE;
  }

  // validate the checksum
  if ($cclen % 2 == 1) { // this is an odd length so make it even
    $ccnum = "0" . $ccnum;
    $cclen++;
  }

  $ccchecksum = 0;
  for ($i=0; $i<$cclen; $i++) {
    $ccdigit = substr($ccnum, $i, 1);
    if ($i % 2 == 0) {
      $ccdigit *= 2;
      if ($ccdigit > 9) $ccdigit = $ccdigit - 9;
    }
    $ccchecksum += $ccdigit;
  }
  if ($ccchecksum % 10 != 0) return FALSE;

  return TRUE;
}


class HCEMD5 {
  var $hce_md5_sec_key;
  var $hce_md5_rand;
  
  function HCEMD5($key, $rand) {
  // Initalize The Keys ..
    if ($key && $rand) {
      $this->hce_md5_sec_key = $key;
      $this->hce_md5_rand = $rand;
      return true;
    } else {
      return false;
    }
  }
    
  function  _new_key($round) {
    // this returns a hex string
    $digest = md5($this->hce_md5_sec_key . $round);
    // convert to binary
    $digest = pack("H*", $digest);
    $e_block = unpack('C*', $digest);
    return $e_block;
  }

  function packarray($parray) {
    // this assumes that array is ones based 
    // we do this because the php unpack uses a ones based array
    if (!IsSet($parray[0])) $parray[0] = 0;
    $out = "";
    $len = count($parray);
    for ($i=1; $i<$len; $i++) {
      $out .= chr($parray[$i]);
    } 
    return $out; 
  }

  function hce_block_encrypt($data) {
    $data = unpack('C*', $data);
    unset($ans);
    $e_block = $this->_new_key($this->hce_md5_rand);
    $data_size = count($data);
    // php unpack unpacks to a 1-based array rather than 0-based
    for($i=0; $i < $data_size; $i++) {
      $mod = $i % 16;
      if(($mod == 0) && ($i > 15)) {
        // this looks odd because of 1-based array
        $tmparr = array(0, $ans[$i - 15],  $ans[$i - 14], $ans[$i - 13], 
          $ans[$i - 12], $ans[$i - 11], $ans[$i - 10], $ans[$i - 9], 
          $ans[$i - 8], $ans[$i - 7], $ans[$i - 6], $ans[$i - 5], 
          $ans[$i - 4], $ans[$i - 3], $ans[$i - 2], $ans[$i - 1], $ans[$i] );
        $e_block = $this->_new_key($this->packarray($tmparr));
      }
      // the +1 is because php unpack uses 1-based array
      $ans[$i+1] = $e_block[$mod+1] ^  $data[$i+1];
    }
    return $this->packarray($ans);
  }

  function hce_block_decrypt($data) {
    $data = unpack('C*', $data);
    unset($ans);
    $e_block = $this->_new_key($this->hce_md5_rand);
    $data_size = count($data);
    // php unpack unpacks to a 1-based array rather than 0-based
    for($i=0; $i < $data_size; $i++) {
      $mod = $i % 16;
      if(($mod == 0) && ($i > 15)) {
        // this looks odd because of 1-based array
        $tmparr = array(0,$data[$i - 15],  $data[$i - 14], $data[$i - 13], 
          $data[$i - 12], $data[$i - 11], $data[$i - 10], $data[$i - 9], 
          $data[$i - 8], $data[$i - 7], $data[$i - 6], $data[$i - 5], 
          $data[$i - 4], $data[$i - 3], $data[$i - 2], $data[$i - 1], 
          $data[$i] );
        $e_block = $this->_new_key($this->packarray($tmparr));
      }
      // the +1 is because php unpack uses 1-based array
      $ans[$i+1] = $e_block[$mod+1] ^  $data[$i+1];
    }
    return $this->packarray($ans);
  }

  function hce_block_decode_mime ($data) {
    $data = base64_decode($data);
    return $this->hce_block_decrypt($data);
  }

  function hce_block_encode_mime ($data) {
    $data = $this->hce_block_encrypt($data);
    return base64_encode($data);
  }

}

?>
