<?php
$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->create();
$Template->Configuration['NAME']->configuration_value = 'Game Rental Store';
$Template->Configuration['DIRECTORY']->configuration_value = 'gamerental';
$Template->Configuration['STYLESHEET_COMPRESSION']->configuration_value = 'none';
$Template->Configuration['JAVASCRIPT_COMPRESSION']->configuration_value = 'none';

$Layout[51] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[51]);
$Layout[51]->layout_name = 'Home Page';

$Container[126] = $Layout[51]->Containers->getTable()->create();
$Layout[51]->Containers->add($Container[126]);
$Container[126]->sort_order = '1';
$Container[126]->Styles['width']->definition_value = 'auto';
$Container[126]->Styles['margin-top']->definition_value = '0px';
$Container[126]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px"}';
$Container[126]->Styles['padding-top']->definition_value = '20px';
$Container[126]->Styles['border-left-color']->definition_value = '#000000';
$Container[126]->Styles['border-left-style']->definition_value = 'solid';
$Container[126]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"255","g":"255","b":"255","a":0},"position":"0"},{"color":{"r":"51","g":"53","b":"56","a":0.5},"position":0},{"color":{"r":"51","g":"53","b":"56","a":1},"position":0.3},{"color":{"r":"0","g":"0","b":"0","a":1},"position":"1"}],"images":[{"css_placement":"after","image":"\/templates\/gamerental\/images\/header_pattern.png","repeat":"repeat","pos_x":"50","pos_y":"50","pos_x_unit":"%","pos_y_unit":"%"}]}';
$Container[126]->Styles['border-left-width']->definition_value = '0px';
$Container[126]->Styles['border-bottom-color']->definition_value = '#000000';
$Container[126]->Styles['margin-left']->definition_value = '0px';
$Container[126]->Styles['border-top-color']->definition_value = '#000000';
$Container[126]->Styles['border-top-width']->definition_value = '1px';
$Container[126]->Styles['border-top-style']->definition_value = 'solid';
$Container[126]->Styles['border-bottom-width']->definition_value = '1px';
$Container[126]->Styles['padding-right']->definition_value = '0px';
$Container[126]->Styles['padding-bottom']->definition_value = '0px';
$Container[126]->Styles['border-bottom-style']->definition_value = 'solid';
$Container[126]->Styles['border-right-width']->definition_value = '0px';
$Container[126]->Styles['border-right-style']->definition_value = 'solid';
$Container[126]->Styles['margin-right']->definition_value = '0px';
$Container[126]->Styles['padding-left']->definition_value = '0px';
$Container[126]->Styles['border-right-color']->definition_value = '#000000';
$Container[126]->Styles['margin-bottom']->definition_value = '0px';
$Container[126]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"255","start_color_g":"255","start_color_b":"255","start_color_a":"0","end_color_r":"0","end_color_g":"0","end_color_b":"0","end_color_a":"100"},"colorStops":[{"color_stop_pos":"0","color_stop_color_r":"51","color_stop_color_g":"53","color_stop_color_b":"56","color_stop_color_a":"50"},{"color_stop_pos":"30","color_stop_color_r":"51","color_stop_color_g":"53","color_stop_color_b":"56","color_stop_color_a":"100"}],"imagesBefore":[],"imagesAfter":[{"image_background_color_r":"69","image_background_color_g":"72","image_background_color_b":"77","image_background_color_a":"100","image_source":"\/templates\/gamerental\/images\/header_pattern.png","image_attachment":"","image_pos_x":"50","image_pos_y":"50","image_repeat":"repeat"}]},"solid":{"config":{}}}}';
$Container[126]->Configuration['border_top_width_unit']->configuration_value = 'px';
$Container[126]->Configuration['border_left_style']->configuration_value = 'solid';
$Container[126]->Configuration['border_bottom_width']->configuration_value = '1';
$Container[126]->Configuration['border_top_width']->configuration_value = '1';
$Container[126]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[126]->Configuration['border_bottom_width_unit']->configuration_value = 'px';
$Container[126]->Configuration['border_right_width']->configuration_value = '0';
$Container[126]->Configuration['shadows']->configuration_value = '[]';
$Container[126]->Configuration['border_right_style']->configuration_value = 'solid';
$Container[126]->Configuration['borderRadius']->configuration_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Container[126]->Configuration['border_right_width_unit']->configuration_value = 'px';
$Container[126]->Configuration['border_right_color']->configuration_value = '#000000';
$Container[126]->Configuration['border_top_style']->configuration_value = 'solid';
$Container[126]->Configuration['border_bottom_color']->configuration_value = '#000000';
$Container[126]->Configuration['border_left_width_unit']->configuration_value = 'px';
$Container[126]->Configuration['border_bottom_style']->configuration_value = 'solid';
$Container[126]->Configuration['border_left_width']->configuration_value = '0';
$Container[126]->Configuration['width']->configuration_value = '1024';
$Container[126]->Configuration['id']->configuration_value = 'gradientWrap';
$Container[126]->Configuration['border_top_color']->configuration_value = '#000000';
$Container[126]->Configuration['border_left_color']->configuration_value = '#000000';
$Container[126]->Configuration['width_unit']->configuration_value = 'auto';

$Child[127] = $Container[126]->Children->getTable()->create();
$Container[126]->Children->add($Child[127]);
$Child[127]->sort_order = '1';
$Child[127]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px"}';
$Child[127]->Styles['border-left-color']->definition_value = '#000000';
$Child[127]->Styles['border-top-style']->definition_value = 'solid';
$Child[127]->Styles['border-bottom-color']->definition_value = '#393939';
$Child[127]->Styles['border-bottom-width']->definition_value = '7px';
$Child[127]->Styles['border-bottom-style']->definition_value = 'solid';
$Child[127]->Styles['border-left-width']->definition_value = '0px';
$Child[127]->Styles['border-right-width']->definition_value = '0px';
$Child[127]->Styles['width']->definition_value = 'auto';
$Child[127]->Styles['border-top-color']->definition_value = '#000000';
$Child[127]->Styles['border-left-style']->definition_value = 'solid';
$Child[127]->Styles['border-top-width']->definition_value = '0px';
$Child[127]->Styles['border-right-style']->definition_value = 'solid';
$Child[127]->Styles['border-right-color']->definition_value = '#000000';
$Child[127]->Configuration['border_top_color']->configuration_value = '#000000';
$Child[127]->Configuration['border_right_color']->configuration_value = '#000000';
$Child[127]->Configuration['border_bottom_width_unit']->configuration_value = 'px';
$Child[127]->Configuration['width']->configuration_value = '1024';
$Child[127]->Configuration['border_right_style']->configuration_value = 'solid';
$Child[127]->Configuration['border_top_width_unit']->configuration_value = 'px';
$Child[127]->Configuration['border_bottom_color']->configuration_value = '#393939';
$Child[127]->Configuration['width_unit']->configuration_value = 'auto';
$Child[127]->Configuration['border_bottom_style']->configuration_value = 'solid';
$Child[127]->Configuration['backgroundType']->configuration_value = '{}';
$Child[127]->Configuration['border_top_width']->configuration_value = '0';
$Child[127]->Configuration['id']->configuration_value = '';
$Child[127]->Configuration['border_right_width_unit']->configuration_value = 'px';
$Child[127]->Configuration['border_left_width']->configuration_value = '0';
$Child[127]->Configuration['shadows']->configuration_value = '[]';
$Child[127]->Configuration['border_left_style']->configuration_value = 'solid';
$Child[127]->Configuration['borderRadius']->configuration_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Child[127]->Configuration['border_left_color']->configuration_value = '#000000';
$Child[127]->Configuration['border_bottom_width']->configuration_value = '7';
$Child[127]->Configuration['border_left_width_unit']->configuration_value = 'px';
$Child[127]->Configuration['border_right_width']->configuration_value = '0';
$Child[127]->Configuration['border_top_style']->configuration_value = 'solid';

$Child[128] = $Child[127]->Children->getTable()->create();
$Child[127]->Children->add($Child[128]);
$Child[128]->sort_order = '1';
$Child[128]->Styles['margin-left']->definition_value = 'auto';
$Child[128]->Styles['margin-bottom']->definition_value = '0px';
$Child[128]->Styles['padding-left']->definition_value = '0px';
$Child[128]->Styles['margin-top']->definition_value = '0px';
$Child[128]->Styles['width']->definition_value = '960px';
$Child[128]->Styles['margin-right']->definition_value = 'auto';
$Child[128]->Styles['padding-bottom']->definition_value = '0px';
$Child[128]->Styles['padding-top']->definition_value = '0px';
$Child[128]->Styles['padding-right']->definition_value = '0px';
$Child[128]->Configuration['shadows']->configuration_value = '[]';
$Child[128]->Configuration['id']->configuration_value = '';
$Child[128]->Configuration['backgroundType']->configuration_value = '{}';
$Child[128]->Configuration['width']->configuration_value = '960';
$Child[128]->Configuration['width_unit']->configuration_value = 'px';

$Column[105] = $Child[128]->Columns->getTable()->create();
$Child[128]->Columns->add($Column[105]);
$Column[105]->sort_order = '1';
$Column[105]->Styles['width']->definition_value = '300px';
$Column[105]->Configuration['id']->configuration_value = '';
$Column[105]->Configuration['width_unit']->configuration_value = 'px';
$Column[105]->Configuration['width']->configuration_value = '300';
$Column[105]->Configuration['shadows']->configuration_value = '[]';
$Column[105]->Configuration['backgroundType']->configuration_value = '{}';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[135] = $Column[105]->Widgets->getTable()->create();
$Column[105]->Widgets->add($Widget[135]);
$Widget[135]->identifier = 'customImage';
$Widget[135]->sort_order = '1';
$Widget[135]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/gamerental/images/logog.png","image_link":""}';

