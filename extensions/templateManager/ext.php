<?php
class Extension_templateManager extends ExtensionBase {

	public function __construct(){
		parent::__construct('templateManager');
	}

	public function init(){
		global $appExtension;
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
				'PageCreateWidgets',
				'PageLayoutAfterCss'
			), null, $this);
	}

	public function postSessionInit(){
		global $templateDir;
		if (isset($_GET['tplDir']) && is_dir(sysConfig::getDirFsCatalog() . 'templates/' . basename($_GET['tplDir']))) {
			Session::set('tplDir', basename($_GET['tplDir']));
		} else {
			if (Session::exists('tplDir') === true && is_dir(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir'))){
			}else{
				if (APPLICATION_ENVIRONMENT == 'admin'){
					Session::set('tplDir', 'fallback');
				}else{
					Session::set('tplDir', sysConfig::get('DIR_WS_TEMPLATES_DEFAULT'));
				}
			}
		}

		EventManager::notify('SetTemplateName');

		$tplDir = Session::get('tplDir');

		if ((preg_match('/^[[:alnum:]|_|-]+$/', $tplDir)) && (is_dir(sysConfig::getDirFsCatalog() . 'templates/' . $tplDir))){
			// 'Input Validated' only allow alfanumeric characters and underscores in template name
			sysConfig::set('DIR_WS_TEMPLATES', sysConfig::getDirFsCatalog() . 'templates/' . $tplDir . '/' );
		} else {
			echo strip_tags($tplDir) . '<br>';
			exit('Illegal template directory!');
		}

		if (APPLICATION_ENVIRONMENT == 'admin'){
			$templateDir = sysConfig::getDirWsAdmin() . 'template/fallback/';
		}else{
			$templateDir = sysConfig::getDirWsCatalog() . 'templates/' . Session::get('tplDir') . '/';
		}
		/*if (basename($_SERVER['PHP_SELF']) == 'stylesheet.php'){
			$templateDir = Session::get('tplDir');
			$layoutId = $_GET['layout_id'];
			$import = '';
			if (isset($_GET['import']) && !empty($_GET['import'])){
				$import = $_GET['import'];
			}

			$cacheKey = 'stylesheet_' . $templateDir . '_' . $layoutId . '_' . $import;

			header('Content-Type:text/css; charset=utf-8');
			FileCache::serve($cacheKey);
		}else
		if (basename($_SERVER['PHP_SELF']) == 'javascript.php'){
			$templateDir = Session::get('tplDir');
			$layoutId = $_GET['layout_id'];
			$import = '';
			if (isset($_GET['import']) && !empty($_GET['import'])){
				$import = $_GET['import'];
			}

			$cacheKey = 'javascript_' . $templateDir . '_' . $layoutId . '_' . $import;

			header('Content-Type:application/javascript; charset=utf-8');
			FileCache::serve($cacheKey);
		}*/
	}

	public function PageLayoutAfterCss(){
		global $App;
		$thisTemplate = Session::get('tplDir');
		$templateData = file_get_contents(sysConfig::getDirFsCatalog(). 'templates/'.$thisTemplate.'/templateData.tms');
		$templateArr = explode(';', $templateData);

		$liquid = '';

		foreach($templateArr as $eachVar){
			if(!empty($eachVar)){
				$myVarArr = explode(':', trim($eachVar));
				if(is_array($myVarArr) && isset($myVarArr[1])){
					$$myVarArr[0] = $myVarArr[1];
				}
			}
		}
		if(isset($liquid) && ($liquid == '1')){
			return '<link rel="stylesheet" href="'.'templates/'.$thisTemplate.'/liquid.css'.'" type="text/css">';
		}else{
			return '';
		}

	}

	public function PageCreateWidgets($Template, $layout_id, $pageContent, $theBox){
		//here I identify the top dir(template folder)
		//I create variables for every widget from template...If there are two the same it will get an id before the name
		$Layout = Doctrine_Core::getTable('TemplateManagerLayouts')->find($layout_id);
		if ($Layout){
			foreach($Layout->Containers as $conInfo){
				//if ($conInfo->Parent->container_id > 0) continue;

				foreach($conInfo->Columns as $colInfo){
					foreach($colInfo->Widgets as $wInfo){
						foreach($wInfo->Configuration as $config){
							if ($config->configuration_key == 'widget_settings'){
								$WidgetSettings = json_decode($config->configuration_value);
								break;
							}
						}
						$className = 'InfoBox' . ucfirst($wInfo->identifier);
						if (!class_exists($className)){
							$Qbox = Doctrine_Query::create()
								->select('box_path')
								->from('TemplatesInfoboxes')
								->where('box_code = ?', $wInfo->identifier)
								->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

							require($Qbox[0]['box_path'] . 'infobox.php');
						}

						$Box = new $className();

						if (isset($WidgetSettings->template_file) && !empty($WidgetSettings->template_file)){
							$Box->setBoxTemplateFile($WidgetSettings->template_file);
						}
						if (isset($WidgetSettings->id) && !empty($WidgetSettings->id)){
							$Box->setBoxId($WidgetSettings->id);
						}

						$Box->setWidgetProperties($WidgetSettings);
						$Box->setBoxHeading($WidgetSettings->widget_title->{Session::get('languages_id')});

						if ($wInfo->identifier != 'pageContent'){
							$Template->setReference($wInfo->identifier . '_' . $wInfo->widget_id, $Box->show());
						}else{
							$Template->setReference($wInfo->identifier . '_' . $wInfo->widget_id, $pageContent);
						}
					}
				}
			}
		}
	}
}
/* @TODO: Find a better place for this stuff */
global $jqueryThemeDir, $jqueryThemeBG, $jqueryThemeIcons, $jqueryThemeImages, $templateDir;

