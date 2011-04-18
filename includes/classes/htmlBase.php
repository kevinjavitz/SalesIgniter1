<?php
require(sysConfig::getDirFsCatalog() . 'includes/classes/html/base/element.php');

/**
 * Static access for building html elements or widgets
 */
class htmlBase {

	/**
	 * Initializes html element/widget based on the element type
	 * @param string $elementType The element type or widget name to use
	 * @param string $html [optional] Html contents to attach to the element
	 * @return htmlElement
	 * @return htmlWidget
	 */
  	public static function newElement($elementType, $html = ''){
  		$elementClassName = 'htmlElement_' . $elementType;
  		$widgetClassName = 'htmlWidget_' . $elementType;
		$elementDir = sysConfig::getDirFsCatalog() . 'includes/classes/html/elements/';
		$widgetDir = sysConfig::getDirFsCatalog() . 'includes/classes/html/widgets/';

  		if (file_exists($elementDir . $elementType . '.php')){
 	 		if (!class_exists($elementClassName)){
 	 			require($elementDir . $elementType . '.php');
	  		}
	  		$element = new $elementClassName($html);
   		}elseif (file_exists($widgetDir . $elementType . '.php')){
 	 		if (!class_exists($widgetClassName)){
 	 			require($widgetDir . $elementType . '.php');
	  		}
	  		$element = new $widgetClassName($html);
  		}else{
  			$element = new htmlElement($elementType);
  			if (!empty($html)){
  				$element->html($html);
  			}
  		}
		return $element->startChain();
  	}
}
?>