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
?>
<div style="float:left;" class="adminLogo"><span><a href="<?php echo itw_app_link(null, 'index', 'default');?>" class="ui-corner-all" style="text-decoration:none;padding:.75em;"><img src="imagick_thumb.php?path=rel&height=250&width=250&imgSrc=<?php echo 'images/'.sysConfig::get('STORE_LOGO');?>"/></a></span></div>
<div style="float:left;height:36px;padding-right:10px;padding-top:5px;">
<?php
$contents = EventManager::notifyWithReturn('AdminHeaderLeftAddContent');
if (!empty($contents)){
	foreach($contents as $content){
		echo '<div class="headerBlock" style="float:left;">' .
		$content .
		'</div>';
	}
}
	if(sysPermissions::isSimple()){
		$contents = EventManager::notifyWithReturn('AdminHeaderRightAddContent');
		if (!empty($contents)){
			foreach($contents as $content){
				echo '<div class="headerBlock" style="display:none;">' .
					$content .
					'</div>';
			}
		}
		$Admin = Doctrine_Core::getTable('Admin')->findOneByAdminId((int)Session::get('login_id'));
		$AdminFavs = Doctrine_Core::getTable('AdminFavorites')->find($Admin->admin_favs_id);
		if($AdminFavs){
			$favorites_links = explode(';', $AdminFavs->favorites_links);
			$favorites_names = explode(';', $AdminFavs->favorites_names);
		}else{
			$favorites_links = explode(';', $Admin->favorites_links);
			$favorites_names = explode(';', $Admin->favorites_names);
		}

		for($i = 0;$i < sizeof($favorites_links); $i++){
			if(!empty($favorites_links[$i])){
				$myItem = $favorites_links[$i];

				$htmlButton = htmlBase::newElement('button')
				->setName('myfavbutton'.$i)
				->setHref($myItem)
				->setText($favorites_names[$i]);
				echo $htmlButton->draw().'&nbsp;&nbsp;&nbsp;';

			}
		}
		$htmlButton = htmlBase::newElement('button')
			->setName('logoff')
			->setHref(itw_app_link('action=logoff', 'login', 'default'))
			->setText('Logoff');
		echo $htmlButton->draw().'&nbsp;&nbsp;&nbsp;'.'</div><div style="clear:both;"></div>';
	}else{
?>

<div class="headerBlock" style="float:left;"><span><a href="<?php echo itw_app_link(null, 'index', 'default');?>" class="ui-corner-all" style="text-decoration:none;padding:.75em;"><b>Home</b></a></span></div>
<div class="headerBlock" style="float:left;"><span><a href="<?php echo itw_app_link(null, 'admin_account', 'default');?>" class="ui-corner-all" style="text-decoration:none;padding:.75em;"><b>My Account</b></a></span></div>
<div class="headerBlock" style="float:left;"><span><a id="addToFavorites" href="<?php echo itw_app_link('action=addToFavorites', 'index', 'default');?>" class="ui-corner-all" style="text-decoration:none;padding:.75em;"><b>Add To Favorites</b></a></span></div>
<div class="headerMenuHeadingBlock" style="float:left;"><span><a href="<?php echo itw_app_link('action=clearCache', 'index', 'default');?>" class="ui-corner-all" style="text-decoration:none;padding:.75em;"><b>Clear Cache</b></a></span></div>
<div class="headerBlock" style="float:left;"><span><a href="<?php echo itw_app_link('action=logoff', 'login', 'default');?>" class="ui-corner-all" style="text-decoration:none;padding:.75em;"><b>Logoff</b></a></span></div>
</div>
<div style="float:right;padding-right:10px;padding-top:5px;">
<?php
	$langDrop = htmlBase::newElement('selectbox')
	->setName('language')
	->selectOptionByValue(Session::get('languages_code'))
	->attr('onchange', 'this.form.submit()');
	foreach(sysLanguage::getLanguages() as $lInfo){
		$langDrop->addOption($lInfo['code'], $lInfo['name']);
	}
	echo '<div class="headerBlock" style="float:right;margin:0.5em;"><form name="changeLanguage" action="' . itw_app_link(tep_get_all_get_params(array('app', 'appPage', 'action')), $App->getAppName(), $App->getAppPage()) . '" method="get">Language: ' . $langDrop->draw() . '</form></div>';
$contents = EventManager::notifyWithReturn('AdminHeaderRightAddContent');
if (!empty($contents)){
	foreach($contents as $content){
		echo '<div class="headerBlock" style="float:right;margin:0 .5em;">' .
		$content .
		'</div>';
	}
}
?>
</div><div style="clear:both;"></div>
<div id="headerMenu_wrapper" class="ui-widget"><?php
	$boxes = array(
		'configuration.php',
		'catalog.php',
		'cms.php',
		'modules.php',
		'customers.php',
		'tools.php',
		'rental_membership.php',
		'marketing.php',
		'data_management.php'
	);

	EventManager::notify('AdminNavMenuAddBox', &$boxes);
	
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
?></div>
	<?php
	}

}
?>