<?php
$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->create();
$Template->Configuration['NAME']->configuration_value = 'Electronics';
$Template->Configuration['DIRECTORY']->configuration_value = 'electronics';

$Layout[68] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[68]);
$Layout[68]->layout_name = 'Home';
$Layout[68]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[68]->Styles['width']->definition_value = 'auto';
$Layout[68]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Layout[68]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":null,"colorStops":[{"color":{"r":"255","g":"255","b":"255","a":1},"position":"0"},{"color":{"r":"240","g":"241","b":"241","a":1},"position":"1"}]}';
$Layout[68]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[68]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1.3","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Layout[68]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[68]->Configuration['shadows']->configuration_value = '[]';
$Layout[68]->Configuration['width_unit']->configuration_value = 'auto';
$Layout[68]->Configuration['width']->configuration_value = '1024';
$Layout[68]->Configuration['id']->configuration_value = '';
$Layout[68]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Layout[68]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[68]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1.3","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Layout[68]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Layout[68]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":"0","start_vertical_pos":"0","end_horizontal_pos":"0","end_vertical_pos":"100","start_color_r":"255","start_color_g":"255","start_color_b":"255","start_color_a":"100","end_color_r":"240","end_color_g":"241","end_color_b":"241","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';

$Container[221] = $Layout[68]->Containers->getTable()->create();
$Layout[68]->Containers->add($Container[221]);
$Container[221]->sort_order = '1';
$Container[221]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[221]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"84","g":"84","b":"91","a":1},"position":"0"},{"color":{"r":"59","g":"60","b":"65","a":1},"position":0.02},{"color":{"r":"60","g":"61","b":"67","a":1},"position":0.1},{"color":{"r":"74","g":"74","b":"82","a":1},"position":0.1},{"color":{"r":"75","g":"75","b":"83","a":1},"position":0.12},{"color":{"r":"0","g":"0","b":"0","a":1},"position":0.12},{"color":{"r":"94","g":"95","b":"103","a":1},"position":0.13},{"color":{"r":"77","g":"77","b":"85","a":1},"position":0.14},{"color":{"r":"163","g":"166","b":"170","a":1},"position":"1"}]}';
$Container[221]->Styles['line-height']->definition_value = '1em';
$Container[221]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[221]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[221]->Styles['width']->definition_value = '100%';
$Container[221]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[221]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[221]->Configuration['id']->configuration_value = 'theContainer0_wrapper_0';
$Container[221]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[221]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"84","start_color_g":"84","start_color_b":"91","start_color_a":"100","end_color_r":"163","end_color_g":"166","end_color_b":"170","end_color_a":"100"},"colorStops":[{"color_stop_pos":"2","color_stop_color_r":"59","color_stop_color_g":"60","color_stop_color_b":"65","color_stop_color_a":"100"},{"color_stop_pos":"10","color_stop_color_r":"60","color_stop_color_g":"61","color_stop_color_b":"67","color_stop_color_a":"100"},{"color_stop_pos":"10","color_stop_color_r":"74","color_stop_color_g":"74","color_stop_color_b":"82","color_stop_color_a":"100"},{"color_stop_pos":"12","color_stop_color_r":"75","color_stop_color_g":"75","color_stop_color_b":"83","color_stop_color_a":"100"},{"color_stop_pos":"12","color_stop_color_r":"0","color_stop_color_g":"0","color_stop_color_b":"0","color_stop_color_a":"100"},{"color_stop_pos":"13","color_stop_color_r":"94","color_stop_color_g":"95","color_stop_color_b":"103","color_stop_color_a":"100"},{"color_stop_pos":"14","color_stop_color_r":"77","color_stop_color_g":"77","color_stop_color_b":"85","color_stop_color_a":"100"}],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[221]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[221]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[221]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[221]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[221]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[221]->Configuration['text_align']->configuration_value = '';
$Container[221]->Configuration['shadows']->configuration_value = '[]';
$Container[221]->Configuration['line_height']->configuration_value = '1';
$Container[221]->Configuration['width_unit']->configuration_value = '%';
$Container[221]->Configuration['width']->configuration_value = '100';
$Container[221]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

$Child[222] = $Container[221]->Children->getTable()->create();
$Container[221]->Children->add($Child[222]);
$Child[222]->sort_order = '1';
$Child[222]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[222]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[222]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[222]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[222]->Styles['padding']->definition_value = '{"top":25,"top_unit":"px","right":0,"right_unit":"px","bottom":22,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[222]->Styles['line-height']->definition_value = '1em';
$Child[222]->Styles['width']->definition_value = '950px';
$Child[222]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[222]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[222]->Configuration['id']->configuration_value = 'theContainer0';
$Child[222]->Configuration['width_unit']->configuration_value = 'px';
$Child[222]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[222]->Configuration['padding']->configuration_value = '{"top":25,"top_unit":"px","right":0,"right_unit":"px","bottom":22,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[222]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[222]->Configuration['width']->configuration_value = '950';
$Child[222]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[222]->Configuration['line_height']->configuration_value = '1';
$Child[222]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[222]->Configuration['shadows']->configuration_value = '[]';
$Child[222]->Configuration['text_align']->configuration_value = '';

$Column[231] = $Child[222]->Columns->getTable()->create();
$Child[222]->Columns->add($Column[231]);
$Column[231]->sort_order = '1';
$Column[231]->Styles['line-height']->definition_value = '1em';
$Column[231]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[231]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[231]->Styles['width']->definition_value = '320px';
$Column[231]->Styles['text-align']->definition_value = 'center';
$Column[231]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[231]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[231]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[231]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[231]->Configuration['line_height']->configuration_value = '1';
$Column[231]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[231]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[231]->Configuration['id']->configuration_value = 'theContainer4';
$Column[231]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[231]->Configuration['text_align']->configuration_value = 'center';
$Column[231]->Configuration['width_unit']->configuration_value = 'px';
$Column[231]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[231]->Configuration['width']->configuration_value = '320';
$Column[231]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[231]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[231]->Configuration['shadows']->configuration_value = '[]';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[343] = $Column[231]->Widgets->getTable()->create();
$Column[231]->Widgets->add($Widget[343]);
$Widget[343]->identifier = 'customImage';
$Widget[343]->sort_order = '1';
$Widget[343]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/electronics/images/logoc.png","image_link":""}';

$Column[232] = $Child[222]->Columns->getTable()->create();
$Child[222]->Columns->add($Column[232]);
$Column[232]->sort_order = '2';
$Column[232]->Styles['text-align']->definition_value = 'left';
$Column[232]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[232]->Styles['width']->definition_value = '630px';
$Column[232]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[232]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[232]->Styles['line-height']->definition_value = '1em';
$Column[232]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[232]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[232]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[232]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[232]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[232]->Configuration['text_align']->configuration_value = 'left';
$Column[232]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[232]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[232]->Configuration['line_height']->configuration_value = '1';
$Column[232]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[232]->Configuration['id']->configuration_value = 'theContainer5';
$Column[232]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[232]->Configuration['shadows']->configuration_value = '[]';
$Column[232]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[232]->Configuration['width_unit']->configuration_value = 'px';
$Column[232]->Configuration['width']->configuration_value = '630';
$Column[232]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[344] = $Column[232]->Widgets->getTable()->create();
$Column[232]->Widgets->add($Widget[344]);
$Widget[344]->identifier = 'navigationMenu';
$Widget[344]->sort_order = '1';
$Widget[344]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"My Account"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"Shopping Cart"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"shoppingCart","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"headerMiniNav","forceFit":"false"}';

$Container[223] = $Layout[68]->Containers->getTable()->create();
$Layout[68]->Containers->add($Container[223]);
$Container[223]->sort_order = '2';
$Container[223]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"1","g":"55","b":"93","a":1},"position":"0"},{"color":{"r":"14","g":"56","b":"88","a":1},"position":"1"}]}';
$Container[223]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#d3d3d4","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[223]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[223]->Styles['width']->definition_value = '100%';
$Container[223]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[223]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[223]->Styles['line-height']->definition_value = '1em';
$Container[223]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[223]->Configuration['shadows']->configuration_value = '[]';
$Container[223]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[223]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[223]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[223]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#d3d3d4","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[223]->Configuration['width_unit']->configuration_value = '%';
$Container[223]->Configuration['width']->configuration_value = '100';
$Container[223]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[223]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[223]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"1","start_color_g":"55","start_color_b":"93","start_color_a":"100","end_color_r":"14","end_color_g":"56","end_color_b":"88","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[223]->Configuration['text_align']->configuration_value = '';
$Container[223]->Configuration['id']->configuration_value = 'theContainer1_wrapper_2';
$Container[223]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[223]->Configuration['line_height']->configuration_value = '1';