$Column[106] = $Child[128]->Columns->getTable()->create();
$Child[128]->Columns->add($Column[106]);
$Column[106]->sort_order = '2';
$Column[106]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"8","border_top_left_radius_unit":"px","border_top_right_radius":"8","border_top_right_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px"}';
$Column[106]->Styles['border-bottom-color']->definition_value = '#000000';
$Column[106]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"103","g":"102","b":"103","a":1},"position":"0"},{"color":{"r":"52","g":"51","b":"52","a":1},"position":0.49},{"color":{"r":"0","g":"0","b":"0","a":1},"position":0.5},{"color":{"r":"29","g":"29","b":"29","a":1},"position":0.75},{"color":{"r":"33","g":"33","b":"33","a":1},"position":"1"}]}';
$Column[106]->Styles['border-top-width']->definition_value = '0px';
$Column[106]->Styles['border-right-style']->definition_value = 'solid';
$Column[106]->Styles['border-left-width']->definition_value = '0px';
$Column[106]->Styles['border-bottom-style']->definition_value = 'solid';
$Column[106]->Styles['border-top-color']->definition_value = '#000000';
$Column[106]->Styles['width']->definition_value = '960px';
$Column[106]->Styles['border-left-color']->definition_value = '#000000';
$Column[106]->Styles['border-right-width']->definition_value = '0px';
$Column[106]->Styles['border-left-style']->definition_value = 'solid';
$Column[106]->Styles['border-right-color']->definition_value = '#000000';
$Column[106]->Styles['border-top-style']->definition_value = 'solid';
$Column[106]->Styles['border-bottom-width']->definition_value = '0px';
$Column[106]->Configuration['border_bottom_width_unit']->configuration_value = 'px';
$Column[106]->Configuration['border_right_width']->configuration_value = '0';
$Column[106]->Configuration['border_bottom_width']->configuration_value = '0';
$Column[106]->Configuration['width_unit']->configuration_value = 'px';
$Column[106]->Configuration['border_bottom_style']->configuration_value = 'solid';
$Column[106]->Configuration['border_top_width']->configuration_value = '0';
$Column[106]->Configuration['id']->configuration_value = '';
$Column[106]->Configuration['border_bottom_color']->configuration_value = '#000000';
$Column[106]->Configuration['border_top_width_unit']->configuration_value = 'px';
$Column[106]->Configuration['border_left_width_unit']->configuration_value = 'px';
$Column[106]->Configuration['borderRadius']->configuration_value = '{"border_top_left_radius":"8","border_top_left_radius_unit":"px","border_top_right_radius":"8","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Column[106]->Configuration['width']->configuration_value = '960';
$Column[106]->Configuration['border_right_color']->configuration_value = '#000000';
$Column[106]->Configuration['border_left_style']->configuration_value = 'solid';
$Column[106]->Configuration['shadows']->configuration_value = '[]';
$Column[106]->Configuration['border_top_style']->configuration_value = 'solid';
$Column[106]->Configuration['border_right_width_unit']->configuration_value = 'px';
$Column[106]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Column[106]->Configuration['border_top_color']->configuration_value = '#000000';
$Column[106]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"103","start_color_g":"102","start_color_b":"103","start_color_a":"100","end_color_r":"33","end_color_g":"33","end_color_b":"33","end_color_a":"100"},"colorStops":[{"color_stop_pos":"49","color_stop_color_r":"52","color_stop_color_g":"51","color_stop_color_b":"52","color_stop_color_a":"100"},{"color_stop_pos":"50","color_stop_color_r":"0","color_stop_color_g":"0","color_stop_color_b":"0","color_stop_color_a":"100"},{"color_stop_pos":"75","color_stop_color_r":"29","color_stop_color_g":"29","color_stop_color_b":"29","color_stop_color_a":"100"}],"imagesBefore":[],"imagesAfter":[]}}}';
$Column[106]->Configuration['border_right_style']->configuration_value = 'solid';
$Column[106]->Configuration['border_left_width']->configuration_value = '0';
$Column[106]->Configuration['border_left_color']->configuration_value = '#000000';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[136] = $Column[106]->Widgets->getTable()->create();
$Column[106]->Widgets->add($Widget[136]);
$Widget[136]->identifier = 'navigationMenu';
$Widget[136]->sort_order = '1';
$Widget[136]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":"Home"},"49":{"text":"Home"},"50":{"text":"Home"},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"1":{"1":{"text":"Help/FAQ"},"48":{"text":"Help/FAQ"},"49":{"text":"Help/FAQ"},"50":{"text":"Help/FAQ"},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"gv_faq","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"How It Works"},"48":{"text":"How It Works"},"49":{"text":"How It Works"},"50":{"text":"How It Works"},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"party_hire","target":"same"},"condition":"","children":[]},"3":{"1":{"text":"My Account"},"48":{"text":"My Account"},"49":{"text":"My Account"},"50":{"text":"My Account"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Shopping Cart"},"48":{"text":"Shopping Cart"},"49":{"text":"Shopping Cart"},"50":{"text":"Shopping Cart"},"icon":"none","icon_src":"","link":{"type":"app","application":"shoppingCart","page":"default","target":"same"},"condition":"","children":[]},"5":{"1":{"text":"Rental Queue"},"48":{"text":"Rental Queue"},"49":{"text":"Rental Queue"},"50":{"text":"Rental Queue"},"icon":"none","icon_src":"","link":{"type":"app","application":"rentals","page":"queue","target":"same"},"condition":"","children":[]}},"menuId":"headerMainNavigation","forceFit":"true"}';

$Container[129] = $Layout[51]->Containers->getTable()->create();
$Layout[51]->Containers->add($Container[129]);
$Container[129]->sort_order = '2';
$Container[129]->Styles['width']->definition_value = 'auto';
$Container[129]->Styles['background_solid']->definition_value = '{"background_r":"214","background_g":"214","background_b":"214","background_a":"100"}';
$Container[129]->Styles['margin-top']->definition_value = '0px';
$Container[129]->Styles['padding-top']->definition_value = '10px';
$Container[129]->Styles['margin-bottom']->definition_value = '0px';
$Container[129]->Styles['padding-bottom']->definition_value = '10px';
$Container[129]->Styles['margin-left']->definition_value = '0px';
$Container[129]->Styles['padding-left']->definition_value = '0px';
$Container[129]->Styles['margin-right']->definition_value = '0px';
$Container[129]->Styles['padding-right']->definition_value = '0px';
$Container[129]->Configuration['background']->configuration_value = '{"global":{"solid":{"background_r":"214","background_g":"214","background_b":"214","background_a":"100","config":{"background_r":"214","background_g":"214","background_b":"214","background_a":"100"}}}}';
$Container[129]->Configuration['shadows']->configuration_value = '[]';
$Container[129]->Configuration['width']->configuration_value = '1024';
$Container[129]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Container[129]->Configuration['id']->configuration_value = '';
$Container[129]->Configuration['width_unit']->configuration_value = 'auto';

$Child[130] = $Container[129]->Children->getTable()->create();
$Container[129]->Children->add($Child[130]);
$Child[130]->sort_order = '1';
$Child[130]->Styles['padding-left']->definition_value = '0px';
$Child[130]->Styles['padding-right']->definition_value = '0px';
$Child[130]->Styles['margin-top']->definition_value = '0px';
$Child[130]->Styles['margin-left']->definition_value = 'auto';
$Child[130]->Styles['margin-right']->definition_value = 'auto';
$Child[130]->Styles['padding-bottom']->definition_value = '0px';
$Child[130]->Styles['padding-top']->definition_value = '0px';
$Child[130]->Styles['margin-bottom']->definition_value = '0px';
$Child[130]->Styles['width']->definition_value = '960px';
$Child[130]->Configuration['width_unit']->configuration_value = 'px';
$Child[130]->Configuration['backgroundType']->configuration_value = '{}';
$Child[130]->Configuration['id']->configuration_value = '';
$Child[130]->Configuration['width']->configuration_value = '960';
$Child[130]->Configuration['shadows']->configuration_value = '[]';

$Column[107] = $Child[130]->Columns->getTable()->create();
$Child[130]->Columns->add($Column[107]);
$Column[107]->sort_order = '1';
$Column[107]->Styles['margin-right']->definition_value = '5px';
$Column[107]->Styles['margin-top']->definition_value = '0px';
$Column[107]->Styles['padding-bottom']->definition_value = '0px';
$Column[107]->Styles['margin-bottom']->definition_value = '0px';
$Column[107]->Styles['width']->definition_value = '260px';
$Column[107]->Styles['padding-top']->definition_value = '0px';
$Column[107]->Styles['margin-left']->definition_value = '0px';
$Column[107]->Styles['padding-left']->definition_value = '0px';
$Column[107]->Styles['padding-right']->definition_value = '0px';
$Column[107]->Configuration['width_unit']->configuration_value = 'px';
$Column[107]->Configuration['backgroundType']->configuration_value = '{}';
$Column[107]->Configuration['shadows']->configuration_value = '[]';
$Column[107]->Configuration['id']->configuration_value = '';
$Column[107]->Configuration['width']->configuration_value = '260';

if (!isset($Box['categories'])){
 $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
    if (!is_object($Box['categories']) || $Box['categories']->count() <= 0){
       installInfobox('includes/modules/infoboxes/categories/', 'categories', 'null');
       $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
   }
}

$Widget[137] = $Column[107]->Widgets->getTable()->create();
$Column[107]->Widgets->add($Widget[137]);
$Widget[137]->identifier = 'categories';
$Widget[137]->sort_order = '1';
$Widget[137]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Categories","48":"Categories","49":"Categories","50":"Categories"}}';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[147] = $Column[107]->Widgets->getTable()->create();
$Column[107]->Widgets->add($Widget[147]);
$Widget[147]->identifier = 'customText';
$Widget[147]->sort_order = '2';
$Widget[147]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Contact Us","48":"Contact Us","49":"Contact Us","50":"Contact Us"},"selected_page":"30","id":""}';

if (!isset($Box['languages'])){
 $Box['languages'] = $TemplatesInfoboxes->findOneByBoxCode('languages');
    if (!is_object($Box['languages']) || $Box['languages']->count() <= 0){
       installInfobox('includes/modules/infoboxes/languages/', 'languages', 'null');
       $Box['languages'] = $TemplatesInfoboxes->findOneByBoxCode('languages');
   }
}

$Widget[148] = $Column[107]->Widgets->getTable()->create();
$Column[107]->Widgets->add($Widget[148]);
$Widget[148]->identifier = 'languages';
$Widget[148]->sort_order = '3';
$Widget[148]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Language","48":"%u8BED%u8A00","49":"Idioma","50":"Sprache"}}';

$Column[108] = $Child[130]->Columns->getTable()->create();
$Child[130]->Columns->add($Column[108]);
$Column[108]->sort_order = '2';
$Column[108]->Styles['margin-left']->definition_value = '5px';
$Column[108]->Styles['padding-bottom']->definition_value = '0px';
$Column[108]->Styles['margin-top']->definition_value = '0px';
$Column[108]->Styles['padding-top']->definition_value = '0px';
$Column[108]->Styles['padding-left']->definition_value = '0px';
$Column[108]->Styles['margin-right']->definition_value = '0px';
$Column[108]->Styles['padding-right']->definition_value = '0px';
$Column[108]->Styles['margin-bottom']->definition_value = '0px';
$Column[108]->Styles['width']->definition_value = '690px';
$Column[108]->Configuration['backgroundType']->configuration_value = '{}';
$Column[108]->Configuration['shadows']->configuration_value = '[]';
$Column[108]->Configuration['width_unit']->configuration_value = 'px';
$Column[108]->Configuration['id']->configuration_value = '';
$Column[108]->Configuration['width']->configuration_value = '690';

if (!isset($Box['banner'])){
 $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
    if (!is_object($Box['banner']) || $Box['banner']->count() <= 0){
       installInfobox('extensions/imageRot/catalog/infoboxes/banner/', 'banner', 'imageRot');
       $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
   }
}

$Widget[139] = $Column[108]->Widgets->getTable()->create();
$Column[108]->Widgets->add($Widget[139]);
$Widget[139]->identifier = 'banner';
$Widget[139]->sort_order = '1';
$Widget[139]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"selected_banner_group":"8"}';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[140] = $Column[108]->Widgets->getTable()->create();
$Column[108]->Widgets->add($Widget[140]);
$Widget[140]->identifier = 'customText';
$Widget[140]->sort_order = '2';
$Widget[140]->Configuration['widget_settings']->configuration_value = '{"id":"IndexWelcomeBlock","template_file":"box.tpl","widget_title":{"1":"Welcome To Game Rental Site"},"selected_page":"31"}';

if (!isset($Box['customScroller'])){
 $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
    if (!is_object($Box['customScroller']) || $Box['customScroller']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customScroller/', 'customScroller', 'null');
       $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
   }
}

$Widget[141] = $Column[108]->Widgets->getTable()->create();
$Column[108]->Widgets->add($Widget[141]);
$Widget[141]->identifier = 'customScroller';
$Widget[141]->sort_order = '3';
$Widget[141]->Configuration['widget_settings']->configuration_value = '{"id":"indexScroller","template_file":"box.tpl","widget_title":{"1":"Featured Products"},"scrollers":{"type":"stack","configs":[{"headings":{"1":"Featured Products"},"query":"featured","query_limit":"25","reflect_blocks":false,"block_width":"200","block_height":"200","prev_image":"/templates/gamerental/images/carousel_prev.png","next_image":"/templates/gamerental/images/carousel_next.png"}]}}';

$Container[131] = $Layout[51]->Containers->getTable()->create();
$Layout[51]->Containers->add($Container[131]);
$Container[131]->sort_order = '3';
$Container[131]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"86","g":"83","b":"90","a":1},"position":"0"},{"color":{"r":"62","g":"58","b":"67","a":1},"position":"1"}]}';
$Container[131]->Styles['padding-right']->definition_value = '0px';
$Container[131]->Styles['width']->definition_value = 'auto';
$Container[131]->Styles['margin-right']->definition_value = '0px';
$Container[131]->Styles['margin-top']->definition_value = '0px';
$Container[131]->Styles['padding-top']->definition_value = '10px';
$Container[131]->Styles['margin-bottom']->definition_value = '0px';
$Container[131]->Styles['padding-left']->definition_value = '0px';
$Container[131]->Styles['margin-left']->definition_value = '0px';
$Container[131]->Styles['padding-bottom']->definition_value = '10px';
$Container[131]->Configuration['width_unit']->configuration_value = 'auto';
$Container[131]->Configuration['id']->configuration_value = '';
$Container[131]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[131]->Configuration['shadows']->configuration_value = '[]';
$Container[131]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"86","start_color_g":"83","start_color_b":"90","start_color_a":"100","end_color_r":"62","end_color_g":"58","end_color_b":"67","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[131]->Configuration['width']->configuration_value = '1024';

