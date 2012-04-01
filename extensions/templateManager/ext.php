<?php
class Extension_templateManager extends ExtensionBase
{

	private $widgetPaths = array();

	private $widgetTemplatePaths = array();

	public function __construct() {
		parent::__construct('templateManager');
		if(!class_exists('InfoBoxAbstract')){
			require(__DIR__ . '/widgets/InfoBoxAbstract.php');
		}
	}

	public function init() {
		global $appExtension;
		if ($this->isEnabled() === false) {
			return;
		}
	}

	public function postSessionInit() {
		global $templateDir;
		if (APPLICATION_ENVIRONMENT == 'admin'){
			Session::set('tplDir', 'fallback');
			$templateDir = sysConfig::getDirWsAdmin() . 'template/fallback/';
			sysConfig::set('DIR_WS_TEMPLATES', sysConfig::getDirFsAdmin() . 'templates/fallback/');
		}
		else {
			if (isset($_GET['tplDir']) && is_dir(sysConfig::getDirFsCatalog() . 'templates/' . basename($_GET['tplDir']))){
				Session::set('tplDir', basename($_GET['tplDir']));
			}
			else {
				if (Session::exists('tplDir') === true && is_dir(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir'))){
				}
				else {
					Session::set('tplDir', sysConfig::get('DIR_WS_TEMPLATES_DEFAULT'));
				}
			}

			EventManager::notify('SetTemplateName');

			$tplDir = Session::get('tplDir');

			if ((preg_match('/^[[:alnum:]|_|-]+$/', $tplDir)) && (is_dir(sysConfig::getDirFsCatalog() . 'templates/' . $tplDir))){
				// 'Input Validated' only allow alfanumeric characters and underscores in template name
				sysConfig::set('DIR_WS_TEMPLATES', sysConfig::getDirFsCatalog() . 'templates/' . $tplDir . '/');
			}
			else {
				echo strip_tags($tplDir) . '<br>';
				exit('Illegal template directory!');
			}

			$templateDir = sysConfig::getDirWsCatalog() . 'templates/' . Session::get('tplDir') . '/';
			$this->loadWidgets(Session::get('tplDir'));
		}
	}

	public function loadWidgets($templateDir = false){
		global $appExtension;
		$dir = new DirectoryIterator(__DIR__ . '/widgets/');
		foreach($dir as $dInfo){
			if ($dInfo->isDot() || $dInfo->isFile()){
				continue;
			}
			$this->widgetPaths[$dInfo->getBasename()] = $dInfo->getPathname();
		}

		$dir = new DirectoryIterator(__DIR__ . '/widgetTemplates/');
		foreach($dir as $dInfo){
			if ($dInfo->isDot() || $dInfo->isDir()){
				continue;
			}
			$this->widgetTemplatePaths[$dInfo->getBasename('.tpl')] = $dInfo->getPathname();
		}

		$dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
		foreach($dir as $dInfo){
			if (
				$dInfo->isDot() ||
				$dInfo->isFile() ||
				$appExtension->isInstalled($dInfo->getBasename()) === false
			){
				continue;
			}

			if (is_dir($dInfo->getPathName() . '/catalog/ext_app/' . $this->getExtensionKey() . '/widgets')){
				$subDir = new DirectoryIterator($dInfo->getPathName() . '/catalog/ext_app/' . $this->getExtensionKey() . '/widgets/');
				foreach($subDir as $sdInfo){
					if ($sdInfo->isDot() || $sdInfo->isFile()){
						continue;
					}
					$this->widgetPaths[$sdInfo->getBasename()] = $sdInfo->getPathname();
				}
			}

			if (is_dir($dInfo->getPathName() . '/catalog/ext_app/' . $this->getExtensionKey() . '/widgetTemplates')){
				$dir = new DirectoryIterator($dInfo->getPathName() . '/catalog/ext_app/' . $this->getExtensionKey() . '/widgetTemplates/');
				foreach($dir as $sdInfo){
					if ($sdInfo->isDot() || $sdInfo->isDir()){
						continue;
					}
					$this->widgetTemplatePaths[$sdInfo->getBasename('.tpl')] = $sdInfo->getPathname();
				}
			}
		}

		if ($templateDir !== false && is_dir(sysConfig::getDirFsCatalog() . 'templates/' . $templateDir)){
			if (is_dir(sysConfig::getDirFsCatalog() . 'templates/' . $templateDir . '/widgets/')){
				$dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'templates/' . $templateDir . '/widgets/');
				foreach($dir as $dInfo){
					if ($dInfo->isDot() || $dInfo->isFile()){
						continue;
					}
					$this->widgetPaths[$dInfo->getBasename()] = $dInfo->getPathname();
				}
			}

			if (is_dir(sysConfig::getDirFsCatalog() . 'templates/' . $templateDir . '/widgetTemplates/')){
				$dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'templates/' . $templateDir . '/widgetTemplates/');
				foreach($dir as $dInfo){
					if ($dInfo->isDot() || $dInfo->isDir()){
						continue;
					}
					$this->widgetTemplatePaths[$dInfo->getBasename('.tpl')] = $dInfo->getPathname();
				}
			}
		}
		ksort($this->widgetPaths);
		ksort($this->widgetTemplatePaths);
	}

	public function getWidgetPaths(){
		return $this->widgetPaths;
	}

	public function getWidgetTemplatePaths(){
		return $this->widgetTemplatePaths;
	}

	private function loadWidget($code){
		if (isset($this->widgetPaths[$code])){
			if (class_exists('InfoBox' . ucfirst($code)) === false){
				require($this->widgetPaths[$code] . '/infobox.php');
			}
			return true;
		}
		return false;
	}

	public function getWidget($code){
		if ($this->loadWidget($code) !== false){
			$className = 'InfoBox' . ucfirst($code);
			return new $className;
		}
		return false;
	}

	public function buildLayout(&$Construct, $layoutId) {
		$Profile = SES_Profiler::newProfile('templateLoad', true);
		$Containers = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from template_manager_layouts_containers where layout_id = "' . $layoutId . '" and parent_id = 0 order by sort_order');
		if (sizeof($Containers) > 0){
			foreach($Containers as $cInfo){
				$MainEl = htmlBase::newElement('div')
					->addClass('container');

				if ($cInfo['link_id'] > 0){
					$QlinkId = Doctrine_Manager::getInstance()
						->getCurrentConnection()
						->fetchAssoc('select container_id from template_manager_container_links where link_id = "' . $cInfo['link_id'] . '"');
					$containerId = $QlinkId[0]['container_id'];
				}else{
					$containerId = $cInfo['container_id'];
				}

				if (($cfgInfo = $this->getConfigInfo('container', $containerId)) !== false){
					$this->addInputs($MainEl, $cfgInfo);
				}

				if (($cssInfo = $this->getStyleInfo('container', $containerId)) !== false){
					$this->addStyles($MainEl, $cssInfo);
				}

				if (($Columns = $this->getContainerColumns($containerId)) !== false){
					$this->processContainerColumns($MainEl, $Columns);
				}

				if (($Children = $this->getContainerChildren($containerId)) !== false){
					$this->processContainerChildren($MainEl, $Children);
				}
				$Construct->append($MainEl);
			}
		}
		$Profile->end();
	}

	public function getConfigInfo($type, $id) {
		if ($type == 'layout'){
			$idCol = 'layout_id';
			$table = 'template_manager_layouts_configuration';
		}
		elseif ($type == 'container'){
			$idCol = 'container_id';
			$table = 'template_manager_layouts_containers_configuration';
		}
		elseif ($type == 'column') {
			$idCol = 'column_id';
			$table = 'template_manager_layouts_columns_configuration';
		}
		elseif ($type == 'widget') {
			$idCol = 'widget_id';
			$table = 'template_manager_layouts_widgets_configuration';
		}

		$cfgInfo = false;
		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from ' . $table . ' where ' . $idCol . ' = "' . $id . '"');
		if (sizeof($ResultSet) > 0){
			$cfgInfo = array();
			foreach($ResultSet as $Result){
				$cfgInfo[] = $Result;
			}
		}
		return $cfgInfo;
	}

	public function getStyleInfo($type, $id) {
		if ($type == 'layout'){
			$idCol = 'layout_id';
			$table = 'template_manager_layouts_styles';
		}
		elseif ($type == 'container'){
			$idCol = 'container_id';
			$table = 'template_manager_layouts_containers_styles';
		}
		elseif ($type == 'column') {
			$idCol = 'column_id';
			$table = 'template_manager_layouts_columns_styles';
		}
		elseif ($type == 'widget') {
			$idCol = 'widget_id';
			$table = 'template_manager_layouts_widgets_styles';
		}

		$cssInfo = false;
		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from ' . $table . ' where ' . $idCol . ' = "' . $id . '"');
		if (sizeof($ResultSet) > 0){
			$cssInfo = array();
			foreach($ResultSet as $Result){
				$cssInfo[] = $Result;
			}
		}
		return $cssInfo;
	}

	public function getContainerColumns($id) {
		$Columns = false;
		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from template_manager_layouts_columns where container_id = "' . $id . '" and parent_id = 0 order by sort_order');
		if (sizeof($ResultSet) > 0){
			$Columns = array();
			foreach($ResultSet as $Result){
				$Columns[] = $Result;
			}
		}
		return $Columns;
	}

	public  function getColumnChildren($id){
		$Columns = false;
		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from template_manager_layouts_columns where parent_id = "' . $id . '" order by sort_order');
		if (sizeof($ResultSet) > 0){
			$Columns = array();
			foreach($ResultSet as $Result){
				$Columns[] = $Result;
			}
		}
		return $Columns;
	}

	public function getContainerChildren($id) {
		$Children = false;
		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from template_manager_layouts_containers where parent_id = "' . $id . '" order by sort_order');
		if (sizeof($ResultSet) > 0){
			$Children = array();
			foreach($ResultSet as $Result){
				$Children[] = $Result;
			}
		}
		return $Children;
	}

	public function getColumnWidgets($id) {
		$Widgets = false;
		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from template_manager_layouts_widgets where column_id = "' . $id . '" order by sort_order');
		if (sizeof($ResultSet) > 0){
			$Widgets = array();
			foreach($ResultSet as $Result){
				$Widgets[] = $Result;
			}
		}
		return $Widgets;
	}

	private function addStyles($El, $Styles) {
		if ($El->hasAttr('id') && $El->attr('id') != ''){
			return;
		}

		$css = array();
		foreach($Styles as $sInfo){
			if (substr($sInfo['definition_value'], 0, 1) == '{' || substr($sInfo['definition_value'], 0, 1) == '['){
				$css[$sInfo['definition_key']] = json_decode($sInfo['definition_value']);
			}
			else {
				$css[$sInfo['definition_key']] = $sInfo['definition_value'];
			}
			$El->css($sInfo['definition_key'], $css[$sInfo['definition_key']]);
		}
	}

	private function addInputs($El, $Config) {
		foreach($Config as $cInfo){
			if ($cInfo['configuration_key'] != 'id'){
				continue;
			}

			$El->attr('id', $cInfo['configuration_value']);
		}
	}

	private function processContainerChildren(&$El, $ChildArr) {
		foreach($ChildArr as $cInfo){
			$NewEl = htmlBase::newElement('div')
				->addClass('container');

			if (($cfgInfo = $this->getConfigInfo('container', $cInfo['container_id'])) !== false){
				$this->addInputs($NewEl, $cfgInfo);
			}

			if (($cssInfo = $this->getStyleInfo('container', $cInfo['container_id'])) !== false){
				$this->addStyles($NewEl, $cssInfo);
			}

			$El->append($NewEl);

			if (($Columns = $this->getContainerColumns($cInfo['container_id'])) !== false){
				$this->processContainerColumns($NewEl, $Columns);
			}

			if (($Children = $this->getContainerChildren($cInfo['container_id'])) !== false){
				$this->processContainerChildren($NewEl, $Children);
			}
		}
	}

	private function processContainerColumns(&$Container, $ColArr) {
		foreach($ColArr as $cInfo){
			$ColEl = htmlBase::newElement('div')
				->addClass('column');

			if (($cfgInfo = $this->getConfigInfo('column', $cInfo['column_id'])) !== false){
				$this->addInputs($ColEl, $cfgInfo);
			}

			if (($cssInfo = $this->getStyleInfo('column', $cInfo['column_id'])) !== false){
				$this->addStyles($ColEl, $cssInfo);
			}

			if (($Columns = $this->getColumnChildren($cInfo['column_id'])) !== false){
				$this->processContainerColumns($ColEl, $Columns);
			}

			$WidgetHtml = '';
			if (($Widgets = $this->getColumnWidgets($cInfo['column_id'])) !== false){
				foreach($Widgets as $wInfo){
					$WidgetEl = htmlBase::newElement('div')
						->addClass('widget')
						->setId('widget_' . $wInfo['widget_id']);

					$WidgetSettings = '';
					if (($cfgInfo = $this->getConfigInfo('widget', $wInfo['widget_id'])) !== false){
						$WidgetInputs = array();
						foreach($cfgInfo as $cfInfo){
							if ($cfInfo['configuration_key'] == 'widget_settings'){
								$WidgetSettings = json_decode(utf8_encode($cfInfo['configuration_value']));
							}else{
								$WidgetInputs[] = $cfInfo;
							}
						}

						if (!empty($WidgetInputs)){
							//$this->addInputs($WidgetEl, $WidgetInputs);
						}
					}

					if (($widgetCssInfo = $this->getStyleInfo('widget', $wInfo['widget_id'])) !== false){
						$this->addStyles($WidgetEl, $widgetCssInfo);
					}

					$WidgetClass = $this->getWidget($wInfo['identifier']);
					if ($WidgetClass !== false){
						if (isset($WidgetSettings->template_file) && !empty($WidgetSettings->template_file)){
							$WidgetClass->setBoxTemplateFile($WidgetSettings->template_file);
						}
						if (isset($WidgetSettings->id) && !empty($WidgetSettings->id)){
							$WidgetClass->setBoxId($WidgetSettings->id);
						}
						if (isset($WidgetSettings->widget_title) && !empty($WidgetSettings->widget_title)){
							$WidgetClass->setBoxHeading($WidgetSettings->widget_title->{Session::get('languages_id')});
						}

						$WidgetClass->setWidgetProperties($WidgetSettings);

						$WidgetEl->html($WidgetClass->show());
						$WidgetHtml .= $WidgetEl->draw();
					}
				}
			}
			$ColEl->html($WidgetHtml);

			$Container->append($ColEl);
		}
	}
}

