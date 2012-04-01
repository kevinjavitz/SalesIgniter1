<?php
$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->create();
$Template->Configuration['NAME']->configuration_value = 'Equipment';
$Template->Configuration['DIRECTORY']->configuration_value = 'equipment';

$Layout[73] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[73]);
$Layout[73]->layout_name = 'All Pages';

$Container[263] = $Layout[73]->Containers->getTable()->create();
$Layout[73]->Containers->add($Container[263]);
$Container[263]->sort_order = '1';
$Container[263]->Styles['width']->definition_value = '100%';
$Container[263]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[263]->Styles['line-height']->definition_value = '1em';
$Container[263]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[263]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[263]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[263]->Styles['border']->definition_value = '{"top":{"width":3,"width_unit":"px","color":"#eb5f01","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[263]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[263]->Configuration['id']->configuration_value = 'theContainer0_wrapper_1';
$Container[263]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[263]->Configuration['text_align']->configuration_value = '';
$Container[263]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[263]->Configuration['line_height']->configuration_value = '1';
$Container[263]->Configuration['width_unit']->configuration_value = '%';
$Container[263]->Configuration['width']->configuration_value = '100';
$Container[263]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[263]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[263]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[263]->Configuration['shadows']->configuration_value = '[]';
$Container[263]->Configuration['border']->configuration_value = '{"top":{"width":3,"width_unit":"px","color":"#eb5f01","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

$Child[264] = $Container[263]->Children->getTable()->create();
$Container[263]->Children->add($Child[264]);
$Child[264]->sort_order = '1';
$Child[264]->Styles['width']->definition_value = '100%';
$Child[264]->Styles['line-height']->definition_value = '1em';
$Child[264]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[264]->Styles['border']->definition_value = '{"top":{"width":1,"width_unit":"px","color":"#ffffff","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[264]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[264]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[264]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"210","g":"210","b":"210","a":1},"position":"0"},{"color":{"r":"255","g":"255","b":"255","a":1},"position":0.5},{"color":{"r":"226","g":"226","b":"226","a":1},"position":"1"}]}';
$Child[264]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[264]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[264]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[264]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[264]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"210","start_color_g":"210","start_color_b":"210","start_color_a":"100","end_color_r":"226","end_color_g":"226","end_color_b":"226","end_color_a":"100"},"colorStops":[{"color_stop_pos":"50","color_stop_color_r":"255","color_stop_color_g":"255","color_stop_color_b":"255","color_stop_color_a":"100"}],"imagesBefore":[],"imagesAfter":[]}}}';
$Child[264]->Configuration['shadows']->configuration_value = '[]';
$Child[264]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[264]->Configuration['text_align']->configuration_value = '';
$Child[264]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[264]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Child[264]->Configuration['border']->configuration_value = '{"top":{"width":1,"width_unit":"px","color":"#ffffff","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[264]->Configuration['width_unit']->configuration_value = '%';
$Child[264]->Configuration['width']->configuration_value = '100';
$Child[264]->Configuration['id']->configuration_value = 'theContainer0_wrapper_0';
$Child[264]->Configuration['line_height']->configuration_value = '1';

$Child[265] = $Child[264]->Children->getTable()->create();
$Child[264]->Children->add($Child[265]);
$Child[265]->sort_order = '1';
$Child[265]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[265]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[265]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[265]->Styles['width']->definition_value = '960px';
$Child[265]->Styles['line-height']->definition_value = '1em';
$Child[265]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[265]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[265]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[265]->Configuration['text_align']->configuration_value = '';
$Child[265]->Configuration['width_unit']->configuration_value = 'px';
$Child[265]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[265]->Configuration['line_height']->configuration_value = '1';
$Child[265]->Configuration['width']->configuration_value = '960';
$Child[265]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[265]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[265]->Configuration['id']->configuration_value = 'theContainer0';
$Child[265]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[265]->Configuration['shadows']->configuration_value = '[]';
$Child[265]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[265]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';

$Column[265] = $Child[265]->Columns->getTable()->create();
$Child[265]->Columns->add($Column[265]);
$Column[265]->sort_order = '1';
$Column[265]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[265]->Styles['line-height']->definition_value = '1em';
$Column[265]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[265]->Styles['width']->definition_value = '320px';
$Column[265]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[265]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[265]->Styles['margin']->definition_value = '{"top":"25","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[265]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[265]->Configuration['margin']->configuration_value = '{"top":"25","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[265]->Configuration['id']->configuration_value = 'theContainer3';
$Column[265]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[265]->Configuration['text_align']->configuration_value = '';
$Column[265]->Configuration['shadows']->configuration_value = '[]';
$Column[265]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[265]->Configuration['width']->configuration_value = '320';
$Column[265]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[265]->Configuration['line_height']->configuration_value = '1';
$Column[265]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[265]->Configuration['width_unit']->configuration_value = 'px';
$Column[265]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[399] = $Column[265]->Widgets->getTable()->create();
$Column[265]->Widgets->add($Widget[399]);
$Widget[399]->identifier = 'customImage';
$Widget[399]->sort_order = '1';
$Widget[399]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/equipment/images/logo.png"}';

$Column[266] = $Child[265]->Columns->getTable()->create();
$Child[265]->Columns->add($Column[266]);
$Column[266]->sort_order = '2';
$Column[266]->Styles['line-height']->definition_value = '1em';
$Column[266]->Styles['text-align']->definition_value = 'right';
$Column[266]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[266]->Styles['width']->definition_value = '640px';
$Column[266]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[266]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[266]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[266]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[266]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[266]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[266]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[266]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[266]->Configuration['id']->configuration_value = 'theContainer5';
$Column[266]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[266]->Configuration['text_align']->configuration_value = 'right';
$Column[266]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[266]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[266]->Configuration['line_height']->configuration_value = '1';
$Column[266]->Configuration['width_unit']->configuration_value = 'px';
$Column[266]->Configuration['width']->configuration_value = '640';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[400] = $Column[266]->Widgets->getTable()->create();
$Column[266]->Widgets->add($Widget[400]);
$Widget[400]->identifier = 'navigationMenu';
$Widget[400]->sort_order = '1';
$Widget[400]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"My Account"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"custom","icon_src":"/templates/equipment/images/icon_woman.png","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"Shopping Cart"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"custom","icon_src":"/templates/equipment/images/icon_cart.png","link":{"type":"app","application":"shoppingCart","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"headerMiniNav","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[401] = $Column[266]->Widgets->getTable()->create();
$Column[266]->Widgets->add($Widget[401]);
$Widget[401]->identifier = 'customPHP';
$Widget[401]->sort_order = '2';
$Widget[401]->Configuration['widget_settings']->configuration_value = '{"php_text":"<form id=\"searchBox\" name=\"search\" action=\"<' . '?php echo itw_app_link(null,\'products\',\'search_result\');?' . '>\" method=\"get\">\n    <span class=\"searchText\">Product search: </span>\n    <' . '?php echo tep_draw_input_field(\'keywords\', \'\', \'class=\"finputs\"\') . tep_hide_session_id();?' . '>\n    <' . '?php echo htmlBase::newElement(\'button\')->setText(\'Go\')->setType(\'submit\')->draw();?' . '>\n</form>","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Container[266] = $Layout[73]->Containers->getTable()->create();
$Layout[73]->Containers->add($Container[266]);
$Container[266]->sort_order = '2';
$Container[266]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"226","g":"226","b":"226","a":1},"position":"0"},{"color":{"r":"216","g":"216","b":"216","a":1},"position":"1"}]}';
$Container[266]->Styles['width']->definition_value = '100%';
$Container[266]->Styles['line-height']->definition_value = '1em';
$Container[266]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[266]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[266]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[266]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[266]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[266]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[266]->Configuration['id']->configuration_value = 'theContainer1_wrapper_0';
$Container[266]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[266]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[266]->Configuration['text_align']->configuration_value = '';
$Container[266]->Configuration['width']->configuration_value = '100';
$Container[266]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[266]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[266]->Configuration['line_height']->configuration_value = '1';
$Container[266]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[266]->Configuration['shadows']->configuration_value = '[]';
$Container[266]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"226","start_color_g":"226","start_color_b":"226","start_color_a":"100","end_color_r":"216","end_color_g":"216","end_color_b":"216","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[266]->Configuration['width_unit']->configuration_value = '%';
$Container[266]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

