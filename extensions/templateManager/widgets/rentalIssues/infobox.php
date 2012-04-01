<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxRentalIssues extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('rentalIssues');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_RENTALISSUES'));
		if ($App->getEnv() == 'catalog'){
			$this->setBoxHeadingLink(itw_app_link(null, 'account', 'rental_issues'));
		}
	}

	public function show(){
		global $userAccount;
		if (sysConfig::get('ALLOW_RENTALS') == 'true'){

			if ($userAccount->isLoggedIn() === true) {
				$cart_contents_string = '';

				$QProdRented = Doctrine_Query::create()
				->from('Products p')
				->leftJoin('p.RentedProducts r')
				->leftJoin('p.ProductsDescription pd')
				->where('r.customers_id = ?', $userAccount->getCustomerId())
				->andWhere('pd.language_id = ?', Session::get('languages_id'))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				if ($QProdRented) {
					$cart_contents_string = '<form name="rental_issues" action="' . itw_app_link('action=resolve_ticket', 'account', 'rental_issues') . '" method="post">';
					$cart_contents_string .= tep_draw_hidden_field('type', 'new');
					$cart_contents_string .= '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
					$cart_contents_string .= '<tr><td align="right" valign="top" class="infoBoxContents">';


					$cart_contents_string .= sprintf(
						'%s: <br><select name="products_id" style="width:100%%;">',
						sysLanguage::get('RENTALISSUES_RENTED_ITEMS')
					);

					foreach($QProdRented as $iProd){
						$cart_contents_string .= '<option value="'.$iProd['products_id'].'">'."\n";
						$cart_contents_string .= $iProd['ProductsDescription'][0]['products_name'] . '</option>'."\n";
					}
					$cart_contents_string .= '</td></tr>'."\n".'<tr><td align="left" valign="top" class="infoBoxContents">';
					$cart_contents_string .= sysLanguage::get('RENTALISSUES_PROBLEM') . '<br>' . "\n";
					$cart_contents_string .= tep_draw_textarea_field('feedback', '', 30, 5)."\n";
					$cart_contents_string .= htmlBase::newElement('button')->setType('submit')->setText(sysLanguage::get('BUTTON_OPEN_TICKET'))->draw();
					$cart_contents_string .= '</td></tr>'."\n".'<tr><td align="left" valign="top" class="infoBoxContents"><br><a href="'. itw_app_link(null, 'account', 'rental_issues') .'">' . sysLanguage::get('BOX_HEADING_OPEN_TICKET') . '</a>';
					$cart_contents_string .= '</td></tr></table>';
					$cart_contents_string .= '</form>';
				} else {
					$cart_contents_string = sysLanguage::get('BOX_RENTAL_QUEUE_EMPTY');
				}


				$boxContent = $cart_contents_string;
				$this->setBoxContent($boxContent);

				return $this->draw();
			}
		}
		return false;
	}
}
?>