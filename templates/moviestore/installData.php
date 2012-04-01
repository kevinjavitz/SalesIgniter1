<?php
$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->create();
$Template->Configuration['NAME']->configuration_value = 'Moviestore';
$Template->Configuration['DIRECTORY']->configuration_value = 'moviestore';

$Layout[71] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[71]);
$Layout[71]->layout_name = 'All Pages';

$Container[250] = $Layout[71]->Containers->getTable()->create();
$Layout[71]->Containers->add($Container[250]);
$Container[250]->sort_order = '1';
$Container[250]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[250]->Styles['width']->definition_value = '960px';
$Container[250]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[250]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"auto","bottom":"8","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[250]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[250]->Styles['line-height']->definition_value = '1em';
$Container[250]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[250]->Configuration['shadows']->configuration_value = '[]';
$Container[250]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[250]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"auto","bottom":"8","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[250]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[250]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[250]->Configuration['id']->configuration_value = 'theContainer0';
$Container[250]->Configuration['width_unit']->configuration_value = 'px';
$Container[250]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[250]->Configuration['text_align']->configuration_value = '';
$Container[250]->Configuration['width']->configuration_value = '960';
$Container[250]->Configuration['line_height']->configuration_value = '1';
$Container[250]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[250]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

$Column[253] = $Container[250]->Columns->getTable()->create();
$Container[250]->Columns->add($Column[253]);
$Column[253]->sort_order = '1';
$Column[253]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[253]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[253]->Styles['width']->definition_value = '325px';
$Column[253]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[253]->Styles['margin']->definition_value = '{"top":20,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[253]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[253]->Styles['line-height']->definition_value = '1em';
$Column[253]->Configuration['width_unit']->configuration_value = 'px';
$Column[253]->Configuration['margin']->configuration_value = '{"top":20,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[253]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[253]->Configuration['line_height']->configuration_value = '1';
$Column[253]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[253]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[253]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[253]->Configuration['id']->configuration_value = 'theContainer3';
$Column[253]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[253]->Configuration['shadows']->configuration_value = '[]';
$Column[253]->Configuration['text_align']->configuration_value = '';
$Column[253]->Configuration['width']->configuration_value = '325';
$Column[253]->Configuration['line_height_unit']->configuration_value = 'em';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[379] = $Column[253]->Widgets->getTable()->create();
$Column[253]->Widgets->add($Widget[379]);
$Widget[379]->identifier = 'customImage';
$Widget[379]->sort_order = '1';
$Widget[379]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/moviestore/images/logom.png"}';

$Column[254] = $Container[250]->Columns->getTable()->create();
$Container[250]->Columns->add($Column[254]);
$Column[254]->sort_order = '2';
$Column[254]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[254]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[254]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[254]->Styles['width']->definition_value = '635px';
$Column[254]->Styles['line-height']->definition_value = '1em';
$Column[254]->Styles['text-align']->definition_value = 'right';
$Column[254]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[254]->Styles['margin']->definition_value = '{"top":20,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[254]->Configuration['margin']->configuration_value = '{"top":20,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[254]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[254]->Configuration['id']->configuration_value = 'theContainer5';
$Column[254]->Configuration['width']->configuration_value = '635';
$Column[254]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[254]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[254]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[254]->Configuration['shadows']->configuration_value = '[]';
$Column[254]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[254]->Configuration['line_height']->configuration_value = '1';
$Column[254]->Configuration['width_unit']->configuration_value = 'px';
$Column[254]->Configuration['text_align']->configuration_value = 'right';
$Column[254]->Configuration['line_height_unit']->configuration_value = 'em';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[380] = $Column[254]->Widgets->getTable()->create();
$Column[254]->Widgets->add($Widget[380]);
$Widget[380]->identifier = 'navigationMenu';
$Widget[380]->sort_order = '1';
$Widget[380]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"My Account"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"custom","icon_src":"/templates/moviestore/images/icon_person.png","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"Rental Queue"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"custom","icon_src":"/templates/moviestore/images/icon_cd.png","link":{"type":"app","application":"rentals","page":"queue","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Shopping Cart"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"custom","icon_src":"/templates/moviestore/images/icon_cart.png","link":{"type":"app","application":"shoppingCart","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"headerMiniNav","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[381] = $Column[254]->Widgets->getTable()->create();
$Column[254]->Widgets->add($Widget[381]);
$Widget[381]->identifier = 'customPHP';
$Widget[381]->sort_order = '2';
$Widget[381]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"php_text":"<form id=\"searchBox\" name=\"search\" action=\"<' . '?php echo itw_app_link(null,\'products\',\'search_result\');?' . '>\" method=\"get\"><span class=\"searchText\">Product search:</span><' . '?php echo tep_draw_input_field(\'keywords\', \'\', \'class=\"ui-corner-all\"\') . tep_hide_session_id();?' . '>\n      <' . '?php echo htmlBase::newElement(\'button\')->setType(\'submit\')->setText(\'Go\')->draw();?' . '>\n</form>"}';

$Container[251] = $Layout[71]->Containers->getTable()->create();
$Layout[71]->Containers->add($Container[251]);
$Container[251]->sort_order = '2';
$Container[251]->Styles['font']->definition_value = '{"color":"#ffffff","family":"Arial","size":1,"size_unit":"em"}';
$Container[251]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[251]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"40","g":"54","b":"64","a":1},"position":"0"},{"color":{"r":"24","g":"25","b":"25","a":1},"position":"1"}]}';
$Container[251]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[251]->Styles['width']->definition_value = 'auto';
$Container[251]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[251]->Styles['text-align']->definition_value = 'left';
$Container[251]->Styles['line-height']->definition_value = '1em';
$Container[251]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[251]->Configuration['id']->configuration_value = 'theContainer1_wrapper_0';
$Container[251]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[251]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[251]->Configuration['shadows']->configuration_value = '[]';
$Container[251]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[251]->Configuration['width']->configuration_value = '100';
$Container[251]->Configuration['line_height']->configuration_value = '1';
$Container[251]->Configuration['width_unit']->configuration_value = 'auto';
$Container[251]->Configuration['font']->configuration_value = '{"color":"#ffffff","family":"Arial","size":1,"size_unit":"em"}';
$Container[251]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[251]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[251]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"40","start_color_g":"54","start_color_b":"64","start_color_a":"100","end_color_r":"24","end_color_g":"25","end_color_b":"25","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[251]->Configuration['text_align']->configuration_value = 'left';
$Container[251]->Configuration['line_height_unit']->configuration_value = 'em';