$Child[267] = $Container[266]->Children->getTable()->create();
$Container[266]->Children->add($Child[267]);
$Child[267]->sort_order = '1';
$Child[267]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"18","g":"18","b":"18","a":1},"position":"0"},{"color":{"r":"100","g":"100","b":"100","a":1},"position":0.5},{"color":{"r":"45","g":"45","b":"45","a":1},"position":0.5},{"color":{"r":"10","g":"8","b":"9","a":1},"position":"1"}]}';
$Child[267]->Styles['border_radius']->definition_value = '{"border_top_left_radius":16,"border_top_left_radius_unit":"px","border_top_right_radius":16,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[267]->Styles['line-height']->definition_value = '1em';
$Child[267]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[267]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[267]->Styles['width']->definition_value = '960px';
$Child[267]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[267]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[267]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[267]->Configuration['line_height']->configuration_value = '1';
$Child[267]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[267]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[267]->Configuration['shadows']->configuration_value = '[]';
$Child[267]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Child[267]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":16,"border_top_left_radius_unit":"px","border_top_right_radius":16,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[267]->Configuration['width']->configuration_value = '960';
$Child[267]->Configuration['width_unit']->configuration_value = 'px';
$Child[267]->Configuration['id']->configuration_value = 'theContainer1';
$Child[267]->Configuration['text_align']->configuration_value = '';
$Child[267]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"18","start_color_g":"18","start_color_b":"18","start_color_a":"100","end_color_r":"10","end_color_g":"8","end_color_b":"9","end_color_a":"100"},"colorStops":[{"color_stop_pos":"50","color_stop_color_r":"100","color_stop_color_g":"100","color_stop_color_b":"100","color_stop_color_a":"100"},{"color_stop_pos":"50","color_stop_color_r":"45","color_stop_color_g":"45","color_stop_color_b":"45","color_stop_color_a":"100"}],"imagesBefore":[],"imagesAfter":[]}}}';
$Child[267]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[267]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

$Column[267] = $Child[267]->Columns->getTable()->create();
$Child[267]->Columns->add($Column[267]);
$Column[267]->sort_order = '1';
$Column[267]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[267]->Styles['width']->definition_value = '960px';
$Column[267]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[267]->Styles['line-height']->definition_value = '1em';
$Column[267]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[267]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[267]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[267]->Configuration['id']->configuration_value = 'theContainer4';
$Column[267]->Configuration['shadows']->configuration_value = '[]';
$Column[267]->Configuration['width_unit']->configuration_value = 'px';
$Column[267]->Configuration['line_height']->configuration_value = '1';
$Column[267]->Configuration['width']->configuration_value = '960';
$Column[267]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[267]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[267]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[267]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[267]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[267]->Configuration['text_align']->configuration_value = '';
$Column[267]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[267]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[402] = $Column[267]->Widgets->getTable()->create();
$Column[267]->Widgets->add($Widget[402]);
$Widget[402]->identifier = 'navigationMenu';
$Widget[402]->sort_order = '1';
$Widget[402]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"All Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Featured Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Rental FAQ"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"gv_faq","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Blog"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_category","page":"none","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"mainNavigationMenu","forceFit":"true"}';

$Container[268] = $Layout[73]->Containers->getTable()->create();
$Layout[73]->Containers->add($Container[268]);
$Container[268]->sort_order = '3';
$Container[268]->Styles['border_radius']->definition_value = '{"border_top_left_radius":12,"border_top_left_radius_unit":"px","border_top_right_radius":12,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[268]->Styles['line-height']->definition_value = '1em';
$Container[268]->Styles['width']->definition_value = '960px';
$Container[268]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[268]->Styles['background_solid']->definition_value = '{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}';
$Container[268]->Styles['margin']->definition_value = '{"top":"12","top_unit":"px","right":"0","right_unit":"auto","bottom":"10","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[268]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[268]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[268]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Container[268]->Configuration['margin']->configuration_value = '{"top":"12","top_unit":"px","right":"0","right_unit":"auto","bottom":"10","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[268]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[268]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":12,"border_top_left_radius_unit":"px","border_top_right_radius":12,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[268]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[268]->Configuration['id']->configuration_value = 'theContainer2';
$Container[268]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}}}}';
$Container[268]->Configuration['width']->configuration_value = '960';
$Container[268]->Configuration['shadows']->configuration_value = '[]';
$Container[268]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[268]->Configuration['width_unit']->configuration_value = 'px';
$Container[268]->Configuration['line_height']->configuration_value = '1';
$Container[268]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[268]->Configuration['text_align']->configuration_value = '';

$Column[268] = $Container[268]->Columns->getTable()->create();
$Container[268]->Columns->add($Column[268]);
$Column[268]->sort_order = '1';
$Column[268]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[268]->Styles['line-height']->definition_value = '1em';
$Column[268]->Styles['width']->definition_value = '200px';
$Column[268]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[268]->Styles['text-align']->definition_value = 'left';
$Column[268]->Styles['margin']->definition_value = '{"top":12,"top_unit":"px","right":6,"right_unit":"px","bottom":12,"bottom_unit":"px","left":12,"left_unit":"px"}';
$Column[268]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[268]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[268]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[268]->Configuration['text_align']->configuration_value = 'left';
$Column[268]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[268]->Configuration['line_height']->configuration_value = '1';
$Column[268]->Configuration['width_unit']->configuration_value = 'px';
$Column[268]->Configuration['width']->configuration_value = '200';
$Column[268]->Configuration['id']->configuration_value = 'theContainer7';
$Column[268]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[268]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[268]->Configuration['shadows']->configuration_value = '[]';
$Column[268]->Configuration['margin']->configuration_value = '{"top":12,"top_unit":"px","right":6,"right_unit":"px","bottom":12,"bottom_unit":"px","left":12,"left_unit":"px"}';
$Column[268]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[268]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['categories'])){
 $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
    if (!is_object($Box['categories']) || $Box['categories']->count() <= 0){
       installInfobox('includes/modules/infoboxes/categories/', 'categories', 'null');
       $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
   }
}

$Widget[403] = $Column[268]->Widgets->getTable()->create();
$Column[268]->Widgets->add($Widget[403]);
$Widget[403]->identifier = 'categories';
$Widget[403]->sort_order = '1';
$Widget[403]->Configuration['widget_settings']->configuration_value = '{"id":"categoriesBoxMenu","template_file":"box.tpl","widget_title":{"1":"Categories","48":"","49":"","50":""}}';

if (!isset($Box['wysiwygBlock'])){
 $Box['wysiwygBlock'] = $TemplatesInfoboxes->findOneByBoxCode('wysiwygBlock');
    if (!is_object($Box['wysiwygBlock']) || $Box['wysiwygBlock']->count() <= 0){
       installInfobox('includes/modules/infoboxes/wysiwygBlock/', 'wysiwygBlock', 'null');
       $Box['wysiwygBlock'] = $TemplatesInfoboxes->findOneByBoxCode('wysiwygBlock');
   }
}

$Widget[404] = $Column[268]->Widgets->getTable()->create();
$Column[268]->Widgets->add($Widget[404]);
$Widget[404]->identifier = 'wysiwygBlock';
$Widget[404]->sort_order = '2';
$Widget[404]->Configuration['widget_settings']->configuration_value = '{"block_html":"<div style=\"padding:.5em;padding-bottom:100px;background: url(/templates/equipment/images/contact_us_woman.png) 100% 100% no-repeat;\">\n\t<div>\n\t\tHeavy Machinery & Tool Rental<br />\n\t\tAddress: xyz<br />\n\t\tPhone: 123-456-7890<br />\n\t\tEmail: xyz@Heavy Machinery & Tool Rental.com<br />\n\t\t </div>\n</div>\n","template_file":"box.tpl","widget_title":{"1":"Contact Us","48":"Contact Us","49":"Contact Us","50":"Contact Us"}}';

