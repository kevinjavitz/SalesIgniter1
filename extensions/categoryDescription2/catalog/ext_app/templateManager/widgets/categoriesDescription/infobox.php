<?php
class InfoBoxCategoriesDescription extends InfoBoxAbstract {
	
	public function __construct(){
		global $App;
		$this->init('categoriesDescription', __DIR__);
	}

	
	public function show(){
		global $appExtension, $current_category_id;

		$Qcheck = Doctrine_Query::create()
			->select('categories_description2')
			->from('CategoriesDescription')
			->where('categories_id = ?', $current_category_id)
			->andWhere('language_id = ?', Session::get('languages_id'))
			->execute();
		$content = '';
		if ($Qcheck->count() > 0){
			$description = $Qcheck->toArray();
			if (!empty($description[Session::get('languages_id')]['categories_description2'])){
				$content = '' .
					$description[Session::get('languages_id')]['categories_description2'] .
					'';
			}
		}else{
			$infoPages = $appExtension->getExtension('infoPages');
			$pageInfo = $infoPages->getInfoPage(31);
			$content =  '' .
				stripslashes($pageInfo['PagesDescription'][Session::get('languages_id')]['pages_html_text']) .
				'';
		}
		
		$this->setBoxContent($content);
		
		return $this->draw();
	}
}
?>