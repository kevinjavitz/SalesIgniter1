<?php
/*
	Product Designer Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class productDesigner_admin_multiStore_manage_new_store extends Extension_productDesigner {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'NewStoreAddTab',
			'ApplicationTemplateBeforeInclude'
		), null, $this);
	}
	
	public function ApplicationTemplateBeforeInclude(){
		global $App;
		
		$App->addJavascriptFile('ext/jQuery/external/iColorPicker/jquery.icolorpicker.js');
		$App->addJavascriptFile('ext/jQuery/external/fancybox/jquery.fancybox.js');
		$App->addJavascriptFile('ext/jQuery/external/uploadify/swfobject.js');
		$App->addJavascriptFile('ext/jQuery/external/uploadify/jquery.uploadify.js');
		
		$App->addStylesheetFile('ext/jQuery/external/uploadify/jquery.uploadify.css');
		$App->addStylesheetFile('ext/jQuery/external/fancybox/jquery.fancybox.css');
	}
	
	public function NewStoreAddTab(&$tabsObj){
		$primaryLightColor = htmlBase::newElement('input')->addClass('iColorPicker')->setName('light_primary_color')->setId('light_primary_color');
		$secondaryLightColor = htmlBase::newElement('input')->addClass('iColorPicker')->setName('light_secondary_color')->setId('light_secondary_color');
		$primaryDarkColor = htmlBase::newElement('input')->addClass('iColorPicker')->setName('dark_primary_color')->setId('dark_primary_color');
		$secondaryDarkColor = htmlBase::newElement('input')->addClass('iColorPicker')->setName('dark_secondary_color')->setId('dark_secondary_color');
		if (isset($_GET['sID'])){
			$QstoreInfo = Doctrine_Query::create()
			->select('designer_light_primary_color, designer_light_secondary_color, designer_dark_primary_color, designer_dark_secondary_color')
			->from('Stores')
			->where('stores_id = ?', $_GET['sID'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			
			$primaryLightColor->val($QstoreInfo[0]['designer_light_primary_color']);
			$secondaryLightColor->val($QstoreInfo[0]['designer_light_secondary_color']);
			$primaryDarkColor->val($QstoreInfo[0]['designer_dark_primary_color']);
			$secondaryDarkColor->val($QstoreInfo[0]['designer_dark_secondary_color']);
		}
		
		$mainTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'colspan' => '2', 'text' => '<b><u>Light Image Colors</u></b>')
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Primary:'),
				array('addCls' => 'main', 'text' => $primaryLightColor->draw()),
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Secondary:'),
				array('addCls' => 'main', 'text' => $secondaryLightColor->draw()),
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'colspan' => '2', 'text' => '<b><u>Dark Image Colors</u></b>')
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Primary:'),
				array('addCls' => 'main', 'text' => $primaryDarkColor->draw()),
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Secondary:'),
				array('addCls' => 'main', 'text' => $secondaryDarkColor->draw()),
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Activities:'),
				array('addCls' => 'main', 'text' => 'Coming in phase 2'),
			)
		));
		
		$textIdTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
		$Qkeys = Doctrine_Query::create()
		->from('ProductDesignerPredesignKeys k')
		//->leftJoin('k.ProductDesignerPredesignKeysToStores k2s')
		->where('set_from = ?', 'admin')
		->andWhere('key_type = ?', 'text')
		->orderBy('key_text')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qkeys){
			foreach($Qkeys as $kInfo){
				$textInput = htmlBase::newElement('input')
				->setName('text_id[' . $kInfo['key_id'] . ']');
				
				$colorReplaceInput = htmlBase::newElement('checkbox')
				->setName('text_color_replace[' . $kInfo['key_id'] . ']')
				->setLabel('Use Color Replacement')
				->setLabelPosition('after');
				
				if (isset($_GET['sID'])){
					$QstoreInfo = Doctrine_Query::create()
					->select('content, use_color_replace')
					->from('ProductDesignerPredesignKeysToStores')
					->where('key_id = ?', $kInfo['key_id'])
					->andWhere('stores_id = ?', $_GET['sID'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($QstoreInfo){
						$textInput->val($QstoreInfo[0]['content']);
						$colorReplaceInput->setChecked(($QstoreInfo[0]['use_color_replace'] == '1'));
					}
				}
				
				$textIdTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'valign' => 'top', 'text' => '<b>' . $kInfo['key_text'] . '</b>: '),
						array('addCls' => 'main', 'text' => $textInput->draw() . '<br />' . $colorReplaceInput->draw())
					)
				));
			}
		}else{
			$textIdTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => 'No Keys Available')
				)
			));
		}
		
		$clipartIdTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
		$Qkeys = Doctrine_Query::create()
		->from('ProductDesignerPredesignKeys k')
		//->leftJoin('k.ProductDesignerPredesignKeysToStores k2s')
		->where('set_from = ?', 'admin')
		->andWhere('key_type = ?', 'clipart')
		->orderBy('key_text')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qkeys){
			foreach($Qkeys as $kInfo){
				$keyId = $kInfo['key_id'];
				
				$fileInput = htmlBase::newElement('input')->setType('file')->addClass('ajaxUpload')
				->setId('clipartUpload_' . $keyId);
				
				$fileInputLight = htmlBase::newElement('input')->setType('file')->addClass('ajaxUpload')
				->setId('clipartLightUpload_' . $keyId);
				
				$fileInputDark = htmlBase::newElement('input')->setType('file')->addClass('ajaxUpload')
				->setId('clipartDarkUpload_' . $keyId);

				$hiddenField = htmlBase::newElement('input')->setType('hidden')->setName('clipart[' . $keyId . '][default]');
				$hiddenFieldLight = htmlBase::newElement('input')->setType('hidden')->setName('clipart[' . $keyId . '][light]');
				$hiddenFieldDark = htmlBase::newElement('input')->setType('hidden')->setName('clipart[' . $keyId . '][dark]');
				
				$colorReplaceInput = htmlBase::newElement('checkbox')
				->setName('clipart_color_replace[' . $keyId . '][default]')
				->setLabel('Use Color Replacement')
				->setLabelPosition('after');
				
				$colorReplaceInputLight = htmlBase::newElement('checkbox')
				->setName('clipart_color_replace[' . $keyId . '][light]')
				->setLabel('Use Color Replacement')
				->setLabelPosition('after');
				
				$colorReplaceInputDark = htmlBase::newElement('checkbox')
				->setName('clipart_color_replace[' . $keyId . '][dark]')
				->setLabel('Use Color Replacement')
				->setLabelPosition('after');
				
				$extraInfo = '';
				if (isset($_GET['sID'])){
					$QstoreInfo = Doctrine_Query::create()
					->from('ProductDesignerPredesignKeysToStores')
					->where('key_id = ?', $keyId)
					->andWhere('stores_id = ?', $_GET['sID'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($QstoreInfo){
						$storeInfo = $QstoreInfo[0];
						$zoomIcon = htmlBase::newElement('icon')->setType('zoomIn');
						$deleteIcon = htmlBase::newElement('icon')->setType('closeThick');
						$imgSrc = sysConfig::getDirWsCatalog() . 'extensions/productDesigner/images/dynamic/';
						$thumbSrc = 'imagick_thumb.php?width=50&height=50&imgSrc=' . sysConfig::getDirFsCatalog() . 'extensions/productDesigner/images/dynamic/';
						
						$extraInfo = '';
						if (!empty($storeInfo['content'])){
							$hiddenField->val($storeInfo['content']);
							$imgPreview = htmlBase::newElement('img')->attr('src', $thumbSrc . $storeInfo['content']);
							$extraInfo = '<a class="fancyBox" href="' . $imgSrc . $storeInfo['content'] . '">' . 
							             	$imgPreview->draw() . 
							             '</a><br />' . 
							             $zoomIcon->draw() . ' ' . $deleteIcon->draw();
						}
						
						$extraInfoLight = '';
						if (!empty($storeInfo['content_light'])){
							$hiddenFieldLight->val($storeInfo['content_light']);
							$imgPreviewLight = htmlBase::newElement('img')->attr('src', $thumbSrc . $storeInfo['content_light']);
							$extraInfoLight = '<a class="fancyBox" href="' . $imgSrc . $storeInfo['content_light'] . '">' . 
							                  	$imgPreviewLight->draw() . 
							                  '</a><br />' . 
							                  $zoomIcon->draw() . ' ' . $deleteIcon->draw();
						}
						
						$extraInfoDark = '';
						if (!empty($storeInfo['content_dark'])){
							$hiddenFieldDark->val($storeInfo['content_dark']);
							$imgPreviewDark = htmlBase::newElement('img')->attr('src', $thumbSrc . $storeInfo['content_dark']);
							$extraInfoDark = '<a class="fancyBox" href="' . $imgSrc . $storeInfo['content_dark'] . '">' . 
							                 	$imgPreviewDark->draw() . 
							                 '</a><br />' . 
							                 $zoomIcon->draw() . ' ' . $deleteIcon->draw();
						}
								
						$colorReplaceInput->setChecked(($storeInfo['use_color_replace'] == '1'));
						$colorReplaceInputLight->setChecked(($storeInfo['use_color_replace_light'] == '1'));
						$colorReplaceInputDark->setChecked(($storeInfo['use_color_replace_dark'] == '1'));
					}
				}
				
				$fileInputsTable = htmlBase::newElement('table')->setCellpadding(3)->setCellSpacing(0);
				$fileInputsTable->addBodyRow(array(
					'attr' => array(
						'color_tone'  => 'default',
						'clipart_key' => $keyId
					),
					'columns' => array(
						array('addCls' => 'main', 'valign' => 'top', 'text' => 'Default:'),
						array('addCls' => 'main', 'valign' => 'top', 'text' => $fileInput->draw() . $colorReplaceInput->draw()),
						array('addCls' => 'main imagePreview', 'valign' => 'top', 'align' => 'center', 'text' => $extraInfo . $hiddenField->draw()),
					)
				));
				$fileInputsTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'colspan' => 3, 'align' => 'center', 'valign' => 'top', 'text' => ' <b>OR</b> ')
					)
				));
				$fileInputsTable->addBodyRow(array(
					'attr' => array(
						'color_tone'  => 'light',
						'clipart_key' => $keyId
					),
					'columns' => array(
						array('addCls' => 'main', 'valign' => 'top', 'text' => 'Light:'),
						array('addCls' => 'main', 'valign' => 'top', 'text' => $fileInputLight->draw() . $colorReplaceInputLight->draw()),
						array('addCls' => 'main imagePreview', 'valign' => 'top', 'align' => 'center', 'text' => $extraInfoLight . $hiddenFieldLight->draw()),
					)
				));
				$fileInputsTable->addBodyRow(array(
					'attr' => array(
						'color_tone'  => 'dark',
						'clipart_key' => $keyId
					),
					'columns' => array(
						array('addCls' => 'main', 'valign' => 'top', 'text' => 'Dark:'),
						array('addCls' => 'main', 'valign' => 'top', 'text' => $fileInputDark->draw() . $colorReplaceInputDark->draw()),
						array('addCls' => 'main imagePreview', 'valign' => 'top', 'align' => 'center', 'text' => $extraInfoDark . $hiddenFieldDark->draw()),
					)
				));
				
				$clipartIdTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'valign' => 'top', 'text' => '<b>' . $kInfo['key_text'] . ':</b> '),
						array('addCls' => 'main', 'valign' => 'top', 'text' => $fileInputsTable),
					)
				));
			}
		}else{
			$clipartIdTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => 'No Keys Available')
				)
			));
		}
		
		$idTabs = htmlBase::newElement('tabs')->setId('idTabs');
		
		$idTabs->addTabHeader('idTabs_textIds', array('text' => 'Text ID\'s'))
		->addTabPage('idTabs_textIds', array('text' => $textIdTable))
		->addTabHeader('idTabs_clipartIds', array('text' => 'Clipart ID\'s'))
		->addTabPage('idTabs_clipartIds', array('text' => $clipartIdTable));
		
		$tabsObj->addTabHeader('tab_' . $this->getExtensionKey(), array('text' => sysLanguage::get('TAB_PRODUCT_DESIGNER')))
		->addTabPage('tab_' . $this->getExtensionKey(), array('text' => $mainTable->draw() . '<br />' . $idTabs->draw()));
	}
}
?>