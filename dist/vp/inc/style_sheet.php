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


$tabonstyle = $a_site_settings['SiteTabOnStyle'] ;
$taboffstyle = $a_site_settings['SiteTabOffStyle'] ;
$taboverstyle = $a_site_settings['SiteTabOverStyle'] ;

//if ($os_page != "home") { 
	
	$menutextstyle		= $a_site_settings['SiteMenuTextStyle'];
	$menutexthoverstyle	= $a_site_settings['SiteMenuHoverStyle'];

	$subtitlestyle 	 	= $a_site_settings['SiteSubTitleStyle'];
	$sidesubtitlestyle 	= $a_site_settings['SiteSideSubTitleStyle'];
	$textstyle	 		= $a_site_settings['SiteTextStyle'];
	$sidetextstyle		= $a_site_settings['SiteSideTextStyle'];
	$titlestyle	 		= $a_site_settings['SiteTitleStyle'];
	$labeltextstyle	 	= $a_site_settings['SiteLabelTextStyle'];
/*
} else {
	$menutextstyle	= $a_site_settings['HomePageMenuTextStyle'];
	$menutexthoverstyle	= $a_site_settings['HomePageMenuHoverStyle'];
	
	$titlestyle		= $a_site_settings['HomePageTitleStyle'];
	$textstyle		= $a_site_settings['HomePageTextStyle'];
	$subtitlestyle  = $a_site_settings['HomePageSubtitleStyle'];
	$labeltextstyle = $a_site_settings['HomePageLabelTextStyle'];
	$notetextstyle  = $a_site_settings['HomePageNoteTextStyle'];
}
*/
print("

<style type=\"text/css\">
<!--
.menu { $menutextstyle }
.menu:hover { $menutexthoverstyle  }
.title { $titlestyle  }
.sidesubtitle { $sidesubtitlestyle }
.subtitle { $subtitlestyle }
.text { $textstyle  }
.sidetext { $sidetextstyle  }
.itemtext { $itemtextstyle }
.label { $labeltextstyle  }
.tabon { $tabonstyle }
.taboff { $taboffstyle  }
.taboff:hover { $taboverstyle }
.button { $a_site_settings[SiteButtonStyle]  }
-->
</style>");

?>