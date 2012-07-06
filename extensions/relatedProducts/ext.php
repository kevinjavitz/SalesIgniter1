<?php
/*
	Related Products Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_relatedProducts extends ExtensionBase {

	public function __construct(){
		parent::__construct('relatedProducts');
	}

	public function init(){
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
			'IndexProductsListingAfterListing',
			'ShoppingCartAfterListing',
			'ProductInfoTabHeader',
			'ProductInfoAfterPurchaseTypes',
			'ProductInfoTabBody',
			'ScrollerRelatedQueryBeforeExecute'
		), null, $this);
	}
	
	public function ScrollerRelatedQueryBeforeExecute(&$Query)
	{
		global $product;
		
		if ($product!==null) {			
			$productsArr = explode(',', $product->productInfo['related_products']);
			$Query->where('products_status = ?','1')
				->andWhereIn('products_id', $productsArr)
				->limit((int)sysConfig::get('EXTENSION_RELATED_PRODUCTS_DISPLAY_NUMBER'));
		} else {
			//Force empty result (related products can only show if we're browsing some product)
			$Query->where('0');
		}
	}

	public function getListing($relatedProducts){
		$productsArr = explode(',', $relatedProducts);
		if (sizeof($productsArr) > 0){
			$Qproducts = Doctrine_Query::create()
			->select('products_id')
			->from('Products')
			->where('products_status=?','1')
			->andWhereIn('products_id', $productsArr)
			->limit((int)sysConfig::get('EXTENSION_RELATED_PRODUCTS_DISPLAY_NUMBER'));

			if (sysConfig::get('EXTENSION_RELATED_PRODUCTS_LISTING_METHOD') == 'Column'){
				$productListing = new productListing_col();
			}else{
				$productListing = new productListing_row();
			}
			$productListing->disablePaging()
			->disableSorting()
			->dontShowWhenEmpty()
			->setQuery($Qproducts);

			return $productListing;
		}
		return false;
	}

	public function IndexProductsListingAfterListing($categoryId){
		$contentRelated = '';
		$Qcheck = Doctrine_Query::create()
		->select('related_products')
		->from('Categories')
		->where('categories_id = ?', $categoryId)
		->execute();
		if ($Qcheck->count() > 0){
			$listing = $this->getListing($Qcheck[0]->related_products);
			$contentRelated .= $listing->draw();
			if (!empty($contentRelated)){
				$boxTemplate = new Template('module.tpl', 'modules');

				$boxTemplate->setVars(array(
					'box_id'     => 'relatedProducts',
					'boxHeading' => sysLanguage::get('PRODUCT_LISTING_RELATED'),
					'boxContent' => $contentRelated
				));

				return $boxTemplate->parse();
			}
		}
		return;
	}

	public function ShoppingCartAfterListing(){
		global $ShoppingCart;

		$contentRelated = '';
		$productArr = '';
		foreach($ShoppingCart->getProducts() as $cartProduct){
			$pClass = new product($cartProduct->getIdString());
			$productArr .= $pClass->productInfo['related_products'] .', ';
		}
		$productArr = substr($productArr, 0, strlen($productArr) -2);
		$listing = $this->getListing($productArr);
		$contentRelated .= $listing->draw();
		if (!empty($contentRelated)){
			$boxTemplate = new Template('box.tpl', sysConfig::getDirFsCatalog() . 'extensions/templateManager/widgetTemplates');

			$boxTemplate->setVars(array(
				'box_id'     => 'relatedProducts',
				'boxHeading' => sysLanguage::get('PRODUCT_LISTING_RELATED'),
				'boxContent' => $contentRelated
			));

			return $boxTemplate->parse();
		}
		return;
	}


	public function ProductInfoTabHeader(&$product){
		if(sysconfig::get('EXTENSION_RELATED_PRODUCTS_SHOW_ON_TAB') == 'True'){
			if(sysConfig::get('EXTENSION_RELATED_PRODUCTS_DISPLAY_TYPE') == 'Admin'){
				$relatedProducts = $product->productInfo['related_products'];
				if (!empty($relatedProducts)){
					return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('PRODUCT_LISTING_RELATED') . '</span></a></li>';
				}
			}else{
				/*This is a custom related products. Could be done by selecting the fields and relation between the fields(custom), manufacturer and category but are a lot of variables. The best solution for now is to just write the code*/
				/*There will always be a related product*/
				return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('PRODUCT_LISTING_RELATED') . '</span></a></li>';
			}
		}
		return;
	}

	public function ProductInfoAfterPurchaseTypes(&$product){
		global $App, $appExtension, $Template;
		$relatedProducts = '';
		if(sysconfig::get('EXTENSION_RELATED_PRODUCTS_SHOW_ON_TAB') == 'False'){
			if(sysConfig::get('EXTENSION_RELATED_PRODUCTS_DISPLAY_TYPE') == 'Admin'){
				$relatedProducts = $product->productInfo['related_products'];
			}else{
                $customNames = explode(',', sysConfig::get('EXTENSION_RELATED_PRODUCTS_CUSTOM_FIELDS'));
				$CustomFields = $appExtension->getExtension('customFields');
				if ($CustomFields !== false && $CustomFields->isEnabled() === true){
                    $customValues = array();
					$groups = $CustomFields->getFields($product->productInfo['products_id'], Session::get('languages_id'), false, false, false);
                    $i = 0;
					foreach($groups as $groupInfo){
						$fieldsToGroups = $groupInfo['ProductsCustomFieldsToGroups'];
						foreach($fieldsToGroups as $fieldToGroup){
							$name = $fieldToGroup['ProductsCustomFields']['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name'];
							$value = $fieldToGroup['ProductsCustomFields']['ProductsCustomFieldsToProducts'][0]['value'];

							if(in_array($name,$customNames)){
								$v = explode(";",$value);
                                $customValues[$i] = $v[0];
								$i++;
							}
						}
						if(tep_not_null($customValues)){
							break;
						}
					}

					$Product = Doctrine_Core::getTable('Products')->find($product->productInfo['products_id']);
					$ProductToCategories = $Product->ProductsToCategories;

					//echo "<pre>". print_r($ProductToCategories);
					foreach($ProductToCategories as $category){
						$QproductFromCat = Doctrine_Query::create()
							->select('p.products_id, c.categories_id')
							->from('Products p')
							->leftJoin('p.ProductsToCategories c')
							->where('c.categories_id = ?', $category['categories_id'])
							->limit((int)sysConfig::get('EXTENSION_RELATED_PRODUCTS_DISPLAY_NUMBER'))
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						if (count($QproductFromCat) > 0){

							$products = Array();
							foreach($QproductFromCat as $pInfo){
								if($pInfo['products_id'] != $product->productInfo['products_id']){
									$products[] = $pInfo['products_id'];
								}
							}

							$Query = Doctrine_Query::create()
								->select('g.group_name, f.*, fd.field_name, f2p.product_id, f2p.value, f2g.sort_order')
								->from('ProductsCustomFieldsGroups g')
								->leftJoin('g.ProductsCustomFieldsGroupsToProducts g2p')
								->leftJoin('g.ProductsCustomFieldsToGroups f2g')
								->leftJoin('f2g.ProductsCustomFields f')
								->leftJoin('f.ProductsCustomFieldsDescription fd')
								->leftJoin('f.ProductsCustomFieldsToProducts f2p')
								->where('fd.field_name is not null')
								->andWhere('fd.language_id = ?', (int)Session::get('languages_id'))
								->andWhereIn('f2p.product_id', $products)
                                ->andWhereIn('fd.field_name', $customNames)
                                ->andWhereIn('f2p.value', $customValues)
								->orderBy('f2g.sort_order')
								->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

							if (count($Query) > 0){
								foreach($Query[0]['ProductsCustomFieldsToGroups'][0]['ProductsCustomFields']['ProductsCustomFieldsToProducts'] as $qp){
									$relatedProducts .= $qp['product_id'].",";
								}
							}

                            $Query = Doctrine_Query::create()
                                ->select('g.products_options_groups_name, f.*, fd.products_options_name, f.product_id, f2p.products_options_values_name, f2g.sort_order')
                                ->from('ProductsOptionsGroups g')
                                ->leftJoin('g.ProductsOptionsToProductsOptionsGroups g2p')
                                ->leftJoin('g.ProductsOptionsValuesToProductsOptions f2g')
                                ->leftJoin('f2g.ProductsAttributes f')
                                ->leftJoin('f.ProductsOptionsDescription fd')
                                ->leftJoin('f.ProductsOptionsValuesDescription f2p')
                                ->where('fd.products_options_values_name is not null')
                                ->andWhere('fd.language_id = ?', (int)Session::get('languages_id'))
                                ->andWhereIn('f.product_id', $products)
                                ->andWhereIn('fd.products_options_name', $customNames)
                                ->andWhereIn('f2p.products_options_values_name', $customValues)
                                ->orderBy('f2g.sort_order')
                                ->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

                            if (count($Query) > 0){
                                foreach($Query[0]['ProductsOptionsGroups'][0]['ProductsAttributes'] as $qp){
                                    $relatedProducts .= $qp['product_id'].",";
                                }
                            }
						}
					}

					$relatedProducts = substr($relatedProducts, 0, strlen($relatedProducts)-1);
					//if number of related products is less than maximum than add the highest priced dvd from category?
					if(empty($relatedProducts)){
						foreach($ProductToCategories as $category){

							$QproductFromCat = Doctrine_Query::create()
								->select('p.products_id, c.categories_id')
								->from('Products p')
								->leftJoin('p.ProductsToCategories c')
								->where('c.categories_id = ?', $category['categories_id'])
								->orderBy('p.products_price DESC')
								->limit((int)sysConfig::get('EXTENSION_RELATED_PRODUCTS_DISPLAY_NUMBER'))
								->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if($QproductFromCat){
								foreach($QproductFromCat as $qp)
									$relatedProducts .= $qp['products_id'].",";
								$relatedProducts = substr($relatedProducts,0, strlen($relatedProducts)-2);
							}
						}
					}
				}
			}

		}

		if(!empty($relatedProducts)){
			$listing = $this->getListing($relatedProducts);
			$boxTemplate = new Template('box.tpl', sysConfig::getDirFsCatalog() . 'extensions/templateManager/widgetTemplates');

			$boxTemplate->setVars(array(
					'box_id'     => 'relatedProducts',
					'boxHeading' => sysLanguage::get('PRODUCT_LISTING_RELATED'),
					'boxContent' => $listing->draw()
				));

			return $boxTemplate->parse();
		}else{
			return '';
		}
	}

	public function ProductInfoTabBody(&$product){
		global $App, $appExtension, $Template;

		$relatedProducts = '';
		if(sysconfig::get('EXTENSION_RELATED_PRODUCTS_SHOW_ON_TAB') == 'True'){
			if(sysConfig::get('EXTENSION_RELATED_PRODUCTS_DISPLAY_TYPE') == 'Admin'){
				$relatedProducts = $product->productInfo['related_products'];
			}else{
                $customNames = explode(',', sysConfig::get('EXTENSION_RELATED_PRODUCTS_CUSTOM_FIELDS'));
                $customAttributes = explode(',', sysConfig::get('EXTENSION_RELATED_PRODUCTS_ATTRIBUTES'));
                $CustomFields = $appExtension->getExtension('customFields');
                $attributes = $appExtension->getExtension('attributes');
				if ($CustomFields !== false && $CustomFields->isEnabled() === true){
                    $customValues = array();
					$groups = $CustomFields->getFields($product->productInfo['products_id'], Session::get('languages_id'), false, false, false);
                    $i = 0;
					foreach($groups as $groupInfo){
						$fieldsToGroups = $groupInfo['ProductsCustomFieldsToGroups'];
						foreach($fieldsToGroups as $fieldToGroup){
							$name = $fieldToGroup['ProductsCustomFields']['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name'];
							$value = $fieldToGroup['ProductsCustomFields']['ProductsCustomFieldsToProducts'][0]['value'];

							if(in_array($name,$customNames)){
                                $v = explode(";",$value);
                                $customValues[$i] = $v[0];
                                $i++;
							}
						}
						if(tep_not_null($customValues)){
							break;
						}
					}
                }

                if ($attributes !== false && $attributes->isEnabled() === true){

                }

                $Product = Doctrine_Core::getTable('Products')->find($product->productInfo['products_id']);
                $ProductToCategories = $Product->ProductsToCategories;

                //echo "<pre>". print_r($ProductToCategories);
                foreach($ProductToCategories as $category){
                    $QproductFromCat = Doctrine_Query::create()
                            ->select('p.products_id, c.categories_id')
                            ->from('Products p')
                            ->leftJoin('p.ProductsToCategories c')
                            ->where('c.categories_id = ?', $category['categories_id'])
                            ->limit((int)sysConfig::get('EXTENSION_RELATED_PRODUCTS_DISPLAY_NUMBER'))
                            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

                    if (count($QproductFromCat) > 0){

                        $products = Array();
                        foreach($QproductFromCat as $pInfo){
                            if($pInfo['products_id'] != $product->productInfo['products_id']){
                                $products[] = $pInfo['products_id'];
                            }
                        }

                        $Query = Doctrine_Query::create()
                                ->select('g.group_name, f.*, fd.field_name, f2p.product_id, f2p.value, f2g.sort_order')
                                ->from('ProductsCustomFieldsGroups g')
                                ->leftJoin('g.ProductsCustomFieldsGroupsToProducts g2p')
                                ->leftJoin('g.ProductsCustomFieldsToGroups f2g')
                                ->leftJoin('f2g.ProductsCustomFields f')
                                ->leftJoin('f.ProductsCustomFieldsDescription fd')
                                ->leftJoin('f.ProductsCustomFieldsToProducts f2p')
                                ->where('fd.field_name is not null')
                                ->andWhere('fd.language_id = ?', (int)Session::get('languages_id'))
                                ->andWhereIn('f2p.product_id', $products)
                                ->andWhereIn('fd.field_name', $customNames)
                                ->andWhereIn('f2p.value', $customValues)
                                //->andWhere('(f2p.value LIKE "%' . $actor . '%" OR f2p.value = ?)', $actor)
                                ->orderBy('f2g.sort_order')
                                ->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

                        if (count($Query) > 0){
                            foreach($Query[0]['ProductsCustomFieldsToGroups'][0]['ProductsCustomFields']['ProductsCustomFieldsToProducts'] as $qp){
                                $relatedProducts .= $qp['product_id'].",";
                            }
                        }

                        $Query = Doctrine_Query::create()
                            ->select('g.products_options_groups_name,fd.id,po.products_options_id, f.*,f2g.products_options_values_to_products_options_id,pov.products_options_values_id, fd.products_options_name, f.products_id, povd.products_options_values_name, f2g.sort_order')
                            ->from('ProductsAttributes f')
                            ->leftJoin('f.ProductsOptionsGroups g')
                            ->leftJoin('g.ProductsOptionsToProductsOptionsGroups g2p')
                            ->leftJoin('f.ProductsOptions po')
                            ->leftJoin('f.ProductsOptionsValues pov')
                            ->leftJoin('pov.ProductsOptionsValuesToProductsOptions f2g')
                            ->leftJoin('po.ProductsOptionsDescription fd')
                            ->leftJoin('pov.ProductsOptionsValuesDescription povd')
                            ->where('fd.products_options_name is not null')
                            ->andWhere('fd.language_id = ?', (int)Session::get('languages_id'))
                            ->andWhereIn('f.products_id', $products)
                            ->andWhereIn('fd.products_options_name', $customAttributes)
                            //->andWhereIn('povd.products_options_values_name', $customValues)
                            ->orderBy('f2g.sort_order')
                            ->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

                        if (count($Query) > 0){
                            foreach($Query as $qp){
                                $relatedProducts .= $qp['products_id'].",";
                            }
                        }
                    }
                }

                $relatedProducts = substr($relatedProducts, 0, strlen($relatedProducts)-1);
                //if number of related products is less than maximum than add the highest priced dvd from category?
            	if(empty($relatedProducts)){
                    foreach($ProductToCategories as $category){

                        $QproductFromCat = Doctrine_Query::create()
                                ->select('p.products_id, c.categories_id')
                                ->from('Products p')
                                ->leftJoin('p.ProductsToCategories c')
                                ->where('c.categories_id = ?', $category['categories_id'])
                                ->orderBy('p.products_price DESC')
                                ->limit((int)sysConfig::get('EXTENSION_RELATED_PRODUCTS_DISPLAY_NUMBER'))
                                ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
                        if($QproductFromCat){
                            foreach($QproductFromCat as $qp)
                                $relatedProducts .= $qp['products_id'].",";
                            $relatedProducts = substr($relatedProducts,0, strlen($relatedProducts)-2);
                        }
                    }
                }

			}

			$ProductG = Doctrine_Core::getTable('ProductsRelatedGlobal')->findOneByType('P');
			if (count($ProductG) > 0){
				$related = explode(',',$ProductG->related_global);
				foreach($related as $qp){
					$relatedProducts .= ",".$qp;
				}
			}

			if (!empty($relatedProducts)){
				$listing = $this->getListing($relatedProducts);
				if ($listing){
					return '<div id="tab_' . $this->getExtensionKey() . '">' . $listing->draw() . '</div>';
				}
			}

			return;
		}
	}
}

function tep_cfg_pull_down_custom_list() {
    $customFields = array();

    $QLayouts = Doctrine_Query::create()
        ->select('field_name')
        ->from('ProductsCustomFieldsDescription')
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    if ($QLayouts){
        foreach($QLayouts as $lInfo){
            $customFields[] = $lInfo['field_name'];
        }
    }

    return $customFields;
}

function tep_cfg_pull_down_attributes_list() {
    $attributesFields = array();

    $QLayouts = Doctrine_Query::create()
        ->select('products_options_name')
        ->from('ProductsOptionsDescription')
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    if ($QLayouts){
        foreach($QLayouts as $lInfo){
            $attributesFields[] = $lInfo['products_options_name'];
        }
    }

    return $attributesFields;
}
?>