if (!isset($Box['languages'])){
 $Box['languages'] = $TemplatesInfoboxes->findOneByBoxCode('languages');
    if (!is_object($Box['languages']) || $Box['languages']->count() <= 0){
       installInfobox('includes/modules/infoboxes/languages/', 'languages', 'null');
       $Box['languages'] = $TemplatesInfoboxes->findOneByBoxCode('languages');
   }
}

$Widget[405] = $Column[268]->Widgets->getTable()->create();
$Column[268]->Widgets->add($Widget[405]);
$Widget[405]->identifier = 'languages';
$Widget[405]->sort_order = '3';
$Widget[405]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"box.tpl","widget_title":{"1":"Languages","48":"","49":"","50":""}}';

$Column[269] = $Container[268]->Columns->getTable()->create();
$Container[268]->Columns->add($Column[269]);
$Column[269]->sort_order = '2';
$Column[269]->Styles['text-align']->definition_value = 'left';
$Column[269]->Styles['line-height']->definition_value = '1em';
$Column[269]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[269]->Styles['width']->definition_value = '724px';
$Column[269]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[269]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[269]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[269]->Styles['margin']->definition_value = '{"top":12,"top_unit":"px","right":12,"right_unit":"px","bottom":12,"bottom_unit":"px","left":6,"left_unit":"px"}';
$Column[269]->Configuration['margin']->configuration_value = '{"top":12,"top_unit":"px","right":12,"right_unit":"px","bottom":12,"bottom_unit":"px","left":6,"left_unit":"px"}';
$Column[269]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[269]->Configuration['id']->configuration_value = 'theContainer8';
$Column[269]->Configuration['width']->configuration_value = '724';
$Column[269]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[269]->Configuration['shadows']->configuration_value = '[]';
$Column[269]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[269]->Configuration['width_unit']->configuration_value = 'px';
$Column[269]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[269]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[269]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[269]->Configuration['line_height']->configuration_value = '1';
$Column[269]->Configuration['text_align']->configuration_value = 'left';

if (!isset($Box['pageStack'])){
 $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
    if (!is_object($Box['pageStack']) || $Box['pageStack']->count() <= 0){
       installInfobox('includes/modules/infoboxes/pageStack/', 'pageStack', 'null');
       $Box['pageStack'] = $TemplatesInfoboxes->findOneByBoxCode('pageStack');
   }
}

$Widget[406] = $Column[269]->Widgets->getTable()->create();
$Column[269]->Widgets->add($Widget[406]);
$Widget[406]->identifier = 'pageStack';
$Widget[406]->sort_order = '1';
$Widget[406]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

if (!isset($Box['pageContent'])){
 $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
    if (!is_object($Box['pageContent']) || $Box['pageContent']->count() <= 0){
       installInfobox('extensions/templateManager/catalog/infoboxes/pageContent/', 'pageContent', 'templateManager');
       $Box['pageContent'] = $TemplatesInfoboxes->findOneByBoxCode('pageContent');
   }
}

$Widget[407] = $Column[269]->Widgets->getTable()->create();
$Column[269]->Widgets->add($Widget[407]);
$Widget[407]->identifier = 'pageContent';
$Widget[407]->sort_order = '2';
$Widget[407]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Container[269] = $Layout[73]->Containers->getTable()->create();
$Layout[73]->Containers->add($Container[269]);
$Container[269]->sort_order = '4';
$Container[269]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[269]->Styles['font']->definition_value = '{"color":"#ffffff","family":"Arial","size":1,"size_unit":"em"}';
$Container[269]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[269]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[269]->Styles['line-height']->definition_value = '1em';
$Container[269]->Styles['width']->definition_value = '100%';
$Container[269]->Styles['text-align']->definition_value = 'center';
$Container[269]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[269]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"67","g":"66","b":"66","a":1},"position":"0"},{"color":{"r":"0","g":"0","b":"0","a":1},"position":"1"}]}';
$Container[269]->Configuration['id']->configuration_value = 'theContainer9';
$Container[269]->Configuration['width']->configuration_value = '100';
$Container[269]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[269]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[269]->Configuration['shadows']->configuration_value = '[]';
$Container[269]->Configuration['line_height']->configuration_value = '1';
$Container[269]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[269]->Configuration['text_align']->configuration_value = 'center';
$Container[269]->Configuration['font']->configuration_value = '{"color":"#ffffff","family":"Arial","size":1,"size_unit":"em"}';
$Container[269]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[269]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[269]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"67","start_color_g":"66","start_color_b":"66","start_color_a":"100","end_color_r":"0","end_color_g":"0","end_color_b":"0","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[269]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[269]->Configuration['width_unit']->configuration_value = '%';

$Column[270] = $Container[269]->Columns->getTable()->create();
$Container[269]->Columns->add($Column[270]);
$Column[270]->sort_order = '1';
$Column[270]->Styles['line-height']->definition_value = '1em';
$Column[270]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[270]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[270]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[270]->Styles['text-align']->definition_value = 'center';
$Column[270]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[270]->Styles['width']->definition_value = '100%';
$Column[270]->Styles['text']->definition_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[270]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[270]->Configuration['shadows']->configuration_value = '[]';
$Column[270]->Configuration['width_unit']->configuration_value = '%';
$Column[270]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[270]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[270]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[270]->Configuration['id']->configuration_value = 'theContainer10';
$Column[270]->Configuration['line_height']->configuration_value = '1';
$Column[270]->Configuration['text_align']->configuration_value = 'center';
$Column[270]->Configuration['width']->configuration_value = '100';
$Column[270]->Configuration['text']->configuration_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[270]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[270]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[270]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[270]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[408] = $Column[270]->Widgets->getTable()->create();
$Column[270]->Widgets->add($Widget[408]);
$Widget[408]->identifier = 'navigationMenu';
$Widget[408]->sort_order = '1';
$Widget[408]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"All Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Featured Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Rental FAQ"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"gv_faq","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Blog"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_category","page":"none","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"footerNavigationMenu","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[409] = $Column[270]->Widgets->getTable()->create();
$Column[270]->Widgets->add($Widget[409]);
$Widget[409]->identifier = 'customPHP';
$Widget[409]->sort_order = '2';
$Widget[409]->Configuration['widget_settings']->configuration_value = '{"php_text":" <br style=\"clear: both;\"><p><' . '?php echo sprintf(sysLanguage::get(\'FOOTER_TEXT_BODY\'), date(\'Y\'), STORE_NAME);?' . '><br />\n   <a href=\"http://www.rental-e-commerce-software.com\" style=\"font-size:7px;color:#6F0909;\">\n    Rental Management E-commerce Software Solution\n   </a></p>\n","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Layout[72] = $Template->Layouts->getTable()->create();
$Template->Layouts->add($Layout[72]);
$Layout[72]->layout_name = 'Home';

$Container[256] = $Layout[72]->Containers->getTable()->create();
$Layout[72]->Containers->add($Container[256]);
$Container[256]->sort_order = '1';
$Container[256]->Styles['width']->definition_value = '100%';
$Container[256]->Styles['line-height']->definition_value = '1em';
$Container[256]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[256]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[256]->Styles['border']->definition_value = '{"top":{"width":3,"width_unit":"px","color":"#eb5f01","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[256]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[256]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[256]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[256]->Configuration['text_align']->configuration_value = '';
$Container[256]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[256]->Configuration['line_height']->configuration_value = '1';
$Container[256]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[256]->Configuration['border']->configuration_value = '{"top":{"width":3,"width_unit":"px","color":"#eb5f01","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[256]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Container[256]->Configuration['id']->configuration_value = 'theContainer0_wrapper_1';
$Container[256]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[256]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[256]->Configuration['width']->configuration_value = '100';
$Container[256]->Configuration['width_unit']->configuration_value = '%';
$Container[256]->Configuration['shadows']->configuration_value = '[]';

