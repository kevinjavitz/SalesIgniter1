<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxCufon extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('cufon');
		$this->buildJavascriptMultiple = true;
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			//$this->setBoxContent($htmlText);
			return $this->draw();
	}
	/*readfile(sysConfig::getDirFsCatalog().'extensions/templateManager/widgets/cufonFonts/javascript/cufon-yui.js');*/
	public function buildJavascript(){
		$boxWidgetProperties = $this->getWidgetProperties();

		ob_start();

		foreach($boxWidgetProperties->fonts as $iInfo){
			readfile(sysConfig::getDirFsCatalog().'fonts/js/'.$iInfo->font);
		}
		foreach($boxWidgetProperties->fonts as $iInfo){
		?>
		Cufon.replace('<?php echo $iInfo->fontElements;?>', {
		<?php if (!empty($iInfo->fontShadow)){ ?>
		textShadow: '<?php echo $iInfo->fontShadow; ?>',
		<?php } ?>
		<?php if (!empty($iInfo->fontColor)){ ?>
		color: '<?php echo $iInfo->fontColor; ?>',
		<?php } ?>
		<?php if (!empty($iInfo->fontFamily)){ ?>
		fontFamily: '<?php echo $iInfo->fontFamily; ?>',
		<?php } ?>
		<?php if (!empty($iInfo->fontSize)){ ?>
		fontSize: '<?php echo $iInfo->fontSize; ?>',
		<?php } ?>

		hover: {
			<?php if (!empty($iInfo->fontShadowHover)){ ?>
			textShadow: <?php echo "'" .$iInfo->fontShadowHover . "'" . (!empty($iInfo->fontFamilyHover)||!empty($iInfo->fontColorHover)||!empty($iInfo->fontSizeHover) ? ',' : '' ); ?>
			<?php }?>
			<?php if (!empty($iInfo->fontFamilyHover)){ ?>
			fontFamily: <?php echo "'" .$iInfo->fontFamilyHover . "'" . (!empty($iInfo->fontColorHover)||!empty($iInfo->fontSizeHover) ? ',' : '' ); ?>
			<?php }?>
		<?php if (!empty($iInfo->fontSizeHover)){ ?>
		fontSize: <?php echo "'" .$iInfo->fontSizeHover . "'" . (!empty($iInfo->fontColorHover) ? ',' : '' ); ?>
		<?php }?>
		<?php if (!empty($iInfo->fontColorHover)){?>
				color: '<?php echo $iInfo->fontColorHover; ?>'
		<?php } ?>
		}
		});
		<?php
}
			?>
		var i=0;
		var url1 = '';
		$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
			if(jqXHR != url1){

				url1 = jqXHR;
			jqXHR.complete(function() {
			if(i <= 20){
			Cufon.refresh();
			i = i+1;
			}
			});
		}

		});
		<?php
		$javascript = ob_get_contents();
		ob_end_clean();

		return $javascript;
	}
}
?>