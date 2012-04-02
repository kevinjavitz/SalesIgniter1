<?php
/*
$Id: header.php,v 1.19 2002/04/13 16:11:52 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2002 osCommerce

Released under the GNU General Public License
*/

if ($messageStack->size('headerStack') > 0) {
	echo $messageStack->output('headerStack');
}
if (Session::exists('login_id') === true){
	function parseMenuItem($item, $isRoot = false, $isLast = false){
		global $firstAdded;
		$itemTemplate = '';
		if(isset($item['text'])){
			$itemLink = htmlBase::newElement('a')
				->addClass('ui-corner-all');
			if ($item['link'] !== false){
				$itemLink->setHref($item['link']);
			}
			$menuText = '<span class="menu_text">' . $item['text'] . '</span>';
			if (isset($item['children']) && !empty($item['children'])){
				$menuText .= '<span class="ui-icon ui-icon-triangle-1-s"></span>';
			}

			$itemLink->html($menuText);

			$addCls = 'ui-state-default';
			if ($isRoot === true){
				$addCls .= ' root';
			}

			if ($firstAdded === false){
				$addCls .= ' first';
				$firstAdded = true;
			}elseif ($isLast === true){
				$addCls .= ' last';
			}else{
				$addCls .= ' middle';
			}

			$itemTemplate = '<li class="' . $addCls . '">';
			if (isset($item['children']) && !empty($item['children'])){
				$itemTemplate .= $itemLink->draw();
				$itemTemplate .= '<ol>';
				$firstAdded = false;
				foreach($item['children'] as $k => $childItem){
					$itemTemplate .= parseMenuItem($childItem, false, (!isset($item['children'][$k + 1])));
				}
				$itemTemplate .= '</ol>';
			}else{
				$itemTemplate .= $itemLink->draw();
			}

			$itemTemplate .= '</li>';
		}

		return $itemTemplate;
	}

	$firstAdded = false;
?>
<div class="headerBarOne">
	<a href="<?php echo itw_app_link(null, 'index', 'default');?>" class="ui-corner-all">Home</a> |
	<a href="<?php echo itw_app_link(null, 'admin_account', 'default');?>" class="ui-corner-all">My Account</a> |
	<a href="<?php echo itw_app_link('action=addToFavorites', 'index', 'default');?>" id="addToFavorites" class="ui-corner-all">Add To Favorites</a> |
	<a href="<?php echo itw_app_link('action=clearCache', 'index', 'default');?>" id="clearCache" class="ui-corner-all">Clear Cache</a> |
	<a href="<?php echo itw_app_link('action=logoff', 'login', 'default');?>" class="ui-corner-all">Logoff</a> |
	<?php
		$langDrop = htmlBase::newElement('selectbox')
			->setName('language')
			->selectOptionByValue(Session::get('languages_code'))
			->attr('onchange', 'this.form.submit()');
		foreach(sysLanguage::getLanguages() as $lInfo){
			$langDrop->addOption($lInfo['code'], $lInfo['name']);
		}
		echo '<form name="changeLanguage" action="' . itw_app_link(tep_get_all_get_params(array('app', 'appPage', 'action')), $App->getAppName(), $App->getAppPage()) . '" method="get">Language: ' . $langDrop->draw() . '</form>';
	?>
</div>
<div class="headerBarTwo">
	<a href="<?php echo itw_app_link(null, 'index', 'default');?>" class="ui-corner-all"><img src="<?php echo sysConfig::getDirWsCatalog().'images/'. sysConfig::get('STORE_LOGO');?>"/></a>
	<div style="float:right;height:36px;padding-right:10px;padding-top:5px;font-size:inherit;">
		<?php
		$contents = EventManager::notifyWithReturn('AdminHeaderRightAddContent');
		if (!empty($contents)){
			foreach($contents as $content){
				echo '<div style="margin:0 .5em;font-size:inherit;">' .
					$content .
					'</div>';
			}
		}
		?>
	</div>
</div>
<div class="headerBarThree"><?php
	if (sysPermissions::isSimple()){
		$Admin = Doctrine_Core::getTable('Admin')->find((int)Session::get('login_id'));
		$AdminFavs = Doctrine_Core::getTable('AdminFavorites')->find($Admin->admin_favs_id);
		if($AdminFavs){
			$favorites_links = explode(';', $AdminFavs->favorites_links);
			$favorites_names = explode(';', $AdminFavs->favorites_names);
		}else{
			$favorites_links = explode(';', $Admin->favorites_links);
			$favorites_names = explode(';', $Admin->favorites_names);
		}

		echo '<div id="headerMenu" class="ui-navigation-menu ui-widget-content ui-corner-all"><ol>';
		for($i = 0;$i < sizeof($favorites_links); $i++){
			if(!empty($favorites_links[$i])){
				echo parseMenuItem(array(
					'text' => $favorites_names[$i],
					'link' => $favorites_links[$i]
				), true, (!isset($favorites_names[$i + 1])));
			}
		}
		echo '</ol></div>';
	}else{
		$boxes = array(
			'configuration.php',
			'catalog.php',
			'cms.php',
			'modules.php',
			'customers.php',
			'tools.php',
			'marketing.php',
			'data_management.php'
		);

		EventManager::notify('AdminNavMenuAddBox', &$boxes);

		echo '<div id="headerMenu" class="ui-navigation-menu ui-widget-content ui-corner-all"><ol>';
		foreach($boxes as $boxIdx => $boxFileName){
			if (strstr($boxFileName, '/') || strstr($boxFileName, '\\')){
				require($boxFileName);
			}else{
				require(sysConfig::get('DIR_WS_BOXES') . $boxFileName);
			}
			echo parseMenuItem($contents, true, (!isset($boxes[$boxIdx + 1])));
		}
		echo '</ol></div>';
	}
?></div>

	<?php
}
?>