<?php

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

//	$_SESSION['require_login'] = 0;

	SecureServerOn(false);
	
	$cartitemid = $_SESSION['cartitemid'];
	$sql = "SELECT Name,Template,PDFProof,ReqApproval FROM Items WHERE ID='$_SESSION[itemid]' AND SiteID='$_SESSION[site]'";
	$r_result = dbq($sql);
	$a_item = mysql_fetch_assoc($r_result);
	$a_prefill = cart_get_imprint($cartitemid);
	$itemname = $a_item[Name];
//	$title = "Proof" ;
	
	$a_template = xml_get_tree($a_item['Template']);
	$width = $a_template[0]['attributes']['PAGEWIDTH']/72;
	$height = $a_template[0]['attributes']['PAGEHEIGHT']/72;	
	
	$title = $a_site_settings['ApprovalTitle'];
	$description = $a_site_settings['ApprovalText']; //$aSitePreview['PreviewText'];
	$os_sidebar = iface_make_sidebar($title, $description);
	if ($_SESSION["modifyitem"] != true) {
//		$os_sidebar .= iface_make_cart_sidebar("Cart",$ossid);
	}
	
	$atabs = array('0' => $itemname);

	if ($a_item[PDFProof] == "true") {
		$pdf_proof	="
			<a href=\"_cartpreviews/".$cartitemid."_preview_pdf.pdf\" target=\"_blank\">View PDF proof</a>...
			<br><br>
			<a href=\"http://www.adobe.com/products/acrobat/readstep2.html\" target=\"_blank\">Download Adobe Acrobat Reader for PDF</a>...
		";
	}
	
	
	if ($a_item[ReqApproval] != "false") {
		$approvaltext = str_replace("\n","<br>",$a_site_settings['ApprovalAgreementText']);
		$approval_text = "
						<br>
						$approvaltext<br><br>
							<table cellpadding=0 cellspacing=0 border=0><tr>
								<td nowrap class=\"text\">
									Enter your initials if you agree &nbsp;
								</td>
								<td>
									<input type=\"text\" name=\"approve_initials\" size=\"10\">
								</td>
							</tr></table>
		";
	}
	
	
	// Make sure this item is still in the cart
	$sql = "SELECT ID FROM Cart WHERE ID='$_SESSION[cartitemid]' AND SiteID='$_SESSION[site]'";
	$res = dbq($sql);
	$item_in_cart = mysql_num_rows($res);
	
	if ($item_in_cart) {
		$content	= "<img src=\"_cartpreviews/" . $_SESSION[cartitemid] . "_preview_raster.jpg?" . time() . "\">";
		$content	= iface_add_drop_shadow($content,"#eeeeee") ;
		$content	= "
				<br>
				<table cellpadding=8 cellspacing=0 border=0 width=\"590\">
					<tr>
						<td>
							
							$content
						</td>
						<td class=\"text\" valign=\"top\" width=\"150\">
							Dimensions: <br>
							$width\"(w) x $height\"(h) <br><br><br>
							

							$pdf_proof
							
						</td>
					</tr>
					<tr>
						<td class=\"text\" >
							
							$approval_text
							
							<br><br>
							<a href=\"$script_name?site=$_SESSION[site]&os_page=input&ossid=$_SESSION[ossid]\">
							<input type=\"button\" onclick=\"document.location='$script_name?os_page=input&site=$_SESSION[site]&ossid=$_SESSION[ossid]'\" class=\"button\" value=\"&laquo; Edit\"></a>&nbsp;&nbsp;
							
							<input class=\"button\" type=\"submit\" value=\"Approve &raquo;\">
							
							<br><br>
						</td>
						<td>
						</td>
					</tr>
				</table>
				<input type=\"hidden\" name=\"os_action\" value=\"approve\">
				";
	} else {
		$content = "
				<table cellpadding=8 cellspacing=0 border=0 width=\"590\">
					<tr>
						<td class=\"text\">
						This item was deleted. <a href=\"vp.php?site=$_SESSION[site]&ossid=$_SESSION[ossid]&os_page=catalog\">Go to catalog</a>.
						</td>
					</tr>
				</table>
						";
	}
	
	$tabs		= iface_make_tabs($atabs, '0', '0', '600') ;
	$content 	= iface_make_box($content,600,100,0);
	$sPage 		= MakePageStructure($os_sidebar,$content,$tabs);




?>