$Child[252] = $Container[251]->Children->getTable()->create();
$Container[251]->Children->add($Child[252]);
$Child[252]->sort_order = '1';
$Child[252]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[252]->Styles['line-height']->definition_value = '1em';
$Child[252]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[252]->Styles['width']->definition_value = '960px';
$Child[252]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[252]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[252]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[252]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[252]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[252]->Configuration['line_height']->configuration_value = '1';
$Child[252]->Configuration['width_unit']->configuration_value = 'px';
$Child[252]->Configuration['shadows']->configuration_value = '[]';
$Child[252]->Configuration['width']->configuration_value = '960';
$Child[252]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[252]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[252]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[252]->Configuration['id']->configuration_value = 'theContainer1';
$Child[252]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[252]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[252]->Configuration['text_align']->configuration_value = '';

$Column[255] = $Child[252]->Columns->getTable()->create();
$Child[252]->Columns->add($Column[255]);
$Column[255]->sort_order = '1';
$Column[255]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[255]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[255]->Styles['width']->definition_value = '960px';
$Column[255]->Styles['line-height']->definition_value = '1em';
$Column[255]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[255]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[255]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[255]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[255]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[255]->Configuration['shadows']->configuration_value = '[]';
$Column[255]->Configuration['width']->configuration_value = '960';
$Column[255]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[255]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[255]->Configuration['line_height']->configuration_value = '1';
$Column[255]->Configuration['width_unit']->configuration_value = 'px';
$Column[255]->Configuration['text_align']->configuration_value = '';
$Column[255]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[255]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[255]->Configuration['id']->configuration_value = 'theContainer6';
$Column[255]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[382] = $Column[255]->Widgets->getTable()->create();
$Column[255]->Widgets->add($Widget[382]);
$Widget[382]->identifier = 'navigationMenu';
$Widget[382]->sort_order = '1';
$Widget[382]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"All Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Featured Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Free Trial"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"custom","url":"checkout_rental_account.php","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Blog"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_category","page":"none","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"mainNavigationMenu","forceFit":"true"}';

$Container[253] = $Layout[71]->Containers->getTable()->create();
$Layout[71]->Containers->add($Container[253]);
$Container[253]->sort_order = '3';
$Container[253]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[253]->Styles['background_solid']->definition_value = '{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}';
$Container[253]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[253]->Styles['line-height']->definition_value = '1em';
$Container[253]->Styles['margin']->definition_value = '{"top":"20","top_unit":"px","right":"0","right_unit":"auto","bottom":"5","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[253]->Styles['width']->definition_value = '960px';
$Container[253]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[253]->Styles['border_radius']->definition_value = '{"border_top_left_radius":8,"border_top_left_radius_unit":"px","border_top_right_radius":8,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[253]->Configuration['line_height']->configuration_value = '1';
$Container[253]->Configuration['width']->configuration_value = '960';
$Container[253]->Configuration['text_align']->configuration_value = '';
$Container[253]->Configuration['width_unit']->configuration_value = 'px';
$Container[253]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}}}}';
$Container[253]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[253]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[253]->Configuration['margin']->configuration_value = '{"top":"20","top_unit":"px","right":"0","right_unit":"auto","bottom":"5","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[253]->Configuration['id']->configuration_value = 'theContainer2';
$Container[253]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[253]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[253]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Container[253]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":8,"border_top_left_radius_unit":"px","border_top_right_radius":8,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[253]->Configuration['shadows']->configuration_value = '[]';

$Column[256] = $Container[253]->Columns->getTable()->create();
$Container[253]->Columns->add($Column[256]);
$Column[256]->sort_order = '1';
$Column[256]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[256]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[256]->Styles['width']->definition_value = '200px';
$Column[256]->Styles['line-height']->definition_value = '1em';
$Column[256]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[256]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[256]->Styles['margin']->definition_value = '{"top":10,"top_unit":"px","right":5,"right_unit":"px","bottom":10,"bottom_unit":"px","left":10,"left_unit":"px"}';
$Column[256]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[256]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[256]->Configuration['margin']->configuration_value = '{"top":10,"top_unit":"px","right":5,"right_unit":"px","bottom":10,"bottom_unit":"px","left":10,"left_unit":"px"}';
$Column[256]->Configuration['shadows']->configuration_value = '[]';
$Column[256]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[256]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[256]->Configuration['line_height']->configuration_value = '1';
$Column[256]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[256]->Configuration['width']->configuration_value = '200';
$Column[256]->Configuration['text_align']->configuration_value = '';
$Column[256]->Configuration['id']->configuration_value = 'theContainer7';
$Column[256]->Configuration['width_unit']->configuration_value = 'px';
$Column[256]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['categories'])){
 $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
    if (!is_object($Box['categories']) || $Box['categories']->count() <= 0){
       installInfobox('includes/modules/infoboxes/categories/', 'categories', 'null');
       $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
   }
}

$Widget[383] = $Column[256]->Widgets->getTable()->create();
$Column[256]->Widgets->add($Widget[383]);
$Widget[383]->identifier = 'categories';
$Widget[383]->sort_order = '1';
$Widget[383]->Configuration['widget_settings']->configuration_value = '{"id":"categoriesInfoBox","template_file":"box.tpl","widget_title":{"1":"Categories","48":"","49":"","50":""}}';

