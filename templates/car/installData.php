<?php
$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->create();
$Template->Configuration['NAME']->configuration_value = 'Car';
$Template->Configuration['DIRECTORY']->configuration_value = 'car';

$Layout[66] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[66]);
$Layout[66]->layout_name = 'Home Page';
$Layout[66]->Styles['background_solid']->definition_value = '{"background_r":"8","background_g":"122","background_b":"203","background_a":"100"}';
$Layout[66]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[66]->Styles['width']->definition_value = 'auto';
$Layout[66]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[66]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[66]->Configuration['id']->configuration_value = '';
$Layout[66]->Configuration['width_unit']->configuration_value = 'auto';
$Layout[66]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"8","background_g":"122","background_b":"203","background_a":"100"}}}}';
$Layout[66]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[66]->Configuration['width']->configuration_value = '1024';
$Layout[66]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Layout[66]->Configuration['shadows']->configuration_value = '[]';

$Container[211] = $Layout[66]->Containers->getTable()->create();
$Layout[66]->Containers->add($Container[211]);
$Container[211]->sort_order = '1';
$Container[211]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[211]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[211]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[211]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[211]->Styles['box-shadow']->definition_value = '';
$Container[211]->Styles['line-height']->definition_value = '1em';
$Container[211]->Styles['width']->definition_value = '980px';
$Container[211]->Styles['background_complex_gradient']->definition_value = '{"type":"linear","h_pos_start":"0","v_pos_start":"0","h_pos_end":"0","v_pos_end":"100","h_pos_start_unit":"%","v_pos_start_unit":"%","h_pos_end_unit":"%","v_pos_end_unit":"%","colorStops":[{"color":{"r":"183","g":"227","b":"255","a":1},"position":"0"},{"color":{"r":"18","g":"164","b":"255","a":1},"position":"1"}]}';
$Container[211]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#1fa9ff","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[211]->Configuration['width']->configuration_value = '980';
$Container[211]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#1fa9ff","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[211]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[211]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[211]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[211]->Configuration['id']->configuration_value = 'theContainer0';
$Container[211]->Configuration['width_unit']->configuration_value = 'px';
$Container[211]->Configuration['line_height']->configuration_value = '1';
$Container[211]->Configuration['shadows']->configuration_value = '[]';
$Container[211]->Configuration['text_align']->configuration_value = '';
$Container[211]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[211]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[211]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":"0","start_vertical_pos":"0","end_horizontal_pos":"0","end_vertical_pos":"100","start_color_r":"183","start_color_g":"227","start_color_b":"255","start_color_a":"100","end_color_r":"18","end_color_g":"164","end_color_b":"255","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[211]->Configuration['line_height_unit']->configuration_value = 'em';

$Column[209] = $Container[211]->Columns->getTable()->create();
$Container[211]->Columns->add($Column[209]);
$Column[209]->sort_order = '1';
$Column[209]->Styles['width']->definition_value = '280px';
$Column[209]->Styles['line-height']->definition_value = '1em';
$Column[209]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[209]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[209]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[209]->Styles['margin']->definition_value = '{"top":2,"top_unit":"em","right":0,"right_unit":"px","bottom":2,"bottom_unit":"em","left":0,"left_unit":"px"}';
$Column[209]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[209]->Styles['text-align']->definition_value = 'center';
$Column[209]->Configuration['width_unit']->configuration_value = 'px';
$Column[209]->Configuration['margin']->configuration_value = '{"top":2,"top_unit":"em","right":0,"right_unit":"px","bottom":2,"bottom_unit":"em","left":0,"left_unit":"px"}';
$Column[209]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[209]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[209]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[209]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[209]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[209]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[209]->Configuration['width']->configuration_value = '280';
$Column[209]->Configuration['text_align']->configuration_value = 'center';
$Column[209]->Configuration['line_height']->configuration_value = '1';
$Column[209]->Configuration['id']->configuration_value = 'theContainer4';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[307] = $Column[209]->Widgets->getTable()->create();
$Column[209]->Widgets->add($Widget[307]);
$Widget[307]->identifier = 'customImage';
$Widget[307]->sort_order = '1';
$Widget[307]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/car/images/logoc.png"}';

$Column[210] = $Container[211]->Columns->getTable()->create();
$Container[211]->Columns->add($Column[210]);
$Column[210]->sort_order = '2';
$Column[210]->Styles['width']->definition_value = '700px';
$Column[210]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[210]->Styles['line-height']->definition_value = '1em';
$Column[210]->Styles['text-align']->definition_value = 'right';
$Column[210]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[210]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[210]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[210]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[210]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[210]->Configuration['text_align']->configuration_value = 'right';
$Column[210]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[210]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[210]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[210]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[210]->Configuration['width']->configuration_value = '700';
$Column[210]->Configuration['width_unit']->configuration_value = 'px';
$Column[210]->Configuration['id']->configuration_value = 'theContainer5';
$Column[210]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[210]->Configuration['line_height']->configuration_value = '1';
$Column[210]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[308] = $Column[210]->Widgets->getTable()->create();
$Column[210]->Widgets->add($Widget[308]);
$Widget[308]->identifier = 'navigationMenu';
$Widget[308]->sort_order = '1';
$Widget[308]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"My Account"},"48":{"text":"My Account"},"49":{"text":"My Account"},"50":{"text":"My Account"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"Shopping Cart"},"48":{"text":"Shopping Cart"},"49":{"text":"Shopping Cart"},"50":{"text":"Shopping Cart"},"icon":"none","icon_src":"","link":{"type":"app","application":"shoppingCart","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"headerMiniNav","forceFit":"false"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[309] = $Column[210]->Widgets->getTable()->create();
$Column[210]->Widgets->add($Widget[309]);
$Widget[309]->identifier = 'navigationMenu';
$Widget[309]->sort_order = '2';
$Widget[309]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":"Home"},"49":{"text":"Home"},"50":{"text":"Home"},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"About Us"},"48":{"text":"About Us"},"49":{"text":"About Us"},"50":{"text":"About Us"},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"cookie_usage","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"All Products"},"48":{"text":"All Products"},"49":{"text":"All Products"},"50":{"text":"All Products"},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Specials"},"48":{"text":"Specials"},"49":{"text":"Specials"},"50":{"text":"Specials"},"icon":"none","icon_src":"","link":{"type":"app","application":"specials/show_specials","page":"default","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Policies"},"48":{"text":"Policies"},"49":{"text":"Policies"},"50":{"text":"Policies"},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"conditions","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":"Contact Us"},"49":{"text":"Contact Us"},"50":{"text":"Contact Us"},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"mainNavigationMenu","forceFit":"false"}';

