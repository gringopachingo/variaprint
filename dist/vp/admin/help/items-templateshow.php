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
<title>Help: How templates work</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body>
<p class="titlebold">How templates work</p>
<p class="text">VariaPrint&#8482; includes a powerful XML-based templating system for
  handling a wide-range of on-the-fly custom document creation needs. </p>
<p class="text">Templates
    are simply a layout definition that includes
    formatting information and rules on how to handle  data
      that
      a
      buyer
      enters. Information entered by the buyer is put over an optional background
    image. There can be a different background images selected for the proof
  file and the press file. See <a href="items-templatesgraphics.php" target="mainFrame">Graphic
formats for templates</a> for more information. </p>
<p class="text">PDF documents are created from the template definition and data
  that a buyer enters. The buyer will see a rendered JPEG image of the document's
  page on the proof page and optionally a PDF proof. </p>
<p class="text">To create or edit 
    a template,
    use
  the VariaPrint&#8482; template editor by selecting Template... from the Edit
  menu of an item. </p>
<p class="text">Template features partially include:</p>
<ul>
  <li class="text"> Variable graphics &#8212; Use  PDF, JPEG, or TIFF formats and
      choose a scaling method: leave as-is, fit to height, fit to width, fit
  within box. See <a href="items-templatesgraphics.php" target="mainFrame">Graphic
  formats for templates</a> for more information.</li>
  <li class="text"> Auto-wrapped paragraph text</li>
  <li class="text">Columns &#8212; set number of columns, gutter width,
    and balancing </li>
  <li class="text">Vertical aligment &#8212; top, middle, or bottom</li>
  <li class="text">Force text to fit &#8212; by tracking, size, and horizontal scale </li>
  <li class="text">Create any
      custom CMYK or spot color</li>
  <li class="text">Use any font</li>
  <li class="text">Prefix, suffix text &#8212; optionally omit prefix or suffix if
    field is not filled by buyer</li>
  <li class="text">In-line fields</li>
  <li class="text">Additional leading before or after a field</li>
  <li class="text">Pre-fill data for buyer input form</li>
</ul>
<p>&nbsp;</p>
</body>
</html>
