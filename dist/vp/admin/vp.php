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

//set_magic_quotes_runtime(false);
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    			// Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 	// always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  	// HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                          			// HTTP/1.0


//$_SESSION['tm'] = "1";

require_once("../inc/config.php");
require_once("../inc/functions-global.php");
require_once("../inc/encrypt.php");
require_once("inc/functions.php");
require_once("inc/iface.php");
require_once("inc/session.php");


session_name("ms-sid");
session_start();
$ms_sid = session_id();

//if ( isset($a_form_vars['site']) ) 
//	$_SESSION['site'] = $a_form_vars['site'];

 
$a_legal_session_vars[] = "tab";
$a_legal_session_vars[] = "settings_tab";
$a_legal_session_vars[] = "appearance_tab";
$a_legal_session_vars[] = "action";
$a_legal_session_vars[] = "tm";
$a_legal_session_vars[] = "site";
$a_legal_session_vars[] = "sel_status";
$a_legal_session_vars[] = "page";
$a_legal_session_vars[] = "delete_item";
$a_legal_session_vars[] = "confirmed";
$a_legal_session_vars[] = "user_id";
$a_legal_session_vars[] = "sel_imposition";
$a_legal_session_vars[] = "sel_imp_vendor";
$a_legal_session_vars[] = "sel_docket_view";


foreach ( $a_legal_session_vars as $this_var) {
	if ( isset($a_form_vars[$this_var] ) && $a_form_vars[$this_var] != "") {
		$_SESSION[$this_var] = $a_form_vars[$this_var];
	}
}

//if ( !isset($_SESSION['site_name']) || $_SESSION['site_name'] == "" ) {
	$sql = "SELECT Name FROM Sites WHERE ID='$_SESSION[site]'";
	$n_result = dbq($sql);
	$a_result = mysql_fetch_assoc($n_result);
	$_SESSION['site_name'] = $a_result['Name'];
//}

$action = $_SESSION['action'];
$sel_menu = $_SESSION['sel_menu'];
$_SESSION[site] = $_SESSION['site'];

// Make sure a site is open, otherwise go to the site open page
if ($_SESSION[site] == "" && $action != "login" && $action != "logout") {
	$_SESSION['action'] = "site_open";
} 

if ( $action != "" ) {
	$actionfile =  "actions/". $action . ".php";
	if ( file_exists($actionfile) ) {
		include_once ($actionfile);
	} else {
		$content = "<font color=\"red\"><strong >Error:</strong> \"$action\" action file not found.</font>";
	}
}



// Check login status
$sql = "SELECT DateLastLogin,LastSID,Username FROM AdminUsers WHERE ID='$_SESSION[user_id]'";
$r_result = dbq($sql);
$a_result = mysql_fetch_assoc($r_result);

//if within last 60 minutes, we're logged in
$logoutinterval = 60 * 60 * 24;
if ( $a_result['DateLastLogin'] > (time() - $logoutinterval) && $a_result['LastSID'] == $ms_sid) {
	$_SESSION['logged_in'] = 1;
	$_SESSION['username'] = $a_result['Username'];
	$time = time();
	$sql = "UPDATE AdminUsers SET DateLastLogin='$time' WHERE ID='$_SESSION[user_id]'";
	dbq($sql);
	$header_content .= "\n<meta http-equiv=\"refresh\" content=\"$logoutinterval;URL=../admin/\">\n";
} else {
	$_SESSION['logged_in'] = 0;
	
//	print_r($_SESSION);
//	$_SESSION['show_alert'] = 1;
//	$_SESSION['alert_msg'] = "There was an error verifying that you are logged in.";
	
	$logged_out_reason = "";
	
	if ($a_result['DateLastLogin'] - (time() - $logoutinterval) < 0) {
		$logged_out_reason .= "time+";
	} 
	if ($ms_sid != $a_result['LastSID']) {
		$logged_out_reason .= "sid+";
	}
	header("Location: ../admin/?$logged_out_reason");
}



