<?php
$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->create();
$Template->Configuration['DIRECTORY']->configuration_value = 'codeGeneration';
$Template->Configuration['NAME']->configuration_value = 'codeGeneration';

$Layout[5] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[5]);
$Layout[5]->layout_name = 'productBuy';

$Container[32] = $Layout[5]->Containers->getTable()->create();
$Layout[5]->Containers->add($Container[32]);
$Container[32]->sort_order = '1';
$Container[32]->Styles['margin-right']->definition_value = 'auto';
$Container[32]->Styles['margin-left']->definition_value = 'auto';
$Container[32]->Styles['width']->definition_value = '100%';
$Container[32]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[32]->Configuration['width_unit']->configuration_value = '%';
$Container[32]->Configuration['shadows']->configuration_value = '[]';
$Container[32]->Configuration['anchor_id']->configuration_value = '0';
$Container[32]->Configuration['is_anchor']->configuration_value = '0';
$Container[32]->Configuration['id']->configuration_value = '';
$Container[32]->Configuration['width']->configuration_value = '100';

$Column[37] = $Container[32]->Columns->getTable()->create();
$Container[32]->Columns->add($Column[37]);
$Column[37]->sort_order = '1';
$Column[37]->Styles['width']->definition_value = '100%';
$Column[37]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[37]->Configuration['anchor_id']->configuration_value = '0';
$Column[37]->Configuration['is_anchor']->configuration_value = '0';
$Column[37]->Configuration['id']->configuration_value = '';
$Column[37]->Configuration['shadows']->configuration_value = '[]';
$Column[37]->Configuration['width']->configuration_value = '100';
$Column[37]->Configuration['width_unit']->configuration_value = '%';

if (!isset($Box['productName'])){
 $Box['productName'] = $TemplatesInfoboxes->findOneByBoxCode('productName');
    if (!is_object($Box['productName']) || $Box['productName']->count() <= 0){
       installInfobox('includes/modules/infoboxes/productName/', 'productName', 'null');
       $Box['productName'] = $TemplatesInfoboxes->findOneByBoxCode('productName');
   }
}

$Widget[56] = $Column[37]->Widgets->getTable()->create();
$Column[37]->Widgets->add($Widget[56]);
$Widget[56]->identifier = 'productName';
$Widget[56]->sort_order = '1';
$Widget[56]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"box.tpl","widget_title":{"1":""}}';

if (!isset($Box['productDescription'])){
 $Box['productDescription'] = $TemplatesInfoboxes->findOneByBoxCode('productDescription');
    if (!is_object($Box['productDescription']) || $Box['productDescription']->count() <= 0){
       installInfobox('includes/modules/infoboxes/productDescription/', 'productDescription', 'null');
       $Box['productDescription'] = $TemplatesInfoboxes->findOneByBoxCode('productDescription');
   }
}

$Widget[57] = $Column[37]->Widgets->getTable()->create();
$Column[37]->Widgets->add($Widget[57]);
$Widget[57]->identifier = 'productDescription';
$Widget[57]->sort_order = '2';
$Widget[57]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

if (!isset($Box['reservationCalendar'])){
 $Box['reservationCalendar'] = $TemplatesInfoboxes->findOneByBoxCode('reservationCalendar');
    if (!is_object($Box['reservationCalendar']) || $Box['reservationCalendar']->count() <= 0){
       installInfobox('includes/modules/infoboxes/reservationCalendar/', 'reservationCalendar', 'null');
       $Box['reservationCalendar'] = $TemplatesInfoboxes->findOneByBoxCode('reservationCalendar');
   }
}

$Widget[58] = $Column[37]->Widgets->getTable()->create();
$Column[37]->Widgets->add($Widget[58]);
$Widget[58]->identifier = 'reservationCalendar';
$Widget[58]->sort_order = '3';
$Widget[58]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

$Layout[6] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[6]);
$Layout[6]->layout_name = 'shoppingCart';

$Container[33] = $Layout[6]->Containers->getTable()->create();
$Layout[6]->Containers->add($Container[33]);
$Container[33]->sort_order = '1';
$Container[33]->Styles['margin-right']->definition_value = 'auto';
$Container[33]->Styles['margin-left']->definition_value = 'auto';
$Container[33]->Styles['width']->definition_value = '100%';
$Container[33]->Configuration['is_anchor']->configuration_value = '0';
$Container[33]->Configuration['anchor_id']->configuration_value = '0';
$Container[33]->Configuration['id']->configuration_value = '';
$Container[33]->Configuration['shadows']->configuration_value = '[]';
$Container[33]->Configuration['width']->configuration_value = '100';
$Container[33]->Configuration['width_unit']->configuration_value = '%';
$Container[33]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

