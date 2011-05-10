<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxReview extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('review','reviews');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_REVIEWS'));
		if ($App->getEnv() == 'catalog'){
			$this->setBoxHeadingLink(itw_app_link('appExt=reviews', 'product_review', 'default'));
		}
	}

	public function show(){
			global $appExtension;
			$lID = (int)Session::get('languages_id');
			$Qreviews = Doctrine_Query::create()
				->select('r.reviews_id,r.reviews_rating,rd.reviews_text, r.products_id,r.customers_name, pd.products_name, p.products_id, r.date_added,p.products_image')
				->from('Reviews r')
				->leftJoin('r.ReviewsDescription rd')
				->leftJoin('r.Products p')
				->leftJoin('p.ProductsDescription pd')
				//->where('r.products_id = ?', $_GET['products_id'])
				->andWhere('rd.languages_id = ?', $lID)
				->andWhere('pd.language_id = ?', $lID)
				->orderBy('rand()')
				->limit(sysConfig::get('MAX_RANDOM_SELECT_REVIEWS'));

			$multiStore = $appExtension->getExtension('multiStore');
			$multiStoreEnabled = ($multiStore !== false && $multiStore->isEnabled() === true);

			if ($multiStoreEnabled && Session::exists('current_store_id')){
				$Qreviews->leftJoin('p.ProductsToStores p2s')
				->andWhere('p2s.stores_id = ?', Session::get('current_store_id'));
			}

			$myReviews = $Qreviews->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
			
			if($myReviews){
				foreach($myReviews as $review){
					$review1 = $review['ReviewsDescription'][0]['reviews_text'];
					if (strlen($review['ReviewsDescription'][0]['reviews_text']) > 150){
						$review1 = substr($review1, 0, 150) . ' ..';
					}

					$boxContent = '<div align="center" class="reviewbox"><a href="' . itw_app_link('appExt=reviews&products_id=' . $review['products_id'],'product_review','default')  . '"><img src="imagick_thumb.php?width=150&height=150&path=rel&imgSrc=' .  sysConfig::get('DIR_WS_IMAGES') . $review['Products'][0]['products_image'] . '"></a><br /><a href="' . itw_app_link('appExt=reviews&products_id=' . $review['products_id'],'product_review','default') . '">' . $review1 . '</a><br />' . tep_image(sysConfig::get('DIR_WS_IMAGES') . 'stars_' . $review['reviews_rating'] . '.gif' , sprintf(INFOBOX_REVIEWS_TEXT_OF_5_STARS, $review['reviews_rating'])) . '</div>';
				}
			} elseif (isset($_GET['products_id'])) {
				$boxContent = '<table border="0" cellspacing="0" cellpadding="2"><tr><td class="infoBoxContents"><a href="' . itw_app_link('appExt=reviews&products_id=' . $_GET['products_id'], 'product_review', 'write') . '">' . tep_image(sysConfig::get('DIR_WS_IMAGES') . 'box_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a></td><td class="infoBoxContents"><a href="' . itw_app_link('appExt=reviews&products_id=' . $review['products_id'],'product_review','write') . '">' . sysLanguage::get('INFOBOX_REVIEWS_WRITE_REVIEW') .'</a></td></tr></table>';
			} else {
				$boxContent = '<div align="center" class="reviewbox">'.sysLanguage::get('INFOBOX_REVIEWS_NO_REVIEWS').'</div>';
			}
			$this->setBoxContent($boxContent);

			return $this->draw();
	}
}
?>