$Column[257] = $Container[253]->Columns->getTable()->create();
$Container[253]->Columns->add($Column[257]);
$Column[257]->sort_order = '2';
$Column[257]->Styles['line-height']->definition_value = '1em';
$Column[257]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[257]->Styles['width']->definition_value = '730px';
$Column[257]->Styles['margin']->definition_value = '{"top":10,"top_unit":"px","right":10,"right_unit":"px","bottom":10,"bottom_unit":"px","left":5,"left_unit":"px"}';
$Column[257]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[257]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[257]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[257]->Configuration['shadows']->configuration_value = '[]';
$Column[257]->Configuration['width']->configuration_value = '730';
$Column[257]->Configuration['width_unit']->configuration_value = 'px';
$Column[257]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[257]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[257]->Configuration['margin']->configuration_value = '{"top":10,"top_unit":"px","right":10,"right_unit":"px","bottom":10,"bottom_unit":"px","left":5,"left_unit":"px"}';
$Column[257]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[257]->Configuration['line_height']->configuration_value = '1';
$Column[257]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[257]->Configuration['id']->configuration_value = 'theContainer8';
$Column[257]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[257]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[257]->Configuration['text_align']->configuration_value = '';

if (!isset($Box['pageContent'])){
 $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
    if (!is_object($Box['pageContent']) || $Box['pageContent']->count() <= 0){
       installInfobox('extensions/templateManager/catalog/infoboxes/pageContent/', 'pageContent', 'templateManager');
       $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
   }
}

$Widget[385] = $Column[257]->Widgets->getTable()->create();
$Column[257]->Widgets->add($Widget[385]);
$Widget[385]->identifier = 'pageContent';
$Widget[385]->sort_order = '1';
$Widget[385]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Container[254] = $Layout[71]->Containers->getTable()->create();
$Layout[71]->Containers->add($Container[254]);
$Container[254]->sort_order = '4';
$Container[254]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"67","g":"66","b":"66","a":1},"position":"0"},{"color":{"r":"0","g":"0","b":"0","a":1},"position":"1"}]}';
$Container[254]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[254]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[254]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[254]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[254]->Styles['width']->definition_value = 'auto';
$Container[254]->Styles['line-height']->definition_value = '1em';
$Container[254]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[254]->Configuration['line_height']->configuration_value = '1';
$Container[254]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[254]->Configuration['width']->configuration_value = '950';
$Container[254]->Configuration['text_align']->configuration_value = '';
$Container[254]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[254]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[254]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[254]->Configuration['width_unit']->configuration_value = 'auto';
$Container[254]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[254]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[254]->Configuration['shadows']->configuration_value = '[]';
$Container[254]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"67","start_color_g":"66","start_color_b":"66","start_color_a":"100","end_color_r":"0","end_color_g":"0","end_color_b":"0","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[254]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[254]->Configuration['id']->configuration_value = 'theContainer9_wrapper_0';

$Child[255] = $Container[254]->Children->getTable()->create();
$Container[254]->Children->add($Child[255]);
$Child[255]->sort_order = '1';
$Child[255]->Styles['line-height']->definition_value = '1em';
$Child[255]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[255]->Styles['text-align']->definition_value = 'center';
$Child[255]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[255]->Styles['width']->definition_value = '960px';
$Child[255]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Child[255]->Styles['text']->definition_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Child[255]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[255]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[255]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[255]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[255]->Configuration['shadows']->configuration_value = '[]';
$Child[255]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[255]->Configuration['text']->configuration_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Child[255]->Configuration['id']->configuration_value = 'theContainer9';
$Child[255]->Configuration['width']->configuration_value = '960';
$Child[255]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":0,"start_vertical_pos":0,"end_horizontal_pos":0,"end_vertical_pos":100,"start_color_r":67,"start_color_g":66,"start_color_b":66,"start_color_a":100,"end_color_r":0,"end_color_g":0,"end_color_b":0,"end_color_a":100},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Child[255]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[255]->Configuration['width_unit']->configuration_value = 'px';
$Child[255]->Configuration['line_height']->configuration_value = '1';
$Child[255]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[255]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[255]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Child[255]->Configuration['text_align']->configuration_value = 'center';

$Column[258] = $Child[255]->Columns->getTable()->create();
$Child[255]->Columns->add($Column[258]);
$Column[258]->sort_order = '1';
$Column[258]->Styles['padding']->definition_value = '{"top":15,"top_unit":"px","right":0,"right_unit":"px","bottom":10,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[258]->Styles['text-align']->definition_value = 'center';
$Column[258]->Styles['line-height']->definition_value = '1em';
$Column[258]->Styles['width']->definition_value = '960px';
$Column[258]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[258]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Column[258]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[258]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[258]->Styles['text']->definition_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[258]->Configuration['padding']->configuration_value = '{"top":15,"top_unit":"px","right":0,"right_unit":"px","bottom":10,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[258]->Configuration['width_unit']->configuration_value = 'px';
$Column[258]->Configuration['width']->configuration_value = '960';
$Column[258]->Configuration['id']->configuration_value = 'theContainer10';
$Column[258]->Configuration['line_height']->configuration_value = '1';
$Column[258]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Column[258]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[258]->Configuration['text_align']->configuration_value = 'center';
$Column[258]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[258]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[258]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[258]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[258]->Configuration['shadows']->configuration_value = '[]';
$Column[258]->Configuration['text']->configuration_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[386] = $Column[258]->Widgets->getTable()->create();
$Column[258]->Widgets->add($Widget[386]);
$Widget[386]->identifier = 'navigationMenu';
$Widget[386]->sort_order = '1';
$Widget[386]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"All Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Featured Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Free Trial"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"custom","url":"checkout_rental_account.php","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Blog"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_category","page":"none","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"footerNavigationMenu","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[387] = $Column[258]->Widgets->getTable()->create();
$Column[258]->Widgets->add($Widget[387]);
$Widget[387]->identifier = 'customPHP';
$Widget[387]->sort_order = '2';
$Widget[387]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"php_text":" <br style=\"clear: both;\"><p><' . '?php echo sprintf(sysLanguage::get(\'FOOTER_TEXT_BODY\'), date(\'Y\'), STORE_NAME);?' . '><br />\n   <a href=\"http://www.rental-e-commerce-software.com\" style=\"font-size:7px;color:#6F0909;\">\n    Rental Management E-commerce Software Solution\n   </a></p>\n"}';

