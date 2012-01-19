<?php
	if(isset($_POST['applied_elements'])){
		$WidgetProperties['applied_elements'] = $_POST['applied_elements'];
	}
	if(isset($_POST['applied_font'])){
		$WidgetProperties['applied_font'] = $_POST['applied_font'];
	}
	$WidgetProperties['cufon_text_shadow'] = $_POST['cufon_text_shadow'];
	$WidgetProperties['cufon_text_shadow_hover'] = $_POST['cufon_text_shadow_hover'];
	$WidgetProperties['cufon_hover_color'] = $_POST['cufon_hover_color'];
	$WidgetProperties['cufon_hover_font_size'] = $_POST['cufon_hover_font_size'];
	$WidgetProperties['cufon_hover_font_weight'] = $_POST['cufon_hover_font_weight'];
	$WidgetProperties['cufon_hover_font_family'] = $_POST['cufon_hover_font_family'];
	$WidgetProperties['cufon_hover_font_style'] = $_POST['cufon_hover_font_style'];
	$WidgetProperties['cufon_color'] = $_POST['cufon_color'];
	$WidgetProperties['cufon_font_family'] = $_POST['cufon_font_family'];
	$WidgetProperties['cufon_font_style'] = $_POST['cufon_font_style'];
	$WidgetProperties['cufon_font_size'] = $_POST['cufon_font_size'];
	$WidgetProperties['cufon_font_stretch'] = $_POST['cufon_font_stretch'];
	$WidgetProperties['cufon_font_weight'] = $_POST['cufon_font_weight'];

?>