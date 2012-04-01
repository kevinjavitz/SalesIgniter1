<?php
/*
	Download Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class downloadProducts_admin_products_new_product extends Extension_downloadProducts {

	public function __construct(){
		parent::__construct('downloadProducts');
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'NewProductTabHeader',
			'NewProductTabBody'
		), null, $this);
	}
	
	public function NewProductTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('TAB_DOWNLOADS') . '</span></a></li>';
	}
	
	public function NewProductTabBody(&$Product){
		$Table = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->addClass('ui-widget ui-widget-content downloadsTable')
		->css(array(
			'width' => '98%'
		));
		
		$headerColumns = array(
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_DOWNLOAD_PROVIDER')),
			array('text' => sysLanguage::get('TABLE_HEADING_DOWNLOAD_TYPE')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_DOWNLOAD_FILE')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_DOWNLOAD_DISPLAY_NAME'))
		);
		
		EventManager::notifyWithReturn('NewProductDownloadsTableAddHeaderCol', &$headerColumns);
		
		$headerColumns[] = array(
			'text' => '&nbsp;'
		);
		
		$Qproviders = Doctrine_Query::create()
		->from('ProductsDownloadProviders')
		->orderBy('provider_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		$providerBox = htmlBase::newElement('selectbox')
		->setName('new_download_provider')
		->addClass('selectDownloadProvider')
		->addOption('', sysLanguage::get('TEXT_PLEASE_SELECT'));
		if ($Qproviders){
			foreach($Qproviders as $providerInfo){
				$providerBox->addOption($providerInfo['provider_id'], $providerInfo['provider_name']);
			}
		}
		
		$fileNameInput = htmlBase::newElement('input')
		->addClass('providerFileName')
		->setName('new_download_file_name');
		
		$displayNameInput = htmlBase::newElement('input')
		->addClass('providerDisplayName')
		->setName('new_download_display_name');
		
		$inputRow = array(
			array('text' => $providerBox->draw()),
			array('align' => 'center', 'text' => '<div class="providerTypes">' . sysLanguage::get('TEXT_PLEASE_SELECT_PROVIDER') . '</div>'),
			array('text' => $fileNameInput->draw() . '<span class="ui-icon ui-icon-newwin" style="vertical-align:middle;"></span>'),
			array('text' => $displayNameInput->draw()),
		);
		
		EventManager::notifyWithReturn('NewProductDownloadsTableAddInputRow', &$inputRow);
		
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
			'text' => '<span class="ui-icon ui-icon-plusthick addDownloadIcon"></span>'
		);
		
		$Table->addHeaderRow(array(
			'addCls' => 'ui-widget-header',
			'columns' => $headerColumns
		));
		
		$Table->addBodyRow(array(
			'addCls' => 'ui-state-hover',
			'columns' => $inputRow
		));
		
		$Downloads = $Product->ProductsDownloads;
		if ($Downloads){
			foreach($Downloads as $dInfo){
				$currentProvider = array();
				
				$providerBox = htmlBase::newElement('selectbox')
				->hide()
				->setName('download_provider[' . $dInfo['download_id'] . ']')
				->addClass('selectDownloadProvider')
				->addOption('', sysLanguage::get('TEXT_PLEASE_SELECT'))
				->selectOptionByValue($dInfo['provider_id']);
				if ($Qproviders){
					foreach($Qproviders as $providerInfo){
						$providerBox->addOption($providerInfo['provider_id'], $providerInfo['provider_name']);
						if ($providerInfo['provider_id'] == $dInfo['provider_id']){
							$currentProvider = $providerInfo;
						}
					}
				}
				
				$providerTypeBox = htmlBase::newElement('selectbox')
				->hide()
				->addClass('downloadProviderType')
				->setName('download_provider_type[' . $dInfo['download_id'] . ']')
				->selectOptionByValue($dInfo['download_type']);
				if (!empty($currentProvider)){
					$moduleName = $currentProvider['provider_module'];
					$className = 'DownloadProvider' . ucfirst($moduleName);
					if (!class_exists($className)){
						require(sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/providerModules/' . $moduleName . '/module.php');
					}
		
					$Module = new $className();
					foreach($Module->getDownloadTypes() as $type){
						$providerTypeBox->addOption($type, ucfirst($type));
					}
				}
				
				$fileNameInput = htmlBase::newElement('input')
				->hide()
				->addClass('providerFileName')
				->setName('download_file_name[' . $dInfo['download_id'] . ']')
				->val($dInfo['file_name']);
		
				$displayNameInput = htmlBase::newElement('input')
				->hide()
				->addClass('providerDisplayName')
				->setName('download_display_name[' . $dInfo['download_id'] . ']')
				->val($dInfo['display_name']);
		
				$BodyColumns = array(
					array('text' => '<span class="downloadInfoText">' . $dInfo['ProductsDownloadProviders']['provider_name'] . '</span>' . $providerBox->draw()),
					array('align' => 'center', 'text' => '<span class="downloadInfoText">' . ucfirst($dInfo['download_type']) . '</span>' . $providerTypeBox->draw()),
					array('text' => '<span class="downloadInfoText">' . $dInfo['file_name'] . '</span>' . $fileNameInput->draw() . '<span class="ui-icon ui-icon-newwin" style="display:none;vertical-align:middle;"></span>'),
					array('text' => '<span class="downloadInfoText">' . $dInfo['display_name'] . '</span>' . $displayNameInput->draw())
				);
			
				EventManager::notifyWithReturn('NewProductDownloadsTableAddBodyCol', $dInfo, &$BodyColumns);
			
				$BodyColumns[] = array(
					'align' => 'right',
					'text' => '<span class="ui-icon ui-icon-pencil editDownloadIcon"></span><span class="ui-icon ui-icon-closethick deleteDownloadIcon"></span>'
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