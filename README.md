variaprint
==========

Web-to-print PDF customization and ordering system 


**Source code notes**

#Order Site

File | Description |
------|--------
**_aopreviews:** | Holds order files for approve order system
**_cartpreviews:** |	Holds order files for the customer previews
**_orderpdfs:**	| Holds the files once orders are placed
**_sites:**	| Holds the images for each site
**_users:**	| Holds the fonts for each admin user

*Note: The 5 directories above must be write-enabled. They hold files for their respective functions.*

**Files in “root” directory:**
 
File | Description |
------|--------
**aa.php** |	Used  for approving accounts for sites that require user accounts to be approved before granting access.
**aa_send.php** | Sends the message from aa.php
**ao.php** | Approve order. The main 
**applyforpo.php**  |  Screen for a user to apply for a PO account if that form of payment is available.
**cropMe.swf**  |  The crop tool that allows a user to zoom in and select the area of the image that they want to use.
**custimg.php**  |  Allows a user to upload, select, and crop a custom image for an image field on an item.
**help-csc.html**  | Explains what the CSC code is on the back of a credit card.
**icon.php**  | Resizes and diplays an image as an icon.
**imglib.php**  | Image library. Used for selecting library(s) of images for an image field on an item.
**inactive.html** |  The page that is shown if an inactive site is accessed.
**itemdetail.php** |  Displays the popup window with the detail for an item in the catalog.
**itemhelp.php** |  Displays the help text associated with a particular item field.
**itempreview.php** |  Popup window that displays the item preview on the cart page.
**loadcropimg.php**  | Resizes and loads a JPEG into the crop tool interface.
**orderitem_file.php** |  Loads a JPEG or PDF order file.
**print_invoice.php** |  Formats a printable invoice.
**vp.php**  | The main file that is accessed on the browser address bar. Works as a http request director to call the appropriate action and page.


**Files in “actions” directory:**

File | Description
------|--------
**addtocart.php** | Adds an item to the cart if it is non-custom. Otherwise, it sends them to the input options_page which will throw the user to the main input page if there are no prefill input options for the selected item.
**approve.php** |  Approves an item in the cart if the site is set to require approval of previews. Sends the user back to the preview page if the approval initials field is blank. Otherwise, it sends them to the catalog.
**cancelorder.php** |  A user may cancel an order from their account page if the order hasn’t been sent into production yet.
**checkout.php** |  Sets the page to the checkout page.
**confirmorder.php** |  Finalizes an order, removes it from the cart table and copies the print files from the _cartpreviews directory to the _orderpdfs directory.
**continuesavedorder.php** |  Allows a user start where they left off on an order by logging into their account page.
**create_account.php** |  Creates a user account. Verifies that the account doesn’t already exist and that the passwords match and are at least 6 characters long.
**deletefromcart.php**  | Removes an item from the user’s cart.
**edititem.php**  | Allows a user to make changes to an item that’s in their cart.
**gotocart.php**  | Goes to the cart page.
**input.php**  | Saves the input options if there are any input options from the previous page (adds the item to the cart if need be) and goes on to the main input page.
**login.php**  | Verifies a user’s login and logs them in or gives an appropriate error message if they could not be logged in.
**logout.php** |  Logs the user out.
**modifyorderitem.php** |  Allows a user to modify an item that has already been ordered if it is not in production.
**preconfirmorder.php** |  Validates the info from the main checkout page to make sure it has been filled out where required and contains valid info (LUHC for credit cards, and non-empty fields where required)
**removeorderitem.php**  | Allows a user to delete an item from an order after the order has been placed.
**reorder.php**  |  Allows a user to place the items from a previous order in their cart so they can approve, modify and re-order the same items.
**resetpassword.php**  | Puts a random password in the user’s account and sends them an email that allows them to reset their password.
**restoreorderitem.php** | Undeletes a deleted item from an order.
**save_and_preview.php** | Inserts the item into the cart and calls the preview function to generate a PDF and JPEG preview for the user.
**skiplogin.php** |  Allows the user to skip the login if the site allows it.
**update_and_preview.php** |  Updates the item’s personalization info in the cart and calls the preview function to generate a PDF and JPEG preview for the user.
**updatecostqty.php**  | Updates the cost and quantity for an item in the cart.
** updatepassword.php**  | Updates the user’s password after they have reset it.

	

