<?php

$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . ($Admin->admin_id > 0 ? sysLanguage::get('TEXT_INFO_HEADING_EDIT') : sysLanguage::get('TEXT_INFO_HEADING_NEW')) . '</b>');
$infoBox->setButtonBarLocation('top');

$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($saveButton)->addButton($cancelButton);

/*Favorites Links*/
if (isset($_GET['aID'])){
	$AdminFavorites = Doctrine_Core::getTable('AdminFavorites')->find((int) $_GET['aID']);
}
$favorites_links = explode(';', $AdminFavorites->favorites_links);
$favorites_names = explode(';', $AdminFavorites->favorites_names);

$favoritesTable = htmlBase::newElement('div')
	->css(array(
		'width' => '100%'
	))
	->addClass('ui-widget ui-widget-content favoritesLinks');

/*$fheaderCols = array(
array('align' => 'center', 'valign' => 'bottom', 'text' => 'Link Name'),
array('align' => 'center', 'valign' => 'bottom', 'text' => 'Action'),
);

$favoritesTable->addHeaderRow(array(
'addCls' => 'ui-widget-header',
'columns' => $fheaderCols
));*/
$favoritesList = htmlBase::newElement('list')
	->css(array(
		'list-style' => 'none',
		'margin' => '0',
		'padding' => '0'
	))
	->addClass('favoritesLinks');
for($i = 0;$i < sizeof($favorites_links); $i++){
	if(!empty($favorites_links[$i])){
		$myItem = '<a href="' . sysConfig::get('DIR_WS_ADMIN') . $favorites_links[$i] . '">' . $favorites_names[$i] . '</a>'. '<a class="remoFav" href="'. itw_app_link('action=removeFromFavoritesSet&url='. $favorites_links[$i],'index','default') .'"><span class="ui-icon ui-icon-closethick"></span></a><input type="hidden" name="fav_links[]" value="'.sysConfig::get('DIR_WS_ADMIN') . $favorites_links[$i].'"/><input type="hidden" name="fav_names[]" value="'.$favorites_names[$i].'"/>';
		$favoritesList->addItem('',$myItem);

		/*$favoritesTable->addBodyRow(array(
  'columns' => $fbodyRowCols
  ));*/
	}
}
$favoritesTable->append($favoritesList);
$infoBox->addContentRow($favoritesTable);

/*End Favorites Links*/

EventManager::attachActionResponse($infoBox->draw(), 'html');