$Child[224] = $Container[223]->Children->getTable()->create();
$Container[223]->Children->add($Child[224]);
$Child[224]->sort_order = '1';
$Child[224]->Styles['line-height']->definition_value = '1em';
$Child[224]->Styles['text-align']->definition_value = 'left';
$Child[224]->Styles['width']->definition_value = '100%';
$Child[224]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[224]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[224]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[224]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[224]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#b9b9b9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[224]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[224]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[224]->Configuration['shadows']->configuration_value = '[]';
$Child[224]->Configuration['width']->configuration_value = '100';
$Child[224]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[224]->Configuration['text_align']->configuration_value = 'left';
$Child[224]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[224]->Configuration['width_unit']->configuration_value = '%';
$Child[224]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[224]->Configuration['line_height']->configuration_value = '1';
$Child[224]->Configuration['id']->configuration_value = 'theContainer1_wrapper_1';
$Child[224]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[224]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#b9b9b9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

$Child[225] = $Child[224]->Children->getTable()->create();
$Child[224]->Children->add($Child[225]);
$Child[225]->sort_order = '1';
$Child[225]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[225]->Styles['line-height']->definition_value = '1em';
$Child[225]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[225]->Styles['width']->definition_value = '100%';
$Child[225]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#f9f9f9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[225]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[225]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[225]->Configuration['shadows']->configuration_value = '[]';
$Child[225]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[225]->Configuration['id']->configuration_value = 'theContainer1_wrapper_0';
$Child[225]->Configuration['width_unit']->configuration_value = '%';
$Child[225]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[225]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[225]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":0,"start_vertical_pos":0,"end_horizontal_pos":0,"end_vertical_pos":100,"start_color_r":255,"start_color_g":255,"start_color_b":255,"start_color_a":100,"end_color_r":255,"end_color_g":255,"end_color_b":255,"end_color_a":100},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Child[225]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[225]->Configuration['line_height']->configuration_value = '1';
$Child[225]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[225]->Configuration['width']->configuration_value = '100';
$Child[225]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#f9f9f9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[225]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[225]->Configuration['text_align']->configuration_value = '';

$Child[226] = $Child[225]->Children->getTable()->create();
$Child[225]->Children->add($Child[226]);
$Child[226]->sort_order = '1';
$Child[226]->Styles['line-height']->definition_value = '1em';
$Child[226]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[226]->Styles['width']->definition_value = '950px';
$Child[226]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[226]->Styles['text-align']->definition_value = 'left';
$Child[226]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[226]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[226]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#acacad","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[226]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[226]->Configuration['shadows']->configuration_value = '[]';
$Child[226]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[226]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[226]->Configuration['text_align']->configuration_value = 'left';
$Child[226]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[226]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[226]->Configuration['line_height']->configuration_value = '1';
$Child[226]->Configuration['id']->configuration_value = 'theContainer1';
$Child[226]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[226]->Configuration['width']->configuration_value = '950';
$Child[226]->Configuration['width_unit']->configuration_value = 'px';
$Child[226]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#acacad","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

$Column[233] = $Child[226]->Columns->getTable()->create();
$Child[226]->Columns->add($Column[233]);
$Column[233]->sort_order = '1';
$Column[233]->Styles['width']->definition_value = '950px';
$Column[233]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[233]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#f9f9f9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[233]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Column[233]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[233]->Styles['line-height']->definition_value = '1em';
$Column[233]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[233]->Configuration['width_unit']->configuration_value = 'px';
$Column[233]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[233]->Configuration['width']->configuration_value = '950';
$Column[233]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[233]->Configuration['line_height']->configuration_value = '1';
$Column[233]->Configuration['shadows']->configuration_value = '[]';
$Column[233]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Column[233]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[233]->Configuration['id']->configuration_value = 'theContainer9';
$Column[233]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[233]->Configuration['text_align']->configuration_value = '';
$Column[233]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#f9f9f9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[233]->Configuration['line_height_unit']->configuration_value = 'em';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[345] = $Column[233]->Widgets->getTable()->create();
$Column[233]->Widgets->add($Widget[345]);
$Widget[345]->identifier = 'navigationMenu';
$Widget[345]->sort_order = '1';
$Widget[345]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"All Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Featured Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Rental FAQ"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"gv_faq","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Blog"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_category","page":"none","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"mainNavigationMenu","forceFit":"true"}';

$Container[227] = $Layout[68]->Containers->getTable()->create();
$Layout[68]->Containers->add($Container[227]);
$Container[227]->sort_order = '3';
$Container[227]->Styles['line-height']->definition_value = '1em';
$Container[227]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[227]->Styles['width']->definition_value = '950px';
$Container[227]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[227]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[227]->Styles['margin']->definition_value = '{"top":1,"top_unit":"em","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[227]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[227]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[227]->Configuration['text_align']->configuration_value = '';
$Container[227]->Configuration['shadows']->configuration_value = '[]';
$Container[227]->Configuration['margin']->configuration_value = '{"top":1,"top_unit":"em","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[227]->Configuration['line_height']->configuration_value = '1';
$Container[227]->Configuration['width_unit']->configuration_value = 'px';
$Container[227]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[227]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[227]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[227]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[227]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[227]->Configuration['id']->configuration_value = 'theContainer2';
$Container[227]->Configuration['width']->configuration_value = '950';

$Column[234] = $Container[227]->Columns->getTable()->create();
$Container[227]->Columns->add($Column[234]);
$Column[234]->sort_order = '1';
$Column[234]->Styles['width']->definition_value = '950px';
$Column[234]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[234]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"em","left":0,"left_unit":"px"}';
$Column[234]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[234]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[234]->Styles['line-height']->definition_value = '1em';
$Column[234]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[234]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[234]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"em","left":0,"left_unit":"px"}';
$Column[234]->Configuration['width']->configuration_value = '950';
$Column[234]->Configuration['shadows']->configuration_value = '[]';
$Column[234]->Configuration['width_unit']->configuration_value = 'px';
$Column[234]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[234]->Configuration['text_align']->configuration_value = '';
$Column[234]->Configuration['line_height']->configuration_value = '1';
$Column[234]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[234]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[234]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[234]->Configuration['id']->configuration_value = 'theContainer10';
$Column[234]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

if (!isset($Box['banner'])){
 $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
    if (!is_object($Box['banner']) || $Box['banner']->count() <= 0){
       installInfobox('extensions/imageRot/catalog/infoboxes/banner/', 'banner', 'imageRot');
       $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
   }
}

$Widget[346] = $Column[234]->Widgets->getTable()->create();
$Column[234]->Widgets->add($Widget[346]);
$Widget[346]->identifier = 'banner';
$Widget[346]->sort_order = '1';
$Widget[346]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"selected_banner_group":"4"}';

$Container[228] = $Layout[68]->Containers->getTable()->create();
$Layout[68]->Containers->add($Container[228]);
$Container[228]->sort_order = '4';
$Container[228]->Styles['text-align']->definition_value = 'left';
$Container[228]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[228]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[228]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[228]->Styles['width']->definition_value = '950px';
$Container[228]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[228]->Styles['line-height']->definition_value = '1em';
$Container[228]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":10,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[228]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[228]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":10,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[228]->Configuration['id']->configuration_value = 'theContainer6_wrapper_0';
$Container[228]->Configuration['text_align']->configuration_value = 'left';
$Container[228]->Configuration['width']->configuration_value = '950';
$Container[228]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[228]->Configuration['line_height']->configuration_value = '1';
$Container[228]->Configuration['width_unit']->configuration_value = 'px';
$Container[228]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[228]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[228]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[228]->Configuration['line_height_unit']->configuration_value = 'em';