**Files in “images” directory:**
		
  btn-choose.gif
  btn-delete.gif
  dot-gray.gif
  dot-litegray.gif
  help-csc.gif
  spacer.gif
  testmode.gif
  unknowfiletype.gif
  unknownfiletype.jpg
  vplogo-small.gif
  vplogo_small.gif


**Files in “inc” directory:**

File | Description
------|--------
**check-login.php**  | Code that checks to validate whether a user is logged in or not.
**config.php**  | The main configuration file for the system.
**encrypt.php**  | Encryption and credit card validation functions
**functions-global.php**  | The general functions used through the whole system.
**functions.php**  | Functions specific to the order site system.
**functions_pdf.php**  | The functions that comprise the main engine of the PDF rendering engine.
**iface.php**  | Interface functions.
**image.class.php**  | Class used for the image cropping / resizing tool.
**pfpro.php**  | PayFlow Pro™ credit card processing functions.
**popup-header.php**  | General functions used in popup windows.
**session.php**  | Functions for managing the PHP SESSION.
**style_sheet.php**  | Puts a site's style sheet into the page.


**Files in “pages” directory:**

Directory/System | File | Description
-------|------|-------------
*account:* | **action.php** | Checks to make sure the user is logged in and code to save account settings.
 | **page.php** | Creates an account page to display to the user. 
*cart:* | **page.php** | Creates the cart page.
*catalog:* | **action.php** | 
 | **page.php** |
*checkout:* | **action.php** | Checks to make sure the user is logged in.
 | **page.php** | Creates the checkout page.
*confirmorder:* | **page.php** | Displays the confirm order (pre-invoice) page.
*home:* | page.php | The homepage.
*input:* | action.php | Checks to make sure the user is logged in and that the input page is a valid place to go.
 | page.php | Creates the input page to display to the user.
*input_options:* | action.php | Checks to make sure the user is logged in and that the input_options page is a valid place to go.
 | page.php | Creates and displays the input options page.
*login:* |page.php|The login page.
*preconfirmorder:*|page.php|Displays the invoice for user approval.
*preview:*|action.php|Makes sure that the item hasn’t been deleted from the cart before proceeding.
 |page.php|Displays the preview page with the JPEG image and optional PDF image and approval message.
*preview_gen:*|action.php|Empty at this point. 
 |page.php|Displays the “Generating preview screen…” and calls the pdf_create()and pdf_rasterize() methods to generate the previews. 
*resetpswd:*|page.php|The page where a user requests to reset their password.
*up:*|page.php|The page where a user is sent a link to with a special code that let’s them reset their password.




#Admin Site

*The admin/tmp directory must be set to writable. This is where self-deleting invoices are stored when being downloaded through the order download screen.*

**Files in root “admin” directory:**