$Container[217] = $Layout[66]->Containers->getTable()->create();
$Layout[66]->Containers->add($Container[217]);
$Container[217]->sort_order = '2';
$Container[217]->Styles['background_solid']->definition_value = '{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}';
$Container[217]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Container[217]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[217]->Styles['width']->definition_value = '980px';
$Container[217]->Styles['border']->definition_value = '{"top":{"width":"1","width_unit":"px","color":"#09659e","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Container[217]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"auto","bottom":"0","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[217]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[217]->Configuration['shadows']->configuration_value = '[]';
$Container[217]->Configuration['id']->configuration_value = '';
$Container[217]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Container[217]->Configuration['border']->configuration_value = '{"top":{"width":"1","width_unit":"px","color":"#09659e","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Container[217]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}}}}';
$Container[217]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"auto","bottom":"0","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[217]->Configuration['width']->configuration_value = '980';
$Container[217]->Configuration['width_unit']->configuration_value = 'px';
$Container[217]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';

$Column[224] = $Container[217]->Columns->getTable()->create();
$Container[217]->Columns->add($Column[224]);
$Column[224]->sort_order = '1';
$Column[224]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[224]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"6","border_top_left_radius_unit":"px","border_top_right_radius":"6","border_top_right_radius_unit":"px","border_bottom_left_radius":"6","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"6","border_bottom_right_radius_unit":"px"}';
$Column[224]->Styles['width']->definition_value = '623px';
$Column[224]->Styles['border']->definition_value = '{"top":{"width":"1","width_unit":"px","color":"#bfbfbf","style":"solid"},"right":{"width":"1","width_unit":"px","color":"#bfbfbf","style":"solid"},"bottom":{"width":"1","width_unit":"px","color":"#bfbfbf","style":"solid"},"left":{"width":"1","width_unit":"px","color":"#bfbfbf","style":"solid"}}';
$Column[224]->Styles['margin']->definition_value = '{"top":"20","top_unit":"px","right":"5","right_unit":"px","bottom":"5","bottom_unit":"px","left":"5","left_unit":"px"}';
$Column[224]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[224]->Configuration['margin']->configuration_value = '{"top":"20","top_unit":"px","right":"5","right_unit":"px","bottom":"5","bottom_unit":"px","left":"5","left_unit":"px"}';
$Column[224]->Configuration['shadows']->configuration_value = '[]';
$Column[224]->Configuration['backgroundType']->configuration_value = '{}';
$Column[224]->Configuration['width']->configuration_value = '623';
$Column[224]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":"6","border_top_left_radius_unit":"px","border_top_right_radius":"6","border_top_right_radius_unit":"px","border_bottom_left_radius":"6","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"6","border_bottom_right_radius_unit":"px"}';
$Column[224]->Configuration['width_unit']->configuration_value = 'px';
$Column[224]->Configuration['id']->configuration_value = 'indexScroller';
$Column[224]->Configuration['border']->configuration_value = '{"top":{"width":"1","width_unit":"px","color":"#bfbfbf","style":"solid"},"right":{"width":"1","width_unit":"px","color":"#bfbfbf","style":"solid"},"bottom":{"width":"1","width_unit":"px","color":"#bfbfbf","style":"solid"},"left":{"width":"1","width_unit":"px","color":"#bfbfbf","style":"solid"}}';

if (!isset($Box['customScroller'])){
 $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
    if (!is_object($Box['customScroller']) || $Box['customScroller']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customScroller/', 'customScroller', 'null');
       $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
   }
}

$Widget[331] = $Column[224]->Widgets->getTable()->create();
$Column[224]->Widgets->add($Widget[331]);
$Widget[331]->identifier = 'customScroller';
$Widget[331]->sort_order = '1';
$Widget[331]->Configuration['widget_settings']->configuration_value = '{"id":"indexScroller","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"scrollers":{"type":"stack","configs":[{"headings":{"1":"Featured Products","48":"Featured Products","49":"Featured Products","50":"Featured Products"},"query":"featured","query_limit":"25","reflect_blocks":true,"block_width":"250","block_height":"208","prev_image":"/templates/car/images/index_scroller_prev.png","next_image":"/templates/car/images/index_scroller_next.png"}]}}';

$Column[225] = $Container[217]->Columns->getTable()->create();
$Container[217]->Columns->add($Column[225]);
$Column[225]->sort_order = '2';
$Column[225]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[225]->Styles['width']->definition_value = '335px';
$Column[225]->Styles['margin']->definition_value = '{"top":"10","top_unit":"px","right":"5","right_unit":"px","bottom":"5","bottom_unit":"px","left":"5","left_unit":"px"}';
$Column[225]->Configuration['margin']->configuration_value = '{"top":"10","top_unit":"px","right":"5","right_unit":"px","bottom":"5","bottom_unit":"px","left":"5","left_unit":"px"}';
$Column[225]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[225]->Configuration['backgroundType']->configuration_value = '{}';
$Column[225]->Configuration['width_unit']->configuration_value = 'px';
$Column[225]->Configuration['width']->configuration_value = '335';
$Column[225]->Configuration['shadows']->configuration_value = '[]';
$Column[225]->Configuration['id']->configuration_value = '';

if (!isset($Box['banner'])){
 $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
    if (!is_object($Box['banner']) || $Box['banner']->count() <= 0){
       installInfobox('extensions/imageRot/catalog/infoboxes/banner/', 'banner', 'imageRot');
       $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
   }
}

$Widget[332] = $Column[225]->Widgets->getTable()->create();
$Column[225]->Widgets->add($Widget[332]);
$Widget[332]->identifier = 'banner';
$Widget[332]->sort_order = '1';
$Widget[332]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"selected_banner_group":"9"}';

if (!isset($Box['banner'])){
 $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
    if (!is_object($Box['banner']) || $Box['banner']->count() <= 0){
       installInfobox('extensions/bannerManager/catalog/infoboxes/banner/', 'banner', 'bannerManager');
       $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
   }
}

