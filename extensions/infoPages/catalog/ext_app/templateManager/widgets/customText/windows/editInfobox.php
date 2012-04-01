<?php
$selectedPage = (isset($WidgetSettings->selected_page) ? $WidgetSettings->selected_page : '');
$cssId = (isset($WidgetSettings->id) ? $WidgetSettings->id : '');
$customText = (isset($WidgetSettings->custom_text) ? $WidgetSettings->custom_text : '');

$Qpages = Doctrine_Query::create()
	->select('p.*, pd.pages_title')
	->from('Pages p')
	->leftJoin('p.PagesDescription pd')
	->where('pd.language_id = ?', Session::get('languages_id'))
	->andWhere('p.page_type = ?', 'block')
	->orderBy('p.sort_order, pd.pages_title')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$editCms = htmlBase::newElement('a')
	->html('Edit This page')
	->attr('target', '_blank')
	->attr('id', 'edit_cms_page');

$boxId = htmlBase::newElement('input')
	->setName('id')
	->val($cssId);

$InfopageName = htmlBase::newElement('selectbox')
	->setName('selected_page')
	->attr('id', 'page_name')
	->addOption('', 'Please Select')
	->selectOptionByValue($selectedPage);

foreach($Qpages as $iPage){
	$attr = array(
		array(
			'name' => 'go',
			'value' => itw_app_link('appExt=infoPages&pID=' . $iPage['pages_id'], 'manage', 'newContentBlock')
		)
	);
	$InfopageName->addOptionWithAttributes($iPage['pages_id'], $iPage['page_key'], $attr);
}

$javascript = '<script>' .
	'$(document).ready(function (){' .
		'$(\'#page_name\').change(function(){' .
			'$(\'#page_name option:selected\').each(function () {' .
		        '$(\'#edit_cms_page\').attr(\'href\', $(this).attr(\'go\'));' .
			'});' .
		'});' .
		'$(\'#page_name\').trigger(\'change\');' .
	'})' .
'</script>';

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => $javascript . '<b>Custom Page Block Widget Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'ID For Css:'),
		array('text' => $boxId->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Page Key:'),
		array('text' => $InfopageName->draw() . '&nbsp;&nbsp;' . $editCms->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'align' => 'center', 'text' => '<b> -- OR -- </b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('valign' => 'top', 'text' => 'Enter Text:'),
		array('text' => '<textarea name="custom_text" style="width:300px;height:200px;">' . $customText . '</textarea>')
	)
));