$Child[257] = $Container[256]->Children->getTable()->create();
$Container[256]->Children->add($Child[257]);
$Child[257]->sort_order = '1';
$Child[257]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[257]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[257]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[257]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"210","g":"210","b":"210","a":1},"position":"0"},{"color":{"r":"255","g":"255","b":"255","a":1},"position":0.5},{"color":{"r":"226","g":"226","b":"226","a":1},"position":"1"}]}';
$Child[257]->Styles['line-height']->definition_value = '1em';
$Child[257]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[257]->Styles['width']->definition_value = '100%';
$Child[257]->Styles['border']->definition_value = '{"top":{"width":1,"width_unit":"px","color":"#ffffff","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[257]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[257]->Configuration['shadows']->configuration_value = '[]';
$Child[257]->Configuration['text_align']->configuration_value = '';
$Child[257]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[257]->Configuration['line_height']->configuration_value = '1';
$Child[257]->Configuration['id']->configuration_value = 'theContainer0_wrapper_0';
$Child[257]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Child[257]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[257]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[257]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[257]->Configuration['width']->configuration_value = '100';
$Child[257]->Configuration['border']->configuration_value = '{"top":{"width":1,"width_unit":"px","color":"#ffffff","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[257]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"210","start_color_g":"210","start_color_b":"210","start_color_a":"100","end_color_r":"226","end_color_g":"226","end_color_b":"226","end_color_a":"100"},"colorStops":[{"color_stop_pos":"50","color_stop_color_r":"255","color_stop_color_g":"255","color_stop_color_b":"255","color_stop_color_a":"100"}],"imagesBefore":[],"imagesAfter":[]}}}';
$Child[257]->Configuration['width_unit']->configuration_value = '%';

$Child[258] = $Child[257]->Children->getTable()->create();
$Child[257]->Children->add($Child[258]);
$Child[258]->sort_order = '1';
$Child[258]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[258]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[258]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[258]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[258]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[258]->Styles['width']->definition_value = '960px';
$Child[258]->Styles['line-height']->definition_value = '1em';
$Child[258]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[258]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Child[258]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[258]->Configuration['shadows']->configuration_value = '[]';
$Child[258]->Configuration['width_unit']->configuration_value = 'px';
$Child[258]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[258]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[258]->Configuration['line_height']->configuration_value = '1';
$Child[258]->Configuration['width']->configuration_value = '960';
$Child[258]->Configuration['text_align']->configuration_value = '';
$Child[258]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[258]->Configuration['id']->configuration_value = 'theContainer0';
$Child[258]->Configuration['line_height_unit']->configuration_value = 'em';

$Column[259] = $Child[258]->Columns->getTable()->create();
$Child[258]->Columns->add($Column[259]);
$Column[259]->sort_order = '1';
$Column[259]->Styles['width']->definition_value = '320px';
$Column[259]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[259]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[259]->Styles['margin']->definition_value = '{"top":"25","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[259]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[259]->Styles['line-height']->definition_value = '1em';
$Column[259]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[259]->Configuration['width']->configuration_value = '320';
$Column[259]->Configuration['margin']->configuration_value = '{"top":"25","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[259]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Column[259]->Configuration['width_unit']->configuration_value = 'px';
$Column[259]->Configuration['shadows']->configuration_value = '[]';
$Column[259]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[259]->Configuration['text_align']->configuration_value = '';
$Column[259]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[259]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[259]->Configuration['line_height']->configuration_value = '1';
$Column[259]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[259]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[259]->Configuration['id']->configuration_value = 'theContainer3';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $TemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[388] = $Column[259]->Widgets->getTable()->create();
$Column[259]->Widgets->add($Widget[388]);
$Widget[388]->identifier = 'customImage';
$Widget[388]->sort_order = '1';
$Widget[388]->Configuration['widget_settings']->configuration_value = '{"template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"image_source":"/templates/equipment/images/logo.png"}';

$Column[260] = $Child[258]->Columns->getTable()->create();
$Child[258]->Columns->add($Column[260]);
$Column[260]->sort_order = '2';
$Column[260]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[260]->Styles['width']->definition_value = '640px';
$Column[260]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[260]->Styles['text-align']->definition_value = 'right';
$Column[260]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[260]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[260]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[260]->Styles['line-height']->definition_value = '1em';
$Column[260]->Configuration['width_unit']->configuration_value = 'px';
$Column[260]->Configuration['shadows']->configuration_value = '[]';
$Column[260]->Configuration['text_align']->configuration_value = 'right';
$Column[260]->Configuration['id']->configuration_value = 'theContainer5';
$Column[260]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[260]->Configuration['line_height']->configuration_value = '1';
$Column[260]->Configuration['width']->configuration_value = '640';
$Column[260]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[260]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[260]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[260]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[260]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[260]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[389] = $Column[260]->Widgets->getTable()->create();
$Column[260]->Widgets->add($Widget[389]);
$Widget[389]->identifier = 'navigationMenu';
$Widget[389]->sort_order = '1';
$Widget[389]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"My Account"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"custom","icon_src":"/templates/equipment/images/icon_woman.png","link":{"type":"app","application":"account","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"Shopping Cart"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"custom","icon_src":"/templates/equipment/images/icon_cart.png","link":{"type":"app","application":"shoppingCart","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"headerMiniNav","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[390] = $Column[260]->Widgets->getTable()->create();
$Column[260]->Widgets->add($Widget[390]);
$Widget[390]->identifier = 'customPHP';
$Widget[390]->sort_order = '2';
$Widget[390]->Configuration['widget_settings']->configuration_value = '{"php_text":"<form id=\"searchBox\" name=\"search\" action=\"<' . '?php echo itw_app_link(null,\'products\',\'search_result\');?' . '>\" method=\"get\">\n    <span class=\"searchText\">Product search: </span>\n    <' . '?php echo tep_draw_input_field(\'keywords\', \'\', \'class=\"finputs\"\') . tep_hide_session_id();?' . '>\n    <' . '?php echo htmlBase::newElement(\'button\')->setText(\'Go\')->setType(\'submit\')->draw();?' . '>\n</form>","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

$Container[259] = $Layout[72]->Containers->getTable()->create();
$Layout[72]->Containers->add($Container[259]);
$Container[259]->sort_order = '2';
$Container[259]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"226","g":"226","b":"226","a":1},"position":"0"},{"color":{"r":"216","g":"216","b":"216","a":1},"position":"1"}]}';
$Container[259]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[259]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[259]->Styles['width']->definition_value = '100%';
$Container[259]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[259]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[259]->Styles['line-height']->definition_value = '1em';
$Container[259]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[259]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[259]->Configuration['shadows']->configuration_value = '[]';
$Container[259]->Configuration['line_height']->configuration_value = '1';
$Container[259]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[259]->Configuration['width_unit']->configuration_value = '%';
$Container[259]->Configuration['width']->configuration_value = '100';
$Container[259]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[259]->Configuration['text_align']->configuration_value = '';
$Container[259]->Configuration['id']->configuration_value = 'theContainer1_wrapper_0';
$Container[259]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[259]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[259]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"226","start_color_g":"226","start_color_b":"226","start_color_a":"100","end_color_r":"216","end_color_g":"216","end_color_b":"216","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[259]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[259]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';

