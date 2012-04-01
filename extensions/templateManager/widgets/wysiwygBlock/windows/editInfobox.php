<?php
$selectedText = isset($WidgetSettings->block_html) ? $WidgetSettings->block_html : '';

$Editor = htmlBase::newElement('ck_editor')
	->setName('block_html')
	->html($selectedText);

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => $Editor->draw())
	)
));
ob_start();
?>
<script>
	$(document).ready(function () {
		$('.editWindow').find('.makeFCK').ckeditor(function () {
		}, {
			//filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
			filebrowserBrowseUrl: DIR_WS_ADMIN + 'rental_wysiwyg/filemanager/index.php'
		});

		$('.editWindow').find('.saveButton').one('onSave.wysiwygBlock', function () {
			//$('.wysiwygBlockContent').html($('.editWindow').find('.makeFCK').ckeditorGet().getData());
			$('.editWindow').find('.makeFCK').ckeditorGet().destroy();
		});
	});
</script>
<?php	
$javascript = ob_get_contents();
ob_end_clean();

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Wysiwyg Block Widget Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => $Editor->draw() . $javascript)
	)
));
