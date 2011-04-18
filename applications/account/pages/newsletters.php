<?php
	ob_start();
?>
<div class="main" style="margin-top:1em;"><b><?php echo sysLanguage::get('MY_NEWSLETTERS_TITLE'); ?></b></div>
<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">
 <div class="main"><?php
 	echo tep_draw_checkbox_field('newsletter_general', '1', (($newsletter['customers_newsletter'] == '1') ? true : false), 'onclick="checkBox(\'newsletter_general\')"') . '&nbsp;<b>' . sysLanguage::get('MY_NEWSLETTERS_GENERAL_NEWSLETTER') . '</b>';
 ?></div>
 <p class="main" style="padding:0;margin:0;margin-left:1em;"><?php echo sysLanguage::get('MY_NEWSLETTERS_GENERAL_NEWSLETTER_DESCRIPTION');?></p>
</div>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_NEWSLETTERS');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'account', 'default', 'SSL'))
	->draw() . 
	htmlBase::newElement('button')
	->usePreset('continue')
	->setType('submit')
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'account_newsletter',
		'action' => itw_app_link('action=updateNewsletters', 'account', 'newsletters', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
