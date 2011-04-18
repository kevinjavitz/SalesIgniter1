<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxTopRentals extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('topRentals');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_TOPRENTALS'));
	}

	public function show(){

		$datePast = date('Y-m-d H:i:s', mktime(0,
							  	    0,
								    0,
									date("m")-2,
									date("d"),
									date("Y")
							)
		);

		$Qproduct = Doctrine_Query::create()
			->select('p.products_id,pd.products_name,rt.*')
			->from('Products p')
			->leftJoin('p.ProductsDescription pd')
			->leftJoin('p.RentalTop rt')
			->where('p.products_status = ?', '1')
			->where('pd.language_id = ?', (int)Session::get('languages_id'))
			->andWhere('rt.date_modified > ?', $datePast)
			->limit(10)
			->orderBy('rt.top desc')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$rows = 0;
		$boxContent = '<table border="0" width="100%" cellspacing="0" cellpadding="1">';
		if($Qproduct){
			foreach ($Qproduct as $tInfo) {							
				$rows++;
				$boxContent .= '<tr>
									<td class="infoBoxContents" valign="top">' .
										'<a href="' . itw_app_link('products_id=' . $tInfo['products_id'], 'product', 'info') . '">' . tep_row_number_format($rows) . '. ' . $tInfo['ProductsDescription'][0]['products_name'] . '</a>
									</td>
								</tr>';
			}
		}
			$boxContent .= '</table>';
			$this->setBoxContent($boxContent);
			
		return $this->draw();
	}
}
?>