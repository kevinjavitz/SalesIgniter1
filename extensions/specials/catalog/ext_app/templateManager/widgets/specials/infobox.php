<?php
class InfoBoxSpecials extends InfoBoxAbstract {
	
	public function __construct(){
		global $App;
		$this->init('specials', __DIR__);

		$this->enabled = (sysConfig::get('EXTENSION_SPECIALS_INFOBOX') == 'True');
		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_SPECIALS'));
	}
	
	public function show(){
		global $appExtension, $currencies;
		if ($this->enabled === false) return;
		
		$Qproduct = Doctrine_Query::create()
		->select('p.products_id, s.specials_id, RANDOM() as rand')
		->from('Products p')
		->leftJoin('p.Specials s')
		->where('p.products_status = ?', '1')
		->andWhere('s.status = ?', '1')
		->andWhereIn('p.products_type', 'new')
		->orderBy('s.specials_date_added desc, rand');
		
		EventManager::notify('ProductQueryBeforeExecute', &$Qproduct);
		
		$Result = $Qproduct->execute();
		if ($Result){
			$product = new product($Result[0]['products_id'], 'new');
			if ($product->isValid() === false){
				unset($product);
				return;
			}
			
			$specials = $appExtension->getExtension('specials');
			
			$specialPrice = $currencies->format($product->getPrice('new'));
			$specials->showSpecialPrice(null, $product, $specialPrice);
      
			$imgLink = htmlBase::newElement('a')
			->setHref(itw_app_link('products_id=' . $product->getID(), 'product', 'default'))
			->html(tep_image(DIR_WS_IMAGES . $product->getImage(), $product->getName(), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT));
			
			$nameLink = htmlBase::newElement('a')
			->setHref(itw_app_link('products_id=' . $product->getID(), 'product', 'default'))
			->html($product->getName());
			
			$specialsLink = htmlBase::newElement('a')
			->setHref(itw_app_link('appExt=specials', 'specials', 'default'))
			->html(sysLanguage::get('INFOBOX_SPECIALS_ALLPRODS'));
			
			$boxContent = $imgLink->draw() . '<br />' . 
			              $nameLink->draw() . '<br />' . 
			              $specialPrice . '<br /><br />' . 
			              $specialsLink->draw();
			              
			$this->setBoxContent($boxContent);
		
			return $this->draw();
		}
		return;
	}
}
?>