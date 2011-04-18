<?php
/*
	Stream Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class streamProducts_admin_products_new_product extends Extension_streamProducts {

	public function __construct(){
		parent::__construct('streamProducts');
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'NewProductTabHeader',
			'NewProductTabBody'
		), null, $this);
	}
	
	public function NewProductTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('TAB_STREAMS') . '</span></a></li>';
	}
	
	public function NewProductTabBody(&$Product){
		$Table = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->addClass('ui-widget ui-widget-content streamsTable')
		->css(array(
			'width' => '100%'
		));
		
		$headerColumns = array(
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_STREAM_PREVIEW')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_STREAM_PROVIDER')),
			array('text' => sysLanguage::get('TABLE_HEADING_STREAM_TYPE')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_STREAM_FILE')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_STREAM_DISPLAY_NAME'))
		);
		
		EventManager::notifyWithReturn('NewProductStreamingTableAddHeaderCol', &$headerColumns);
		
		$headerColumns[] = array(
			'text' => '&nbsp;'
		);
		
		$Qproviders = Doctrine_Query::create()
		->from('ProductsStreamProviders')
		->orderBy('provider_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		$providerBox = htmlBase::newElement('selectbox')
		->setName('new_stream_provider')
		->addClass('selectStreamProvider')
		->addOption('', sysLanguage::get('TEXT_PLEASE_SELECT'));
		if ($Qproviders){
			foreach($Qproviders as $providerInfo){
				$providerBox->addOption($providerInfo['provider_id'], $providerInfo['provider_name']);
			}
		}
		
		$fileNameInput = htmlBase::newElement('input')
		->setName('new_stream_file_name');
		
		$displayNameInput = htmlBase::newElement('input')
		->setName('new_stream_display_name');
		
		$inputRow = array(
			array('addCls' => 'previewBoxCol', 'text' => ''),
			array('text' => $providerBox->draw()),
			array('align' => 'center', 'text' => '<div class="providerTypes">' . sysLanguage::get('TEXT_PLEASE_SELECT_PROVIDER') . '</div>'),
			array('text' => $fileNameInput->draw()),
			array('text' => $displayNameInput->draw()),
		);
		
		EventManager::notifyWithReturn('NewProductStreamingTableAddInputRow', &$inputRow);
		
		if (sizeof($inputRow)+1 != sizeof($headerColumns)){
			if (sizeof($inputRow)+1 > sizeof($headerColumns)){
				while(sizeof($inputRow)+1 > sizeof($headerColumns)){
					$headerColumns[] = array('text' => '&nbsp;');
				}
			}else{
				while(sizeof($inputRow)+1 < sizeof($headerColumns)){
					$inputRow[] = array('text' => '&nbsp;');
				}
			}
		}
		
		$inputRow[] = array(
			'align' => 'right',
			'text' => '<span class="ui-icon ui-icon-plusthick addStreamIcon"></span>'
		);
		
		$Table->addHeaderRow(array(
			'addCls' => 'ui-widget-header',
			'columns' => $headerColumns
		));
		
		$Table->addBodyRow(array(
			'addCls' => 'ui-state-hover',
			'columns' => $inputRow
		));
		
		$Streams = $Product->ProductsStreams;
		if ($Streams){
			foreach($Streams as $sInfo){
				$currentProvider = array();
				
				$providerBox = htmlBase::newElement('selectbox')
				->hide()
				->setName('stream_provider[' . $sInfo['stream_id'] . ']')
				->addClass('selectStreamProvider')
				->addOption('', sysLanguage::get('TEXT_PLEASE_SELECT'))
				->selectOptionByValue($sInfo['provider_id']);
				if ($Qproviders){
					foreach($Qproviders as $providerInfo){
						$providerBox->addOption($providerInfo['provider_id'], $providerInfo['provider_name']);
						if ($providerInfo['provider_id'] == $sInfo['provider_id']){
							$currentProvider = $providerInfo;
						}
					}
				}
				
				$providerTypeBox = htmlBase::newElement('selectbox')
				->hide()
				->addClass('streamProviderType')
				->setName('stream_provider_type[' . $sInfo['stream_id'] . ']')
				->selectOptionByValue($sInfo['stream_type']);
				if (!empty($currentProvider)){
					$moduleName = $currentProvider['provider_module'];
					$className = 'StreamProvider' . ucfirst($moduleName);
					if (!class_exists($className)){
						require(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/' . $moduleName . '/module.php');
					}
		
					$Module = new $className();
					foreach($Module->getStreamTypes() as $type){
						$providerTypeBox->addOption($type, ucfirst($type));
					}
				}
				
				$fileNameInput = htmlBase::newElement('input')
				->hide()
				->setName('stream_file_name[' . $sInfo['stream_id'] . ']')
				->val($sInfo['file_name']);
		
				$displayNameInput = htmlBase::newElement('input')
				->hide()
				->setName('stream_display_name[' . $sInfo['stream_id'] . ']')
				->val($sInfo['display_name']);
		
				$previewBox = htmlBase::newElement('checkbox')
				->addClass('previewStreamSetting noHide')
				->setName('preview_stream[' . $sInfo['stream_id'] . ']')
				->val($sInfo['stream_id'])
				->setChecked(($sInfo['is_preview'] == 1));
				
				$BodyColumns = array(
					array('text' => $previewBox->draw()),
					array('text' => '<span class="streamInfoText">' . $sInfo['ProductsStreamProviders']['provider_name'] . '</span>' . $providerBox->draw()),
					array('align' => 'center', 'text' => '<span class="streamInfoText">' . ucfirst($sInfo['stream_type']) . '</span>' . $providerTypeBox->draw()),
					array('text' => '<span class="streamInfoText">' . $sInfo['file_name'] . '</span>' . $fileNameInput->draw()),
					array('text' => '<span class="streamInfoText">' . $sInfo['display_name'] . '</span>' . $displayNameInput->draw())
				);
			
				EventManager::notifyWithReturn('NewProductStreamingTableAddBodyCol', $sInfo, &$BodyColumns);
			
				$BodyColumns[] = array(
					'align' => 'right',
					'text' => '<span class="ui-icon ui-icon-pencil editStreamIcon"></span><span class="ui-icon ui-icon-closethick deleteStreamIcon"></span>'
				);
			
				$Table->addBodyRow(array(
					'columns' => $BodyColumns
				));
			}
		}
				
		return '<div id="tab_' . $this->getExtensionKey() . '">' .  $Table->draw() . '</div>';
	}
}
?>