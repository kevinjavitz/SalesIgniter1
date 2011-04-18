<?php
function rating_bar($name, $product_id) {
	global $userAccount;
  	if (EXTENSION_REVIEWS_ENABLED != 'True') return '';
  	
	$units = 5;
	$voted = false;
	$current_rating = 0;

	if ($userAccount->isLoggedIn()){
		$QcustomerRating = Doctrine_Query::create()
		->select('reviews_rating')
		->from('Ratings')
		->where('products_id = ?', $product_id)
		->andWhere('customers_id = ?', $userAccount->getCustomerId())
		->execute();
		if ($QcustomerRating !== false){
			$current_rating = number_format($QcustomerRating[0]->reviews_rating, 1);
		}
	}else{
		$Qtotals = Doctrine_Query::create()
		->select('avg(reviews_rating) as total')
		->from('Ratings')
		->where('products_id = ?', $product_id)
		->groupBy('products_id')
		->execute();
		if ($Qtotals->count()){
			$current_rating = number_format($Qtotals[0]->total, 1);
		}
	}

	$inputFields = '';
	for($i=1; $i<11; $i++){
		$inputFields .= tep_draw_radio_field('star_rating_' . $product_id, $i, ($current_rating == ($i/2)), 'id="star_rating_' . $product_id . '_' . $i . '" style="display:none;"');
	}
	return '<br /><table style="margin:0 auto;"><tr><td align="left"><div class="starRating starRating_' . $product_id . '" products_id="' . $product_id . '" style="width:' . ($userAccount->isLoggedIn() === true ? (17*6) : (16*5)) . 'px;">
        ' . $inputFields . '
      </div><div style="clear:both;"></div></td></tr></table>';
}
?>