<?php
$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->create();
$Template->Configuration['STYLESHEET_COMPRESSION']->configuration_value = 'none';
$Template->Configuration['DIRECTORY']->configuration_value = 'newred';
$Template->Configuration['NAME']->configuration_value = 'Newred';
$Template->Configuration['JAVASCRIPT_COMPRESSION']->configuration_value = 'none';

$Layout[1] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[1]);
$Layout[1]->layout_name = 'Home Page';
$Layout[1]->Styles['font']->definition_value = '{"family":"Arial","size":"12","size_unit":"px","style":"normal","variant":"normal","weight":"normal"}';
$Layout[1]->Styles['width']->definition_value = 'auto';
$Layout[1]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Layout[1]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"240","g":"240","b":"240","a":1},"position":"0"},{"color":{"r":"178","g":"178","b":"179","a":1},"position":"1"}]}';
$Layout[1]->Configuration['font']->configuration_value = '{"family":"Arial","size":"12","size_unit":"px","style":"normal","variant":"normal","weight":"normal"}';
$Layout[1]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Layout[1]->Configuration['shadows']->configuration_value = '[]';
$Layout[1]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Layout[1]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"240","start_color_g":"240","start_color_b":"240","start_color_a":"100","end_color_r":"178","end_color_g":"178","end_color_b":"179","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Layout[1]->Configuration['id']->configuration_value = '';
$Layout[1]->Configuration['width']->configuration_value = '1024';
$Layout[1]->Configuration['width_unit']->configuration_value = 'auto';

$Container[1] = $Layout[1]->Containers->getTable()->create();
$Layout[1]->Containers->add($Container[1]);
$Container[1]->sort_order = '1';
$Container[1]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"121","g":"14","b":"8","a":1},"position":"0"},{"color":{"r":"182","g":"14","b":"8","a":1},"position":"1"}]}';
$Container[1]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[1]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"14","border_top_left_radius_unit":"px","border_top_right_radius":"14","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Container[1]->Styles['width']->definition_value = 'auto';
$Container[1]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[1]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[1]->Styles['margin']->definition_value = '{"top":"10","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[1]->Styles['border']->definition_value = '{"top":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Container[1]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[1]->Configuration['margin']->configuration_value = '{"top":"10","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[1]->Configuration['width']->configuration_value = '98';
$Container[1]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"121","start_color_g":"14","start_color_b":"8","start_color_a":"100","end_color_r":"182","end_color_g":"14","end_color_b":"8","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[1]->Configuration['shadows']->configuration_value = '[]';
$Container[1]->Configuration['border']->configuration_value = '{"top":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Container[1]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[1]->Configuration['width_unit']->configuration_value = 'auto';
$Container[1]->Configuration['id']->configuration_value = 'theContainer0';
$Container[1]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[1]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":"14","border_top_left_radius_unit":"px","border_top_right_radius":"14","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Container[1]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';

$Column[1] = $Container[1]->Columns->getTable()->create();
$Container[1]->Columns->add($Column[1]);
$Column[1]->sort_order = '1';
$Column[1]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[1]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[1]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"2","left_unit":"%"}';
$Column[1]->Styles['width']->definition_value = '58%';
$Column[1]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[1]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[1]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"2","left_unit":"%"}';
$Column[1]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[1]->Configuration['width_unit']->configuration_value = '%';
$Column[1]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[1]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[1]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[1]->Configuration['width']->configuration_value = '58';
$Column[1]->Configuration['shadows']->configuration_value = '[]';
$Column[1]->Configuration['id']->configuration_value = 'theContainer4';
$Column[1]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[1] = $Column[1]->Widgets->getTable()->create();
$Column[1]->Widgets->add($Widget[1]);
$Widget[1]->identifier = 'customImage';
$Widget[1]->sort_order = '1';
$Widget[1]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":""},"image_source":"/templates/newred/images/logo.png"}';

$Column[2] = $Container[1]->Columns->getTable()->create();
$Container[1]->Columns->add($Column[2]);
$Column[2]->sort_order = '2';
$Column[2]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[2]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[2]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[2]->Styles['width']->definition_value = '40%';
$Column[2]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[2]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[2]->Configuration['id']->configuration_value = 'theContainer5';
$Column[2]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[2]->Configuration['width_unit']->configuration_value = '%';
$Column[2]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[2]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[2]->Configuration['shadows']->configuration_value = '[]';
$Column[2]->Configuration['width']->configuration_value = '40';
$Column[2]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[2] = $Column[2]->Widgets->getTable()->create();
$Column[2]->Widgets->add($Widget[2]);
$Widget[2]->identifier = 'navigationMenu';
$Widget[2]->sort_order = '1';
$Widget[2]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":"Home"},"49":{"text":"Home"},"50":{"text":"Home"},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"Register"},"48":{"text":"Register"},"49":{"text":"Register"},"50":{"text":"Register"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"create","target":"same"},"condition":"customer_not_logged_in","children":[]},"4":{"1":{"text":"My Account"},"48":{"text":"My Account"},"49":{"text":"My Account"},"50":{"text":"My Account"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"customer_logged_in","children":[]},"6":{"1":{"text":"Sign In"},"48":{"text":"Sign In"},"49":{"text":"Sign In"},"50":{"text":"Sign In"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"login","target":"same"},"condition":"customer_not_logged_in","children":[]},"8":{"1":{"text":"Sign Out"},"48":{"text":"Sign Out"},"49":{"text":"Sign Out"},"50":{"text":"Sign Out"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"logoff","target":"same"},"condition":"customer_logged_in","children":[]}},"menuId":"headerMiniMenu","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[3] = $Column[2]->Widgets->getTable()->create();
$Column[2]->Widgets->add($Widget[3]);
$Widget[3]->identifier = 'customPHP';
$Widget[3]->sort_order = '2';
$Widget[3]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""},"php_text":"<div id=\"headerShoppingCart\"><span class=\"shopIcon\"></span><' . '?php \r\nglobal $ShoppingCart;\r\n\t$shoppingProducts = $ShoppingCart->getProducts();\r\n    if(isset($shoppingProducts) && sizeof($shoppingProducts) >0){\r\n\t    echo \'<a href=\"\'.itw_app_link(null,\'shoppingCart\',\'default\').\'\">\'. sprintf(sysLanguage::get(\'HEADER_TEXT_SHOPPING_CART_ITEMS\'), sizeof($shoppingProducts)) . \'</a>\';\r\n\t}else{\r\n\t   echo sprintf(sysLanguage::get(\'HEADER_TEXT_SHOPPING_CART_ITEMS\'), 0);\r\n\t}\r\n ?' . '></div>\r\n"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[4] = $Column[2]->Widgets->getTable()->create();
$Column[2]->Widgets->add($Widget[4]);
$Widget[4]->identifier = 'customPHP';
$Widget[4]->sort_order = '3';
$Widget[4]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""},"php_text":"<div id=\"headerRentalQueue\"><span class=\"rentIcon\"></span><' . '?php\r\nglobal $rentalQueue;\r\n\t\tif (sysConfig::get(\'ALLOW_RENTALS\') == \'true\'){\r\n\t\t\t$cart_contents_string = \'\';\r\n\t\t\tif ($rentalQueue->count_contents() > 0) {\r\n\t\t\t\t  echo \'<a href=\"\'.itw_app_link(null,\'rentals\',\'queue\').\'\">\'.  sprintf(sysLanguage::get(\'HEADER_TEXT_RENTAL_QUEUE_ITEMS\'), $rentalQueue->count_contents()) . \'</a>\';\r\n\t\t\t}else{\r\n\t\t\t\techo sprintf(sysLanguage::get(\'HEADER_TEXT_RENTAL_QUEUE_ITEMS\'), 0);\r\n\t\t\t}\r\n\t\t}\r\n?' . '></div>\r\n"}';