$Layout[70] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[70]);
$Layout[70]->layout_name = 'Home';

$Container[244] = $Layout[70]->Containers->getTable()->create();
$Layout[70]->Containers->add($Container[244]);
$Container[244]->sort_order = '1';
$Container[244]->Styles['line-height']->definition_value = '1em';
$Container[244]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[244]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"auto","bottom":"8","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[244]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[244]->Styles['width']->definition_value = '960px';
$Container[244]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[244]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[244]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"auto","bottom":"8","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[244]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[244]->Configuration['line_height']->configuration_value = '1';
$Container[244]->Configuration['text_align']->configuration_value = '';
$Container[244]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[244]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[244]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[244]->Configuration['shadows']->configuration_value = '[]';
$Container[244]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[244]->Configuration['width']->configuration_value = '960';
$Container[244]->Configuration['width_unit']->configuration_value = 'px';
$Container[244]->Configuration['id']->configuration_value = 'theContainer0';
$Container[244]->Configuration['line_height_unit']->configuration_value = 'em';

$Column[247] = $Container[244]->Columns->getTable()->create();
$Container[244]->Columns->add($Column[247]);
$Column[247]->sort_order = '1';
$Column[247]->Styles['width']->definition_value = '325px';
$Column[247]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[247]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[247]->Styles['margin']->definition_value = '{"top":20,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[247]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[247]->Styles['line-height']->definition_value = '1em';
$Column[247]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[247]->Configuration['margin']->configuration_value = '{"top":20,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[247]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[247]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[247]->Configuration['text_align']->configuration_value = '';
$Column[247]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[247]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[247]->Configuration['width']->configuration_value = '325';
$Column[247]->Configuration['width_unit']->configuration_value = 'px';
$Column[247]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[247]->Configuration['id']->configuration_value = 'theContainer3';
$Column[247]->Configuration['line_height']->configuration_value = '1';
$Column[247]->Configuration['line_height_unit']->configuration_value = 'em';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[368] = $Column[247]->Widgets->getTable()->create();
$Column[247]->Widgets->add($Widget[368]);
$Widget[368]->identifier = 'customImage';
$Widget[368]->sort_order = '1';
$Widget[368]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/moviestore/images/logom.png"}';

$Column[248] = $Container[244]->Columns->getTable()->create();
$Container[244]->Columns->add($Column[248]);
$Column[248]->sort_order = '2';
$Column[248]->Styles['margin']->definition_value = '{"top":20,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[248]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[248]->Styles['width']->definition_value = '635px';
$Column[248]->Styles['text-align']->definition_value = 'right';
$Column[248]->Styles['line-height']->definition_value = '1em';
$Column[248]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[248]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[248]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[248]->Configuration['line_height']->configuration_value = '1';
$Column[248]->Configuration['text_align']->configuration_value = 'right';
$Column[248]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[248]->Configuration['margin']->configuration_value = '{"top":20,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[248]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[248]->Configuration['width_unit']->configuration_value = 'px';
$Column[248]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[248]->Configuration['id']->configuration_value = 'theContainer5';
$Column[248]->Configuration['shadows']->configuration_value = '[]';
$Column[248]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[248]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[248]->Configuration['width']->configuration_value = '635';
$Column[248]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[369] = $Column[248]->Widgets->getTable()->create();
$Column[248]->Widgets->add($Widget[369]);
$Widget[369]->identifier = 'navigationMenu';
$Widget[369]->sort_order = '1';
$Widget[369]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"My Account"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"custom","icon_src":"/templates/moviestore/images/icon_person.png","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"Rental Queue"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"custom","icon_src":"/templates/moviestore/images/icon_cd.png","link":{"type":"app","application":"rentals","page":"queue","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Shopping Cart"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"custom","icon_src":"/templates/moviestore/images/icon_cart.png","link":{"type":"app","application":"shoppingCart","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"headerMiniNav","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[370] = $Column[248]->Widgets->getTable()->create();
$Column[248]->Widgets->add($Widget[370]);
$Widget[370]->identifier = 'customPHP';
$Widget[370]->sort_order = '2';
$Widget[370]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"php_text":"<form id=\"searchBox\" name=\"search\" action=\"<' . '?php echo itw_app_link(null,\'products\',\'search_result\');?' . '>\" method=\"get\"><span class=\"searchText\">Product search:</span><' . '?php echo tep_draw_input_field(\'keywords\', \'\', \'class=\"ui-corner-all\"\') . tep_hide_session_id();?' . '>\n      <' . '?php echo htmlBase::newElement(\'button\')->setType(\'submit\')->setText(\'Go\')->draw();?' . '>\n</form>"}';

$Container[245] = $Layout[70]->Containers->getTable()->create();
$Layout[70]->Containers->add($Container[245]);
$Container[245]->sort_order = '2';
$Container[245]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[245]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"40","g":"54","b":"64","a":1},"position":"0"},{"color":{"r":"24","g":"25","b":"25","a":1},"position":"1"}]}';
$Container[245]->Styles['text-align']->definition_value = 'left';
$Container[245]->Styles['font']->definition_value = '{"color":"#ffffff","family":"Arial","size":1,"size_unit":"em"}';
$Container[245]->Styles['line-height']->definition_value = '1em';
$Container[245]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[245]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[245]->Styles['width']->definition_value = 'auto';
$Container[245]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[245]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[245]->Configuration['width_unit']->configuration_value = 'auto';
$Container[245]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[245]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[245]->Configuration['shadows']->configuration_value = '[]';
$Container[245]->Configuration['line_height']->configuration_value = '1';
$Container[245]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[245]->Configuration['id']->configuration_value = 'theContainer1_wrapper_0';
$Container[245]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[245]->Configuration['width']->configuration_value = '100';
$Container[245]->Configuration['font']->configuration_value = '{"color":"#ffffff","family":"Arial","size":1,"size_unit":"em"}';
$Container[245]->Configuration['text_align']->configuration_value = 'left';
$Container[245]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[245]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"40","start_color_g":"54","start_color_b":"64","start_color_a":"100","end_color_r":"24","end_color_g":"25","end_color_b":"25","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';