$Child[260] = $Container[259]->Children->getTable()->create();
$Container[259]->Children->add($Child[260]);
$Child[260]->sort_order = '1';
$Child[260]->Styles['line-height']->definition_value = '1em';
$Child[260]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[260]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[260]->Styles['width']->definition_value = '960px';
$Child[260]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[260]->Styles['border_radius']->definition_value = '{"border_top_left_radius":16,"border_top_left_radius_unit":"px","border_top_right_radius":16,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[260]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[260]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"18","g":"18","b":"18","a":1},"position":"0"},{"color":{"r":"100","g":"100","b":"100","a":1},"position":0.5},{"color":{"r":"45","g":"45","b":"45","a":1},"position":0.5},{"color":{"r":"10","g":"8","b":"9","a":1},"position":"1"}]}';
$Child[260]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"auto","bottom":0,"bottom_unit":"px","left":0,"left_unit":"auto"}';
$Child[260]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Child[260]->Configuration['id']->configuration_value = 'theContainer1';
$Child[260]->Configuration['width_unit']->configuration_value = 'px';
$Child[260]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Child[260]->Configuration['line_height']->configuration_value = '1';
$Child[260]->Configuration['shadows']->configuration_value = '[]';
$Child[260]->Configuration['line_height_unit']->configuration_value = 'em';
$Child[260]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":16,"border_top_left_radius_unit":"px","border_top_right_radius":16,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Child[260]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Child[260]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"18","start_color_g":"18","start_color_b":"18","start_color_a":"100","end_color_r":"10","end_color_g":"8","end_color_b":"9","end_color_a":"100"},"colorStops":[{"color_stop_pos":"50","color_stop_color_r":"100","color_stop_color_g":"100","color_stop_color_b":"100","color_stop_color_a":"100"},{"color_stop_pos":"50","color_stop_color_r":"45","color_stop_color_g":"45","color_stop_color_b":"45","color_stop_color_a":"100"}],"imagesBefore":[],"imagesAfter":[]}}}';
$Child[260]->Configuration['width']->configuration_value = '960';
$Child[260]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Child[260]->Configuration['text_align']->configuration_value = '';

$Column[261] = $Child[260]->Columns->getTable()->create();
$Child[260]->Columns->add($Column[261]);
$Column[261]->sort_order = '1';
$Column[261]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[261]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[261]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[261]->Styles['width']->definition_value = '960px';
$Column[261]->Styles['line-height']->definition_value = '1em';
$Column[261]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[261]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[261]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[261]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[261]->Configuration['width_unit']->configuration_value = 'px';
$Column[261]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[261]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[261]->Configuration['line_height']->configuration_value = '1';
$Column[261]->Configuration['width']->configuration_value = '960';
$Column[261]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[261]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[261]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[261]->Configuration['shadows']->configuration_value = '[]';
$Column[261]->Configuration['id']->configuration_value = 'theContainer4';
$Column[261]->Configuration['text_align']->configuration_value = '';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[391] = $Column[261]->Widgets->getTable()->create();
$Column[261]->Widgets->add($Widget[391]);
$Widget[391]->identifier = 'navigationMenu';
$Widget[391]->sort_order = '1';
$Widget[391]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"All Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Featured Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Rental FAQ"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"gv_faq","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Blog"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_archive","page":"default","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"mainNavigationMenu","forceFit":"true"}';

$Container[261] = $Layout[72]->Containers->getTable()->create();
$Layout[72]->Containers->add($Container[261]);
$Container[261]->sort_order = '3';
$Container[261]->Styles['margin']->definition_value = '{"top":"12","top_unit":"px","right":"0","right_unit":"auto","bottom":"10","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[261]->Styles['border_radius']->definition_value = '{"border_top_left_radius":12,"border_top_left_radius_unit":"px","border_top_right_radius":12,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[261]->Styles['padding']->definition_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';
$Container[261]->Styles['line-height']->definition_value = '1em';
$Container[261]->Styles['width']->definition_value = '960px';
$Container[261]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[261]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[261]->Styles['background_solid']->definition_value = '{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}';
$Container[261]->Configuration['shadows']->configuration_value = '[]';
$Container[261]->Configuration['backgroundType']->configuration_value = '{"global":"solid"}';
$Container[261]->Configuration['line_height']->configuration_value = '1';
$Container[261]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[261]->Configuration['width_unit']->configuration_value = 'px';
$Container[261]->Configuration['width']->configuration_value = '960';
$Container[261]->Configuration['id']->configuration_value = 'theContainer2';
$Container[261]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[261]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Container[261]->Configuration['background']->configuration_value = '{"global":{"solid":{"config":{"background_r":"255","background_g":"255","background_b":"255","background_a":"100"}}}}';
$Container[261]->Configuration['text_align']->configuration_value = '';
$Container[261]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":12,"border_top_left_radius_unit":"px","border_top_right_radius":12,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[261]->Configuration['margin']->configuration_value = '{"top":"12","top_unit":"px","right":"0","right_unit":"auto","bottom":"10","bottom_unit":"px","left":"0","left_unit":"auto"}';
$Container[261]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"px","right":"0","right_unit":"px","bottom":"0","bottom_unit":"px","left":"0","left_unit":"px"}';

$Column[262] = $Container[261]->Columns->getTable()->create();
$Container[261]->Columns->add($Column[262]);
$Column[262]->sort_order = '1';
$Column[262]->Styles['line-height']->definition_value = '1em';
$Column[262]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[262]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[262]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[262]->Styles['text-align']->definition_value = 'left';
$Column[262]->Styles['margin']->definition_value = '{"top":12,"top_unit":"px","right":6,"right_unit":"px","bottom":12,"bottom_unit":"px","left":12,"left_unit":"px"}';
$Column[262]->Styles['width']->definition_value = '200px';
$Column[262]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[262]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[262]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[262]->Configuration['margin']->configuration_value = '{"top":12,"top_unit":"px","right":6,"right_unit":"px","bottom":12,"bottom_unit":"px","left":12,"left_unit":"px"}';
$Column[262]->Configuration['id']->configuration_value = 'theContainer7';
$Column[262]->Configuration['width_unit']->configuration_value = 'px';
$Column[262]->Configuration['shadows']->configuration_value = '[]';
$Column[262]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[262]->Configuration['line_height']->configuration_value = '1';
$Column[262]->Configuration['width']->configuration_value = '200';
$Column[262]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[262]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[262]->Configuration['text_align']->configuration_value = 'left';
$Column[262]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';

if (!isset($Box['categories'])){
 $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
    if (!is_object($Box['categories']) || $Box['categories']->count() <= 0){
       installInfobox('includes/modules/infoboxes/categories/', 'categories', 'null');
       $Box['categories'] = $TemplatesInfoboxes->findOneByBoxCode('categories');
   }
}

$Widget[392] = $Column[262]->Widgets->getTable()->create();
$Column[262]->Widgets->add($Widget[392]);
$Widget[392]->identifier = 'categories';
$Widget[392]->sort_order = '1';
$Widget[392]->Configuration['widget_settings']->configuration_value = '{"id":"categoriesBoxMenu","template_file":"box.tpl","widget_title":{"1":"Categories","48":"","49":"","50":""}}';

if (!isset($Box['wysiwygBlock'])){
 $Box['wysiwygBlock'] = $TemplatesInfoboxes->findOneByBoxCode('wysiwygBlock');
    if (!is_object($Box['wysiwygBlock']) || $Box['wysiwygBlock']->count() <= 0){
       installInfobox('includes/modules/infoboxes/wysiwygBlock/', 'wysiwygBlock', 'null');
       $Box['wysiwygBlock'] = $TemplatesInfoboxes->findOneByBoxCode('wysiwygBlock');
   }
}

$Widget[393] = $Column[262]->Widgets->getTable()->create();
$Column[262]->Widgets->add($Widget[393]);
$Widget[393]->identifier = 'wysiwygBlock';
$Widget[393]->sort_order = '2';
$Widget[393]->Configuration['widget_settings']->configuration_value = '{"block_html":"<div style=\"padding:.5em;padding-bottom:100px;background: url(/templates/equipment/images/contact_us_woman.png) 100% 100% no-repeat;\">\n\t<div>\n\t\tHeavy Machinery & Tool Rental<br />\n\t\tAddress: xyz<br />\n\t\tPhone: 123-456-7890<br />\n\t\tEmail: xyz@Heavy Machinery & Tool Rental.com<br />\n\t\t </div>\n</div>\n","template_file":"box.tpl","widget_title":{"1":"Contact Us","48":"Contact Us","49":"Contact Us","50":"Contact Us"}}';