$Column[3] = $Container[1]->Columns->getTable()->create();
$Container[1]->Columns->add($Column[3]);
$Column[3]->sort_order = '3';
$Column[3]->Styles['width']->definition_value = '59%';
$Column[3]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[3]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[3]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[3]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"1","left_unit":"%"}';
$Column[3]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"bottom","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[3]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"1","left_unit":"%"}';
$Column[3]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[3]->Configuration['id']->configuration_value = 'theContainer6';
$Column[3]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"bottom","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[3]->Configuration['width']->configuration_value = '59';
$Column[3]->Configuration['width_unit']->configuration_value = '%';
$Column[3]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[3]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[3]->Configuration['shadows']->configuration_value = '[]';
$Column[3]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[5] = $Column[3]->Widgets->getTable()->create();
$Column[3]->Widgets->add($Widget[5]);
$Widget[5]->identifier = 'navigationMenu';
$Widget[5]->sort_order = '1';
$Widget[5]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":"Home"},"49":{"text":"Home"},"50":{"text":"Home"},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"All Products"},"48":{"text":"All Products"},"49":{"text":"All Products"},"50":{"text":"All Products"},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Featured"},"48":{"text":"Featured"},"49":{"text":"Featured"},"50":{"text":"Featured"},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Free Trial"},"48":{"text":"Free Trial"},"49":{"text":"Free Trial"},"50":{"text":"Free Trial"},"icon":"none","icon_src":"","link":{"type":"custom","url":"checkout_rental_account.php","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Blog"},"48":{"text":"Blog"},"49":{"text":"Blog"},"50":{"text":"Blog"},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_category","page":"default","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":"Contact Us"},"49":{"text":"Contact Us"},"50":{"text":"Contact Us"},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"mainNavigationMenu","forceFit":"false"}';

$Column[4] = $Container[1]->Columns->getTable()->create();
$Container[1]->Columns->add($Column[4]);
$Column[4]->sort_order = '4';
$Column[4]->Styles['width']->definition_value = '40%';
$Column[4]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[4]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[4]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"bottom","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[4]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[4]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"bottom","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[4]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[4]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[4]->Configuration['shadows']->configuration_value = '[]';
$Column[4]->Configuration['width']->configuration_value = '40';
$Column[4]->Configuration['id']->configuration_value = 'theContainer7';
$Column[4]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[4]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[6] = $Column[4]->Widgets->getTable()->create();
$Column[4]->Widgets->add($Widget[6]);
$Widget[6]->identifier = 'customPHP';
$Widget[6]->sort_order = '1';
$Widget[6]->Configuration['widget_settings']->configuration_value = '{"php_text":"<div id=\"headerSearch\">\n\t<form style=\"\" name=\"search\" action=\"<' . '?php echo itw_app_link(null, \'products\', \'search_result\');?' . '>\" method=\"get\">\n\t\t<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:300px;margin-bottom:0 !important;\">\n\t\t\t<tr>\n\t\t\t\t<td><' . '?php echo sysLanguage::get(\'HEADER_NAV_TEXT_SEARCH\');?' . '></td>\n\t\t\t\t<td><' . '?php echo tep_draw_input_field(\'keywords\', \'\', \'style=\"height:20px;\"\') . tep_hide_session_id();?' . '></td>\n\t\t\t\t<td><' . '?php echo htmlBase::newElement(\'button\')->setType(\'submit\')->setText(\'GO\')->draw();?' . '></td>\n\t\t\t</tr>\n\t\t</table>\n\t</form>\n</div>","template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

$Container[2] = $Layout[1]->Containers->getTable()->create();
$Layout[1]->Containers->add($Container[2]);
$Container[2]->sort_order = '2';
$Container[2]->Styles['width']->definition_value = 'auto';
$Container[2]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[2]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[2]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[2]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[2]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[2]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"255","g":"255","b":"255","a":1},"position":"0"},{"color":{"r":"208","g":"207","b":"208","a":1},"position":"1"}]}';
$Container[2]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[2]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[2]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[2]->Configuration['width_unit']->configuration_value = 'auto';
$Container[2]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[2]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[2]->Configuration['width']->configuration_value = '99';
$Container[2]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"255","start_color_g":"255","start_color_b":"255","start_color_a":"100","end_color_r":"208","end_color_g":"207","end_color_b":"208","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[2]->Configuration['id']->configuration_value = 'theContainer1';
$Container[2]->Configuration['shadows']->configuration_value = '[]';
$Container[2]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

$Column[5] = $Container[2]->Columns->getTable()->create();
$Container[2]->Columns->add($Column[5]);
$Column[5]->sort_order = '1';
$Column[5]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[5]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[5]->Styles['width']->definition_value = '80%';
$Column[5]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"3","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[5]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[5]->Configuration['shadows']->configuration_value = '[]';
$Column[5]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[5]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[5]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"3","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[5]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[5]->Configuration['id']->configuration_value = 'theContainer8';
$Column[5]->Configuration['width']->configuration_value = '80';
$Column[5]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';

if (!isset($Box['breadcrumb'])){
 $Box['breadcrumb'] = $TemplatesInfoboxes->findOneByBoxCode('breadcrumb');
    if (!is_object($Box['breadcrumb']) || $Box['breadcrumb']->count() <= 0){
       installInfobox('includes/modules/infoboxes/breadcrumb/', 'breadcrumb', 'null');
       $Box['breadcrumb'] = $TemplatesInfoboxes->findOneByBoxCode('breadcrumb');
   }
}

$Widget[7] = $Column[5]->Widgets->getTable()->create();
$Column[5]->Widgets->add($Widget[7]);
$Widget[7]->identifier = 'breadcrumb';
$Widget[7]->sort_order = '1';
$Widget[7]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

$Column[6] = $Container[2]->Columns->getTable()->create();
$Container[2]->Columns->add($Column[6]);
$Column[6]->sort_order = '2';
$Column[6]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[6]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[6]->Styles['width']->definition_value = '20%';
$Column[6]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[6]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[6]->Configuration['id']->configuration_value = 'theContainer9';
$Column[6]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[6]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[6]->Configuration['shadows']->configuration_value = '[]';
$Column[6]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[6]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[6]->Configuration['width']->configuration_value = '20';
$Column[6]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';

if (!isset($Box['currencies'])){
 $Box['currencies'] = $TemplatesInfoboxes->findOneByBoxCode('currencies');
    if (!is_object($Box['currencies']) || $Box['currencies']->count() <= 0){
       installInfobox('includes/modules/infoboxes/currencies/', 'currencies', 'null');
       $Box['currencies'] = $TemplatesInfoboxes->findOneByBoxCode('currencies');
   }
}

$Widget[8] = $Column[6]->Widgets->getTable()->create();
$Column[6]->Widgets->add($Widget[8]);
$Widget[8]->identifier = 'currencies';
$Widget[8]->sort_order = '1';
$Widget[8]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Container[3] = $Layout[1]->Containers->getTable()->create();
$Layout[1]->Containers->add($Container[3]);
$Container[3]->sort_order = '3';
$Container[3]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[3]->Styles['background_solid']->definition_value = '{"background_r":"250","background_g":"245","background_b":"250","background_a":"100"}';
$Container[3]->Styles['padding']->definition_value = '{"top":"10","top_unit":"px","right":"0","right_unit":"px","bottom":"10","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[3]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[3]->Styles['width']->definition_value = 'auto';
$Container[3]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[3]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[3]->Configuration['padding']->configuration_value = '{"top":"10","top_unit":"px","right":"0","right_unit":"px","bottom":"10","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[3]->Configuration['id']->configuration_value = 'theContainer2';
$Container[3]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Container[3]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[3]->Configuration['width']->configuration_value = '99';
$Container[3]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[3]->Configuration['width_unit']->configuration_value = 'auto';
$Container[3]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[3]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"250","background_g":"245","background_b":"250","background_a":"100"}}}}';
$Container[3]->Configuration['shadows']->configuration_value = '[]';
$Container[3]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';

