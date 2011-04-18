<?php
	$file = str_replace('\\', '/', $_GET['file']);
	$filePath = sysConfig::getDirFsCatalog() . $file;
	
	$userFilePath = sysConfig::getDirFsCatalog() . 'includes/languages/english/';
	if (substr($file, 0, 5) != 'admin'){
		if (substr($file, 0, 10) != 'extensions'){
			if (substr($file, 0, 8) != 'includes'){
				if (substr($userFilePath, 0, 7) != 'catalog'){
					$userFilePath .= 'catalog/';
				}
			}
		}
	}
	$userFilePath .= str_replace('language_defines/', '', $file);
	
	$Table = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0)
	->css(array(
		'width' => '100%'
	));
	
	$saveButton = htmlBase::newElement('button')
	->addClass('saveButton')
	->attr('data-filePath', $file)
	->usePreset('save');
	
	$Table->addBodyRow(array(
		'columns' => array(
			array(
				'colspan' => 2,
				'align' => 'right',
				'text' => $saveButton->draw()
				
			)
		)
	));
	
	$langData = simplexml_load_file(
		$filePath,
		'SimpleXMLElement',
		LIBXML_NOCDATA
	);
	
	$userLangData = false;
	if (file_exists($userFilePath)){
		$userLangData = simplexml_load_file(
			$userFilePath,
			'SimpleXMLElement',
			LIBXML_NOCDATA
		);
	}
	$columns = array();
	$i = 0;
	
	$deleteIcon = /*htmlBase::newElement('icon')->setType('delete')->draw()*/'';
	$editorIcon = htmlBase::newElement('icon')->setType('newwin')->draw();
	$userDefineIcon = htmlBase::newElement('icon')->setType('star')->draw();
	foreach($langData->define as $define){
		$key = (string) $define['key'];
		
		$userDefined = false;
		if ($userLangData){
			if ($userLangData->definitions->xpath('//definitions/define[@key="' . $key . '"]')){
				$userDefined = true;
			}
		}
		
		$textInput = htmlBase::newElement('textarea')
		->setName('text[' . $key . ']')
		->val(str_replace('\\', '\\\\', (string) $define[0]))
		->setRows('3')
		->css(array(
			'width' => '100%'
		));
		
		$text = $key;
		if ($userDefined === true){
			//$text .= ' ' . $userDefineIcon;
			$textInput->addClass('hasCustomDefine');
		}
		$text .= '<span style="float:right;">' . $editorIcon . ' ' . $deleteIcon . '</span><br />' . $textInput->draw();
		
		$columns[] = array(
			'valign' => 'top',
			'css' => array('padding-right' => '2em'),
			'text' => $text
		);
		
		$i++;
		if ($i > 1){
			$Table->addBodyRow(array(
				'columns' => $columns
			));
			$columns = array();
			$i = 0;
		}
	}
	
	if (sizeof($columns) > 0){
		$Table->addBodyRow(array(
			'columns' => $columns
		));
	}
	
	EventManager::attachActionResponse($Table->draw(), 'html');
?>