File | Description
-----|------
account_edit.php|Edit admin user’s account info and password.
add_manager.php|Add a manager to a site and edit their permission access level.
approval_managers.php|Not used.
color_picker.php|Pick a web-safe hex color for a site appearance property.
create_account.php|Functions that create a new admin user account.
createaccount.php|Screen to create a new admin user account.
delete_site.php|Set’s a site’s status to deleted in the database.
docket.php|Displays a docket from an imposed print job.
docket_change_status.php|Changes the status of a docket.
download_file.php|Downloads a file.
edit_coupons.php|Set’s up the discount coupons for use on the cart page of an order site.
edit_font_style.php|Edit the CSS of a particular style on the order site.
edit_tax_tables.php|Edit the state or province names and abbreviations to charge tax.
file_manager.php|The main frameset for the file manager.
file_manager.swf|The file manager that displays the files and allows them to be selected.
file_manager_files.php|Contains functions that the SWF calls to show the file list and delete files.
file_manager_flash.php|Contains the SWF file manager that displays files from a particular directory.
file_manager_menu.php|The top bar of the file manager
file_upload.php|Uploads a file into the file manager.
finishorder.php|Completes an order and sends the transaction through PFP.
forgotpswd.php|Resets an admin user’s profile
howtolink.php|Show’s a link that can be used to link to the order site.
image_library.php|Image library frameset. 
image_library_files.php|Displays images from the selected image library.
image_library_menu.php|Top bar of the image library.
imposition.php|Creates an imposition of the files that are passed as form variables.
index.php|The login page.
item_inventory.php|Sets up the inventory handling for a particular item.
item_new.php|Creates a new item.
loadfile.php|Loads a PDF or raster file as a low res JPEG.
manageraccounts.swf|Interface to edit manager permissions.
notice_options.php|Set the notification options for the owner of the current site.
order_download_docket.php|Shows the docket from the “Impose” screen in the manager.
order_download_invoice.php|Downloads invoices for the selected orders as one of three options.
order_impose.php|Interface screens to select the imposition and imposition delivery set up. Creates the docket as the last step.
payflow.php|Sets up the VeriSign® PayFlow Pro™ options.
pdfwrapper.php|An experiment to embed an image as a URL in a PDF file that can be downloaded at the time the PDF is viewed. Creates a very small PDF file but can take some time to render. Never used for production.
reportbug.php|Links to GitHub repository.
resetpswd.php|Screen that an admin user is sent to to enter a new password once they have reset their password.
sendAndLoad_mngrpermissions.php|File that the manageraccounts.swf file connects to in order to read and write the data to the database.
sendAndLoad_shipaddr.php|File that the shipping_address_list.swf file connects to in order to read and write the data to the database.
sendmessage.php|Sends a message regarding an order to the person who placed the order.
shipping_address_list.php|Page that encapsulates the shipping_address_list.swf file.
shipping_address_list.swf|Interface screen to edit the list of addresses that users can select from to populate the shipping information on the checkout page of an order site.
site_edit_name.php|Edit the name of  a site.
site_edit_status.php|Edit the status of a site.
site_new.php|Create a new site.
site_save_config.php|When leaving a screen with unsaved changes in the Site tab, this page will open in a popup window allowing those changes to be saved. Popup blocking must be off for this to work.
style.css|The CSS style sheet for the manager site.
vp.php|The main file of the admin site. Works as a traffic controller calling the appropriate action and page files.


Files in “admin/actions” directory:

File | Description
-----|------
home.php|The homepage of the manager system.
item_list.php|Shows a list of items.
login.php|The action that logs an admin user in to the manager.
logout.php|Logs an admin user out of the manager.
order_list_approval.php|Shows an approval screen similar to the non-admin screen shown with ao.php
order_list_imp_hist.php|Shows a history of imposition and docket files that have been created.
order_list_impose.php|Shows items that are ready to be imposed so they can be selected and imposed.
order_search_dockets.php|Searches for and displays a search results page of items imposed with a particular docket.
order_search_results.php|The search results action.
order_view.php|The search options selection screen.
site_appearance.php|Reads the xml/site_appearance.xml file to allow options for the site appearance to be selected and saved.
site_open.php|Make a different site active in the admin control panel.
site_settings.php|Reads the xml/site_settings.xml file to layout tabbed edit screens for changing the open site’s settings.|users_list_browse.php|Shows a list of order site user accounts.
users_list_browse_manager.php|Shows a list of order site managers.
users_list_poapprove.php|Approves or denies users who have requested a PO account.