$jqueryThemeDir = sysConfig::getDirWsCatalog() . 'ext/jQuery/themes/smoothness/';
$jqueryThemeBG = sysConfig::getDirWsCatalog() . 'ext/jQuery/themes/smoothness/';
$jqueryThemeIcons = sysConfig::getDirWsCatalog() . 'ext/jQuery/themes/icons';
$jqueryThemeImages = sysConfig::getDirWsCatalog() . 'ext/jQuery/themes/smoothness/images';

function jqueryIconsPath($color){
	global $jqueryThemeIcons;
	return $jqueryThemeIcons . '/ui-icons_' . $color . '_256x240.png';
}

function matchEngineVersion($engine, $v){
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$matched = false;
	$vInfo = array();
	preg_match_all('/' . $engine . '\/(.*)\)/', $u_agent, $vInfo);
	if ((int) $vInfo[1][0] == $v){
		$matched = true;
	}
	return $matched;
}

function matchUserAgent($toMatch){
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = false;
	if (preg_match('/' . $toMatch . '/i',$u_agent)){
		$ub = true;
	}
	return $ub;
}

function isIE(){
	return matchUserAgent('MSIE');
}

/* Trident/3.0 */
function isIE7(){
	return (isIE() ? matchEngineVersion('Trident', 3) : false);
}

/* Trident/4.0 */
function isIE8(){
	return (isIE() ? matchEngineVersion('Trident', 4) : false);
}

/* Trident/5.0 */
function isIE9(){
	return (isIE() ? matchEngineVersion('Trident', 5) : false);
}

/* Trident/6.0 */
function isIE10(){
	return (isIE() ? matchEngineVersion('Trident', 6) : false);
}

function isMoz(){
	return (matchUserAgent('Mozilla') && !matchUserAgent('AppleWebKit'));
}

function isWebkit(){
	return matchUserAgent('AppleWebKit');
}

function isPresto(){
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = false;
	if (matchUserAgent('Presto')){
		$vInfo = array();
		preg_match_all('/Presto\/(.*) Version/', $u_agent, $vInfo);
		if ($vInfo[1][0] > 2.7){
			$ub = true;
		}
	}
	return $ub;
}

