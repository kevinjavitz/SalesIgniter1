<?php
	class packages_catalog_product_info extends Extension_packages {
		public function __construct(){
			global $App;
			parent::__construct();

		}

		public function load(){
			if ($this->enabled === false) return;

			EventManager::attachEvent('ProductInfoBeforeDescription', null, $this);
			//
		}

		public function ProductInfoBeforeDescription(&$product){
			$QPackageProducts = Doctrine_Query::create()
			->from('ProductsPackages pp')
			->leftJoin('pp.Products p')
			->leftJoin('p.ProductsDescription pd')
			->where('pp.parent_id = ?', $product->getID())
			->andWhere('pd.language_id = ?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if(count($QPackageProducts) > 0){
				echo sysLanguage::get('TEXT_PRODUCT_INFO_PACKAGE_DESCRIPTION').'<br/><br/>';
				foreach($QPackageProducts as $package){
					echo $package['Products']['ProductsDescription'][0]['products_name']. sysLanguage::get('TEXT_PRODUCT_INFO_AS'). $package['purchase_type'].'<br/>';
				}
			}

		}

	}
?>