/* @TODO: Find a better place for this stuff */
global $jqueryThemeDir, $jqueryThemeBG, $jqueryThemeIcons, $jqueryThemeImages, $templateDir;

$jqueryThemeDir = sysConfig::getDirWsCatalog() . 'ext/jQuery/themes/smoothness/';
$jqueryThemeBG = sysConfig::getDirWsCatalog() . 'ext/jQuery/themes/smoothness/';
$jqueryThemeIcons = sysConfig::getDirWsCatalog() . 'ext/jQuery/themes/icons';
$jqueryThemeImages = sysConfig::getDirWsCatalog() . 'ext/jQuery/themes/smoothness/images';

function jqueryIconsPath($color) {
	global $jqueryThemeIcons;
	return $jqueryThemeIcons . '/ui-icons_' . $color . '_256x240.png';
}

function matchEngineVersion($engine, $v) {
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$matched = false;
	$vInfo = array();
	preg_match_all('/' . $engine . '\/(.*)\)/', $u_agent, $vInfo);
	if ((int)$vInfo[1][0] == $v){
		$matched = true;
	}
	return $matched;
}

function matchUserAgent($toMatch) {
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = false;
	if (preg_match('/' . $toMatch . '/i', $u_agent)){
		$ub = true;
	}
	return $ub;
}

