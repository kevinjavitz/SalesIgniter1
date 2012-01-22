<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxCufonFonts extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('cufonFonts');
		$this->buildJavascriptMultiple = true;
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			//$this->setBoxContent($htmlText);
			return $this->draw();
	}

	public function buildJavascript(){
		$boxWidgetProperties = $this->getWidgetProperties();

		ob_start();
		readfile(sysConfig::getDirFsCatalog().'templates/'.Session::get('tplDir').'/fonts/'.$boxWidgetProperties->applied_font.'.js');
		?>
		Cufon.replace('<?php echo $boxWidgetProperties->applied_elements;?>', {
		<?php if (!empty($boxWidgetProperties->cufon_text_shadow)){ ?>
		textShadow: '<?php echo $boxWidgetProperties->cufon_text_shadow; ?>',
		<?php } ?>
		<?php if (!empty($boxWidgetProperties->cufon_color)){ ?>
		color: '<?php echo $boxWidgetProperties->cufon_color; ?>',
		<?php } ?>
		<?php if (!empty($boxWidgetProperties->cufon_font_family)){ ?>
		fontFamily: '<?php echo $boxWidgetProperties->cufon_font_family; ?>',
		<?php } ?>
		<?php if (!empty($boxWidgetProperties->cufon_font_size)){ ?>
		fontSize: '<?php echo $boxWidgetProperties->cufon_font_size; ?>',
		<?php } ?>
		<?php if (!empty($boxWidgetProperties->cufon_font_stretch)){ ?>
		fontStretch: '<?php echo $boxWidgetProperties->cufon_font_stretch; ?>',
		<?php } ?>
		<?php if (!empty($boxWidgetProperties->cufon_font_style)){ ?>
		fontStyle: '<?php echo $boxWidgetProperties->cufon_font_style; ?>',
		<?php } ?>
		<?php if (!empty($boxWidgetProperties->cufon_font_weight)){ ?>
		fontWeight: '<?php echo $boxWidgetProperties->cufon_font_weight; ?>',
		<?php } ?>
		hover: {
			<?php if (!empty($boxWidgetProperties->cufon_text_shadow_hover)){ ?>
			textShadow: <?php echo "'" .$boxWidgetProperties->cufon_text_shadow_hover . "'" . (!empty($boxWidgetProperties->cufon_hover_font_family)||!empty($boxWidgetProperties->cufon_hover_color)||!empty($boxWidgetProperties->cufon_hover_font_size)||!empty($boxWidgetProperties->cufon_hover_font_weight)||!empty($boxWidgetProperties->cufon_hover_font_style) ? ',' : '' ); ?>
			<?php }?>
			<?php if (!empty($boxWidgetProperties->cufon_hover_font_family)){ ?>
			fontFamily: <?php echo "'" .$boxWidgetProperties->cufon_hover_font_family . "'" . (!empty($boxWidgetProperties->cufon_hover_color)||!empty($boxWidgetProperties->cufon_hover_font_size)||!empty($boxWidgetProperties->cufon_hover_font_weight)||!empty($boxWidgetProperties->cufon_hover_font_style) ? ',' : '' ); ?>
			<?php }?>
		<?php if (!empty($boxWidgetProperties->cufon_hover_font_size)){ ?>
		fontSize: <?php echo "'" .$boxWidgetProperties->cufon_hover_font_size . "'" . (!empty($boxWidgetProperties->cufon_hover_color)||!empty($boxWidgetProperties->cufon_hover_font_weight)||!empty($boxWidgetProperties->cufon_hover_font_style) ? ',' : '' ); ?>
		<?php }?>

		<?php if (!empty($boxWidgetProperties->cufon_hover_font_weight)){ ?>
		fontWeight: <?php echo "'" .$boxWidgetProperties->cufon_hover_font_weight . "'" . (!empty($boxWidgetProperties->cufon_hover_color)||!empty($boxWidgetProperties->cufon_hover_font_style) ? ',' : '' ); ?>
		<?php }?>
		<?php if (!empty($boxWidgetProperties->cufon_hover_font_style)){ ?>
		fontStyle: <?php echo "'" .$boxWidgetProperties->cufon_hover_font_style . "'" . (!empty($boxWidgetProperties->cufon_hover_color) ? ',' : '' ); ?>
		<?php }?>
		<?php if (!empty($boxWidgetProperties->cufon_hover_color)){?>
				color: '<?php echo $boxWidgetProperties->cufon_hover_color; ?>'
		<?php } ?>
		}
		});
		<?php
		$javascript = ob_get_contents();
		ob_end_clean();

		return $javascript;
	}
	
	public function onTemplateExport(&$iInfo, $data){
		$widgetProperties = unserialize($iInfo->widget_properties);
		if (!isset($widgetProperties['image_src'])){
			return;
		}
		$fileContent = '';
		ob_start();
?>
		$widgetProperties['image_src'] = str_replace('<?php echo $data['template_name'];?>', $tplName, $widgetProperties['image_src']);
<?php
		$fileContent = ob_get_contents();
		ob_end_clean();
		
		return $fileContent;
	}
}
?>