$Column[7] = $Container[3]->Columns->getTable()->create();
$Container[3]->Columns->add($Column[7]);
$Column[7]->sort_order = '1';
$Column[7]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[7]->Styles['width']->definition_value = '18%';
$Column[7]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[7]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"1","right_unit":"%","bottom":"0","bottom_unit":"px","left":".5","left_unit":"%"}';
$Column[7]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[7]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[7]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[7]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"1","right_unit":"%","bottom":"0","bottom_unit":"px","left":".5","left_unit":"%"}';
$Column[7]->Configuration['width_unit']->configuration_value = '%';
$Column[7]->Configuration['width']->configuration_value = '18';
$Column[7]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[7]->Configuration['shadows']->configuration_value = '[]';
$Column[7]->Configuration['id']->configuration_value = 'theContainer10';
$Column[7]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';

if (!isset($Box['categories'])){
 $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
    if (!is_object($Box['categories']) || $Box['categories']->count() <= 0){
       installInfobox('includes/modules/infoboxes/categories/', 'categories', 'null');
       $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
   }
}

$Widget[9] = $Column[7]->Widgets->getTable()->create();
$Column[7]->Widgets->add($Widget[9]);
$Widget[9]->identifier = 'categories';
$Widget[9]->sort_order = '1';
$Widget[9]->Configuration['widget_settings']->configuration_value = '{"id":"categoriesInfoBox","template_file":"box.tpl","widget_title":{"1":"Categories","48":"Categories","49":"Categories","50":"Categories"}}';

if (!isset($Box['loginBox'])){
 $Box['loginBox'] = $TemplatesInfoboxes->findOneByBoxCode('loginBox');
    if (!is_object($Box['loginBox']) || $Box['loginBox']->count() <= 0){
       installInfobox('includes/modules/infoboxes/loginBox/', 'loginBox', 'null');
       $Box['loginBox'] = $TemplatesInfoboxes->findOneByBoxCode('loginBox');
   }
}

$Widget[10] = $Column[7]->Widgets->getTable()->create();
$Column[7]->Widgets->add($Widget[10]);
$Widget[10]->identifier = 'loginBox';
$Widget[10]->sort_order = '2';
$Widget[10]->Configuration['widget_settings']->configuration_value = '{}';

$Column[8] = $Container[3]->Columns->getTable()->create();
$Container[3]->Columns->add($Column[8]);
$Column[8]->sort_order = '2';
$Column[8]->Styles['width']->definition_value = '61%';
$Column[8]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[8]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[8]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[8]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[8]->Configuration['width']->configuration_value = '61';
$Column[8]->Configuration['id']->configuration_value = 'theContainer11';
$Column[8]->Configuration['shadows']->configuration_value = '[]';
$Column[8]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[8]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[8]->Configuration['width_unit']->configuration_value = '%';
$Column[8]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

if (!isset($Box['pageStack'])){
 $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
    if (!is_object($Box['pageStack']) || $Box['pageStack']->count() <= 0){
       installInfobox('includes/modules/infoboxes/pageStack/', 'pageStack', 'null');
       $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
   }
}

$Widget[11] = $Column[8]->Widgets->getTable()->create();
$Column[8]->Widgets->add($Widget[11]);
$Widget[11]->identifier = 'pageStack';
$Widget[11]->sort_order = '1';
$Widget[11]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

if (!isset($Box['customScroller'])){
 $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
    if (!is_object($Box['customScroller']) || $Box['customScroller']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customScroller/', 'customScroller', 'null');
       $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
   }
}

$Widget[12] = $Column[8]->Widgets->getTable()->create();
$Column[8]->Widgets->add($Widget[12]);
$Widget[12]->identifier = 'customScroller';
$Widget[12]->sort_order = '2';
$Widget[12]->Configuration['widget_settings']->configuration_value = '{"id":"indexButtonScroller","template_file":"box.tpl","widget_title":{"1":""},"scrollers":{"type":"buttons","configs":[{"headings":{"1":"Featured Products"},"rows":"1","query":"featured","selected_category":"","query_limit":"25","show_product_name":false,"reflect_blocks":true,"block_width":"200","block_height":"200","prev_image":"/templates/newred/images/scroller_prev.png","next_image":"/templates/newred/images/scroller_next.png"},{"headings":{"1":"Best Sellers"},"rows":"1","query":"best_sellers","selected_category":"","query_limit":"25","show_product_name":false,"reflect_blocks":true,"block_width":"200","block_height":"200","prev_image":"/templates/newred/images/scroller_prev.png","next_image":"/templates/newred/images/scroller_next.png"},{"headings":{"1":"New Products"},"rows":"1","query":"new_products","selected_category":"","query_limit":"25","show_product_name":false,"reflect_blocks":true,"block_width":"200","block_height":"200","prev_image":"/templates/newred/images/scroller_prev.png","next_image":"/templates/newred/images/scroller_next.png"}]}}';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[13] = $Column[8]->Widgets->getTable()->create();
$Column[8]->Widgets->add($Widget[13]);
$Widget[13]->identifier = 'customText';
$Widget[13]->sort_order = '3';
$Widget[13]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"How It Works","48":"How It Works","49":"How It Works","50":"How It Works"},"selected_page":"4","id":""}';

$Column[9] = $Container[3]->Columns->getTable()->create();
$Container[3]->Columns->add($Column[9]);
$Column[9]->sort_order = '3';
$Column[9]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[9]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[9]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[9]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":".5","right_unit":"%","bottom":"0","bottom_unit":"px","left":"1","left_unit":"%"}';
$Column[9]->Styles['width']->definition_value = '18%';
$Column[9]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[9]->Configuration['id']->configuration_value = 'theContainer12';
$Column[9]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":".5","right_unit":"%","bottom":"0","bottom_unit":"px","left":"1","left_unit":"%"}';
$Column[9]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[9]->Configuration['width']->configuration_value = '18';
$Column[9]->Configuration['shadows']->configuration_value = '[]';
$Column[9]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[9]->Configuration['width_unit']->configuration_value = '%';
$Column[9]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

if (!isset($Box['shoppingCart'])){
 $Box['shoppingCart'] = $TemplatesInfoboxes->findOneByBoxCode('shoppingCart');
    if (!is_object($Box['shoppingCart']) || $Box['shoppingCart']->count() <= 0){
       installInfobox('includes/modules/infoboxes/shoppingCart/', 'shoppingCart', 'null');
       $Box['shoppingCart'] = $TemplatesInfoboxes->findOneByBoxCode('shoppingCart');
   }
}

$Widget[14] = $Column[9]->Widgets->getTable()->create();
$Column[9]->Widgets->add($Widget[14]);
$Widget[14]->identifier = 'shoppingCart';
$Widget[14]->sort_order = '1';
$Widget[14]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Shopping Cart"}}';

if (!isset($Box['bestSellers'])){
 $Box['bestSellers'] = $TemplatesInfoboxes->findOneByBoxCode('bestSellers');
    if (!is_object($Box['bestSellers']) || $Box['bestSellers']->count() <= 0){
       installInfobox('includes/modules/infoboxes/bestSellers/', 'bestSellers', 'null');
       $Box['bestSellers'] = $TemplatesInfoboxes->findOneByBoxCode('bestSellers');
   }
}

$Widget[15] = $Column[9]->Widgets->getTable()->create();
$Column[9]->Widgets->add($Widget[15]);
$Widget[15]->identifier = 'bestSellers';
$Widget[15]->sort_order = '2';
$Widget[15]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Best Sellers"}}';

if (!isset($Box['infoPages'])){
 $Box['infoPages'] = $TemplatesInfoboxes->findOneByBoxCode('infoPages');
    if (!is_object($Box['infoPages']) || $Box['infoPages']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/infoPages/', 'infoPages', 'infoPages');
       $Box['infoPages'] = $TemplatesInfoboxes->findOneByBoxCode('infoPages');
   }
}

$Widget[16] = $Column[9]->Widgets->getTable()->create();
$Column[9]->Widgets->add($Widget[16]);
$Widget[16]->identifier = 'infoPages';
$Widget[16]->sort_order = '3';
$Widget[16]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Information"}}';

