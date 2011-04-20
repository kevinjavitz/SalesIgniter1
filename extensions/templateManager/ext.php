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
if (APPLICATION_ENVIRONMENT == 'admin'){
	$templateDir = sysConfig::getDirWsAdmin() . 'template/fallback/';
}else{
	$templateDir = sysConfig::getDirWsCatalog() . 'templates/' . Session::get('tplDir') . '/';
}

function jqueryIconsPath($color){
	global $jqueryThemeIcons;
	return $jqueryThemeIcons . '/ui-icons_' . $color . '_256x240.png';
}

function isIE(){
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = false;
	if (preg_match('/MSIE/i',$u_agent)){
		$ub = true;
		$vInfo = array();
		preg_match_all('/Trident\/(.*)\)/', $u_agent, $vInfo);
		if ($vInfo[1][0] > 4){
			$ub = false;
		}
	}
	return $ub;
}

function isIE9(){
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = false;
	if (preg_match('/MSIE/i',$u_agent)){
		$vInfo = array();
		preg_match_all('/Trident\/(.*)\)/', $u_agent, $vInfo);
		if ($vInfo[1][0] > 4){
			$ub = true;
		}
	}
	return $ub;
}

function isMoz(){
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = false;
	if (preg_match('/Mozilla/i',$u_agent) && !preg_match('/AppleWebKit/i',$u_agent)){
		$ub = true;
	}
	return $ub;
}

function isWebkit(){
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = false;
	if (preg_match('/AppleWebKit/i',$u_agent)){
		$ub = true;
	}
	return $ub;
}

function isPresto(){
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = false;
	if (preg_match('/Presto/i',$u_agent)){
		$vInfo = array();
		preg_match_all('/Presto\/(.*) Version/', $u_agent, $vInfo);
		if ($vInfo[1][0] > 2.7){
			$ub = true;
		}
	}
	return $ub;
}

function buildBackgroundAlpha($r, $g, $b, $a){
	$css = '';
	if (isIE() === true){
		$css .= '-pie-background: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');' .
			'behavior: url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc);';
	}elseif (isIE9() === true){
		$css .= 'background-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
	}elseif (isPresto() === true){
		$css .= 'background-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
	}elseif (isMoz() === true){
		$css .= 'background-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
	}elseif (isWebkit() === true){
		$css .= 'background-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
	}
	return $css;
}

function buildSimpleGradient($start, $end){
	$css = '';
	if (isIE() === true){
		$css .= '-pie-background: linear-gradient(' . $start . ' 0%, ' . $end . ' 100%);' .
			'behavior: url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc);';
	}elseif (isIE9() === true){
		$css .= 'background-image: url(/extensions/templateManager/catalog/globalFiles/IE9_gradient.php?start_pos_x=0&start_pos_y=0&end_pos_x=0&end_pos_y=100&colorStops=' . urlencode(json_encode(array(
			array(
				'pos' => '0',
				'color' => $start,
				'opacity' => '1'
			),
			array(
				'pos' => '100',
				'color' => $end,
				'opacity' => '1'
			)
		))) . ');';
	}elseif (isPresto() === true){
		$css .= 'background-image: -o-linear-gradient(top, ' . $start . ' 0%, ' . $end . ' 100%);';
	}elseif (isMoz() === true){
		$css .= 'background-image: -moz-linear-gradient(center top, ' . $start . ' 0%, ' . $end . ' 100%);';
	}elseif (isWebkit() === true){
		$css .= 'background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, ' . $start . '), color-stop(1, ' . $end . '));';
	}
	return $css;
}