$Column[38] = $Container[33]->Columns->getTable()->create();
$Container[33]->Columns->add($Column[38]);
$Column[38]->sort_order = '1';
$Column[38]->Styles['width']->definition_value = '100%';
$Column[38]->Configuration['is_anchor']->configuration_value = '0';
$Column[38]->Configuration['anchor_id']->configuration_value = '0';
$Column[38]->Configuration['id']->configuration_value = '';
$Column[38]->Configuration['shadows']->configuration_value = '[]';
$Column[38]->Configuration['width_unit']->configuration_value = '%';
$Column[38]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[38]->Configuration['width']->configuration_value = '100';

if (!isset($Box['shoppingCart'])){
 $Box['shoppingCart'] = $TemplatesInfoboxes->findOneByBoxCode('shoppingCart');
    if (!is_object($Box['shoppingCart']) || $Box['shoppingCart']->count() <= 0){
       installInfobox('includes/modules/infoboxes/shoppingCart/', 'shoppingCart', 'null');
       $Box['shoppingCart'] = $TemplatesInfoboxes->findOneByBoxCode('shoppingCart');
   }
}

$Widget[59] = $Column[38]->Widgets->getTable()->create();
$Column[38]->Widgets->add($Widget[59]);
$Widget[59]->identifier = 'shoppingCart';
$Widget[59]->sort_order = '1';
$Widget[59]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

$Layout[7] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[7]);
$Layout[7]->layout_name = 'anyApp';

$Container[34] = $Layout[7]->Containers->getTable()->create();
$Layout[7]->Containers->add($Container[34]);
$Container[34]->sort_order = '1';
$Container[34]->Styles['margin-right']->definition_value = 'auto';
$Container[34]->Styles['margin-left']->definition_value = 'auto';
$Container[34]->Styles['width']->definition_value = '100%';
$Container[34]->Configuration['width']->configuration_value = '100';
$Container[34]->Configuration['width_unit']->configuration_value = '%';
$Container[34]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[34]->Configuration['is_anchor']->configuration_value = '0';
$Container[34]->Configuration['id']->configuration_value = '';
$Container[34]->Configuration['anchor_id']->configuration_value = '0';
$Container[34]->Configuration['shadows']->configuration_value = '[]';

$Column[39] = $Container[34]->Columns->getTable()->create();
$Container[34]->Columns->add($Column[39]);
$Column[39]->sort_order = '1';
$Column[39]->Styles['width']->definition_value = '100%';
$Column[39]->Configuration['anchor_id']->configuration_value = '0';
$Column[39]->Configuration['width_unit']->configuration_value = '%';
$Column[39]->Configuration['width']->configuration_value = '100';
$Column[39]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[39]->Configuration['id']->configuration_value = '';
$Column[39]->Configuration['is_anchor']->configuration_value = '0';
$Column[39]->Configuration['shadows']->configuration_value = '[]';

if (!isset($Box['pageStack'])){
 $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
    if (!is_object($Box['pageStack']) || $Box['pageStack']->count() <= 0){
       installInfobox('includes/modules/infoboxes/pageStack/', 'pageStack', 'null');
       $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
   }
}

$Widget[61] = $Column[39]->Widgets->getTable()->create();
$Column[39]->Widgets->add($Widget[61]);
$Widget[61]->identifier = 'pageStack';
$Widget[61]->sort_order = '1';
$Widget[61]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

if (!isset($Box['pageContent'])){
 $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
    if (!is_object($Box['pageContent']) || $Box['pageContent']->count() <= 0){
       installInfobox('extensions/templateManager/catalog/infoboxes/pageContent/', 'pageContent', 'templateManager');
       $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
   }
}

$Widget[60] = $Column[39]->Widgets->getTable()->create();
$Column[39]->Widgets->add($Widget[60]);
$Widget[60]->identifier = 'pageContent';
$Widget[60]->sort_order = '2';
$Widget[60]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

$Layout[8] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[8]);
$Layout[8]->layout_name = 'onlyPurchase';

$Container[35] = $Layout[8]->Containers->getTable()->create();
$Layout[8]->Containers->add($Container[35]);
$Container[35]->sort_order = '1';
$Container[35]->Styles['margin-right']->definition_value = 'auto';
$Container[35]->Styles['margin-left']->definition_value = 'auto';
$Container[35]->Styles['width']->definition_value = '100%';
$Container[35]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[35]->Configuration['width_unit']->configuration_value = '%';
$Container[35]->Configuration['width']->configuration_value = '100';