$Container[4] = $Layout[1]->Containers->getTable()->create();
$Layout[1]->Containers->add($Container[4]);
$Container[4]->sort_order = '4';
$Container[4]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[4]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[4]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[4]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"10","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[4]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[4]->Styles['width']->definition_value = 'auto';
$Container[4]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"114","g":"13","b":"4","a":1},"position":"0"},{"color":{"r":"195","g":"13","b":"4","a":1},"position":"1"}]}';
$Container[4]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[4]->Configuration['width_unit']->configuration_value = 'auto';
$Container[4]->Configuration['width']->configuration_value = '99';
$Container[4]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"10","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[4]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[4]->Configuration['shadows']->configuration_value = '[]';
$Container[4]->Configuration['id']->configuration_value = 'theContainer3';
$Container[4]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"114","start_color_g":"13","start_color_b":"4","start_color_a":"100","end_color_r":"195","end_color_g":"13","end_color_b":"4","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[4]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[4]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[4]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';

$Column[10] = $Container[4]->Columns->getTable()->create();
$Container[4]->Columns->add($Column[10]);
$Column[10]->sort_order = '1';
$Column[10]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[10]->Styles['width']->definition_value = '100%';
$Column[10]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"bold"}';
$Column[10]->Styles['text']->definition_value = '{"color":"#ebb93c","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[10]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[10]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"6","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[10]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"6","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[10]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[10]->Configuration['text']->configuration_value = '{"color":"#ebb93c","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[10]->Configuration['id']->configuration_value = 'theContainer13';
$Column[10]->Configuration['width']->configuration_value = '100';
$Column[10]->Configuration['shadows']->configuration_value = '[]';
$Column[10]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[10]->Configuration['width_unit']->configuration_value = '%';
$Column[10]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"bold"}';
$Column[10]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[17] = $Column[10]->Widgets->getTable()->create();
$Column[10]->Widgets->add($Widget[17]);
$Widget[17]->identifier = 'navigationMenu';
$Widget[17]->sort_order = '1';
$Widget[17]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"linked_to":"5","menuId":"footerMenu","forceFit":"false"}';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[18] = $Column[10]->Widgets->getTable()->create();
$Column[10]->Widgets->add($Widget[18]);
$Widget[18]->identifier = 'customText';
$Widget[18]->sort_order = '2';
$Widget[18]->Configuration['widget_settings']->configuration_value = '{"selected_page":"12","template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

$Layout[2] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[2]);
$Layout[2]->layout_name = 'allpages';
$Layout[2]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"240","g":"240","b":"240","a":1},"position":"0"},{"color":{"r":"178","g":"178","b":"179","a":1},"position":"1"}]}';
$Layout[2]->Styles['width']->definition_value = 'auto';
$Layout[2]->Styles['font']->definition_value = '{"family":"Arial","size":"12","size_unit":"px","style":"normal","variant":"normal","weight":"normal"}';
$Layout[2]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Layout[2]->Configuration['width']->configuration_value = '1024';
$Layout[2]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"240","start_color_g":"240","start_color_b":"240","start_color_a":"100","end_color_r":"178","end_color_g":"178","end_color_b":"179","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Layout[2]->Configuration['id']->configuration_value = '';
$Layout[2]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Layout[2]->Configuration['width_unit']->configuration_value = 'auto';
$Layout[2]->Configuration['shadows']->configuration_value = '[]';
$Layout[2]->Configuration['font']->configuration_value = '{"family":"Arial","size":"12","size_unit":"px","style":"normal","variant":"normal","weight":"normal"}';
$Layout[2]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';

$Container[5] = $Layout[2]->Containers->getTable()->create();
$Layout[2]->Containers->add($Container[5]);
$Container[5]->sort_order = '1';
$Container[5]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"121","g":"14","b":"8","a":1},"position":"0"},{"color":{"r":"182","g":"14","b":"8","a":1},"position":"1"}]}';
$Container[5]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[5]->Styles['margin']->definition_value = '{"top":"10","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[5]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"14","border_top_left_radius_unit":"px","border_top_right_radius":"14","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Container[5]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[5]->Styles['border']->definition_value = '{"top":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Container[5]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[5]->Styles['width']->definition_value = 'auto';
$Container[5]->Configuration['margin']->configuration_value = '{"top":"10","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[5]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[5]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[5]->Configuration['border']->configuration_value = '{"top":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Container[5]->Configuration['width_unit']->configuration_value = 'auto';
$Container[5]->Configuration['shadows']->configuration_value = '[]';
$Container[5]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[5]->Configuration['id']->configuration_value = 'theContainer0';
$Container[5]->Configuration['width']->configuration_value = '98';
$Container[5]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":"14","border_top_left_radius_unit":"px","border_top_right_radius":"14","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Container[5]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"121","start_color_g":"14","start_color_b":"8","start_color_a":"100","end_color_r":"182","end_color_g":"14","end_color_b":"8","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[5]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';

$Column[11] = $Container[5]->Columns->getTable()->create();
$Container[5]->Columns->add($Column[11]);
$Column[11]->sort_order = '1';
$Column[11]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[11]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[11]->Styles['width']->definition_value = '58%';
$Column[11]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[11]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[11]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"2","left_unit":"%"}';
$Column[11]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"2","left_unit":"%"}';
$Column[11]->Configuration['id']->configuration_value = 'theContainer4';
$Column[11]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[11]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[11]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[11]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[11]->Configuration['width']->configuration_value = '58';
$Column[11]->Configuration['width_unit']->configuration_value = '%';
$Column[11]->Configuration['shadows']->configuration_value = '[]';
$Column[11]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[19] = $Column[11]->Widgets->getTable()->create();
$Column[11]->Widgets->add($Widget[19]);
$Widget[19]->identifier = 'customImage';
$Widget[19]->sort_order = '1';
$Widget[19]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":""},"image_source":"/templates/newred/images/logo.png"}';

$Column[12] = $Container[5]->Columns->getTable()->create();
$Container[5]->Columns->add($Column[12]);
$Column[12]->sort_order = '2';
$Column[12]->Styles['width']->definition_value = '40%';
$Column[12]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[12]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[12]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[12]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[12]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[12]->Configuration['id']->configuration_value = 'theContainer5';
$Column[12]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[12]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[12]->Configuration['shadows']->configuration_value = '[]';
$Column[12]->Configuration['width_unit']->configuration_value = '%';
$Column[12]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[12]->Configuration['width']->configuration_value = '40';
$Column[12]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[20] = $Column[12]->Widgets->getTable()->create();
$Column[12]->Widgets->add($Widget[20]);
$Widget[20]->identifier = 'navigationMenu';
$Widget[20]->sort_order = '1';
$Widget[20]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":"Home"},"49":{"text":"Home"},"50":{"text":"Home"},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"Register"},"48":{"text":"Register"},"49":{"text":"Register"},"50":{"text":"Register"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"create","target":"same"},"condition":"customer_not_logged_in","children":[]},"4":{"1":{"text":"My Account"},"48":{"text":"My Account"},"49":{"text":"My Account"},"50":{"text":"My Account"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"customer_logged_in","children":[]},"6":{"1":{"text":"Log In"},"48":{"text":"Log In"},"49":{"text":"Log In"},"50":{"text":"Log In"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"login","target":"same"},"condition":"customer_not_logged_in","children":[]},"8":{"1":{"text":"Log Out"},"48":{"text":"Log Out"},"49":{"text":"Log Out"},"50":{"text":"Log Out"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"logoff","target":"same"},"condition":"customer_logged_in","children":[]}},"menuId":"headerMiniMenu","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[21] = $Column[12]->Widgets->getTable()->create();
$Column[12]->Widgets->add($Widget[21]);
$Widget[21]->identifier = 'customPHP';
$Widget[21]->sort_order = '2';
$Widget[21]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""},"php_text":"<div id=\"headerShoppingCart\"><span class=\"shopIcon\"></span><' . '?php \r\nglobal $ShoppingCart;\r\n\t$shoppingProducts = $ShoppingCart->getProducts();\r\n    if(isset($shoppingProducts) && sizeof($shoppingProducts) >0){\r\n\t    echo \'<a href=\"\'.itw_app_link(null,\'shoppingCart\',\'default\').\'\">\'. sprintf(sysLanguage::get(\'HEADER_TEXT_SHOPPING_CART_ITEMS\'), sizeof($shoppingProducts)) . \'</a>\';\r\n\t}else{\r\n\t   echo sprintf(sysLanguage::get(\'HEADER_TEXT_SHOPPING_CART_ITEMS\'), 0);\r\n\t}\r\n ?' . '></div>\r\n"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[22] = $Column[12]->Widgets->getTable()->create();
$Column[12]->Widgets->add($Widget[22]);
$Widget[22]->identifier = 'customPHP';
$Widget[22]->sort_order = '3';
$Widget[22]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""},"php_text":"<div id=\"headerRentalQueue\"><span class=\"rentIcon\"></span><' . '?php\r\nglobal $rentalQueue;\r\n\t\tif (sysConfig::get(\'ALLOW_RENTALS\') == \'true\'){\r\n\t\t\t$cart_contents_string = \'\';\r\n\t\t\tif ($rentalQueue->count_contents() > 0) {\r\n\t\t\t\t  echo \'<a href=\"\'.itw_app_link(null,\'rentals\',\'queue\').\'\">\'.  sprintf(sysLanguage::get(\'HEADER_TEXT_RENTAL_QUEUE_ITEMS\'), $rentalQueue->count_contents()) . \'</a>\';\r\n\t\t\t}else{\r\n\t\t\t\techo sprintf(sysLanguage::get(\'HEADER_TEXT_RENTAL_QUEUE_ITEMS\'), 0);\r\n\t\t\t}\r\n\t\t}\r\n?' . '></div>\r\n"}';

