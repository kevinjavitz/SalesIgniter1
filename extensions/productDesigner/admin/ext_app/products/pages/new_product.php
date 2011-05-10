<?php
/*
	Product Designer Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class productDesigner_admin_products_new_product extends Extension_productDesigner {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'NewProductTabHeader',
			'NewProductTabBody',
			'ApplicationTemplateBeforeInclude',
		), null, $this);
	}
	
	public function ApplicationTemplateBeforeInclude(){
		global $App;
		
		$App->addJavascriptFile('ext/jQuery/external/iColorPicker/jquery.icolorpicker.js');
	}
	
	public function NewProductTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('TAB_PRODUCT_DESIGNER') . '</span></a></li>';
	}
	
	public function NewProductTabBody(&$Product){
		if ($Product['products_id'] > 0){
			//$payPerRental = $Product['ProductsPresetDesigns'];
		}
		
		$tabObj = htmlBase::newElement('tabs')->setId('product_designer_settings_tabs');

		$this->buildDesignControlsTab(&$tabObj, &$Product);
		$this->buildEditableAreasTab(&$tabObj, &$Product);
		$this->buildImagesTab(&$tabObj, &$Product);
		
		return '<div id="tab_' . $this->getExtensionKey() . '">' . 
			'<link rel="stylesheet" type="text/css" href="' . sysConfig::getDirWsCatalog() . 'extensions/productDesigner/admin/ext_app/products/javascript/imgAreaSelect/css/imgareaselect-default.css" />' . 
			'<script type="text/javascript" src="' . sysConfig::getDirWsCatalog() . 'extensions/productDesigner/admin/ext_app/products/javascript/imgAreaSelect/jquery.imgareaselect.js"></script>' . 
			$tabObj->draw() . 
		'</div>';
	}
	
	public function buildEditableAreasTab(&$tabObj, &$Product){
		$productImageEditable = htmlBase::newElement('img')->addClass('productImageEditable')->attr('src', sysConfig::getDirWsCatalog() . sysConfig::get('DIR_WS_IMAGES') . $Product['products_image']);
		$productImageEditableBack = htmlBase::newElement('img')->addClass('productImageEditableBack')->attr('src', sysConfig::getDirWsCatalog() . sysConfig::get('DIR_WS_IMAGES') . $Product['products_image_back']);
		
		$x1 = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[front][x1]')->setId('selectedX1');
		$y1 = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[front][y1]')->setId('selectedY1');
		$x2 = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[front][x2]')->setId('selectedX2');
		$y2 = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[front][y2]')->setId('selectedY2');
		$width = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[front][width]')->setId('selectedWidth');
		$height = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[front][height]')->setId('selectedHeight');
		$widthInches = htmlBase::newElement('input')->setName('editable_area[front][width_inches]')->setSize(5);
		$heightInches = htmlBase::newElement('input')->setName('editable_area[front][height_inches]')->setSize(5);
		
		$x1Back = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[back][x1]')->setId('selectedX1Back');
		$y1Back = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[back][y1]')->setId('selectedY1Back');
		$x2Back = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[back][x2]')->setId('selectedX2Back');
		$y2Back = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[back][y2]')->setId('selectedY2Back');
		$widthBack = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[back][width]')->setId('selectedWidthBack');
		$heightBack = htmlBase::newElement('input')->setType('hidden')->setName('editable_area[back][height]')->setId('selectedHeightBack');
		$widthInchesBack = htmlBase::newElement('input')->setName('editable_area[back][width_inches]')->setSize(5);
		$heightInchesBack = htmlBase::newElement('input')->setName('editable_area[back][height_inches]')->setSize(5);
		
		if ($Product['products_id'] > 0){
			$Qarea = Doctrine_Query::create()
			->from('ProductDesignerEditableAreas')
			->where('products_id = ?', $Product['products_id'])
			->andWhere('area_location = ?', 'front')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qarea){
				$x1->val($Qarea[0]['area_x1']);
				$x2->val($Qarea[0]['area_x2']);
				$y1->val($Qarea[0]['area_y1']);
				$y2->val($Qarea[0]['area_y2']);
				$width->val($Qarea[0]['area_width']);
				$height->val($Qarea[0]['area_height']);
				$widthInches->val($Qarea[0]['area_width_inches']);
				$heightInches->val($Qarea[0]['area_height_inches']);
			}
			
			$QareaBack = Doctrine_Query::create()
			->from('ProductDesignerEditableAreas')
			->where('products_id = ?', $Product['products_id'])
			->andWhere('area_location = ?', 'back')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($QareaBack){
				$x1Back->val($QareaBack[0]['area_x1']);
				$x2Back->val($QareaBack[0]['area_x2']);
				$y1Back->val($QareaBack[0]['area_y1']);
				$y2Back->val($QareaBack[0]['area_y2']);
				$widthBack->val($QareaBack[0]['area_width']);
				$heightBack->val($QareaBack[0]['area_height']);
				$widthInchesBack->val($QareaBack[0]['area_width_inches']);
				$heightInchesBack->val($QareaBack[0]['area_height_inches']);
			}
		}
		
		$hiddenInputs = $x1->draw() . $x2->draw() . $y1->draw() . $y2->draw() . $width->draw() . $height->draw();
		$hiddenInputsBack = $x1Back->draw() . $x2Back->draw() . $y1Back->draw() . $y2Back->draw() . $widthBack->draw() . $heightBack->draw();
		
		$sizeInputs = $widthInches->draw() . ' x ' . $heightInches->draw() . ' ( width x height )';
		$sizeInputsBack = $widthInchesBack->draw() . ' x ' . $heightInchesBack->draw() . ' ( width x height )';
		
		$Qpredesigns = Doctrine_Query::create()
		->select('predesign_name, predesign_id')
		->from('ProductDesignerPredesigns')
		->orderBy('predesign_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$predesignInput = '';
		$predesignInputBack = '';
		if ($Qpredesigns){
			$predesignInput = htmlBase::newElement('selectbox')->setName('predesign_id');
			$predesignInput->addOption('', 'None');
			$predesignInputBack = htmlBase::newElement('selectbox')->setName('predesign_id_back');
			$predesignInputBack->addOption('', 'None');
			foreach($Qpredesigns as $predesign){
				$predesignInput->addOption($predesign['predesign_id'], $predesign['predesign_name']);
				$predesignInputBack->addOption($predesign['predesign_id'], $predesign['predesign_name']);
			}
			if (!empty($Product->predesign_id)){
				$predesignInput->selectOptionByValue($Product->predesign_id);
			}
			if (!empty($Product->predesign_id_back)){
				$predesignInput->selectOptionByValue($Product->predesign_id_back);
			}
			$predesignInput = 'Choose Default Predesign To Show On Product ( Only if needed ):' . $predesignInput->draw() . '<br />';
			$predesignInputBack = 'Choose Default Predesign To Show On Product ( Only if needed ):' . $predesignInputBack->draw() . '<br />';
		}
		
		$subTabObj = htmlBase::newElement('tabs')->setId('tab_productDesigner_editableAreas_tabs');
		
		$subTabObj->addTabHeader('tab_productDesigner_editableAreas_tab_front', array('text' => 'Front'))
		->addTabPage('tab_productDesigner_editableAreas_tab_front', array(
			'text' => 'Editable Area Size In Inches On Real Product:' . $sizeInputs . '<br />' . 
			          $predesignInput . '<br />' . 
			          'Draw Editable Area ( predesigns will be center-top aligned in the area ):' . $hiddenInputs . '<br />' . 
			          '<div style="position:relative;">' . $productImageEditable->draw() . '</div>'
		));
		
		$subTabObj->addTabHeader('tab_productDesigner_editableAreas_tab_back', array('text' => 'Back'))
		->addTabPage('tab_productDesigner_editableAreas_tab_back', array(
			'text' => 'Editable Area Size In Inches On Real Product:' . $sizeInputsBack . '<br />' . 
			          $predesignInputBack . '<br />' . 
			          'Draw Editable Area ( predesigns will be center-top aligned in the area ):' . $hiddenInputsBack . '<br />' . 
			          '<div style="position:relative;">' . $productImageEditableBack->draw() . '</div>'
		));
		
		$tabObj->addTabHeader('tab_productDesigner_editableAreas', array('text' => 'Editable Areas'))
		->addTabPage('tab_productDesigner_editableAreas', array('text' => $subTabObj->draw()));
	}
	
	public function buildDesignControlsTab(&$tabObj, &$Product){
		global $appExtension;
		/**
		 * Inputs for allowing text entry on the customer side product designer --BEGIN--
		 */
		$designControlText = htmlBase::newElement('checkbox')->addClass('designControl')->setName('design_control[]')
		->setId('design_control_text')
		->val('text')
		->setLabelPosition('after')
		->setLabel('Allow adding text');
		
		$designControlText_input1 = htmlBase::newElement('input')->setName('design_control_text_input[max_entries]');
		$designControlText_input2 = htmlBase::newElement('input')->setName('design_control_text_input[max_chars]');
		$designControlText_input3 = htmlBase::newElement('input')->setName('design_control_text_input[cost]');
		
		$designControlText_inputs = htmlBase::newElement('div')->setId('design_control_text_inputs')->hide();
		
		$designControlText_inputs_table = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
		$designControlText_inputs_table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Max number of text entries:'),
				array('addCls' => 'main', 'text' => $designControlText_input1),
			)
		));
		$designControlText_inputs_table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Max number of characters per entry:'),
				array('addCls' => 'main', 'text' => $designControlText_input2),
			)
		));
		$designControlText_inputs_table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Cost per entry:'),
				array('addCls' => 'main', 'text' => $designControlText_input3),
			)
		));
		$designControlText_inputs->append($designControlText_inputs_table);
		/**
		 * Inputs for allowing text entry on the customer side product designer --END--
		 */

		/**
		 * Inputs for allowing clipart entry on the customer side product designer --BEGIN--
		 */
		$designControlClipart = htmlBase::newElement('checkbox')->addClass('designControl')->setName('design_control[]')
		->setId('design_control_clipart')
		->val('clipart')
		->setLabelPosition('after')
		->setLabel('Allow adding clipart');
		
		$designControlClipart_input1 = htmlBase::newElement('input')->setName('design_control_clipart_input[max_entries]');
		$designControlClipart_input2 = htmlBase::newElement('input')->setName('design_control_clipart_input[cost]');
		
		$designControlClipart_inputs = htmlBase::newElement('div')->setId('design_control_clipart_inputs')->hide();
		
		$designControlClipart_inputs_table = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
		$designControlClipart_inputs_table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Max number of clipart allowed:'),
				array('addCls' => 'main', 'text' => $designControlClipart_input1),
			)
		));
		$designControlClipart_inputs_table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Cost per clipart:'),
				array('addCls' => 'main', 'text' => $designControlClipart_input2),
			)
		));
		$designControlClipart_inputs->append($designControlClipart_inputs_table);
		/**
		 * Inputs for allowing clipart entry on the customer side product designer --END--
		 */

		/**
		 * Inputs for allowing image entry on the customer side product designer --BEGIN--
		 */
		$designControlImage = htmlBase::newElement('checkbox')->addClass('designControl')->setName('design_control[]')
		->setId('design_control_image')
		->val('image')
		->setLabelPosition('after')
		->setLabel('Allow adding images');
		
		$designControlImage_input1 = htmlBase::newElement('input')->setName('design_control_image_input[max_entries]');
		$designControlImage_input2 = htmlBase::newElement('input')->setName('design_control_image_input[max_chars]');
		$designControlImage_input3 = htmlBase::newElement('input')->setName('design_control_image_input[cost]');

		$designControlImage_inputs = htmlBase::newElement('div')->setId('design_control_image_inputs')->hide();
		
		$designControlImage_inputs_table = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
		$designControlImage_inputs_table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Max number of images allowed:'),
				array('addCls' => 'main', 'text' => $designControlImage_input1),
			)
		));
		$designControlImage_inputs_table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Max size of image:'),
				array('addCls' => 'main', 'text' => $designControlImage_input2),
			)
		));
		$designControlImage_inputs_table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Cost per image:'),
				array('addCls' => 'main', 'text' => $designControlImage_input3),
			)
		));
		$designControlImage_inputs->append($designControlImage_inputs_table);
		/**
		 * Inputs for allowing image entry on the customer side product designer --END--
		 */

		$Qclasses = Doctrine_Query::create()
		->from('ProductDesignerPredesignClasses')
		->orderBy('class_name')
		->execute();
		$groupBoxes = array();
		if ($Qclasses->count() > 0){
			foreach($Qclasses->toArray() as $class){
				$groupBoxes[] = array(
					'value' => $class['class_id'],
					'label' => $class['class_name']
				);
			}
		}
	
		$designClassesInputs = htmlBase::newElement('checkbox')
		->addGroup(array(
			'separator' => array(
				'type' => 'table',
				'cols' => 5
			),
			'checked' => explode(',', $Product['product_designer_predesign_classes']),
			'name' => 'predesign_class[]',
			'data' => $groupBoxes
		));
		
		$designableInput = htmlBase::newElement('checkbox')->setName('product_designable')->setChecked(($Product['product_designable'] == '1'));
		
		$mainTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
		
		$infoPages = $appExtension->getExtension('infoPages');
		if ($infoPages !== false && $infoPages->isEnabled() === true){
			$sizingTableInput = htmlBase::newElement('selectbox')->setName('product_designer_size_chart_id');
			$pages = $infoPages->getInfoPage();

			foreach($pages as $pInfo){
				$sizingTableInput->addOption($pInfo['pages_id'], $pInfo['PagesDescription'][Session::get('languages_id')]['pages_title']);
			}
			$sizingTableInput->selectOptionByValue($Product['product_designer_size_chart_id']);
		
			$mainTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => '<b>Size Chart Page:</b> ' . $sizingTableInput->draw())
				)
			));
		}
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => '<b>Turn On Designer:</b> ' . $designableInput->draw())
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => '<b>Predesign Classes:</b> ' . $designClassesInputs->draw())
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => '<b>Design Control Sets</b>')
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => $designControlText->draw() . $designControlText_inputs->draw())
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => $designControlClipart->draw() . $designControlClipart_inputs->draw())
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => $designControlImage->draw() . $designControlImage_inputs->draw())
			)
		));
		
		$tabObj->addTabHeader('tab_productDesigner_controls', array('text' => 'Design Controls'))
		->addTabPage('tab_productDesigner_controls', array('text' => $mainTable->draw()));
	}
	
	public function buildImagesTab(&$tabObj, &$Product){
		global $appExtension;

/*		$images = array();
		foreach(array('light', 'dark') as $tone){
			foreach(array('front', 'back') as $loc){
				$images[] = htmlBase::newElement('input')
				->setType('file')
				->setLabel(ucfirst($loc) . ' ' . ucfirst($tone) . ' Image:')
				->setLabelPosition('before')
				->setName($tone . '_image_' . $loc);
			}
		}*/
		
		$addLightImageButton = htmlBase::newElement('button')
		->usePreset('install')
		->setText('Add Light Image Set')
		->addClass('addLightImageSet');
		
		$addDarkImageButton = htmlBase::newElement('button')
		->usePreset('install')
		->setText('Add Dark Image Set')
		->addClass('addDarkImageSet');
		
		$imagesHtml = '';

		$colorInput = htmlBase::newElement('input')
		->setType('text')
		->addClass('iColorPicker')
		->setId('product_designer_display_color')
		->setName('product_designer_display_color')
		->val($Product['product_designer_display_color']);
		
		$multiStore = $appExtension->getExtension('multiStore');
		if ($multiStore !== false && $multiStore->isEnabled() === true){
			$storesGroup = array();
			foreach($multiStore->getStoresArray() as $sInfo){
				$storesGroup[] = array(
					'label' => $sInfo['stores_name'],
					'labelPosition' => 'after',
					'value' => $sInfo['stores_id']
				);
			}

			$defaultInput = htmlBase::newElement('checkbox')
			->addGroup(array(
				'name' => 'designer_image_default[product][]',
				'addCls' => 'defaultSetSelector',
				'separator' => array(
					'type' => 'table',
					'cols' => 3
				),
				'checked' => explode(',', $Product['product_designer_default_set']),
				'data' => $storesGroup
			));
		}else{
			$defaultInput = htmlBase::newElement('radio')
			->setName('designer_image_default')
			->val('product');
			if ($Product['product_designer_default_set'] == '1'){
				$defaultInput->setChecked(true);
			}
		}
		
		$toneInput = htmlBase::newElement('radio')
		->addGroup(array(
			'name' => 'product_designer_color_tone',
			'checked' => $Product['product_designer_color_tone'],
			'data' => array(
				array(
					'label' => 'Light',
					'value' => 'light'
				),
				array(
					'label' => 'Dark',
					'value' => 'dark'
				)
			)
		));
		
		$imagesHtml .= '<fieldset>
			<legend>Product Image Set</legend>
			<div style="position:relative">
				<table cellpadding="2" cellspacing="0" border="0">
				 <tr>
				  <td class="main" style="width:150px;">Color Tone:</td>
				  <td class="main">' . $toneInput->draw() . '</td>
				 </tr>
				 <tr>
				  <td class="main" style="width:150px;">Color:</td>
				  <td class="main">' . $colorInput->draw() . '</td>
				 </tr>
				 <tr>
				  <td class="main" style="width:150px;">Default Set:</td>
				  <td class="main">' . $defaultInput->draw() . '</td>
				 </tr>
				</table>
			</div>
		</fieldset>';
		
		if (!empty($Product['products_id'])){
			$Qimages = Doctrine_Query::create()
			->from('ProductDesignerProductImages')
			->where('products_id = ?', $Product['products_id'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qimages){
				$imgIdx = 0;
				foreach($Qimages as $image){
					if (isset($storesGroup)){
						$defaultInput = htmlBase::newElement('checkbox')
						->addGroup(array(
							'name' => 'designer_image_default[' . $image['color_tone'] . '_' . $imgIdx . '][]',
							'separator' => array(
								'type' => 'table',
								'cols' => 3
							),
							'addCls' => 'defaultSetSelector',
							'checked' => explode(',', $image['default_set']),
							'data' => $storesGroup
						));
					}else{
						$defaultInput = htmlBase::newElement('radio')
						->setName('designer_image_default')
						->val($image['color_tone'] . '_' . $imgIdx);
						if ($image['default_set'] == '1'){
							$defaultInput->setChecked(true);
						}
					}
					
					$colorInput = htmlBase::newElement('input')
					->setType('text')
					->addClass('iColorPicker')
					->setId('designer_image_' . $image['color_tone'] . '_color' . $imgIdx)
					->setName('designer_image_' . $image['color_tone'] . '_color[' . $imgIdx . ']')
					->val($image['display_color']);
					
					$backImage = htmlBase::newElement('uploadManagerInput')
					->setName('designer_image_' . $image['color_tone'] . '_back[' . $imgIdx . ']')
					->setFileType('image')
					->autoUpload(true)
					->showPreview(true)
					->showMaxUploadSize(true)
					->setPreviewFile($image['back_image']);
					
					$frontImage = htmlBase::newElement('uploadManagerInput')
					->setName('designer_image_' . $image['color_tone'] . '_front[' . $imgIdx . ']')
					->setFileType('image')
					->autoUpload(true)
					->showPreview(true)
					->showMaxUploadSize(true)
					->setPreviewFile($image['front_image']);
					
					$imagesHtml .= '<fieldset class="' . $image['color_tone'] . 'ImageContainer">
						<legend>' . ucfirst($image['color_tone']) . ' Image Set</legend>
						<div style="position:relative">
							<table cellpadding="2" cellspacing="0" border="0">
							 <tr>
							  <td class="main" style="width:150px;" valign="top">Front Image:</td>
							  <td class="main">' . $frontImage->draw() . '</td>
							 </tr>
							 <tr>
							  <td class="main" style="width:150px;" valign="top">Back Image:</td>
							  <td class="main">' . $backImage->draw() . '</td>
							 </tr>
							 <tr>
							  <td class="main" style="width:150px;">Color:</td>
							  <td class="main">' . $colorInput->draw() . '</td>
							 </tr>
							 <tr>
							  <td class="main" style="width:150px;">Default Set:</td>
							  <td class="main">' . $defaultInput->draw() . '</td>
							 </tr>
							</table>
							<a href="#" style="position:absolute;top:0px;right:0px;" class="ui-icon ui-icon-circle-close imageSetRemove"></a>
						</div>
					</fieldset>';
					$imgIdx++;
				}
			}
		}
		$tabObj->addTabHeader('tab_productDesigner_images', array('text' => 'Images'))
		->addTabPage('tab_productDesigner_images', array('text' => $addLightImageButton->draw() . '&nbsp;' . $addDarkImageButton->draw() . '<br /><br />' . $imagesHtml));
	}
}
?>