$Child[229] = $Container[228]->Children->getTable()->create();
$Container[228]->Children->add($Child[229]);
$Child[229]->sort_order = '1';
$Child[229]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[229]->Styles['line-height']->definition_value = '1em';
$Child[229]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[229]->Styles['width']->definition_value = '921px';
$Child[229]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[229]->Styles['text-align']->definition_value = 'center';
$Child[229]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#adadad","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[229]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[229]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[229]->Configuration['text_align']->configuration_value = 'center';
$Child[229]->Configuration['id']->configuration_value = 'theContainer6';
$Child[229]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[229]->Configuration['line_height']->configuration_value = '1';
$Child[229]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[229]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[229]->Configuration['width']->configuration_value = '921';
$Child[229]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[229]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#adadad","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[229]->Configuration['width_unit']->configuration_value = 'px';
$Child[229]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

$Column[235] = $Child[229]->Columns->getTable()->create();
$Child[229]->Columns->add($Column[235]);
$Column[235]->sort_order = '1';
$Column[235]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":6,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[235]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[235]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[235]->Styles['line-height']->definition_value = '0em';
$Column[235]->Styles['text-align']->definition_value = 'center';
$Column[235]->Styles['width']->definition_value = '307px';
$Column[235]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[235]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[235]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":6,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[235]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[235]->Configuration['width_unit']->configuration_value = 'px';
$Column[235]->Configuration['line_height']->configuration_value = '0';
$Column[235]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[235]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[235]->Configuration['width']->configuration_value = '307';
$Column[235]->Configuration['id']->configuration_value = 'theContainer12';
$Column[235]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[235]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[235]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[235]->Configuration['text_align']->configuration_value = 'center';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[347] = $Column[235]->Widgets->getTable()->create();
$Column[235]->Widgets->add($Widget[347]);
$Widget[347]->identifier = 'customImage';
$Widget[347]->sort_order = '1';
$Widget[347]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/electronics/images/ubanner1.png","image_link":""}';

$Column[236] = $Child[229]->Columns->getTable()->create();
$Child[229]->Columns->add($Column[236]);
$Column[236]->sort_order = '2';
$Column[236]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[236]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[236]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[236]->Styles['text-align']->definition_value = 'center';
$Column[236]->Styles['line-height']->definition_value = '0em';
$Column[236]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":6,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[236]->Styles['width']->definition_value = '307px';
$Column[236]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[236]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":6,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[236]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[236]->Configuration['line_height']->configuration_value = '0';
$Column[236]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[236]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[236]->Configuration['id']->configuration_value = 'theContainer3';
$Column[236]->Configuration['text_align']->configuration_value = 'center';
$Column[236]->Configuration['width_unit']->configuration_value = 'px';
$Column[236]->Configuration['width']->configuration_value = '307';
$Column[236]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[236]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[236]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[348] = $Column[236]->Widgets->getTable()->create();
$Column[236]->Widgets->add($Widget[348]);
$Widget[348]->identifier = 'customImage';
$Widget[348]->sort_order = '1';
$Widget[348]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/electronics/images/ubanner2.png","image_link":""}';

$Column[237] = $Child[229]->Columns->getTable()->create();
$Child[229]->Columns->add($Column[237]);
$Column[237]->sort_order = '3';
$Column[237]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[237]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[237]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[237]->Styles['line-height']->definition_value = '0em';
$Column[237]->Styles['width']->definition_value = '307px';
$Column[237]->Styles['text-align']->definition_value = 'center';
$Column[237]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":6,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[237]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[237]->Configuration['width_unit']->configuration_value = 'px';
$Column[237]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[237]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[237]->Configuration['line_height']->configuration_value = '0';
$Column[237]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":6,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[237]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[237]->Configuration['width']->configuration_value = '307';
$Column[237]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[237]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[237]->Configuration['text_align']->configuration_value = 'center';
$Column[237]->Configuration['id']->configuration_value = 'theContainer11';
$Column[237]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[349] = $Column[237]->Widgets->getTable()->create();
$Column[237]->Widgets->add($Widget[349]);
$Widget[349]->identifier = 'customImage';
$Widget[349]->sort_order = '1';
$Widget[349]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/electronics/images/ubanner3.png","image_link":""}';

$Container[230] = $Layout[68]->Containers->getTable()->create();
$Layout[68]->Containers->add($Container[230]);
$Container[230]->sort_order = '5';
$Container[230]->Styles['width']->definition_value = '950px';
$Container[230]->Styles['line-height']->definition_value = '1em';
$Container[230]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[230]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[230]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[230]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[230]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[230]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[230]->Configuration['line_height']->configuration_value = '1';
$Container[230]->Configuration['width']->configuration_value = '950';
$Container[230]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[230]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[230]->Configuration['text_align']->configuration_value = '';
$Container[230]->Configuration['id']->configuration_value = 'theContainer7';
$Container[230]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[230]->Configuration['width_unit']->configuration_value = 'px';
$Container[230]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[230]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[230]->Configuration['shadows']->configuration_value = '[]';
$Container[230]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[230]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{}}}}';

$Column[238] = $Container[230]->Columns->getTable()->create();
$Container[230]->Columns->add($Column[238]);
$Column[238]->sort_order = '1';
$Column[238]->Styles['width']->definition_value = '220px';
$Column[238]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[238]->Styles['line-height']->definition_value = '1em';
$Column[238]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[238]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[238]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":5,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[238]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[238]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[238]->Configuration['id']->configuration_value = 'theContainer13';
$Column[238]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[238]->Configuration['width']->configuration_value = '220';
$Column[238]->Configuration['width_unit']->configuration_value = 'px';
$Column[238]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":5,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[238]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[238]->Configuration['text_align']->configuration_value = '';
$Column[238]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[238]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[238]->Configuration['line_height']->configuration_value = '1';
$Column[238]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['categories'])){
 $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
    if (!is_object($Box['categories']) || $Box['categories']->count() <= 0){
       installInfobox('includes/modules/infoboxes/categories/', 'categories', 'null');
       $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
   }
}

$Widget[350] = $Column[238]->Widgets->getTable()->create();
$Column[238]->Widgets->add($Widget[350]);
$Widget[350]->identifier = 'categories';
$Widget[350]->sort_order = '1';
$Widget[350]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"box.tpl","widget_title":{"1":"Categories","48":"","49":"","50":""}}';

$Column[239] = $Container[230]->Columns->getTable()->create();
$Container[230]->Columns->add($Column[239]);
$Column[239]->sort_order = '2';
$Column[239]->Styles['line-height']->definition_value = '1em';
$Column[239]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[239]->Styles['width']->definition_value = '720px';
$Column[239]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[239]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":5,"left_unit":"px"}';
$Column[239]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[239]->Styles['text-align']->definition_value = 'left';
$Column[239]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[239]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[239]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":5,"left_unit":"px"}';
$Column[239]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[239]->Configuration['shadows']->configuration_value = '[]';
$Column[239]->Configuration['line_height']->configuration_value = '1';
$Column[239]->Configuration['id']->configuration_value = 'theContainer14';
$Column[239]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[239]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[239]->Configuration['width_unit']->configuration_value = 'px';
$Column[239]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[239]->Configuration['text_align']->configuration_value = 'left';
$Column[239]->Configuration['width']->configuration_value = '720';
$Column[239]->Configuration['line_height_unit']->configuration_value = 'em';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[351] = $Column[239]->Widgets->getTable()->create();
$Column[239]->Widgets->add($Widget[351]);
$Widget[351]->identifier = 'customText';
$Widget[351]->sort_order = '1';
$Widget[351]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"selected_page":"18"}';

if (!isset($Box['customScroller'])){
 $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
    if (!is_object($Box['customScroller']) || $Box['customScroller']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customScroller/', 'customScroller', 'null');
       $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
   }
}