// Check to see if we have permission to edit this site
$sql = "SELECT * FROM Sites WHERE ID='$_SESSION[site]'";
$r_result = dbq($sql);
$a_result = mysql_fetch_assoc($r_result);

$_SESSION['privilege'] = "";
if ($a_result['MasterUID'] == $_SESSION['user_id'] || $_SESSION['username'] == "master") {
	$_SESSION['privilege'] = "owner";
} else {
	$sql = "SELECT * FROM AdminUsers WHERE ID='$_SESSION[user_id]'";
	$r_result = dbq($sql);
	$a_result2 = mysql_fetch_assoc($r_result);
	
	$a_priv = xml_get_tree($a_result['VendorManagers']);
	if ( is_array($a_priv[0]['children']) ) {
		foreach($a_priv[0]['children'] as $node) {
			if ($node['attributes']['EMAIL'] == $a_result2['Username'] && $node['attributes']['ENABLE'] == "true") {
				$_SESSION['privilege'] = "slave";
// enable, site, item_template, item_properties, user_browse, user_po_approve, user_po_notify, 
// order_notify, order_browse, order_download_invoice, order_change_status, order_download_impositions, order_download_dockets, order_approve
				if ($node['attributes']['ORDER_BROWSE'] == "true") 					{ $_SESSION['privilege_order_browse'] = 1; } else { $_SESSION['privilege_order_browse'] = 0; }
				if ($node['attributes']['ORDER_DOWNLOAD_DOCKETS'] == "true") 		{ $_SESSION['privilege_dockets'] = 1; } else { $_SESSION['privilege_dockets'] = 0; }
				if ($node['attributes']['ORDER_DOWNLOAD_IMPOSITIONS'] == "true") 	{ $_SESSION['privilege_impositions'] = 1; } else { $_SESSION['privilege_impositions'] = 0; }				
				if ($node['attributes']['ORDER_DOWNLOAD_INVOICE'] == "true") 		{ $_SESSION['privilege_invoices'] = 1; } else { $_SESSION['privilege_invoices'] = 0; }				
				if ($node['attributes']['ORDER_CHANGE_STATUS'] == "true") 			{ $_SESSION['privilege_order_status'] = 1; } else { $_SESSION['privilege_order_status'] = 0; }				
				if ($node['attributes']['ORDER_APPROVE'] == "true") 				{ $_SESSION['privilege_order_approval'] = 1; } else { $_SESSION['privilege_order_approval'] = 0; }				

				if ($node['attributes']['USER_PO_APPROVE'] == "true") 				{ $_SESSION['privilege_user_poapprove'] = 1; } else { $_SESSION['privilege_user_poapprove'] = 0; }				
				if ($node['attributes']['USER_BROWSE'] == "true") 					{ $_SESSION['privilege_user_browse'] = 1; } else { $_SESSION['privilege_user_browse'] = 0; }				

				if ($node['attributes']['SITE'] == "true") 							{ $_SESSION['privilege_site'] = 1; } else { $_SESSION['privilege_site'] = 0; }
				
				if ($node['attributes']['ITEM_PROPERTIES'] == "true") 				{ $_SESSION['privilege_items_properties'] = 1; } else { $_SESSION['privilege_items_properties'] = 0; }
				if ($node['attributes']['ITEM_TEMPLATE'] == "true") 				{ $_SESSION['privilege_items_template'] = 1; } else { $_SESSION['privilege_items_template'] = 0; }
				if ($node['attributes']['ITEM_TEMPLATE'] == "true" || $node['attributes']['ITEM_PROPERTIES'] == "true") {
					$_SESSION['privilege_items_browse'] = 1; 
				} else { 
					$_SESSION['privilege_items_browse'] = 0; 
				}
				break;

/*
	$a_allow['users_list_poapprove'] = "privilege_user_poapprove";
	$a_allow['users_list_browse'] = "privilege_user_browse";
	$a_allow['order_list_approval'] = "privilege_order_approval";
	$a_allow['order_list_browse'] = "privilege_order_browse";
	$a_allow['order_list_impose'] = "privilege_impositions";
	$a_allow['item_list'] = "privilege_items_browse";
	$a_allow['site_appearance'] = "privilege_site";
*/
			}
		}
	}
	
	$allow_page = 0;
	switch ($_SESSION['action']) {

		case "users_list_poapprove" : if ($_SESSION['privilege_user_poapprove']) { $allow_page = 1; } break;
		case "users_list_browse" : if ($_SESSION['privilege_user_browse']) { $allow_page = 1; } break;
		case "order_list_approval" : if ($_SESSION['privilege_order_approval']) { $allow_page = 1; } break;
		case "item_list" : if ($_SESSION['privilege_items_browse']) { $allow_page = 1; } break;
		case "site_appearance" : if ($_SESSION['privilege_site']) { $allow_page = 1; } break;
		case "site_settings" : if ($_SESSION['privilege_site']) { $allow_page = 1; } break;
		
		case "order_view" : if ($_SESSION['privilege_order_browse']) { $allow_page = 1; } break;
		case "order_search_results" : if ($_SESSION['privilege_order_browse']) { $allow_page = 1; } break;
		case "order_list_impose" : if ($_SESSION['privilege_impositions'] || $_SESSION['privilege_dockets']) { $allow_page = 1; } break;
		case "order_list_imp_hist" : if ($_SESSION['privilege_impositions'] || $_SESSION['privilege_dockets']) { $allow_page = 1; } break;
		case "site_open" :  $allow_page = 1; break;
		case "home" :  $allow_page = 1; break;
	}	
	
	if (($_SESSION['privilege'] == "" && $_SESSION['site'] != "") || !$allow_page) { 
		header("Location: vp.php?action=site_open"); 
		exit(" "); //Not enough privileges. <a href=\"vp.php?action=site_open\">Open a different site</a>.
	}
}