$Column[263] = $Container[261]->Columns->getTable()->create();
$Container[261]->Columns->add($Column[263]);
$Column[263]->sort_order = '2';
$Column[263]->Styles['text-align']->definition_value = 'left';
$Column[263]->Styles['font']->definition_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[263]->Styles['line-height']->definition_value = '1em';
$Column[263]->Styles['width']->definition_value = '724px';
$Column[263]->Styles['margin']->definition_value = '{"top":12,"top_unit":"px","right":12,"right_unit":"px","bottom":12,"bottom_unit":"px","left":6,"left_unit":"px"}';
$Column[263]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[263]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[263]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[263]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[263]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[263]->Configuration['width_unit']->configuration_value = 'px';
$Column[263]->Configuration['text_align']->configuration_value = 'left';
$Column[263]->Configuration['shadows']->configuration_value = '[]';
$Column[263]->Configuration['width']->configuration_value = '724';
$Column[263]->Configuration['id']->configuration_value = 'theContainer8';
$Column[263]->Configuration['line_height']->configuration_value = '1';
$Column[263]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[263]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[263]->Configuration['font']->configuration_value = '{"color":"#000000","family":"Arial","size":1,"size_unit":"em"}';
$Column[263]->Configuration['margin']->configuration_value = '{"top":12,"top_unit":"px","right":12,"right_unit":"px","bottom":12,"bottom_unit":"px","left":6,"left_unit":"px"}';
$Column[263]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

if (!isset($Box['banner'])){
 $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
    if (!is_object($Box['banner']) || $Box['banner']->count() <= 0){
       installInfobox('extensions/imageRot/catalog/infoboxes/banner/', 'banner', 'imageRot');
       $Box['banner'] = $TemplatesInfoboxes->findOneByBoxCode('banner');
   }
}

$Widget[394] = $Column[263]->Widgets->getTable()->create();
$Column[263]->Widgets->add($Widget[394]);
$Widget[394]->identifier = 'banner';
$Widget[394]->sort_order = '1';
$Widget[394]->Configuration['widget_settings']->configuration_value = '{"selected_banner_group":"7","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';

if (!isset($Box['customText'])){
 $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/infoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $TemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[395] = $Column[263]->Widgets->getTable()->create();
$Column[263]->Widgets->add($Widget[395]);
$Widget[395]->identifier = 'customText';
$Widget[395]->sort_order = '2';
$Widget[395]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"box.tpl","widget_title":{"1":"We Rent A Variety of Heavy Equipment & Tools:","48":"","49":"","50":""},"selected_page":"28"}';

if (!isset($Box['customScroller'])){
 $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
    if (!is_object($Box['customScroller']) || $Box['customScroller']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customScroller/', 'customScroller', 'null');
       $Box['customScroller'] = $TemplatesInfoboxes->findOneByBoxCode('customScroller');
   }
}

$Widget[396] = $Column[263]->Widgets->getTable()->create();
$Column[263]->Widgets->add($Widget[396]);
$Widget[396]->identifier = 'customScroller';
$Widget[396]->sort_order = '3';
$Widget[396]->Configuration['widget_settings']->configuration_value = '{"id":"indexScroller","template_file":"box.tpl","widget_title":{"1":"Featured Products","48":"","49":"","50":""},"scrollers":{"type":"stack","configs":[{"headings":{"1":"Featured Products","48":"","49":"","50":""},"query":"featured","query_limit":"25","reflect_blocks":true,"block_width":"150","block_height":"150","prev_image":"/templates/equipment/images/scroller_prev.png","next_image":"/templates/equipment/images/scroller_next.png"}]}}';

$Container[262] = $Layout[72]->Containers->getTable()->create();
$Layout[72]->Containers->add($Container[262]);
$Container[262]->sort_order = '4';
$Container[262]->Styles['width']->definition_value = '100%';
$Container[262]->Styles['line-height']->definition_value = '1em';
$Container[262]->Styles['font']->definition_value = '{"color":"#ffffff","family":"Arial","size":1,"size_unit":"em"}';
$Container[262]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[262]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[262]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[262]->Styles['text-align']->definition_value = 'center';
$Container[262]->Styles['background_linear_gradient']->definition_value = '{"type":"linear","angle":"270","colorStops":[{"color":{"r":"67","g":"66","b":"66","a":1},"position":"0"},{"color":{"r":"0","g":"0","b":"0","a":1},"position":"1"}]}';
$Container[262]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[262]->Configuration['shadows']->configuration_value = '[]';
$Container[262]->Configuration['backgroundType']->configuration_value = '{"global":"gradient"}';
$Container[262]->Configuration['background']->configuration_value = '{"global":{"gradient":{"config":{"gradient_type":"linear","angle":"270","start_color_r":"67","start_color_g":"66","start_color_b":"66","start_color_a":"100","end_color_r":"0","end_color_g":"0","end_color_b":"0","end_color_a":"100"},"colorStops":[],"imagesBefore":[],"imagesAfter":[]}}}';
$Container[262]->Configuration['line_height']->configuration_value = '1';
$Container[262]->Configuration['line_height_unit']->configuration_value = 'em';
$Container[262]->Configuration['text_align']->configuration_value = 'center';
$Container[262]->Configuration['font']->configuration_value = '{"color":"#ffffff","family":"Arial","size":1,"size_unit":"em"}';
$Container[262]->Configuration['id']->configuration_value = 'theContainer9';
$Container[262]->Configuration['width']->configuration_value = '100';
$Container[262]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Container[262]->Configuration['width_unit']->configuration_value = '%';
$Container[262]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Container[262]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Container[262]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';

$Column[264] = $Container[262]->Columns->getTable()->create();
$Container[262]->Columns->add($Column[264]);
$Column[264]->sort_order = '1';
$Column[264]->Styles['margin']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[264]->Styles['border_radius']->definition_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[264]->Styles['text-align']->definition_value = 'center';
$Column[264]->Styles['line-height']->definition_value = '1em';
$Column[264]->Styles['width']->definition_value = '100%';
$Column[264]->Styles['padding']->definition_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[264]->Styles['text']->definition_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[264]->Styles['border']->definition_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';
$Column[264]->Styles['font']->definition_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[264]->Configuration['border_radius']->configuration_value = '{"border_top_left_radius":0,"border_top_left_radius_unit":"px","border_top_right_radius":0,"border_top_right_radius_unit":"px","border_bottom_left_radius":0,"border_bottom_left_radius_unit":"px","border_bottom_right_radius":0,"border_bottom_right_radius_unit":"px"}';
$Column[264]->Configuration['font']->configuration_value = '{"family":"Arial","size":"1","size_unit":"em","style":"normal","variant":"normal","weight":"normal"}';
$Column[264]->Configuration['backgroundType']->configuration_value = '{"global":"transparent"}';
$Column[264]->Configuration['id']->configuration_value = 'theContainer10';
$Column[264]->Configuration['text_align']->configuration_value = 'center';
$Column[264]->Configuration['line_height_unit']->configuration_value = 'em';
$Column[264]->Configuration['width']->configuration_value = '100';
$Column[264]->Configuration['text']->configuration_value = '{"color":"#ffffff","letter_spacing":"0","letter_spacing_unit":"normal","line_height":"1","line_height_unit":"em","align":"center","decoration":"none","indent":"0","indent_unit":"px","transform":"none","vertical_align":"baseline","white_space":"normal","word_spacing":"0","word_spacing_unit":"normal"}';
$Column[264]->Configuration['shadows']->configuration_value = '[]';
$Column[264]->Configuration['line_height']->configuration_value = '1';
$Column[264]->Configuration['width_unit']->configuration_value = '%';
$Column[264]->Configuration['margin']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[264]->Configuration['padding']->configuration_value = '{"top":0,"top_unit":"px","right":0,"right_unit":"px","bottom":0,"bottom_unit":"px","left":0,"left_unit":"px"}';
$Column[264]->Configuration['border']->configuration_value = '{"top":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"right":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"bottom":{"width":0,"width_unit":"px","color":"#000000","style":"solid"},"left":{"width":0,"width_unit":"px","color":"#000000","style":"solid"}}';

