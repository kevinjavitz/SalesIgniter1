<?php
/*
	Addon Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class productAddons_admin_products_new_product extends Extension_productAddons {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'NewProductTabHeader',
			'NewProductTabBody'
		), null, $this);
	}


	public function get_category_tree_list($parent_id = '0', $checked = false, $include_itself = true, $ProdID){

		$langId = Session::get('languages_id');
		$excludedList = array();
		$excludedList[] = $ProdID;
		$catList = '';
		$QCategories = Doctrine_Query::create()
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('cd.language_id = ?', $langId)
		->andWhere('c.parent_id = ?', $parent_id)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($QCategories as $cat){
			$catList .= '<optgroup label="' . $cat['CategoriesDescription'][0]['categories_name'] . '">';
			$QProducts = Doctrine_Query::create()
			->from('Products p')
			->leftJoin('p.ProductsDescription pd')
			->leftJoin('p.ProductsToCategories p2c')
			->where('pd.language_id = ?', $langId)
			->andWhere('p2c.categories_id = ?', $cat['categories_id'])
			->andWhereNotIn('p.products_id', $excludedList)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			foreach($QProducts as $products){
				$exclude = false;

				$QProductsAdd = Doctrine_Query::create()
				->from('Products p')
				->where('products_id = ?', $products['products_id'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$addon_products = explode(',', $QProductsAdd[0]['addon_products']);
				foreach($addon_products as $AddonProduct){
					if($AddonProduct == $ProdID){
						$exclude = true;
					}
				}
				$optional_addon_products = explode(',', $QProductsAdd[0]['optional_addon_products']);
				foreach($optional_addon_products as $AddonProduct){
					if($AddonProduct == $ProdID){
						$exclude = true;
					}
				}
				if(!$exclude){
					$catList .= '<option value="' . $products['products_id'] . '">(' . $products['products_model'] . ") " . $products['ProductsDescription'][0]['products_name'] . '</option>';
				}
			}
			
			if (tep_childs_in_category_count($cat['categories_id']) > 0){
				$catList .= $this->get_category_tree_list($cat['categories_id'], $checked, false);
			}
			$catList .= '</optgroup>';
		}
		return $catList;
	}

	public function no_category($ProdID){
		$langId = Session::get('languages_id');
		$catList = '<optgroup label="nocategory">';
		$excludedList = array();
		$excludedList[] = $ProdID;

		$QProducts = Doctrine_Query::create()
			->from('Products p')
			->leftJoin('p.ProductsDescription pd')
			->leftJoin('p.ProductsToCategories p2c')
			->where('pd.language_id = ?', $langId)
			->andWhereNotIn('p.products_id', $excludedList)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach($QProducts as $products){
		    if(count($products['ProductsToCategories']) == 0){
			    $exclude = false;

			    $QProductsAdd = Doctrine_Query::create()
				    ->from('Products p')
				    ->where('products_id = ?', $products['products_id'])
				    ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			    $addon_products = explode(',', $QProductsAdd[0]['addon_products']);
			    foreach($addon_products as $AddonProduct){
				    if($AddonProduct == $ProdID){
					    $exclude = true;
				    }
			    }
			    $optional_addon_products = explode(',', $QProductsAdd[0]['optional_addon_products']);
			    foreach($optional_addon_products as $AddonProduct){
				    if($AddonProduct == $ProdID){
					    $exclude = true;
				    }
			    }
			    if(!$exclude){
					$catList .= '<option value="' . $products['products_id'] . '">(' . $products['products_model'] . ") " . $products['ProductsDescription'][0]['products_name'] . '</option>';
			    }
		    }
		}
		$catList .= '</optgroup>';
		return $catList;
	}

	public function NewProductTabHeader($Product) {

		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('TAB_PAY_ADDON_PRODUCTS') . '</span></a></li>';
	}

	public function NewProductTabBody($Product){
		$table = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css('width', '100%');
		$table->addHeaderRow(array(
			'columns' => array(
				array('attr' => array('width' => '40%'), 'text' => sysLanguage::get('TAB_PAY_ADDON_PRODUCTS_TEXT_PRODUCTS')),
				array('text' => '&nbsp;'),
				array('attr' => array('width' => '40%'), 'text' => sysLanguage::get('TAB_PAY_ADDON_PRODUCTS_TEXT_ADDONS'))
			)
		));
		
		$addonProducts = '';
		//print_r($pInfo);

		if(!empty($Product->addon_products)){
			$products = explode(',', $Product->addon_products);
		}

		if(isset($products)){
			foreach($products as $pID){
                $addonProducts .= '<div><a href="#" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' .tep_get_products_name($pID) . '</span>' . tep_draw_hidden_field('addon_products[]', $pID) . '</div>';
            }
		}

		
		$table->addBodyRow(array(
			'columns' => array(
				array(
					'addCls' => 'main',
					'attr' => array(
						'valign' => 'top'
					), 
					'text' => '<select size="30" style="width:100%;" id="productListAddons">' .  $this->no_category($Product->products_id)  . $this->get_category_tree_list('0',false,true,$Product->products_id) . '</select>'
				),
				array(
					'addCls' => 'main',
					'text' => '<button type="button" id="moveRightAddon"><span>&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</span></button>'
				),
				array(
					'addCls' => 'main',
					'attr' => array(
						'id' => 'addons',
						'valign' => 'top'
					), 
					'text' => $addonProducts
				)
			)
		));

		$addonTables = $table->draw();

		$tableOptional = htmlBase::newElement('table')
			->setCellPadding(3)
			->setCellSpacing(0)
			->css('width', '100%');

		$tableOptional->addHeaderRow(array(
				'columns' => array(
					array('attr' => array('width' => '40%'), 'text' => sysLanguage::get('TAB_PAY_ADDON_PRODUCTS_TEXT_PRODUCTS')),
					array('text' => '&nbsp;'),
					array('attr' => array('width' => '40%'), 'text' => sysLanguage::get('TAB_PAY_ADDON_PRODUCTS_TEXT_ADDONS_OPTIONAL'))
				)
			));

		$addonProducts = '';
		if(!empty($Product->optional_addon_products)){
			$productsAdd = explode(',', $Product->optional_addon_products);
		}

		if(isset($productsAdd)){
			foreach($productsAdd as $pID){
				$addonProducts .= '<div><a href="#" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' . tep_get_products_name($pID) . '</span>' . tep_draw_hidden_field('addon_products[]', $pID) . '</div>';
			}
		}

		$tableOptional->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'main',
						'attr' => array(
							'valign' => 'top'
						),
						'text' => '<select size="30" style="width:100%;" id="productListOptional">' . $this->no_category($Product->products_id) . $this->get_category_tree_list('0',false,true,$Product->products_id) . '</select>'
					),
					array(
						'addCls' => 'main',
						'text' => '<button type="button" id="moveOptionalRightAddon"><span>&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</span></button>'
					),
					array(
						'addCls' => 'main',
						'attr' => array(
							'id' => 'optionaladdons',
							'valign' => 'top'
						),
						'text' => $addonProducts
					)
				)
			));
		$addonTables .= '<br/>'. $tableOptional->draw();

		return '<div id="tab_' . $this->getExtensionKey() . '">' . $addonTables . '</div>';
	}
}
?>