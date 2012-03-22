<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxNavigationMenu extends InfoBoxAbstract
{

	public function __construct() {
		global $App;
		$this->init('navigationMenu');
		$this->firstAdded = false;
		$this->firstAddedol = false;
		$this->buildStylesheetMultiple = true;
		$this->buildJavascriptMultiple = true;
	}

	private function checkCondition($condition){
		global $ShoppingCart, $userAccount;
		switch($condition){
			case 'customer_logged_in':
				if ($userAccount->isLoggedIn() === false){
					return false;
				}
				break;
			case 'customer_not_logged_in':
				if ($userAccount->isLoggedIn() === true){
					return false;
				}
				break;
			case 'shopping_cart_empty':
				if ($ShoppingCart->countContents() > 0){
					return false;
				}
				break;
			case 'shopping_cart_not_empty':
				if ($ShoppingCart->countContents() <= 0){
					return false;
				}
				break;
		}
		return true;
	}

	private function parseMenuItem($item, $isRoot = false, $isLast = false) {
		global $App;
		if (isset($item->condition) && $this->checkCondition($item->condition) === false){
			return '';
		}

		$icon = '';
		if ($item->icon == 'jquery'){
			$icon = '<span class="ui-icon ' . $item->icon_src . '"></span>';
		}
		elseif ($item->icon == 'custom') {
			$htmlCode = $item->icon_src;
			if(sysConfig::getDirWsCatalog() == '/' || (strpos($htmlCode, sysConfig::getDirWsCatalog()) === 0)){
				$imgPath = $htmlCode;
			}else{
				$imgPath = sysConfig::getDirWsCatalog() .$htmlCode;
			}
			$imgPath = str_replace('//','/', $imgPath);
			$icon = '<img src="' . $imgPath . '">';
		}

            	$menuText = '<span class="menu_text">' . $item->{Session::get('languages_id')}->text . '</span>';
		$itemLink = htmlBase::newElement('a')
			->addClass('ui-corner-all');
		if ($item->link !== false){
			if ($item->link->type == 'app'){
				$getParams = null;
				if (stristr($item->link->application, '/')){
					$extInfo = explode('/', $item->link->application);
					$application = $extInfo[1];
					$getParams = 'appExt=' . $extInfo[0];
				}
				else {
					$application = $item->link->application;
				}

				$itemLink->setHref(itw_app_link($getParams, $application, $item->link->page));
			}
			elseif ($item->link->type == 'category'){
				$catPage = 'default';
				$ct = explode('=',$item->link->get_vars);
				$ct2 = explode('_', $ct[1]);
				$catId = $ct2[count($ct2) - 1];
				$QCategory = Doctrine_Manager::getInstance()
					->getCurrentConnection()
					->fetchAssoc("select * from categories c left join categories_description cd on c.categories_id=cd.categories_id where c.categories_id = ".$catId.' and cd.language_id='.Session::get('languages_id'));
				if (sizeof($QCategory) > 0) {
					$catPage = $QCategory[0]['categories_seo_url'];
				}
				$itemLink->setHref(itw_app_link(null, $item->link->application, $catPage));
			}
			elseif ($item->link->type == 'custom') {
				$itemLink->setHref($item->link->url);
			}

			if ($item->link->type != 'none'){
				if ($item->link->target == 'new'){
					$itemLink->attr('target', '_blank');
				}
				elseif ($item->link->target == 'dialog') {
					$itemLink->attr('onclick', 'Javascript:popupWindow(this.href);');
				}
			}
		}
		$itemLink->html($icon . $menuText);

		$addCls = 'ui-state-default';
		if ($isRoot === true){
			$addCls .= ' root';
		}

		if ($this->firstAdded === false){
			$addCls .= ' first';
			$this->firstAdded = true;
		}
		elseif ($isLast === true) {
			$addCls .= ' last';
		}
		else {
			$addCls .= ' middle';
		}

		if (isset($application) && $App->getAppName() == $application && $App->getPageName() == $item->link->page && !isset($_GET['cPath'])){
			$addCls .= ' ui-state-active';
		}elseif (isset($application) && $App->getAppName() == $application && isset($_GET['appPage']) && $_GET['appPage'] == $item->link->page && !isset($_GET['cPath'])){
			$addCls .= ' ui-state-active';
		}elseif (isset($_GET['cPath']) && $item->link->get_vars == 'cPath='. $_GET['cPath']){
			$addCls .= ' ui-state-active';
		}

		$itemTemplate = '<li class="' . $addCls . '">';
		if (isset($item->children) && !empty($item->children)){
			$itemTemplate .= $itemLink->draw() . '<span class="ui-icon ui-icon-triangle-1-e"></span>';
			$addClsol = '';
			if ($this->firstAddedol === false){
				$addClsol = 'firstol';
				$this->firstAddedol = true;
			}
			$itemTemplate .= '<ol class="' . $addClsol . '">';
			foreach($item->children as $k => $childItem){
				$itemTemplate .= $this->parseMenuItem($childItem, false, (!isset($item->children->{$k + 1}) || empty($item->children->{$k + 1})));
			}
			$itemTemplate .= '</ol>';
		}
		else {
			$itemTemplate .= $itemLink->draw();
		}

		$itemTemplate .= '</li>';

		return $itemTemplate;
	}

	public function buildStylesheet() {
		$WidgetProperties = $this->loadLinkedSettings($this->getWidgetProperties());
		if ($WidgetProperties->alwaysShow == 'false' || !isset($WidgetProperties->alwaysShow)){
		$css = '/* Navigation Menu --BEGIN-- */' . "\n" .
			'.ui-navigation-menu { position:relative;background-color:transparent;border: none;line-height:inherit;font-size:inherit; }' . "\n" .
			'.ui-navigation-menu ol { background-color:transparent;list-style:none;padding:0;margin:0;border:none;line-height:inherit;z-index: 100; }' . "\n" .
			'.ui-navigation-menu li { float:left;position:relative;display:block;border:none;background:none;line-height:inherit;text-align:left; }' . "\n" .
			'.ui-navigation-menu li a { width:100%;background-color:transparent;display:inline-block;text-decoration:none;white-space:nowrap; }' . "\n" .
			'.ui-navigation-menu li a span { line-height:1em;background-color:transparent;display:inline-block;vertical-align:baseline; }' . "\n" .
			'.ui-navigation-menu li ol { display:none;position:absolute; }' . "\n" .
			'.ui-navigation-menu li.root { display:inline-block;text-align:center; }' . "\n" .
			'.ui-navigation-menu li.root.first {  }' . "\n" .
			'.ui-navigation-menu li.root.middle { border-left:none; }' . "\n" .
			'.ui-navigation-menu li.root.last { border-left:none; }' . "\n" .
			'.ui-navigation-menu li.root.ui-state-default {  }' . "\n" .
			'.ui-navigation-menu li.root.ui-state-active {  }' . "\n" .
			'.ui-navigation-menu li.root.ui-state-hover {  }' . "\n" .
			'.ui-navigation-menu li ol li.first {  }' . "\n" .
			'.ui-navigation-menu li ol li.middle { border-top:none; }' . "\n" .
			'.ui-navigation-menu li ol li.last { border-top:none; }' . "\n" .
			'.ui-navigation-menu li ol li.ui-state-default {  }' . "\n" .
			'.ui-navigation-menu li ol li.ui-state-active { }' . "\n" .
			'.ui-navigation-menu li ol li.ui-state-hover {  }' . "\n" .
			'.ui-navigation-menu .ui-icon, .ui-navigation-menu img { vertical-align:baseline;display:inline-block; }' . "\n" .
			'.ui-navigation-menu img { margin-right:.3em; }' . "\n" .
			'/* Navigation Menu --END-- */' . "\n";
		}else{
				ob_start();
			?>
		.showAlwaysMenu ol, .showAlwaysMenu ol li {list-style: none;}
		.showAlwaysMenu ol {position: relative; padding: 0; margin: 0;}
		.showAlwaysMenu ol li ol {display: none;}
		.showAlwaysMenu .sub {display: none;}
		.showAlwaysMenu .sub ol {display: block;}

		#<?php echo $WidgetProperties->menuId; ?> {
		list-style: none;
		position: relative;
		padding: 0;
		margin: 0;
		}
		#<?php echo $WidgetProperties->menuId; ?> .sub ol {
		display: block;
		}
		#<?php echo $WidgetProperties->menuId; ?> {
		width: 100%;
		height: 20px;
		position: relative;
		}
		#<?php echo $WidgetProperties->menuId; ?> li {
		float: left;
		margin: 0;
		padding: 0;
		font-weight: bold;
		}
		#<?php echo $WidgetProperties->menuId; ?> li a {
		float: left;
		display: block;
		color: #fff;
		padding-left:18px;
		padding-right:18px;
		padding-top:2px;
		padding-bottom:2px;
		text-decoration: none;
		}
		#<?php echo $WidgetProperties->menuId; ?> li ol li a {
			padding-top:2px;
			padding-bottom:2px;
		}
		#<?php echo $WidgetProperties->menuId; ?> li.mega-hover a, #<?php echo $WidgetProperties->menuId; ?> li.mega-hover a:hover {
		background: #ff0000;
		color: #ffffff;
		}
		#<?php echo $WidgetProperties->menuId; ?> li a:hover {
		background: #ff0000;
		color: #ffffff;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub-container {
		position: absolute;
		}

		#<?php echo $WidgetProperties->menuId; ?> li .sub .row {
		width: 100%;
		overflow: hidden;
		clear: both;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub li {
		list-style: none;
		float: none;
		width: 170px;
		font-size: 1em;
		font-weight: normal;
		text-align:left;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub li.mega-hdr {
		margin: 0 10px 10px 0;
		float: left;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub li.mega-hdr.last {
		margin-right: 0;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub a {
		background: none;
		color: #111;
		display: block;
		float: none;
		font-size: 0.9em;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub li.mega-hdr a.mega-hdr-a {
		margin-bottom: 5px;
		text-transform: uppercase;
		font-weight: bold;
		color: #fff;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub li.mega-hdr a.mega-hdr-a:hover {
		color: #ffffff;
		background:none;
		}
		#<?php echo $WidgetProperties->menuId; ?> .sub li.mega-hdr li a {
		font-weight: normal;
		}
		#<?php echo $WidgetProperties->menuId; ?> .sub li.mega-hdr li a:hover {
		color: #3e3e3e;
		background: none;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub li.mega-hdr ol li a:hover {
			background:#3e3e3e;
		}
		#<?php echo $WidgetProperties->menuId; ?> .sub ol li {
		padding-right: 0;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub-container.non-mega .sub {
		padding: 10px;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub-container.non-mega li {
		padding: 0;
		width: 190px;
		margin: 0;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub-container.non-mega li a {
		padding: 7px 5px 7px 22px;
		}
		#<?php echo $WidgetProperties->menuId; ?> li .sub-container.non-mega li a:hover {
		color: #3e3e3e;
		background: #ffffff;
		}
		#<?php echo $WidgetProperties->menuId; ?> .ui-state-active, #<?php echo $WidgetProperties->menuId; ?> .ui-state-hover, #<?php echo $WidgetProperties->menuId; ?> .ui-state-default{
			border:0;
			background:none;
		}
			<?php
				$cssSource = ob_get_contents();
				ob_end_clean();

				$css = '/* Navigation Menu --BEGIN-- */' . "\n" . $cssSource.	'/* Navigation Menu --END-- */' . "\n";
		}
		return $css;
	}

	public function buildJavascript() {
		$WidgetProperties = $this->loadLinkedSettings($this->getWidgetProperties());

		ob_start();
		if (isset($WidgetProperties->alwaysShow) && $WidgetProperties->alwaysShow == 'true'){
			readfile(sysConfig::getDirFsCatalog().'ext/jQuery/external/megamenu/jquery.hoverIntent.minified.js');
			readfile(sysConfig::getDirFsCatalog().'ext/jQuery/external/megamenu/jquery.dcmegamenu.1.3.3.js');
			?>
		$('#<?php echo $WidgetProperties->menuId;?>').dcMegaMenu({
		rowItems: '3',
		speed: 'fast',
		effect: 'slide'
		});
			<?php
		}else{
		?>
	$('#<?php echo $WidgetProperties->menuId; ?>.ui-navigation-menu').each(function (){
	<?php if ($WidgetProperties->forceFit == 'true'){ ?>
		var Roots = [];
		<?php } ?>
		$(this).find('li').each(function (){
			$(this).addClass('ui-state-default');
			$(this).mouseover(function (){
				$(this).addClass('ui-state-hover');

				if ($(this).children('ol').size() > 0){
					var self = $(this);
					$(this).find('ol:first').each(function (i, el){
						var cssSettings = {
							top: 0,
							left: 0,
							zIndex: self.parent().css('z-index') + 1
						};

						if (self.hasClass('root')){
							cssSettings.top = self.innerHeight();
						}else{
							cssSettings.left = '98%';
						}

						$(this).css(cssSettings).show();

						$(this).find('.ui-icon.ui-icon-triangle-1-s').each(function (){
							$(this).removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e').css({
								position: 'absolute',
								right: 0,
								top: (self.innerHeight() / 2) - ($(this).outerHeight() / 2)
							});
						});
					});
				}
			}).mouseout(function (){
				$(this).removeClass('ui-state-hover');
				if ($(this).children('ol').size() > 0){
					$(this).children('ol').hide();
				}
			});
			if ($(this).find('.ui-icon:first').size() > 0){
				$(this).find('.ui-icon:first').each(function (){
					$(this).css({
						position: 'absolute',
						right: 0,
						top: ($(this).parent().parent().parent().innerHeight() / 2) - ($(this).outerHeight(true) / 2)
					});
				});
			}

	<?php if ($WidgetProperties->forceFit == 'true'){ ?>
			if ($(this).hasClass('root')){
				Roots.push(this);
			}
		<?php } ?>
		});

	<?php if ($WidgetProperties->forceFit == 'true'){ ?>
		var numRoots = Roots.length;
		var totalWidth = $(Roots[0]).parent().parent().width();
		var RootsWidth = 0;
		$.each(Roots, function (i, el){
			RootsWidth += $(this).outerWidth(true);
		});

		var totalSpace = totalWidth - RootsWidth;
		var newPadding = (totalSpace / numRoots);
		$.each(Roots, function (i, el){
			$(this).css({
				width: $(this).innerWidth() + Math.floor(newPadding) + 'px'
			});
		});
		<?php } ?>
	});

		<?php }
 		$javascript = '/* Navigation Menu --BEGIN-- */' . "\n" .
			ob_get_contents();
		'/* Navigation Menu --END-- */' . "\n";
		ob_end_clean();

		return $javascript;
	}

	function loadLinkedSettings($WidgetProperties) {
		if (isset($WidgetProperties->linked_to)){
			$Qsettings = Doctrine_Query::create()
				->select('configuration_value')
				->from('TemplateManagerLayoutsWidgetsConfiguration')
				->where('configuration_key = ?', 'widget_settings')
				->andWhere('widget_id = ?', $WidgetProperties->linked_to)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$LinkedProperties = json_decode($Qsettings[0]['configuration_value']);
			$WidgetProperties->menuSettings = $LinkedProperties->menuSettings;
		}
		return $WidgetProperties;
	}

	public function show() {
		$WidgetProperties = $this->loadLinkedSettings($this->getWidgetProperties());

		$menuItems = '';
		$this->firstAdded = false;
		$this->firstAddedol = false;
		if (isset($WidgetProperties->menuSettings)){
			//echo '<pre>';print_r($boxWidgetProperties['menuSettings']);
			$MenuSettings = array();
			foreach($WidgetProperties->menuSettings as $mInfo){
				if (isset($mInfo->condition) && $this->checkCondition($mInfo->condition) === true){
					$MenuSettings[] = $mInfo;
				}
			}

			foreach($MenuSettings as $k => $mInfo){
				$menuItems .= $this->parseMenuItem($mInfo, true, (!isset($MenuSettings[$k + 1])));
			}
		}
		if($WidgetProperties->alwaysShow == 'false' || !isset($WidgetProperties->alwaysShow)){
			$this->setBoxContent('<div id="' . $WidgetProperties->menuId . '" class="ui-navigation-menu ui-widget ui-corner-all"><ol>' . $menuItems . '</ol></div>');
		}else{
			$this->setBoxContent('<div class="ui-widget ui-corner-all"><ol id="'.$WidgetProperties->menuId.'" class="showAlwaysMenu">' . $menuItems . '</ol></div>');
		}
		return $this->draw();
	}
}

?>
