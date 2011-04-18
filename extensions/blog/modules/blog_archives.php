<?php
	$boxTemplate = new Template('module.tpl', 'modules');

	$menuContainer = htmlBase::newElement('div')
	->setId('barchives');

	$ulElement = htmlBase::newElement('list');

	$blog = $appExtension->getExtension('blog');
	foreach($blog->getArchives() as $cat){
		$time = mktime(0,0,0,$cat['month'],10);
		
		$appPage = date('F', $time) . '-' . $cat['year'];
		
		$link = itw_app_link('appExt=blog', 'show_archive', $appPage);
		
		$childLinkEl = htmlBase::newElement('a')
		->addClass('ui-widget ui-widget-content ui-corner-all')
		->css('border-color', 'transparent')
		->html($appPage)
		->setHref($link);
		
		$liElement = htmlBase::newElement('li')
		->append($childLinkEl);

		$ulElement->addItemObj($liElement);
	}

	$menuContainer->append($ulElement);

	$boxTemplate->setVars(array(
		'boxHeading' => 'Blog Archives',
		'boxContent' => $menuContainer->draw()
	));

	echo $boxTemplate->parse();
?>