$Widget[333] = $Column[225]->Widgets->getTable()->create();
$Column[225]->Widgets->add($Widget[333]);
$Widget[333]->identifier = 'banner';
$Widget[333]->sort_order = '2';
$Widget[333]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"selected_banner_group":"10"}';

$Container[212] = $Layout[66]->Containers->getTable()->create();
$Layout[66]->Containers->add($Container[212]);
$Container[212]->sort_order = '3';
$Container[212]->Styles['width']->definition_value = '980px';
$Container[212]->Styles['line-height']->definition_value = '1em';
$Container[212]->Styles['background_complex_gradient']->definition_value = '{"type":"linear","h_pos_start":"0","v_pos_start":"0","h_pos_end":"100","v_pos_end":"0","h_pos_start_unit":"%","v_pos_start_unit":"%","h_pos_end_unit":"%","v_pos_end_unit":"%","colorStops":[{"color":{"r":"194","g":"224","b":"245","a":1},"position":"0"},{"color":{"r":"255","g":"255","b":"255","a":1},"position":0.35},{"color":{"r":"255","g":"255","b":"255","a":1},"position":"1"}]}';
$Container[212]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[212]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Container[212]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[212]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[212]->Styles['border']->definition_value = '{"top":{"width":"0","width_unit":"px","color":"#09659e","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Container[212]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[212]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[212]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[212]->Configuration['shadows']->configuration_value = '[]';
$Container[212]->Configuration['line_height']->configuration_value = '1';
$Container[212]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[212]->Configuration['id']->configuration_value = 'theContainer2';
$Container[212]->Configuration['width_unit']->configuration_value = 'px';
$Container[212]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[212]->Configuration['width']->configuration_value = '980';
$Container[212]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":"0","border_top_left_radius_unit":"px","border_top_right_radius":"0","border_top_right_radius_unit":"px","border_bottom_left_radius":"0","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"0","border_bottom_right_radius_unit":"px"}';
$Container[212]->Configuration['border']->configuration_value = '{"top":{"width":"0","width_unit":"px","color":"#09659e","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Container[212]->Configuration['text_align']->configuration_value = '';
$Container[212]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":"0","start_vertical_pos":"0","end_horizontal_pos":"100","end_vertical_pos":"0","start_color_r":"194","start_color_g":"224","start_color_b":"245","start_color_a":"100","end_color_r":"255","end_color_g":"255","end_color_b":"255","end_color_a":"100"},"colorStops":[{"color_stop_pos":"35","color_stop_color_r":"255","color_stop_color_g":"255","color_stop_color_b":"255","color_stop_color_a":"100"}],"imagesBefore":[],"imagesAfter":[]},"solid":{"config":{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}}}}';

$Column[211] = $Container[212]->Columns->getTable()->create();
$Container[212]->Columns->add($Column[211]);
$Column[211]->sort_order = '1';
$Column[211]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[211]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[211]->Styles['width']->definition_value = '625px';
$Column[211]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[211]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[211]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[211]->Styles['margin']->definition_value = '{"top":50,"top_unit":"px","right":5,"right_unit":"px","bottom":10,"bottom_unit":"px","left":10,"left_unit":"px"}';
$Column[211]->Styles['line-height']->definition_value = '1em';
$Column[211]->Configuration['width_unit']->configuration_value = 'px';
$Column[211]->Configuration['margin']->configuration_value = '{"top":50,"top_unit":"px","right":5,"right_unit":"px","bottom":10,"bottom_unit":"px","left":10,"left_unit":"px"}';
$Column[211]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[211]->Configuration['width']->configuration_value = '625';
$Column[211]->Configuration['line_height']->configuration_value = '1';
$Column[211]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[211]->Configuration['shadows']->configuration_value = '[]';
$Column[211]->Configuration['id']->configuration_value = 'theContainer1';
$Column[211]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[211]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[211]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[211]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[211]->Configuration['text_align']->configuration_value = '';
$Column[211]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}}}}';
$Column[211]->Configuration['line_height_unit']->configuration_value = 'em';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[329] = $Column[211]->Widgets->getTable()->create();
$Column[211]->Widgets->add($Widget[329]);
$Widget[329]->identifier = 'customText';
$Widget[329]->sort_order = '1';
$Widget[329]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"selected_page":"31"}';

$Column[223] = $Container[212]->Columns->getTable()->create();
$Container[212]->Columns->add($Column[223]);
$Column[223]->sort_order = '2';
$Column[223]->Styles['background_complex_gradient']->definition_value = '{"type":"linear","h_pos_start":"0","v_pos_start":"0","h_pos_end":"0","v_pos_end":"100","h_pos_start_unit":"%","v_pos_start_unit":"%","h_pos_end_unit":"%","v_pos_end_unit":"%","colorStops":[{"color":{"r":"5","g":"104","b":"203","a":1},"position":"0"},{"color":{"r":"12","g":"158","b":"255","a":1},"position":"1"}]}';
$Column[223]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"8","border_top_left_radius_unit":"px","border_top_right_radius":"8","border_top_right_radius_unit":"px","border_bottom_left_radius":"8","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"8","border_bottom_right_radius_unit":"px"}';
$Column[223]->Styles['width']->definition_value = '325px';
$Column[223]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"20","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[223]->Styles['border']->definition_value = '{"top":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Column[223]->Styles['margin']->definition_value = '{"top":"50","top_unit":"px","right":"10","right_unit":"px","bottom":"10","bottom_unit":"px","left":"5","left_unit":"px"}';
$Column[223]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":"8","border_top_left_radius_unit":"px","border_top_right_radius":"8","border_top_right_radius_unit":"px","border_bottom_left_radius":"8","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"8","border_bottom_right_radius_unit":"px"}';
$Column[223]->Configuration['shadows']->configuration_value = '[]';
$Column[223]->Configuration['width_unit']->configuration_value = 'px';
$Column[223]->Configuration['id']->configuration_value = 'pprRight';
$Column[223]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":"0","start_vertical_pos":"0","end_horizontal_pos":"0","end_vertical_pos":"100","start_color_r":"5","start_color_g":"104","start_color_b":"203","start_color_a":"100","end_color_r":"12","end_color_g":"158","end_color_b":"255","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Column[223]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Column[223]->Configuration['border']->configuration_value = '{"top":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Column[223]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"20","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[223]->Configuration['width']->configuration_value = '325';
$Column[223]->Configuration['margin']->configuration_value = '{"top":"50","top_unit":"px","right":"10","right_unit":"px","bottom":"10","bottom_unit":"px","left":"5","left_unit":"px"}';