$Column[13] = $Container[5]->Columns->getTable()->create();
$Container[5]->Columns->add($Column[13]);
$Column[13]->sort_order = '3';
$Column[13]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[13]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[13]->Styles['width']->definition_value = '59%';
$Column[13]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"1","left_unit":"%"}';
$Column[13]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[13]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"bottom","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[13]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"1","left_unit":"%"}';
$Column[13]->Configuration['width_unit']->configuration_value = '%';
$Column[13]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"bottom","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[13]->Configuration['shadows']->configuration_value = '[]';
$Column[13]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[13]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[13]->Configuration['id']->configuration_value = 'theContainer6';
$Column[13]->Configuration['width']->configuration_value = '59';
$Column[13]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[13]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[23] = $Column[13]->Widgets->getTable()->create();
$Column[13]->Widgets->add($Widget[23]);
$Widget[23]->identifier = 'navigationMenu';
$Widget[23]->sort_order = '1';
$Widget[23]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""},"menuSettings":[{"1":{"text":"Home"},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},{"1":{"text":"All Products"},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},{"1":{"text":"Featured"},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},{"1":{"text":"Free Trial"},"icon":"none","icon_src":"","link":{"type":"custom","url":"checkout_rental_account.php","target":"same"},"condition":"","children":[]},{"1":{"text":"Blog"},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_category","page":"default","target":"same"},"condition":"","children":[]},{"1":{"text":"Contact Us"},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}],"menuId":"mainNavigationMenu","forceFit":"false"}';

$Column[14] = $Container[5]->Columns->getTable()->create();
$Container[5]->Columns->add($Column[14]);
$Column[14]->sort_order = '4';
$Column[14]->Styles['width']->definition_value = '40%';
$Column[14]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"bottom","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[14]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[14]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[14]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[14]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[14]->Configuration['width']->configuration_value = '40';
$Column[14]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[14]->Configuration['shadows']->configuration_value = '[]';
$Column[14]->Configuration['id']->configuration_value = 'theContainer7';
$Column[14]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[14]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"bottom","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[14]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[24] = $Column[14]->Widgets->getTable()->create();
$Column[14]->Widgets->add($Widget[24]);
$Widget[24]->identifier = 'customPHP';
$Widget[24]->sort_order = '1';
$Widget[24]->Configuration['widget_settings']->configuration_value = '{"php_text":"<div id=\"headerSearch\">\n\t<form style=\"\" name=\"search\" action=\"<' . '?php echo itw_app_link(null, \'products\', \'search_result\');?' . '>\" method=\"get\">\n\t\t<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:300px;margin-bottom:0 !important;\">\n\t\t\t<tr>\n\t\t\t\t<td><' . '?php echo sysLanguage::get(\'HEADER_NAV_TEXT_SEARCH\');?' . '></td>\n\t\t\t\t<td><' . '?php echo tep_draw_input_field(\'keywords\', \'\', \'style=\"height:20px;\"\') . tep_hide_session_id();?' . '></td>\n\t\t\t\t<td><' . '?php echo htmlBase::newElement(\'button\')->setType(\'submit\')->setText(\'GO\')->draw();?' . '></td>\n\t\t\t</tr>\n\t\t</table>\n\t</form>\n</div>","template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

$Container[6] = $Layout[2]->Containers->getTable()->create();
$Layout[2]->Containers->add($Container[6]);
$Container[6]->sort_order = '2';
$Container[6]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[6]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"255","g":"255","b":"255","a":1},"position":"0"},{"color":{"r":"208","g":"207","b":"208","a":1},"position":"1"}]}';
$Container[6]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[6]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[6]->Styles['width']->definition_value = 'auto';
$Container[6]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[6]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[6]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[6]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"255","start_color_g":"255","start_color_b":"255","start_color_a":"100","end_color_r":"208","end_color_g":"207","end_color_b":"208","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[6]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[6]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[6]->Configuration['shadows']->configuration_value = '[]';
$Container[6]->Configuration['id']->configuration_value = 'theContainer1';
$Container[6]->Configuration['width']->configuration_value = '99';
$Container[6]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[6]->Configuration['width_unit']->configuration_value = 'auto';
$Container[6]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[6]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';

$Column[15] = $Container[6]->Columns->getTable()->create();
$Container[6]->Columns->add($Column[15]);
$Column[15]->sort_order = '1';
$Column[15]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"3","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[15]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[15]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[15]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[15]->Styles['width']->definition_value = '80%';
$Column[15]->Configuration['id']->configuration_value = 'theContainer8';
$Column[15]->Configuration['shadows']->configuration_value = '[]';
$Column[15]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"3","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[15]->Configuration['width']->configuration_value = '80';
$Column[15]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[15]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[15]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[15]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

if (!isset($Box['breadcrumb'])){
 $Box['breadcrumb'] = $TemplatesInfoboxes->findOneByBoxCode('breadcrumb');
    if (!is_object($Box['breadcrumb']) || $Box['breadcrumb']->count() <= 0){
       installInfobox('includes/modules/infoboxes/breadcrumb/', 'breadcrumb', 'null');
       $Box['breadcrumb'] = $TemplatesInfoboxes->findOneByBoxCode('breadcrumb');
   }
}

$Widget[25] = $Column[15]->Widgets->getTable()->create();
$Column[15]->Widgets->add($Widget[25]);
$Widget[25]->identifier = 'breadcrumb';
$Widget[25]->sort_order = '1';
$Widget[25]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

$Column[16] = $Container[6]->Columns->getTable()->create();
$Container[6]->Columns->add($Column[16]);
$Column[16]->sort_order = '2';
$Column[16]->Styles['width']->definition_value = '20%';
$Column[16]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[16]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[16]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[16]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[16]->Configuration['id']->configuration_value = 'theContainer9';
$Column[16]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[16]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[16]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[16]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[16]->Configuration['shadows']->configuration_value = '[]';
$Column[16]->Configuration['width']->configuration_value = '20';
$Column[16]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

if (!isset($Box['currencies'])){
 $Box['currencies'] = $TemplatesInfoboxes->findOneByBoxCode('currencies');
    if (!is_object($Box['currencies']) || $Box['currencies']->count() <= 0){
       installInfobox('includes/modules/infoboxes/currencies/', 'currencies', 'null');
       $Box['currencies'] = $TemplatesInfoboxes->findOneByBoxCode('currencies');
   }
}