$Child[132] = $Container[131]->Children->getTable()->create();
$Container[131]->Children->add($Child[132]);
$Child[132]->sort_order = '1';
$Child[132]->Styles['padding-top']->definition_value = '0px';
$Child[132]->Styles['line-height']->definition_value = '1em';
$Child[132]->Styles['margin-right']->definition_value = 'auto';
$Child[132]->Styles['margin-top']->definition_value = '0px';
$Child[132]->Styles['text-align']->definition_value = 'left';
$Child[132]->Styles['margin-bottom']->definition_value = '0px';
$Child[132]->Styles['width']->definition_value = '960px';
$Child[132]->Styles['padding-left']->definition_value = '0px';
$Child[132]->Styles['padding-bottom']->definition_value = '0px';
$Child[132]->Styles['font-family']->definition_value = 'Arial';
$Child[132]->Styles['margin-left']->definition_value = 'auto';
$Child[132]->Styles['color']->definition_value = '#ffffff';
$Child[132]->Styles['font-size']->definition_value = '1em';
$Child[132]->Styles['padding-right']->definition_value = '0px';
$Child[132]->Configuration['line_height']->configuration_value = '1';
$Child[132]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[132]->Configuration['font_family']->configuration_value = 'Arial';
$Child[132]->Configuration['shadows']->configuration_value = '[]';
$Child[132]->Configuration['text_align']->configuration_value = 'left';
$Child[132]->Configuration['color']->configuration_value = '#ffffff';
$Child[132]->Configuration['width']->configuration_value = '960';
$Child[132]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[132]->Configuration['id']->configuration_value = '';
$Child[132]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":"0","start_vertical_pos":"0","end_horizontal_pos":"0","end_vertical_pos":"100","start_color_r":"86","start_color_g":"83","start_color_b":"90","start_color_a":"100","end_color_r":"56","end_color_g":"52","end_color_b":"62","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Child[132]->Configuration['font_size']->configuration_value = '1';
$Child[132]->Configuration['width_unit']->configuration_value = 'px';
$Child[132]->Configuration['font_size_unit']->configuration_value = 'em';

$Column[109] = $Child[132]->Columns->getTable()->create();
$Child[132]->Columns->add($Column[109]);
$Column[109]->sort_order = '1';
$Column[109]->Styles['width']->definition_value = '660px';
$Column[109]->Configuration['width_unit']->configuration_value = 'px';
$Column[109]->Configuration['shadows']->configuration_value = '[]';
$Column[109]->Configuration['backgroundType']->configuration_value = '{}';
$Column[109]->Configuration['width']->configuration_value = '660';
$Column[109]->Configuration['id']->configuration_value = 'FooterColumn1';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[143] = $Column[109]->Widgets->getTable()->create();
$Column[109]->Widgets->add($Widget[143]);
$Widget[143]->identifier = 'navigationMenu';
$Widget[143]->sort_order = '1';
$Widget[143]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"linked_to":"136","menuId":"footerNavigationMenu","forceFit":"false"}';

