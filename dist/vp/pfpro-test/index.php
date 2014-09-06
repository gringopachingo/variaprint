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


require_once("pfpro.php");

if ($_GET[action] == "process") {
	$transaction = array('USER'    => 'USERNAME',
                     'PWD'     => 'PASSWORD',
                     'PARTNER' => 'VeriSign',
                     'TRXTYPE' => 'S',
                     'TENDER'  => 'C',
                     'AMT'     => $_GET[amt],
                     'ACCT'    => '4111111111111111',
                     'EXPDATE' => $_GET[expdate]
                    ); 


	$cc_result = pfpro_process($transaction);

	print_r($_GET);

	print("\n-----------------\n");

	print_r($cc_result);

}

?><html>
<head>
<title>Test CC Transaction</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<form name="form1" method="get" action="">
  <p>Amount<br>
    <input name="amt" type="text" id="amt">
    <br>
    <br>
  Exp:<br>
  <input type="text" name="expdate">
  </p>
  <p>
    <input type="submit" name="Submit" value="Test">
</p>
</form>
</body>
</html>