$Widget[26] = $Column[16]->Widgets->getTable()->create();
$Column[16]->Widgets->add($Widget[26]);
$Widget[26]->identifier = 'currencies';
$Widget[26]->sort_order = '1';
$Widget[26]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Container[7] = $Layout[2]->Containers->getTable()->create();
$Layout[2]->Containers->add($Container[7]);
$Container[7]->sort_order = '3';
$Container[7]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[7]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[7]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[7]->Styles['width']->definition_value = 'auto';
$Container[7]->Styles['padding']->definition_value = '{"top":"10","top_unit":"px","right":"0","right_unit":"px","bottom":"10","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[7]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[7]->Styles['background_solid']->definition_value = '{"background_r":"250","background_g":"245","background_b":"250","background_a":"100"}';
$Container[7]->Configuration['width']->configuration_value = '99';
$Container[7]->Configuration['id']->configuration_value = 'theContainer2';
$Container[7]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"0","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[7]->Configuration['width_unit']->configuration_value = 'auto';
$Container[7]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"250","background_g":"245","background_b":"250","background_a":"100"}}}}';
$Container[7]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Container[7]->Configuration['shadows']->configuration_value = '[]';
$Container[7]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[7]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[7]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[7]->Configuration['padding']->configuration_value = '{"top":"10","top_unit":"px","right":"0","right_unit":"px","bottom":"10","bottom_unit":"px","left":"0","left_unit":"px"}';

$Column[17] = $Container[7]->Columns->getTable()->create();
$Container[7]->Columns->add($Column[17]);
$Column[17]->sort_order = '1';
$Column[17]->Styles['width']->definition_value = '18%';
$Column[17]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[17]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"1","right_unit":"%","bottom":"0","bottom_unit":"px","left":".5","left_unit":"%"}';
$Column[17]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[17]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[17]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"1","right_unit":"%","bottom":"0","bottom_unit":"px","left":".5","left_unit":"%"}';
$Column[17]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[17]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[17]->Configuration['width_unit']->configuration_value = '%';
$Column[17]->Configuration['shadows']->configuration_value = '[]';
$Column[17]->Configuration['id']->configuration_value = 'theContainer10';
$Column[17]->Configuration['width']->configuration_value = '18';
$Column[17]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[17]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';

if (!isset($Box['categories'])){
 $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
    if (!is_object($Box['categories']) || $Box['categories']->count() <= 0){
       installInfobox('includes/modules/infoboxes/categories/', 'categories', 'null');
       $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
   }
}

$Widget[27] = $Column[17]->Widgets->getTable()->create();
$Column[17]->Widgets->add($Widget[27]);
$Widget[27]->identifier = 'categories';
$Widget[27]->sort_order = '1';
$Widget[27]->Configuration['widget_settings']->configuration_value = '{"id":"categoriesInfoBox","template_file":"box.tpl","widget_title":{"1":"Categories","48":"Categories","49":"Categories","50":"Categories"}}';

$Column[18] = $Container[7]->Columns->getTable()->create();
$Container[7]->Columns->add($Column[18]);
$Column[18]->sort_order = '2';
$Column[18]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[18]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[18]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[18]->Styles['width']->definition_value = '61%';
$Column[18]->Configuration['id']->configuration_value = 'theContainer11';
$Column[18]->Configuration['shadows']->configuration_value = '[]';
$Column[18]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[18]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[18]->Configuration['width']->configuration_value = '61';
$Column[18]->Configuration['width_unit']->configuration_value = '%';
$Column[18]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[18]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

if (!isset($Box['pageStack'])){
 $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
    if (!is_object($Box['pageStack']) || $Box['pageStack']->count() <= 0){
       installInfobox('includes/modules/infoboxes/pageStack/', 'pageStack', 'null');
       $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
   }
}

$Widget[28] = $Column[18]->Widgets->getTable()->create();
$Column[18]->Widgets->add($Widget[28]);
$Widget[28]->identifier = 'pageStack';
$Widget[28]->sort_order = '1';
$Widget[28]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';

if (!isset($Box['pageContent'])){
 $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
    if (!is_object($Box['pageContent']) || $Box['pageContent']->count() <= 0){
       installInfobox('extensions/templateManager/catalog/infoboxes/pageContent/', 'pageContent', 'templateManager');
       $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
   }
}

$Widget[29] = $Column[18]->Widgets->getTable()->create();
$Column[18]->Widgets->add($Widget[29]);
$Widget[29]->identifier = 'pageContent';
$Widget[29]->sort_order = '2';
$Widget[29]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Column[19] = $Container[7]->Columns->getTable()->create();
$Container[7]->Columns->add($Column[19]);
$Column[19]->sort_order = '3';
$Column[19]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[19]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[19]->Styles['width']->definition_value = '18%';
$Column[19]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":".5","right_unit":"%","bottom":"0","bottom_unit":"px","left":"1","left_unit":"%"}';
$Column[19]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[19]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":".5","right_unit":"%","bottom":"0","bottom_unit":"px","left":"1","left_unit":"%"}';
$Column[19]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[19]->Configuration['width']->configuration_value = '18';
$Column[19]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[19]->Configuration['shadows']->configuration_value = '[]';
$Column[19]->Configuration['id']->configuration_value = 'theContainer12';
$Column[19]->Configuration['width_unit']->configuration_value = '%';
$Column[19]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[19]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';

if (!isset($Box['shoppingCart'])){
 $Box['shoppingCart'] = $TemplatesInfoboxes->findOneByBoxCode('shoppingCart');
    if (!is_object($Box['shoppingCart']) || $Box['shoppingCart']->count() <= 0){
       installInfobox('includes/modules/infoboxes/shoppingCart/', 'shoppingCart', 'null');
       $Box['shoppingCart'] = $TemplatesInfoboxes->findOneByBoxCode('shoppingCart');
   }
}

$Widget[30] = $Column[19]->Widgets->getTable()->create();
$Column[19]->Widgets->add($Widget[30]);
$Widget[30]->identifier = 'shoppingCart';
$Widget[30]->sort_order = '1';
$Widget[30]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Shopping Cart"}}';

if (!isset($Box['bestSellers'])){
 $Box['bestSellers'] = $TemplatesInfoboxes->findOneByBoxCode('bestSellers');
    if (!is_object($Box['bestSellers']) || $Box['bestSellers']->count() <= 0){
       installInfobox('includes/modules/infoboxes/bestSellers/', 'bestSellers', 'null');
       $Box['bestSellers'] = $TemplatesInfoboxes->findOneByBoxCode('bestSellers');
   }
}

$Widget[31] = $Column[19]->Widgets->getTable()->create();
$Column[19]->Widgets->add($Widget[31]);
$Widget[31]->identifier = 'bestSellers';
$Widget[31]->sort_order = '2';
$Widget[31]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Best Sellers"}}';

if (!isset($Box['infoPages'])){
 $Box['infoPages'] = $TemplatesInfoboxes->findOneByBoxCode('infoPages');
    if (!is_object($Box['infoPages']) || $Box['infoPages']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/infoPages/', 'infoPages', 'infoPages');
       $Box['infoPages'] = $TemplatesInfoboxes->findOneByBoxCode('infoPages');
   }
}

$Widget[32] = $Column[19]->Widgets->getTable()->create();
$Column[19]->Widgets->add($Widget[32]);
$Widget[32]->identifier = 'infoPages';
$Widget[32]->sort_order = '3';
$Widget[32]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Information"}}';

