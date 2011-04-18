<?php
	$infoPages = $appExtension->getExtension('infoPages');
	$pageContents = '';
	if ($appExtension->isInstalled('infoPages')){
		$pageContents .= $infoPages->displayContentBlock(1);
	}
	$pageContent->set('pageContent', $pageContents);