function isIE() {
	return matchUserAgent('MSIE');
}

/* Trident/3.0 */
function isIE7() {
	return (isIE() ? matchEngineVersion('Trident', 3) : false);
}

/* Trident/4.0 */
function isIE8() {
	return (isIE() ? matchEngineVersion('Trident', 4) : false);
}

/* Trident/5.0 */
function isIE9() {
	return (isIE() ? matchEngineVersion('Trident', 5) : false);
}

/* Trident/6.0 */
function isIE10() {
	return (isIE() ? matchEngineVersion('Trident', 6) : false);
}

function isMoz() {
	return (matchUserAgent('Mozilla') && !matchUserAgent('AppleWebKit'));
}

function isChrome() {
	return (isWebkit() ? matchUserAgent('Chrome') : false);
}

function isSafari() {
	return (isWebkit() ? (!matchUserAgent('Chrome') && matchUserAgent('Safari')) : false);
}

function isWebkit() {
	return matchUserAgent('AppleWebKit');
}

function isPresto() {
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = false;
	if (matchUserAgent('Presto')){
		$vInfo = array();
		preg_match_all('/Presto\/(.*) Version/', $u_agent, $vInfo);
		if ($vInfo[1][0] > 2.07){
			$ub = true;
		}
	}
	return $ub;
}

