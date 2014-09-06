<?

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


function iface_payment_pulldown($prpty, $action="", $sel="", $name="billing_type") {
	
	if ($prpty['DontRequirePayment'] != "checked" ) { 
		$paytype['BillingAcceptCC']['val'] = "Credit Card";
		$paytype['BillingAcceptCC']['key'] = "cc";
		$paytype['BillingAcceptChecks']['val'] = "Check";
		$paytype['BillingAcceptChecks']['key'] = "check";
		$paytype['BillingAcceptPO']['val'] = "Purchase Order";
		$paytype['BillingAcceptPO']['key'] = "po";
		$paytype['BillingAcceptPayPal']['val'] = "PayPal";
		$paytype['BillingAcceptPayPal']['key'] = "pp";
	
		$pd = "<!-- start payment type pulldown //-->\n<select name=\"$name\" class=\"text\" $action>";
		foreach ( $paytype as $key => $val) {
			if ( $prpty[$key] == "checked") { 	
				if ( $val['key'] == $sel) { $selected = " selected"; } else { $selected = " "; }
				$pd .=	"<option $selected value=\"" . $val['key'] . "\">" . $val['val'] . "</option>";
			}	
		}
		$pd .=	"</select>\n<!-- end payment type pulldown//-->";
	} else {
		$pd = NULL;
	}
	
	return $pd;
}

function iface_billing_card_type_pulldown($sel) {
	global $a_site_settings;
	$billing_card_type['Visa'] = "Visa";
	$billing_card_type['MC'] = "Master Card";
	$billing_card_type['AMEX'] = "American Express";
	$billing_card_type['DISC'] = "Discover Card";
	$billing_card_type['DC'] = "Diner's Club";
	$billing_card_type['JCB'] = "JCB";

	$pd = "<!-- start card type pulldown //-->\n<select name=\"billing_card_type\" class=\"text\" $action>";

	foreach ( $billing_card_type as $key => $val) {
		$prefkey = "BillingCCsAccepted_" . $key;
		if ( $a_site_settings[$prefkey] == "checked") { 	
			if ( $key == $sel) { $selected = " selected"; } else { $selected = " "; }
			$pd .=	"<option $selected value=\"" . $key . "\">" . $val . "</option>";
		}	
	}
	$pd .=	"</select>\n<!-- end card type pulldown//-->";
	return $pd;

}


function iface_add_drop_shadow($incObj,$bgColor) {
	$tObj = "
<table width=\"10\" height=\"10\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
  <tr><td>
	<table width=\"10\" height=\"10\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" bgcolor=\"#000000\">
<tr><td bgcolor=\"#CCCCCC\">$incObj</td></tr>
    </table>
	</td><td valign=\"top\" bgcolor=\"#333333\"> 
      <table width=\"2\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"$bgColor\">
        <tr><td><img src=\"images/global/spacer.gif\" width=\"2\" height=\"3\"></td></tr>
      </table></td>
  </tr>
  <tr bgcolor=\"#333333\"><td> 
      <table width=\"2\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"$bgColor\">
        <tr><td><img src=\"images/global/spacer.gif\" width=\"3\" height=\"2\"></td></tr>
      </table></td>
    <td><img src=\"images/global/spacer.gif\" width=\"2\" height=\"2\"></td>
  </tr>
</table>

	";
	
	return $tObj;
}

function iface_pulldown_menu($a,$prefill) {
	if ( is_array($a['pulldown']['children']) ) {
		while ( list(,$val) = each($a['pulldown']['children']) ) {
			$name = $val[attributes][NAME]; $value = $val[attributes][VALUE];
			if ($value == $prefill) { $sel = "selected"; } else { $sel = "" ; }
			$options .= "<option value=\"$value\" $sel>$name</option>\n";
		}
		$field = "<select name=\"field_" . $a['attributes']['ID'] . "\">$options</select>";
		return $field ;
	} else {
		return false;
	}
}

function iface_label_menu($a,$type,$id) {
	$label = "<select onChange=\"setFieldLabel(this, ". $id . ");\">";
	while ( list(,$v) = each($a) ) {
		$label .= "<option value=\"" . $v['attributes']['VALUE'] . "\">" . $v['attributes']['NAME'] . "</option>";
	}
	$label .= "</select>";
	return $label;
}

