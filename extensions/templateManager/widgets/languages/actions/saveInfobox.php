<?php
	foreach(sysLanguage::getLanguages() as $lInfo) {
		if (isset($_POST['image_source_' . $lInfo['code']])){
			$WidgetProperties['image_source_' . $lInfo['code']] = $_POST['image_source_' . $lInfo['code']];
		}
	}
?>