function buildBackgroundAlpha($r, $g, $b, $a, &$styleObj = false){
	$cssData = array();
	if (isIE8() === true){
		$cssData['-pie-background'] = 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ')';
		$cssData['behavior'] = 'url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc)';
	}else{
		$cssData['background-color'] = 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ')';
	}

	$css = '';
	foreach($cssData as $bgKey => $bgInfo){
		if ($styleObj !== false){
			$styleObj->addRule($bgKey, $bgInfo);
		}else{
			$css .= $bgKey . ': ' . $bgInfo . ';';
		}
	}
	return $css;
}

function buildSimpleGradient($start, $end, &$styleObj = false){
	return buildLinearGradient(270, array(
			array($start, 0),
			array($end, 1)
		), $styleObj);
}

function buildLinearGradient($deg, $colorStops, $images = false, &$styleObj = false) {
	$cssData = array();
	if (isIE7() === true){
		$stops = array();
		foreach($colorStops as $cInfo){
			$stops[] = $cInfo[0] . ' ' . ($cInfo[1] * 100) . '%';
		}

		if ($images !== false){
			foreach($images as $iInfo){
				if (isset($iInfo['css_placement']) && $iInfo['css_placement'] == 'after') {
					continue;
				}

				$cssData['-pie-background'][] = 'url(' . $iInfo['image'] . ') ' .
					$iInfo['repeat'] . ' ' .
					(isset($iInfo['attachment']) ? $iInfo['attachment'] . ' ' : 'scroll ') .
					$iInfo['pos_x'] . ' ' . $iInfo['pos_y'];
			}
		}
		$cssData['-pie-background'][] = 'linear-gradient(' . $deg . 'deg, ' . implode(', ', $stops) . ')';
		if ($images !== false){
			foreach($images as $iInfo){
				if (isset($iInfo['css_placement']) && $iInfo['css_placement'] == 'before') {
					continue;
				}

				$cssData['-pie-background'][] = 'url(' . $iInfo['image'] . ') ' .
					$iInfo['repeat'] . ' ' .
					(isset($iInfo['attachment']) ? $iInfo['attachment'] . ' ' : 'scroll ') .
					$iInfo['pos_x'] . ' ' . $iInfo['pos_y'];
			}
		}
		$cssData['behavior'][] = 'url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc)';
	}
	elseif (isIE8() === true){
		$stops = array();
		foreach($colorStops as $cInfo){
			$stops[] = array(
				'pos' => ($cInfo[1] * 100),
				'color' => $cInfo[0],
				'opacity' => 100
			);
		}

		$imageName = md5('browser=ie8&angle=' . $deg . '&colorStops=' . json_encode($stops));
		if (file_exists(sysConfig::getDirFsCatalog() . 'cache/' . $imageName . '.png')){
			$cssData['background-image'][] = 'url(' . sysConfig::getDirWsCatalog() . 'cache/' . $imageName . '.png)';
		}else{
			$cssData['-jquery'][] = '$(this).css(\'background-image\', \'url(/extensions/templateManager/catalog/globalFiles/IE8_gradient.php?width=\' + $(this).width() + \'&height=\' + $(this).height() + \'&angle=' . $deg . '&colorStops=' . urlencode(json_encode($stops)) . ')\')';
		}
		$cssData['background-repeat'][] = 'repeat-x';
		$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
		$cssData['background-position'][] = '0% 0%';
	}
	else {
		if ($images !== false){
			foreach($images as $iInfo){
				if (isset($iInfo['css_placement']) && $iInfo['css_placement'] == 'after') {
					continue;
				}

				$cssData['background'][] = 'url(' . $iInfo['image'] . ')';
				$cssData['background-repeat'][] = $iInfo['repeat'];
				$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
				$cssData['background-position'][] = $iInfo['pos_x'] . ' ' . $iInfo['pos_y'];
			}
		}

		if (isIE9() === true){
			$stops = array();
			foreach($colorStops as $cInfo){
				$stops[] = array(
					'pos' => ($cInfo[1] * 100),
					'color' => $cInfo[0],
					'opacity' => 100
				);
			}
			$cssData['background'][] = 'url(/extensions/templateManager/catalog/globalFiles/IE9_gradient.php?angle=' . $deg . '&colorStops=' . urlencode(json_encode($stops)) . ')';
			$cssData['background-repeat'][] = 'no-repeat';
			$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
			$cssData['background-position'][] = '0% 0%';
		}
		else {
			$stops = array();
			foreach($colorStops as $cInfo){
				$stops[] = $cInfo[0] . ' ' . ($cInfo[1] * 100) . '%';
			}

			$prefix = '';
			switch(true){
				case (isIE10() === true):
					$prefix = '-ms-';
					break;
				case (isPresto() === true):
					$prefix = '-o-';
					break;
				case (isMoz() === true):
					$prefix = '-moz-';
					break;
				case (isWebkit() === true):
					$prefix = '-webkit-';
					break;
			}

			$angle = $deg . 'deg';
			switch($deg){
				case 0: $angle = 'left'; break;
				case 45: $angle = 'bottom left'; break;
				case 90: $angle = 'bottom'; break;
				case 135: $angle = 'bottom right'; break;
				case 190: $angle = 'right'; break;
				case 235: $angle = 'top right'; break;
				case 270: $angle = 'top'; break;
				case 315: $angle = 'top left'; break;
				case 360: $angle = 'left'; break;
			}

			$cssData['background'][] = $prefix . 'linear-gradient(' . $angle . ', ' . implode(', ', $stops) . ')';
			$cssData['background-repeat'][] = 'no-repeat';
			$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
			$cssData['background-position'][] = '0% 0%';
		}

		if ($images !== false){
			foreach($images as $iInfo){
				if (!isset($iInfo['css_placement']) || $iInfo['css_placement'] == 'before') {
					continue;
				}

				$cssData['background'][] = 'url(' . $iInfo['image'] . ')';
				$cssData['background-repeat'][] = $iInfo['repeat'];
				$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
				$cssData['background-position'][] = $iInfo['pos_x'] . ' ' . $iInfo['pos_y'];
			}
		}
	}
	$css = '';
	foreach($cssData as $bgKey => $bgInfo){
		if ($styleObj !== false){
			$styleObj->addRule($bgKey, implode(', ', $bgInfo));
		}else{
			$css .= $bgKey . ': ' . implode(', ', $bgInfo) . ';';
		}
	}
	return $css;
}

