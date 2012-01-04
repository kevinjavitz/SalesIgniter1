<?php
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action)) {
	$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_AGGRESSIVE);
	Doctrine::loadModels(sysConfig::get('DIR_FS_CATALOG') . 'ext/Doctrine/Models');

	switch ($action) {
		case 'clear':
			Doctrine_Core::getTable('Orders')->findAll()->delete();
			Doctrine_Core::getTable('OrdersProducts')->findAll()->delete();
			Doctrine_Core::getTable('OrdersStatusHistory')->findAll()->delete();
			Doctrine_Core::getTable('OrdersPaymentsHistory')->findAll()->delete();
			Doctrine_Core::getTable('OrdersProductsReservation')->findAll()->delete();//this should be into extension.
			Doctrine_Core::getTable('Products')->findAll()->delete();
			Doctrine_Core::getTable('ProductsInventoryBarcodes')->findAll()->delete();
			Doctrine_Core::getTable('ProductsInventoryQuantity')->findAll()->delete();
			Doctrine_Core::getTable('Customers')->findAll()->delete();
			Doctrine_Core::getTable('Categories')->findAll()->delete();
			Doctrine_Core::getTable('FunwaysCategories')->findAll()->delete();
			Doctrine_Core::getTable('FunwaysProducts')->findAll()->delete();
			Doctrine_Core::getTable('FunwaysProductsToCategories')->findAll()->delete();
			//Doctrine_Core::getTable('RentalBookings')->findAll()->delete();
			
			Doctrine_Core::getTable('ProductsCustomFields')->findAll()->delete();
			Doctrine_Core::getTable('ProductsCustomFieldsGroups')->findAll()->delete();
			Doctrine_Core::getTable('ProductsCustomFieldsOptions')->findAll()->delete();
			
			Doctrine_Core::getTable('ProductsAttributes')->findAll()->delete();
			Doctrine_Core::getTable('ProductsOptions')->findAll()->delete();
			Doctrine_Core::getTable('ProductsOptionsGroups')->findAll()->delete();
			Doctrine_Core::getTable('ProductsOptionsValues')->findAll()->delete();
			
			
			//Doctrine_Core::getTable('Manufacturers')->findAll()->delete();
			//Doctrine_Core::getTable('ManufacturersInfo')->findAll()->delete();
			//Doctrine_Core::getTable('Banners')->findAll()->delete();
			//Doctrine_Core::getTable('BannersHistory')->findAll()->delete();
			Doctrine_Core::getTable('Specials')->findAll()->delete();
			//Doctrine_Core::getTable('Membership')->findAll()->delete();
			//Doctrine_Core::getTable('CustomersWishlist')->findAll()->delete();
			//Doctrine_Core::getTable('CustomersWishlistAttributes')->findAll()->delete();
			Doctrine_Core::getTable('Reviews')->findAll()->delete();
			Doctrine_Core::getTable('ReviewsDescription')->findAll()->delete();
			//Doctrine_Core::getTable('ArticleReviews')->findAll()->delete();
			//Doctrine_Core::getTable('ArticleReviewsDescription')->findAll()->delete();
			Doctrine_Core::getTable('Articles')->findAll()->delete();
			Doctrine_Core::getTable('ArticlesDescription')->findAll()->delete();
			Doctrine_Core::getTable('ArticlesToTopics')->findAll()->delete();
			//Doctrine_Core::getTable('ArticlesXsell')->findAll()->delete();
			//Doctrine_Core::getTable('Authors')->findAll()->delete();
			//Doctrine_Core::getTable('AuthorsInfo')->findAll()->delete();
			Doctrine_Core::getTable('Topics')->findAll()->delete();
			Doctrine_Core::getTable('TopicsDescription')->findAll()->delete();
			//Doctrine_Core::getTable('AffiliateAffiliate')->findAll()->delete();
			//Doctrine_Core::getTable('AffiliateNews')->findAll()->delete();
			//Doctrine_Core::getTable('AffiliateNewsContents')->findAll()->delete();
			//Doctrine_Core::getTable('AffiliateNewsletters')->findAll()->delete();
			//Doctrine_Core::getTable('AffiliateBanners')->findAll()->delete();
			//Doctrine_Core::getTable('AffiliateBannersHistory')->findAll()->delete();
			//Doctrine_Core::getTable('AffiliateClickthroughs')->findAll()->delete();
			//Doctrine_Core::getTable('AffiliatePayment')->findAll()->delete();
			//Doctrine_Core::getTable('AffiliatePaymentStatus')->findAll()->delete();
			//Doctrine_Core::getTable('AffiliatePaymentStatusHistory')->findAll()->delete();
			//Doctrine_Core::getTable('AffiliateSales')->findAll()->delete();
			
			tep_redirect(tep_href_link('clearDB.php', 'success=true'));
			break;
	}
}

?>