<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class reviews_catalog_product_info extends Extension_reviews {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'ProductInfoTabHeader',
			'ProductInfoTabBody'
		), null, $this);
	}
	public function ProductInfoTabHeader(&$product){
		   /* $Qreviews = Doctrine_Query::create()
			->select('r.reviews_id, r.reviews_rating, rd.reviews_text, r.products_id, r.customers_name, pd.products_name, p.products_id, r.date_added, p.products_image')
			->from('Reviews r')
			->leftJoin('r.ReviewsDescription rd')
			->leftJoin('r.Products p')
			->leftJoin('p.ProductsDescription pd')
			->andWhere('rd.languages_id = ?', $lID)
			->andWhere('pd.language_id = ?', $lID)
			->andWhere('p.products_id = ?', (int)$_GET['products_id'])
			->orderBy('r.date_added');

			EventManager::notify('ReviewsQueryBeforeExecute', &$Qreviews);
			$Result = $Qreviews->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if(count($Result) > 0){*/
			$return = '<li><a href="#tabReviews"><span>' . sysLanguage::get('TAB_REVIEWS') . '</span></a></li>';
			/*}else{
				$return = '';
			} */
			return $return;
		}

		public function ProductInfoTabBody(&$product){
			$rowTemplate = addslashes('<div>' .
				'<a href="$infoLink"><u><b>$productName</b></u></a> ' .
				'<span class="smallText">$customersName</span>' .
				'<span style="float:right;">$dateAdded</span>' .
			'</div>' .
			'<div class="ui-widget ui-widget-content ui-corner-all"><table cellpadding="3" cellspacing="0" border="0" width="100%" style="margin:.5em;">' .
				'<tr>' .
					'<td><a href="$infoLink">$productImage</a></td>' .
					'<td class="main">$reviewText<br><br><i>$reviewRating</i></td>' .
				'</tr>' .
			'</table></div>');

			$lID = (int)Session::get('languages_id');

			$Qreviews = Doctrine_Query::create()
			->select('r.reviews_id, r.reviews_rating, rd.reviews_text, r.products_id, r.customers_name, pd.products_name, p.products_id, r.date_added, p.products_image')
			->from('Reviews r')
			->leftJoin('r.ReviewsDescription rd')
			->leftJoin('r.Products p')
			->leftJoin('p.ProductsDescription pd')
			->andWhere('rd.languages_id = ?', $lID)
			->andWhere('pd.language_id = ?', $lID)
			->andWhere('p.products_id = ?', (int)$_GET['products_id'])
			->orderBy('r.date_added');

			EventManager::notify('ReviewsQueryBeforeExecute', &$Qreviews);

			$tableContent = '';

			if (isset($_GET['page']) && !empty($_GET['page'])){
				$page = $_GET['page'];
			}else{
				$page = 1;
			}
			if (isset($_GET['limit']) && !empty($_GET['limit'])){
				$limit = $_GET['limit'];
			}else{
				$limit = sysConfig::get('EXTENSION_REVIEWS_PER_PAGE');
			}


			$listingPager = new Doctrine_Pager($Qreviews, $page, $limit);
			$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}#tabReviews');

			$pagerRange = new Doctrine_Pager_Range_Sliding(array(
				'chunk' => 5
			));

			$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
			$pagerLayout->setMyType('reviews');
			$pagerLayout->setTemplate('<a href="{%url}" style="margin-left:5px;padding:3px;">{%page}</a>');
			$pagerLayout->setSelectedTemplate('<span style="margin-left:5px;">{%page}</span>');

			$pager = $pagerLayout->getPager();

			$Result = $pager->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$pagerBar = $pagerLayout->display(array(), true);
			if ($Result){
				foreach($Result as $rInfo){

					$infoLink = itw_app_link('appExt=reviews&products_id=' . $rInfo['products_id'] . '&reviews_id=' . $rInfo['reviews_id'], 'product_review', 'details');
					$productName = $rInfo['Products'][0]['ProductsDescription'][0]['products_name'];
					$customersName = sprintf(sysLanguage::get('TEXT_REVIEW_BY'), $rInfo['customers_name']);
					$dateAdded = sprintf(sysLanguage::get('TEXT_REVIEW_DATE_ADDED'), tep_date_long($rInfo['date_added']));
					$rating = $rInfo['reviews_rating'];


					$productImage = '<img src="imagick_thumb.php?width=150&height=150&path=rel&imgSrc=' .  sysConfig::get('DIR_WS_IMAGES') . $rInfo['Products'][0]['products_image'] . '" alt="' . $productName . '"/>';
					$reviewText = nl2br(tep_break_string(htmlspecialchars($rInfo['ReviewsDescription'][0]['reviews_text']), 60, '-<br>') . ((strlen($rInfo['reviews_text']) >= 100) ? '..' : ''));
					$reviewRating = sprintf(TEXT_REVIEW_RATING, tep_image(sysConfig::get('DIR_WS_IMAGES') . 'stars_' . $rating . '.gif', sprintf(TEXT_OF_5_STARS, $rating)), sprintf(TEXT_OF_5_STARS, $rating));

					eval("\$tableContent .= \"$rowTemplate\";");
				}
		    }else{
				$tableContent .= sysLanguage::get('TEXT_NO_REVIEWS');
				$pagerBar = '';
		    }

			$div = htmlBase::newElement('div')
			->css(array(
					'display' => 'block',
					'margin-left' => 'auto',
					'margin-right' => 'auto'
			));

			$reviewButton = htmlBase::newElement('button')
			->setText(sysLanguage::get('TEXT_BUTTON_WRITE_REVIEW'))
			->setHref(itw_app_link('appExt=reviews&products_id=' . $_GET['products_id'], 'product_review', 'write'))
			->draw();
			$div->html($tableContent);

			$content = $div->draw(). $pagerBar . '<br/><br/>' . $reviewButton;

			$return = '<div id="tabReviews">' . $content . '</div>';

			return $return;
		}
}
?>