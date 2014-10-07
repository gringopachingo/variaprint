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
<title>Help: Graphic formats for templates</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body>
<p class="titlebold">Graphic formats and prepping for templates</p>
<p class="text">There are 5 formats that can be used as background images and
variable graphics in the template editor:</p>
<blockquote>
  <p class="text">&#8226; PDF<br>
&#8226; JPEG<br>
&#8226; PNG<br>
&#8226; TIFF<br>
&#8226; and GIF</p>
</blockquote>
<p class="text"> To include vector graphics, use a PDF file. This file can be
  created through Adobe Distiller or saved from Illustrator as PDF. It must be
  1.3 (Acrobat 4)
    or earlier. If saved from Illustrator, set the options to a version 4 PDF
  and DO NOT set it to be editable in Illustrator as this adds a large amount
  of
    excess information and in some cases can be rendered incorrectly with the
  VariaPrint image processing software. </p>
<p class="text"><em> Note: To edit press files created with VariaPrint, Acrobat with
  a prepress plug-in such as PitStop works best. PDF press files created with
  VariaPrint
      generally
      don't work correctly when opened in Illustrator because of how Illustrator
      handles PDF Form Objects and embedded fonts.</em></p>
<p class="text">The preferred raster graphics
    format is a high-quality JPEG (use 10-12 compression in Photoshop) or PNG
  file. Although other formats will work,
        they are conmssiderably
        larger (TIFF) or have limitations in the color quality (GIF) and should
        not be used unless there is a specific reason.</p>
<p class="text"><em>Note: If you use a GIF image,
    you must save it with at least 128 colors even if there are less colors in
    the image. You can do this by opening
        the GIF
        file in PhotoShop and setting the color mode to RGB and then back to
  Indexed and
        choosing a color palette option that has more than 128 colors.</em></p>
</body>
</html>