**Image files in “admin/images” directory:**
bkg-groove.gif
bkg-menu.gif
blue-dot.gif
btn-approve-off.gif
btn-approve-on.gif
btn-big-go.gif
btn-browse_orders-off.gif
btn-browse_orders-on.gif
btn-browse_users-off.gif
btn-browse_users-on.gif
btn-change_site.gif
btn-impose-off.gif
btn-impose-on.gif
btn-items-disabled.gif
btn-items-off.gif
btn-items-on.gif
btn-move.gif
btn-po_approve-off.gif
btn-po_approve-on.gif
btn-site-disabled.gif
btn-site-off.gif
btn-site-on.gif
btn-users-disabled.gif
btn-users-off.gif
btn-users-on.gif
collapse-down.gif
collapse-up.gif
colorpicker.gif
createaccount-loginlink.gif
createaccount-title.gif
createyourown.gif
dot-liteblue.gif
fields.gif
filemanager-top-bkg.gif
firstsite-message.gif
forgotpswd.gif
home-welcome.gif
icon-add.gif
icon-bug.gif
icon-cont_help.gif
icon-delete.gif
icon-help-on.gif
icon-help.gif
icon-imposition-on.gif
icon-imposition.gif
icon-input.gif
icon-items.gif
icon-pdfsmall.gif
icon-pricing-on.gif
icon-pricing.gif
icon-rastersmall.gif
icon-shipping-on.gif
icon-shipping.gif
icon-site.gif
icon-sitenew.gif
icon-siteopen.gif
icon-supplier-on.gif
icon-supplier.gif
icon-template-on.gif
icon-template.gif
icon-users.gif
icon_fl.gif
intro-bottom.gif
intro-top.gif
login-bottomleft.gif
login-bottomright.gif
login-button.gif
login-lbl-password.gif
login-lbl-username.gif
login-newaccount.gif
login-title.gif
login-topleft.gif
login-topright.gif
menu-bkg.gif
menu-end-left.gif
menu-end.gif
menu-spacer.gif
overview.gif
progress-anim.gif
progress.gif
sidebar.gif
sort-down.gif
sort-up.gif
spacer.gif
tab-bkg.gif
tab-extender.gif
tab-group-off.gif
tab-group-on.gif
tab-help-off.gif
tab-help-on.gif
tab-imposition-off.gif
tab-imposition-on.gif
tab-input-off.gif
tab-input-on.gif
tab-name-off.gif
tab-name-on.gif
tab-off_left.gif
tab-off_middle.gif
tab-off_right.gif
tab-on_left.gif
tab-on_middle.gif
tab-on_right.gif
tab-prefill-off.gif
tab-prefill-on.gif
tab-pricing-off.gif
tab-pricing-on.gif
tab-right-end.gif
tab-shipping-off.gif
tab-shipping-on.gif
tab-supplier-off.gif
tab-supplier-on.gif
tab-template-off.gif
tab-template-on.gif
title-forgotpswd.gif
title-resetpswd.gif
tutorial-bkg.gif
vp-logo.gif



**Files in “admin/inc” directory:**

File | Description
-----|------
functions.php|General functions relating the manager system.
iface.php|Interface functions for the manager system.
popup_log_check.php|Checks to make sure that a user has access to the popup window that they’re trying to access.
session.php|Session related functions.



**Files in “admin/itemeditors” directory:**

File | Description
-----|------
addfont.php|Upload and install a Unix Type 1 font for use in the template editor.
detect.swf|Detects if version 6+ Flash Player is installed.
get_flash.html|Page that browser is sent to if Flash 6+ is not detected.
item_properties.swf|Used to edit the item properties.
item_template_editor.php|Encapsulates the template editor SWF file.
menu.swf|The menu to select between the template editor and the item properties screens.
preview_template.php|Previews the item template from the template editor.
sendAndLoad_allData.php|Sends all the data to the template editor for an item and handles the saving of an template file and it’s input options into the database.
sendAndLoad_item_properties.php|Reads and writes item properties from the item_properties.swf file.
variaprint.swf|The template editor.


**Files in “admin/itempreseteditors” directory:**

File | Description
-----|------
item_presets_editor.php|Main frameset for the preset editors
item_presets_imposition.php|This is where imposition “templates” are created. The layout is defined here to determine how the items will be positioned on the imposed page.
item_presets_pricing.php|Pricing presets can be created so that it’s easy to maintain and apply pricing to new items.
item_presets_shipping.php|All shipping costs for different locations and weights are set up here. 
item_ps_editor_menu.php|The menu for the preset editors.
sendAndLoad_shipping.php|Reads and writes shipping profile information through the shipping.swf file.
shipping.swf|The shipping setup interface.



**Files in “admin/xml” directory:**

File | Description
-----|------
site_appearance.xml|XML file that defines the options and structure for the site appearance properties in the Site > Appearance section of the manager.
site_settings.xml|XML file that defines the options and structure for the site settings in the Site > Settings section of the manager.