$Widget[352] = $Column[239]->Widgets->getTable()->create();
$Column[239]->Widgets->add($Widget[352]);
$Widget[352]->identifier = 'customScroller';
$Widget[352]->sort_order = '2';
$Widget[352]->Configuration['widget_settings']->configuration_value = '{"id":"indexScroller","template_file":"box.tpl","widget_title":{"1":"Featured Products","48":"","49":"","50":""},"scrollers":{"type":"stack","configs":[{"headings":{"1":"Featured Products","48":"","49":"Featured Products","50":"Featured Products"},"query":"featured","query_limit":"25","reflect_blocks":false,"block_width":"185","block_height":"150","prev_image":"/templates/electronics/images/scroller_prev.png","next_image":"/templates/electronics/images/scroller_next.png"}]}}';

$Container[231] = $Layout[68]->Containers->getTable()->create();
$Layout[68]->Containers->add($Container[231]);
$Container[231]->sort_order = '6';
$Container[231]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[231]->Styles['background_solid']->definition_value = '{"background_r":"21","background_g":"33","background_b":"57","background_a":"100"}';
$Container[231]->Styles['width']->definition_value = '100%';
$Container[231]->Styles['margin']->definition_value = '{"top":1,"top_unit":"em","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[231]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[231]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[231]->Styles['line-height']->definition_value = '1em';
$Container[231]->Styles['border']->definition_value = '{"top":{"width":1,"width_unit":"px","color":"#adadad","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[231]->Configuration['text_align']->configuration_value = '';
$Container[231]->Configuration['shadows']->configuration_value = '[]';
$Container[231]->Configuration['margin']->configuration_value = '{"top":1,"top_unit":"em","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[231]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[231]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"21","background_g":"33","background_b":"57","background_a":"100"}}}}';
$Container[231]->Configuration['width_unit']->configuration_value = '%';
$Container[231]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[231]->Configuration['line_height']->configuration_value = '1';
$Container[231]->Configuration['width']->configuration_value = '100';
$Container[231]->Configuration['id']->configuration_value = 'theContainer8_wrapper_1';
$Container[231]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[231]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[231]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Container[231]->Configuration['border']->configuration_value = '{"top":{"width":1,"width_unit":"px","color":"#adadad","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

$Child[232] = $Container[231]->Children->getTable()->create();
$Container[231]->Children->add($Child[232]);
$Child[232]->sort_order = '1';
$Child[232]->Styles['line-height']->definition_value = '1em';
$Child[232]->Styles['width']->definition_value = '100%';
$Child[232]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[232]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[232]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[232]->Styles['border']->definition_value = '{"top":{"width":1,"width_unit":"px","color":"#fbfbfb","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[232]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[232]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[232]->Configuration['shadows']->configuration_value = '[]';
$Child[232]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[232]->Configuration['width_unit']->configuration_value = '%';
$Child[232]->Configuration['width']->configuration_value = '100';
$Child[232]->Configuration['text_align']->configuration_value = '';
$Child[232]->Configuration['line_height']->configuration_value = '1';
$Child[232]->Configuration['border']->configuration_value = '{"top":{"width":1,"width_unit":"px","color":"#fbfbfb","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[232]->Configuration['id']->configuration_value = 'theContainer8_wrapper_0';
$Child[232]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[232]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[232]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[232]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';

$Child[233] = $Child[232]->Children->getTable()->create();
$Child[232]->Children->add($Child[233]);
$Child[233]->sort_order = '1';
$Child[233]->Styles['line-height']->definition_value = '1em';
$Child[233]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[233]->Styles['width']->definition_value = '950px';
$Child[233]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[233]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#fbfbfb","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[233]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[233]->Styles['text-align']->definition_value = 'center';
$Child[233]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[233]->Configuration['id']->configuration_value = 'theContainer8';
$Child[233]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[233]->Configuration['width']->configuration_value = '950';
$Child[233]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[233]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[233]->Configuration['width_unit']->configuration_value = 'px';
$Child[233]->Configuration['text_align']->configuration_value = 'center';
$Child[233]->Configuration['line_height']->configuration_value = '1';
$Child[233]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[233]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#fbfbfb","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[233]->Configuration['shadows']->configuration_value = '[]';
$Child[233]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[233]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

$Column[240] = $Child[233]->Columns->getTable()->create();
$Child[233]->Columns->add($Column[240]);
$Column[240]->sort_order = '1';
$Column[240]->Styles['width']->definition_value = '950px';
$Column[240]->Styles['line-height']->definition_value = '1em';
$Column[240]->Styles['text']->definition_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[240]->Styles['text-align']->definition_value = 'center';
$Column[240]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[240]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[240]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[240]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[240]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[240]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[240]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[240]->Configuration['text_align']->configuration_value = 'center';
$Column[240]->Configuration['text']->configuration_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[240]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[240]->Configuration['width']->configuration_value = '950';
$Column[240]->Configuration['width_unit']->configuration_value = 'px';
$Column[240]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[240]->Configuration['shadows']->configuration_value = '[]';
$Column[240]->Configuration['line_height']->configuration_value = '1';
$Column[240]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[240]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[240]->Configuration['id']->configuration_value = 'theContainer15';
$Column[240]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[477] = $Column[240]->Widgets->getTable()->create();
$Column[240]->Widgets->add($Widget[477]);
$Widget[477]->identifier = 'navigationMenu';
$Widget[477]->sort_order = '1';
$Widget[477]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"linked_to":"345","menuId":"footerNavigationMenu","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[354] = $Column[240]->Widgets->getTable()->create();
$Column[240]->Widgets->add($Widget[354]);
$Widget[354]->identifier = 'customPHP';
$Widget[354]->sort_order = '2';
$Widget[354]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"php_text":" <br style=\"clear: both;\"><p><' . '?php echo sprintf(sysLanguage::get(\'FOOTER_TEXT_BODY\'), date(\'Y\'), STORE_NAME);?' . '><br />\n   <a href=\"http://www.rental-e-commerce-software.com\" style=\"font-size:7px;color:#6F0909;\">\n    Rental Management E-commerce Software Solution\n   </a></p>\n"}';

$Layout[78] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[78]);
$Layout[78]->layout_name = 'All Pages';
$Layout[78]->Styles['width']->definition_value = 'auto';
$Layout[78]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Layout[78]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":null,"colorStops":[{"color":{"r":"255","g":"255","b":"255","a":1},"position":"0"},{"color":{"r":"240","g":"241","b":"241","a":1},"position":"1"}]}';
$Layout[78]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[78]->Styles['margin']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[78]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1.3","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Layout[78]->Configuration['id']->configuration_value = '';
$Layout[78]->Configuration['width']->configuration_value = '1024';
$Layout[78]->Configuration['width_unit']->configuration_value = 'auto';
$Layout[78]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[78]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Layout[78]->Configuration['shadows']->configuration_value = '[]';
$Layout[78]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1.3","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Layout[78]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":"0","start_vertical_pos":"0","end_horizontal_pos":"0","end_vertical_pos":"100","start_color_r":"255","start_color_g":"255","start_color_b":"255","start_color_a":"100","end_color_r":"240","end_color_g":"241","end_color_b":"241","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Layout[78]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Layout[78]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';