$Column[110] = $Child[132]->Columns->getTable()->create();
$Child[132]->Columns->add($Column[110]);
$Column[110]->sort_order = '2';
$Column[110]->Styles['text-align']->definition_value = 'center';
$Column[110]->Styles['line-height']->definition_value = '1em';
$Column[110]->Styles['color']->definition_value = '#000000';
$Column[110]->Styles['font-size']->definition_value = '1em';
$Column[110]->Styles['width']->definition_value = '300px';
$Column[110]->Styles['font-family']->definition_value = 'Arial';
$Column[110]->Configuration['id']->configuration_value = 'FooterColumn2';
$Column[110]->Configuration['shadows']->configuration_value = '[]';
$Column[110]->Configuration['width_unit']->configuration_value = 'px';
$Column[110]->Configuration['color']->configuration_value = '#000000';
$Column[110]->Configuration['width']->configuration_value = '300';
$Column[110]->Configuration['font_family']->configuration_value = 'Arial';
$Column[110]->Configuration['backgroundType']->configuration_value = '{}';
$Column[110]->Configuration['text_align']->configuration_value = 'center';
$Column[110]->Configuration['font_size_unit']->configuration_value = 'em';
$Column[110]->Configuration['line_height']->configuration_value = '1';
$Column[110]->Configuration['font_size']->configuration_value = '1';
$Column[110]->Configuration['line_height_unit']->configuration_value = 'em';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[142] = $Column[110]->Widgets->getTable()->create();
$Column[110]->Widgets->add($Widget[142]);
$Widget[142]->identifier = 'customImage';
$Widget[142]->sort_order = '1';
$Widget[142]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/gamerental/images/xboxwiips3logo.png","image_link":""}';

$Layout[79] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[79]);
$Layout[79]->layout_name = 'All Pages';

$Container[322] = $Layout[79]->Containers->getTable()->create();
$Layout[79]->Containers->add($Container[322]);
$Container[322]->sort_order = '1';
$Container[322]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"255","g":"255","b":"255","a":0},"position":"0"},{"color":{"r":"51","g":"53","b":"56","a":0.5},"position":0},{"color":{"r":"51","g":"53","b":"56","a":1},"position":0.25},{"color":{"r":"0","g":"0","b":"0","a":1},"position":"1"}],"images":[{"css_placement":"after","image":"\/templates\/gamerental\/images\/header_pattern.png","repeat":"repeat","pos_x":"50","pos_y":"50","pos_x_unit":"%","pos_y_unit":"%"}]}';
$Container[322]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px"}';
$Container[322]->Styles['margin-left']->definition_value = '0px';
$Container[322]->Styles['border-bottom-color']->definition_value = '#000000';
$Container[322]->Styles['padding-bottom']->definition_value = '0px';
$Container[322]->Styles['margin-bottom']->definition_value = '0px';
$Container[322]->Styles['border-bottom-width']->definition_value = '1px';
$Container[322]->Styles['border-left-color']->definition_value = '#000000';
$Container[322]->Styles['padding-top']->definition_value = '20px';
$Container[322]->Styles['border-bottom-style']->definition_value = 'solid';
$Container[322]->Styles['width']->definition_value = 'auto';
$Container[322]->Styles['margin-top']->definition_value = '0px';
$Container[322]->Styles['border-top-color']->definition_value = '#000000';
$Container[322]->Styles['padding-right']->definition_value = '0px';
$Container[322]->Styles['padding-left']->definition_value = '0px';
$Container[322]->Styles['border-right-color']->definition_value = '#000000';
$Container[322]->Styles['border-right-style']->definition_value = 'solid';
$Container[322]->Styles['border-left-width']->definition_value = '0px';
$Container[322]->Styles['border-top-style']->definition_value = 'solid';
$Container[322]->Styles['border-top-width']->definition_value = '1px';
$Container[322]->Styles['border-left-style']->definition_value = 'solid';
$Container[322]->Styles['border-right-width']->definition_value = '0px';
$Container[322]->Styles['margin-right']->definition_value = '0px';
$Container[322]->Configuration['border_left_style']->configuration_value = 'solid';
$Container[322]->Configuration['border_right_width_unit']->configuration_value = 'px';
$Container[322]->Configuration['borderRadius']->configuration_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Container[322]->Configuration['border_bottom_width']->configuration_value = '1';
$Container[322]->Configuration['border_right_style']->configuration_value = 'solid';
$Container[322]->Configuration['shadows']->configuration_value = '[]';
$Container[322]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"255","start_color_g":"255","start_color_b":"255","start_color_a":"0","end_color_r":"0","end_color_g":"0","end_color_b":"0","end_color_a":"100"},"colorStops":[{"color_stop_pos":"0","color_stop_color_r":"51","color_stop_color_g":"53","color_stop_color_b":"56","color_stop_color_a":"50"},{"color_stop_pos":"25","color_stop_color_r":"51","color_stop_color_g":"53","color_stop_color_b":"56","color_stop_color_a":"100"}],"imagesBefore":[],"imagesAfter":[{"image_background_color_r":"69","image_background_color_g":"72","image_background_color_b":"77","image_background_color_a":"100","image_source":"\/templates\/gamerental\/images\/header_pattern.png","image_attachment":"","image_pos_x":"50","image_pos_y":"50","image_repeat":"repeat"}]}}}';
$Container[322]->Configuration['border_bottom_width_unit']->configuration_value = 'px';
$Container[322]->Configuration['border_left_color']->configuration_value = '#000000';
$Container[322]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[322]->Configuration['border_right_width']->configuration_value = '0';
$Container[322]->Configuration['border_top_width_unit']->configuration_value = 'px';
$Container[322]->Configuration['border_top_width']->configuration_value = '1';
$Container[322]->Configuration['id']->configuration_value = 'header_wrapper';
$Container[322]->Configuration['border_left_width']->configuration_value = '0';
$Container[322]->Configuration['width']->configuration_value = '1024';
$Container[322]->Configuration['border_bottom_style']->configuration_value = 'solid';
$Container[322]->Configuration['border_left_width_unit']->configuration_value = 'px';
$Container[322]->Configuration['border_bottom_color']->configuration_value = '#000000';
$Container[322]->Configuration['width_unit']->configuration_value = 'auto';
$Container[322]->Configuration['border_top_color']->configuration_value = '#000000';
$Container[322]->Configuration['border_top_style']->configuration_value = 'solid';
$Container[322]->Configuration['border_right_color']->configuration_value = '#000000';

$Child[323] = $Container[322]->Children->getTable()->create();
$Container[322]->Children->add($Child[323]);
$Child[323]->sort_order = '1';
$Child[323]->Styles['border-right-width']->definition_value = '0px';
$Child[323]->Styles['border-bottom-style']->definition_value = 'solid';
$Child[323]->Styles['border-top-width']->definition_value = '0px';
$Child[323]->Styles['border-left-width']->definition_value = '0px';
$Child[323]->Styles['border-left-style']->definition_value = 'solid';
$Child[323]->Styles['border-top-style']->definition_value = 'solid';
$Child[323]->Styles['border-left-color']->definition_value = '#000000';
$Child[323]->Styles['border-bottom-color']->definition_value = '#393939';
$Child[323]->Styles['border-right-color']->definition_value = '#000000';
$Child[323]->Styles['border-top-color']->definition_value = '#000000';
$Child[323]->Styles['border-right-style']->definition_value = 'solid';
$Child[323]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px"}';
$Child[323]->Styles['width']->definition_value = 'auto';
$Child[323]->Styles['border-bottom-width']->definition_value = '7px';
$Child[323]->Configuration['border_bottom_width_unit']->configuration_value = 'px';
$Child[323]->Configuration['border_bottom_width']->configuration_value = '7';
$Child[323]->Configuration['shadows']->configuration_value = '[]';
$Child[323]->Configuration['border_right_width']->configuration_value = '0';
$Child[323]->Configuration['border_top_color']->configuration_value = '#000000';
$Child[323]->Configuration['border_left_width']->configuration_value = '0';
$Child[323]->Configuration['id']->configuration_value = '';
$Child[323]->Configuration['border_right_style']->configuration_value = 'solid';
$Child[323]->Configuration['borderRadius']->configuration_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Child[323]->Configuration['border_top_width_unit']->configuration_value = 'px';
$Child[323]->Configuration['width']->configuration_value = '1024';
$Child[323]->Configuration['border_right_width_unit']->configuration_value = 'px';
$Child[323]->Configuration['border_bottom_style']->configuration_value = 'solid';
$Child[323]->Configuration['border_bottom_color']->configuration_value = '#393939';
$Child[323]->Configuration['border_top_width']->configuration_value = '0';
$Child[323]->Configuration['border_right_color']->configuration_value = '#000000';
$Child[323]->Configuration['border_top_style']->configuration_value = 'solid';
$Child[323]->Configuration['border_left_color']->configuration_value = '#000000';
$Child[323]->Configuration['border_left_style']->configuration_value = 'solid';
$Child[323]->Configuration['border_left_width_unit']->configuration_value = 'px';
$Child[323]->Configuration['width_unit']->configuration_value = 'auto';
$Child[323]->Configuration['backgroundType']->configuration_value = '{}';

$Child[324] = $Child[323]->Children->getTable()->create();
$Child[323]->Children->add($Child[324]);
$Child[324]->sort_order = '1';
$Child[324]->Styles['margin-bottom']->definition_value = '0px';
$Child[324]->Styles['margin-right']->definition_value = 'auto';
$Child[324]->Styles['padding-left']->definition_value = '0px';
$Child[324]->Styles['margin-left']->definition_value = 'auto';
$Child[324]->Styles['padding-right']->definition_value = '0px';
$Child[324]->Styles['padding-top']->definition_value = '0px';
$Child[324]->Styles['margin-top']->definition_value = '0px';
$Child[324]->Styles['padding-bottom']->definition_value = '0px';
$Child[324]->Styles['width']->definition_value = '960px';
$Child[324]->Configuration['id']->configuration_value = '';
$Child[324]->Configuration['shadows']->configuration_value = '[]';
$Child[324]->Configuration['width']->configuration_value = '960';
$Child[324]->Configuration['backgroundType']->configuration_value = '{}';
$Child[324]->Configuration['width_unit']->configuration_value = 'px';