function buildBackgroundAlpha($r, $g, $b, $a, &$styleObj = false) {
	$cssData = array();
	if (isIE8() === true){
		//$cssData['-pie-background'] = 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ')';
		//$cssData['behavior'] = 'url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc)';
		$cssData['background-color'] = 'rgb(' . $r . ', ' . $g . ', ' . $b . ')';
	}
	else {
		$cssData['background-color'] = 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ')';
	}

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

function buildSimpleGradient($start, $end, &$styleObj = false) {
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
				if (isset($iInfo['css_placement']) && $iInfo['css_placement'] == 'after'){
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
				if (isset($iInfo['css_placement']) && $iInfo['css_placement'] == 'before'){
					continue;
				}

				$cssData['-pie-background'][] = 'url(' . $iInfo['image'] . ') ' .
					$iInfo['repeat'] . ' ' .
					(isset($iInfo['attachment']) ? $iInfo['attachment'] . ' ' : 'scroll ') .
					$iInfo['pos_x'] . ' ' . $iInfo['pos_y'];
			}
		}
		//$cssData['behavior'][] = 'url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc)';
	}
	elseif (isIE8() === true) {
		$stops = array();
		foreach($colorStops as $cInfo){
			$stops[] = array(
				'pos'     => ($cInfo[1] * 100),
				'color'   => $cInfo[0],
				'opacity' => 100
			);
		}

		$cssData['background-image'][] = 'url(extensions/templateManager/catalog/globalFiles/IE8_gradient.php?width=10&height=100&angle=' . $deg . '&colorStops=' . urlencode(json_encode($stops)) . ')';
		$cssData['-jquery'][] = 'if ($(this).height() > 10){ $(this).css(\'background-image\', \'url(extensions/templateManager/catalog/globalFiles/IE8_gradient.php?width=\' + $(this).outerWidth(true) + \'&height=\' + $(this).outerHeight(true) + \'&angle=' . $deg . '&colorStops=' . urlencode(json_encode($stops)) . ')\'); }';
		$cssData['background-repeat'][] = 'repeat-x';
		$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
		$cssData['background-position'][] = '0% 0%';
	}
	elseif (isSafari() === true) {
		if ($images !== false){
			foreach($images as $iInfo){
				if (isset($iInfo['css_placement']) && $iInfo['css_placement'] == 'after'){
					continue;
				}

				$cssData['background'][] = 'url(' . $iInfo['image'] . ')';
				$cssData['background-repeat'][] = $iInfo['repeat'];
				$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
				$cssData['background-position'][] = $iInfo['pos_x'] . ' ' . $iInfo['pos_y'];
			}
		}

		$stops = array();
		foreach($colorStops as $cInfo){
			$stops[] = 'color-stop(' . $cInfo[1] . ', ' . $cInfo[0] . ')';
		}

		$angle = $deg . 'deg';
		switch($deg){
			case 0:
				$angle = 'left';
				break;
			case 45:
				$angle = 'bottom left';
				break;
			case 90:
				$angle = 'bottom';
				break;
			case 135:
				$angle = 'bottom right';
				break;
			case 190:
				$angle = 'right';
				break;
			case 235:
				$angle = 'top right';
				break;
			case 270:
				$angle = 'left top, left bottom';
				break;
			case 315:
				$angle = 'top left';
				break;
			case 360:
				$angle = 'left';
				break;
		}

		$cssData['background'][] = '-webkit-gradient(linear, ' . $angle . ', ' . implode(', ', $stops) . ')';
		$cssData['background-repeat'][] = 'no-repeat';
		$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
		$cssData['background-position'][] = '0% 0%';

		if ($images !== false){
			foreach($images as $iInfo){
				if (!isset($iInfo['css_placement']) || $iInfo['css_placement'] == 'before'){
					continue;
				}

				$cssData['background'][] = 'url(' . $iInfo['image'] . ')';
				$cssData['background-repeat'][] = $iInfo['repeat'];
				$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
				$cssData['background-position'][] = $iInfo['pos_x'].'%' . ' ' . $iInfo['pos_y'].'%';
			}
		}
	}
	else {
		if ($images !== false){
			foreach($images as $iInfo){
				if (isset($iInfo['css_placement']) && $iInfo['css_placement'] == 'after'){
					continue;
				}

				$cssData['background'][] = 'url(' . $iInfo['image'] . ')';
				$cssData['background-repeat'][] = $iInfo['repeat'];
				$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
				$cssData['background-position'][] = $iInfo['pos_x'].'%' . ' ' . $iInfo['pos_y'].'%';
			}
		}

		if (isIE9() === true){
			$stops = array();
			foreach($colorStops as $cInfo){
				$stops[] = array(
					'pos'     => ($cInfo[1] * 100),
					'color'   => $cInfo[0],
					'opacity' => 100
				);
			}
			$backgroundStr = 'data:image/svg+xml;base64,' . base64_encode(buildSvgGradientContent($deg, $stops));
			$cssData['background'][] = 'url(' . $backgroundStr . ')';
			//$cssData['background'][] = 'url(/extensions/templateManager/catalog/globalFiles/IE9_gradient.php?angle=' . $deg . '&colorStops=' . urlencode(json_encode($stops)) . ')';
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
				case 0:
					$angle = 'left';
					break;
				case 45:
					$angle = 'bottom left';
					break;
				case 90:
					$angle = 'bottom';
					break;
				case 135:
					$angle = 'bottom right';
					break;
				case 190:
					$angle = 'right';
					break;
				case 235:
					$angle = 'top right';
					break;
				case 270:
					$angle = 'top';
					break;
				case 315:
					$angle = 'top left';
					break;
				case 360:
					$angle = 'left';
					break;
			}

			$cssData['background'][] = $prefix . 'linear-gradient(' . $angle . ', ' . implode(', ', $stops) . ')';
			$cssData['background-repeat'][] = 'no-repeat';
			$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
			$cssData['background-position'][] = '0% 0%';
		}

		if ($images !== false){
			foreach($images as $iInfo){
				if (!isset($iInfo['css_placement']) || $iInfo['css_placement'] == 'before'){
					continue;
				}

				$cssData['background'][] = 'url(' . $iInfo['image'] . ')';
				$cssData['background-repeat'][] = $iInfo['repeat'];
				$cssData['background-attachment'][] = (isset($iInfo['attachment']) ? $iInfo['attachment'] : 'scroll');
				$cssData['background-position'][] = $iInfo['pos_x'].'%' . ' ' . $iInfo['pos_y'].'%';
			}
		}
	}
	$css = '';
	foreach($cssData as $bgKey => $bgInfo){
		if ($styleObj !== false){
			$styleObj->addRule($bgKey, implode(', ', $bgInfo));
		}
		else {
			$css .= $bgKey . ': ' . implode(', ', $bgInfo) . ';';
		}
	}
	return $css;
}

