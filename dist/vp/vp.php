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


header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    			// Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 	// always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  	// HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                          			// HTTP/1.0


include("inc/config.php");
include("inc/encrypt.php");
include("inc/functions-global.php");
include("inc/functions.php");
include("inc/iface.php");
//include("inc/session.php");



//session_save_path("/www/tmp");
session_name("os_sid");
session_start();
$_SESSION[os_sid] = $os_sid = session_id();

if (isset($a_form_vars['site']) && $a_form_vars['site'] != $_SESSION['site'] && $a_form_vars['r'] != "1") {
	$_SESSION[site] = $a_form_vars['site'];
	session_destroy();
	setcookie("os_sid","",time()-10000);
	setcookie("siteid",$a_form_vars['site'],time()+60*60*24);
	if (!isset($a_form_vars['os_page'])) {
		$page_frag = "&os_page=home";
	}
	header("Location: $_SERVER[REQUEST_URI]&r=1".$page_frag);
	exit;
}

if ( isset($a_form_vars['site']) ) {
	$_SESSION['site'] = $a_form_vars['site'];
}

if ( !isset($_SESSION['site']) || $_SESSION['site'] == "" ) {
	$_SESSION['site'] = $_COOKIE['siteid'];
//	$a_form_vars['site'] = $_COOKIE['siteid'];
}

//if (!isset($_COOKIE['siteid'])) {
	setcookie("siteid",$_SESSION['site'],time()+60*60*24);
//}

$logoutinterval = 3600;					// seconds until we log em out
$showform = 1;							// set default to wrap a form tag around the content
$script_name = $_SERVER['SCRIPT_NAME']; // set the default script name



// Check logged in status **********************************************************************
require_once("inc/check-login.php");


// If we're logged in, make sure the cart is saved
if ($_SESSION['logged_in'] == 1) {
	$sql = "SELECT ID,SavedID FROM Cart WHERE SessionID='$os_sid' AND SiteID='$_SESSION[site]'";
	$r_result = dbq($sql); 
	
	if ( mysql_num_rows($r_result) > 0 ) {
		$sql = "SELECT ID FROM SavedOrders WHERE SessionID='$os_sid' AND UserID='$_SESSION[user_id]'";
		$r_result2 = dbq($sql);
		
		// Make sure that there's a saved DB entry
		if (mysql_num_rows($r_result2) > 0) {
			$a_result2 = mysql_fetch_assoc($r_result2);
			$saved_id = $a_result2['ID'];
		} else {
			$time = time();
			$sql = "INSERT INTO SavedOrders SET SessionID='$os_sid', UserID='$_SESSION[user_id]', DateSaved='$time', SiteID='$_SESSION[site]'";
			dbq($sql);
			$saved_id = db_get_last_insert_id();
		} 
		
		while ( $a_result = mysql_fetch_assoc($r_result) ) {
			if ($a_result['SavedID'] != $saved_id) {
				$sql = "UPDATE Cart SET SavedID='$saved_id' WHERE ID='$a_result[ID]'";
				dbq($sql);
			}
		}
	}
}

// $_SESSION['loginpassword'] = $a_form_vars['login_password'];
// Preprare and SAVE all the sessionvars
if ( isset($a_form_vars['billing_type'] ) ) {  $_SESSION['billing_type'] = $a_form_vars['billing_type']; }
if ( isset($a_form_vars[os_page] ) ) {  $_SESSION[os_page] = $a_form_vars[os_page]; }
if ( isset($a_form_vars[accounttab] ) )  {  $_SESSION[accounttab]  = $a_form_vars[accounttab]; }
if ( isset($a_form_vars[catalogtab] ) ) {  $_SESSION[catalogtab] = $a_form_vars[catalogtab]; }
if ( isset($a_form_vars[mode] ) ) {  $_SESSION[mode] = $a_form_vars[mode]; }
if ( isset($a_form_vars[site] ) ) {  $_SESSION[site] = $a_form_vars[site]; }
if ( isset($a_form_vars[itemid] ) ) {  $_SESSION[itemid] = $a_form_vars[itemid]; }
if ( isset($a_form_vars[prefill_set] ) ) {  $_SESSION[prefill_set] = $a_form_vars[prefill_set]; }
if ( isset($a_form_vars[input_options] ) ) {  $_SESSION[input_options] = $a_form_vars[input_options]; }
if ( isset($a_form_vars[overwrite_prefill] ) ) {  $_SESSION[overwrite_prefill] = $a_form_vars[overwrite_prefill]; }
if ( isset($a_form_vars[os_page_afterlogin] ) ) {  $_SESSION[os_page_afterlogin] = $a_form_vars[os_page_afterlogin]; }
if ( isset($a_form_vars['username'] ) ) {  $_SESSION['username'] = $a_form_vars['username']; }
if ( isset($a_form_vars['rc'] ) ) {  $_SESSION['resetpasswordcode'] = $a_form_vars['rc']; }
if ( isset($a_form_vars['ordertab'] ) ) {  $_SESSION['ordertab'] = $a_form_vars['ordertab']; }
if ( isset($a_form_vars['in_prod'] ) ) {  $_SESSION['in_prod'] = $a_form_vars['in_prod']; }

