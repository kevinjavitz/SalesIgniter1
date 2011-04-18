<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxFunways extends InfoBoxAbstract {
	private $categoriesString = '';
	private $tree = array();
	public function __construct(){
		global $App;
		$this->init('funways');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_FUNWAYS'));
	}

	public function show(){
		global $fcPath, $fcPath_array, $parent_id,$first_element, $first_id;
		$this->categoriesString = '';
		$this->tree = array();

		$Qcategories = Doctrine_Query::create()
		->select('c.categories_id, c.categories_name, c.parent_id, c.link_to')
		->from('FunwaysCategories c')
		->where('c.parent_id = ?', '0')
		->orderBy('c.sort_order, c.categories_name')
		->execute(array(), Doctrine::HYDRATE_ARRAY);
		foreach($Qcategories as $cInfo){
			$categoryId = $cInfo['categories_id'];
			$parentId = $cInfo['parent_id'];
			$categoryName = $cInfo['categories_name'];
			
			$this->tree[$categoryId] = array(
				'name'    => $categoryName,
				'parent'  => $parentId,
				'level'   => 0,
				'path'    => $categoryId,
				'link_to' => $cInfo['link_to'],
				'next_id' => false
			);

			if (isset($parent_id)) {
				$this->tree[$parent_id]['next_id'] = $categoryId;
			}

			$parent_id = $categoryId;

			if (!isset($first_element)) {
				$first_element = $categoryId;
			}
		}

		//------------------------
		if (!empty($fcPath)){
			$new_path = '';
			reset($fcPath_array);
			while(list($key, $value) = each($fcPath_array)){
				unset($parent_id);
				unset($first_id);

				$Qcategories = Doctrine_Query::create()
				->select('c.categories_id, c.categories_name, c.link_to, c.parent_id')
				->from('FunwaysCategories c')
				->where('c.parent_id = ?', (int)$value)
				->orderBy('c.sort_order, c.categories_name')
				->execute();
				if ($Qcategories->count() > 0){
					$new_path .= $value;
					foreach($Qcategories->toArray() as $cInfo){
						$categoryId = $cInfo['categories_id'];
						$parentId = $cInfo['parent_id'];
						$categoryName = $cInfo['categories_name'];
						
						$this->tree[$categoryId] = array(
							'name'    => $categoryName,
							'parent'  => $parentId,
							'level'   => $key+1,
							'path'    => $new_path . '_' . $categoryId,
							'link_to' => $cInfo['link_to'],
							'next_id' => false
						);

						if (isset($parent_id)) {
							$this->tree[$parent_id]['next_id'] = $categoryId;
						}

						$parent_id = $categoryId;

						if (!isset($first_id)) {
							$first_id = $categoryId;
						}

						$last_id = $categoryId;
					}
					$this->tree[$last_id]['next_id'] = $this->tree[$value]['next_id'];
					$this->tree[$value]['next_id'] = $first_id;
					$new_path .= '_';
				}else{
					break;
				}
			}
		}
		$this->show_funways($first_element);
		
		$this->setBoxContent($this->categoriesString);
		
		return $this->draw();
	}

	public function show_funways($counter) {
		global $fcPath_array;

		if (!isset($this->tree[$counter])) return;

		for ($i=0; $i<$this->tree[$counter]['level']; $i++) {
			$this->categoriesString .= "&nbsp;&nbsp;";
		}

		$this->categoriesString .= '<img style="margin-left:3px;margin-right:3px;" src="' . sysConfig::getDirWsCatalog() . 'images/categories_arrow.png" alt="" />';

		$this->categoriesString .= '<a href="';

		if(!empty($this->tree[$counter]['link_to'])){
			$this->categoriesString .= ($this->tree[$counter]['link_to']) . '">';
		}else{
			if ($this->tree[$counter]['parent'] == 0) {
				$cPath_new = 'fcPath=' . $counter;
			} else {
				$cPath_new = 'fcPath=' . $this->tree[$counter]['path'];
			}
			$this->categoriesString .= itw_app_link($cPath_new, 'funways', 'default') . '">';
		}



		if (isset($fcPath_array) && in_array($counter, $fcPath_array)) {
			$this->categoriesString .= '<b>';
		}

		// display category name
		$this->categoriesString .= $this->tree[$counter]['name'];

		if (isset($fcPath_array) && in_array($counter, $fcPath_array)) {
			$this->categoriesString .= '</b>';
		}

		$this->categoriesString .= '</a><br />';

		if ($this->tree[$counter]['next_id'] != false) {
			$this->show_funways($this->tree[$counter]['next_id']);
		}
	}
}
?>