if (!isset($Box['navigationMenu'])){
 $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
    if (!is_object($Box['navigationMenu']) || $Box['navigationMenu']->count() <= 0){
       installInfobox('includes/modules/infoboxes/navigationMenu/', 'navigationMenu', 'null');
       $Box['navigationMenu'] = $TemplatesInfoboxes->findOneByBoxCode('navigationMenu');
   }
}

$Widget[397] = $Column[264]->Widgets->getTable()->create();
$Column[264]->Widgets->add($Widget[397]);
$Widget[397]->identifier = 'navigationMenu';
$Widget[397]->sort_order = '1';
$Widget[397]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""},"menuSettings":{"0":{"1":{"text":"Home"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"index","page":"default","target":"same"},"condition":"","children":[]},"2":{"1":{"text":"All Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"all","target":"same"},"condition":"","children":[]},"4":{"1":{"text":"Featured Products"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"products","page":"featured","target":"same"},"condition":"","children":[]},"6":{"1":{"text":"Rental FAQ"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"infoPages/show_page","page":"gv_faq","target":"same"},"condition":"","children":[]},"8":{"1":{"text":"Blog"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"blog/show_category","page":"none","target":"same"},"condition":"","children":[]},"10":{"1":{"text":"Contact Us"},"48":{"text":""},"49":{"text":""},"50":{"text":""},"icon":"none","icon_src":"","link":{"type":"app","application":"contact_us","page":"default","target":"same"},"condition":"","children":[]}},"menuId":"footerNavigationMenu","forceFit":"false"}';

if (!isset($Box['customPHP'])){
 $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
    if (!is_object($Box['customPHP']) || $Box['customPHP']->count() <= 0){
       installInfobox('includes/modules/infoboxes/customPHP/', 'customPHP', 'null');
       $Box['customPHP'] = $TemplatesInfoboxes->findOneByBoxCode('customPHP');
   }
}

$Widget[398] = $Column[264]->Widgets->getTable()->create();
$Column[264]->Widgets->add($Widget[398]);
$Widget[398]->identifier = 'customPHP';
$Widget[398]->sort_order = '2';
$Widget[398]->Configuration['widget_settings']->configuration_value = '{"php_text":" <br style=\"clear: both;\"><p><' . '?php echo sprintf(sysLanguage::get(\'FOOTER_TEXT_BODY\'), date(\'Y\'), STORE_NAME);?' . '><br />\n   <a href=\"http://www.rental-e-commerce-software.com\" style=\"font-size:7px;color:#6F0909;\">\n    Rental Management E-commerce Software Solution\n   </a></p>\n","template_file":"noFormatingBox.tpl","widget_title":{"1":"","48":"","49":"","50":""}}';
$Template->save();
$WidgetProperties = json_decode($Widget[399]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('equipment', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[399]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[399]->save();
$WidgetProperties = json_decode($Widget[400]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('equipment', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[400]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[400]->save();
$WidgetProperties = json_decode($Widget[402]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('equipment', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[402]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[402]->save();
$WidgetProperties = json_decode($Widget[408]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('equipment', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[408]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[408]->save();
$WidgetProperties = json_decode($Widget[388]->Configuration['widget_settings']->configuration_value);
$WidgetProperties->image_source = str_replace('equipment', $Template->Configuration['DIRECTORY']->configuration_value, $WidgetProperties->image_source);
$Widget[388]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[388]->save();
$WidgetProperties = json_decode($Widget[389]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('equipment', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[389]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[389]->save();
$WidgetProperties = json_decode($Widget[391]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('equipment', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[391]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[391]->save();
$WidgetProperties = json_decode($Widget[394]->Configuration['widget_settings']->configuration_value);
$BannerManagerBanners = Doctrine_Core::getTable('BannerManagerBanners');
$BannerManagerGroups = Doctrine_Core::getTable('BannerManagerGroups');
$BannerManagerBannersToGroups = Doctrine_Core::getTable('BannerManagerBannersToGroups');

$Banner = $BannerManagerBanners->findOneByBannersName('banner1_eq');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'banner1_eq';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'banner14.png';
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

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannerg3');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannerg3';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '726';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '315';
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


$Banner = $BannerManagerBanners->findOneByBannersName('bannereq2');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'bannereq2';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'bannerequip2.png';
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

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannerg3');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannerg3';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '726';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '315';
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


$Banner = $BannerManagerBanners->findOneByBannersName('bannereq3');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
	$Banner->banners_name = 'bannereq3';
		$Banner->banners_status = '2';
		$Banner->banners_products_id = '0';
		$Banner->banners_url = '';
		$Banner->banners_body = 'bannerequip3.png';
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

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('bannerg3');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
	$BannerGroup->banner_group_name = 'bannerg3';
		$BannerGroup->banner_group_is_rotator = '0';
		$BannerGroup->banner_group_show_arrows = '0';
		$BannerGroup->banner_group_show_numbers = '0';
		$BannerGroup->banner_group_show_thumbnails = '0';
		$BannerGroup->banner_group_time = '3000';
		$BannerGroup->banner_group_effect = 'none';
		$BannerGroup->banner_group_effect_time = '500';
		$BannerGroup->banner_group_width = '726';
		$BannerGroup->banner_group_spw = '7';
		$BannerGroup->banner_group_sph = '5';
		$BannerGroup->banner_group_strips = '10';
		$BannerGroup->banner_group_height = '315';
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

$Widget[394]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[394]->save();
$WidgetProperties = json_decode($Widget[395]->Configuration['widget_settings']->configuration_value);
$Pages = Doctrine_Core::getTable('Pages');
$PagesDescription = Doctrine_Core::getTable('PagesDescription');

$Page = $Pages->findOneByPageKey('middle_equipment');
if (!$Page){
$Page = $Pages->create();
$Page->sort_order = '0';
$Page->status = '1';
$Page->infobox_status = '0';
$Page->page_type = 'block';
$Page->page_key = 'middle_equipment';

$PageDescription = $PagesDescription->create();
	$PageDescription->pages_title = 'Middle Equipment';
		$PageDescription->pages_html_text = '<div id="indexContentMainContainer">
' . "\n" . '	<table border="0" cellpadding="0" cellspacing="0" style="width: 100%; ">
' . "\n" . '		<thead>
' . "\n" . '			<tr>
' . "\n" . '				<th>
' . "\n" . '					Heavy Equipment</th>
' . "\n" . '				<th>
' . "\n" . '					Tools</th>
' . "\n" . '			</tr>
' . "\n" . '		</thead>
' . "\n" . '		<tbody>
' . "\n" . '			<tr>
' . "\n" . '				<td>
' . "\n" . '					<ul style="list-style-type: square; ">
' . "\n" . '						<li>
' . "\n" . '							Backhoes</li>
' . "\n" . '						<li>
' . "\n" . '							Excavators</li>
' . "\n" . '						<li>
' . "\n" . '							Forklifts</li>
' . "\n" . '						<li>
' . "\n" . '							Trenchers</li>
' . "\n" . '						<li>
' . "\n" . '							Tractors</li>
' . "\n" . '						<li>
' . "\n" . '							Scissorlifts</li>
' . "\n" . '					</ul>
' . "\n" . '				</td>
' . "\n" . '				<td>
' . "\n" . '					<ul style="list-style-type: square; ">
' . "\n" . '						<li>
' . "\n" . '							Air Compressors</li>
' . "\n" . '						<li>
' . "\n" . '							Engines</li>
' . "\n" . '						<li>
' . "\n" . '							Generators</li>
' . "\n" . '						<li>
' . "\n" . '							Hand Tools</li>
' . "\n" . '						<li>
' . "\n" . '							Sprayers</li>
' . "\n" . '						<li>
' . "\n" . '							Water Pumps</li>
' . "\n" . '					</ul>
' . "\n" . '				</td>
' . "\n" . '			</tr>
' . "\n" . '		</tbody>
' . "\n" . '	</table>
' . "\n" . '</div>
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
$Widget[395]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[395]->save();
$WidgetProperties = json_decode($Widget[397]->Configuration['widget_settings']->configuration_value);
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('equipment', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}
$Widget[397]->Configuration['widget_settings']->configuration_value = json_encode($WidgetProperties);
$Widget[397]->save();
addLayoutToPage('account', 'address_book.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'create.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'create_success.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'credit_card_details.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'cron_auto_send_return.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'cron_membership_update.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'edit.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'history.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'history_info.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'login.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'logoff.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'membership.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'membership_cancel.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'membership_info.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'membership_upgrade.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'newsletters.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'password.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'password_forgotten.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'rental_issues.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'rented_products.php', null, $Layout[73]->layout_id);
addLayoutToPage('billing_address_process', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('center_address_check', 'center_address_check.php', null, $Layout[73]->layout_id);
addLayoutToPage('checkout', 'addresses.php', null, $Layout[73]->layout_id);
addLayoutToPage('checkout', 'cart.php', null, $Layout[73]->layout_id);
addLayoutToPage('checkout', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('checkout', 'shipping_payment.php', null, $Layout[73]->layout_id);
addLayoutToPage('checkout', 'success.php', null, $Layout[73]->layout_id);
addLayoutToPage('checkout_old', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('checkout_old', 'rental_process.php', null, $Layout[73]->layout_id);
addLayoutToPage('checkout_old', 'success.php', null, $Layout[73]->layout_id);
addLayoutToPage('contact_us', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('contact_us', 'success.php', null, $Layout[73]->layout_id);
addLayoutToPage('funways', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('gv_redeem', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('index', 'nested.php', null, $Layout[73]->layout_id);
addLayoutToPage('index', 'products.php', null, $Layout[73]->layout_id);
addLayoutToPage('index', 'index.php', null, $Layout[73]->layout_id);
addLayoutToPage('product', 'info.php', null, $Layout[73]->layout_id);
addLayoutToPage('product', 'reviews.php', null, $Layout[73]->layout_id);
addLayoutToPage('products', 'all.php', null, $Layout[73]->layout_id);
addLayoutToPage('products', 'best_sellers.php', null, $Layout[73]->layout_id);
addLayoutToPage('products', 'featured.php', null, $Layout[73]->layout_id);
addLayoutToPage('products', 'new.php', null, $Layout[73]->layout_id);
addLayoutToPage('products', 'search.php', null, $Layout[73]->layout_id);
addLayoutToPage('products', 'search_result.php', null, $Layout[73]->layout_id);
addLayoutToPage('products', 'upcoming.php', null, $Layout[73]->layout_id);
addLayoutToPage('recent_additions', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('redirect', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('rentals', 'queue.php', null, $Layout[73]->layout_id);
addLayoutToPage('rentals', 'top.php', null, $Layout[73]->layout_id);
addLayoutToPage('shoppingCart', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('shopping_cart', 'shopping_cart.php', null, $Layout[73]->layout_id);
addLayoutToPage('tell_a_friend', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('viewStream', 'default.php', null, $Layout[73]->layout_id);
addLayoutToPage('viewStream', 'pull.php', null, $Layout[73]->layout_id);
addLayoutToPage('show', 'default.php', 'articleManager', $Layout[73]->layout_id);
addLayoutToPage('show', 'info.php', 'articleManager', $Layout[73]->layout_id);
addLayoutToPage('show', 'new.php', 'articleManager', $Layout[73]->layout_id);
addLayoutToPage('show', 'rss.php', 'articleManager', $Layout[73]->layout_id);
addLayoutToPage('banner_actions', 'default.php', 'bannerManager', $Layout[73]->layout_id);
addLayoutToPage('show_archive', 'default.php', 'blog', $Layout[73]->layout_id);
addLayoutToPage('show_post', 'default.php', 'blog', $Layout[73]->layout_id);
addLayoutToPage('show_page', 'default.php', 'categoriesPages', $Layout[73]->layout_id);
addLayoutToPage('simpleDownload', 'default.php', 'customFields', $Layout[73]->layout_id);
addLayoutToPage('downloads', 'default.php', 'downloadProducts', $Layout[73]->layout_id);
addLayoutToPage('downloads', 'get.php', 'downloadProducts', $Layout[73]->layout_id);
addLayoutToPage('downloads', 'listing.php', 'downloadProducts', $Layout[73]->layout_id);
addLayoutToPage('main', 'default.php', 'downloadProducts', $Layout[73]->layout_id);
addLayoutToPage('show_page', 'default.php', 'infoPages', $Layout[73]->layout_id);
addLayoutToPage('center_address_check', 'default.php', 'inventoryCenters', $Layout[73]->layout_id);
addLayoutToPage('show_inventory', 'default.php', 'inventoryCenters', $Layout[73]->layout_id);
addLayoutToPage('show_inventory', 'delivery.php', 'inventoryCenters', $Layout[73]->layout_id);
addLayoutToPage('show_inventory', 'list.php', 'inventoryCenters', $Layout[73]->layout_id);
addLayoutToPage('account_addon', 'history_inventory_info.php', 'inventoryCenters', $Layout[73]->layout_id);
addLayoutToPage('account_addon', 'view_orders_inventory.php', 'inventoryCenters', $Layout[73]->layout_id);
addLayoutToPage('show_shipping', 'default.php', 'payPerRentals', $Layout[73]->layout_id);
addLayoutToPage('build_reservation', 'default.php', 'payPerRentals', $Layout[73]->layout_id);
addLayoutToPage('address_check', 'default.php', 'payPerRentals', $Layout[73]->layout_id);
addLayoutToPage('show_event', 'default.php', 'payPerRentals', $Layout[73]->layout_id);
addLayoutToPage('show_event', 'list.php', 'payPerRentals', $Layout[73]->layout_id);
addLayoutToPage('design', 'default.php', 'productDesigner', $Layout[73]->layout_id);
addLayoutToPage('clipart', 'default.php', 'productDesigner', $Layout[73]->layout_id);
addLayoutToPage('product_review', 'default.php', 'reviews', $Layout[73]->layout_id);
addLayoutToPage('product_review', 'details.php', 'reviews', $Layout[73]->layout_id);
addLayoutToPage('product_review', 'write.php', 'reviews', $Layout[73]->layout_id);
addLayoutToPage('account_addon', 'view_royalties.php', 'royaltiesSystem', $Layout[73]->layout_id);
addLayoutToPage('show_specials', 'default.php', 'specials', $Layout[73]->layout_id);
addLayoutToPage('streams', 'default.php', 'streamProducts', $Layout[73]->layout_id);
addLayoutToPage('streams', 'listing.php', 'streamProducts', $Layout[73]->layout_id);
addLayoutToPage('streams', 'view.php', 'streamProducts', $Layout[73]->layout_id);
addLayoutToPage('main', 'default.php', 'streamProducts', $Layout[73]->layout_id);
addLayoutToPage('notify', 'cron.php', 'waitingList', $Layout[73]->layout_id);
addLayoutToPage('notify', 'default.php', 'waitingList', $Layout[73]->layout_id);
addLayoutToPage('account', 'address_book_details.php', null, $Layout[73]->layout_id);
addLayoutToPage('account', 'address_book_process.php', null, $Layout[73]->layout_id);
addLayoutToPage('show_category', 'default.php', 'blog', $Layout[73]->layout_id);
addLayoutToPage('show_category', 'rss.php', 'blog', $Layout[73]->layout_id);
addLayoutToPage('index', 'default.php', null, $Layout[72]->layout_id);
