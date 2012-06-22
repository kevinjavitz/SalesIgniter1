<?php
	$QCustomer = Doctrine_Query::create()
	->from('Customers')
	->where('customers_id = ?', $userAccount->getCustomerId())
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
ob_start();
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td class="main"><b><?php echo sysLanguage::get('TABLE_HEADING_MEMBER_MEMBERSHIP_CARD'); ?></b></td>
		<td class="inputRequirement" align="right"></td>
	</tr>
</table>

<table border="0" cellspacing="2" cellpadding="2" style="margin:.5em;">
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_INFO_UPLOAD_PHOTO');?></td>
		<td class="main">
			<?php
		//uploadPhoto field
			$user_image = htmlBase::newElement('uploadManagerInput')
				->setName('customers_photo')
				->setFileType('image')
				->autoUpload(true)
				->showPreview(true)
				->showMaxUploadSize(true)
				->allowMultipleUploads(false)
				->attr('cid', $userAccount->getCustomerId());

			$user_image->setPreviewFile($QCustomer[0]['customers_photo']);
			echo $user_image->draw();
?></td>
	</tr>
	<tr>
		<td colspan="2">
	<?php
		$htmlButton = htmlBase::newElement('button')
	    ->setName('printOrder')
		->attr('target','_blank')
		->setHref(itw_app_link('action=printCard','account','membership_card'))
		->setText(sysLanguage::get('TEXT_PRINT_CARD'));
		echo $htmlButton->draw();

	?>
		</td>
	</tr>

</table>
<?php
	$pageContents = ob_get_contents();
ob_end_clean();

$pageTitle = sysLanguage::get('HEADING_TITLE_MEMBERSHIP_CARD');

$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'account', 'default', 'SSL'))
	->draw();

$pageContent->set('pageTitle', $pageTitle);
$pageContent->set('pageContent', $pageContents);
$pageContent->set('pageButtons', $pageButtons);
