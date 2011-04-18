<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxBestSellers extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('bestSellers');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_BESTSELLERS'));
	}

	public function show(){
		global $current_category_id;
		$QbestSellers = Doctrine_Query::create()
		->distinct(true)
		->select('p.products_id, pd.products_name')
		->from('Products p')
		->leftJoin('p.ProductsDescription pd')
		->where('p.products_status = ?', '1')
		->andWhere('p.products_ordered > ?', '0')
		->andWhere('pd.language_id = ?', (int)Session::get('languages_id'))
		->orderBy('p.products_ordered desc, pd.products_name')
		->limit(MAX_DISPLAY_BESTSELLERS);
		if (isset($current_category_id) && ($current_category_id > 0)) {
			$QbestSellers->leftJoin('p.ProductsToCategories p2c')
			->leftJoin('p2c.Categories c')
			->andWhere('p2c.categories_id = ?', (int)$current_category_id);
		}
		$Result = $QbestSellers->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if ($Result && sizeof($Result) >= MIN_DISPLAY_BESTSELLERS) {
			$rows = 0;
			$boxContent = '<table border="0" width="100%" cellspacing="0" cellpadding="1">';
			foreach($Result as $pInfo){
				$rows++;
				$boxContent .= '<tr>
									<td class="infoBoxContents" valign="top">' .
										'<a href="' . itw_app_link('products_id=' . $pInfo['products_id'], 'product', 'info') . '">' . tep_row_number_format($rows) . '. '. $pInfo['ProductsDescription'][0]['products_name'] . '</a>
									</td>
								</tr>';
			}
			$boxContent .= '</table>';
			$this->setBoxContent($boxContent);
			
			return $this->draw();
		}
		return false;
	}
}
?>