$Column[325] = $Child[324]->Columns->getTable()->create();
$Child[324]->Columns->add($Column[325]);
$Column[325]->sort_order = '1';
$Column[325]->Styles['width']->definition_value = '300px';
$Column[325]->Configuration['backgroundType']->configuration_value = '{}';
$Column[325]->Configuration['shadows']->configuration_value = '[]';
$Column[325]->Configuration['width']->configuration_value = '300';
$Column[325]->Configuration['width_unit']->configuration_value = 'px';
$Column[325]->Configuration['id']->configuration_value = '';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[496] = $Column[325]->Widgets->getTable()->create();
$Column[325]->Widgets->add($Widget[496]);
$Widget[496]->identifier = 'customImage';
$Widget[496]->sort_order = '1';
$Widget[496]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/gamerental/images/logog.png","image_link":""}';

$Column[326] = $Child[324]->Columns->getTable()->create();
$Child[324]->Columns->add($Column[326]);
$Column[326]->sort_order = '2';
$Column[326]->Styles['border-top-style']->definition_value = 'solid';
$Column[326]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"103","g":"102","b":"103","a":1},"position":"0"},{"color":{"r":"52","g":"51","b":"52","a":1},"position":0.49},{"color":{"r":"0","g":"0","b":"0","a":1},"position":0.5},{"color":{"r":"29","g":"29","b":"29","a":1},"position":0.75},{"color":{"r":"33","g":"33","b":"33","a":1},"position":"1"}]}';
$Column[326]->Styles['border-left-width']->definition_value = '0px';
$Column[326]->Styles['border-bottom-style']->definition_value = 'solid';
$Column[326]->Styles['border-left-style']->definition_value = 'solid';
$Column[326]->Styles['border-right-width']->definition_value = '0px';
$Column[326]->Styles['border-top-width']->definition_value = '0px';
$Column[326]->Styles['width']->definition_value = '960px';
$Column[326]->Styles['border-bottom-color']->definition_value = '#000000';
$Column[326]->Styles['border-top-color']->definition_value = '#000000';
$Column[326]->Styles['border-bottom-width']->definition_value = '0px';
$Column[326]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"8","border_top_left_radius_unit":"px","border_top_right_radius":"8","border_top_right_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px"}';
$Column[326]->Styles['border-right-color']->definition_value = '#000000';
$Column[326]->Styles['border-left-color']->definition_value = '#000000';
$Column[326]->Styles['border-right-style']->definition_value = 'solid';
$Column[326]->Configuration['border_left_style']->configuration_value = 'solid';
$Column[326]->Configuration['shadows']->configuration_value = '[]';
$Column[326]->Configuration['id']->configuration_value = 'headerNavMenuWrapper';
$Column[326]->Configuration['border_right_style']->configuration_value = 'solid';
$Column[326]->Configuration['border_left_width']->configuration_value = '0';
$Column[326]->Configuration['border_right_width']->configuration_value = '0';
$Column[326]->Configuration['border_top_width_unit']->configuration_value = 'px';
$Column[326]->Configuration['borderRadius']->configuration_value = '{"border_top_left_radius":"8","border_top_left_radius_unit":"px","border_top_right_radius":"8","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Column[326]->Configuration['border_left_color']->configuration_value = '#000000';
$Column[326]->Configuration['border_bottom_style']->configuration_value = 'solid';
$Column[326]->Configuration['border_top_color']->configuration_value = '#000000';
$Column[326]->Configuration['border_right_color']->configuration_value = '#000000';
$Column[326]->Configuration['border_bottom_color']->configuration_value = '#000000';
$Column[326]->Configuration['width_unit']->configuration_value = 'px';
$Column[326]->Configuration['border_right_width_unit']->configuration_value = 'px';
$Column[326]->Configuration['border_left_width_unit']->configuration_value = 'px';
$Column[326]->Configuration['border_bottom_width_unit']->configuration_value = 'px';
$Column[326]->Configuration['border_bottom_width']->configuration_value = '0';
$Column[326]->Configuration['border_top_width']->configuration_value = '0';
$Column[326]->Configuration['border_top_style']->configuration_value = 'solid';
$Column[326]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Column[326]->Configuration['width']->configuration_value = '960';
$Column[326]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"103","start_color_g":"102","start_color_b":"103","start_color_a":"100","end_color_r":"33","end_color_g":"33","end_color_b":"33","end_color_a":"100"},"colorStops":[{"color_stop_pos":"49","color_stop_color_r":"52","color_stop_color_g":"51","color_stop_color_b":"52","color_stop_color_a":"100"},{"color_stop_pos":"50","color_stop_color_r":"0","color_stop_color_g":"0","color_stop_color_b":"0","color_stop_color_a":"100"},{"color_stop_pos":"75","color_stop_color_r":"29","color_stop_color_g":"29","color_stop_color_b":"29","color_stop_color_a":"100"}],"imagesBefore":[],"imagesAfter":[]}}}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[497] = $Column[326]->Widgets->getTable()->create();
$Column[326]->Widgets->add($Widget[497]);
$Widget[497]->identifier = 'navigationMenu';
$Widget[497]->sort_order = '1';
$Widget[497]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":"Home"},"49":{"text":"Home"},"50":{"text":"Home"},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"1":{"1":{"text":"Help/FAQ"},"48":{"text":"Help/FAQ"},"49":{"text":"Help/FAQ"},"50":{"text":"Help/FAQ"},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"gv_faq","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"How It Works"},"48":{"text":"How It Works"},"49":{"text":"How It Works"},"50":{"text":"How It Works"},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"party_hire","target":"same"},"condition":"","children":[]},"3":{"1":{"text":"My Account"},"48":{"text":"My Account"},"49":{"text":"My Account"},"50":{"text":"My Account"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Shopping Cart"},"48":{"text":"Shopping Cart"},"49":{"text":"Shopping Cart"},"50":{"text":"Shopping Cart"},"icon":"none","icon_src":"","link":{"type":"app","application":"shoppingCart","page":"default","target":"same"},"condition":"","children":[]},"5":{"1":{"text":"Rental Queue"},"48":{"text":"Rental Queue"},"49":{"text":"Rental Queue"},"50":{"text":"Rental Queue"},"icon":"none","icon_src":"","link":{"type":"app","application":"rentals","page":"queue","target":"same"},"condition":"","children":[]}},"menuId":"headerMainNavigation","forceFit":"true"}';

$Container[325] = $Layout[79]->Containers->getTable()->create();
$Layout[79]->Containers->add($Container[325]);
$Container[325]->sort_order = '2';
$Container[325]->Styles['background_solid']->definition_value = '{"background_r":"214","background_g":"214","background_b":"214","background_a":"100"}';
$Container[325]->Styles['margin-right']->definition_value = '0px';
$Container[325]->Styles['margin-top']->definition_value = '0px';
$Container[325]->Styles['padding-left']->definition_value = '0px';
$Container[325]->Styles['width']->definition_value = 'auto';
$Container[325]->Styles['padding-right']->definition_value = '0px';
$Container[325]->Styles['margin-left']->definition_value = '0px';
$Container[325]->Styles['padding-bottom']->definition_value = '10px';
$Container[325]->Styles['margin-bottom']->definition_value = '0px';
$Container[325]->Styles['padding-top']->definition_value = '10px';
$Container[325]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Container[325]->Configuration['width_unit']->configuration_value = 'auto';
$Container[325]->Configuration['width']->configuration_value = '1024';
$Container[325]->Configuration['id']->configuration_value = '';
$Container[325]->Configuration['shadows']->configuration_value = '[]';
$Container[325]->Configuration['background']->configuration_value = '{"global":{"solid":{"background_r":"214","background_g":"214","background_b":"214","background_a":"100","config":{"background_r":"214","background_g":"214","background_b":"214","background_a":"100"}}}}';

$Child[326] = $Container[325]->Children->getTable()->create();
$Container[325]->Children->add($Child[326]);
$Child[326]->sort_order = '1';
$Child[326]->Styles['margin-left']->definition_value = 'auto';
$Child[326]->Styles['padding-left']->definition_value = '0px';
$Child[326]->Styles['padding-bottom']->definition_value = '0px';
$Child[326]->Styles['width']->definition_value = '960px';
$Child[326]->Styles['padding-top']->definition_value = '0px';
$Child[326]->Styles['margin-bottom']->definition_value = '0px';
$Child[326]->Styles['margin-right']->definition_value = 'auto';
$Child[326]->Styles['margin-top']->definition_value = '0px';
$Child[326]->Styles['padding-right']->definition_value = '0px';
$Child[326]->Configuration['backgroundType']->configuration_value = '{}';
$Child[326]->Configuration['width_unit']->configuration_value = 'px';
$Child[326]->Configuration['shadows']->configuration_value = '[]';
$Child[326]->Configuration['id']->configuration_value = '';
$Child[326]->Configuration['width']->configuration_value = '960';

$Column[327] = $Child[326]->Columns->getTable()->create();
$Child[326]->Columns->add($Column[327]);
$Column[327]->sort_order = '1';
$Column[327]->Styles['padding-top']->definition_value = '0px';
$Column[327]->Styles['padding-left']->definition_value = '0px';
$Column[327]->Styles['width']->definition_value = '260px';
$Column[327]->Styles['padding-bottom']->definition_value = '0px';
$Column[327]->Styles['margin-bottom']->definition_value = '0px';
$Column[327]->Styles['margin-left']->definition_value = '0px';
$Column[327]->Styles['padding-right']->definition_value = '0px';
$Column[327]->Styles['margin-top']->definition_value = '0px';
$Column[327]->Styles['margin-right']->definition_value = '5px';
$Column[327]->Configuration['shadows']->configuration_value = '[]';
$Column[327]->Configuration['id']->configuration_value = '';
$Column[327]->Configuration['width']->configuration_value = '260';
$Column[327]->Configuration['backgroundType']->configuration_value = '{}';
$Column[327]->Configuration['width_unit']->configuration_value = 'px';

if (!isset($Box['categories'])){
 $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
    if (!is_object($Box['categories']) || $Box['categories']->count() <= 0){
       installInfobox('includes/modules/infoboxes/categories/', 'categories', 'null');
       $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
   }
}

$Widget[498] = $Column[327]->Widgets->getTable()->create();
$Column[327]->Widgets->add($Widget[498]);
$Widget[498]->identifier = 'categories';
$Widget[498]->sort_order = '1';
$Widget[498]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Categories","48":"Categories","49":"Categories","50":"Categories"}}';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[499] = $Column[327]->Widgets->getTable()->create();
$Column[327]->Widgets->add($Widget[499]);
$Widget[499]->identifier = 'customText';
$Widget[499]->sort_order = '2';
$Widget[499]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Contact Us","48":"Contact Us","49":"Contact Us","50":"Contact Us"},"selected_page":"30","id":""}';

if (!isset($Box['languages'])){
 $Box['languages'] = $TemplatesInfoboxes->findOneByBoxCode('languages');
    if (!is_object($Box['languages']) || $Box['languages']->count() <= 0){
       installInfobox('includes/modules/infoboxes/languages/', 'languages', 'null');
       $Box['languages'] = $TemplatesInfoboxes->findOneByBoxCode('languages');
   }
}

$Widget[500] = $Column[327]->Widgets->getTable()->create();
$Column[327]->Widgets->add($Widget[500]);
$Widget[500]->identifier = 'languages';
$Widget[500]->sort_order = '3';
$Widget[500]->Configuration['widget_settings']->configuration_value = '{"template_file":"box.tpl","widget_title":{"1":"Language","48":"%u8BED%u8A00","49":"Idioma","50":"Sprache"}}';

$Column[328] = $Child[326]->Columns->getTable()->create();
$Child[326]->Columns->add($Column[328]);
$Column[328]->sort_order = '2';
$Column[328]->Styles['padding-bottom']->definition_value = '0px';
$Column[328]->Styles['margin-bottom']->definition_value = '0px';
$Column[328]->Styles['padding-right']->definition_value = '0px';
$Column[328]->Styles['width']->definition_value = '690px';
$Column[328]->Styles['padding-top']->definition_value = '0px';
$Column[328]->Styles['margin-right']->definition_value = '0px';
$Column[328]->Styles['padding-left']->definition_value = '0px';
$Column[328]->Styles['margin-left']->definition_value = '5px';
$Column[328]->Styles['margin-top']->definition_value = '0px';
$Column[328]->Configuration['width_unit']->configuration_value = 'px';
$Column[328]->Configuration['id']->configuration_value = '';
$Column[328]->Configuration['width']->configuration_value = '690';
$Column[328]->Configuration['shadows']->configuration_value = '[]';
$Column[328]->Configuration['backgroundType']->configuration_value = '{}';

if (!isset($Box['pageStack'])){
 $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
    if (!is_object($Box['pageStack']) || $Box['pageStack']->count() <= 0){
       installInfobox('includes/modules/infoboxes/pageStack/', 'pageStack', 'null');
       $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
   }
}

$Widget[510] = $Column[328]->Widgets->getTable()->create();
$Column[328]->Widgets->add($Widget[510]);
$Widget[510]->identifier = 'pageStack';
$Widget[510]->sort_order = '1';
$Widget[510]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

if (!isset($Box['pageContent'])){
 $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
    if (!is_object($Box['pageContent']) || $Box['pageContent']->count() <= 0){
       installInfobox('extensions/templateManager/catalog/infoboxes/pageContent/', 'pageContent', 'templateManager');
       $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
   }
}

$Widget[511] = $Column[328]->Widgets->getTable()->create();
$Column[328]->Widgets->add($Widget[511]);
$Widget[511]->identifier = 'pageContent';
$Widget[511]->sort_order = '2';
$Widget[511]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Container[327] = $Layout[79]->Containers->getTable()->create();
$Layout[79]->Containers->add($Container[327]);
$Container[327]->sort_order = '3';
$Container[327]->Styles['padding-right']->definition_value = '0px';
$Container[327]->Styles['margin-left']->definition_value = '0px';
$Container[327]->Styles['margin-bottom']->definition_value = '0px';
$Container[327]->Styles['padding-left']->definition_value = '0px';
$Container[327]->Styles['margin-right']->definition_value = '0px';
$Container[327]->Styles['width']->definition_value = 'auto';
$Container[327]->Styles['padding-bottom']->definition_value = '10px';
$Container[327]->Styles['padding-top']->definition_value = '10px';
$Container[327]->Styles['margin-top']->definition_value = '0px';
$Container[327]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"86","g":"83","b":"90","a":1},"position":"0"},{"color":{"r":"62","g":"58","b":"67","a":1},"position":"1"}]}';
$Container[327]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[327]->Configuration['width_unit']->configuration_value = 'auto';
$Container[327]->Configuration['width']->configuration_value = '1024';
$Container[327]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"86","start_color_g":"83","start_color_b":"90","start_color_a":"100","end_color_r":"62","end_color_g":"58","end_color_b":"67","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[327]->Configuration['shadows']->configuration_value = '[]';
$Container[327]->Configuration['id']->configuration_value = '';