if (!isset($Box['payPerRental'])){
 $Box['payPerRental'] = $TemplatesInfoboxes->findOneByBoxCode('payPerRental');
    if (!is_object($Box['payPerRental']) || $Box['payPerRental']->count() <= 0){
       installInfobox('extensions/payPerRentals/catalog/infoboxes/payPerRental/', 'payPerRental', 'payPerRentals');
       $Box['payPerRental'] = $TemplatesInfoboxes->findOneByBoxCode('payPerRental');
   }
}

$Widget[330] = $Column[223]->Widgets->getTable()->create();
$Column[223]->Widgets->add($Widget[330]);
$Widget[330]->identifier = 'payPerRental';
$Widget[330]->sort_order = '1';
$Widget[330]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"pprbox2.tpl","widget_title":{"48":"","49":"","50":"","1":"MAKE A RESERVATION"}}';

$Container[213] = $Layout[66]->Containers->getTable()->create();
$Layout[66]->Containers->add($Container[213]);
$Container[213]->sort_order = '4';
$Container[213]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[213]->Styles['text']->definition_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[213]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[213]->Styles['line-height']->definition_value = '1em';
$Container[213]->Styles['text-align']->definition_value = 'center';
$Container[213]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[213]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[213]->Styles['width']->definition_value = '980px';
$Container[213]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[213]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[213]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[213]->Configuration['line_height']->configuration_value = '1';
$Container[213]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[213]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[213]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Container[213]->Configuration['id']->configuration_value = 'theContainer3';
$Container[213]->Configuration['width']->configuration_value = '980';
$Container[213]->Configuration['text']->configuration_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Container[213]->Configuration['text_align']->configuration_value = 'center';
$Container[213]->Configuration['width_unit']->configuration_value = 'px';
$Container[213]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[213]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[213]->Configuration['shadows']->configuration_value = '[]';

$Column[213] = $Container[213]->Columns->getTable()->create();
$Container[213]->Columns->add($Column[213]);
$Column[213]->sort_order = '1';
$Column[213]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[213]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[213]->Styles['line-height']->definition_value = '1em';
$Column[213]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[213]->Styles['width']->definition_value = '980px';
$Column[213]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[213]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[213]->Styles['text']->definition_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[213]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[213]->Configuration['text_align']->configuration_value = '';
$Column[213]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[213]->Configuration['text']->configuration_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[213]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[213]->Configuration['shadows']->configuration_value = '[]';
$Column[213]->Configuration['line_height']->configuration_value = '1';
$Column[213]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[213]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[213]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[213]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[213]->Configuration['width_unit']->configuration_value = 'px';
$Column[213]->Configuration['id']->configuration_value = 'theContainer10';
$Column[213]->Configuration['width']->configuration_value = '980';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[313] = $Column[213]->Widgets->getTable()->create();
$Column[213]->Widgets->add($Widget[313]);
$Widget[313]->identifier = 'customText';
$Widget[313]->sort_order = '1';
$Widget[313]->Configuration['widget_settings']->configuration_value = '{"selected_page":"19","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Layout[67] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[67]);
$Layout[67]->layout_name = 'All Pages';
$Layout[67]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[67]->Styles['background_solid']->definition_value = '{"background_r":"8","background_g":"122","background_b":"203","background_a":"100"}';
$Layout[67]->Styles['width']->definition_value = 'auto';
$Layout[67]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[67]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[67]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[67]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"8","background_g":"122","background_b":"203","background_a":"100"}}}}';
$Layout[67]->Configuration['width_unit']->configuration_value = 'auto';
$Layout[67]->Configuration['width']->configuration_value = '1024';
$Layout[67]->Configuration['shadows']->configuration_value = '[]';
$Layout[67]->Configuration['id']->configuration_value = '';
$Layout[67]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';

$Container[218] = $Layout[67]->Containers->getTable()->create();
$Layout[67]->Containers->add($Container[218]);
$Container[218]->sort_order = '1';
$Container[218]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[218]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#1fa9ff","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[218]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[218]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[218]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[218]->Styles['width']->definition_value = '980px';
$Container[218]->Styles['background_complex_gradient']->definition_value = '{"type":"linear","h_pos_start":0,"v_pos_start":0,"h_pos_end":0,"v_pos_end":100,"h_pos_start_unit":"%","v_pos_start_unit":"%","h_pos_end_unit":"%","v_pos_end_unit":"%","colorStops":[{"color":{"r":183,"g":227,"b":255,"a":1},"position":"0"},{"color":{"r":31,"g":169,"b":255,"a":1},"position":"1"}]}';
$Container[218]->Styles['line-height']->definition_value = '1em';
$Container[218]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[218]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[218]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#1fa9ff","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[218]->Configuration['line_height']->configuration_value = '1';
$Container[218]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[218]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":0,"start_vertical_pos":0,"end_horizontal_pos":0,"end_vertical_pos":100,"start_color_r":183,"start_color_g":227,"start_color_b":255,"start_color_a":100,"end_color_r":31,"end_color_g":169,"end_color_b":255,"end_color_a":100},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[218]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[218]->Configuration['width']->configuration_value = '980';
$Container[218]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[218]->Configuration['width_unit']->configuration_value = 'px';
$Container[218]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[218]->Configuration['id']->configuration_value = 'theContainer0';
$Container[218]->Configuration['text_align']->configuration_value = '';