$Column[40] = $Container[35]->Columns->getTable()->create();
$Container[35]->Columns->add($Column[40]);
$Column[40]->sort_order = '1';
$Column[40]->Styles['width']->definition_value = '100%';
$Column[40]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[40]->Configuration['width']->configuration_value = '100';
$Column[40]->Configuration['width_unit']->configuration_value = '%';

if (!isset($Box['productPurchaseTypes'])){
 $Box['productPurchaseTypes'] = $TemplatesInfoboxes->findOneByBoxCode('productPurchaseTypes');
    if (!is_object($Box['productPurchaseTypes']) || $Box['productPurchaseTypes']->count() <= 0){
       installInfobox('includes/modules/infoboxes/productPurchaseTypes/', 'productPurchaseTypes', 'null');
       $Box['productPurchaseTypes'] = $TemplatesInfoboxes->findOneByBoxCode('productPurchaseTypes');
   }
}

$Widget[62] = $Column[40]->Widgets->getTable()->create();
$Column[40]->Widgets->add($Widget[62]);
$Widget[62]->identifier = 'productPurchaseTypes';
$Widget[62]->sort_order = '1';
$Widget[62]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""},"widgetHeader":"false","useQty":"false","showPrice":"true","showButton":"true","showQtyDiscounts":"false","showNew":"true","showUsed":"false","showDownload":"false","showStream":"false","showRental":"false","showReservation":"false"}';

$Layout[9] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[9]);
$Layout[9]->layout_name = 'MyAccount';

$Container[36] = $Layout[9]->Containers->getTable()->create();
$Layout[9]->Containers->add($Container[36]);
$Container[36]->sort_order = '1';
$Container[36]->Styles['margin-right']->definition_value = 'auto';
$Container[36]->Styles['margin-left']->definition_value = 'auto';
$Container[36]->Styles['width']->definition_value = '100%';
$Container[36]->Configuration['width']->configuration_value = '100';
$Container[36]->Configuration['width_unit']->configuration_value = '%';
$Container[36]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

$Column[41] = $Container[36]->Columns->getTable()->create();
$Container[36]->Columns->add($Column[41]);
$Column[41]->sort_order = '1';
$Column[41]->Styles['width']->definition_value = '100%';
$Column[41]->Configuration['width']->configuration_value = '100';
$Column[41]->Configuration['width_unit']->configuration_value = '%';
$Column[41]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

if (!isset($Box['pageStack'])){
 $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
    if (!is_object($Box['pageStack']) || $Box['pageStack']->count() <= 0){
       installInfobox('includes/modules/infoboxes/pageStack/', 'pageStack', 'null');
       $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
   }
}

$Widget[63] = $Column[41]->Widgets->getTable()->create();
$Column[41]->Widgets->add($Widget[63]);
$Widget[63]->identifier = 'pageStack';
$Widget[63]->sort_order = '1';
$Widget[63]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

if (!isset($Box['pageContent'])){
 $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
    if (!is_object($Box['pageContent']) || $Box['pageContent']->count() <= 0){
       installInfobox('extensions/templateManager/catalog/infoboxes/pageContent/', 'pageContent', 'templateManager');
       $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
   }
}

$Widget[64] = $Column[41]->Widgets->getTable()->create();
$Column[41]->Widgets->add($Widget[64]);
$Widget[64]->identifier = 'pageContent';
$Widget[64]->sort_order = '2';
$Widget[64]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

$Layout[10] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[10]);
$Layout[10]->layout_name = 'menuleft';

$Container[37] = $Layout[10]->Containers->getTable()->create();
$Layout[10]->Containers->add($Container[37]);
$Container[37]->sort_order = '1';
$Container[37]->Styles['margin-right']->definition_value = 'auto';
$Container[37]->Styles['margin-left']->definition_value = 'auto';
$Container[37]->Styles['width']->definition_value = '100%';
$Container[37]->Configuration['width_unit']->configuration_value = '%';
$Container[37]->Configuration['width']->configuration_value = '100';
$Container[37]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

$Column[42] = $Container[37]->Columns->getTable()->create();
$Container[37]->Columns->add($Column[42]);
$Column[42]->sort_order = '1';
$Column[42]->Styles['width']->definition_value = '100%';
$Column[42]->Configuration['width']->configuration_value = '100';
$Column[42]->Configuration['width_unit']->configuration_value = '%';
$Column[42]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