function buildBorderRadius($tl = 0, $tr = 0, $br = 0, $bl = 0, &$styleObj = false) {
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
		//$cssData['behavior'] = 'url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc)';
	}

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

function buildBoxShadow($shadows, &$styleObj = false) {
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

function buildTextShadow($shadows, &$styleObj = false) {
	$cssData = array();

	$allShadows = array();
	foreach($shadows as $sInfo){
		$allShadows[] = $sInfo[0] . 'px ' .
			$sInfo[1] . 'px ' .
			$sInfo[2] . 'px ' .
			$sInfo[3];
	}

	$cssData['text-shadow'] = implode(', ', $allShadows);

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

function gradientDegreesToArray($deg){
	$xStart = 0;
	$yStart = 0;
	$xEnd = 0;
	$yEnd = 0;
	switch($deg){
		case '0':
			$xStart = 0;
			$yStart = 0;
			$xEnd = 100;
			$yEnd = 0;
			break;
		case '45':
			$xStart = 0;
			$yStart = 100;
			$xEnd = 100;
			$yEnd = 0;
			break;
		case '90':
			$xStart = 0;
			$yStart = 100;
			$xEnd = 0;
			$yEnd = 0;
			break;
		case '135':
			$xStart = 100;
			$yStart = 100;
			$xEnd = 0;
			$yEnd = 0;
			break;
		case '180':
			$xStart = 100;
			$yStart = 0;
			$xEnd = 0;
			$yEnd = 0;
			break;
		case '225':
			$xStart = 100;
			$yStart = 0;
			$xEnd = 0;
			$yEnd = 100;
			break;
		case '270':
			$xStart = 100;
			$yStart = 0;
			$xEnd = 100;
			$yEnd = 100;
			break;
		case '315':
			$xStart = 0;
			$yStart = 0;
			$xEnd = 100;
			$yEnd = 100;
			break;
		case '360':
			$xStart = 0;
			$yStart = 0;
			$xEnd = 100;
			$yEnd = 0;
			break;
	}

	return array(
		'x_start' => $xStart,
		'y_start' => $yStart,
		'x_end' => $xEnd,
		'y_end' => $yEnd
	);
}

function colorStopArrayToSvg($colorStops){
	$return = '';
	foreach($colorStops as $sInfo){
		$color = $sInfo['color'];
		$opacity = $sInfo['opacity'];
		if (substr($color, 0, 4) == 'rgba'){
			$matches = array();
			preg_match_all('/rgba\((.*),[\s?](.*),[\s?](.*),[\s?](.*)\)/', $color, $matches);
			$color = 'rgb(' . $matches[1][0] . ', ' . $matches[2][0] . ', ' . $matches[3][0] . ')';
			$opacity = $matches[4][0];
		}

		$return .= '<stop offset="' . $sInfo['pos'] . '%" stop-color="' . $color . '" stop-opacity="' . $opacity . '" />';
	}
	return $return;
}

function buildSvgGradientContent($deg, $colorStops) {
	$xy = gradientDegreesToArray($deg);
	$random = rand(500, 1000);
	$svgStr = '<svg ' .
		'xmlns="http://www.w3.org/2000/svg" ' .
		'width="100%" ' .
		'height="100%" ' .
		'viewBox="0 0 1 1" ' .
		'preserveAspectRatio="none"' .
		'>' .
		'<linearGradient ' .
		'id="gradient' . $random . '" ' .
		'gradientUnits="userSpaceOnUse" ' .
		'x1="' . $xy['x_start'] . '%" ' .
		'y1="' . $xy['y_start'] . '%" ' .
		'x2="' . $xy['x_end'] . '%" ' .
		'y2="' . $xy['y_end'] . '%"' .
		'>' .
		colorStopArrayToSvg($colorStops) .
		'</linearGradient>' .
		'<rect ' .
		'x="0" ' .
		'y="0" ' .
		'width="100%" ' .
		'height="100%" ' .
		'fill="url(#gradient' . $random . ')"' .
		'/>' .
		'</svg>';
	return $svgStr;
}

function buildSvgTextGradientContent($deg, $colorStops, $text, $fontSize) {
	$xy = gradientDegreesToArray($deg);
	$random = rand(500, 1000);
	$svgStr = '<svg ' .
			'xmlns="http://www.w3.org/2000/svg" ' .
			'width="100%" ' .
			'height="100%" ' .
			'viewBox="0 0 1 1" ' .
			'preserveAspectRatio="none"' .
		'>' .
		'<linearGradient ' .
			'id="text_gradient' . $random . '" ' .
			'gradientUnits="userSpaceOnUse" ' .
			'x1="' . $xy['x_start'] . '%" ' .
			'y1="' . $xy['y_start'] . '%" ' .
			'x2="' . $xy['x_end'] . '%" ' .
			'y2="' . $xy['y_end'] . '%"' .
		'>' .
		colorStopArrayToSvg($colorStops) .
		'</linearGradient>' .
		'<g transform="translate(50,150)">' .
			'<text ' .
				'id="horizontalText' . $random . '" ' .
				'x="0" ' .
				'y="0" ' .
				'fill="url(#text_gradient' . $random . ')" ' .
				'font-size="' . $fontSize . '"' .
			'>' .
			$_GET['string'] .
			'</text>' .
		'</g>' .
		'</svg>';
	return $svgStr;
}
