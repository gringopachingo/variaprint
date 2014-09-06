-- Database: `vp`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `AccessLog`
-- 

CREATE TABLE `AccessLog` (
  `ID` int(11) NOT NULL auto_increment,
  `DateTime` varchar(255) NOT NULL default '',
  `UserAgent` text NOT NULL,
  `SessionVars` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `AccessLog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `AdminUsers`
-- 

CREATE TABLE `AdminUsers` (
  `ID` int(11) NOT NULL auto_increment,
  `LastSID` text NOT NULL,
  `DateCreated` text NOT NULL,
  `DateLastLogin` text NOT NULL,
  `Username` text NOT NULL,
  `Password` text NOT NULL,
  `Firstname` text NOT NULL,
  `Lastname` text NOT NULL,
  `Company` varchar(100) NOT NULL default '',
  `Phone` text NOT NULL,
  `Email` text NOT NULL,
  `Permissions` text NOT NULL,
  `Fonts` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `AdminUsers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `Approval`
-- 

CREATE TABLE `Approval` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` int(11) NOT NULL default '0',
  `MasterUID` int(11) NOT NULL default '0',
  `Name` text NOT NULL,
  `Definition` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `Approval`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `Cart`
-- 

CREATE TABLE `Cart` (
  `ID` int(11) NOT NULL auto_increment,
  `SessionID` text,
  `SiteID` int(11) NOT NULL default '0',
  `ApprovalInitials` varchar(10) NOT NULL default '',
  `SavedID` int(11) default NULL,
  `ItemID` int(11) NOT NULL default '0',
  `Qty` text NOT NULL,
  `Cost` text NOT NULL,
  `OptionalFieldSets` text NOT NULL,
  `Imprint` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `Cart`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `DiscountCoupons`
-- 

CREATE TABLE `DiscountCoupons` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` int(11) NOT NULL default '0',
  `Code` varchar(20) NOT NULL default '',
  `Amount` int(11) NOT NULL default '0',
  `Type` set('Percent','Dollars') NOT NULL default '',
  `ExpirationDate` varchar(50) NOT NULL default '',
  `OneUse` varchar(5) NOT NULL default 'false',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `DiscountCoupons`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `Dockets`
-- 

CREATE TABLE `Dockets` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` int(11) NOT NULL default '0',
  `VendorUsername` varchar(255) NOT NULL default '',
  `DateCreated` varchar(255) NOT NULL default '',
  `DateDue` varchar(255) NOT NULL default '',
  `DateCompleted` varchar(255) NOT NULL default '',
  `Priority` set('High','Standard','Low') NOT NULL default '',
  `PressRun` int(11) NOT NULL default '0',
  `Status` varchar(100) NOT NULL default '',
  `ImpositionID` int(11) NOT NULL default '0',
  `ImpositionLayout` text NOT NULL,
  `OrderItems` text NOT NULL,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `Dockets`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `Imposition`
-- 

CREATE TABLE `Imposition` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` int(11) NOT NULL default '0',
  `MasterUID` int(11) NOT NULL default '0',
  `Name` text NOT NULL,
  `Template` set('Y','N') NOT NULL default '',
  `Definition` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `Imposition`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `Items`
-- 

CREATE TABLE `Items` (
  `ID` int(11) NOT NULL auto_increment,
  `IsTemplate` varchar(5) NOT NULL default '',
  `Name` varchar(255) NOT NULL default '',
  `Description` text NOT NULL,
  `Custom` set('Y','N') NOT NULL default '',
  `TrackInventory` varchar(5) NOT NULL default '',
  `InventoryAmount` int(11) NOT NULL default '0',
  `GroupID` int(11) NOT NULL default '0',
  `MasterUID` int(11) NOT NULL default '0',
  `SiteID` int(11) NOT NULL default '0',
  `ImpositionID` int(11) NOT NULL default '0',
  `VendorUsername` varchar(255) NOT NULL default '0',
  `Weight` varchar(255) NOT NULL default '',
  `SmallIconLink` varchar(255) NOT NULL default '',
  `SmallShadow` text NOT NULL,
  `LargeIconLink` varchar(255) NOT NULL default '',
  `LargeShadow` text NOT NULL,
  `Pricing` text NOT NULL,
  `PDFProof` varchar(5) NOT NULL default '',
  `ReqApproval` varchar(5) NOT NULL default '',
  `Template` text NOT NULL,
  `TestTemplate` text NOT NULL,
  `Prefill` text NOT NULL,
  `FieldSections` text NOT NULL,
  `TestData` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=940 ;

-- 
-- Dumping data for table `Items`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `OrderItems`
-- 

CREATE TABLE `OrderItems` (
  `ID` int(11) NOT NULL auto_increment,
  `OrderID` int(11) NOT NULL default '0',
  `SiteID` int(11) NOT NULL default '0',
  `ItemID` int(11) NOT NULL default '0',
  `ItemName` varchar(255) NOT NULL default '',
  `ApprovalInitials` varchar(10) NOT NULL default '',
  `Qty` varchar(50) NOT NULL default '',
  `Cost` varchar(50) NOT NULL default '',
  `Status` int(11) NOT NULL default '0',
  `Imprint` text NOT NULL,
  `OriginalImprint` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `OrderItems`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `Orders`
-- 

CREATE TABLE `Orders` (
  `ID` int(11) NOT NULL auto_increment,
  `UserID` int(11) NOT NULL default '0',
  `Email` varchar(100) NOT NULL default '',
  `SiteID` int(11) NOT NULL default '0',
  `Status` int(11) NOT NULL default '0',
  `ApprovalCode` varchar(255) NOT NULL default '',
  `ApprovalEmail` varchar(255) NOT NULL default '',
  `DateOrdered` text NOT NULL,
  `DateApproved` varchar(255) NOT NULL default '',
  `DateCanceled` varchar(255) NOT NULL default '',
  `OrderInfo` text NOT NULL,
  `PayType` varchar(20) NOT NULL default '',
  `BilledStatus` varchar(20) NOT NULL default '',
  `PFPResult` text NOT NULL,
  `PFPResult2` text NOT NULL,
  `Messages` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `Orders`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `PO`
-- 

CREATE TABLE `PO` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` int(11) NOT NULL default '0',
  `MasterID` int(11) NOT NULL default '0',
  `Status` set('approved','notapproved') NOT NULL default '',
  `DateCreated` varchar(50) NOT NULL default '',
  `DateModified` varchar(50) NOT NULL default '',
  `Billing` text NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `ID` (`ID`),
  KEY `ID_2` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `PO`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `Pricing`
-- 

CREATE TABLE `Pricing` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` int(11) NOT NULL default '0',
  `MasterUID` int(11) NOT NULL default '0',
  `Template` set('Y','N') NOT NULL default '',
  `Name` text NOT NULL,
  `Definition` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `Pricing`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ProcessTimeLog`
-- 

CREATE TABLE `ProcessTimeLog` (
  `ID` int(11) NOT NULL auto_increment,
  `TimeStamp` text NOT NULL,
  `ItemID` text NOT NULL,
  `Type` text NOT NULL,
  `ProcessTime` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=11 ;

-- 
-- Dumping data for table `ProcessTimeLog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `SavedOrders`
-- 

CREATE TABLE `SavedOrders` (
  `ID` int(11) NOT NULL auto_increment,
  `UserID` int(11) NOT NULL default '0',
  `SiteID` int(11) NOT NULL default '0',
  `SessionID` varchar(100) NOT NULL default '',
  `DateSaved` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM COMMENT='Links with Cart>SavedID to save orders' AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `SavedOrders`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `Sessions`
-- 

CREATE TABLE `Sessions` (
  `ID` int(11) NOT NULL auto_increment,
  `SessionID` text NOT NULL,
  `SessionVars` text NOT NULL,
  `OrderInfo` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `Sessions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `Shipping`
-- 

CREATE TABLE `Shipping` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` int(11) NOT NULL default '0',
  `MasterUID` int(11) NOT NULL default '0',
  `Template` set('Y','N') NOT NULL default '',
  `Name` text NOT NULL,
  `Definition` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `Shipping`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `Sites`
-- 

CREATE TABLE `Sites` (
  `ID` int(11) NOT NULL auto_increment,
  `Template` varchar(5) NOT NULL default '',
  `DateCreated` varchar(50) NOT NULL default '',
  `Status` set('Live','Inactive','Deleted') NOT NULL default '',
  `MasterUID` int(11) NOT NULL default '0',
  `NoticePO` varchar(10) NOT NULL default '',
  `NoticeOrder` varchar(10) NOT NULL default '',
  `Name` text NOT NULL,
  `Settings` text NOT NULL,
  `SettingsTmp` text NOT NULL,
  `ItemGroups` text NOT NULL,
  `OrderStatuses` text NOT NULL,
  `ImageLibraries` text NOT NULL,
  `ShippingID` int(11) NOT NULL default '0',
  `ShippingAddresses` text NOT NULL,
  `ApprovalID` int(11) NOT NULL default '0',
  `ApprovalManagers` text NOT NULL,
  `VendorManagers` text NOT NULL,
  `Taxes` text NOT NULL,
  `PFP` text NOT NULL,
  `NoticeOrderApproved` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=509 ;

-- 
-- Dumping data for table `Sites`
-- 

INSERT INTO `Sites` (`ID`, `Template`, `DateCreated`, `Status`, `MasterUID`, `NoticePO`, `NoticeOrder`, `Name`, `Settings`, `SettingsTmp`, `ItemGroups`, `OrderStatuses`, `ImageLibraries`, `ShippingID`, `ShippingAddresses`, `ApprovalID`, `ApprovalManagers`, `VendorManagers`, `Taxes`, `PFP`, `NoticeOrderApproved`) VALUES (500, 'true', '', 'Inactive', 2, 'true', 'true', '[ Default Site Template ]', '<?xml version="1.0" encoding="iso-8859-1"?>\r\n<properties site="500"><property id=""></property><property id="publish_revert_test">Test</property><property id="action">site_settings</property><property id="hpURL">http://www.prevario.com/acmesite/</property><property id="HomePageTitle">&lt;font color=#000000&gt;Welcome&lt;/font&gt;</property><property id="HomePageIntroText">Enter welcome text here ...</property><property id="HomePageStyle">Standard</property><property id=""></property><property id="publish_revert_test">Test</property><property id="action">Save</property><property id="SiteStatus">Active</property><property id="SiteTitle">Stationery Order Site</property><property id="HomePageTitleStyle">font-family:Arial, Helvetica, san-serif; font-size:20px; text-decoration:on; color:#666699; </property><property id="HomePageTextStyle">font-family:Arial, Helvetica, san-serif; font-size:12px; text-decoration:on; color:#000000; </property><property id="HomePageBannerBkgColor">#FFFFFF</property><property id="HomePageBkgColor">#666699</property><property id="HomePageMenuBgColor">#666699</property><property id="HomePageBevelMenuBar">N</property><property id="HomePageMenuTextStyle">font-family:Arial, Helvetica, san-serif; font-size:10px; text-decoration:none; color:#FFFFFF; </property><property id="HomePageMenuHoverStyle">color:#00CCFF; </property><property id="HomePageSubtitleStyle">font-family:Arial, Helvetica, san-serif; font-size:12px; font-weight:bold; color:white; </property><property id="HomePageLabelTextStyle">font-family:Arial, Helvetica, san-serif; font-size:11px; color:white; </property><property id="HomePageNoteTextStyle"></property><property id="HomePageLogin">N</property><property id="SiteMenuBarColor">#666699</property><property id="SiteBevelMenuBar">N</property><property id="SiteMenuTextStyle">font-family:Arial, Helvetica, san-serif; font-size:10px; font-weight:normal; font-style:normal; text-decoration:none; color:#FFFFFF; </property><property id="SiteMenuHoverStyle">color:#00FFFF; </property><property id="SiteMenuLabelHome">home</property><property id="SiteMenuLabelCatalog">catalog</property><property id="SiteMenuLabelAccount">my account</property><property id="SiteBannerColor">#FFFFFF</property><property id="SiteBannerLogo">logo.gif</property><property id="SitePageBgColor">#666699</property><property id="SiteTitleStyle">font-family:Arial, Helvetica, san-serif; font-size:14px; font-weight:bold; font-style:normal; text-decoration:none; color:#FFFFFF; </property><property id="SiteSubTitleStyle">font-family:Arial, Helvetica, san-serif; font-size:12px; font-weight:bolder; text-decoration:on; color:#666699; </property><property id="SiteTextStyle">font-family:Arial, Helvetica, san-serif; font-size:11px; text-decoration:on; color:#000000; </property><property id="SiteLabelTextStyle">font-family:Arial, Helvetica, san-serif; font-size:11px; text-decoration:on; color:#000000; </property><property id="SiteNoteTextStyle">text-decoration:on; color:#000000; </property><property id="SiteTabOnStyle">font-family:Arial, Helvetica, san-serif; font-size:11px; font-weight:bold; text-decoration:none; color:black; </property><property id="SiteTabOffStyle">font-family:Arial, Helvetica, san-serif; font-size:11px; text-decoration:none; color:#666699; </property><property id="SiteTabOverStyle">color:black; </property><property id=""></property><property id="publish_revert_test">Test</property><property id="action">Save</property><property id="CatalogItemDisplayStyle">Images</property><property id="CatalogItemGroupStyle">Tabs</property><property id="CatalogRowHiliteColor"></property><property id="CatalogTitle">Catalog</property><property id="CatalogText">Select items on the right to add to your cart.</property><property id=""></property><property id="publish_revert_test">Test</property><property id="action">Save</property><property id="InvoiceAddress">Positive ID\r\n10635 Winterhawk Drive\r\nBoise ID 83709\r\n208-362-8006</property><property id="InvoiceNote">Thank you for allowing us to serve your ministry.</property><property id="NoPaymentNote"></property><property id="BillingAcceptCC">checked</property><property id="BillingCCsAccepted_Visa">checked</property><property id="BillingCCsAccepted_MC">checked</property><property id="BillingCCsAccepted_AMEX">checked</property><property id="CCNote">NOTE: Charge on your credit card statement will be from Positive ID</property><property id="BillingAcceptPO">checked</property><property id="PONote"></property><property id="BillingAcceptChecks">checked</property><property id="CheckNoteCheckout"></property><property id="CheckNote"></property><property id="SpecialInstructionsNote">Please let us know if you have any questions or comments, or if your order requires any special handling. Thank you.</property><property id="DontRequirePayment"></property><property id="HideCost"></property><property id="HideQty"></property><property id="BillingCCsAccepted_DISC">checked</property><property id="BillingCCsAccepted_DC"></property><property id="AllowPurchaseOnPending">checked</property><property id="SendPOManagerNoticeEmail">checked</property><property id="CheckoutCheckNote">Please send your check or money order to:\r\nPositive ID\r\n10635 Winterhawk Dr.\r\nBoise, ID 83709</property><property id="InvoiceCheckNote">Your order will be processed when your check is received.</property><property id="ChargeShipping">checked</property><property id="ShowAccount">checked</property><property id=""></property><property id="publish_revert_test">Test</property><property id="action">Save</property><property id="field_20"></property><property id=""></property><property id="publish_revert_test">Test</property><property id="action">save</property><property id="AccountTitle">My Account</property><property id="AccountText">You can edit your account settings and manage your orders on this page.</property><property id="save">Save</property><property id="site">500</property><property id="tab">tab_10</property><property id="save_action">Save</property><property id="user_id"></property><property id="sid">7Sm17b0zgjHYuT r28F6SsSrf5Y7oSz85S1gbQmzlY87bpPi g==</property><property id="username">luke</property><property id="password">harry</property><property id="Submit">Login</property><property id="tm">1</property><property id="sel_imposition">1</property><property id="sel_status">30</property><property id="page">home</property><property id="IncludeApprovalManager"></property><property id="RequireAccount"></property><property id="ms_sid">119bf472bc549b12ae807b3c7a500030</property><property id="appearance_tab">tab_10</property><property id="counter">20</property><property id="SiteButtonStyle"></property><property id="settings_tab">tab_10</property><property id="CatalogRequireLogin">No</property><property id="CartTitle">Cart</property><property id="CartText"></property><property id="CheckoutTitle">Checkout</property><property id="CheckoutText">Step 1 of 3</property><property id="PreInvoiceTitle">Checkout</property><property id="PreInvoiceText">Step 2 of 3</property><property id="InvoiceTitle">Checkout</property><property id="InvoiceText">Step 3 of 3\r\n&lt;br&gt;&lt;br&gt;\r\n&lt;b&gt;PLEASE NOTE:&lt;/b&gt; &lt;br&gt;This invoice will not be mailed to you. Please print a copy for your records. You will also recieve a copy of your invoice by e-mail.</property><property id="ChargeTax">checked</property><property id="IncludeSpecialInstructions">checked</property><property id="submit">Save</property><property id="SiteSideSubTitleStyle"></property><property id="SiteSideTextStyle">font-family:Arial, Helvetica, san-serif; font-size:12px; text-decoration:on; color:#FFFFFF; </property><property id="InputOptionsText">Fill in the information that you would like to appear on your item.</property><property id="InputText">Fill in the information that you would like to appear on your item.</property><property id="HomeCatalogButton">btn_start_order.gif</property><property id="SitePageBgImage"></property><property id="ApprovalTitle"></property><property id="ApprovalText"></property><property id="ApprovalAgreementText">&lt;b&gt;Tip: Click on the &quot;View PDF proof&quot; link on the right if you need a more detailed view of your product.&lt;/b&gt;&lt;br&gt;&lt;br&gt;By entering your initials below you agree that you have carefully reviewed the above proof, that you accept full responsibility for the accuracy of the information you have entered, and approve this proof to be printed as it appears above.</property><property id="LoginTitle"></property><property id="LoginText"></property><property id="BillingCCUsePayFlow"></property><property id="BillingCCsAccepted_JCB"></property><property id="BillingAcceptPayPal"></property><property id="PayPalEmail"></property><property id="PayPalPrefix"></property><property id="CheckoutPayPalNote"></property><property id="InvoicePayPalNote"></property><property id="InitialOrderStatus">20</property><property id="MenuHomeName">home</property><property id="MenuCatalogName">catalog</property><property id="MenuAccountName">my account</property><property id="MenuOrderStatusName">order status</property><property id="MenuText1">customer service</property><property id="MenuLink1">javascript:popupWin(&apos;http://www.prevario.com/positiveid/pages/customerservice.php&apos;,&apos;outside&apos;,&apos;width=550,height=450,centered=1,scrollbars=1&apos;)</property><property id="MenuText2">your privacy &amp; security</property><property id="MenuLink2">javascript:popupWin(&apos;http://www.prevario.com/positiveid/pages/privacy.php&apos;,&apos;outside&apos;,&apos;width=550,height=450,centered=1,scrollbars=1&apos;)</property><property id="action-disabled">site_appearance</property><property id="Currency">dollar</property><property id="ShowCoupon">checked</property><property id="IncludeAddressList"></property></properties>', '<?xml version="1.0" encoding="iso-8859-1"?>\r\n<properties site="500"><property id=""></property><property id="publish_revert_test">Test</property><property id="action">site_settings</property><property id="hpURL">http://www.prevario.com/acmesite/</property><property id="HomePageTitle">&lt;font color=#000000&gt;Welcome&lt;/font&gt;</property><property id="HomePageIntroText">Enter welcome text here ...</property><property id="HomePageStyle">Standard</property><property id=""></property><property id="publish_revert_test">Test</property><property id="action">Save</property><property id="SiteStatus">Active</property><property id="SiteTitle">Stationery Order Site</property><property id="HomePageTitleStyle">font-family:Arial, Helvetica, san-serif; font-size:20px; text-decoration:on; color:#666699; </property><property id="HomePageTextStyle">font-family:Arial, Helvetica, san-serif; font-size:12px; text-decoration:on; color:#000000; </property><property id="HomePageBannerBkgColor">#FFFFFF</property><property id="HomePageBkgColor">#666699</property><property id="HomePageMenuBgColor">#666699</property><property id="HomePageBevelMenuBar">N</property><property id="HomePageMenuTextStyle">font-family:Arial, Helvetica, san-serif; font-size:10px; text-decoration:none; color:#FFFFFF; </property><property id="HomePageMenuHoverStyle">color:#00CCFF; </property><property id="HomePageSubtitleStyle">font-family:Arial, Helvetica, san-serif; font-size:12px; font-weight:bold; color:white; </property><property id="HomePageLabelTextStyle">font-family:Arial, Helvetica, san-serif; font-size:11px; color:white; </property><property id="HomePageNoteTextStyle"></property><property id="HomePageLogin">N</property><property id="SiteMenuBarColor">#666699</property><property id="SiteBevelMenuBar">N</property><property id="SiteMenuTextStyle">font-family:Arial, Helvetica, san-serif; font-size:10px; font-weight:normal; font-style:normal; text-decoration:none; color:#FFFFFF; </property><property id="SiteMenuHoverStyle">color:#00FFFF; </property><property id="SiteMenuLabelHome">home</property><property id="SiteMenuLabelCatalog">catalog</property><property id="SiteMenuLabelAccount">my account</property><property id="SiteBannerColor">#FFFFFF</property><property id="SiteBannerLogo">logo.gif</property><property id="SitePageBgColor">#666699</property><property id="SiteTitleStyle">font-family:Arial, Helvetica, san-serif; font-size:14px; font-weight:bold; font-style:normal; text-decoration:none; color:#FFFFFF; </property><property id="SiteSubTitleStyle">font-family:Arial, Helvetica, san-serif; font-size:12px; font-weight:bolder; text-decoration:on; color:#666699; </property><property id="SiteTextStyle">font-family:Arial, Helvetica, san-serif; font-size:11px; text-decoration:on; color:#000000; </property><property id="SiteLabelTextStyle">font-family:Arial, Helvetica, san-serif; font-size:11px; text-decoration:on; color:#000000; </property><property id="SiteNoteTextStyle">text-decoration:on; color:#000000; </property><property id="SiteTabOnStyle">font-family:Arial, Helvetica, san-serif; font-size:11px; font-weight:bold; text-decoration:none; color:black; </property><property id="SiteTabOffStyle">font-family:Arial, Helvetica, san-serif; font-size:11px; text-decoration:none; color:#666699; </property><property id="SiteTabOverStyle">color:black; </property><property id=""></property><property id="publish_revert_test">Test</property><property id="action">Save</property><property id="CatalogItemDisplayStyle">Images</property><property id="CatalogItemGroupStyle">Tabs</property><property id="CatalogRowHiliteColor"></property><property id="CatalogTitle">Catalog</property><property id="CatalogText">Select items on the right to add to your cart.</property><property id=""></property><property id="publish_revert_test">Test</property><property id="action">Save</property><property id="InvoiceAddress">Printforce, Inc\r\n</property><property id="InvoiceNote">Thank you for your business.</property><property id="NoPaymentNote"></property><property id="BillingAcceptCC">checked</property><property id="BillingCCsAccepted_Visa">checked</property><property id="BillingCCsAccepted_MC">checked</property><property id="BillingCCsAccepted_AMEX">checked</property><property id="CCNote">NOTE: Charge on your credit card statement will be from Positive ID</property><property id="BillingAcceptPO">checked</property><property id="PONote"></property><property id="BillingAcceptChecks">checked</property><property id="CheckNoteCheckout"></property><property id="CheckNote"></property><property id="SpecialInstructionsNote">Please let us know if you have any questions or comments, or if your order requires any special handling. Thank you.</property><property id="DontRequirePayment"></property><property id="HideCost"></property><property id="HideQty"></property><property id="BillingCCsAccepted_DISC">checked</property><property id="BillingCCsAccepted_DC"></property><property id="AllowPurchaseOnPending">checked</property><property id="SendPOManagerNoticeEmail">checked</property><property id="CheckoutCheckNote">Please send your check or money order to:\r\nPositive ID\r\n10635 Winterhawk Dr.\r\nBoise, ID 83709</property><property id="InvoiceCheckNote">Your order will be processed when your check is received.</property><property id="ChargeShipping">checked</property><property id="ShowAccount">checked</property><property id=""></property><property id="publish_revert_test">Test</property><property id="action">Save</property><property id="field_20"></property><property id=""></property><property id="publish_revert_test">Test</property><property id="action">save</property><property id="AccountTitle">My Account</property><property id="AccountText">You can edit your account settings and manage your orders on this page.</property><property id="save">Save</property><property id="site">500</property><property id="tab">tab_10</property><property id="save_action">Save</property><property id="user_id"></property><property id="sid">7Sm17b0zgjHYuT r28F6SsSrf5Y7oSz85S1gbQmzlY87bpPi g==</property><property id="username">luke</property><property id="password">harry</property><property id="Submit">Login</property><property id="tm">1</property><property id="sel_imposition">1</property><property id="sel_status">30</property><property id="page">home</property><property id="IncludeApprovalManager"></property><property id="RequireAccount"></property><property id="ms_sid">119bf472bc549b12ae807b3c7a500030</property><property id="appearance_tab">tab_10</property><property id="counter">20</property><property id="SiteButtonStyle"></property><property id="settings_tab">tab_50</property><property id="CatalogRequireLogin">No</property><property id="CartTitle">Cart</property><property id="CartText"></property><property id="CheckoutTitle">Checkout</property><property id="CheckoutText">Step 1 of 3</property><property id="PreInvoiceTitle">Checkout</property><property id="PreInvoiceText">Step 2 of 3</property><property id="InvoiceTitle">Checkout</property><property id="InvoiceText">Step 3 of 3\r\n&lt;br&gt;&lt;br&gt;\r\n&lt;b&gt;PLEASE NOTE:&lt;/b&gt; &lt;br&gt;This invoice will not be mailed to you. Please print a copy for your records. You will also recieve a copy of your invoice by e-mail.</property><property id="ChargeTax">checked</property><property id="IncludeSpecialInstructions">checked</property><property id="submit">Save</property><property id="SiteSideSubTitleStyle"></property><property id="SiteSideTextStyle">font-family:Arial, Helvetica, san-serif; font-size:12px; text-decoration:on; color:#FFFFFF; </property><property id="InputOptionsText">Fill in the information that you would like to appear on your item.</property><property id="InputText">Fill in the information that you would like to appear on your item.</property><property id="HomeCatalogButton">btn_start_order.gif</property><property id="SitePageBgImage"></property><property id="ApprovalTitle"></property><property id="ApprovalText"></property><property id="ApprovalAgreementText">&lt;b&gt;Tip: Click on the &quot;View PDF proof&quot; link on the right if you need a more detailed view of your product.&lt;/b&gt;&lt;br&gt;&lt;br&gt;By entering your initials below you agree that you have carefully reviewed the above proof, that you accept full responsibility for the accuracy of the information you have entered, and approve this proof to be printed as it appears above.</property><property id="LoginTitle"></property><property id="LoginText"></property><property id="BillingCCUsePayFlow"></property><property id="BillingCCsAccepted_JCB"></property><property id="BillingAcceptPayPal"></property><property id="PayPalEmail"></property><property id="PayPalPrefix"></property><property id="CheckoutPayPalNote"></property><property id="InvoicePayPalNote"></property><property id="InitialOrderStatus">20</property><property id="MenuHomeName">home</property><property id="MenuCatalogName">catalog</property><property id="MenuAccountName">my account</property><property id="MenuOrderStatusName">order status</property><property id="MenuText1">customer service</property><property id="MenuLink1">javascript:popupWin(&apos;http://www.prevario.com/positiveid/pages/customerservice.php&apos;,&apos;outside&apos;,&apos;width=550,height=450,centered=1,scrollbars=1&apos;)</property><property id="MenuText2">your privacy &amp; security</property><property id="MenuLink2">javascript:popupWin(&apos;http://www.prevario.com/positiveid/pages/privacy.php&apos;,&apos;outside&apos;,&apos;width=550,height=450,centered=1,scrollbars=1&apos;)</property><property id="action-disabled">site_settings</property><property id="Currency">dollar</property><property id="ShowCoupon"></property><property id="IncludeAddressList"></property></properties>', '<?xml version="1.0" encoding="iso-8859-1"?> \r\n      <itemgroups> \r\n        <itemgroup name="Business Cards" id="0" description="" hidden="N"></itemgroup> \r\n        <itemgroup name="Letterhead/envelopes" id="1" description="" hidden="N"></itemgroup> \r\n        <itemgroup id="3" name="Address Labels" description="" hidden="N"></itemgroup> \r\n        <itemgroup id="5" name="Other" description="" hidden="N"></itemgroup> \r\n      </itemgroups>', '<?xml version="1.0" encoding="iso-8859-1"?> <statuses> <status name="Cancelled" id="0"/> <status name="Cancelled by Customer" id="10"/> <status name="Cancelled by Approval Manager" id="15"/> <status name="Waiting for Approval" id="20"/> <status name="On Hold" id="30"/> <status name="Ready for Production" id="35"/> <status name="In Production" id="40"/> <status name="Shipped" id="50"/> </statuses>', '<?xml version="1.0" encoding="iso-8859-1"?>\r\n<libraries><library name="Untitled Library" id="1"></library></libraries>', 0, '', 1, '', '', '', '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `Users`
-- 

CREATE TABLE `Users` (
  `ID` int(11) NOT NULL auto_increment,
  `POID` int(11) NOT NULL default '0',
  `SiteID` int(11) NOT NULL default '0',
  `Status` set('WaitingForApproval','Denied','Active','Inactive') NOT NULL default '',
  `ApprovalCode` text NOT NULL,
  `ApprovalEmail` varchar(255) NOT NULL default '',
  `DateCreated` varchar(50) NOT NULL default '',
  `DateLastLogin` varchar(50) NOT NULL default '',
  `DateLastApprovalNotice` varchar(50) NOT NULL default '',
  `DateApproved` varchar(50) NOT NULL default '',
  `DateCanceled` varchar(50) NOT NULL default '',
  `LastSID` varchar(100) NOT NULL default '',
  `Username` varchar(16) NOT NULL default '',
  `Password` varchar(100) NOT NULL default '',
  `FirstName` varchar(50) NOT NULL default '',
  `LastName` varchar(50) NOT NULL default '',
  `Email` varchar(100) NOT NULL default '',
  `Phone` varchar(50) NOT NULL default '',
  `Address1` varchar(100) NOT NULL default '',
  `Address2` varchar(100) NOT NULL default '',
  `City` varchar(50) NOT NULL default '',
  `State` varchar(50) NOT NULL default '',
  `Zip` varchar(16) NOT NULL default '',
  `Country` varchar(50) NOT NULL default '',
  `OrderInfo` text NOT NULL,
  `sessionID` text NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `Users`
-- 

        