if (!isset($Box['categories'])){
 $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
    if (!is_object($Box['categories']) || $Box['categories']->count() <= 0){
       installInfobox('includes/modules/infoboxes/categories/', 'categories', 'null');
       $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
   }
}

$Widget[65] = $Column[42]->Widgets->getTable()->create();
$Column[42]->Widgets->add($Widget[65]);
$Widget[65]->identifier = 'categories';
$Widget[65]->sort_order = '1';
$Widget[65]->Configuration['widget_settings']->configuration_value = '{}';
$Template->save();
addLayoutToPage('product', 'info.php', null, $Layout[5]->layout_id);
addLayoutToPage('product', '12', null, $Layout[5]->layout_id);
addLayoutToPage('product', '16', null, $Layout[5]->layout_id);
addLayoutToPage('shoppingCart', 'default.php', null, $Layout[6]->layout_id);
addLayoutToPage('index', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'address_book.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'create.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'create_success.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'credit_card_details.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'edit.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'history.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'history_info.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'login.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'logoff.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'membership.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'membership_cancel.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'membership_info.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'membership_upgrade.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'newsletters.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'password.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'password_forgotten.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'rental_issues.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'rented_products.php', null, $Layout[7]->layout_id);
addLayoutToPage('billing_address_process', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('center_address_check', 'center_address_check.php', null, $Layout[7]->layout_id);
addLayoutToPage('checkout', 'addresses.php', null, $Layout[7]->layout_id);
addLayoutToPage('checkout', 'cart.php', null, $Layout[7]->layout_id);
addLayoutToPage('checkout', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('checkout', 'shipping_payment.php', null, $Layout[7]->layout_id);
addLayoutToPage('checkout', 'success.php', null, $Layout[7]->layout_id);
addLayoutToPage('contact_us', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('contact_us', 'success.php', null, $Layout[7]->layout_id);
addLayoutToPage('funways', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('gv_redeem', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'products.php', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'index.php', null, $Layout[7]->layout_id);
addLayoutToPage('product', 'info.php', null, $Layout[7]->layout_id);
addLayoutToPage('product', 'reviews.php', null, $Layout[7]->layout_id);
addLayoutToPage('products', 'all.php', null, $Layout[7]->layout_id);
addLayoutToPage('products', 'best_sellers.php', null, $Layout[7]->layout_id);
addLayoutToPage('products', 'featured.php', null, $Layout[7]->layout_id);
addLayoutToPage('products', 'new.php', null, $Layout[7]->layout_id);
addLayoutToPage('products', 'search.php', null, $Layout[7]->layout_id);
addLayoutToPage('products', 'search_result.php', null, $Layout[7]->layout_id);
addLayoutToPage('products', 'upcoming.php', null, $Layout[7]->layout_id);
addLayoutToPage('recent_additions', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('redirect', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('rentals', 'queue.php', null, $Layout[7]->layout_id);
addLayoutToPage('rentals', 'top.php', null, $Layout[7]->layout_id);
addLayoutToPage('shoppingCart', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('tell_a_friend', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('viewStream', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('viewStream', 'pull.php', null, $Layout[7]->layout_id);
addLayoutToPage('show', 'default.php', 'articleManager', $Layout[7]->layout_id);
addLayoutToPage('show', 'info.php', 'articleManager', $Layout[7]->layout_id);
addLayoutToPage('show', 'new.php', 'articleManager', $Layout[7]->layout_id);
addLayoutToPage('show', 'rss.php', 'articleManager', $Layout[7]->layout_id);
addLayoutToPage('show_archive', 'default.php', 'blog', $Layout[7]->layout_id);
addLayoutToPage('show_post', 'default.php', 'blog', $Layout[7]->layout_id);
addLayoutToPage('show_page', 'default.php', 'categoriesPages', $Layout[7]->layout_id);
addLayoutToPage('simpleDownload', 'default.php', 'customFields', $Layout[7]->layout_id);
addLayoutToPage('show_page', 'default.php', 'infoPages', $Layout[7]->layout_id);
addLayoutToPage('center_address_check', 'default.php', 'inventoryCenters', $Layout[7]->layout_id);
addLayoutToPage('show_inventory', 'default.php', 'inventoryCenters', $Layout[7]->layout_id);
addLayoutToPage('show_inventory', 'delivery.php', 'inventoryCenters', $Layout[7]->layout_id);
addLayoutToPage('show_inventory', 'list.php', 'inventoryCenters', $Layout[7]->layout_id);
addLayoutToPage('account_addon', 'history_inventory_info.php', 'inventoryCenters', $Layout[7]->layout_id);
addLayoutToPage('account_addon', 'view_orders_inventory.php', 'inventoryCenters', $Layout[7]->layout_id);
addLayoutToPage('show_shipping', 'default.php', 'payPerRentals', $Layout[7]->layout_id);
addLayoutToPage('build_reservation', 'default.php', 'payPerRentals', $Layout[7]->layout_id);
addLayoutToPage('show_event', 'default.php', 'payPerRentals', $Layout[7]->layout_id);
addLayoutToPage('show_event', 'list.php', 'payPerRentals', $Layout[7]->layout_id);
addLayoutToPage('product_review', 'default.php', 'reviews', $Layout[7]->layout_id);
addLayoutToPage('product_review', 'details.php', 'reviews', $Layout[7]->layout_id);
addLayoutToPage('product_review', 'write.php', 'reviews', $Layout[7]->layout_id);
addLayoutToPage('show_specials', 'default.php', 'specials', $Layout[7]->layout_id);
addLayoutToPage('account', 'address_book_details.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'address_book_process.php', null, $Layout[7]->layout_id);
addLayoutToPage('show_category', 'default.php', 'blog', $Layout[7]->layout_id);
addLayoutToPage('show_category', 'rss.php', 'blog', $Layout[7]->layout_id);
addLayoutToPage('index', 'xccxxcqqq11', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'wewewqqw', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'mini-short', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'men', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'girls', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'boxer-short', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'boardshorts', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'success_outside.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'billing_address_book.php', null, $Layout[7]->layout_id);
addLayoutToPage('account', 'create_rental.php', null, $Layout[7]->layout_id);
addLayoutToPage('show_js', 'default.php', 'codeIntegration', $Layout[7]->layout_id);
addLayoutToPage('show_php', 'default.php', 'codeIntegration', $Layout[7]->layout_id);
addLayoutToPage('show_page', 'aboutus', 'infoPages', $Layout[7]->layout_id);
addLayoutToPage('show_page', 'conditions', 'infoPages', $Layout[7]->layout_id);
addLayoutToPage('show_page', 'cookie_usage', 'infoPages', $Layout[7]->layout_id);
addLayoutToPage('show_page', 'gv_faq', 'infoPages', $Layout[7]->layout_id);
addLayoutToPage('show_page', 'help_download', 'infoPages', $Layout[7]->layout_id);
addLayoutToPage('show_page', 'help_stream', 'infoPages', $Layout[7]->layout_id);
addLayoutToPage('show_page', 'maintenance_page', 'infoPages', $Layout[7]->layout_id);
addLayoutToPage('show_page', 'ssl_check', 'infoPages', $Layout[7]->layout_id);
addLayoutToPage('show_page', 'store_locations', 'infoPages', $Layout[7]->layout_id);
addLayoutToPage('generate_pdf', 'default.php', 'pdfPrinter', $Layout[7]->layout_id);
addLayoutToPage('product', 'default.php', null, $Layout[7]->layout_id);
addLayoutToPage('product', '20', null, $Layout[7]->layout_id);
addLayoutToPage('product', '12', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'boxer', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'cotton', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'microfiber', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'swimwear', null, $Layout[7]->layout_id);
addLayoutToPage('index', 'underwear', null, $Layout[7]->layout_id);
addLayoutToPage('products', 'top_rentals.php', null, $Layout[7]->layout_id);
addLayoutToPage('gift_certificates_transactions', 'default.php', 'giftCertificates', $Layout[7]->layout_id);
addLayoutToPage('gift_certificates_send', 'default.php', 'giftCertificates', $Layout[7]->layout_id);
addLayoutToPage('gift_certificates_send', 'redeem.php', 'giftCertificates', $Layout[7]->layout_id);
addLayoutToPage('banner_actions', 'default.php', 'imageRot', $Layout[7]->layout_id);
addLayoutToPage('show_store', 'default.php', 'multiStore', $Layout[7]->layout_id);
addLayoutToPage('show_store', 'list.php', 'multiStore', $Layout[7]->layout_id);
addLayoutToPage('zip', 'default.php', 'multiStore', $Layout[7]->layout_id);
addLayoutToPage('show_shipping', 'default_all.php', 'payPerRentals', $Layout[7]->layout_id);
addLayoutToPage('product', '16', null, $Layout[7]->layout_id);
addLayoutToPage('product', '15', null, $Layout[7]->layout_id);
addLayoutToPage('product', 'info.php', null, $Layout[8]->layout_id);
addLayoutToPage('account', 'default.php', null, $Layout[9]->layout_id);
