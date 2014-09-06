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
<title>Help: Setting e-commerce preferences</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body>
<p class="titlebold">Setting e-commerce preferences</p>
<p class="text">The  options for invoices and payments can be customized for
  a wide variety of needs. To edit these settings go to Site &gt; Settings &gt; Checkout
  &amp; E-commerce.</p>
<p class="text"><strong class="text">Invoice</strong></p>
<ul>
  <li class="text">The company name and address to print
    on the buyer's invoice can be edited
    here. If the order site is for in-house purchasing, we recommend just entering
  the company name and any appropriate message.</li>
  <li class="text">General message on invoice &#8212; this can be anything such as
    &quot;Thank you for your order&quot;.</li>
  <li class="text">Payment type messages &#8212; you may enter a message to appear
    on the invoice as well as checkout page based on what payment type the buyer
    selects. E.g. if a buyer is paying by credit card, you may enter a message
    that says &quot;This charge will appear on your card from Acme Corp.&quot; or
    if paying by check you may enter a note that says &quot;Please remit your
    payment to address above.&quot; etc.</li>
</ul>
<p class="text"><strong>Payment types<br>
</strong>You may choose not to accept any payment (for in-house) or select
from
and configure the available payment types for your specific needs. </p>
<p class="text">Available
  payment
  types
  include:</p>
<ul>
  <li class="text"> <strong>Credit card</strong> &#8212; Credit card
    numbers  are sent securely (all other order data is secure as well) and
    may be charged manually or automatically through VeriSign&#8482; PayFlow
    Pro (VeriSign&#8482; PayFlow
    Pro gateway account required.)</li>
  <li class="text"><strong>Check or Money Order</strong> &#8212; Enter custom instructions to
    notify buyers where to send their check or money order, whether or not their
    order will be processed before their check is received, and any other instructions
    your buyers need.</li>
  <li class="text"><strong>Purchase order</strong> &#8212; Built into the VariaPrint&#8482;
    system is a simple purchase order feature that allows buyers to apply for
    a PO account (buyers
    must have a user account on the order site to do so) and allows
  a manager to approve PO account requests. Orders using a
    PO account will put the PO  account billing information onto the invoice.
    You may choose not to allow buyers to place an order on a pending PO account
    in which case buyers will  need to use an alternate form of payment for
    their orders until the PO account is approved.</li>
  <li class="text"><strong>PayPal</strong> &#8212; This is a great way to accept a
    wide range of payments with virtually no hassle. Simply enter the merchant
    PayPal account information in the VariaPrint manager and buyers using this
    payment option will get a button on their invoice to make their payment.</li>
</ul>
<p class="text"><strong>Other Options</strong><br>
Several other general e-commerce options are also available:</p>
<ol>
  <li class="text">Charge Tax &#8212; set up states to charge tax for.</li>
  <li class="text">Require User Account &#8212; set whether or not to require buyer
    to have an account in order to make purchase.</li>
  <li class="text">Shipping Address and Method / Cost &#8212; choose whether or not
    to show the shipping address and shipping method selection. </li>
  <li class="text">Order Approval Manager &#8212; set up all  managers for buyers to
    choose from. See <a href="orders-approve.php" target="mainFrame">Approving
    orders</a> and <a href="users-notification.php" target="mainFrame">Setting
    up manager notification options</a> and for more information.</li>
  <li class="text">Include Special Instructions &#8212; select whether or not to include
    a section for buyers to enter special instructions for their order when checking
    out.</li>
</ol>
</body>
</html>