$Child[328] = $Container[327]->Children->getTable()->create();
$Container[327]->Children->add($Child[328]);
$Child[328]->sort_order = '1';
$Child[328]->Styles['line-height']->definition_value = '1em';
$Child[328]->Styles['text-align']->definition_value = 'left';
$Child[328]->Styles['margin-bottom']->definition_value = '0px';
$Child[328]->Styles['padding-left']->definition_value = '0px';
$Child[328]->Styles['padding-bottom']->definition_value = '0px';
$Child[328]->Styles['width']->definition_value = '960px';
$Child[328]->Styles['margin-right']->definition_value = 'auto';
$Child[328]->Styles['margin-top']->definition_value = '0px';
$Child[328]->Styles['font-size']->definition_value = '1em';
$Child[328]->Styles['margin-left']->definition_value = 'auto';
$Child[328]->Styles['padding-top']->definition_value = '0px';
$Child[328]->Styles['font-family']->definition_value = 'Arial';
$Child[328]->Styles['padding-right']->definition_value = '0px';
$Child[328]->Styles['color']->definition_value = '#ffffff';
$Child[328]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[328]->Configuration['width_unit']->configuration_value = 'px';
$Child[328]->Configuration['font_family']->configuration_value = 'Arial';
$Child[328]->Configuration['text_align']->configuration_value = 'left';
$Child[328]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[328]->Configuration['width']->configuration_value = '960';
$Child[328]->Configuration['font_size']->configuration_value = '1';
$Child[328]->Configuration['line_height']->configuration_value = '1';
$Child[328]->Configuration['color']->configuration_value = '#ffffff';
$Child[328]->Configuration['shadows']->configuration_value = '[]';
$Child[328]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":"0","start_vertical_pos":"0","end_horizontal_pos":"0","end_vertical_pos":"100","start_color_r":"86","start_color_g":"83","start_color_b":"90","start_color_a":"100","end_color_r":"56","end_color_g":"52","end_color_b":"62","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Child[328]->Configuration['font_size_unit']->configuration_value = 'em';
$Child[328]->Configuration['id']->configuration_value = '';

$Column[329] = $Child[328]->Columns->getTable()->create();
$Child[328]->Columns->add($Column[329]);
$Column[329]->sort_order = '1';
$Column[329]->Styles['width']->definition_value = '660px';
$Column[329]->Configuration['id']->configuration_value = 'FooterColumn1';
$Column[329]->Configuration['width']->configuration_value = '660';
$Column[329]->Configuration['backgroundType']->configuration_value = '{}';
$Column[329]->Configuration['shadows']->configuration_value = '[]';
$Column[329]->Configuration['width_unit']->configuration_value = 'px';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[505] = $Column[329]->Widgets->getTable()->create();
$Column[329]->Widgets->add($Widget[505]);
$Widget[505]->identifier = 'navigationMenu';
$Widget[505]->sort_order = '1';
$Widget[505]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"linked_to":"136","menuId":"footerNavigationMenu","forceFit":"false"}';

