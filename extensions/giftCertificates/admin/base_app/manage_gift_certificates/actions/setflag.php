<?php
	if ($_GET['flag'] == 'N' || $_GET['flag'] == 'Y'){
    if (isset($_GET['gcID'])){
        Doctrine_Query::create()
                ->update('GiftCertificates')
                ->set('gift_certificates_status', '?', $_GET['flag'])
                ->where('gift_certificates_id = ?', (int) $_GET['gcID'])
                ->execute();
    }
}

    EventManager::attachActionResponse(itw_app_link('appExt=giftCertificates', 'manage_gift_certificates', 'default'), 'redirect');
?>