$Container[309] = $Layout[78]->Containers->getTable()->create();
$Layout[78]->Containers->add($Container[309]);
$Container[309]->sort_order = '1';
$Container[309]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[309]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[309]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[309]->Styles['width']->definition_value = '100%';
$Container[309]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[309]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"84","g":"84","b":"91","a":1},"position":"0"},{"color":{"r":"59","g":"60","b":"65","a":1},"position":0.02},{"color":{"r":"60","g":"61","b":"67","a":1},"position":0.1},{"color":{"r":"74","g":"74","b":"82","a":1},"position":0.1},{"color":{"r":"75","g":"75","b":"83","a":1},"position":0.12},{"color":{"r":"0","g":"0","b":"0","a":1},"position":0.12},{"color":{"r":"94","g":"95","b":"103","a":1},"position":0.13},{"color":{"r":"77","g":"77","b":"85","a":1},"position":0.14},{"color":{"r":"163","g":"166","b":"170","a":1},"position":"1"}]}';
$Container[309]->Styles['line-height']->definition_value = '1em';
$Container[309]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[309]->Configuration['shadows']->configuration_value = '[]';
$Container[309]->Configuration['width']->configuration_value = '100';
$Container[309]->Configuration['line_height']->configuration_value = '1';
$Container[309]->Configuration['width_unit']->configuration_value = '%';
$Container[309]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[309]->Configuration['text_align']->configuration_value = '';
$Container[309]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[309]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[309]->Configuration['id']->configuration_value = 'theContainer0_wrapper_0';
$Container[309]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[309]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[309]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"84","start_color_g":"84","start_color_b":"91","start_color_a":"100","end_color_r":"163","end_color_g":"166","end_color_b":"170","end_color_a":"100"},"colorStops":[{"color_stop_pos":"2","color_stop_color_r":"59","color_stop_color_g":"60","color_stop_color_b":"65","color_stop_color_a":"100"},{"color_stop_pos":"10","color_stop_color_r":"60","color_stop_color_g":"61","color_stop_color_b":"67","color_stop_color_a":"100"},{"color_stop_pos":"10","color_stop_color_r":"74","color_stop_color_g":"74","color_stop_color_b":"82","color_stop_color_a":"100"},{"color_stop_pos":"12","color_stop_color_r":"75","color_stop_color_g":"75","color_stop_color_b":"83","color_stop_color_a":"100"},{"color_stop_pos":"12","color_stop_color_r":"0","color_stop_color_g":"0","color_stop_color_b":"0","color_stop_color_a":"100"},{"color_stop_pos":"13","color_stop_color_r":"94","color_stop_color_g":"95","color_stop_color_b":"103","color_stop_color_a":"100"},{"color_stop_pos":"14","color_stop_color_r":"77","color_stop_color_g":"77","color_stop_color_b":"85","color_stop_color_a":"100"}],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[309]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[309]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

$Child[310] = $Container[309]->Children->getTable()->create();
$Container[309]->Children->add($Child[310]);
$Child[310]->sort_order = '1';
$Child[310]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[310]->Styles['width']->definition_value = '950px';
$Child[310]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[310]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[310]->Styles['line-height']->definition_value = '1em';
$Child[310]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[310]->Styles['padding']->definition_value = '{"top":25,"top_unit":"px","right":0,"right_unit":"px","bottom":22,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[310]->Configuration['text_align']->configuration_value = '';
$Child[310]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[310]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[310]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[310]->Configuration['id']->configuration_value = 'theContainer0';
$Child[310]->Configuration['width_unit']->configuration_value = 'px';
$Child[310]->Configuration['line_height']->configuration_value = '1';
$Child[310]->Configuration['padding']->configuration_value = '{"top":25,"top_unit":"px","right":0,"right_unit":"px","bottom":22,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[310]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[310]->Configuration['width']->configuration_value = '950';
$Child[310]->Configuration['shadows']->configuration_value = '[]';
$Child[310]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[310]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

$Column[315] = $Child[310]->Columns->getTable()->create();
$Child[310]->Columns->add($Column[315]);
$Column[315]->sort_order = '1';
$Column[315]->Styles['width']->definition_value = '320px';
$Column[315]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[315]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[315]->Styles['text-align']->definition_value = 'center';
$Column[315]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[315]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[315]->Styles['line-height']->definition_value = '1em';
$Column[315]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[315]->Configuration['text_align']->configuration_value = 'center';
$Column[315]->Configuration['line_height']->configuration_value = '1';
$Column[315]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[315]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[315]->Configuration['width_unit']->configuration_value = 'px';
$Column[315]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[315]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[315]->Configuration['id']->configuration_value = 'theContainer4';
$Column[315]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[315]->Configuration['width']->configuration_value = '320';
$Column[315]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[315]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[315]->Configuration['shadows']->configuration_value = '[]';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[482] = $Column[315]->Widgets->getTable()->create();
$Column[315]->Widgets->add($Widget[482]);
$Widget[482]->identifier = 'customImage';
$Widget[482]->sort_order = '1';
$Widget[482]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/electronics/images/logoc.png","image_link":""}';

$Column[316] = $Child[310]->Columns->getTable()->create();
$Child[310]->Columns->add($Column[316]);
$Column[316]->sort_order = '2';
$Column[316]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[316]->Styles['line-height']->definition_value = '1em';
$Column[316]->Styles['text']->definition_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[316]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[316]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[316]->Styles['text-align']->definition_value = 'left';
$Column[316]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[316]->Styles['width']->definition_value = '630px';
$Column[316]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[316]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[316]->Configuration['id']->configuration_value = 'theContainer5';
$Column[316]->Configuration['line_height']->configuration_value = '1';
$Column[316]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[316]->Configuration['text_align']->configuration_value = 'left';
$Column[316]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[316]->Configuration['text']->configuration_value = '{"color":"#000000","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"left","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"top","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[316]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[316]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[316]->Configuration['shadows']->configuration_value = '[]';
$Column[316]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[316]->Configuration['width']->configuration_value = '630';
$Column[316]->Configuration['width_unit']->configuration_value = 'px';
$Column[316]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[483] = $Column[316]->Widgets->getTable()->create();
$Column[316]->Widgets->add($Widget[483]);
$Widget[483]->identifier = 'navigationMenu';
$Widget[483]->sort_order = '1';
$Widget[483]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"My Account"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"Shopping Cart"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"shoppingCart","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"headerMiniNav","forceFit":"false"}';

$Container[311] = $Layout[78]->Containers->getTable()->create();
$Layout[78]->Containers->add($Container[311]);
$Container[311]->sort_order = '2';
$Container[311]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[311]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[311]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[311]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[311]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"1","g":"55","b":"93","a":1},"position":"0"},{"color":{"r":"14","g":"56","b":"88","a":1},"position":"1"}]}';
$Container[311]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#d3d3d4","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[311]->Styles['line-height']->definition_value = '1em';
$Container[311]->Styles['width']->definition_value = '100%';
$Container[311]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[311]->Configuration['text_align']->configuration_value = '';
$Container[311]->Configuration['shadows']->configuration_value = '[]';
$Container[311]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[311]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[311]->Configuration['width']->configuration_value = '100';
$Container[311]->Configuration['width_unit']->configuration_value = '%';
$Container[311]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[311]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#d3d3d4","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[311]->Configuration['id']->configuration_value = 'theContainer1_wrapper_2';
$Container[311]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"1","start_color_g":"55","start_color_b":"93","start_color_a":"100","end_color_r":"14","end_color_g":"56","end_color_b":"88","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[311]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[311]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[311]->Configuration['line_height']->configuration_value = '1';

$Child[312] = $Container[311]->Children->getTable()->create();
$Container[311]->Children->add($Child[312]);
$Child[312]->sort_order = '1';
$Child[312]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[312]->Styles['width']->definition_value = '100%';
$Child[312]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[312]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[312]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#b9b9b9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[312]->Styles['line-height']->definition_value = '1em';
$Child[312]->Styles['text-align']->definition_value = 'left';
$Child[312]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[312]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[312]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#b9b9b9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[312]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[312]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[312]->Configuration['shadows']->configuration_value = '[]';
$Child[312]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[312]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[312]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[312]->Configuration['text_align']->configuration_value = 'left';
$Child[312]->Configuration['width_unit']->configuration_value = '%';
$Child[312]->Configuration['width']->configuration_value = '100';
$Child[312]->Configuration['line_height']->configuration_value = '1';
$Child[312]->Configuration['id']->configuration_value = 'theContainer1_wrapper_1';

$Child[313] = $Child[312]->Children->getTable()->create();
$Child[312]->Children->add($Child[313]);
$Child[313]->sort_order = '1';
$Child[313]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[313]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[313]->Styles['line-height']->definition_value = '1em';
$Child[313]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[313]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[313]->Styles['width']->definition_value = '100%';
$Child[313]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#f9f9f9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[313]->Configuration['shadows']->configuration_value = '[]';
$Child[313]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","start_horizontal_pos":0,"start_vertical_pos":0,"end_horizontal_pos":0,"end_vertical_pos":100,"start_color_r":255,"start_color_g":255,"start_color_b":255,"start_color_a":100,"end_color_r":255,"end_color_g":255,"end_color_b":255,"end_color_a":100},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Child[313]->Configuration['width_unit']->configuration_value = '%';
$Child[313]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[313]->Configuration['id']->configuration_value = 'theContainer1_wrapper_0';
$Child[313]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[313]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[313]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[313]->Configuration['line_height']->configuration_value = '1';
$Child[313]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[313]->Configuration['text_align']->configuration_value = '';
$Child[313]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[313]->Configuration['width']->configuration_value = '100';
$Child[313]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":1,"width_unit":"px","color":"#f9f9f9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