$Column[330] = $Child[328]->Columns->getTable()->create();
$Child[328]->Columns->add($Column[330]);
$Column[330]->sort_order = '2';
$Column[330]->Styles['text-align']->definition_value = 'center';
$Column[330]->Styles['color']->definition_value = '#000000';
$Column[330]->Styles['line-height']->definition_value = '1em';
$Column[330]->Styles['font-size']->definition_value = '1em';
$Column[330]->Styles['font-family']->definition_value = 'Arial';
$Column[330]->Styles['width']->definition_value = '300px';
$Column[330]->Configuration['font_size']->configuration_value = '1';
$Column[330]->Configuration['line_height']->configuration_value = '1';
$Column[330]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[330]->Configuration['text_align']->configuration_value = 'center';
$Column[330]->Configuration['backgroundType']->configuration_value = '{}';
$Column[330]->Configuration['font_size_unit']->configuration_value = 'em';
$Column[330]->Configuration['color']->configuration_value = '#000000';
$Column[330]->Configuration['font_family']->configuration_value = 'Arial';
$Column[330]->Configuration['width_unit']->configuration_value = 'px';
$Column[330]->Configuration['width']->configuration_value = '300';
$Column[330]->Configuration['shadows']->configuration_value = '[]';
$Column[330]->Configuration['id']->configuration_value = 'FooterColumn2';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[506] = $Column[330]->Widgets->getTable()->create();
$Column[330]->Widgets->add($Widget[506]);
$Widget[506]->identifier = 'customImage';
$Widget[506]->sort_order = '1';
$Widget[506]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/gamerental/images/xboxwiips3logo.png","image_link":""}';
$Template->save();
$WidgetProperties = json_decode($Widget[135]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('gamerental', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[135]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[135]->save();
$WidgetProperties = json_decode($Widget[136]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('gamerental', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[136]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[136]->save();
$WidgetProperties = json_decode($Widget[147]->Configuration['widget_settings']->configuration_value);
$Pages = Doctrine_Core::getTable('Pages');
$PagesDescription = Doctrine_Core::getTable('PagesDescription');

$Page = $Pages->findOneByPageKey('contact_block2');
if (!$Page){
$Page = $Pages->create();
$Page->sort_order = '0';
$Page->status = '1';
$Page->infobox_status = '0';
$Page->page_type = 'block';
$Page->page_key = 'contact_block2';

$PageDescription = $PagesDescription->create();
	$PageDescription->pages_title = 'Contact Block2';
		$PageDescription->pages_html_text = '<p>
' . "\n" . '	Game Rental<br />
' . "\n" . '	Address: xyz<br />
' . "\n" . '	Phone: 123-456-7890<br />
' . "\n" . '	Email:<br />
' . "\n" . '	xyz@gamerental.com</p>
' . "\n" . '';
		$PageDescription->intorext = '';
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
$Widget[147]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[147]->save();
$WidgetProperties = json_decode($Widget[139]->Configuration['widget_settings']->configuration_value);
$BannerManagerBanners = Doctrine_Core::getTable('BannerManagerBanners');
$BannerManagerGroups = Doctrine_Core::getTable('BannerManagerGroups');
$BannerManagerBannersToGroups = Doctrine_Core::getTable('BannerManagerBannersToGroups');

$Banner = $BannerManagerBanners->findOneByBannersName('bngame');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'bngame';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'banner1.jpg';
		$Banner->banners_body_thumbs = '';
		$Banner->banners_html = '';
		$Banner->banners_description = '';
		$Banner->banners_small_description = '';
		$Banner->banners_views = '0';
		$Banner->banners_clicks = '0';
		$Banner->banners_sort_order = '';
		$Banner->banners_expires_views = '0';
		$Banner->banners_expires_clicks = '0';
	$Banner->save();
}

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannerg4');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannerg4';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '720';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '303';
		$BannerGroup->banner_group_thumbs_width = '50';
		$BannerGroup->banner_group_thumbs_height = '50';
		$BannerGroup->banner_group_show_description = '0';
		$BannerGroup->banner_group_auto_rotate = '0';
		$BannerGroup->banner_group_show_custom = '0';
		$BannerGroup->banner_group_show_thumbs_desc = '0';
		$BannerGroup->banner_group_use_autoresize = '0';
		$BannerGroup->banner_group_use_thumbs = '0';
		$BannerGroup->banner_group_auto_hide_title = '0';
		$BannerGroup->banner_group_auto_hide_numbers = '0';
		$BannerGroup->banner_group_auto_hide_thumbs = '0';
		$BannerGroup->banner_group_auto_hide_arrows = '0';
		$BannerGroup->banner_group_auto_hide_thumbs_desc = '0';
		$BannerGroup->banner_group_auto_hide_custom = '0';
		$BannerGroup->banner_group_hover_pause = '0';
		$BannerGroup->banner_group_description_opacity = '0.00';
		$BannerGroup->banner_group_is_expiring = '0';
	$BannerGroup->save();
}

$BannerToGroup = $BannerManagerBannersToGroups->findOneByBannersIdAndBannerGroupId($Banner->banners_id, $BannerGroup->banner_group_id);
if (!$BannerToGroup){
$BannerToGroup = $BannerManagerBannersToGroups->create();
$BannerToGroup->banners_id = $Banner->banners_id;
$BannerToGroup->banner_group_id = $BannerGroup->banner_group_id;
$BannerToGroup->save();
}


$Banner = $BannerManagerBanners->findOneByBannersName('bannergame3');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'bannergame3';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'bannergame3.jpg';
		$Banner->banners_body_thumbs = '';
		$Banner->banners_html = '';
		$Banner->banners_description = '';
		$Banner->banners_small_description = '';
		$Banner->banners_views = '0';
		$Banner->banners_clicks = '0';
		$Banner->banners_sort_order = '';
		$Banner->banners_expires_views = '0';
		$Banner->banners_expires_clicks = '0';
	$Banner->save();
}

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannerg4');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannerg4';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '720';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '303';
		$BannerGroup->banner_group_thumbs_width = '50';
		$BannerGroup->banner_group_thumbs_height = '50';
		$BannerGroup->banner_group_show_description = '0';
		$BannerGroup->banner_group_auto_rotate = '0';
		$BannerGroup->banner_group_show_custom = '0';
		$BannerGroup->banner_group_show_thumbs_desc = '0';
		$BannerGroup->banner_group_use_autoresize = '0';
		$BannerGroup->banner_group_use_thumbs = '0';
		$BannerGroup->banner_group_auto_hide_title = '0';
		$BannerGroup->banner_group_auto_hide_numbers = '0';
		$BannerGroup->banner_group_auto_hide_thumbs = '0';
		$BannerGroup->banner_group_auto_hide_arrows = '0';
		$BannerGroup->banner_group_auto_hide_thumbs_desc = '0';
		$BannerGroup->banner_group_auto_hide_custom = '0';
		$BannerGroup->banner_group_hover_pause = '0';
		$BannerGroup->banner_group_description_opacity = '0.00';
		$BannerGroup->banner_group_is_expiring = '0';
	$BannerGroup->save();
}

$BannerToGroup = $BannerManagerBannersToGroups->findOneByBannersIdAndBannerGroupId($Banner->banners_id, $BannerGroup->banner_group_id);
if (!$BannerToGroup){
$BannerToGroup = $BannerManagerBannersToGroups->create();
$BannerToGroup->banners_id = $Banner->banners_id;
$BannerToGroup->banner_group_id = $BannerGroup->banner_group_id;
$BannerToGroup->save();
}


$Banner = $BannerManagerBanners->findOneByBannersName('bannergame2');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'bannergame2';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'bannergame2.jpg';
		$Banner->banners_body_thumbs = '';
		$Banner->banners_html = '';
		$Banner->banners_description = '';
		$Banner->banners_small_description = '';
		$Banner->banners_views = '0';
		$Banner->banners_clicks = '0';
		$Banner->banners_sort_order = '';
		$Banner->banners_expires_views = '0';
		$Banner->banners_expires_clicks = '0';
	$Banner->save();
}

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannerg4');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannerg4';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '720';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '303';
		$BannerGroup->banner_group_thumbs_width = '50';
		$BannerGroup->banner_group_thumbs_height = '50';
		$BannerGroup->banner_group_show_description = '0';
		$BannerGroup->banner_group_auto_rotate = '0';
		$BannerGroup->banner_group_show_custom = '0';
		$BannerGroup->banner_group_show_thumbs_desc = '0';
		$BannerGroup->banner_group_use_autoresize = '0';
		$BannerGroup->banner_group_use_thumbs = '0';
		$BannerGroup->banner_group_auto_hide_title = '0';
		$BannerGroup->banner_group_auto_hide_numbers = '0';
		$BannerGroup->banner_group_auto_hide_thumbs = '0';
		$BannerGroup->banner_group_auto_hide_arrows = '0';
		$BannerGroup->banner_group_auto_hide_thumbs_desc = '0';
		$BannerGroup->banner_group_auto_hide_custom = '0';
		$BannerGroup->banner_group_hover_pause = '0';
		$BannerGroup->banner_group_description_opacity = '0.00';
		$BannerGroup->banner_group_is_expiring = '0';
	$BannerGroup->save();
}

$BannerToGroup = $BannerManagerBannersToGroups->findOneByBannersIdAndBannerGroupId($Banner->banners_id, $BannerGroup->banner_group_id);
if (!$BannerToGroup){
$BannerToGroup = $BannerManagerBannersToGroups->create();
$BannerToGroup->banners_id = $Banner->banners_id;
$BannerToGroup->banner_group_id = $BannerGroup->banner_group_id;
$BannerToGroup->save();
}

$WidgetProperties->selected_banner_group = $BannerGroup->banner_group_id;

$Widget[139]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[139]->save();
$WidgetProperties = json_decode($Widget[140]->Configuration['widget_settings']->configuration_value);
$Pages = Doctrine_Core::getTable('Pages');
$PagesDescription = Doctrine_Core::getTable('PagesDescription');

