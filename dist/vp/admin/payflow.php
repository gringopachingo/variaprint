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


	session_name("ms-sid");
	session_start();
	$ms_sid = session_id();

	require_once("../inc/config.php");
	require_once("../inc/functions-global.php");
	require_once("../inc/encrypt.php");
	require_once("inc/functions.php");
	require_once("inc/iface.php");
	require_once("inc/session.php");
	require_once("../inc/pfpro.php");
	
	require_once("inc/popup_log_check.php");
	
	SecureServerOn(true, "?ms_sid=$ms_sid");
	
	if (isset($a_form_vars['save']) || isset($a_form_vars['test'])) {
	//	print("saving...");
		
		if ($a_form_vars[password] == "****** ******") {
			$a_pfp = getPFP();
			$a_form_vars[password] = $a_pfp['password'];
		}
		
		$xml = "<" . "?xml version=\"1.0\"?"."><pfp>";
		$xml .= "<partner>".htmlentities($a_form_vars[partner])."</partner>";
		$xml .= "<vendor>".htmlentities($a_form_vars[vendor])."</vendor>";
		$xml .= "<user>".htmlentities($a_form_vars[user])."</user>";
		$xml .= "<password>".htmlentities($a_form_vars[password])."</password>";
		$xml .= "<avs>".htmlentities($a_form_vars[avs])."</avs>";
		$xml .= "<csc>".htmlentities($a_form_vars[csc])."</csc>";
		$xml .= "<comment>".htmlentities($a_form_vars[comment])."</comment>";
		$xml .= "</pfp>";
				
		$xml = addslashes(encrypt($xml,"tyTeae43ad3adf31ASdas90adEae"));
		$sql = "UPDATE Sites SET PFP='$xml' WHERE ID='$_SESSION[site]'";
		dbq($sql);
		
	}
	
	if (isset($a_form_vars['test'])) {
		
	//	print("testing...");
		$a_pfp = getPFP();
		$transaction = array(
			'USER'    => $a_pfp[user],
			'PWD'     => $a_pfp[password],
			'PARTNER' => $a_pfp[partner],
			'VENDOR' =>  $a_pfp[vendor],
			'TENDER' =>  'C',
			'TRXTYPE' => 'A',
			'AMT'     => '1.23',
			'ACCT'    => '4111111111111111',
			'EXPDATE' => date("my",time()+(86400*12))
		);

		$cc_result = pfpro_process($transaction,"test-payflow.verisign.com");
		
		if ($cc_result['RESULT'] == "0" && isset($cc_result['RESULT'])) {
			$msg = "Account info is valid.";
		} else {
			if ($cc_result['RESPMSG'] == "" || !isset($cc_result['RESULT'])) {
				$msg = "Unable to verify account info.";
			} else {
				$msg = $cc_result['RESPMSG'];
			}
		}
	//	print_r($cc_result);
	}
	
	
	$a_pfp = getPFP();
	if (isset($a_pfp['password'])) {
		$a_pfp['password'] = "****** ******";
	}

?><html>
<head>
<title>PayFlow Pro</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>

<?

if (isset($msg)) {
	print("<table cellpadding=10 cellspacing=0 bgcolor=orange border=0><tr><td class=\"text\">
		$msg
	</td></tr></table>
	");	
}

?>
<p class="titlebold">VeriSign&reg; PayFlow
Pro&#8482; Setup</p>
<form name="form1" method="post" action="">
  <strong><span class="subhead">Account Info</span></strong>
  <span class="text">(all required) </span><table width="363" height="66" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td class="text">PayFlow Pro Partner</td>
      <td class="text">        <input name="partner" type="text" id="partner" value="<? print($a_pfp[partner]); ?>">
      </td>
    </tr>
    <tr>
      <td class="text">Vendor ID</td>
      <td class="text">        <input name="vendor" type="text" id="vendor" value="<? print($a_pfp[vendor]); ?>">
      </td>
    </tr>
    <tr>
      <td width="148" class="text">PayFlow Pro Username</td>
      <td width="215" class="text">        <input name="user" type="text" id="user" value="<? print($a_pfp[user]); ?>">
      </td>
    </tr>
    <tr>
      <td><span class="text">Payflow Pro Password</span><span class="text"></span></td>
      <td class="text">        <input name="password" type="password" value="<? print($a_pfp[password]); ?>">
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td class="text">        <input name="test" type="submit" id="test3" value="Test Account Info">
      </td>
    </tr>
  </table>
  <br>
    <strong><span class="subhead">Address Verification Service (AVS)</span></strong><br>
    <span class="text">
    <input name="avs" type="checkbox" id="avs" value="checked" <? print($a_pfp[avs]); ?>>
    Do not allow transaction if address entered  by buyer is incorrect<br>
    </span><br>
    <span class="subhead"><strong>Card Security Code Validation (CSC)</strong></span><br>
    <span class="text">
    <input name="csc" type="checkbox" id="csc" value="checked" <? print($a_pfp[csc]); ?>> 
    Do not allow transaction if code  entered by buyer is incorrect</span> <br>
    <br>
    <strong><span class="subhead">Optional comment
    to include with each transaction for internal reporting</span></strong><br>
        <input name="comment" type="text" id="comment" value="<? print($a_pfp[comment]); ?>" size="45" maxlength="128">
</p>
  <p>
    <input type="button" value="Close" onClick="top.close()">
    <input name="save" type="submit" id="save2" value="Save">
</p>
</form>
</body>
</html>