$Child[246] = $Container[245]->Children->getTable()->create();
$Container[245]->Children->add($Child[246]);
$Child[246]->sort_order = '1';
$Child[246]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[246]->Styles['line-height']->definition_value = '1em';
$Child[246]->Styles['width']->definition_value = '960px';
$Child[246]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[246]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[246]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[246]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[246]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[246]->Configuration['line_height']->configuration_value = '1';
$Child[246]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[246]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[246]->Configuration['width']->configuration_value = '960';
$Child[246]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[246]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[246]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[246]->Configuration['width_unit']->configuration_value = 'px';
$Child[246]->Configuration['text_align']->configuration_value = '';
$Child[246]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[246]->Configuration['id']->configuration_value = 'theContainer1';
$Child[246]->Configuration['shadows']->configuration_value = '[]';

$Column[249] = $Child[246]->Columns->getTable()->create();
$Child[246]->Columns->add($Column[249]);
$Column[249]->sort_order = '1';
$Column[249]->Styles['line-height']->definition_value = '1em';
$Column[249]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[249]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[249]->Styles['width']->definition_value = '960px';
$Column[249]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[249]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[249]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[249]->Configuration['line_height']->configuration_value = '1';
$Column[249]->Configuration['shadows']->configuration_value = '[]';
$Column[249]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[249]->Configuration['id']->configuration_value = 'theContainer6';
$Column[249]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[249]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[249]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[249]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[249]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[249]->Configuration['width_unit']->configuration_value = 'px';
$Column[249]->Configuration['text_align']->configuration_value = '';
$Column[249]->Configuration['width']->configuration_value = '960';
$Column[249]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[371] = $Column[249]->Widgets->getTable()->create();
$Column[249]->Widgets->add($Widget[371]);
$Widget[371]->identifier = 'navigationMenu';
$Widget[371]->sort_order = '1';
$Widget[371]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"All Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Featured Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Free Trial"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"custom","url":"checkout_rental_account.php","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Blog"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_category","page":"none","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"mainNavigationMenu","forceFit":"true"}';

$Container[247] = $Layout[70]->Containers->getTable()->create();
$Layout[70]->Containers->add($Container[247]);
$Container[247]->sort_order = '3';
$Container[247]->Styles['background_solid']->definition_value = '{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}';
$Container[247]->Styles['border_radius']->definition_value = '{"border_top_left_radius":8,"border_top_left_radius_unit":"px","border_top_right_radius":8,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[247]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[247]->Styles['line-height']->definition_value = '1em';
$Container[247]->Styles['width']->definition_value = '960px';
$Container[247]->Styles['margin']->definition_value = '{"top":"20","top_unit":"px","right":"0","right_unit":"auto","bottom":"5","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[247]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[247]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[247]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[247]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[247]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[247]->Configuration['shadows']->configuration_value = '[]';
$Container[247]->Configuration['margin']->configuration_value = '{"top":"20","top_unit":"px","right":"0","right_unit":"auto","bottom":"5","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[247]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[247]->Configuration['width_unit']->configuration_value = 'px';
$Container[247]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":8,"border_top_left_radius_unit":"px","border_top_right_radius":8,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[247]->Configuration['text_align']->configuration_value = '';
$Container[247]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Container[247]->Configuration['id']->configuration_value = 'theContainer2';
$Container[247]->Configuration['line_height']->configuration_value = '1';
$Container[247]->Configuration['width']->configuration_value = '960';
$Container[247]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}}}}';

$Column[250] = $Container[247]->Columns->getTable()->create();
$Container[247]->Columns->add($Column[250]);
$Column[250]->sort_order = '1';
$Column[250]->Styles['width']->definition_value = '200px';
$Column[250]->Styles['line-height']->definition_value = '1em';
$Column[250]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[250]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[250]->Styles['margin']->definition_value = '{"top":10,"top_unit":"px","right":5,"right_unit":"px","bottom":10,"bottom_unit":"px","left":10,"left_unit":"px"}';
$Column[250]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[250]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[250]->Configuration['shadows']->configuration_value = '[]';
$Column[250]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[250]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[250]->Configuration['text_align']->configuration_value = '';
$Column[250]->Configuration['id']->configuration_value = 'theContainer7';
$Column[250]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[250]->Configuration['width_unit']->configuration_value = 'px';
$Column[250]->Configuration['line_height']->configuration_value = '1';
$Column[250]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[250]->Configuration['width']->configuration_value = '200';
$Column[250]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[250]->Configuration['margin']->configuration_value = '{"top":10,"top_unit":"px","right":5,"right_unit":"px","bottom":10,"bottom_unit":"px","left":10,"left_unit":"px"}';
$Column[250]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

if (!isset($Box['categories'])){
 $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
    if (!is_object($Box['categories']) || $Box['categories']->count() <= 0){
       installInfobox('includes/modules/infoboxes/categories/', 'categories', 'null');
       $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
   }
}

$Widget[372] = $Column[250]->Widgets->getTable()->create();
$Column[250]->Widgets->add($Widget[372]);
$Widget[372]->identifier = 'categories';
$Widget[372]->sort_order = '1';
$Widget[372]->Configuration['widget_settings']->configuration_value = '{"id":"categoriesInfoBox","template_file":"box.tpl","widget_title":{"1":"Categories","48":"","49":"","50":""}}';