$Page = $Pages->findOneByPageKey('welcome_pag');
if (!$Page){
$Page = $Pages->create();
$Page->sort_order = '0';
$Page->status = '1';
$Page->infobox_status = '0';
$Page->page_type = 'block';
$Page->page_key = 'welcome_pag';

$PageDescription = $PagesDescription->create();
	$PageDescription->pages_title = 'Welcome Page2';
		$PageDescription->pages_html_text = '<p>
' . "\n" . '	Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
' . "\n" . '';
		$PageDescription->intorext = '';
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
$Widget[140]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[140]->save();
$WidgetProperties = json_decode($Widget[143]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('gamerental', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[143]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[143]->save();
$WidgetProperties = json_decode($Widget[142]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('gamerental', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[142]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[142]->save();
$WidgetProperties = json_decode($Widget[496]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('gamerental', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[496]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[496]->save();
$WidgetProperties = json_decode($Widget[497]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('gamerental', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[497]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[497]->save();
$WidgetProperties = json_decode($Widget[499]->Configuration['widget_settings']->configuration_value);
$Pages = Doctrine_Core::getTable('Pages');
$PagesDescription = Doctrine_Core::getTable('PagesDescription');

$Page = $Pages->findOneByPageKey('contact_block2');
if (!$Page){
$Page = $Pages->create();
$Page->sort_order = '0';
$Page->status = '1';
$Page->infobox_status = '0';
$Page->page_type = 'block';
$Page->page_key = 'contact_block2';

$PageDescription = $PagesDescription->create();
	$PageDescription->pages_title = 'Contact Block2';
		$PageDescription->pages_html_text = '<p>
' . "\n" . '	Game Rental<br />
' . "\n" . '	Address: xyz<br />
' . "\n" . '	Phone: 123-456-7890<br />
' . "\n" . '	Email:<br />
' . "\n" . '	xyz@gamerental.com</p>
' . "\n" . '';
		$PageDescription->intorext = '';
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
$Widget[499]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[499]->save();
$WidgetProperties = json_decode($Widget[505]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('gamerental', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[505]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[505]->save();
$WidgetProperties = json_decode($Widget[506]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('gamerental', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[506]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[506]->save();
addLayoutToPage('index', 'default.php', null, $Layout[51]->layout_id);
addLayoutToPage('account', 'address_book.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'create.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'create_success.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'credit_card_details.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'cron_auto_send_return.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'cron_membership_update.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'edit.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'history.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'history_info.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'login.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'logoff.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'membership.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'membership_cancel.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'membership_info.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'membership_upgrade.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'newsletters.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'password.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'password_forgotten.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'rental_issues.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'rented_products.php', null, $Layout[79]->layout_id);
addLayoutToPage('billing_address_process', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('center_address_check', 'center_address_check.php', null, $Layout[79]->layout_id);
addLayoutToPage('checkout', 'addresses.php', null, $Layout[79]->layout_id);
addLayoutToPage('checkout', 'cart.php', null, $Layout[79]->layout_id);
addLayoutToPage('checkout', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('checkout', 'shipping_payment.php', null, $Layout[79]->layout_id);
addLayoutToPage('checkout', 'success.php', null, $Layout[79]->layout_id);
addLayoutToPage('checkout_old', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('checkout_old', 'rental_process.php', null, $Layout[79]->layout_id);
addLayoutToPage('checkout_old', 'success.php', null, $Layout[79]->layout_id);
addLayoutToPage('contact_us', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('contact_us', 'success.php', null, $Layout[79]->layout_id);
addLayoutToPage('funways', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('gv_redeem', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('index', 'nested.php', null, $Layout[79]->layout_id);
addLayoutToPage('index', 'products.php', null, $Layout[79]->layout_id);
addLayoutToPage('index', 'index.php', null, $Layout[79]->layout_id);
addLayoutToPage('product', 'info.php', null, $Layout[79]->layout_id);
addLayoutToPage('product', 'reviews.php', null, $Layout[79]->layout_id);
addLayoutToPage('products', 'all.php', null, $Layout[79]->layout_id);
addLayoutToPage('products', 'best_sellers.php', null, $Layout[79]->layout_id);
addLayoutToPage('products', 'featured.php', null, $Layout[79]->layout_id);
addLayoutToPage('products', 'new.php', null, $Layout[79]->layout_id);
addLayoutToPage('products', 'search.php', null, $Layout[79]->layout_id);
addLayoutToPage('products', 'search_result.php', null, $Layout[79]->layout_id);
addLayoutToPage('products', 'upcoming.php', null, $Layout[79]->layout_id);
addLayoutToPage('recent_additions', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('redirect', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('rentals', 'queue.php', null, $Layout[79]->layout_id);
addLayoutToPage('rentals', 'top.php', null, $Layout[79]->layout_id);
addLayoutToPage('shoppingCart', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('shopping_cart', 'shopping_cart.php', null, $Layout[79]->layout_id);
addLayoutToPage('tell_a_friend', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('viewStream', 'default.php', null, $Layout[79]->layout_id);
addLayoutToPage('viewStream', 'pull.php', null, $Layout[79]->layout_id);
addLayoutToPage('show', 'default.php', 'articleManager', $Layout[79]->layout_id);
addLayoutToPage('show', 'info.php', 'articleManager', $Layout[79]->layout_id);
addLayoutToPage('show', 'new.php', 'articleManager', $Layout[79]->layout_id);
addLayoutToPage('show', 'rss.php', 'articleManager', $Layout[79]->layout_id);
addLayoutToPage('banner_actions', 'default.php', 'bannerManager', $Layout[79]->layout_id);
addLayoutToPage('show_archive', 'default.php', 'blog', $Layout[79]->layout_id);
addLayoutToPage('show_post', 'default.php', 'blog', $Layout[79]->layout_id);
addLayoutToPage('show_page', 'default.php', 'categoriesPages', $Layout[79]->layout_id);
addLayoutToPage('simpleDownload', 'default.php', 'customFields', $Layout[79]->layout_id);
addLayoutToPage('downloads', 'default.php', 'downloadProducts', $Layout[79]->layout_id);
addLayoutToPage('downloads', 'get.php', 'downloadProducts', $Layout[79]->layout_id);
addLayoutToPage('downloads', 'listing.php', 'downloadProducts', $Layout[79]->layout_id);
addLayoutToPage('main', 'default.php', 'downloadProducts', $Layout[79]->layout_id);
addLayoutToPage('show_page', 'default.php', 'infoPages', $Layout[79]->layout_id);
addLayoutToPage('center_address_check', 'default.php', 'inventoryCenters', $Layout[79]->layout_id);
addLayoutToPage('show_inventory', 'default.php', 'inventoryCenters', $Layout[79]->layout_id);
addLayoutToPage('show_inventory', 'delivery.php', 'inventoryCenters', $Layout[79]->layout_id);
addLayoutToPage('show_inventory', 'list.php', 'inventoryCenters', $Layout[79]->layout_id);
addLayoutToPage('account_addon', 'history_inventory_info.php', 'inventoryCenters', $Layout[79]->layout_id);
addLayoutToPage('account_addon', 'view_orders_inventory.php', 'inventoryCenters', $Layout[79]->layout_id);
addLayoutToPage('show_shipping', 'default.php', 'payPerRentals', $Layout[79]->layout_id);
addLayoutToPage('build_reservation', 'default.php', 'payPerRentals', $Layout[79]->layout_id);
addLayoutToPage('address_check', 'default.php', 'payPerRentals', $Layout[79]->layout_id);
addLayoutToPage('show_event', 'default.php', 'payPerRentals', $Layout[79]->layout_id);
addLayoutToPage('show_event', 'list.php', 'payPerRentals', $Layout[79]->layout_id);
addLayoutToPage('design', 'default.php', 'productDesigner', $Layout[79]->layout_id);
addLayoutToPage('clipart', 'default.php', 'productDesigner', $Layout[79]->layout_id);
addLayoutToPage('product_review', 'default.php', 'reviews', $Layout[79]->layout_id);
addLayoutToPage('product_review', 'details.php', 'reviews', $Layout[79]->layout_id);
addLayoutToPage('product_review', 'write.php', 'reviews', $Layout[79]->layout_id);
addLayoutToPage('account_addon', 'view_royalties.php', 'royaltiesSystem', $Layout[79]->layout_id);
addLayoutToPage('show_specials', 'default.php', 'specials', $Layout[79]->layout_id);
addLayoutToPage('streams', 'default.php', 'streamProducts', $Layout[79]->layout_id);
addLayoutToPage('streams', 'listing.php', 'streamProducts', $Layout[79]->layout_id);
addLayoutToPage('streams', 'view.php', 'streamProducts', $Layout[79]->layout_id);
addLayoutToPage('main', 'default.php', 'streamProducts', $Layout[79]->layout_id);
addLayoutToPage('notify', 'cron.php', 'waitingList', $Layout[79]->layout_id);
addLayoutToPage('notify', 'default.php', 'waitingList', $Layout[79]->layout_id);
addLayoutToPage('account', 'address_book_details.php', null, $Layout[79]->layout_id);
addLayoutToPage('account', 'address_book_process.php', null, $Layout[79]->layout_id);
addLayoutToPage('show_category', 'default.php', 'blog', $Layout[79]->layout_id);
addLayoutToPage('show_category', 'rss.php', 'blog', $Layout[79]->layout_id);
