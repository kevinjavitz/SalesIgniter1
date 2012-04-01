<?php
$pageContents = '';
$Product = new Product((int)$_GET['products_id']);
if ($Product->isValid() === false || $Product->isActive() === false){
	$notFoundDiv = htmlBase::newElement('div')
		->addClass('ui-widget ui-widget-content ui-corner-all')
		->css('padding', '.5em')
		->html(sysLanguage::get('TEXT_PRODUCT_NOT_FOUND'));

	$continueButton = htmlBase::newElement('button')->usePreset('continue')
		->setHref(itw_app_link(null, 'index', 'default'));

	$buttonBar = htmlBase::newElement('div')
		->addClass('ui-widget ui-widget-content ui-corner-all pageButtonBar')
		->append($continueButton);

	$pageTitle = sysLanguage::get('TEXT_PRODUCT_NOT_FOUND');
	$pageContents .= $notFoundDiv->draw() . $buttonBar->draw();
}
else {
	$pageTitle = $Product->getName();
	if ($Product->hasModel()){
		$pageTitle .= '&nbsp;<span class="smallText">[' . $Product->getModel() . ']</span>';
	}
	$Product->updateViews();

	$showAlsoPurchased = false;
	if (isset($_GET['products_id'])){
		$QOrders = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc("select p.products_id, p.products_image from orders_products opa, orders_products opb, orders o, products p where opa.products_id = '" . (int)$_GET['products_id'] . "' and opa.orders_id = opb.orders_id and opb.products_id != '" . (int)$_GET['products_id'] . "' and opb.products_id = p.products_id and opb.orders_id = o.orders_id and p.products_status = '1' group by p.products_id order by o.date_purchased desc limit " . sysConfig::get('MAX_DISPLAY_ALSO_PURCHASED'));
		$num_products_ordered = sizeof($QOrders);
		if ($num_products_ordered >= sysConfig::get('MIN_DISPLAY_ALSO_PURCHASED')){
			$showAlsoPurchased = true;
		}
	}

	$contents = EventManager::notifyWithReturn('ProductInfoBeforeInfo', &$Product);
	if (!empty($contents)){
		foreach($contents as $content){
			$pageContents .= $content;
		}
	}

	$pageContents .= '<div class="tabs" data-role="navbar"><ul>';
	$pageContents .= '<li><a href="#" data-href="tabOverview">' . sysLanguage::get('TAB_OVERVIEW') . '</a></li>';
	if ($showAlsoPurchased === true){
		$pageContents .= '<li><a href="#" data-href="tabAlsoPurchased">' . sysLanguage::get('TAB_ALSO_PURCHASED') . '</a></li>';
	}

	$contents = EventManager::notifyWithReturn('ProductInfoTabHeader', &$Product);
	if (!empty($contents)){
		foreach($contents as $content){
			$pageContents .= $content;
		}
	}
	$pageContents .= '</ul></div>';

	$pageContents .= '<div id="tabOverview" class="tabPage">';
	ob_start();
	include(sysConfig::getDirFsCatalog() . 'applications/mobile/pagesTabs/productInfo/tab_overview.php');
	$pageContents .= ob_get_contents();
	ob_end_clean();
	$pageContents .= '</div>';

	if ($showAlsoPurchased === true){
		$pageContents .= '<div id="tabAlsoPurchased">';
		ob_start();
		include($pageTabsFolder . 'tab_also_purchased.php');
		$pageContents .= ob_get_contents();
		ob_end_clean();
		$pageContents .= '</div>';
	}

	$contents = EventManager::notifyWithReturn('ProductInfoTabBody', &$Product);
	if (!empty($contents)){
		foreach($contents as $content){
			$pageContents .= $content;
		}
	}

	$contents = EventManager::notifyWithReturn('ProductInfoAfterInfo', &$Product);
	if (!empty($contents)){
		foreach($contents as $content){
			$pageContents .= $content;
		}
	}
}

$pageButtons = '';

$content = EventManager::notifyWithReturn('ProductInfoButtonBarAddButton', $Product);
if (!empty($content)){
	foreach($content as $html){
		$pageButtons .= $html;
	}
}
ob_start();
?>
<style>
	.tabs .ui-btn { -webkit-border-radius: 10px 10px 0 0; }
	.tabPage { display:none;background:white;padding:5px; }
	.tabPage p { margin:0px; }
</style>
<script>
	$(document).delegate('.tabs[data-role="navbar"] a', 'click', function () {
		$(this).addClass('ui-btn-active');
		$('.tabPage').hide();
		$('#' + $(this).attr('data-href')).show();
	});
$('.tabs li a').first().trigger('click');
</script>
<?php
$pageContents .= ob_get_contents();
ob_end_clean();

$pageContent->set('pageTitle', $pageTitle);
$pageContent->set('pageContent', '<br>' . $pageContents);