if ( $a_form_vars[r] != "1") 
//	header ("Location: vp.php?r=1&ms_sid=$ms_sid");

$menu = iface_menu($_SESSION[site],$_SESSION['user_id'],$_SESSION['tm'],$sel_menu);



?><!--

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
<title>Variaprint Manager</title>
<?php
print($header_content);
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.topmenu {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #FFFFFF;
	text-decoration: none;
}
-->
</style>

<script language="JavaScript" type="text/JavaScript">
//this.window.name = "main" ;
//vp_main = this.window ;
//alert(vp_main.name);
var elem_saved = true;
var c = false;
var al = "";
var saved_btn_clicked = false;

function findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function update_color(obj) {
	elem_saved = false;
	oPicker = findObj('colorpicker_' + obj)
	oColor = findObj(obj)
	if (oColor.value != "") {	oPicker.style.backgroundColor = oColor.value }
}


function doUnload() {
	if (!elem_saved && !saved_btn_clicked) {
		var msg = "";
		var hash = "";
		with (document.forms[0]) {
			for (var i = 0; i < elements.length; i++) {
				if (elements[i].type == "radio" || elements[i].type == "checkbox") {
					if (elements[i].checked) hash += elements[i].name + "=" + escape(elements[i].value) + "&"
				} else {
					hash += elements[i].name + "=" + escape(elements[i].value) + "&"
				}
				msg += "<BR>type is " + elements[i].type + " <strong>for</strong> " + elements[i].name + " <strong>= </strong>" + elements[i].value + "<br>"
			}
			msg += " " + elements.length
		}
		
		//hash = hash.replace(/#/gi,"%23")		
		testWin = popupWin('site_save_config.php?' + hash,'savedata','width=450,height=150,resizable=yes,centered=1');
	}
}

function set_save(flag) {
	elem_saved = flag;
}

