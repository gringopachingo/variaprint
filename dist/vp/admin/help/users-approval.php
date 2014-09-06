<!--
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
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Help: Setting up approval managers</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body>
<p class="titlebold">Setting up approval managers</p>
<p class="text">There are two ways for an approval manager to be set up:</p>
<ol>
  <li class="text"> Require a manager account: manager can approve any order
    from any buyer. <br>
    - To use this option, make sure the manager's access/notification options
    has the &quot;Notify on new order&quot; and &quot;Allow Manager to Approve
    Orders&quot; checked.</li>
  <li class="text">Don't use a manager account: an email is sent to the manager
    based on what approval option the buyer selects at checkout; manager can
    only approve orders where they are selected by buyer. <br>
    - To use this option, go to Site &gt; Settings &gt; Checkout &amp; E-commerce
    and under Other Options check &quot;Include 'Order Approval Manager' Selection&quot; and
    click the link to edit the approval manager email addresses and descriptions.</li>
</ol>
</body>
</html>
