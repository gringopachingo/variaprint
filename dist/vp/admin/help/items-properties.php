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
<p class="titlebold">Editing an item's properties</p>
<p class="text">To edit an item's properties, select Items to view the item list
  and choose Properties... from the Edit menu of the item you want to edit.</p>
<p class="text"><strong>Name and Description</strong><br>
Change the name of an item,  description and the preview images of the item.  </p>
<p class="text">If a description or  detail view image are entered/selected,
  clicking on the item in the catalog on the order site will display a detail
  window with the
    description and/or detail image. If a description is entered and no detail
  image is selected, the selected catalog image will be used  if it exists, in
  the detail window.</p>
<p class="text">Catalog and detail images must be in a format that can be displayed
  by a browser as no processing is done on these images after uploading to an
  order site. RGB JPEGs (CMYK will not work) and GIF images are recommended.</p>
<p class="text"><em>Note: Although GIF images with less than 128 colors will
    work fine in all browsers, they will not work as a template image. Any GIF
    image
  used in a template &#8212; either as a background or variable graphic &#8212; must be 128
  colors or more. </em>See <a href="items-templatesgraphics.php" target="mainFrame">Graphic
  formats for templates</a> for more information.</p>
<p class="text"><strong>Group with...<br>
</strong>Select which group (catalog tab) the item will appear
in on the order site. See <a href="items-groups.php" target="mainFrame">Grouping
items in the catalog</a> for more information.</p>
<p class="text"><strong>Imposition</strong><br>
Select the imposition style to use for the item. This determines what imposition
  style the item will be listed with on the &quot;Create Impositions&quot; screen when
  an order is placed for this item. You must make sure that size of the item
  and the item size on the imposition match or there will be mismatched results
  in the imposition document. See <a href="items-stylesimposition.php">Setting
  up imposition styles</a> for more information.</p>
<p class="text"><strong>Approval Preferences<br>
</strong>You may choose whether or not to show the buyer a PDF proof on the proof
page
of the order site for the selected item (defaults to not showing a PDF proof),
and whether or not to require the buyer to enter their initials to indicate they
have reviewed the proof (defaults to requiring approval.) The proof approval
text
may
be
edited
under Site &gt; Settings &gt; Page Text.</p>
<p class="text"><strong>Pricing</strong><br>
Pricing may be set up on an individual basis for each item, or may be based on
  a prcing style. The price for all items that use
  a particular pricing style will be updated when the pricing style is updated.
  See <a href="items-stylespricing.php" target="mainFrame">Setting up pricing styles</a> for more information</p>
<p class="text"><strong>Shipping Weight</strong><br>
  Enter the weight for 1000 individual, ready-to-ship, items. This is used to
    calculate the shipping charge based on the settings in the shipping profile.
    See <a href="items-stylesshipping.php">Setting
    up a shipping cost style</a> for more information.</p>
<p class="text"><strong>Supplier</strong><br>
A supplier may be selected for imposing/printing an item. Suppliers must be given
  access to downloading impositions in order to be selected as a supplier&#8212;see
  <a href="users-vendor.php" target="mainFrame"><span class="text">Setting up
  vendor manager</span>s</a> for more information. Once selected, a supplier
  will be able to view and impose all the ordered items  that they
  are
  the supplier for. This property has no effect, currently, for non-custom items. </p>
</body>
</html>
