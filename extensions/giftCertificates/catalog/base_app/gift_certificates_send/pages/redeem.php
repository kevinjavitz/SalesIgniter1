<?php
	ob_start();

    $message = sprintf(sysLanguage::get('TEXT_VALID_GV'), $currencies->format($coupon['coupon_amount']));
    if ($error){
        $message = sysLanguage::get('TEXT_INVALID_GV');
    }
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td class="main"><?php echo sysLanguage::get('TEXT_INFORMATION'); ?></td>
    </tr>
    <tr>
        <td class="main"><?php echo $message; ?></td>
    </tr>
</table>
<?php
	$pageContents = ob_get_contents();
    ob_end_clean();

    $pageButtons = htmlBase::newElement('button')
        ->usePreset('continue')
        ->setHref(itw_app_link(null, 'index', 'default'))
        ->draw();

    $pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE'));
    $pageContent->set('pageContent', $pageContents);
    $pageContent->set('pageButtons', $pageButtons);