function iface_inline_fields($a,$aPrefill) { 
	$len = count($a); $counter = 0;
	if ( is_array($a) ) { 
		while (list($k,$v) = each($a) ) {
			$id =  $v['attributes']['ID']; $name = $v['attributes']['NAME'];
			++$counter; 
			if ( $len > $counter ) { $r['label'] .= $name . ", "; } else {  $r['label'] .= "and " . $name; }
			$r['fields'] .= "<input style=\"width:100\" type=\"text\" value=\"$aPrefill[$id]\" name=\"field_" .$id . "\">&nbsp;&nbsp;";
		}
	}
	return $r;
}

function iface_make_input_row($label, $field, $help, $alert=false) {
	$row = "<tr><td class=\"text\" valign=\"top\">$label&nbsp;</td><td width=\"350\" class=\"text\" >$field </td><td valign=\"top\"> $help </td><td valign=\"top\"> $alert </td></tr>";
	return $row;
}

// COMMAS DON'T WORK in JS AND  URLENCODING screws everything up.

function iface_make_cart_row($name, $qtycost, $cartid, $itemid, $os_sid,$approved=true) {
	$sql = "SELECT Custom FROM Items WHERE ID='$itemid'";
	$r = dbq($sql);
	$a = mysql_fetch_assoc($r);
	if (!$approved) {
		$approval = " &nbsp; <a href=\"vp.php?site=$_SESSION[site]&os_action=edititem&itemid=$itemid&cartitemid=$cartid&os_sid=$_SESSION[os_sid]\" class=\"text\">approve</a> &raquo;";
		//onClick=\"popupWin('approve_item.php?cart_id=$cartid','','height=300,width=400,centered=1')\" 
	} 		
	$row = "
		<table cellpadding=6 cellspacing=0 border=0 width=\"596\">
		<tr><td width=\"1\" align=\"left\">";
		
	if ($a["Custom"]!="N") {
		$row .=	"<a href=\"javascript:;\" onClick=\"popupWin('itempreview.php?site=$_SESSION[site]&cartitemid=$cartid&itemid=$itemid&name=" 
				. urlencode(addslashes($name)) . "&os_sid=$_SESSION[os_sid]','view','width=600,height=450,centered=1')\" title=\"View preview of item &quot;$name&quot;.\">
				<img src=\"_sites/$_SESSION[site]/ifaceimg/icon-preview.gif\" border=\"0\"></a>
		";
	} else {
		$row .= "<img width=\"17\" src=\"images/spacer.gif\" border=\"0\">";
	}
	
	$row .= "</td>
			<td width=\"1\"><a href=\"vp.php?site=$_SESSION[site]&os_action=deletefromcart&deleteitemid=$cartid&os_sid=$_SESSION[os_sid]\"" . 
				" onclick=\"return confirmAction(this, 'This will delete all the information you entered for this item.')\">" .
				" <img src=\"_sites/$_SESSION[site]/ifaceimg/icon-delete.gif\" border=\"0\" title=\"Remove item &quot;$name&quot; from cart.\"></a></td>
			<td width=\"360\" class=\"text\">$name &nbsp; &nbsp; 
			<a href=\"vp.php?site=$_SESSION[site]&os_action=edititem&itemid=$itemid&cartitemid=$cartid&os_sid=$_SESSION[os_sid]\" class=\"text\">edit</a> &raquo; $approval</td>
			<td align=\"left\">$qtycost</td>
		</tr>
		</table>";
	return $row;
}

function MakePageStructure($os_sidebar, $content, $top="") { 
	$page = "
		<table width=\"768\"  border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
		  <tr> 
		  	<td><img src=\"images/spacer.gif\" height=\"23\"></td>
			<td width=\"600\" valign=\"bottom\">$top</td>
		  </tr>
		  <tr> 
		  	<td width=\"167\" valign=\"top\">$os_sidebar</td>
			<td width=\"600\" valign=\"top\">$content</td>
		  </tr>
		</table>
	";
	return $page;
}

