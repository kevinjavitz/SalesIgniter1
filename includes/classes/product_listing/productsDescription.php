<?php
class productListing_productsDescription {
    public function sortColumns(){
        $selectSortKeys = array(
            array(
                'value' => 'p.products_description',
                'name'  => sysLanguage::get('PRODUCT_LISTING_PRODUCT_MODEL')
            )

        );
        return $selectSortKeys;
    }

	public function show(&$productClass){
		//return implode(' ', array_slice(str_word_count(strip_tags($productClass->getDescription()), 1), 0, 50)) . ' <a class="readMore" href="' . itw_app_link('products_id=' . $productClass->getID(), 'product', 'info') . '">Read More</a>';
		$description = strip_tags($productClass->getDescription());
		$explode = explode(' ',$description);
		$string  = '';
		$limit = 50;

		for($i=0;$i<$limit;$i++){
			$string .= $explode[$i]." ";
		}

		return $string . ' <a class="readMore" href="' . itw_app_link('products_id=' . $productClass->getID(), 'product', 'info') . '">Read More</a>';
	}
}
?>