$Child[314] = $Child[313]->Children->getTable()->create();
$Child[313]->Children->add($Child[314]);
$Child[314]->sort_order = '1';
$Child[314]->Styles['text-align']->definition_value = 'left';
$Child[314]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#acacad","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[314]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[314]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[314]->Styles['width']->definition_value = '950px';
$Child[314]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[314]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[314]->Styles['line-height']->definition_value = '1em';
$Child[314]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#acacad","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[314]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[314]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[314]->Configuration['shadows']->configuration_value = '[]';
$Child[314]->Configuration['width']->configuration_value = '950';
$Child[314]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[314]->Configuration['id']->configuration_value = 'theContainer1';
$Child[314]->Configuration['text_align']->configuration_value = 'left';
$Child[314]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[314]->Configuration['line_height']->configuration_value = '1';
$Child[314]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[314]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[314]->Configuration['width_unit']->configuration_value = 'px';

$Column[317] = $Child[314]->Columns->getTable()->create();
$Child[314]->Columns->add($Column[317]);
$Column[317]->sort_order = '1';
$Column[317]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#f9f9f9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[317]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[317]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[317]->Styles['line-height']->definition_value = '1em';
$Column[317]->Styles['width']->definition_value = '950px';
$Column[317]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Column[317]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[317]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Column[317]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[317]->Configuration['id']->configuration_value = 'theContainer9';
$Column[317]->Configuration['line_height']->configuration_value = '1';
$Column[317]->Configuration['width_unit']->configuration_value = 'px';
$Column[317]->Configuration['shadows']->configuration_value = '[]';
$Column[317]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[317]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[317]->Configuration['width']->configuration_value = '950';
$Column[317]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[317]->Configuration['text_align']->configuration_value = '';
$Column[317]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#f9f9f9","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[317]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[484] = $Column[317]->Widgets->getTable()->create();
$Column[317]->Widgets->add($Widget[484]);
$Widget[484]->identifier = 'navigationMenu';
$Widget[484]->sort_order = '1';
$Widget[484]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"All Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Featured Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Rental FAQ"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"gv_faq","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Blog"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_category","page":"none","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"mainNavigationMenu","forceFit":"true"}';

$Container[318] = $Layout[78]->Containers->getTable()->create();
$Layout[78]->Containers->add($Container[318]);
$Container[318]->sort_order = '3';
$Container[318]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[318]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[318]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[318]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[318]->Styles['width']->definition_value = '950px';
$Container[318]->Styles['line-height']->definition_value = '1em';
$Container[318]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[318]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Container[318]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[318]->Configuration['id']->configuration_value = 'theContainer7';
$Container[318]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[318]->Configuration['text_align']->configuration_value = '';
$Container[318]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[318]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[318]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{}}}}';
$Container[318]->Configuration['line_height']->configuration_value = '1';
$Container[318]->Configuration['width']->configuration_value = '950';
$Container[318]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[318]->Configuration['shadows']->configuration_value = '[]';
$Container[318]->Configuration['width_unit']->configuration_value = 'px';
$Container[318]->Configuration['line_height_unit']->configuration_value = 'em';

$Column[322] = $Container[318]->Columns->getTable()->create();
$Container[318]->Columns->add($Column[322]);
$Column[322]->sort_order = '1';
$Column[322]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[322]->Styles['line-height']->definition_value = '1em';
$Column[322]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[322]->Styles['width']->definition_value = '220px';
$Column[322]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":5,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[322]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[322]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[322]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[322]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[322]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":5,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[322]->Configuration['shadows']->configuration_value = '[]';
$Column[322]->Configuration['line_height']->configuration_value = '1';
$Column[322]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[322]->Configuration['text_align']->configuration_value = '';
$Column[322]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[322]->Configuration['width']->configuration_value = '220';
$Column[322]->Configuration['width_unit']->configuration_value = 'px';
$Column[322]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[322]->Configuration['id']->configuration_value = 'theContainer13';
$Column[322]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';

if (!isset($Box['categories'])){
 $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
    if (!is_object($Box['categories']) || $Box['categories']->count() <= 0){
       installInfobox('includes/modules/infoboxes/categories/', 'categories', 'null');
       $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
   }
}

$Widget[489] = $Column[322]->Widgets->getTable()->create();
$Column[322]->Widgets->add($Widget[489]);
$Widget[489]->identifier = 'categories';
$Widget[489]->sort_order = '1';
$Widget[489]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"box.tpl","widget_title":{"1":"Categories","48":"","49":"","50":""}}';

$Column[323] = $Container[318]->Columns->getTable()->create();
$Container[318]->Columns->add($Column[323]);
$Column[323]->sort_order = '2';
$Column[323]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[323]->Styles['line-height']->definition_value = '1em';
$Column[323]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[323]->Styles['width']->definition_value = '720px';
$Column[323]->Styles['text-align']->definition_value = 'left';
$Column[323]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":5,"left_unit":"px"}';
$Column[323]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[323]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[323]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[323]->Configuration['width']->configuration_value = '720';
$Column[323]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":5,"left_unit":"px"}';
$Column[323]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[323]->Configuration['text_align']->configuration_value = 'left';
$Column[323]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[323]->Configuration['shadows']->configuration_value = '[]';
$Column[323]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[323]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[323]->Configuration['line_height']->configuration_value = '1';
$Column[323]->Configuration['width_unit']->configuration_value = 'px';
$Column[323]->Configuration['id']->configuration_value = 'theContainer14';
$Column[323]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

if (!isset($Box['pageStack'])){
 $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
    if (!is_object($Box['pageStack']) || $Box['pageStack']->count() <= 0){
       installInfobox('includes/modules/infoboxes/pageStack/', 'pageStack', 'null');
       $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
   }
}

$Widget[494] = $Column[323]->Widgets->getTable()->create();
$Column[323]->Widgets->add($Widget[494]);
$Widget[494]->identifier = 'pageStack';
$Widget[494]->sort_order = '1';
$Widget[494]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

if (!isset($Box['pageContent'])){
 $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
    if (!is_object($Box['pageContent']) || $Box['pageContent']->count() <= 0){
       installInfobox('extensions/templateManager/catalog/infoboxes/pageContent/', 'pageContent', 'templateManager');
       $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
   }
}

$Widget[495] = $Column[323]->Widgets->getTable()->create();
$Column[323]->Widgets->add($Widget[495]);
$Widget[495]->identifier = 'pageContent';
$Widget[495]->sort_order = '2';
$Widget[495]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Container[319] = $Layout[78]->Containers->getTable()->create();
$Layout[78]->Containers->add($Container[319]);
$Container[319]->sort_order = '4';
$Container[319]->Styles['background_solid']->definition_value = '{"background_r":"21","background_g":"33","background_b":"57","background_a":"100"}';
$Container[319]->Styles['line-height']->definition_value = '1em';
$Container[319]->Styles['border']->definition_value = '{"top":{"width":1,"width_unit":"px","color":"#adadad","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[319]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[319]->Styles['margin']->definition_value = '{"top":1,"top_unit":"em","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[319]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[319]->Styles['width']->definition_value = '100%';
$Container[319]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[319]->Configuration['shadows']->configuration_value = '[]';
$Container[319]->Configuration['margin']->configuration_value = '{"top":1,"top_unit":"em","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[319]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[319]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[319]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[319]->Configuration['id']->configuration_value = 'theContainer8_wrapper_1';
$Container[319]->Configuration['width_unit']->configuration_value = '%';
$Container[319]->Configuration['width']->configuration_value = '100';
$Container[319]->Configuration['line_height']->configuration_value = '1';
$Container[319]->Configuration['border']->configuration_value = '{"top":{"width":1,"width_unit":"px","color":"#adadad","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[319]->Configuration['text_align']->configuration_value = '';
$Container[319]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[319]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Container[319]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"21","background_g":"33","background_b":"57","background_a":"100"}}}}';