function popupWin(u,n,o) { // v3
	var ftr = o.split(","); 
	nmv=new Array(); 
	for (i in ftr) {
		x=ftr[i]; 
		p=x.split("=");
		t=p[0]; v=p[1]; 
		nmv[t]=v;
	}
	if (nmv['centered']=='yes' || nmv['centered']==1) {
		nmv['left']=(screen.width-nmv['width'])/2 ; 
		nmv['top']=(eval(screen.height-nmv['height']-72))/2 ; 
		nmv['left'] = (nmv['left']<0)?'0':nmv['left'] ; 
		nmv['top'] = (nmv['top']<0)?'0':nmv['top']; 
		delete nmv['centered'];
	}
	o=""; 
	var j=0; 
	for (i in nmv) {
		o+=i+"="+nmv[i]+"\,";
	} 
	o=o.slice(0,o.length-1);
	window.open(u,n,o);
}
function addFiles() {
// deadend function to keep from getting an error in some browsers
}

</script>


</head>

<body background="images/sidebar.gif" link="#121E37" vlink="#2C4781" leftmargin="0" topmargin="10" marginwidth="0" marginheight="10" onload="logoutRefresh()" onunload="doUnload()">
<?php
if (!isset($form_method)) {
	$form_method = "get";
}
?>

<form action="vp.php" method="<?php print($form_method); ?>">
  <table width="761" border="0" cellpadding="0" cellspacing="0">
