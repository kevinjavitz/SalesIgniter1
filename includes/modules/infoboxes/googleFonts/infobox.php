<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxGoogleFonts extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('googleFonts');
		$this->buildJavascriptMultiple = true;
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			return $this->draw();
	}

	public function buildJavascript(){
		$boxWidgetProperties = $this->getWidgetProperties();
		ob_start();
		?>
		var link = $("<link>");
		link.attr({
		type: 'text/css',
		rel: 'stylesheet',
		href: 'http://fonts.googleapis.com/css?family=<?php echo $boxWidgetProperties->applied_font;?>'
		});
			$("head").append( link );
		<?php
		$javascript = ob_get_contents();
		ob_end_clean();

		return $javascript;
	}
}
?>