$Column[251] = $Container[247]->Columns->getTable()->create();
$Container[247]->Columns->add($Column[251]);
$Column[251]->sort_order = '2';
$Column[251]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[251]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[251]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[251]->Styles['line-height']->definition_value = '1em';
$Column[251]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[251]->Styles['width']->definition_value = '730px';
$Column[251]->Styles['margin']->definition_value = '{"top":10,"top_unit":"px","right":10,"right_unit":"px","bottom":10,"bottom_unit":"px","left":5,"left_unit":"px"}';
$Column[251]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[251]->Configuration['margin']->configuration_value = '{"top":10,"top_unit":"px","right":10,"right_unit":"px","bottom":10,"bottom_unit":"px","left":5,"left_unit":"px"}';
$Column[251]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[251]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[251]->Configuration['width']->configuration_value = '730';
$Column[251]->Configuration['width_unit']->configuration_value = 'px';
$Column[251]->Configuration['shadows']->configuration_value = '[]';
$Column[251]->Configuration['text_align']->configuration_value = '';
$Column[251]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[251]->Configuration['id']->configuration_value = 'theContainer8';
$Column[251]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[251]->Configuration['line_height']->configuration_value = '1';
$Column[251]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';

if (!isset($Box['banner'])){
 $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
    if (!is_object($Box['banner']) || $Box['banner']->count() <= 0){
       installInfobox('extensions/bannerManager/catalog/infoboxes/banner/', 'banner', 'bannerManager');
       $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
   }
}

$Widget[374] = $Column[251]->Widgets->getTable()->create();
$Column[251]->Widgets->add($Widget[374]);
$Widget[374]->identifier = 'banner';
$Widget[374]->sort_order = '1';
$Widget[374]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"selected_banner_group":"6"}';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[375] = $Column[251]->Widgets->getTable()->create();
$Column[251]->Widgets->add($Widget[375]);
$Widget[375]->identifier = 'customText';
$Widget[375]->sort_order = '2';
$Widget[375]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"Featured Products","48":"","49":"","50":""},"selected_page":"24"}';

if (!isset($Box['customScroller'])){
 $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
    if (!is_object($Box['customScroller']) || $Box['customScroller']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customScroller/', 'customScroller', 'null');
       $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
   }
}

$Widget[376] = $Column[251]->Widgets->getTable()->create();
$Column[251]->Widgets->add($Widget[376]);
$Widget[376]->identifier = 'customScroller';
$Widget[376]->sort_order = '3';
$Widget[376]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"box.tpl","widget_title":{"1":"","48":"","49":"","50":""},"scrollers":{"type":"buttons","configs":[{"headings":{"1":"Featured Products","48":"","49":"","50":""},"query":"featured","query_limit":"25","reflect_blocks":false,"block_width":"150","block_height":"150","prev_image":"/templates/moviestore/images/scroller_prev.png","next_image":"/templates/moviestore/images/scroller_next.png"},{"headings":{"1":"New Products","48":"","49":"","50":""},"query":"featured","query_limit":"25","reflect_blocks":false,"block_width":"150","block_height":"150","prev_image":"/templates/moviestore/images/scroller_prev.png","next_image":"/templates/moviestore/images/scroller_next.png"},{"headings":{"1":"Best Sellers","48":"","49":"","50":""},"query":"featured","query_limit":"25","reflect_blocks":false,"block_width":"150","block_height":"150","prev_image":"/templates/moviestore/images/scroller_prev.png","next_image":"/templates/moviestore/images/scroller_next.png"}]}}';

$Container[248] = $Layout[70]->Containers->getTable()->create();
$Layout[70]->Containers->add($Container[248]);
$Container[248]->sort_order = '4';
$Container[248]->Styles['width']->definition_value = 'auto';
$Container[248]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[248]->Styles['line-height']->definition_value = '1em';
$Container[248]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[248]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"67","g":"66","b":"66","a":1},"position":"0"},{"color":{"r":"0","g":"0","b":"0","a":1},"position":"1"}]}';
$Container[248]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[248]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[248]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[248]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[248]->Configuration['text_align']->configuration_value = '';
$Container[248]->Configuration['width']->configuration_value = '950';
$Container[248]->Configuration['shadows']->configuration_value = '[]';
$Container[248]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[248]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[248]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[248]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[248]->Configuration['id']->configuration_value = 'theContainer9_wrapper_0';
$Container[248]->Configuration['line_height']->configuration_value = '1';
$Container[248]->Configuration['width_unit']->configuration_value = 'auto';
$Container[248]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[248]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"67","start_color_g":"66","start_color_b":"66","start_color_a":"100","end_color_r":"0","end_color_g":"0","end_color_b":"0","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[248]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';

$Child[249] = $Container[248]->Children->getTable()->create();
$Container[248]->Children->add($Child[249]);
$Child[249]->sort_order = '1';
$Child[249]->Styles['text-align']->definition_value = 'center';
$Child[249]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Child[249]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[249]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[249]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[249]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[249]->Styles['width']->definition_value = '960px';
$Child[249]->Styles['line-height']->definition_value = '1em';
$Child[249]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Child[249]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[249]->Configuration['id']->configuration_value = 'theContainer9';
$Child[249]->Configuration['text_align']->configuration_value = 'center';
$Child[249]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[249]->Configuration['line_height']->configuration_value = '1';
$Child[249]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[249]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":0,"start_vertical_pos":0,"end_horizontal_pos":0,"end_vertical_pos":100,"start_color_r":67,"start_color_g":66,"start_color_b":66,"start_color_a":100,"end_color_r":0,"end_color_g":0,"end_color_b":0,"end_color_a":100},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Child[249]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[249]->Configuration['width_unit']->configuration_value = 'px';
$Child[249]->Configuration['width']->configuration_value = '960';
$Child[249]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[249]->Configuration['shadows']->configuration_value = '[]';
$Child[249]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Child[249]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[249]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';