$a_site_settings = GetSiteAttributes($_SESSION[site], $_SESSION[mode]);

// Set the correct currency type
$currency = "$";
switch(strtolower($a_site_settings["Currency"])) {
	case "dollar" : $currency = "$"; break;
	case "euro" : $currency = "&euro;"; break;
	case "pound" : $currency = "&pound;"; break;
	case "yen" : $currency = "&yen;";
}


// Make sure the site is live
$sql = "SELECT Status FROM Sites WHERE ID='$_SESSION[site]'";
$res = dbq($sql);
$ar = mysql_fetch_assoc($res);
if ($_SESSION['mode'] != "test" && $ar["Status"] != "Live")
	header("Location: inactive.html");



// GET AND SET SESSION VARIABLES 
if ( isset($a_form_vars['os_action']) && $a_form_vars['os_action'] != "" ) {
	$actionfile =  "actions/". $a_form_vars['os_action'] . ".php";
	if ( file_exists($actionfile) ) {
		require_once ($actionfile);
		$a_form_vars['os_action'] = "";
	} else {
		$_SESSION['alert_msg'] .= "<br><br>Fatal error: action \"$a_form_vars[os_action]\" not found.";
		$_SESSION['show_alert'] = 1;
	}
}

$os_page = $_SESSION['os_page'];

// Handle page action
$actionfilename = "pages/" . $os_page . "/action.php";
if ( file_exists($actionfilename) ) { 
	include($actionfilename); 
}




// GET AND PARSE Site Settings   ************************************************************************ 
$a_site_settings = GetSiteAttributes($_SESSION[site], $_SESSION[mode]);



// START Processing  ************************************************************************************
$logosrc = "";
if ( trim($a_site_settings['SiteBannerLogo']) != "") { 
	$logosrc = "_sites/" . $_SESSION[site] . "/images/" . $a_site_settings['SiteBannerLogo'] ; 
}


// $title 			= $a_site_settings['SiteTitle'];
// $os_page = $_SESSION['os_page'];


// 1 800 755 5268 - www.mommystyle.com
$cust_menutext1		= $a_site_settings['MenuText1'];
$cust_menulink1		= $a_site_settings['MenuLink1'];
$cust_menutext2		= $a_site_settings['MenuText2'];
$cust_menulink2		= $a_site_settings['MenuLink2'];

$menutext_home 	= $a_site_settings['MenuHomeName'];
$menutext_catalog 	= $a_site_settings['MenuCatalogName'];
$menutext_account 	= $a_site_settings['MenuAccountName'];
$menutext_orderstatus 	= $a_site_settings['MenuOrderStatusName'];

$menubarcolor 		= $a_site_settings['SiteMenuBarColor'];
$menubevel			= $a_site_settings['SiteBevelMenuBar'];
$bannerbgcolor 		= $a_site_settings['SiteBannerColor'];
$bgcolor 			= $a_site_settings['SitePageBgColor'];
$bgimage 			= "_sites/$_SESSION[site]/images/" . $a_site_settings['SitePageBgImage'];
if (!file_exists($bgimage) || trim($a_site_settings['SitePageBgImage']) == "") {
	$bgimage = "";
} else {
	$bgimage = " background=\"$bgimage\"";
}

if ( $os_page == "" ) { $os_page = "home"; }
$pagefilename = "pages/" . $os_page . "/page.php";
if ( file_exists($pagefilename) ) {
	require_once($pagefilename);

} else {
	exit("Fatal error: \"$os_page\" page found.");
}


// Make sure we don't have any leftover order modification going on
if ($_SESSION[os_page] != "input" && $_SESSION[os_page] != "input_options" && $_SESSION[os_page] != "preview" && $_SESSION[os_page] != "preview_gen") {
	if ($_SESSION["modifyitem"]) {
		$_SESSION["modifyitem"] = false;
		// set the order status back to what it was
		if ($_SESSION[originalorderstatus] == 30) {
			$_SESSION[originalorderstatus] = 20;
		}
		$sql = "UPDATE Orders SET Status='$_SESSION[originalorderstatus]' WHERE ID='$_SESSION[modorderid]'";
		dbq($sql);
		
		// remove the cartitem
		$sql = "DELETE FROM Cart WHERE ID='$_SESSION[cartitemid]'";
		dbq($sql);
		
		$_SESSION['cartitemid'] = "";
		
		$_SESSION['alert_msg'] = "Modifying the item has been canceled. No changes have been made.";
		$_SESSION['show_alert'] = 1;
		if (!headers_sent()) {
			header("Location: vp.php?os_page=account&os_sid=$os_sid&site=$_SESSION[site]");
			exit;
		}
	}
}