$Child[320] = $Container[319]->Children->getTable()->create();
$Container[319]->Children->add($Child[320]);
$Child[320]->sort_order = '1';
$Child[320]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[320]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[320]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[320]->Styles['line-height']->definition_value = '1em';
$Child[320]->Styles['width']->definition_value = '100%';
$Child[320]->Styles['border']->definition_value = '{"top":{"width":1,"width_unit":"px","color":"#fbfbfb","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[320]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[320]->Configuration['shadows']->configuration_value = '[]';
$Child[320]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[320]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[320]->Configuration['width_unit']->configuration_value = '%';
$Child[320]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[320]->Configuration['line_height']->configuration_value = '1';
$Child[320]->Configuration['id']->configuration_value = 'theContainer8_wrapper_0';
$Child[320]->Configuration['text_align']->configuration_value = '';
$Child[320]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[320]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[320]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[320]->Configuration['width']->configuration_value = '100';
$Child[320]->Configuration['border']->configuration_value = '{"top":{"width":1,"width_unit":"px","color":"#fbfbfb","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

$Child[321] = $Child[320]->Children->getTable()->create();
$Child[320]->Children->add($Child[321]);
$Child[321]->sort_order = '1';
$Child[321]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[321]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[321]->Styles['text-align']->definition_value = 'center';
$Child[321]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[321]->Styles['width']->definition_value = '950px';
$Child[321]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[321]->Styles['line-height']->definition_value = '1em';
$Child[321]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#fbfbfb","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[321]->Configuration['line_height']->configuration_value = '1';
$Child[321]->Configuration['width']->configuration_value = '950';
$Child[321]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[321]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[321]->Configuration['width_unit']->configuration_value = 'px';
$Child[321]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[321]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#fbfbfb","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[321]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[321]->Configuration['id']->configuration_value = 'theContainer8';
$Child[321]->Configuration['text_align']->configuration_value = 'center';
$Child[321]->Configuration['shadows']->configuration_value = '[]';
$Child[321]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[321]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