function iface_make_cart_sidebar($title,$os_sid) {
	$sql = "SELECT ID,ItemID FROM Cart WHERE SessionID='$os_sid' AND SiteID='$_SESSION[site]'";
	$nResult = dbq($sql); 
	
	
	
	if ( mysql_num_rows($nResult) > 0) {
		if ($_SESSION[logged_in] != 1) { 
			$save = "&nbsp;<a href=\"vp.php?site=$_SESSION[site]&os_sid=$_SESSION[os_sid]&os_page=login\">save</a> &raquo; "; }
		
		$text .= "
		<!-- START CART SIDEBAR //-->
		<table cellpadding=0 cellspacing=0 border=0>";
		
		while ( $aItem = mysql_fetch_assoc($nResult) ) {
			$sql = "SELECT Name,Custom FROM Items WHERE ID='" . $aItem['ItemID'] . "'";
			$nResult2 = dbq($sql);
			$aItemName = mysql_fetch_assoc($nResult2);
			//<td valign=\"top\" class=\"text\">&bull;&nbsp;</td>
			$text .= "<tr><td valign=\"top\" class=\"text\">" . $aItemName['Name'] . 
			"<br><a href=\"vp.php?site=$_SESSION[site]&os_action=deletefromcart&deleteitemid=$aItem[ID]&os_sid=$_SESSION[os_sid]\" onclick=\"return confirmAction(this, 'This will delete all the information you entered.')\">delete&nbsp;&raquo;</a>&nbsp;&nbsp;";
			if ($aItemName["Custom"] != "N") {
				$text .= "<a href=\"vp.php?site=$_SESSION[site]&os_action=edititem&cartitemid=$aItem[ID]&itemid=$aItem[ItemID]&os_sid=$_SESSION[os_sid]\">edit&nbsp;&raquo;</a>";
			}	
			$text .= "<br>
			<img src=\"images/spacer.gif\" height=\"10\" width=\"1\"></td></tr>";
		}
		$text .= "</table>";
		
		// iface_dottedline() .
		$os_sidebar = "
		<table cellpadding=0 cellspacing=0 border=0><tr>
			<td width=\"16\"><img src=\"images/spacer.gif\" width=\"16\" height=\"1\"></td>
			<td width=\"130\" class=\"text\"><br><br>" . 
			iface_make_box("<table cellpadding=0 cellspacing=8 border=0 width=\"100%\"><tr><td class=\"text\">
				<span class=\"subtitle\">$title</span>
				$save&nbsp;
				<a href=\"vp.php?site=$_SESSION[site]&os_action=gotocart&os_sid=$_SESSION[os_sid]\">view</a> &raquo;
				</td></tr></table>". iface_dottedline() ."
				<table cellpadding=0 cellspacing=8 border=0><tr><td class=\"text\">
					<a href=\"vp.php?site=$_SESSION[site]&os_action=gotocart&os_sid=$_SESSION[os_sid]\">
					<input type=\"button\" onclick=\"document.location = ".
						"'vp.php?os_action=gotocart&site=$_SESSION[site]&os_sid=$_SESSION[os_sid]'\" class=\"button\" ".
						"value=\"Checkout &raquo;\"></a>
<br><br>
<div class=\"text\">$text </div>				</td></tr></table>
			", 130) . "
			</td>
		</tr></table>
		<!-- END CART SIDEBAR //-->
		";
		//, "",1,"#7C775E","#CFCFCF"
		return $os_sidebar;
	}	
}

function iface_make_sidebar($title,$text) {
	$os_sidebar = "
	<table cellpadding=0 cellspacing=0 border=0><tr>
		<td width=\"16\"><img src=\"images/spacer.gif\" width=\"16\" height=\"1\"></td>
		<td width=\"130\"><div class=\"title\">$title</div> <div class=\"sidetext\">$text </div></td>
	</tr></table>";
	return $os_sidebar;	
}

function iface_dottedline($width = "100%") {
	$line = "
			<table width=\"$width\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
              <tr>
                <td background=\"_sites/$_SESSION[site]/ifaceimg/dottedline.gif\"><img src=\"images/spacer.gif\" width=\"1\" height=\"1\"></td>
              </tr>
            </table>
		";
	return $line;
}

function MakeMenuBar($menubarcolor, $bevel="N",$MenuHomeName="", $MenuCatalogName="", $MenuAccountName="", $MenuOrderStatusName="", $cm_text1="", $cm_link1="", $cm_text2="", $cm_link2="") {
	global $os_sid, $a_site_settings;
	
	if (trim($MenuHomeName) == "") $MenuHomeName = "Home";
	if (trim($MenuCatalogName) == "") $MenuCatalogName = "Catalog";
	if (trim($MenuAccountName) == "") $MenuAccountName = "My Account";
	if (trim($MenuOrderStatusName) == "") $MenuOrderStatusName = "Order Status";
	
	
	
	if ( $_SESSION[os_page] != "login") {
		if ($_SESSION['logged_in'] == 1) {
			$login_text = "<a href=\"vp.php?site=$_SESSION[site]&os_action=logout&os_page_afterlogin=$_SESSION[os_page]&os_sid=$_SESSION[os_sid]\" class=\"menu\">Logout &quot;$_SESSION[username]&quot;</a>";
		} else {
			$login_text = "<a href=\"vp.php?site=$_SESSION[site]&os_page=login&os_page_afterlogin=$_SESSION[os_page]&os_sid=$_SESSION[os_sid]\" class=\"menu\">Login</a>";
		}
	}
	
	if ( $bevel != "Y") { 
		$menubarhilite	= $menubarcolor ;
		$menubarshadow	= $menubarcolor ;
	} else {
		$menubarhilite	= ModifyColor($menubarcolor,80);
		$menubarshadow	= ModifyColor($menubarcolor,-60);
	}
	
	if (trim($cm_text1) != "" && trim($cm_link1) != "") {
		$cm1 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$cm_link1\" class=\"menu\">". str_replace(" ","&nbsp;",$cm_text1) ."</a>";
	}
	if (trim($cm_text2) != "" && trim($cm_link2) != "") {
		$cm2 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$cm_link2\" class=\"menu\">". str_replace(" ","&nbsp;",$cm_text2) ."</a>";
	}
	
	if ($a_site_settings['HomePageStyle'] != "NoPage") {
		$homelink = "<a href=\"vp.php?site=$_SESSION[site]&os_page=home&os_sid=$_SESSION[os_sid]\" class=\"menu\">".str_replace(" ","&nbsp;",$MenuHomeName)."<a>";
	}
	$menubar = "
	<table width=\"100%\" height=\"22\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
	  <tr> 
		<td height=1 bgcolor=\"$menubarhilite\"><img src=\"images/spacer.gif\" width=\"1\" height=\"2\"></td>
	  </tr>
	  <tr> 
		<td valign=\"middle\"  bgcolor=\"$menubarcolor\" class=\"menu\"> 
		  <table width=\"768\" height=\"16\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
			<tr class=\"menu\">
			  <td width=\"16\"><img src=\"images/spacer.gif\" width=\"16\" height=\"1\"></td>
			  <td width=\"150\" class=\"menu\">$homelink&nbsp;</td>
			  <td width=\"459\" valign=\"middle\"> 
				  <a href=\"vp.php?site=$_SESSION[site]&os_page=catalog&os_sid=$_SESSION[os_sid]\" class=\"menu\">"
				  .str_replace(" ","&nbsp;",$MenuCatalogName).
				  "</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . 
				  "<a class=\"menu\" href=\"vp.php?site=$_SESSION[site]&os_page=account&os_sid=$_SESSION[os_sid]\">".str_replace(" ","&nbsp;",$MenuAccountName)."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . 
				  "<a href=\"vp.php?site=$_SESSION[site]&os_page=account&accounttab=0&os_sid=$_SESSION[os_sid]\" class=\"menu\">".str_replace(" ","&nbsp;",$MenuOrderStatusName)."</a>". $cm1 . $cm2 .
				 
				  
			  "</td>
			  <td width=\"141\" align=\"right\" valign=\"middle\" class=\"menu\">$login_text</td>
			</tr>
		  </table> 
		</td>
	  </tr>
	  <tr>
		<td height=1 bgcolor=\"$menubarshadow\"><img src=\"images/spacer.gif\" width=\"1\" height=\"2\"></td>
	  </tr>
	</table>
	";
	
//	exit($menubar);
	return $menubar;
}


function MakeMastHead ($logosrc, $bannerbgcolor) {
	if (file_exists($logosrc)) {
		$img = "<img name=\"logo\" src=\"$logosrc\" alt=\"logo\">"; 
		$height = "";
	} else { 
		global $a_site_settings; 
		$img = "<table cellpadding=8 cellspacing=0 border=0><tr><td><img src=\"images/spacer.gif\" width=\"142\" height=\"1\"></td><td  class=\"title\">" 
		. $a_site_settings['SiteTitle'] . "</td></tr></table>" ; 
		$height = "height=\"30\"";
	}
	$masthead = "
	<table width=\"100%\" $height  border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$bannerbgcolor\">
	  <tr>
		<td valign=\"top\">$img</td>
	  </tr>
	</table>
	";
	return $masthead; /**/
}

function iface_make_box($content="",$width=600,$height="",$top=1,$bordercolor="#7C775E",$bgcolor="#eeeeee") {
	if ($height != "")  { $height = " height=\"$height\" "; }
    $insidewidth = $width-2;
	//<!-- START BOX //-->
	$box = "<table width=\"$width\" $height border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
	if ($top) {
		 $box .= "
	    <tr>
          <td bgcolor=\"$bordercolor\"><img src=\"images/spacer.gif\" width=\"1\" height=\"1\"></td>
          <td bgcolor=\"$bordercolor\"><img src=\"images/spacer.gif\" width=\"$insidewidth\" height=\"1\" bgcolor=\"$bgcolor\"></td>
          <td bgcolor=\"$bordercolor\"><img src=\"images/spacer.gif\" width=\"1\" height=\"1\"></td>
        </tr>
		";
	}
	
	$bottomlinewidth = $width-19 ;
		 $box .= "
        <tr> 
          <td width=\"1\" bgcolor=\"$bordercolor\"><img src=\"images/spacer.gif\" width=\"1\" $height></td>
          <td width=\"$insidewidth\" valign=\"top\"  $height bgcolor=\"$bgcolor\"> 
				$content
			</td>
          <td width=\"1\" align=\"right\" bgcolor=\"$bordercolor\"  $height><img src=\"images/spacer.gif\" width=\"1\"></td>
        </tr>
      </table>
      <table width=\"$width\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr>
          <td width=\"18\"><img src=\"_sites/$_SESSION[site]/ifaceimg/corner.gif\" width=\"18\" height=\"18\"></td>
          <td width=\"$bottomlinewidth\" valign=\"bottom\" bgcolor=\"$bgcolor\"><table width=\"$bottomlinewidth\" cellpadding=0 cellspacing=0 border=0 height=1><tr><td bgcolor=\"$bordercolor\"><img src=\"images/spacer.gif\" width=\"$bottomlinewidth\" height=\"1\"></td></tr></table></td>
          <td width=\"1\" align=\"right\"><img src=\"_sites/$_SESSION[site]/ifaceimg/dot-gray.gif\" width=\"1\" height=\"18\"></td>
        </tr>
      </table>
	";
	  return $box;
}

/*<img src=\"_sites/$_SESSION[site]/ifaceimg/dot-gray.gif\" width=\"$bottomlinewidth\" height=\"1\">
				<!--  <table width=\"$bottomlinewidth\" cellpadding=0 cellspacing=0 border=0 height=1><tr><td bgcolor=\"$bordercolor\">
					<img src=\"images/spacer.gif\" width=\"$bottomlinewidth\" height=\"1\">
				  </td></tr></table> //-->
*/

function iface_make_tabs($atabs, $ontab, $varname="tab", $width=600, $page="") {
	global $os_sid;
	$tabs = "
	<!-- START TABS //-->
    <table width=\"$width\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" height=\"5\"><tr>";
	$passone = true;
	
	if ( is_array($atabs) ) {
		while ( list($k, $v) = each($atabs) ) {
			if ( $k == $ontab) { 
				$tabio = "on"; $label = ereg_replace(" ", "&nbsp;", $v); 
			} else { 
				if ($page != "") {
					$page_link = "os_page=$page&";
				}
				$tabio = "off";  $label = "<a href=\"vp.php?".$page_link."site=$_SESSION[site]&$varname=$k&os_sid=$_SESSION[os_sid]\" class=\"taboff\">" 
				. ereg_replace(" ", "&nbsp;", $v) . "</a>";
			}
			
			if ($passone) { 
				$end = "_most"; $passone = false; 
			} else { 
				$end = ""; 
			}
			
			$tabs .= "<td width=\"1\" height=\"5\"><img src=\"_sites/$_SESSION[site]/ifaceimg/tab-" . $tabio . "_left$end.gif\"></td><td  nowrap background=\"_sites/$_SESSION[site]/ifaceimg/tab-" . $tabio . "_middle.gif\" class=\"tab" .$tabio . "\">" . $label . "</td><td width=\"1\"><img src=\"_sites/$_SESSION[site]/ifaceimg/tab-" . $tabio . "_right.gif\"></td>";
		}
	}
	
	$tabs .= "<td width=\"99%\" background=\"_sites/$_SESSION[site]/ifaceimg/tab-extender.gif\" align=\"right\"><img src=\"_sites/$_SESSION[site]/ifaceimg/tab-right_end.gif\"></td></tr></table>";//<!-- END TABS //-->
	return $tabs;

}
?>