function buildComplexGradient($gradientType, $xStart, $yStart, $xEnd, $yEnd, $colorStops, $images = false){
	$css = '';

	$prependBgImages = '';
	if ($images !== false){
		foreach($images as $iInfo){
			if (isset($iInfo['css_placement']) && $iInfo['css_placement'] == 'after') continue;

			$cssData['background'][] = 'url(' . $iInfo['image'] . ')';
			$cssData['background-repeat'][] = $iInfo['repeat'];
			$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
			$cssData['background-position'][] = $iInfo['pos_x'] . '% ' . $iInfo['pos_y'] . '%';

			//$prependBgImages .= 'url(' . $iInfo['image'] . ') ' . $iInfo['repeat'] . '' . (isset($iInfo['attachment']) ? ' ' . $iInfo['attachment'] : '') . ' ' . $iInfo['pos_x'] . '% ' . $iInfo['pos_y'] . '%, ';
		}
	}

	if (isIE() === true){
		$stops = array();
		foreach($colorStops as $cInfo){
			$stops[] = $cInfo[0] . ' ' . ($cInfo[1] * 100) . '%';
		}
		return '-pie-background: ' . $gradientType . '-gradient(' . implode(', ', $stops) . ');' .
			'behavior: url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc);';
	}elseif (isIE9() === true){
		$stops = array();
		foreach($colorStops as $cInfo){
			$stops[] = array(
				'pos' => ($cInfo[1] * 100),
				'color' => $cInfo[0],
				'opacity' => 100
			);
		}
		$cssData['background'][] = 'url(/extensions/templateManager/catalog/globalFiles/IE9_gradient.php?start_pos_x=0&start_pos_y=0&end_pos_x=0&end_pos_y=100&colorStops=' . urlencode(json_encode($stops)) . ')';
		$cssData['background-repeat'][] = 'repeat';
		$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
		$cssData['background-position'][] = '0% 0%';
		//$css .= 'background: ' . $prependBgImages . 'url(/extensions/templateManager/catalog/globalFiles/IE9_gradient.php?start_pos_x=0&start_pos_y=0&end_pos_x=0&end_pos_y=100&colorStops=' . urlencode(json_encode($stops)) . ')' . $appendBgImages . ';';
	}elseif (isPresto() === true){
		$stops = array();
		foreach($colorStops as $cInfo){
			$stops[] = $cInfo[0] . ' ' . ($cInfo[1] * 100) . '%';
		}
		$cssData['background'][] = '-o-' . $gradientType . '-gradient(top, ' . implode(', ', $stops) . ')';
		$cssData['background-repeat'][] = 'repeat';
		$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
		$cssData['background-position'][] = '0% 0%';
		//$css .= 'background: ' . $prependBgImages . '-o-' . $gradientType . '-gradient(top, ' . implode(', ', $stops) . ')' . $appendBgImages . ';';
	}elseif (isMoz() === true){
		$stops = array();
		foreach($colorStops as $cInfo){
			$stops[] = $cInfo[0] . ' ' . ($cInfo[1] * 100) . '%';
		}
		$cssData['background'][] = '-moz-' . $gradientType . '-gradient(top, ' . implode(', ', $stops) . ')';
		$cssData['background-repeat'][] = 'repeat';
		$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
		$cssData['background-position'][] = '0% 0%';
		//$css .= 'background: ' . $prependBgImages . '-moz-' . $gradientType . '-gradient(top, ' . implode(', ', $stops) . ')' . $appendBgImages . ';';
	}elseif (isWebkit() === true){
		$stops = array();
		foreach($colorStops as $k => $cInfo){
			if ($k == 0){
				$from = 'from(' . $cInfo[0] . ')';
			}elseif (!isset($colorStops[$k + 1])){
				$to = 'to(' . $cInfo[0] . ')';
			}else{
				$stops[] = 'color-stop(' . $cInfo[1] . ', ' . $cInfo[0] . ')';
			}
		}
		$cssData['background'][] = '-webkit-gradient(' . $gradientType . ', ' . $xStart . ' ' . $yStart . ', ' . $xEnd . ' ' . $yEnd . ', ' . $from . ', ' . $to . (!empty($stops) ? ', ' . implode(', ', $stops) : '') . ')';
		$cssData['background-repeat'][] = 'repeat';
		$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
		$cssData['background-position'][] = '0% 0%';
		//$css .= 'background: ' . $prependBgImages . '-webkit-gradient(' . $gradientType . ', ' . $xStart . ' ' . $yStart . ', ' . $xEnd . ' ' . $yEnd . ', ' . $from . ', ' . $to . (!empty($stops) ? ', ' . implode(', ', $stops) : '') . ')' . $appendBgImages . ';';
	}

	$appendBgImages = '';
	if ($images !== false){
		foreach($images as $iInfo){
			if (!isset($iInfo['css_placement']) || $iInfo['css_placement'] == 'before') continue;

			$cssData['background'][] = 'url(' . $iInfo['image'] . ')';
			$cssData['background-repeat'][] = $iInfo['repeat'];
			$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
			$cssData['background-position'][] = $iInfo['pos_x'] . '% ' . $iInfo['pos_y'] . '%';
			//$appendBgImages .= ', url(' . $iInfo['image'] . ') ' . $iInfo['repeat'] . '' . (isset($iInfo['attachment']) ? ' ' . $iInfo['attachment'] : '') . ' ' . $iInfo['pos_x'] . '% ' . $iInfo['pos_y'] . '%';
		}
	}

	$css = '';
	foreach($cssData as $bgKey => $bgInfo){
		$css .= $bgKey . ': ' . implode(', ', $bgInfo) . ';';
	}
	return $css;
}

function buildBorderRadius($tl = 0, $tr = 0, $br = 0, $bl = 0){
	$css = '';
	if (isIE() === true){
		$css .= 'border-radius: ' . $tl . ' ' . $tr . ' ' . $br . ' ' . $bl . ';' .
			'behavior: url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc);';
	}elseif (isIE9() === true){
		$css .= 'border-radius: ' . $tl . ' ' . $tr . ' ' . $br . ' ' . $bl . ';';
	}elseif (isPresto() === true){
		$css .= 'border-radius: ' . $tl . ' ' . $tr . ' ' . $br . ' ' . $bl . ';';
	}elseif (isMoz() === true){
		$css .= '-moz-border-radius: ' . $tl . ' ' . $tr . ' ' . $br . ' ' . $bl . ';';
	}elseif (isWebkit()){
		$css .= '-webkit-border-radius: ' . $tl . ' ' . $tr . ' ' . $br . ' ' . $bl . ';';
	}
	return $css;
}

function buildBoxShadow($shadows){
	$css = '';

	$allShadows = array();
	foreach($shadows as $sInfo){
		$allShadows[] = (isset($sInfo[5]) && $sInfo[5] === true ? 'inset ' : '') .
			$sInfo[0] . ' ' .
			$sInfo[1] . ' ' .
			$sInfo[2] . ' ' .
			$sInfo[3] . ' ' .
			$sInfo[4];
	}

	if (isIE() === true){
		$css .= 'box-shadow: ' . implode(', ', $allShadows) . ';' .
			'behavior: url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc);';
	}elseif (isIE9() === true){
		$css .= 'box-shadow: ' . implode(', ', $allShadows) . ';';
	}elseif (isPresto() === true){
		$css .= 'box-shadow: ' . implode(', ', $allShadows) . ';';
	}elseif (isMoz() === true){
		$css .= '-moz-box-shadow:' . implode(', ', $allShadows) . ';';
	}elseif (isWebkit()){
		$css .= '-webkit-box-shadow:' . implode(', ', $allShadows) . ';';
	}
	return $css;
}
