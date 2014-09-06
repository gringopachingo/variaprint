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
<title>Help: Setting up a template</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body>
<p class="titlebold">Setting up a template</p>
<p class="text">Here's an overview that will get you started using and understanding
the template editor in just a few minutes.</p>
<p class="text"><em>Note: Make sure you choose &quot;Save..&quot; from
  the file menu before you close the template window, otherwise YOU WILL LOSE
ANY UNSAVED CHANGES.</em></p>
<p class="text"><strong>For static elements / preprinted stock (otherwise skip
  to &quot;Adding variable
elements...&quot;):</strong><br>To get started you'll need to create a PDF or JPEG file (PNG
  and TIFF can also be used) of all the static information for the item a buyer
would be purchasing.</p>
<blockquote>
  <p class="text"><strong>1)</strong> Open the template editor for an item you
    have created (see creating items)
    by selecting &quot;Template&quot; from the &quot;Edit&quot; menu of the item
    in the list under the &quot;Items&quot; tab of the manager.<br>
    Note: You may need to go to a second page if there are more than one page of
  items if you've added more than 10 items.</p>
  <p class="text"><strong>2) </strong>Select &quot;File&gt;Document Properties&quot; once the
    template editor loads.</p>
  <p class="text">  <strong>3)</strong> Click on &quot;Choose...&quot; next to &quot;Image 1&quot; in the &quot;Background
    Images&quot; section of the palette and select the image (you will need to upload
    your image(s) if you haven't already) to include as the background of the template.
    You can set the &quot;Tracing Layer Opacity&quot; to &lt;100 if you don't want
    it to show the full darkness of the background image in the template editor.<br>
    Note: Changing the &quot;Tracing Layer Opacity&quot; doesn't affect the image
    preview that the buyer sees or the image included in the final press file that
    is downloaded or imposed for printing.</p>
  <p class="text">    <strong>4)</strong> You can select a second image if you
    want to use a different image for the background in the final press file.
    This can be used for preprinted
      stock or
      if a plate for one or more of the separations is already made and, of course,
      static.</p>
</blockquote>
<p class="text"><strong>Adding variable elements to the template:<br>
</strong>There are two types of variable elements in a template: graphics
  and text fields. Both are contained within a box that can be sized to the desired
  height and width
and rotated 360º.</p>
<blockquote>
  <p class="text"><strong>1)</strong>	To add a variable text field to the template, click on
    the &quot;A&quot; tool
    in the tools palette at the top left of the window and drag out an area where
    you want to place the text. Add multiple fields by clicking on the &quot;+&quot; button
    at the top of the box. Selected fields can be removed by clicking on the &quot;-&quot; button.</p>
  <p class="text"> &#8226; To edit the properties of a box or field, select the item
      and select the appropriate options in the properties palette (towards the
      bottom of the
      window.) Click &quot;Advanced...&quot; on
      the properties palette to edit some advanced formatting features.</p>
  <p class="text"> &#8226; A field's text formatting is set by default to use the format
      applied to the box. To set unique text formatting for a field, select the
      field that
      you want
      to be unique and deselect the &quot;Use box format&quot; checkbox in the
      properties palette. Advanced field features are unique for each field whether
      a field
      uses the box's text formatting or not.<br>
    To edit the type of form object that is displayed on the input screen when
      the buyer is personalizing the item, select the field and choose an option
      from &quot;Input
      Type&quot; (on the left side of the properties palette) and click on &quot;Settings...&quot; to
      set specific options like the items listed in a pulldown menu, or the maximum
      number of characters allowed in a field.</p>
  <p class="text"><strong>2)</strong> To add a variable graphic field,
      select the picture icon (the tree) in the tools palette and drag out an
    area where you want to place the graphic.
      Graphics
      can be scaled to fit into the box in 4 different ways:</p>
  <p class="text">&#8226;&nbsp;Use graphic's original size &#8212; uses the
      graphic as is; <br>
&#8226; Scale to box width &#8212; scales graphic to the width of the box; <br>
&#8226; Scale to box height &#8212; scales graphic to the box's height; <br>
&#8226; Scale to fit &#8212; scales proportionally to the box's width or height
          so that none of the graphic hangs out of the box.</p>
  <p class="text">To edit the list of graphics
  that buyers can choose from, double click on graphic or select the graphic
    and click on &quot;Edit Graphic Selection List...&quot; in
        the properties palette.</p>
</blockquote>
<p class="text"><strong>To preview a template with real data:<br>
</strong>Choose &quot;Preview Template...&quot; from the &quot;File&quot; menu and enter
  your data and click on &quot;Preview&quot;. The information you enter will
be saved.</p>
<p class="text">Se also <a href="items-templateshow.php">How templates work</a>, <a href="items-templatesgraphics.php">Image
    formats for templates</a>, <a href="items-customfonts.php">Using custom fonts
    with templates</a>, and <a href="#">Changing buyer input field options</a></p>
<p class="text">&nbsp;</p>
<p class="text">&nbsp;</p>
</body>
</html>
