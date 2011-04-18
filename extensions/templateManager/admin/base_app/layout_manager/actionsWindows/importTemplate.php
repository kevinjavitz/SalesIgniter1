<?php
$RequestObj = new CurlRequest('https://' . sysConfig::get('SYSTEM_UPGRADE_SERVER') . '/sesUpgrades/getTemplates.php');
$RequestObj->setSendMethod('post');
$RequestObj->setData(array(
	'action' => 'process',
	'version' => 1,
	'username' => sysConfig::get('SYSTEM_UPGRADE_USERNAME'),
	'password' => sysConfig::get('SYSTEM_UPGRADE_PASSWORD'),
	'domain' => $_SERVER['HTTP_HOST']
));

$ResponseObj = $RequestObj->execute();

$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . sysLanguage::get('WINDOW_HEADING_IMPORT_TEMPLATES') . '</b>');
$infoBox->setButtonBarLocation('top');

$installButton = htmlBase::newElement('button')->addClass('installButton')->usePreset('save')->setText('Import');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($installButton)->addButton($cancelButton);

$json = json_decode($ResponseObj->getResponse());
if ($json->success === true){
	$templatesContainer = htmlBase::newElement('div')
	->addClass('importTemplateContainer');

	$numAvailable = 0;
	foreach($json->templates as $tInfo){
		//if (is_dir(sysConfig::getDirFsCatalog() . 'templates/' . strtolower($tInfo->name))) continue;
		$numAvailable++;

		$images = '<ul style="list-style:none;position:absolute;bottom:0;left:0;margin:1em;padding:0;">';
		foreach($tInfo->images as $iInfo){
			$images .= '<li style="display:inline;margin: 0 10px;"><img src="' . $iInfo . '" width="75px"></li>';
		}
		$images .= '</ul>';

		$TemplateBox = htmlBase::newElement('div')
		->addClass('ui-widget ui-widget-content ui-corner-all importTemplate')
		->css(array(
			'float' => 'left',
			'width' => '350px',
			'height' => '450px',
			'margin' => '.5em',
			'position' => 'relative'
		))
		->html('<center>' .
			'<input type=checkbox name="template[]" value="' . strtolower($tInfo->name) . '">&nbsp;' .
			'<b style="font-size:1.2em;">' . $tInfo->name . '</b><br><br>' .
			'<div class="currentImage" style="height:300px"></div>' .
			$images .
		'</center>');

		$templatesContainer->append($TemplateBox);
	}

	if ($numAvailable == 0){
		$infoBox->addContentRow('No Templates Available');
	}else{
		$infoBox->addContentRow($templatesContainer->draw() . '<div class="ui-helper-clearfix"></div>');
	}
}else{
	$infoBox->addContentRow('No Templates Available');
}

ob_start();
?>
<script type="text/javascript">
function importWindowOnLoad(){
	$('.importTemplateContainer').find('.importTemplate').each(function (){
		$(this).find('img').each(function (){
			$(this).click(function (){
				$(this).parent().parent().find('.ui-state-highlight').removeClass('ui-state-highlight');
				$(this).addClass('ui-state-highlight');
				$(this).parent().parent().parent().find('.currentImage').html('<img src="' + $(this).attr('src') + '" width="300px">');
			}).mouseover(function (){
				this.style.cursor = 'pointer';
			}).mouseout(function (){
				this.style.cursor = 'default';
			});
		});

		$(this).find('img').first().click();
	});
}
</script>
<?php
$javascript = ob_get_contents();
ob_end_clean();

EventManager::attachActionResponse($javascript . $infoBox->draw(), 'html');
