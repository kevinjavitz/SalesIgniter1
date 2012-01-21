<?php
/*
	Products Attributes Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class attributes_admin_products_new_product extends Extension_attributes {

	public function __construct(){
		parent::__construct();

		define('TEXT_BUTTON_NEW_OPTION', 'Add Option');
		define('TEXT_BUTTON_LOAD_SET', 'Load Set');
		define('TEXT_BUTTON_SAVE_SET', 'Save As Set');
		define('TEXT_BUTTON_UPDATE_SET', 'Update Set');
	}

	public function load(){
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
			'NewProductTabHeader',
			'NewProductTabBody',
			'NewProductAppendJavascriptFiles',
		), null, $this);
	}

	public function NewProductAppendJavascriptFiles(){
		global $App;
		$App->addJavascriptFile('extensions/attributes/admin/applications/products/javascript/new_product.js');
	}

	public function NewProductTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . 'Attributes' . '</span></a></li>';
	}

	public function NewProductTabBody(&$pInfo){
		$newOptionButton = htmlBase::newElement('button')
		->usePreset('install')
		->css(array('float' => 'left'))
		->setId('newOption')
		->setText(sysLanguage::get('TEXT_BUTTON_NEW_OPTION'));

		$loadSetButton = htmlBase::newElement('button')
		->usePreset('load')
		->setId('loadSet')
		->setText(TEXT_BUTTON_LOAD_SET);

		$buttonBar = htmlBase::newElement('div')
		->css(array(
			'text-align' => 'right'
		))
		->append($newOptionButton)->append($loadSetButton);

		$currentGroup = htmlBase::newElement('fieldset')
		->append(htmlBase::newElement('legend')->html('Current Group'));

		$currentGroupDiv = htmlBase::newElement('div')->attr('id', 'currentGroup');
		if (isset($pInfo['products_id'])){
			$groupTable = $this->getGroupTable($pInfo['products_id']);
			if ($groupTable !== false){
				$currentGroupDiv->append($groupTable);
			}
		}
		$currentGroup->append($currentGroupDiv);

		$currentOptions = htmlBase::newElement('ul')
		->css(array(
			'list-style' => 'none',
			'padding' => 0,
			'margin' => 0
		))
		->attr('id', 'currentOptions');

		if (isset($pInfo['products_id'])){
			$ProductsAttributes = Doctrine_Query::create()
			->from('ProductsAttributes a')
			->leftJoin('a.ProductsAttributesViews av')
			->where('a.products_id = ?', $pInfo['products_id'])
			->andWhere('a.groups_id is null')
			->orderBy('a.sort_order');

			$ProductsAttributes = $ProductsAttributes->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$Attributes = array();
			foreach($ProductsAttributes as $attribute){
				$Attributes[$attribute['options_id']][$attribute['options_values_id']] = array(
					'options_values_price'    => $attribute['options_values_price'],
					'options_values_image'    => $attribute['options_values_image'],
					'price_prefix'            => $attribute['price_prefix'],
					'ProductsAttributesViews' => $attribute['ProductsAttributesViews']
				);
			}

			foreach($Attributes as $optionId => $Values){
				$optionTable = $this->getOptionTable($optionId, $Values);
				if ($optionTable !== false){
					$liObj = htmlBase::newElement('li')
					->attr('id', 'option_' . $optionId . '_sort')
					->css(array(
						'padding' => '.5em'
					))
					->append($optionTable);
					$currentOptions->append($liObj);
				}
			}
		}

		return '<div id="tab_' . $this->getExtensionKey() . '">' .
			$buttonBar->draw() .
			'<hr />' .
			$currentGroup->draw() .
			'<hr />' .
			$currentOptions->draw() .
		'</div>';
	}

	public function getGroupTable($productsId = null){
		global $typeNames;
		if (is_null($productsId) === false){
			$ProductsAttributes = Doctrine_Query::create()
			->from('ProductsAttributes a')
			->leftJoin('a.ProductsAttributesViews av')
			->where('a.products_id = ?', $productsId)
			->andWhere('a.groups_id is not null');

			$ProductsAttributes = $ProductsAttributes->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$Attributes = array();
			foreach($ProductsAttributes as $attribute){
				$Attributes[$attribute['groups_id']][$attribute['options_id']][$attribute['options_values_id']] = array(
					'options_values_price'    => $attribute['options_values_price'],
					'options_values_image'    => $attribute['options_values_image'],
					'price_prefix'            => $attribute['price_prefix'],
					'use_inventory'           => $attribute['use_inventory'],
					'purchase_types'          => $attribute['purchase_types'],
					'ProductsAttributesViews' => $attribute['ProductsAttributesViews']
				);
			}

			if (isset($attribute['groups_id'])){
				$groupId = $attribute['groups_id'];
			}else{
				$groupId = 0;
			}
		}else{
			$groupId = (isset($_POST['option_group']) ? $_POST['option_group'] : 0);
		}

		if ($groupId > 0){
			$Group = Doctrine_Query::create()
			->from('ProductsOptionsGroups g')
			->where('g.products_options_groups_id = ?', $groupId)
			->fetchOne();
			if ($Group){
				$Group = $Group->toArray(true);
				//echo '<pre>';print_r($Group);echo '</pre>';
				$GroupContainer = htmlBase::newElement('div')
				->addClass('ui-widget ui-widget-content ui-corner-all')
				->attr('id', 'group_' . $Group['products_options_groups_id'])
				->css('padding', '.2em')
				->append(htmlBase::newElement('div')
					->css('padding', '.5em')
					->addClass('ui-widget ui-widget-header')
					->html($Group['products_options_groups_name'])
				)->append(new htmlElement('br'));

				$prefixMenu = htmlBase::newElement('selectbox')
				->addOption('-', '-')
				->addOption('+', '+')
				->selectOptionByValue('+');

				$priceInput = htmlBase::newElement('input')
				->attr('size', 8);

				$inventoryCheckbox = htmlBase::newElement('checkbox')
				->val('1');

				$Qoptions = Doctrine_Query::create()
				->from('ProductsOptions o')
				->leftJoin('o.ProductsOptionsDescription od')
				->leftJoin('o.ProductsOptionsToProductsOptionsGroups o2g')
				->where('o2g.products_options_groups_id = ?', $Group['products_options_groups_id'])
				->andWhere('od.language_id = ?', Session::get('languages_id'))
				->execute()->toArray();
				if ($Qoptions){
					foreach($Qoptions as $oInfo){
						$OptionsTable = htmlBase::newElement('table')
						->css('width', '100%')
						->addClass('ui-widget ui-widget-content')
						->setCellPadding(2)
						->setCellSpacing(0);

						$Option = $oInfo;
						$OptionDescription = $Option['ProductsOptionsDescription'][Session::get('languages_id')];

						$optionId = $Option['products_options_id'];
						$useImage = $Option['use_image'];
						$useMultiImage = $Option['use_multi_image'];
						$updateProductImage = $Option['update_product_image'];
						$optionName = $OptionDescription['products_options_name'];

						$columns = array(
							array(
								'addCls' => 'ui-widget ui-state-hover',
								'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
								'text' => '<b>' . $optionName . '</b>'
							),
							array(
								'addCls' => 'ui-widget ui-state-hover',
								'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
								'text' => '<b>' . 'Price' . '</b>'
							),
							array(
								'addCls' => 'ui-widget ui-state-hover',
								'align' => 'left',
								'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
								'text' => '<b>' . 'Purchase Type' . '</b>'
							),
							array(
								'addCls' => 'ui-widget ui-state-hover',
								'align' => 'left',
								'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
								'text' => '<b>' . 'Allow Inventory Tracking' . '</b>'
							)
						);

						if ($useImage == '1'){
							if ($useMultiImage == '1'){
								$columns[] = array(
									'addCls' => 'ui-widget ui-state-hover',
									'align' => 'right',
									'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
									'text' => '<b>' . 'Add View' . '</b>'
								);
							}else{
								$columns[] = array(
									'addCls' => 'ui-widget ui-state-hover',
									'align' => 'right',
									'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
									'text' => '<b>' . 'Image' . '</b>'
								);
							}
						}

						$OptionsTable->addBodyRow(array(
							'columns' => $columns
						));

						$Qvalues = Doctrine_Query::create()
						->from('ProductsOptionsValues ov')
						->leftJoin('ov.ProductsOptionsValuesDescription ovd')
						->leftJoin('ov.ProductsOptionsValuesToProductsOptions v2o')
						->where('v2o.products_options_id = ?', $optionId)
						->andWhere('ovd.language_id = ?', Session::get('languages_id'))
						->execute()->toArray();
						if ($Qvalues){
							foreach($Qvalues as $vInfo){
								$Value = $vInfo;
								$ValueDescription = $Value['ProductsOptionsValuesDescription'][Session::get('languages_id')];

								$valueId = $Value['products_options_values_id'];
								$valueName = $ValueDescription['products_options_values_name'];

								$prefixMenu->setName('attributes_prefix[' . $groupId . '][' . $optionId . '][' . $valueId . ']');
								$priceInput->setName('attributes_price[' . $groupId . '][' . $optionId . '][' . $valueId . ']');
								$inventoryCheckbox->setName('attributes_inventory[' . $groupId . '][' . $optionId . '][' . $valueId . ']');

								if (isset($Attributes[$groupId][$optionId][$valueId])){
									$prefixMenu->selectOptionByValue($Attributes[$groupId][$optionId][$valueId]['price_prefix']);
									$priceInput->setValue($Attributes[$groupId][$optionId][$valueId]['options_values_price']);
									$inventoryCheckbox->setChecked($Attributes[$groupId][$optionId][$valueId]['use_inventory'] == '1');
								}

								$checkboxes = array();
								foreach($typeNames as $key => $typeName){
									$checkboxes[] = array(
										'label' => $typeName,
										'labelPosition' => 'after',
										'labelSeparator' => '&nbsp;',
										'value' => $key
									);
								}
								$purchaseTypeBoxes = htmlBase::newElement('checkbox')
								->addGroup(array(
									'name' => 'attributes_purchase_types[' . $groupId . '][' . $optionId . '][' . $valueId . '][]',
									'separator' => array(
										'type' => 'table',
										'cols' => 3
									),
									'checked' => (isset($Attributes[$groupId][$optionId][$valueId]) ? explode(',', $Attributes[$groupId][$optionId][$valueId]['purchase_types']) : null),
									'data' => $checkboxes
								));

								$columns = array(
									array(
										'addCls' => 'main',
										'attr' => array(
											'valign' => 'top'
										),
										'text' => $valueName
									),
									array(
										'addCls' => 'main',
										'attr' => array(
											'valign' => 'top'
										),
										'text' => $prefixMenu->draw() . '&nbsp;' . $priceInput->draw()
									),
									array(
										'addCls' => 'main',
										'attr' => array(
											'valign' => 'top'
										),
										'text' => $purchaseTypeBoxes->draw()
									),
									array(
										'addCls' => 'main',
										'attr' => array(
											'valign' => 'top'
										),
										'text' => $inventoryCheckbox->draw()
									)
								);

								if ($useImage == '1'){
									if ($useMultiImage == '1'){
										$addIcon = htmlBase::newElement('icon')
										->setType('circlePlus')
										->addClass('addImage')
										->attr('view_name', 'attributes_view_image_name[' . $groupId . '][' . $optionId . '][' . $valueId . '][]')
										->attr('upload_name', 'attributes_view_image_file[' . $groupId . '][' . $optionId . '][' . $valueId . '][]');

										$deleteIcon = htmlBase::newElement('icon')->setType('closeThick')->css(array(
											'float' => 'right',
											'position' => 'relative'
										));

										$html = $addIcon->draw();
										if (isset($Attributes[$groupId][$optionId][$valueId]['ProductsAttributesViews'])){
											foreach($Attributes[$groupId][$optionId][$valueId]['ProductsAttributesViews'] as $imgInfo){
												$html .= '<div style="padding:.2em;text-align:right;">' .
												'View Name: <input type="text" name="' . $addIcon->attr('view_name') . '" value="' . $imgInfo['view_name'] . '" />' .
												'&nbsp;&nbsp;&nbsp;&nbsp;' .
												'View Image: <input type="file" name="' . $addIcon->attr('upload_name') . '" />' .
												$deleteIcon->draw() .
												'<br />' .
											    'Current: ' . $imgInfo['view_image'] .
												'<input type="hidden" name="attributes_previous_images[' . $groupId . '][' . $optionId . '][' . $valueId . ']" value="' . $imgInfo['view_image'] . '" />' .
												'</div>';
											}
										}

										$columns[] = array(
											'addCls' => 'main',
											'align' => 'right',
											'attr' => array(
												'valign' => 'top'
											),
											'text' => $html
										);
									}else{
										$uploadInput = htmlBase::newElement('input')->setType('file')
										->setName('value_image[' . $groupId . '][' . $optionId . '][' . $valueId . ']');

										$html = $uploadInput->draw();
										if (isset($Attributes[$groupId][$optionId][$valueId])){
											$html .= '<br />' .
											         'Current: ' . $Attributes[$groupId][$optionId][$valueId]['options_values_image'] .
											         '<input type="hidden" name="attributes_previous_images[' . $groupId . '][' . $optionId . '][' . $valueId . ']" value="' . $Attributes[$groupId][$optionId][$valueId]['options_values_image'] . '" />';
										}

										$columns[] = array(
											'addCls' => 'main',
											'align' => 'right',
											'attr' => array(
												'valign' => 'top'
											),
											'text' => $html
										);
									}
								}

								$OptionsTable->addBodyRow(array(
									'columns' => $columns
								));
							}
						}

						$GroupContainer->append($OptionsTable);
						$GroupContainer->append(new htmlElement('br'));
					}
				}
				return $GroupContainer;
			}
		}
		return false;
	}

	public function getOptionTable($optionsId = null, $Values = null){
		global $typeNames;
		if (is_null($optionsId) === false){
			$optionId = $optionsId;
		}else{
			$optionId = $_POST['option'];
		}
		$html = '';
		$OptionContainer = false;
		if ($optionId > 0){
			$Option = Doctrine_Query::create()
			->from('ProductsOptions o')
			->leftJoin('o.ProductsOptionsDescription od')
			->leftJoin('o.ProductsOptionsValuesToProductsOptions v2o')
			->where('products_options_id = ?', $optionId)
			->fetchOne();
			if ($Option){
				$Option = $Option->toArray(true);
				$OptionDescription = $Option['ProductsOptionsDescription'][Session::get('languages_id')];

				$optionId = $Option['products_options_id'];
				$useImage = $Option['use_image'];
				$useMultiImage = $Option['use_multi_image'];
				$updateProductImage = $Option['update_product_image'];
				$optionName = $OptionDescription['products_options_name'];

				$OptionContainer = htmlBase::newElement('div')
				->addClass('ui-widget ui-widget-content ui-corner-all')
				->attr('id', 'option_' . $Option['products_options_id'])
				->css(array(
					'padding' => '.2em',
					'position' => 'relative'
				))
				->append(htmlBase::newElement('div')
					->css(array(
						'padding' => '.5em',
						'border-bottom' => '0px'
					))
					->addClass('ui-widget ui-widget-header')
					->html($Option['ProductsOptionsDescription'][Session::get('languages_id')]['products_options_name'])
				)
				->append(htmlBase::newElement('input')
					->setType('hidden')
					->setName('option_' . $Option['products_options_id'] . '_sort')
					->setValue('0')
				)
				->append(htmlBase::newElement('icon')
					->setType('closeThick')
					->css(array(
						'position' => 'absolute',
						'top' => '.8em',
						'right' => '1em'
					))
				)
				->append(htmlBase::newElement('icon')
					->setType('move')
					->css(array(
						'position' => 'absolute',
						'top' => '.8em',
						'right' => '2em'
					))
				);

				$prefixMenu = htmlBase::newElement('selectbox')
				->addOption('-', '-')
				->addOption('+', '+')
				->selectOptionByValue('+');

				$priceInput = htmlBase::newElement('input')
				->attr('size', 8);

				$inventoryCheckbox = htmlBase::newElement('checkbox')
				->val('1');

				$OptionsTable = htmlBase::newElement('table')
				->css('width', '100%')
				->addClass('ui-widget ui-widget-content')
				->setCellPadding(2)
				->setCellSpacing(0)
				->stripeRows('tableRowEven', 'tableRowOdd');

				$columns = array(
					array(
						'addCls' => 'ui-widget ui-state-hover',
						'align' => 'left',
						'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
						'text' => '<b>' . 'Value Name' . '</b>'
					),
					array(
						'addCls' => 'ui-widget ui-state-hover',
						'align' => 'left',
						'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
						'text' => '<b>' . 'Price' . '</b>'
					),
					array(
						'addCls' => 'ui-widget ui-state-hover',
						'align' => 'left',
						'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
						'text' => '<b>' . 'Purchase Type' . '</b>'
					),
					array(
						'addCls' => 'ui-widget ui-state-hover',
						'align' => 'left',
						'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
						'text' => '<b>' . 'Allow Inventory Tracking' . '</b>'
					)
				);

				if ($useImage == '1'){
					if ($useMultiImage == '1'){
						$columns[] = array(
							'addCls' => 'ui-widget ui-state-hover',
							'align' => 'right',
							'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
							'text' => '<b>' . 'Add View' . '</b>'
						);
					}else{
						$columns[] = array(
							'addCls' => 'ui-widget ui-state-hover',
							'align' => 'right',
							'css' => array('border-left' => '0px', 'border-right' => '0px', 'border-top' => '0px'),
							'text' => '<b>' . 'Image' . '</b>'
						);
					}
				}

				$OptionsTable->addHeaderRow(array(
					'columns' => $columns
				));

				$Values = Doctrine_Query::create()
				->from('ProductsOptionsValues ov')
				->leftJoin('ov.ProductsOptionsValuesDescription ovd')
				->leftJoin('ov.ProductsOptionsValuesToProductsOptions v2o')
				->where('v2o.products_options_id = ?', $optionId)
				->andWhere('ovd.language_id = ?', Session::get('languages_id'))
				->orderBy('v2o.sort_order')
				->execute()->toArray();
				if ($Values){
					foreach($Values as $vInfo){
						$Value = $vInfo;
						$ValueDescription = $Value['ProductsOptionsValuesDescription'][Session::get('languages_id')];

						$valueId = $Value['products_options_values_id'];
						$valueName = $ValueDescription['products_options_values_name'];

						$prefixMenu->setName('attributes_prefix[0][' . $optionId . '][' . $valueId . ']');
						$priceInput->setName('attributes_price[0][' . $optionId . '][' . $valueId . ']');
						$inventoryCheckbox->setName('attributes_inventory[0][' . $optionId . '][' . $valueId . ']');

						if (isset($_GET['pID'])){
							$QproductsAttributes = Doctrine_Query::create()
							->from('ProductsAttributes')
							->where('products_id = ?', $_GET['pID'])
							->andWhere('options_id = ?', $optionId)
							->andWhere('options_values_id = ?', $valueId)
							->andWhere('groups_id IS NULL')
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if ($QproductsAttributes){
								$prefixMenu->selectOptionByValue($QproductsAttributes[0]['price_prefix']);
								$priceInput->setValue($QproductsAttributes[0]['options_values_price']);
								$inventoryCheckbox->setChecked($QproductsAttributes[0]['use_inventory'] == '1');
							}
						}

						$checkboxes = array();
						foreach($typeNames as $key => $typeName){
							$checkboxes[] = array(
								'label' => $typeName,
								'labelPosition' => 'after',
								'labelSeparator' => '&nbsp;',
								'value' => $key
							);
						}
						$purchaseTypeBoxes = htmlBase::newElement('checkbox')
						->addGroup(array(
							'name' => 'attributes_purchase_types[0][' . $optionId . '][' . $valueId . '][]',
							'separator' => array(
								'type' => 'table',
								'cols' => 3
							),
							'checked' => (isset($QproductsAttributes) ? explode(',', $QproductsAttributes[0]['purchase_types']) : null),
							'data' => $checkboxes
						));

						$columns = array(
							array(
								'addCls' => 'main',
								'attr' => array(
									'valign' => 'top'
								),
								'text' => $valueName
							),
							array(
								'addCls' => 'main',
								'attr' => array(
									'valign' => 'top'
								),
								'text' => $prefixMenu->draw() . '&nbsp;' . $priceInput->draw()
							),
							array(
								'addCls' => 'main',
								'attr' => array(
									'valign' => 'top'
								),
								'text' => $purchaseTypeBoxes->draw()
							),
							array(
								'addCls' => 'main',
								'attr' => array(
									'valign' => 'top'
								),
								'text' => $inventoryCheckbox->draw()
							)
						);

						if ($useImage == '1'){
							if ($useMultiImage == '1'){
								$addIcon = htmlBase::newElement('icon')
								->setType('circlePlus')
								->addClass('addImage')
								->attr('view_name', 'attributes_view_image_name[0][' . $optionId . '][' . $valueId . '][]')
								->attr('upload_name', 'attributes_view_image_file[0][' . $optionId . '][' . $valueId . '][]');

								$deleteIcon = htmlBase::newElement('icon')->setType('closeThick')->css(array(
									'float' => 'right',
									'position' => 'relative'
								));

								$html = $addIcon->draw();
								if (isset($Value['ProductsAttributesViews'])){
									foreach($Value['ProductsAttributesViews'] as $imgInfo){
										$html .= '<div style="padding:.2em;text-align:right;">' .
										'View Name: <input type="text" name="' . $addIcon->attr('view_name') . '" value="' . $imgInfo['view_name'] . '" />' .
										'&nbsp;&nbsp;&nbsp;&nbsp;' .
										'View Image: <input type="file" name="' . $addIcon->attr('upload_name') . '" />' .
										$deleteIcon->draw() .
										'<br />' .
									    'Current: ' . $imgInfo['view_image'] .
										'<input type="hidden" name="attributes_previous_images[0][' . $optionId . '][' . $valueId . ']" value="' . $imgInfo['view_image'] . '" />' .
										'</div>';
									}
								}

								$columns[] = array(
									'addCls' => 'main',
									'align' => 'right',
									'attr' => array(
										'valign' => 'top'
									),
									'text' => $html
								);
							}else{
								$uploadInput = htmlBase::newElement('input')->setType('file')
								->setName('attributes_value_image[0][' . $optionId . '][' . $valueId . ']');

								$html = $uploadInput->draw();
								$html .= '<br />' .
								         'Current: ' . $vInfo['options_values_image'] .
								         '<input type="hidden" name="attributes_previous_images[0][' . $optionId . '][' . $valueId . ']" value="' . $vInfo['options_values_image'] . '" />';
							}

								$columns[] = array(
									'addCls' => 'main',
									'align' => 'right',
									'attr' => array(
										'valign' => 'top'
									),
									'text' => $html
								);
						}

						$OptionsTable->addBodyRow(array(
							'columns' => $columns
						));
					}
				}

				$OptionContainer->append($OptionsTable);
			}

		}
		return $OptionContainer;
	}

	public function getInventoryEntry($data){
		$Qinventory = Doctrine_Query::create()
		->select('type, track_method, inventory_id')
		->from('ProductsInventory')
		->where('products_id = ?', $data['productId'])
		->andWhere('controller = ?', 'attribute');

		if (isset($data['purchaseType'])){
			$Qinventory->andWhere('type = ?', $data['purchaseType']);
		}

		if (isset($data['trackMethod'])){
			$Qinventory->andWhere('track_method = ?', $data['trackMethod']);
		}

		$Result = $Qinventory->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return $Result;
	}

	public function getAttributeQuantity($data){
		global $appExtension;
		$ProductsInventory = $this->getInventoryEntry($data);

		$qtyInfo = array();
		if ($ProductsInventory){
			$inventory = $ProductsInventory[0];
			$attributesPermutations = $this->permutateAttributesFromString($data['aID_string']);

			$QinventoryQuantity = Doctrine_Query::create()
			->from('ProductsInventoryQuantity')
			->where('inventory_id = ?', $inventory['inventory_id'])
			->andWhereIn('attributes', $attributesPermutations);

			if ($appExtension->isInstalled('inventoryCenters') === true){
				$QinventoryQuantity->andWhere('inventory_center_id <= ?', '0');
				if ($appExtension->isInstalled('multiStore')){
					$QinventoryQuantity->andWhere('inventory_store_id <= ?', '0');
				}
			}
			$qtyResult = $QinventoryQuantity->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($qtyResult){
				$qtyInfo = $qtyResult[0];
			}
		}
		return $qtyInfo;
	}

	public function getAttributeBarcodes($data){
		global $appExtension;
		$ProductsInventory = $this->getInventoryEntry($data);

		$barcodeData = array();
		if ($ProductsInventory){
			$attributesPermutations = $this->permutateAttributesFromString($data['aID_string']);
			foreach($ProductsInventory as $inventory){
				$Qbarcodes = Doctrine_Query::create()
				->from('ProductsInventoryBarcodes')
				->where('inventory_id = ?', $inventory[0]['inventory_id'])
				->andWhereIn('attributes', $attributesPermutations)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qbarcodes){
					foreach($Qbarcodes as $bInfo){
						$barcodeData[] = array(
							'barcode_id' => $bInfo['barcode_id'],
							'barcode' => $bInfo['barcode'],
							'status' => $bInfo['status']
						);
					}
				}
			}
		}
		return $barcodeData;
	}

	public function getInventoryTable($settings){
		global $appExtension;
		$purchaseType = $settings['purchaseType'];
		$productId = $settings['productId'];
		$trackMethod = $settings['trackMethod'];

		$Attributes = $appExtension->getExtension('attributes');
		$selectedAttributes = array();
		foreach($settings['options'] as $optionId => $valueId){
			$Query = attributesUtil::getAttributes(null, $optionId, $valueId, $purchaseType);
			$Result = attributesUtil::organizeAttributeArray($Query);

			$selectedAttributes[] = $Result;
		}

		$aID_String = '';
		$InventoryStringArr = array();
		$tableHeadingDescription = array();
		foreach($selectedAttributes as $aInfo){
			foreach($aInfo as $optionId => $oInfo){
				$aID_String .= '{' . $optionId . '}' . $oInfo['ProductsOptionsValues'][0]['options_values_id'];
				$InventoryStringArr[] = $oInfo['options_name'];
				$InventoryStringArr[] = $oInfo['ProductsOptionsValues'][0]['options_values_name'];
			}
			$tableHeadingDescription[] = implode(' > ', $InventoryStringArr);
			$InventoryStringArr = array();
		}

		$invItemInfo = array(
			'productId'    => $productId,
			'purchaseType' => $purchaseType,
			'trackMethod'  => $trackMethod,
			'aID_string'   => $aID_String
		);

		$tableData = array(
			'purchaseType' => $purchaseType,
			'attributeString' => $aID_String,
			'dataSet' => (isset($settings['dataSet']) ? $settings['dataSet'] : array())
		);

		if ($trackMethod == 'barcode'){
			$barcodeTable = buildBarcodeEntryTable(array(
				'purchaseType' => $purchaseType,
				'attributeString' => $aID_String
			));

			$ajaxNotice = htmlBase::newElement('div')
			->addClass('main')
			->html('<small>*Barcodes are dynamically added and do not require the product to be updated</small>');

			$currentBarcodesTable = buildCurrentBarcodesTable($tableData);

			$tableHtml = htmlBase::newElement('div')
			->append($barcodeTable)
			->append($ajaxNotice)
			->append(htmlBase::newElement('hr'))
			->append($currentBarcodesTable);
		}else{
	     	$quantityTable = buildQuantityTable($tableData);

			$tableHtml = $quantityTable;
		}

		$deleteIcon = htmlBase::newElement('icon')
		->setType('closeThick')
		->css(array(
			'float' => 'right'
		))
		->attr('data-purchase_type', $purchaseType)
		->attr('data-product_id', $productId)
		->attr('data-attribute_string', $aID_String)
		->attr('data-track_method', $trackMethod);

		return '<div class="ui-widget ui-widget-content ui-corner-all" style="margin-bottom:1em;">' .
			'<div class="ui-widget-header" style="padding:.5em;">' .
				$deleteIcon->draw() .
				'<b>' . implode(' + ', $tableHeadingDescription) . '</b>' .
			'</div>' .
			$tableHtml->draw() .
		'</div>';
	}
}
?>