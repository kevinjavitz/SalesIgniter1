<?php
	$GiftCertificates = Doctrine_Core::getTable('GiftCertificates')->find((int) $_GET['gcID']);
    $success = false;
    if ($GiftCertificates){
        $GiftCertificates->delete();
        $success = true;
    }

    EventManager::attachActionResponse(array(
                                            'success' => $success
                                       ), 'json');
?>