$Column[226] = $Container[218]->Columns->getTable()->create();
$Container[218]->Columns->add($Column[226]);
$Column[226]->sort_order = '1';
$Column[226]->Styles['text-align']->definition_value = 'center';
$Column[226]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[226]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[226]->Styles['margin']->definition_value = '{"top":2,"top_unit":"em","right":0,"right_unit":"px","bottom":2,"bottom_unit":"em","left":0,"left_unit":"px"}';
$Column[226]->Styles['width']->definition_value = '280px';
$Column[226]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[226]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[226]->Styles['line-height']->definition_value = '1em';
$Column[226]->Configuration['width_unit']->configuration_value = 'px';
$Column[226]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[226]->Configuration['margin']->configuration_value = '{"top":2,"top_unit":"em","right":0,"right_unit":"px","bottom":2,"bottom_unit":"em","left":0,"left_unit":"px"}';
$Column[226]->Configuration['id']->configuration_value = 'theContainer4';
$Column[226]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[226]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[226]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[226]->Configuration['line_height']->configuration_value = '1';
$Column[226]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[226]->Configuration['width']->configuration_value = '280';
$Column[226]->Configuration['text_align']->configuration_value = 'center';
$Column[226]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[336] = $Column[226]->Widgets->getTable()->create();
$Column[226]->Widgets->add($Widget[336]);
$Widget[336]->identifier = 'customImage';
$Widget[336]->sort_order = '1';
$Widget[336]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/car/images/logoc.png"}';

$Column[227] = $Container[218]->Columns->getTable()->create();
$Container[218]->Columns->add($Column[227]);
$Column[227]->sort_order = '2';
$Column[227]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[227]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[227]->Styles['width']->definition_value = '700px';
$Column[227]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[227]->Styles['line-height']->definition_value = '1em';
$Column[227]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[227]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[227]->Styles['text-align']->definition_value = 'right';
$Column[227]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[227]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[227]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[227]->Configuration['width_unit']->configuration_value = 'px';
$Column[227]->Configuration['width']->configuration_value = '700';
$Column[227]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[227]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[227]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[227]->Configuration['text_align']->configuration_value = 'right';
$Column[227]->Configuration['id']->configuration_value = 'theContainer5';
$Column[227]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[227]->Configuration['line_height']->configuration_value = '1';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[337] = $Column[227]->Widgets->getTable()->create();
$Column[227]->Widgets->add($Widget[337]);
$Widget[337]->identifier = 'navigationMenu';
$Widget[337]->sort_order = '1';
$Widget[337]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"My Account"},"48":{"text":"My Account"},"49":{"text":"My Account"},"50":{"text":"My Account"},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"Shopping Cart"},"48":{"text":"Shopping Cart"},"49":{"text":"Shopping Cart"},"50":{"text":"Shopping Cart"},"icon":"none","icon_src":"","link":{"type":"app","application":"shoppingCart","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"headerMiniNav","forceFit":"false"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[338] = $Column[227]->Widgets->getTable()->create();
$Column[227]->Widgets->add($Widget[338]);
$Widget[338]->identifier = 'navigationMenu';
$Widget[338]->sort_order = '2';
$Widget[338]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":"Home"},"49":{"text":"Home"},"50":{"text":"Home"},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"About Us"},"48":{"text":"About Us"},"49":{"text":"About Us"},"50":{"text":"About Us"},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"cookie_usage","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"View Rental Cars"},"48":{"text":"View Rental Cars"},"49":{"text":"View Rental Cars"},"50":{"text":"View Rental Cars"},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Specials"},"48":{"text":"Specials"},"49":{"text":"Specials"},"50":{"text":"Specials"},"icon":"none","icon_src":"","link":{"type":"app","application":"specials/show_specials","page":"default","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Policies"},"48":{"text":"Policies"},"49":{"text":"Policies"},"50":{"text":"Policies"},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"conditions","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":"Contact Us"},"49":{"text":"Contact Us"},"50":{"text":"Contact Us"},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"mainNavigationMenu","forceFit":"false"}';

$Container[219] = $Layout[67]->Containers->getTable()->create();
$Layout[67]->Containers->add($Container[219]);
$Container[219]->sort_order = '2';
$Container[219]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[219]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[219]->Styles['background_complex_gradient']->definition_value = '{"type":"linear","h_pos_start":"0","v_pos_start":"0","h_pos_end":"0","v_pos_end":"100","h_pos_start_unit":"%","v_pos_start_unit":"%","h_pos_end_unit":"%","v_pos_end_unit":"%","colorStops":[{"color":{"r":"194","g":"224","b":"245","a":1},"position":"0"},{"color":{"r":"255","g":"255","b":"255","a":1},"position":"1"}]}';
$Container[219]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[219]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[219]->Styles['border']->definition_value = '{"top":{"width":1,"width_unit":"px","color":"#09659e","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[219]->Styles['width']->definition_value = '980px';
$Container[219]->Styles['line-height']->definition_value = '1em';
$Container[219]->Configuration['border']->configuration_value = '{"top":{"width":1,"width_unit":"px","color":"#09659e","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[219]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[219]->Configuration['line_height']->configuration_value = '1';
$Container[219]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":"0","start_vertical_pos":"0","end_horizontal_pos":"0","end_vertical_pos":"100","start_color_r":"194","start_color_g":"224","start_color_b":"245","start_color_a":"100","end_color_r":"255","end_color_g":"255","end_color_b":"255","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[219]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[219]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[219]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[219]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[219]->Configuration['shadows']->configuration_value = '[]';
$Container[219]->Configuration['width']->configuration_value = '980';
$Container[219]->Configuration['width_unit']->configuration_value = 'px';
$Container[219]->Configuration['id']->configuration_value = 'theContainer2';
$Container[219]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[219]->Configuration['text_align']->configuration_value = '';

