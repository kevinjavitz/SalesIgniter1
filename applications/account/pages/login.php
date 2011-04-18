<?php
	ob_start();
?>
<div id="tabs">
	<ul>
		<li><a href="#tabReturning"><span><?php echo sysLanguage::get('HEADING_RETURNING_CUSTOMER');?></span></a></li>
		<li><a href="#tabNewAccount"><span><?php echo sysLanguage::get('HEADING_NEW_CUSTOMER');?></span></a></li>
		<?php if (sysConfig::get('ALLOW_RENTALS') == 'true'){?>
		<li><a href="#tabNewRentAccount"><span><?php echo sysLanguage::get('HEADING_NEW_RENTAL_CUSTOMER');?></span></a></li>
		<?php }?>
	</ul>
	<div id="tabReturning"><?php include(sysConfig::getDirFsCatalog() . 'applications/account/pages_tabs/login/returning.php');?></div>
	<div id="tabNewAccount"><?php include(sysConfig::getDirFsCatalog() . 'applications/account/pages_tabs/login/customer.php');?></div>
	<?php if (sysConfig::get('ALLOW_RENTALS') == 'true'){?>
	<div id="tabNewRentAccount"><?php include(sysConfig::getDirFsCatalog() . 'applications/account/pages_tabs/login/rental.php');?></div>
	<?php }?>
</div>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_LOGIN');
	
	//$pageButtons = htmlBase::newElement('button')
	//->usePreset('continue')
	//->setType('submit')
	//->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'login',
		'action' => itw_app_link('action=processLogin', 'account', 'login', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	//$pageContent->set('pageButtons', $pageButtons);