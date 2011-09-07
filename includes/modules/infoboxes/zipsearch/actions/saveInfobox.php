<?php
	if(isset($_POST['nr_imag'])){
		$TemplateInfoboxesCollection[$myValue]['widget_properties']['nr_imag'] = $_POST['nr_imag'];
	}else{
		$TemplateInfoboxesCollection[$myValue]['widget_properties']['nr_imag'] = 3;
	}

	if(isset($_POST['width_imag'])){
		$TemplateInfoboxesCollection[$myValue]['widget_properties']['width_imag'] = $_POST['width_imag'];
	}else{
		$TemplateInfoboxesCollection[$myValue]['widget_properties']['width_imag'] = 3;
	}

	if(isset($_POST['height_imag'])){
		$TemplateInfoboxesCollection[$myValue]['widget_properties']['height_imag'] = $_POST['height_imag'];
	}else{
		$TemplateInfoboxesCollection[$myValue]['widget_properties']['height_imag'] = 3;
	}

	if(isset($_POST['nr_new'])){
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['nr_new'] = $_POST['nr_new'];
		}else{
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['nr_new'] = 10;
		}
	if(isset($_POST['nr_best'])){
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['nr_best'] = $_POST['nr_best'];
		}else{
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['nr_best'] = 10;
		}
	if(isset($_POST['nr_feat'])){
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['nr_feat'] = $_POST['nr_feat'];
		}else{
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['nr_feat'] = 10;
		}

	if(isset($_POST['new_text'])){
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['new_text'] = $_POST['new_text'];
		}else{
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['new_text'] = 10;
		}
	if(isset($_POST['best_text'])){
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['best_text'] = $_POST['best_text'];
		}else{
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['best_text'] = 10;
		}
	if(isset($_POST['feat_text'])){
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['feat_text'] = $_POST['feat_text'];
		}else{
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['feat_text'] = 3;
		}
	if(isset($_POST['nr_space'])){
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['nr_space'] = $_POST['nr_space'];
		}else{
			$TemplateInfoboxesCollection[$myValue]['widget_properties']['nr_space'] = 30;
		}

?>