$Column[252] = $Child[249]->Columns->getTable()->create();
$Child[249]->Columns->add($Column[252]);
$Column[252]->sort_order = '1';
$Column[252]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[252]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Column[252]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[252]->Styles['padding']->definition_value = '{"top":15,"top_unit":"px","right":0,"right_unit":"px","bottom":10,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[252]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[252]->Styles['text']->definition_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[252]->Styles['width']->definition_value = '960px';
$Column[252]->Styles['line-height']->definition_value = '1em';
$Column[252]->Styles['text-align']->definition_value = 'center';
$Column[252]->Configuration['id']->configuration_value = 'theContainer10';
$Column[252]->Configuration['text_align']->configuration_value = 'center';
$Column[252]->Configuration['text']->configuration_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[252]->Configuration['padding']->configuration_value = '{"top":15,"top_unit":"px","right":0,"right_unit":"px","bottom":10,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[252]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Column[252]->Configuration['shadows']->configuration_value = '[]';
$Column[252]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[252]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[252]->Configuration['line_height']->configuration_value = '1';
$Column[252]->Configuration['width_unit']->configuration_value = 'px';
$Column[252]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[252]->Configuration['width']->configuration_value = '960';
$Column[252]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[252]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[377] = $Column[252]->Widgets->getTable()->create();
$Column[252]->Widgets->add($Widget[377]);
$Widget[377]->identifier = 'navigationMenu';
$Widget[377]->sort_order = '1';
$Widget[377]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"All Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Featured Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Free Trial"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"custom","url":"checkout_rental_account.php","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Blog"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_archive","page":"default","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"footerNavigationMenu","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[378] = $Column[252]->Widgets->getTable()->create();
$Column[252]->Widgets->add($Widget[378]);
$Widget[378]->identifier = 'customPHP';
$Widget[378]->sort_order = '2';
$Widget[378]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"Featured Products","48":"","49":"","50":""},"php_text":" <br style=\"clear: both;\"><p><' . '?php echo sprintf(sysLanguage::get(\'FOOTER_TEXT_BODY\'), date(\'Y\'), STORE_NAME);?' . '><br />\n   <a href=\"http://www.rental-e-commerce-software.com\" style=\"font-size:7px;color:#6F0909;\">\n    Rental Management E-commerce Software Solution\n   </a></p>\n"}';
$Template->save();
$WidgetProperties = json_decode($Widget[379]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('moviestore', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[379]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[379]->save();
$WidgetProperties = json_decode($Widget[380]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('moviestore', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[380]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[380]->save();
$WidgetProperties = json_decode($Widget[382]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('moviestore', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[382]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[382]->save();
$WidgetProperties = json_decode($Widget[386]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('moviestore', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[386]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[386]->save();
$WidgetProperties = json_decode($Widget[368]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('moviestore', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[368]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[368]->save();
$WidgetProperties = json_decode($Widget[369]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('moviestore', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[369]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[369]->save();
$WidgetProperties = json_decode($Widget[371]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('moviestore', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[371]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[371]->save();
$WidgetProperties = json_decode($Widget[374]->Configuration['widget_settings']->configuration_value);
$BannerManagerBanners = Doctrine_Core::getTable('BannerManagerBanners');
$BannerManagerGroups = Doctrine_Core::getTable('BannerManagerGroups');
$BannerManagerBannersToGroups = Doctrine_Core::getTable('BannerManagerBannersToGroups');

$Banner = $BannerManagerBanners->findOneByBannersName('banner1_movie');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'banner1_movie';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'banner192.jpg';
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

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannermiddlemovie');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannermiddlemovie';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '714';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '260';
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


$Banner = $BannerManagerBanners->findOneByBannersName('banner2_movie');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'banner2_movie';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'banner2.jpg';
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

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannermiddlemovie');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannermiddlemovie';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '714';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '260';
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


$Banner = $BannerManagerBanners->findOneByBannersName('bannermovie3');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'bannermovie3';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'bannermovie3.jpg';
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

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannermiddlemovie');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannermiddlemovie';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '714';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '260';
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

$Widget[374]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[374]->save();
$WidgetProperties = json_decode($Widget[375]->Configuration['widget_settings']->configuration_value);
$Pages = Doctrine_Core::getTable('Pages');
$PagesDescription = Doctrine_Core::getTable('PagesDescription');

$Page = $Pages->findOneByPageKey('center_movie');
if (!$Page){
$Page = $Pages->create();
$Page->sort_order = '0';
$Page->status = '1';
$Page->infobox_status = '0';
$Page->page_type = 'block';
$Page->page_key = 'center_movie';

$PageDescription = $PagesDescription->create();
	$PageDescription->pages_title = 'Center Movie';
		$PageDescription->pages_html_text = '<p>
' . "\n" . '	<img alt="" src="/images/free_trial.jpg" style="width: 389px; height: 180px;" /><img alt="" src="/images/click_here.jpg" style="width: 309px; height: 180px; margin-left: 10px;" /></p>
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
$Widget[375]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[375]->save();
$WidgetProperties = json_decode($Widget[377]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('moviestore', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[377]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[377]->save();
addLayoutToPage('account', 'address_book.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'create.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'create_success.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'credit_card_details.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'cron_auto_send_return.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'cron_membership_update.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'edit.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'history.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'history_info.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'login.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'logoff.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'membership.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'membership_cancel.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'membership_info.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'membership_upgrade.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'newsletters.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'password.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'password_forgotten.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'rental_issues.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'rented_products.php', null, $Layout[71]->layout_id);
addLayoutToPage('billing_address_process', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('center_address_check', 'center_address_check.php', null, $Layout[71]->layout_id);
addLayoutToPage('checkout', 'addresses.php', null, $Layout[71]->layout_id);
addLayoutToPage('checkout', 'cart.php', null, $Layout[71]->layout_id);
addLayoutToPage('checkout', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('checkout', 'shipping_payment.php', null, $Layout[71]->layout_id);
addLayoutToPage('checkout', 'success.php', null, $Layout[71]->layout_id);
addLayoutToPage('checkout_old', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('checkout_old', 'rental_process.php', null, $Layout[71]->layout_id);
addLayoutToPage('checkout_old', 'success.php', null, $Layout[71]->layout_id);
addLayoutToPage('contact_us', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('contact_us', 'success.php', null, $Layout[71]->layout_id);
addLayoutToPage('funways', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('gv_redeem', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('index', 'nested.php', null, $Layout[71]->layout_id);
addLayoutToPage('index', 'products.php', null, $Layout[71]->layout_id);
addLayoutToPage('index', 'index.php', null, $Layout[71]->layout_id);
addLayoutToPage('product', 'info.php', null, $Layout[71]->layout_id);
addLayoutToPage('product', 'reviews.php', null, $Layout[71]->layout_id);
addLayoutToPage('products', 'all.php', null, $Layout[71]->layout_id);
addLayoutToPage('products', 'best_sellers.php', null, $Layout[71]->layout_id);
addLayoutToPage('products', 'featured.php', null, $Layout[71]->layout_id);
addLayoutToPage('products', 'new.php', null, $Layout[71]->layout_id);
addLayoutToPage('products', 'search.php', null, $Layout[71]->layout_id);
addLayoutToPage('products', 'search_result.php', null, $Layout[71]->layout_id);
addLayoutToPage('products', 'upcoming.php', null, $Layout[71]->layout_id);
addLayoutToPage('recent_additions', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('redirect', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('rentals', 'queue.php', null, $Layout[71]->layout_id);
addLayoutToPage('rentals', 'top.php', null, $Layout[71]->layout_id);
addLayoutToPage('shoppingCart', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('shopping_cart', 'shopping_cart.php', null, $Layout[71]->layout_id);
addLayoutToPage('tell_a_friend', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('viewStream', 'default.php', null, $Layout[71]->layout_id);
addLayoutToPage('viewStream', 'pull.php', null, $Layout[71]->layout_id);
addLayoutToPage('show', 'default.php', 'articleManager', $Layout[71]->layout_id);
addLayoutToPage('show', 'info.php', 'articleManager', $Layout[71]->layout_id);
addLayoutToPage('show', 'new.php', 'articleManager', $Layout[71]->layout_id);
addLayoutToPage('show', 'rss.php', 'articleManager', $Layout[71]->layout_id);
addLayoutToPage('banner_actions', 'default.php', 'bannerManager', $Layout[71]->layout_id);
addLayoutToPage('show_archive', 'default.php', 'blog', $Layout[71]->layout_id);
addLayoutToPage('show_post', 'default.php', 'blog', $Layout[71]->layout_id);
addLayoutToPage('show_page', 'default.php', 'categoriesPages', $Layout[71]->layout_id);
addLayoutToPage('simpleDownload', 'default.php', 'customFields', $Layout[71]->layout_id);
addLayoutToPage('downloads', 'default.php', 'downloadProducts', $Layout[71]->layout_id);
addLayoutToPage('downloads', 'get.php', 'downloadProducts', $Layout[71]->layout_id);
addLayoutToPage('downloads', 'listing.php', 'downloadProducts', $Layout[71]->layout_id);
addLayoutToPage('main', 'default.php', 'downloadProducts', $Layout[71]->layout_id);
addLayoutToPage('show_page', 'default.php', 'infoPages', $Layout[71]->layout_id);
addLayoutToPage('center_address_check', 'default.php', 'inventoryCenters', $Layout[71]->layout_id);
addLayoutToPage('show_inventory', 'default.php', 'inventoryCenters', $Layout[71]->layout_id);
addLayoutToPage('show_inventory', 'delivery.php', 'inventoryCenters', $Layout[71]->layout_id);
addLayoutToPage('show_inventory', 'list.php', 'inventoryCenters', $Layout[71]->layout_id);
addLayoutToPage('account_addon', 'history_inventory_info.php', 'inventoryCenters', $Layout[71]->layout_id);
addLayoutToPage('account_addon', 'view_orders_inventory.php', 'inventoryCenters', $Layout[71]->layout_id);
addLayoutToPage('show_shipping', 'default.php', 'payPerRentals', $Layout[71]->layout_id);
addLayoutToPage('build_reservation', 'default.php', 'payPerRentals', $Layout[71]->layout_id);
addLayoutToPage('address_check', 'default.php', 'payPerRentals', $Layout[71]->layout_id);
addLayoutToPage('show_event', 'default.php', 'payPerRentals', $Layout[71]->layout_id);
addLayoutToPage('show_event', 'list.php', 'payPerRentals', $Layout[71]->layout_id);
addLayoutToPage('design', 'default.php', 'productDesigner', $Layout[71]->layout_id);
addLayoutToPage('clipart', 'default.php', 'productDesigner', $Layout[71]->layout_id);
addLayoutToPage('product_review', 'default.php', 'reviews', $Layout[71]->layout_id);
addLayoutToPage('product_review', 'details.php', 'reviews', $Layout[71]->layout_id);
addLayoutToPage('product_review', 'write.php', 'reviews', $Layout[71]->layout_id);
addLayoutToPage('account_addon', 'view_royalties.php', 'royaltiesSystem', $Layout[71]->layout_id);
addLayoutToPage('show_specials', 'default.php', 'specials', $Layout[71]->layout_id);
addLayoutToPage('streams', 'default.php', 'streamProducts', $Layout[71]->layout_id);
addLayoutToPage('streams', 'listing.php', 'streamProducts', $Layout[71]->layout_id);
addLayoutToPage('streams', 'view.php', 'streamProducts', $Layout[71]->layout_id);
addLayoutToPage('main', 'default.php', 'streamProducts', $Layout[71]->layout_id);
addLayoutToPage('notify', 'cron.php', 'waitingList', $Layout[71]->layout_id);
addLayoutToPage('notify', 'default.php', 'waitingList', $Layout[71]->layout_id);
addLayoutToPage('account', 'address_book_details.php', null, $Layout[71]->layout_id);
addLayoutToPage('account', 'address_book_process.php', null, $Layout[71]->layout_id);
addLayoutToPage('show_category', 'default.php', 'blog', $Layout[71]->layout_id);
addLayoutToPage('show_category', 'rss.php', 'blog', $Layout[71]->layout_id);
addLayoutToPage('index', 'default.php', null, $Layout[70]->layout_id);
