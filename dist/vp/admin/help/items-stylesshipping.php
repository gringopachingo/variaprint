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
<title>Help: Editing an item's properties</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body>
<p class="titlebold">Setting up a shipping
cost style</p>
<p class="text">To set up a shipping cost style, go to Items and click on Shipping...
  in the submenu. Then click Add... next to the profile menu and click the Save
  button at the bottom of the window. Modify the settings to the desired rates.</p>
<p class="text"><strong>How shipping is calculated for an order</strong><br>Shipping is calculated by adding:<br>
  The base order handling cost
    (added to an order's shipping cost only once) <br>
  + The item's handling cost (added to an order's shipping cost for each item), <br>
+ The weight-based shipping cost*. </p>
<p class="text">*The weight-based shipping cost is calculated by determining
  the total weight for the order (from the weights entered in each item's weight
  property)
and looking up the cost in the weight/cost table for the selected shipping region
  and method.</p>
</body>
</html>
