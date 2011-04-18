<?php
	$addressBook->deleteAddress((int)$_GET['delete']);
	$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_ADDRESS_BOOK_ENTRY_DELETED'), 'success');

	EventManager::attachActionResponse(itw_app_link(null, 'account', 'address_book', 'SSL'), 'redirect');
?>