$Container[8] = $Layout[2]->Containers->getTable()->create();
$Layout[2]->Containers->add($Container[8]);
$Container[8]->sort_order = '4';
$Container[8]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"114","g":"13","b":"4","a":1},"position":"0"},{"color":{"r":"195","g":"13","b":"4","a":1},"position":"1"}]}';
$Container[8]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[8]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"10","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[8]->Styles['width']->definition_value = 'auto';
$Container[8]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[8]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[8]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[8]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"10","right_unit":"px","bottom":"10","bottom_unit":"px","left":"10","left_unit":"px"}';
$Container[8]->Configuration['width']->configuration_value = '99';
$Container[8]->Configuration['width_unit']->configuration_value = 'auto';
$Container[8]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[8]->Configuration['shadows']->configuration_value = '[]';
$Container[8]->Configuration['id']->configuration_value = 'theContainer3';
$Container[8]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"114","start_color_g":"13","start_color_b":"4","start_color_a":"100","end_color_r":"195","end_color_g":"13","end_color_b":"4","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[8]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[8]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"middle","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[8]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[8]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';

$Column[20] = $Container[8]->Columns->getTable()->create();
$Container[8]->Columns->add($Column[20]);
$Column[20]->sort_order = '1';
$Column[20]->Styles['text']->definition_value = '{"color":"#ebb93c","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[20]->Styles['width']->definition_value = '100%';
$Column[20]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[20]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[20]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"6","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[20]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"bold"}';
$Column[20]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[20]->Configuration['text']->configuration_value = '{"color":"#ebb93c","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"inherit","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[20]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"bold"}';
$Column[20]->Configuration['id']->configuration_value = 'theContainer13';
$Column[20]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[20]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[20]->Configuration['width_unit']->configuration_value = '%';
$Column[20]->Configuration['shadows']->configuration_value = '[]';
$Column[20]->Configuration['width']->configuration_value = '100';
$Column[20]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"6","bottom_unit":"px","left":"0","left_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[33] = $Column[20]->Widgets->getTable()->create();
$Column[20]->Widgets->add($Widget[33]);
$Widget[33]->identifier = 'navigationMenu';
$Widget[33]->sort_order = '1';
$Widget[33]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"linked_to":"23","menuId":"footerMenu","forceFit":"false"}';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[34] = $Column[20]->Widgets->getTable()->create();
$Column[20]->Widgets->add($Widget[34]);
$Widget[34]->identifier = 'customText';
$Widget[34]->sort_order = '2';
$Widget[34]->Configuration['widget_settings']->configuration_value = '{"selected_page":"12","template_file":"noFormatingBox.tpl","widget_title":{"1":""}}';
$Template->save();
$WidgetProperties = json_decode($Widget[1]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('newred', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[1]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[1]->save();
$WidgetProperties = json_decode($Widget[2]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('newred', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[2]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[2]->save();
$WidgetProperties = json_decode($Widget[5]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('newred', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[5]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[5]->save();
$WidgetProperties = json_decode($Widget[13]->Configuration['widget_settings']->configuration_value);
$Pages = Doctrine_Core::getTable('Pages');
$PagesDescription = Doctrine_Core::getTable('PagesDescription');

$Page = $Pages->findOneByPageKey('how_it_works');
if (!$Page){
$Page = $Pages->create();
$Page->sort_order = '1';
$Page->status = '1';
$Page->infobox_status = '1';
$Page->page_type = 'block';
$Page->page_key = 'how_it_works';

$PageDescription = $PagesDescription->create();
	$PageDescription->pages_title = 'How It Works';
		$PageDescription->pages_html_text = '<table border="0" cellpadding="6" cellspacing="0" style="width: 100%">
' . "\n" . '	<tbody>
' . "\n" . '		<tr>
' . "\n" . '			<td align="middle">
' . "\n" . '				<img alt="" height="72" src="images/how_it_works_dvd.png" width="72" /></td>
' . "\n" . '			<td align="left">
' . "\n" . '				<b style="font-family: arial; color: rgb(156,25,24); font-size: 14px">Step 1</b><br />
' . "\n" . '				<span style="font-family: arial; color: rgb(59,59,59); font-size: 12px">Choose The product You Want</span></td>
' . "\n" . '			<td align="middle">
' . "\n" . '				<img alt="" height="45" src="images/how_it_works_truck.png" width="68" /></td>
' . "\n" . '			<td align="left">
' . "\n" . '				<b style="font-family: arial; color: rgb(156,25,24); font-size: 14px">Step 2</b><br />
' . "\n" . '				<span style="font-family: arial; color: rgb(59,59,59); font-size: 12px">We ship you your requested titles</span></td>
' . "\n" . '		</tr>
' . "\n" . '		<tr>
' . "\n" . '			<td align="middle">
' . "\n" . '				<img alt="" height="51" src="images/how_it_works_clock.png" width="50" /></td>
' . "\n" . '			<td align="left">
' . "\n" . '				<b style="font-family: arial; color: rgb(156,25,24); font-size: 14px">Step 3</b><br />
' . "\n" . '				<span style="font-family: arial; color: rgb(59,59,59); font-size: 12px">Watch your product(s) and keep them as long as you want</span></td>
' . "\n" . '			<td align="middle">
' . "\n" . '				<img alt="" height="48" src="images/how_it_works_arrow.png" width="50" /></td>
' . "\n" . '			<td align="left">
' . "\n" . '				<b style="font-family: arial; color: rgb(156,25,24); font-size: 14px">Step 4</b><br />
' . "\n" . '				<span style="font-family: arial; color: rgb(59,59,59); font-size: 12px">When you are done return your items, and we&#39;ll ship you your next selections</span></td>
' . "\n" . '		</tr>
' . "\n" . '	</tbody>
' . "\n" . '</table>
' . "\n" . '';
		$PageDescription->intorext = '0';
		$PageDescription->externallink = '';
		$PageDescription->link_target = '';
		$PageDescription->language_id = '1';
		$PageDescription->pages_head_title_tag = '';
		$PageDescription->pages_head_desc_tag = '';
		$PageDescription->pages_head_keywords_tag = '';
	
$Page->PagesDescription->add($PageDescription);
$Page->save();
}
$WidgetProperties->selected_page = $Page->pages_id;
$Widget[13]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[13]->save();
$WidgetProperties = json_decode($Widget[17]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('newred', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[17]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[17]->save();
$WidgetProperties = json_decode($Widget[18]->Configuration['widget_settings']->configuration_value);
$Pages = Doctrine_Core::getTable('Pages');
$PagesDescription = Doctrine_Core::getTable('PagesDescription');

$Page = $Pages->findOneByPageKey('store_locations');
if (!$Page){
$Page = $Pages->create();
$Page->sort_order = '0';
$Page->status = '1';
$Page->infobox_status = '1';
$Page->page_type = 'page';
$Page->page_key = 'store_locations';

$PageDescription = $PagesDescription->create();
	$PageDescription->pages_title = 'Store Locations';
		$PageDescription->pages_html_text = '<p>
' . "\n" . '	Store Locations</p>
' . "\n" . '';
		$PageDescription->intorext = '0';
		$PageDescription->externallink = '';
		$PageDescription->link_target = '0';
		$PageDescription->language_id = '1';
		$PageDescription->pages_head_title_tag = '';
		$PageDescription->pages_head_desc_tag = '';
		$PageDescription->pages_head_keywords_tag = '';
	
$Page->PagesDescription->add($PageDescription);
$Page->save();
}
$WidgetProperties->selected_page = $Page->pages_id;
$Widget[18]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[18]->save();
$WidgetProperties = json_decode($Widget[19]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('newred', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[19]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[19]->save();
$WidgetProperties = json_decode($Widget[20]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('newred', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[20]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[20]->save();
$WidgetProperties = json_decode($Widget[23]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('newred', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[23]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[23]->save();
$WidgetProperties = json_decode($Widget[33]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('newred', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[33]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[33]->save();
$WidgetProperties = json_decode($Widget[34]->Configuration['widget_settings']->configuration_value);
$Pages = Doctrine_Core::getTable('Pages');
$PagesDescription = Doctrine_Core::getTable('PagesDescription');

$Page = $Pages->findOneByPageKey('store_locations');
if (!$Page){
$Page = $Pages->create();
$Page->sort_order = '0';
$Page->status = '1';
$Page->infobox_status = '1';
$Page->page_type = 'page';
$Page->page_key = 'store_locations';

$PageDescription = $PagesDescription->create();
	$PageDescription->pages_title = 'Store Locations';
		$PageDescription->pages_html_text = '<p>
' . "\n" . '	Store Locations</p>
' . "\n" . '';
		$PageDescription->intorext = '0';
		$PageDescription->externallink = '';
		$PageDescription->link_target = '0';
		$PageDescription->language_id = '1';
		$PageDescription->pages_head_title_tag = '';
		$PageDescription->pages_head_desc_tag = '';
		$PageDescription->pages_head_keywords_tag = '';
	
$Page->PagesDescription->add($PageDescription);
$Page->save();
}
$WidgetProperties->selected_page = $Page->pages_id;
$Widget[34]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[34]->save();
addLayoutToPage('index', 'default.php', null, $Layout[1]->layout_id);
addLayoutToPage('account', 'address_book.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'create.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'create_success.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'credit_card_details.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'cron_auto_send_return.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'cron_membership_update.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'edit.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'history.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'history_info.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'login.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'logoff.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'membership.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'membership_cancel.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'membership_info.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'membership_upgrade.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'newsletters.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'password.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'password_forgotten.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'rental_issues.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'rented_products.php', null, $Layout[2]->layout_id);
addLayoutToPage('billing_address_process', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('center_address_check', 'center_address_check.php', null, $Layout[2]->layout_id);
addLayoutToPage('checkout', 'addresses.php', null, $Layout[2]->layout_id);
addLayoutToPage('checkout', 'cart.php', null, $Layout[2]->layout_id);
addLayoutToPage('checkout', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('checkout', 'shipping_payment.php', null, $Layout[2]->layout_id);
addLayoutToPage('checkout', 'success.php', null, $Layout[2]->layout_id);
addLayoutToPage('checkout_old', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('checkout_old', 'rental_process.php', null, $Layout[2]->layout_id);
addLayoutToPage('checkout_old', 'success.php', null, $Layout[2]->layout_id);
addLayoutToPage('contact_us', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('contact_us', 'success.php', null, $Layout[2]->layout_id);
addLayoutToPage('funways', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('gv_redeem', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('index', 'nested.php', null, $Layout[2]->layout_id);
addLayoutToPage('index', 'products.php', null, $Layout[2]->layout_id);
addLayoutToPage('index', 'index.php', null, $Layout[2]->layout_id);
addLayoutToPage('product', 'info.php', null, $Layout[2]->layout_id);
addLayoutToPage('product', 'reviews.php', null, $Layout[2]->layout_id);
addLayoutToPage('products', 'all.php', null, $Layout[2]->layout_id);
addLayoutToPage('products', 'best_sellers.php', null, $Layout[2]->layout_id);
addLayoutToPage('products', 'featured.php', null, $Layout[2]->layout_id);
addLayoutToPage('products', 'new.php', null, $Layout[2]->layout_id);
addLayoutToPage('products', 'search.php', null, $Layout[2]->layout_id);
addLayoutToPage('products', 'search_result.php', null, $Layout[2]->layout_id);
addLayoutToPage('products', 'upcoming.php', null, $Layout[2]->layout_id);
addLayoutToPage('recent_additions', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('redirect', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('rentals', 'queue.php', null, $Layout[2]->layout_id);
addLayoutToPage('rentals', 'top.php', null, $Layout[2]->layout_id);
addLayoutToPage('shoppingCart', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('shopping_cart', 'shopping_cart.php', null, $Layout[2]->layout_id);
addLayoutToPage('tell_a_friend', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('viewStream', 'default.php', null, $Layout[2]->layout_id);
addLayoutToPage('viewStream', 'pull.php', null, $Layout[2]->layout_id);
addLayoutToPage('show', 'default.php', 'articleManager', $Layout[2]->layout_id);
addLayoutToPage('show', 'info.php', 'articleManager', $Layout[2]->layout_id);
addLayoutToPage('show', 'new.php', 'articleManager', $Layout[2]->layout_id);
addLayoutToPage('show', 'rss.php', 'articleManager', $Layout[2]->layout_id);
addLayoutToPage('banner_actions', 'default.php', 'bannerManager', $Layout[2]->layout_id);
addLayoutToPage('show_archive', 'default.php', 'blog', $Layout[2]->layout_id);
addLayoutToPage('show_post', 'default.php', 'blog', $Layout[2]->layout_id);
addLayoutToPage('show_page', 'default.php', 'categoriesPages', $Layout[2]->layout_id);
addLayoutToPage('simpleDownload', 'default.php', 'customFields', $Layout[2]->layout_id);
addLayoutToPage('downloads', 'default.php', 'downloadProducts', $Layout[2]->layout_id);
addLayoutToPage('downloads', 'get.php', 'downloadProducts', $Layout[2]->layout_id);
addLayoutToPage('downloads', 'listing.php', 'downloadProducts', $Layout[2]->layout_id);
addLayoutToPage('main', 'default.php', 'downloadProducts', $Layout[2]->layout_id);
addLayoutToPage('show_page', 'default.php', 'infoPages', $Layout[2]->layout_id);
addLayoutToPage('center_address_check', 'default.php', 'inventoryCenters', $Layout[2]->layout_id);
addLayoutToPage('show_inventory', 'default.php', 'inventoryCenters', $Layout[2]->layout_id);
addLayoutToPage('show_inventory', 'delivery.php', 'inventoryCenters', $Layout[2]->layout_id);
addLayoutToPage('show_inventory', 'list.php', 'inventoryCenters', $Layout[2]->layout_id);
addLayoutToPage('account_addon', 'history_inventory_info.php', 'inventoryCenters', $Layout[2]->layout_id);
addLayoutToPage('account_addon', 'view_orders_inventory.php', 'inventoryCenters', $Layout[2]->layout_id);
addLayoutToPage('show_shipping', 'default.php', 'payPerRentals', $Layout[2]->layout_id);
addLayoutToPage('build_reservation', 'default.php', 'payPerRentals', $Layout[2]->layout_id);
addLayoutToPage('address_check', 'default.php', 'payPerRentals', $Layout[2]->layout_id);
addLayoutToPage('show_event', 'default.php', 'payPerRentals', $Layout[2]->layout_id);
addLayoutToPage('show_event', 'list.php', 'payPerRentals', $Layout[2]->layout_id);
addLayoutToPage('design', 'default.php', 'productDesigner', $Layout[2]->layout_id);
addLayoutToPage('clipart', 'default.php', 'productDesigner', $Layout[2]->layout_id);
addLayoutToPage('product_review', 'default.php', 'reviews', $Layout[2]->layout_id);
addLayoutToPage('product_review', 'details.php', 'reviews', $Layout[2]->layout_id);
addLayoutToPage('product_review', 'write.php', 'reviews', $Layout[2]->layout_id);
addLayoutToPage('account_addon', 'view_royalties.php', 'royaltiesSystem', $Layout[2]->layout_id);
addLayoutToPage('show_specials', 'default.php', 'specials', $Layout[2]->layout_id);
addLayoutToPage('streams', 'default.php', 'streamProducts', $Layout[2]->layout_id);
addLayoutToPage('streams', 'listing.php', 'streamProducts', $Layout[2]->layout_id);
addLayoutToPage('streams', 'view.php', 'streamProducts', $Layout[2]->layout_id);
addLayoutToPage('main', 'default.php', 'streamProducts', $Layout[2]->layout_id);
addLayoutToPage('notify', 'cron.php', 'waitingList', $Layout[2]->layout_id);
addLayoutToPage('notify', 'default.php', 'waitingList', $Layout[2]->layout_id);
addLayoutToPage('account', 'address_book_details.php', null, $Layout[2]->layout_id);
addLayoutToPage('account', 'address_book_process.php', null, $Layout[2]->layout_id);
addLayoutToPage('show_category', 'default.php', 'blog', $Layout[2]->layout_id);
addLayoutToPage('show_category', 'rss.php', 'blog', $Layout[2]->layout_id);
