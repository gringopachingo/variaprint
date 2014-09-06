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

// Portions of this code from PayFlow Pro

	function getPFP() {
		// Initialize: Get PFP data from DB and decrypt/parse XML
		$sql = "SELECT PFP FROM Sites WHERE ID='$_SESSION[site]'";
		$res = dbq($sql);
		$a_res = mysql_fetch_assoc($res);
		$decrypted = decrypt($a_res['PFP'],"tyTeae43ad3adf31ASdas90adEae");
		$a_xmlpfp = xml_get_tree($decrypted);
		
		if (is_array($a_xmlpfp[0]['children'])) {
			foreach($a_xmlpfp[0]['children'] as $node) {
				$a_pfp[strtolower($node['tag'])] = $node['value'];
			}
		}
		return $a_pfp;
	}	

	/* You have the choice of either setting your Defaults within this script (the default), or
		pulling the defaults from the PHP.INI file.
		
		Set $READ_PHP_INI to 0 (zero) to define the constants within this script, or 1 (one) to grab the
		constants from the PHP.INI file. 
		
		FYI: The php_pfpro.dll extension pulls the info from the PHP.INI file only! Just remember, if you decide 
			  to set your constants within this script, when you move to the extension, you'll have to make
			  the necessary changes in the PHP.INI file. */
	
	$READ_PHP_INI = 0;

	define(pfpro_defaulthost, 		($READ_PHP_INI == 0) ? 'test-payflow.verisign.com' 			: get_cfg_var('pfpro.defaulthost'));
	define(pfpro_defaultport, 		($READ_PHP_INI == 0) ? 443 						: get_cfg_var('pfpro.defaultport'));
	define(pfpro_defaulttimeout, 		($READ_PHP_INI == 0) ? 30 						: get_cfg_var('pfpro.defaulttimeout'));
	define(pfpro_proxyaddress, 		($READ_PHP_INI == 0) ? NULL 						: get_cfg_var('pfpro.proxyaddress'));
	define(pfpro_proxyport, 		($READ_PHP_INI == 0) ? NULL 						: get_cfg_var('pfpro.proxyport'));
	define(pfpro_proxylogin,		($READ_PHP_INI == 0) ? NULL						: get_cfg_var('pfpro.proxylogin'));
	define(pfpro_proxypassword,		($READ_PHP_INI == 0) ? NULL						: get_cfg_var('pfpro.proxypassword'));
	
	
	define(PFPRO_EXE_PATH, '/verisign/payflowpro/linux/bin/pfpro');

	
	function pfpro_init()
	{
		/* This function is here for 
			compatibility only. Returns
			NULL (nothing) */
			
		return(NULL);
	}


	function pfpro_cleanup()
	{
		/* This function is here for 
			compatibility only. Returns
			NULL (nothing) */
			
		return(NULL);
	}


	function pfpro_version()
	{		
		@exec(PFPRO_EXE_PATH, $result);
		$version = substr($result[0], strlen($result[0])-4, 4);
		
		return($version);	
	}


	function pfpro_process(
		$transaction, 
		$url=pfpro_defaulthost, 
		$port=pfpro_defaultport, 
		$timeout=pfpro_defaulttimeout, 
		$proxy_url=pfpro_proxyaddress, 
		$proxy_port=pfpro_proxyport, 
		$proxy_logon=pfpro_proxylogin,
		$proxy_password=pfpro_proxypassword)
	{
		if(!(is_array($transaction)))
			return(NULL);

		$libpath="/verisign/payflowpro/linux/lib:/verisign/payflowpro/linux";
		$LD_LIBRARY_PATH="LD_LIBRARY_PATH=".$libpath.":".getenv("LD_LIBRARY_PATH") ;
		putenv($LD_LIBRARY_PATH);

		$PFPRO_CERT_PATH="PFPRO_CERT_PATH=/verisign/payflowpro/linux/certs";
		putenv($PFPRO_CERT_PATH);


		/* destruct (transaction) array into (trans) string
			and dynamically add LENGTH TAGS */
		foreach($transaction as $val1=>$val2)
			$parmsArray[] = $val1  . '['.strlen($val2).']=' . $val2;
		$parmsString = implode('&', $parmsArray);

		
		$trans  = PFPRO_EXE_PATH . ' ';
		$trans .= $url . ' ';
		$trans .= $port . ' "';
		$trans .= $parmsString . '" ';
		$trans .= $timeout . ' ';
		$trans .= $proxy_url . ' ';
		$trans .= $proxy_port . ' ';
		$trans .= $proxy_logon . ' ';
		$trans .= $proxy_password;
		
		/* run transaction, if result blank, return(NULL) */
		@exec($trans, $result);


		if($result[0] == NULL)
			return(NULL);

		/* replace any '&' that are surrounded by spaces -- this assumes
			the '&' isn't a delimiter, but instead part of a message string
			and converting it to 'ASCII(38)' will prevent the explode function from
			thinking it's actually a delimiter. */
		$result[0] = str_replace(' & ', ' ASCII(38) ', $result[0]);
		
		/* construct (pfpro) array out of (result) string */
		$valArray = explode('&', $result[0]);
 
		foreach($valArray as $val)
		{ 
  			$valArray2 = explode('=', $val);
  			$pfpro[$valArray2[0]] = str_replace('ASCII(38)', '&', $valArray2[1]);
		}

		return($pfpro);
	}
	
	
	function pfpro_process_raw($transaction, $autoLenTags=1, $url=pfpro_defaulthost, $port=pfpro_defaultport, $timeout=pfpro_defaulttimeout, 
								  		$proxy_url=pfpro_proxyaddress, $proxy_port=pfpro_proxyport, $proxy_logon=pfpro_proxylogin,
								  		$proxy_password=pfpro_proxypassword)
	{
			
		/* This function receives a string, processes it, and then returns a results string. 
			Use autoLenTags = 1 to enable auto length tags, or 0 (zero) for no length tags --
			the default is 1. 
			
			For example:
			
			pfpro_process_raw(' ... transaction string ... ') will default to 1 if not specified 
			
			or,
			
			pfpro_process_raw(' ... transaction string ... ', 0) will process the string without
			length tags 
			
			This functionality is NOT part of the standard pfpro functions as defined in the 
			PHP manual -- I've added this functionality simply because I think it should be
			here. */
		
		if(!(is_string($transaction)))
			return(NULL);

		/* Check to see if autoLenTags is enabled */
		if($autoLenTags)
		{
			$transaction = str_replace(' & ', ' ASCII(38) ', $transaction);
			$transArray = explode('&', $transaction);
				
			foreach($transArray as $val)
				list($val1[], $val2[]) = split('=', $val, 2);
					
			$cnt = count($transArray);
			for($x=0; $x<$cnt; $x++)
			{
				$val2[$x] = str_replace('ASCII(38)', '&', $val2[$x]);
				$a[] = $val1[$x] . '[' . strlen($val2[$x]) . ']=' . $val2[$x];
			}				
			
			$transaction = implode('&', $a);
		}

		$trans  = PFPRO_EXE_PATH . ' ';
		$trans .= $url . ' ';
		$trans .= $port . ' "';
		$trans .= $transaction . '" ';
		$trans .= $timeout . ' ';
		$trans .= $proxy_url . ' ';
		$trans .= $proxy_port . ' ';
		$trans .= $proxy_logon . ' ';
		$trans .= $proxy_password;
		
		/* run transaction, if result blank, return(NULL) */
		@exec($trans, $result);
		if($result[0] == NULL)
			return(NULL);
		
		return($result);	
	}
	
?>