<?php if ( isset($_SESSION['site']) && $_SESSION['site'] != "") { ?>
    <tr>
      <td class="text">&nbsp;</td>
      <td class="text">
        <table  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td nowrap bgcolor="#FFFFFF" class="text"><a href="vp.php?action=site_open&user_id=<?php 
		print($_SESSION[user_id]); ?>">open&nbsp;order&nbsp;site</a><span class="title"> </span> </td>
            <td align="right" nowrap bgcolor="#FFFFFF" class="text">&nbsp;</td>
          </tr>
          <tr>
            <td nowrap bgcolor="#FFFFFF" class="text"><a href="javascript:;" onClick="popupWin('site_new.php','','width=450,height=300,centered=1')">new&nbsp;order&nbsp;site</a>...</td>
            <td align="right" nowrap bgcolor="#FFFFFF" class="text">&nbsp;</td>
          </tr>
        </table>
</td>
      <td>&nbsp;</td>
      <td valign="top">        <table width="600" border="0" cellpadding="8" cellspacing="1" bgcolor="#ADB7CE">
          <tr>
            <td bgcolor="#F3F3F3" class="text">
              <table width="575" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="title"><span class="text">editing site:</span> <?php
		
		print(
	  	" 
		<strong>&quot;" . $_SESSION['site_name'] . "&quot;</strong>   &nbsp; 
		 <a href=\"javascript:;\" onclick=\"popupWin('site_edit_name.php','renamesite','width=300,height=120,centered=1')\" class=\"text\">rename</a>...
		");
		
		?>
                  </td>
                  <td nowrap class="text"><?php
		
		print(
	  	" 
		<b class=\"text\">$_SESSION[site_status]</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"popupWin('site_edit_status.php','renamestatus','width=300,height=300,centered=1')\" class=\"text\">edit&nbsp;status</a>...
		");
		
		?>
                  </td>
                  <td width="150" align="right" nowrap class="text"><a href="../vp.php?site=<?php print($_SESSION[site]); ?>&mode=test" target="test">view&nbsp;order&nbsp;site</a>&nbsp;...<a href="vp.php?action=logout"><span class="title"><strong></strong></span></a> &nbsp;&nbsp;&nbsp;<a href="javascript:;" onClick="popupWin('howtolink.php?s=<?php print($_SESSION[site]); ?>','howtolink','height=100,width=300,centered=1')">link</a>&nbsp;...</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
        <tr>
          <td colspan="4" valign="bottom" class="text">&nbsp;</td>
        </tr>
        <?php } ?>
    <tr>
      <td width="10" valign="bottom" class="text">&nbsp;</td> 
      <td width="131" rowspan="3" valign="top" class="text"><strong>Current User:</strong><br>
        &quot;<em><?php print($_SESSION['username']); ?></em>&quot; &nbsp;<br> 
          <a href="vp.php?action=logout">logout</a> 
        &nbsp; <a href="javascript:;" onClick="popupWin('account_edit.php?user_id=<?php print($_SESSION[user_id]); ?>','','width=550,height=380,centered=yes,resizable=yes')">edit</a>... 
          <script language="JavaScript" type="text/JavaScript">
		  var minutes = 21
		  var refreshTimerInit = false
		  
		  	function resetMinutes(newval) {
				minutes = newval;
			}
			function logoutRefresh()
			{
			/*
				if (minutes > document.forms[0].counter.value) {
					document.forms[0].counter.value = minutes;
				} else {
					--document.forms[0].counter.value;
					minutes = document.forms[0].counter.value;
				}
				*/
				--minutes;
				document.forms[0].counter.value = minutes;
				
				if (minutes <= 0)
				{
					document.location = 'index.php'
				}
				if (refreshTimerInit) { clearTimeout(refreshTimer);  } // alert("This is a test");
				refreshTimer = setTimeout("logoutRefresh()", 1000*60);
				refreshTimerInit = true
			}
		  </script>
          <br>
          <table border="0" cellspacing="0" cellpadding="0">
          <tr class="text">
            <td colspan="3">&nbsp;</td>
            </tr>
          <tr class="text"> 
            <td>Logout&nbsp;in&nbsp;</td>
            <td> <input name="counter" type="text" class="text" id="counter" size="3" style="width:25" value="21" onChange="resetMinutes(this.value)"> 
            </td>
            <td>&nbsp;min</td>
          </tr>
      </table></td>
      <td width="20">&nbsp;</td>
      <td width="640" valign="top"><?php print($menu); ?></td>
    </tr>
    <tr>
      <td width="10" rowspan="2"><img src="images/spacer.gif" width="10" height="1"></td> 
      <td rowspan="2"><img src="images/spacer.gif" width="1" height="36"></td>
      <td height="1" valign="top" class="text"><img src="images/spacer.gif" width="1" height="8"></td>
    </tr>
    <tr>
      <td height="30" valign="top" class="text"><?php print($submenu); ?></td>
    </tr>
    <tr>
      <td width="10" valign="top" class="text">&nbsp;</td> 
      <td width="131" valign="top" class="text"><?php if ( isset($_SESSION['site']) && $_SESSION['site'] != "") { 
	
	
	/*  print(
	  	" <em>Current order site:</em><br>
		Name:  <strong>" . $_SESSION['site_name'] . "</strong > &nbsp; <a href=\"javascript:;\" onclick=\"popupWin('site_edit_name.php','renamesite','width=300,height=120,top=120,left=250')\">edit</a>...<br>
		Status: <strong>$_SESSION[site_status]</strong> &nbsp;&nbsp; <a href=\"javascript:;\" onclick=\"popupWin('site_edit_status.php','renamestatus','width=300,height=120,top=100,left=230')\">edit</a>...
		");*/
	?>        &raquo; <a href="vp.php?action=home&ms_sid=<?php 	print($ms_sid); ?>"> 
        welcome page</a> 
        <p> 
          <?php } ?>
      <p><img src="images/vp-logo.gif" width="120" height="25"> <strong><br>
          <br>
          <font color="#999999"><?php print($_SESSION[site]); ?>
	    <img src="images/spacer.gif" width="1" height="40"><img src="images/spacer.gif" width="130" height="1"></font></strong></p> </td>
      <td><img src="images/spacer.gif" width="20" height="1"></td>
      <td valign="top"><?php
	  
	  print($content . "\n<br><br><br><br>");
//	 print("<textarea cols=100 rows=6 style=\"width: 600\">");
//	  print_r($_SESSION);
//	  print("</textarea>");
	  ?>
        <br>
      </td>
    </tr>
  </table>
<input type="hidden" name="user_id" value="<?php print($a_form_vars['user_id']); ?>">
<input type="hidden" name="action-disabled" value="<?php print($action); ?>">
</form>
<script language="Javascript">
<!--
if (document.forms.length > 0) {
	if (document.forms[0].elements.length > 0) {
	//	document.forms[0].elements[0].focus();
	}
}
//-->
</script>
</body>
</html>