$Column[228] = $Container[219]->Columns->getTable()->create();
$Container[219]->Columns->add($Column[228]);
$Column[228]->sort_order = '1';
$Column[228]->Styles['margin']->definition_value = '{"top":"50","top_unit":"px","right":"5","right_unit":"px","bottom":"10","bottom_unit":"px","left":"10","left_unit":"px"}';
$Column[228]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[228]->Styles['background_complex_gradient']->definition_value = '{"type":"linear","h_pos_start":"0","v_pos_start":"0","h_pos_end":"0","v_pos_end":"100","h_pos_start_unit":"%","v_pos_start_unit":"%","h_pos_end_unit":"%","v_pos_end_unit":"%","colorStops":[{"color":{"r":"5","g":"104","b":"203","a":1},"position":"0"},{"color":{"r":"12","g":"158","b":"255","a":1},"position":"1"}]}';
$Column[228]->Styles['border_radius']->definition_value = '{"border_top_left_radius":"5","border_top_left_radius_unit":"px","border_top_right_radius":"5","border_top_right_radius_unit":"px","border_bottom_left_radius":"5","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"5","border_bottom_right_radius_unit":"px"}';
$Column[228]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"20","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[228]->Styles['line-height']->definition_value = '1em';
$Column[228]->Styles['width']->definition_value = '220px';
$Column[228]->Styles['border']->definition_value = '{"top":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Column[228]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[228]->Configuration['margin']->configuration_value = '{"top":"50","top_unit":"px","right":"5","right_unit":"px","bottom":"10","bottom_unit":"px","left":"10","left_unit":"px"}';
$Column[228]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"20","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[228]->Configuration['shadows']->configuration_value = '[]';
$Column[228]->Configuration['text_align']->configuration_value = '';
$Column[228]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Column[228]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[228]->Configuration['width']->configuration_value = '220';
$Column[228]->Configuration['id']->configuration_value = 'theContainer1';
$Column[228]->Configuration['border']->configuration_value = '{"top":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"right":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":"0","width_unit":"px","color":"#000000","style":"solid"},"left":{"width":"0","width_unit":"px","color":"#000000","style":"solid"}}';
$Column[228]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{}},"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":"0","start_vertical_pos":"0","end_horizontal_pos":"0","end_vertical_pos":"100","start_color_r":"5","start_color_g":"104","start_color_b":"203","start_color_a":"100","end_color_r":"12","end_color_g":"158","end_color_b":"255","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Column[228]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":"5","border_top_left_radius_unit":"px","border_top_right_radius":"5","border_top_right_radius_unit":"px","border_bottom_left_radius":"5","border_bottom_left_radius_unit":"px","border_bottom_right_radius":"5","border_bottom_right_radius_unit":"px"}';
$Column[228]->Configuration['line_height']->configuration_value = '1';
$Column[228]->Configuration['width_unit']->configuration_value = 'px';

if (!isset($Box['payPerRental'])){
 $Box['payPerRental'] = $TemplatesInfoboxes->findOneByBoxCode('payPerRental');
    if (!is_object($Box['payPerRental']) || $Box['payPerRental']->count() <= 0){
       installInfobox('extensions/payPerRentals/catalog/infoboxes/payPerRental/', 'payPerRental', 'payPerRentals');
       $Box['payPerRental'] = $TemplatesInfoboxes->findOneByBoxCode('payPerRental');
   }
}

$Widget[339] = $Column[228]->Widgets->getTable()->create();
$Column[228]->Widgets->add($Widget[339]);
$Widget[339]->identifier = 'payPerRental';
$Widget[339]->sort_order = '1';
$Widget[339]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"pprbox2.tpl","widget_title":{"48":"","49":"","50":"","1":"Make A Reservation"}}';

$Column[229] = $Container[219]->Columns->getTable()->create();
$Container[219]->Columns->add($Column[229]);
$Column[229]->sort_order = '2';
$Column[229]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[229]->Styles['line-height']->definition_value = '1em';
$Column[229]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[229]->Styles['width']->definition_value = '730px';
$Column[229]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[229]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[229]->Styles['margin']->definition_value = '{"top":50,"top_unit":"px","right":5,"right_unit":"px","bottom":10,"bottom_unit":"px","left":10,"left_unit":"px"}';
$Column[229]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[229]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[229]->Configuration['margin']->configuration_value = '{"top":50,"top_unit":"px","right":5,"right_unit":"px","bottom":10,"bottom_unit":"px","left":10,"left_unit":"px"}';
$Column[229]->Configuration['id']->configuration_value = 'theContainer8';
$Column[229]->Configuration['width']->configuration_value = '730';
$Column[229]->Configuration['width_unit']->configuration_value = 'px';
$Column[229]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[229]->Configuration['line_height']->configuration_value = '1';
$Column[229]->Configuration['text_align']->configuration_value = '';
$Column[229]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[229]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[229]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

if (!isset($Box['pageStack'])){
 $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
    if (!is_object($Box['pageStack']) || $Box['pageStack']->count() <= 0){
       installInfobox('includes/modules/infoboxes/pageStack/', 'pageStack', 'null');
       $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
   }
}

$Widget[340] = $Column[229]->Widgets->getTable()->create();
$Column[229]->Widgets->add($Widget[340]);
$Widget[340]->identifier = 'pageStack';
$Widget[340]->sort_order = '1';
$Widget[340]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

if (!isset($Box['pageContent'])){
 $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
    if (!is_object($Box['pageContent']) || $Box['pageContent']->count() <= 0){
       installInfobox('extensions/templateManager/catalog/infoboxes/pageContent/', 'pageContent', 'templateManager');
       $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
   }
}

$Widget[341] = $Column[229]->Widgets->getTable()->create();
$Column[229]->Widgets->add($Widget[341]);
$Widget[341]->identifier = 'pageContent';
$Widget[341]->sort_order = '2';
$Widget[341]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Container[220] = $Layout[67]->Containers->getTable()->create();
$Layout[67]->Containers->add($Container[220]);
$Container[220]->sort_order = '3';
$Container[220]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[220]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[220]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[220]->Styles['width']->definition_value = '980px';
$Container[220]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[220]->Styles['text-align']->definition_value = 'center';
$Container[220]->Styles['line-height']->definition_value = '1em';
$Container[220]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[220]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[220]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[220]->Configuration['width']->configuration_value = '980';
$Container[220]->Configuration['width_unit']->configuration_value = 'px';
$Container[220]->Configuration['line_height']->configuration_value = '1';
$Container[220]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[220]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[220]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[220]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[220]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[220]->Configuration['id']->configuration_value = 'theContainer3';
$Container[220]->Configuration['text_align']->configuration_value = 'center';

$Column[230] = $Container[220]->Columns->getTable()->create();
$Container[220]->Columns->add($Column[230]);
$Column[230]->sort_order = '1';
$Column[230]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[230]->Styles['line-height']->definition_value = '1em';
$Column[230]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[230]->Styles['width']->definition_value = '980px';
$Column[230]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[230]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[230]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[230]->Configuration['line_height']->configuration_value = '1';
$Column[230]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[230]->Configuration['text_align']->configuration_value = '';
$Column[230]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[230]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[230]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[230]->Configuration['width_unit']->configuration_value = 'px';
$Column[230]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[230]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[230]->Configuration['id']->configuration_value = 'theContainer10';
$Column[230]->Configuration['width']->configuration_value = '980';
$Column[230]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[342] = $Column[230]->Widgets->getTable()->create();
$Column[230]->Widgets->add($Widget[342]);
$Widget[342]->identifier = 'customText';
$Widget[342]->sort_order = '1';
$Widget[342]->Configuration['widget_settings']->configuration_value = '{"selected_page":"19","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Layout[80] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[80]);
$Layout[80]->layout_name = 'Test';

