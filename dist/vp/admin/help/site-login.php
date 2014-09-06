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
<title>Help: Setting order site user (buyer) login requirements</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body>
<p class="titlebold">Setting order site buyer login requirements</p>
<p class="text">The buyer can be required to log in at a number of different
  points in an order site or not at all. Here are the stages a buyer may be
  required to log in and how to change the settings:</p>
<ul>
  <li class="text"><strong>Catalog</strong> &#8212; Buyer cannot see the catalog until logging in. To
    use this option, open the site in the manager, go to Site &gt; Settngs and select
    the Catalog tab, thenset  &quot;Require users to login? &quot; to &quot;To view
    catalog&quot;.</li>
  <li class="text"><strong>To customize an item</strong> &#8212; Buyers will be able
    to view the catalog but not be able to create/add an item to their cart
    without logging in. To
  use this option, open the order site in the manager, go to Site &gt; Settings
  and select the Catalog tab, then set &quot;Require users to login? &quot; to &quot;To
  add items to cart &quot;.</li>
  <li class="text"><strong>To checkout</strong> &#8212; Users can do everything except
    checkout (and, of course, view their account/order status.) To use this option,
    make sure the Site &gt; Settings &gt; Catalog &gt; &quot;Require
    users to login? &quot; option is set to &quot;None&quot;, and then go to
    Site &gt; Settings and select the &quot;Checkout &amp; E-commerce&quot; tab, and at the bottom
    of the page under &quot;Other Options&quot; make sure that both the &quot;Include Account
    Signup at Checkout&quot; is checked and the &quot;Require Account in order to
    check out&quot; is also checked.</li>
</ul>
</body>
</html>