$mast_menu = MakeMasthead($logosrc, $bannerbgcolor) . MakeMenuBar($menubarcolor,$menubevel,$menutext_home,$menutext_catalog,$menutext_account,$menutext_orderstatus,$cust_menutext1,$cust_menulink1,$cust_menutext2,$cust_menulink2) ;

if ($_SESSION[mode] == "test") { 
	$testmodebutton = "<div style=\"position:absolute; width:85; height:20; top:0; left:625;\">
	<a href=\"$script_name?mode=live&os_sid=$_SESSION[os_sid]&site=$_SESSION[site]\"><img src=\"images/testmode.gif\" border=\"0\"></a></div>";
}


if ( isset($title) ) {
	$title = $a_site_settings['SiteTitle'] . ": " . $title ;
} else {
	$title = $a_site_settings['SiteTitle'] ;
}


?>
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
<head>
<title><? print($title); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">


<?
 require_once("inc/style_sheet.php"); 
?>

<script language="JavaScript" type="text/JavaScript">

<?  print($js) ?>

function setFieldLabel(pldwnObj, fieldID) {
	fObj = findObj('field_' + fieldID);
	label = pldwnObj[pldwnObj.selectedIndex].value;
	
	if (fObj.value != '' && fObj.value.indexOf(': ') == -1 && label != '') {
		fObj.value = label + ": " + fObj.value;
	} else if (fObj.value.indexOf(': ') != -1) {
		aVal = fObj.value.split(': ');
		if (label =='none' || label =='null' || label == '' || label ==' ') { 
			fObj.value = aVal[1];
		} else {
			fObj.value = label + ": " + aVal[1];
		}
	} else {
		if ((label =='none' || label =='null' || label == '' || label ==' ') && fObj.value == "") { 
			fObj.value = '';
		} else if ((label =='none' || label =='null' || label == '' || label ==' ') && fObj.value != "") {
			fObj.value	= fObj.value;
		} else {
			fObj.value = label + ": " ;
		}
		
	}
	fObj.focus();
}

function findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function confirmAction (linkobj, msg) {
	is_confirmed = confirm(msg)
	
	if (is_confirmed) {	
		linkobj.href += '&confirmed=1'
	} 
	return is_confirmed
}


function popupWin(u,n,o) { // v3
	var ftr = o.split(","); 
	nmv=new Array(); 
	for (i in ftr) {
		x=ftr[i]; 
		t=x.split("=")[0]; 
		v=x.split("=")[1]; 
		nmv[t]=v;
	}
	if (nmv['centered']=='yes' || nmv['centered']==1) {
		nmv['left']=(screen.width-nmv['width'])/2 ; 
		nmv['top']=(screen.height-nmv['height']-72)/2 ; 
		nmv['left'] = (nmv['left']<0)?'0':nmv['left'] ; 
		nmv['top']=(nmv['top']<0)?'0':nmv['top']; 
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


</script>
<? print($header_content); ?> 
</head>
<body bgcolor="<? print($bgcolor); ?>" <? print($bgimage);  ?> leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" <? print($body_script); ?>>

<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%"><tr><td valign="top" height="99%">

<?
if ( strtolower($form_method) != "get" && strtolower($form_method) != "post") { $form_method = "get"; }
if ($showform != 0) print("<form action=\"$script_name\" method=\"$form_method\">");
//<!-- START MASTHEAD / MENU BAR //-->
print($testmodebutton);
print($mast_menu);
?><img src="images/spacer.gif" width="1" height="30"><?
//<!-- START MAIN PART OF PAGE //-->


if ($_SESSION['show_alert'] == 1) {	
	print("<table cellspacing=0 cellpadding=0 border=0><tr><td>
	<img src=\"images/spacer.gif\" width=\"167\" height=\"1\">
	</td><td>".
	alert_msg($_SESSION['alert_msg']) . 
	"</td></tr></table>" ); 
}
/**/

print($sPage); //$my_page);

/*
print("<textarea cols=130 rows=10>");
print_r($_SESSION);
print("</textarea>


<br>
<br>
");
*/

/*
//print("<br><br>");

print("<textarea cols=130 rows=10>");
print_r($a_site_settings);
print("</textarea>");
*/
$_SESSION[show_alert] = 0;
$_SESSION[alert_msg] = "";
?>
<? if ($showform != 0) { print("
<input type=\"hidden\" name=\"site\" value=\"$_SESSION[site]\">
<input type=\"hidden\" name=\"os_sid\" value=\"$_SESSION[os_sid]\">
</form>
"); }
?>

<br><br>
</td>
</tr>
<tr>
<td valign="top" height="1" bgcolor="#FFFFFF" style="padding:5px 10px 20px 165px;font-family:Arial, 
Helvetica,sans-serif;font-size:12px;">
Powered by <a href="http://www.variaprint.com/" target="_blank">VariaPrint</a>™</td>
</tr></table>

</body>
</html>
