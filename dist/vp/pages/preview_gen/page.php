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
<html>
<head></head>
<body>
<table width="75%" height="90%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"  class="text"><font face="Arial, Helvetica, sans-serif">Generating 
      preview... </font></td>
  </tr>
</table>
</body>
</html>
<?php
	SecureServerOn(false);

	require_once($cfg_base_dir."inc/functions_pdf.php");

	$cartitemid = $_SESSION['cartitemid'];
	$itemid 	= $_SESSION['itemid'];
	$a_prefill 	= cart_get_imprint($cartitemid,true);
	
	$site_id = $_SESSION['site'];
	


	
	// MAIN PROGRAM			***************
	$sql = "SELECT Name,Template FROM Items WHERE ID='$itemid' AND SiteID='$_SESSION[site]'";
	$nResult = dbq($sql);
	if ( mysql_num_rows($nResult) == 0 ) {  
		print("Error. No template found."); exit;
	}
	$aItem = mysql_fetch_assoc($nResult);


	/* Make the PDF */
	// We need a PDF proof, raster proof, and PDF press file
	$aItem['Template'] = utf8ToISO_8859_1($aItem['Template']);
	$buf = pdf_create($aItem['Template'],$a_prefill);
	$pressbuf = pdf_create($aItem['Template'],$a_prefill,"press");
	
	function getmicrotime(){ 
		list($usec, $sec) = explode(" ",microtime()); 
		return ((float)$usec + (float)$sec); 
	} 
	
	// Output generated image
	if ( ($scale = (430 / $pdf_vars['pagewidth'])*100) > 180 ) { $scale = 180; }
	
	$starttime = getmicrotime();
	$img = pdf_rasterize( $buf, $scale ); 
	$endtime = getmicrotime();
	$totalrendertime = $endtime - $starttime;
				
	$File = new File;
	$img_file = "_cartpreviews/" . $cartitemid . "_preview_raster.jpg";
	if ( file_exists($img_file) ) { unlink($img_file) ; }
	if(!$File->write_file( $img_file, $img )) { $error = 1; }

	$pdf_file = "_cartpreviews/" . $cartitemid . "_preview_pdf.pdf";
	if ( file_exists($pdf_file) ) { unlink($pdf_file) ; } 		// Make sure it's not already here
	if(!$File->write_file( $pdf_file, $buf )) { $error = 1; }

	$pdf_file2 = "_cartpreviews/" . $cartitemid . "_press_pdf.pdf";
	if ( file_exists($pdf_file2) ) { unlink($pdf_file2) ; } 	// Make sure it's not already here
	if(!$File->write_file( $pdf_file2, $pressbuf )) { $error = 1; }
	
	$sql = "INSERT LOW_PRIORITY INTO ProcessTimeLog  SET TimeStamp='" . time() . "', ItemID='$_SESSION[cartitemid]', Type='Render PDF & JPEG', ProcessTime='$totalrendertime'";	
	$nInsert = dbq($sql);
		
	
	
	$a_template = xml_get_tree($aItem['Template']);
	$_SESSION['item_width'] = $a_template[0]['attributes']['PAGEWIDTH']/72;
	$_SESSION['height'] = $a_template[0]['attributes']['PAGEHEIGHT']/72;	
	

?>

<script language="JavaScript" type="text/JavaScript">
document.location='vp.php?os_page=preview&site=<?php print($_SESSION['site']); ?>&ossid=<?php print($_SESSION['ossid']); ?>'
</script>

<?php exit(); ?>