function buildBorderRadius($tl = 0, $tr = 0, $br = 0, $bl = 0, &$styleObj = false){
	$cssData = array();
	$prefix = '';
	switch(true){
		case (isIE() === false && isMoz() === true):
			$prefix = '-moz-';
			break;
		case (isWebkit() === true):
			$prefix = '-webkit-';
			break;
	}
	$cssData[$prefix . 'border-radius'] = $tl . ' ' . $tr . ' ' . $br . ' ' . $bl;
	if (isIE8() === true){
		$cssData['behavior'] = 'url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc)';
	}

	$css = '';
	foreach($cssData as $bgKey => $bgInfo){
		if ($styleObj !== false){
			$styleObj->addRule($bgKey, $bgInfo);
		}else{
			$css .= $bgKey . ': ' . $bgInfo . ';';
		}
	}
	return $css;
}

function buildBoxShadow($shadows, &$styleObj = false){
	$cssData = array();

	$allShadows = array();
	foreach($shadows as $sInfo){
		$allShadows[] = (isset($sInfo[5]) && $sInfo[5] === true ? 'inset ' : '') .
			$sInfo[0] . ' ' .
			$sInfo[1] . ' ' .
			$sInfo[2] . ' ' .
			$sInfo[3] . ' ' .
			$sInfo[4];
	}

	$prefix = '';
	switch(true){
		case (isMoz() === true):
			$prefix = '-moz-';
			break;
		case (isWebkit() === true):
			$prefix = '-webkit-';
			break;
	}
	$cssData[$prefix . 'box-shadow'] = implode(', ', $allShadows);

	$css = '';
	foreach($cssData as $bgKey => $bgInfo){
		if ($styleObj !== false){
			$styleObj->addRule($bgKey, $bgInfo);
		}
		else {
			$css .= $bgKey . ': ' . $bgInfo . ';';
		}
	}
	return $css;
}