$Container[335] = $Layout[80]->Containers->getTable()->create();
$Layout[80]->Containers->add($Container[335]);
$Container[335]->sort_order = '1';

$Column[337] = $Container[335]->Columns->getTable()->create();
$Container[335]->Columns->add($Column[337]);
$Column[337]->sort_order = '1';

$Container[349] = $Layout[80]->Containers->getTable()->create();
$Layout[80]->Containers->add($Container[349]);
$Container[349]->sort_order = '2';

$Column[351] = $Container[349]->Columns->getTable()->create();
$Container[349]->Columns->add($Column[351]);
$Column[351]->sort_order = '1';
$Template->save();
$WidgetProperties = json_decode($Widget[307]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('car', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[307]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[307]->save();
$WidgetProperties = json_decode($Widget[308]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('car', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[308]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[308]->save();
$WidgetProperties = json_decode($Widget[309]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('car', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[309]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[309]->save();
$WidgetProperties = json_decode($Widget[332]->Configuration['widget_settings']->configuration_value);
$BannerManagerBanners = Doctrine_Core::getTable('BannerManagerBanners');
$BannerManagerGroups = Doctrine_Core::getTable('BannerManagerGroups');
$BannerManagerBannersToGroups = Doctrine_Core::getTable('BannerManagerBannersToGroups');

$Banner = $BannerManagerBanners->findOneByBannersName('Index Banner 1');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'Index Banner 1';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'banner1.png';
		$Banner->banners_body_thumbs = '';
		$Banner->banners_html = '';
		$Banner->banners_description = '';
		$Banner->banners_small_description = '';
		$Banner->banners_views = '0';
		$Banner->banners_clicks = '11';
		$Banner->banners_sort_order = '';
		$Banner->banners_expires_views = '0';
		$Banner->banners_expires_clicks = '0';
	$Banner->save();
}

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('Index Top');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'Index Top';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '320';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '170';
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

$Widget[332]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[332]->save();
$WidgetProperties = json_decode($Widget[333]->Configuration['widget_settings']->configuration_value);
$BannerManagerBanners = Doctrine_Core::getTable('BannerManagerBanners');
$BannerManagerGroups = Doctrine_Core::getTable('BannerManagerGroups');
$BannerManagerBannersToGroups = Doctrine_Core::getTable('BannerManagerBannersToGroups');

$Banner = $BannerManagerBanners->findOneByBannersName('Index Banner 2');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'Index Banner 2';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'banner2.png';
		$Banner->banners_body_thumbs = '';
		$Banner->banners_html = '';
		$Banner->banners_description = '';
		$Banner->banners_small_description = '';
		$Banner->banners_views = '0';
		$Banner->banners_clicks = '10';
		$Banner->banners_sort_order = '';
		$Banner->banners_expires_views = '0';
		$Banner->banners_expires_clicks = '0';
	$Banner->save();
}

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('Index Bottom');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'Index Bottom';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '320';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '170';
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

$Widget[333]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[333]->save();
$WidgetProperties = json_decode($Widget[329]->Configuration['widget_settings']->configuration_value);
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
$Widget[329]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[329]->save();
$WidgetProperties = json_decode($Widget[313]->Configuration['widget_settings']->configuration_value);
$Pages = Doctrine_Core::getTable('Pages');
$PagesDescription = Doctrine_Core::getTable('PagesDescription');

$Page = $Pages->findOneByPageKey('copyright_text');
if (!$Page){
$Page = $Pages->create();
$Page->sort_order = '0';
$Page->status = '1';
$Page->infobox_status = '0';
$Page->page_type = 'block';
$Page->page_key = 'copyright_text';

$PageDescription = $PagesDescription->create();
	$PageDescription->pages_title = 'Copyright Text';
		$PageDescription->pages_html_text = '<p>
' . "\n" . '	&copy;COPYRIGHT 2010. ALL RIGHTS RESERVED.</p>
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
$Widget[313]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[313]->save();
$WidgetProperties = json_decode($Widget[336]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('car', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[336]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[336]->save();
$WidgetProperties = json_decode($Widget[337]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('car', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[337]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[337]->save();
$WidgetProperties = json_decode($Widget[338]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('car', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[338]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[338]->save();
$WidgetProperties = json_decode($Widget[342]->Configuration['widget_settings']->configuration_value);
$Pages = Doctrine_Core::getTable('Pages');
$PagesDescription = Doctrine_Core::getTable('PagesDescription');

$Page = $Pages->findOneByPageKey('copyright_text');
if (!$Page){
$Page = $Pages->create();
$Page->sort_order = '0';
$Page->status = '1';
$Page->infobox_status = '0';
$Page->page_type = 'block';
$Page->page_key = 'copyright_text';

$PageDescription = $PagesDescription->create();
	$PageDescription->pages_title = 'Copyright Text';
		$PageDescription->pages_html_text = '<p>
' . "\n" . '	&copy;COPYRIGHT 2010. ALL RIGHTS RESERVED.</p>
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
$Widget[342]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[342]->save();
addLayoutToPage('index', 'default.php', null, $Layout[66]->layout_id);
addLayoutToPage('account', 'address_book.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'create.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'create_success.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'credit_card_details.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'cron_auto_send_return.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'cron_membership_update.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'edit.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'history.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'history_info.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'login.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'logoff.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'membership.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'membership_cancel.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'membership_info.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'membership_upgrade.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'newsletters.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'password.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'password_forgotten.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'rental_issues.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'rented_products.php', null, $Layout[67]->layout_id);
addLayoutToPage('billing_address_process', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('center_address_check', 'center_address_check.php', null, $Layout[67]->layout_id);
addLayoutToPage('checkout', 'addresses.php', null, $Layout[67]->layout_id);
addLayoutToPage('checkout', 'cart.php', null, $Layout[67]->layout_id);
addLayoutToPage('checkout', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('checkout', 'shipping_payment.php', null, $Layout[67]->layout_id);
addLayoutToPage('checkout', 'success.php', null, $Layout[67]->layout_id);
addLayoutToPage('checkout_old', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('checkout_old', 'rental_process.php', null, $Layout[67]->layout_id);
addLayoutToPage('checkout_old', 'success.php', null, $Layout[67]->layout_id);
addLayoutToPage('contact_us', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('contact_us', 'success.php', null, $Layout[67]->layout_id);
addLayoutToPage('funways', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('gv_redeem', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('index', 'nested.php', null, $Layout[67]->layout_id);
addLayoutToPage('index', 'products.php', null, $Layout[67]->layout_id);
addLayoutToPage('index', 'index.php', null, $Layout[67]->layout_id);
addLayoutToPage('product', 'info.php', null, $Layout[67]->layout_id);
addLayoutToPage('product', 'reviews.php', null, $Layout[67]->layout_id);
addLayoutToPage('products', 'all.php', null, $Layout[67]->layout_id);
addLayoutToPage('products', 'best_sellers.php', null, $Layout[67]->layout_id);
addLayoutToPage('products', 'featured.php', null, $Layout[67]->layout_id);
addLayoutToPage('products', 'new.php', null, $Layout[67]->layout_id);
addLayoutToPage('products', 'search.php', null, $Layout[67]->layout_id);
addLayoutToPage('products', 'search_result.php', null, $Layout[67]->layout_id);
addLayoutToPage('products', 'upcoming.php', null, $Layout[67]->layout_id);
addLayoutToPage('recent_additions', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('redirect', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('rentals', 'queue.php', null, $Layout[67]->layout_id);
addLayoutToPage('rentals', 'top.php', null, $Layout[67]->layout_id);
addLayoutToPage('shoppingCart', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('shopping_cart', 'shopping_cart.php', null, $Layout[67]->layout_id);
addLayoutToPage('tell_a_friend', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('viewStream', 'default.php', null, $Layout[67]->layout_id);
addLayoutToPage('viewStream', 'pull.php', null, $Layout[67]->layout_id);
addLayoutToPage('show', 'default.php', 'articleManager', $Layout[67]->layout_id);
addLayoutToPage('show', 'info.php', 'articleManager', $Layout[67]->layout_id);
addLayoutToPage('show', 'new.php', 'articleManager', $Layout[67]->layout_id);
addLayoutToPage('show', 'rss.php', 'articleManager', $Layout[67]->layout_id);
addLayoutToPage('banner_actions', 'default.php', 'bannerManager', $Layout[67]->layout_id);
addLayoutToPage('show_archive', 'default.php', 'blog', $Layout[67]->layout_id);
addLayoutToPage('show_post', 'default.php', 'blog', $Layout[67]->layout_id);
addLayoutToPage('show_page', 'default.php', 'categoriesPages', $Layout[67]->layout_id);
addLayoutToPage('simpleDownload', 'default.php', 'customFields', $Layout[67]->layout_id);
addLayoutToPage('downloads', 'default.php', 'downloadProducts', $Layout[67]->layout_id);
addLayoutToPage('downloads', 'get.php', 'downloadProducts', $Layout[67]->layout_id);
addLayoutToPage('downloads', 'listing.php', 'downloadProducts', $Layout[67]->layout_id);
addLayoutToPage('main', 'default.php', 'downloadProducts', $Layout[67]->layout_id);
addLayoutToPage('show_page', 'default.php', 'infoPages', $Layout[67]->layout_id);
addLayoutToPage('center_address_check', 'default.php', 'inventoryCenters', $Layout[67]->layout_id);
addLayoutToPage('show_inventory', 'default.php', 'inventoryCenters', $Layout[67]->layout_id);
addLayoutToPage('show_inventory', 'delivery.php', 'inventoryCenters', $Layout[67]->layout_id);
addLayoutToPage('show_inventory', 'list.php', 'inventoryCenters', $Layout[67]->layout_id);
addLayoutToPage('account_addon', 'history_inventory_info.php', 'inventoryCenters', $Layout[67]->layout_id);
addLayoutToPage('account_addon', 'view_orders_inventory.php', 'inventoryCenters', $Layout[67]->layout_id);
addLayoutToPage('show_shipping', 'default.php', 'payPerRentals', $Layout[67]->layout_id);
addLayoutToPage('build_reservation', 'default.php', 'payPerRentals', $Layout[67]->layout_id);
addLayoutToPage('address_check', 'default.php', 'payPerRentals', $Layout[67]->layout_id);
addLayoutToPage('show_event', 'default.php', 'payPerRentals', $Layout[67]->layout_id);
addLayoutToPage('show_event', 'list.php', 'payPerRentals', $Layout[67]->layout_id);
addLayoutToPage('design', 'default.php', 'productDesigner', $Layout[67]->layout_id);
addLayoutToPage('clipart', 'default.php', 'productDesigner', $Layout[67]->layout_id);
addLayoutToPage('product_review', 'default.php', 'reviews', $Layout[67]->layout_id);
addLayoutToPage('product_review', 'details.php', 'reviews', $Layout[67]->layout_id);
addLayoutToPage('product_review', 'write.php', 'reviews', $Layout[67]->layout_id);
addLayoutToPage('account_addon', 'view_royalties.php', 'royaltiesSystem', $Layout[67]->layout_id);
addLayoutToPage('show_specials', 'default.php', 'specials', $Layout[67]->layout_id);
addLayoutToPage('streams', 'default.php', 'streamProducts', $Layout[67]->layout_id);
addLayoutToPage('streams', 'listing.php', 'streamProducts', $Layout[67]->layout_id);
addLayoutToPage('streams', 'view.php', 'streamProducts', $Layout[67]->layout_id);
addLayoutToPage('main', 'default.php', 'streamProducts', $Layout[67]->layout_id);
addLayoutToPage('notify', 'cron.php', 'waitingList', $Layout[67]->layout_id);
addLayoutToPage('notify', 'default.php', 'waitingList', $Layout[67]->layout_id);
addLayoutToPage('account', 'address_book_details.php', null, $Layout[67]->layout_id);
addLayoutToPage('account', 'address_book_process.php', null, $Layout[67]->layout_id);
addLayoutToPage('show_category', 'default.php', 'blog', $Layout[67]->layout_id);
addLayoutToPage('show_category', 'rss.php', 'blog', $Layout[67]->layout_id);