$Column[324] = $Child[321]->Columns->getTable()->create();
$Child[321]->Columns->add($Column[324]);
$Column[324]->sort_order = '1';
$Column[324]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[324]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[324]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[324]->Styles['line-height']->definition_value = '1em';
$Column[324]->Styles['text']->definition_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[324]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[324]->Styles['text-align']->definition_value = 'center';
$Column[324]->Styles['width']->definition_value = '950px';
$Column[324]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[324]->Configuration['id']->configuration_value = 'theContainer15';
$Column[324]->Configuration['width']->configuration_value = '950';
$Column[324]->Configuration['width_unit']->configuration_value = 'px';
$Column[324]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[324]->Configuration['shadows']->configuration_value = '[]';
$Column[324]->Configuration['line_height']->configuration_value = '1';
$Column[324]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[324]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[324]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[324]->Configuration['text_align']->configuration_value = 'center';
$Column[324]->Configuration['text']->configuration_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[324]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[324]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[324]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[492] = $Column[324]->Widgets->getTable()->create();
$Column[324]->Widgets->add($Widget[492]);
$Widget[492]->identifier = 'navigationMenu';
$Widget[492]->sort_order = '1';
$Widget[492]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"linked_to":"345","menuId":"footerNavigationMenu","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[493] = $Column[324]->Widgets->getTable()->create();
$Column[324]->Widgets->add($Widget[493]);
$Widget[493]->identifier = 'customPHP';
$Widget[493]->sort_order = '2';
$Widget[493]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"php_text":" <br style=\"clear: both;\"><p><' . '?php echo sprintf(sysLanguage::get(\'FOOTER_TEXT_BODY\'), date(\'Y\'), STORE_NAME);?' . '><br />\n   <a href=\"http://www.rental-e-commerce-software.com\" style=\"font-size:7px;color:#6F0909;\">\n    Rental Management E-commerce Software Solution\n   </a></p>\n"}';
$Template->save();
$WidgetProperties = json_decode($Widget[343]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('electronics', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[343]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[343]->save();
$WidgetProperties = json_decode($Widget[344]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('electronics', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[344]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[344]->save();
$WidgetProperties = json_decode($Widget[345]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('electronics', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[345]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[345]->save();
$WidgetProperties = json_decode($Widget[346]->Configuration['widget_settings']->configuration_value);
$BannerManagerBanners = Doctrine_Core::getTable('BannerManagerBanners');
$BannerManagerGroups = Doctrine_Core::getTable('BannerManagerGroups');
$BannerManagerBannersToGroups = Doctrine_Core::getTable('BannerManagerBannersToGroups');

$Banner = $BannerManagerBanners->findOneByBannersName('bn1');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'bn1';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'banner13.jpg';
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

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannerg1');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannerg1';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '928';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '354';
		$BannerGroup->banner_group_thumbs_width = '50';
		$BannerGroup->banner_group_thumbs_height = '50';
		$BannerGroup->banner_group_show_description = '0';
		$BannerGroup->banner_group_auto_rotate = '0';
		$BannerGroup->banner_group_show_custom = '1';
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


$Banner = $BannerManagerBanners->findOneByBannersName('elec2');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'elec2';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'elec2.jpg';
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

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannerg1');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannerg1';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '928';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '354';
		$BannerGroup->banner_group_thumbs_width = '50';
		$BannerGroup->banner_group_thumbs_height = '50';
		$BannerGroup->banner_group_show_description = '0';
		$BannerGroup->banner_group_auto_rotate = '0';
		$BannerGroup->banner_group_show_custom = '1';
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


$Banner = $BannerManagerBanners->findOneByBannersName('elec3');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'elec3';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'elec3.jpg';
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

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannerg1');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannerg1';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '928';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '354';
		$BannerGroup->banner_group_thumbs_width = '50';
		$BannerGroup->banner_group_thumbs_height = '50';
		$BannerGroup->banner_group_show_description = '0';
		$BannerGroup->banner_group_auto_rotate = '0';
		$BannerGroup->banner_group_show_custom = '1';
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

$Widget[346]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[346]->save();
$WidgetProperties = json_decode($Widget[347]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('electronics', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[347]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[347]->save();
$WidgetProperties = json_decode($Widget[348]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('electronics', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[348]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[348]->save();
$WidgetProperties = json_decode($Widget[349]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('electronics', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[349]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[349]->save();
$WidgetProperties = json_decode($Widget[351]->Configuration['widget_settings']->configuration_value);
$Pages = Doctrine_Core::getTable('Pages');
$PagesDescription = Doctrine_Core::getTable('PagesDescription');

$Page = $Pages->findOneByPageKey('welcome_page');
if (!$Page){
$Page = $Pages->create();
$Page->sort_order = '0';
$Page->status = '1';
$Page->infobox_status = '0';
$Page->page_type = 'block';
$Page->page_key = 'welcome_page';

$PageDescription = $PagesDescription->create();
	$PageDescription->pages_title = 'Welcome Page';
		$PageDescription->pages_html_text = '<h2>
' . "\n" . '	<span style="color: rgb(0, 0, 0);"><strong>Welcome To The Demo Car Rental Store</strong></span></h2>
' . "\n" . '<p>
' . "\n" . '	Renting cars is lots of fun. Rent cars from us: sports cars, sedans, luxury cars, motorcycles, and minivans.&nbsp;Renting cars is lots of fun. Rent cars from us: sports cars, sedans, luxury cars, motorcycles, and minivans.&nbsp;Renting cars is lots of fun. Rent cars from us: sports cars, sedans, luxury cars, motorcycles, and minivans.&nbsp;Renting cars is lots of fun. Rent cars from us: sports cars, sedans, luxury cars, motorcycles, and minivans.&nbsp;Renting cars is lots of fun. Rent cars from us: sports cars, sedans, luxury cars, motorcycles, and minivans.&nbsp;Renting cars is lots of fun. Rent cars from us: sports cars, sedans, luxury cars, motorcycles, and minivans.&nbsp;Renting cars is lots of fun. Rent cars from us: sports cars, sedans, luxury cars, motorcycles, and minivans.&nbsp;Renting cars is lots of fun. Rent cars from us: sports cars, sedans, luxury cars, motorcycles, and minivans.&nbsp;Renting cars is lots of fun. Rent cars from us: sports cars, sedans, luxury cars, motorcycles, and minivans.&nbsp;Renting cars is lots of fun. Rent cars from us: sports cars, sedans, luxury cars, motorcycles, and minivans.&nbsp;Renting cars is lots of fun. Rent cars from us: sports cars, sedans, luxury cars, motorcycles, and minivans.&nbsp;</p>
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
$Widget[351]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[351]->save();
$WidgetProperties = json_decode($Widget[477]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('electronics', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[477]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[477]->save();
$WidgetProperties = json_decode($Widget[482]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('electronics', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[482]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[482]->save();
$WidgetProperties = json_decode($Widget[483]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('electronics', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[483]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[483]->save();
$WidgetProperties = json_decode($Widget[484]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('electronics', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[484]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[484]->save();
$WidgetProperties = json_decode($Widget[492]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('electronics', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[492]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[492]->save();
addLayoutToPage('index', 'default.php', null, $Layout[68]->layout_id);
addLayoutToPage('account', 'address_book.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'create.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'create_success.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'credit_card_details.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'cron_auto_send_return.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'cron_membership_update.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'edit.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'history.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'history_info.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'login.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'logoff.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'membership.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'membership_cancel.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'membership_info.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'membership_upgrade.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'newsletters.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'password.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'password_forgotten.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'rental_issues.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'rented_products.php', null, $Layout[78]->layout_id);
addLayoutToPage('billing_address_process', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('center_address_check', 'center_address_check.php', null, $Layout[78]->layout_id);
addLayoutToPage('checkout', 'addresses.php', null, $Layout[78]->layout_id);
addLayoutToPage('checkout', 'cart.php', null, $Layout[78]->layout_id);
addLayoutToPage('checkout', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('checkout', 'shipping_payment.php', null, $Layout[78]->layout_id);
addLayoutToPage('checkout', 'success.php', null, $Layout[78]->layout_id);
addLayoutToPage('checkout_old', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('checkout_old', 'rental_process.php', null, $Layout[78]->layout_id);
addLayoutToPage('checkout_old', 'success.php', null, $Layout[78]->layout_id);
addLayoutToPage('contact_us', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('contact_us', 'success.php', null, $Layout[78]->layout_id);
addLayoutToPage('funways', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('gv_redeem', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('index', 'nested.php', null, $Layout[78]->layout_id);
addLayoutToPage('index', 'products.php', null, $Layout[78]->layout_id);
addLayoutToPage('index', 'index.php', null, $Layout[78]->layout_id);
addLayoutToPage('product', 'info.php', null, $Layout[78]->layout_id);
addLayoutToPage('product', 'reviews.php', null, $Layout[78]->layout_id);
addLayoutToPage('products', 'all.php', null, $Layout[78]->layout_id);
addLayoutToPage('products', 'best_sellers.php', null, $Layout[78]->layout_id);
addLayoutToPage('products', 'featured.php', null, $Layout[78]->layout_id);
addLayoutToPage('products', 'new.php', null, $Layout[78]->layout_id);
addLayoutToPage('products', 'search.php', null, $Layout[78]->layout_id);
addLayoutToPage('products', 'search_result.php', null, $Layout[78]->layout_id);
addLayoutToPage('products', 'upcoming.php', null, $Layout[78]->layout_id);
addLayoutToPage('recent_additions', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('redirect', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('rentals', 'queue.php', null, $Layout[78]->layout_id);
addLayoutToPage('rentals', 'top.php', null, $Layout[78]->layout_id);
addLayoutToPage('shoppingCart', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('shopping_cart', 'shopping_cart.php', null, $Layout[78]->layout_id);
addLayoutToPage('tell_a_friend', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('viewStream', 'default.php', null, $Layout[78]->layout_id);
addLayoutToPage('viewStream', 'pull.php', null, $Layout[78]->layout_id);
addLayoutToPage('show', 'default.php', 'articleManager', $Layout[78]->layout_id);
addLayoutToPage('show', 'info.php', 'articleManager', $Layout[78]->layout_id);
addLayoutToPage('show', 'new.php', 'articleManager', $Layout[78]->layout_id);
addLayoutToPage('show', 'rss.php', 'articleManager', $Layout[78]->layout_id);
addLayoutToPage('banner_actions', 'default.php', 'bannerManager', $Layout[78]->layout_id);
addLayoutToPage('show_archive', 'default.php', 'blog', $Layout[78]->layout_id);
addLayoutToPage('show_post', 'default.php', 'blog', $Layout[78]->layout_id);
addLayoutToPage('show_page', 'default.php', 'categoriesPages', $Layout[78]->layout_id);
addLayoutToPage('simpleDownload', 'default.php', 'customFields', $Layout[78]->layout_id);
addLayoutToPage('downloads', 'default.php', 'downloadProducts', $Layout[78]->layout_id);
addLayoutToPage('downloads', 'get.php', 'downloadProducts', $Layout[78]->layout_id);
addLayoutToPage('downloads', 'listing.php', 'downloadProducts', $Layout[78]->layout_id);
addLayoutToPage('main', 'default.php', 'downloadProducts', $Layout[78]->layout_id);
addLayoutToPage('show_page', 'default.php', 'infoPages', $Layout[78]->layout_id);
addLayoutToPage('center_address_check', 'default.php', 'inventoryCenters', $Layout[78]->layout_id);
addLayoutToPage('show_inventory', 'default.php', 'inventoryCenters', $Layout[78]->layout_id);
addLayoutToPage('show_inventory', 'delivery.php', 'inventoryCenters', $Layout[78]->layout_id);
addLayoutToPage('show_inventory', 'list.php', 'inventoryCenters', $Layout[78]->layout_id);
addLayoutToPage('account_addon', 'history_inventory_info.php', 'inventoryCenters', $Layout[78]->layout_id);
addLayoutToPage('account_addon', 'view_orders_inventory.php', 'inventoryCenters', $Layout[78]->layout_id);
addLayoutToPage('show_shipping', 'default.php', 'payPerRentals', $Layout[78]->layout_id);
addLayoutToPage('build_reservation', 'default.php', 'payPerRentals', $Layout[78]->layout_id);
addLayoutToPage('address_check', 'default.php', 'payPerRentals', $Layout[78]->layout_id);
addLayoutToPage('show_event', 'default.php', 'payPerRentals', $Layout[78]->layout_id);
addLayoutToPage('show_event', 'list.php', 'payPerRentals', $Layout[78]->layout_id);
addLayoutToPage('design', 'default.php', 'productDesigner', $Layout[78]->layout_id);
addLayoutToPage('clipart', 'default.php', 'productDesigner', $Layout[78]->layout_id);
addLayoutToPage('product_review', 'default.php', 'reviews', $Layout[78]->layout_id);
addLayoutToPage('product_review', 'details.php', 'reviews', $Layout[78]->layout_id);
addLayoutToPage('product_review', 'write.php', 'reviews', $Layout[78]->layout_id);
addLayoutToPage('account_addon', 'view_royalties.php', 'royaltiesSystem', $Layout[78]->layout_id);
addLayoutToPage('show_specials', 'default.php', 'specials', $Layout[78]->layout_id);
addLayoutToPage('streams', 'default.php', 'streamProducts', $Layout[78]->layout_id);
addLayoutToPage('streams', 'listing.php', 'streamProducts', $Layout[78]->layout_id);
addLayoutToPage('streams', 'view.php', 'streamProducts', $Layout[78]->layout_id);
addLayoutToPage('main', 'default.php', 'streamProducts', $Layout[78]->layout_id);
addLayoutToPage('notify', 'cron.php', 'waitingList', $Layout[78]->layout_id);
addLayoutToPage('notify', 'default.php', 'waitingList', $Layout[78]->layout_id);
addLayoutToPage('account', 'address_book_details.php', null, $Layout[78]->layout_id);
addLayoutToPage('account', 'address_book_process.php', null, $Layout[78]->layout_id);
addLayoutToPage('show_category', 'default.php', 'blog', $Layout[78]->layout_id);
addLayoutToPage('show_category', 'rss.php', 'blog', $Layout[78]->layout_id);
