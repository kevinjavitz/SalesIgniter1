<?php
/*
  $Id: filenames.php,v 1.4 2003/06/11 17:38:00 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// define the filenames used in the project
  define('CONTENT_ACCOUNT', 'account');
  define('CONTENT_ACCOUNT_EDIT', 'account_edit');
  define('CONTENT_ACCOUNT_HISTORY', 'account_history');
  define('CONTENT_ACCOUNT_HISTORY_INFO', 'account_history_info');
  define('CONTENT_ACCOUNT_HISTORY_INVENTORY_INFO', 'account_history_inventory_info');
  define('CONTENT_ACCOUNT_NEWSLETTERS', 'account_newsletters');
  define('CONTENT_ACCOUNT_NOTIFICATIONS', 'account_notifications');
  define('CONTENT_ACCOUNT_PASSWORD', 'account_password');
  //

  define('CONTENT_ADDRESS_BOOK', 'address_book');
  define('CONTENT_ADDRESS_BOOK_PROCESS', 'address_book_process');
  define('CONTENT_RENTAL_ADDRESS_PROCESS', 'rental_address_process');
  define('CONTENT_BILLING_ADDRESS_PROCESS', 'billing_address_process');
  define('CONTENT_ADVANCED_SEARCH', 'advanced_search');
  define('CONTENT_ADVANCED_SEARCH_RESULT', 'advanced_search_result');
  define('CONTENT_ALSO_PURCHASED_PRODUCTS', 'also_purchased_products');
  define('CONTENT_CHECKOUT_CONFIRMATION', 'checkout_confirmation');
  define('CONTENT_CHECKOUT_PAYMENT', 'checkout_payment');
  define('CONTENT_CHECKOUT_PAYMENT_ADDRESS', 'checkout_payment_address');
  define('CONTENT_CHECKOUT_PROCESS', 'checkout_process');
  define('CONTENT_CHECKOUT_SHIPPING', 'checkout_shipping');
  define('CONTENT_CHECKOUT_SHIPPING_ADDRESS', 'checkout_shipping_address');
  define('CONTENT_CHECKOUT_SUCCESS', 'checkout_success');
  define('CONTENT_CONTACT_US', 'contact_us');
  define('CONTENT_CONDITIONS', 'conditions');
  define('CONTENT_COOKIE_USAGE', 'cookie_usage');
  define('CONTENT_CREATE_ACCOUNT', 'create_account');
  define('CONTENT_CREATE_ACCOUNT_SUCCESS', 'create_account_success');
  define('CONTENT_INDEX_DEFAULT', 'index_default');
  define('CONTENT_INDEX_NESTED', 'index_nested');
  define('CONTENT_INDEX_PRODUCTS', 'index_products');
  define('CONTENT_DOWNLOAD', 'download');
  define('CONTENT_LOGIN', 'login');
  define('CONTENT_LOGOFF', 'logoff');
  define('CONTENT_NEW_PRODUCTS', 'new_products');
  define('CONTENT_FEATURED_PRODUCTS', 'featured_products');
  define('CONTENT_PASSWORD_FORGOTTEN', 'password_forgotten');
  define('CONTENT_POPUP_IMAGE', 'popup_image');
  define('CONTENT_POPUP_SEARCH_HELP', 'popup_search_help');
  define('CONTENT_PRIVACY', 'privacy');
  define('CONTENT_PRODUCT_INFO', 'product_info');
  define('CONTENT_PRODUCT_LISTING', 'product_listing');
  define('CONTENT_FEATURED_PRODUCT_LISTING', 'featured_product_listing');
  define('CONTENT_PRODUCT_REVIEWS', 'product_reviews');
  define('CONTENT_PRODUCT_REVIEWS_INFO', 'product_reviews_info');
  define('CONTENT_PRODUCT_REVIEWS_WRITE', 'product_reviews_write');
  define('CONTENT_PRODUCTS_NEW', 'products_new');
  define('CONTENT_REDIRECT', 'redirect');
  define('CONTENT_REVIEWS', 'reviews');
  define('CONTENT_SHIPPING', 'shipping');
  define('CONTENT_SHOPPING_CART', 'shopping_cart');
  define('CONTENT_SPECIALS', 'specials');
  define('CONTENT_SSL_CHECK', 'ssl_check');
  define('CONTENT_TELL_A_FRIEND', 'tell_a_friend');
  define('CONTENT_UPCOMING_PRODUCTS', 'upcoming_products');
  define('CONTENT_MANAGE_QUEUE', 'manage_queue');
  define('CONTENT_POLL_BOOTH', 'pollbooth');

//inventory centers view orders
  define('CONTENT_ACCOUNT_VIEW_ORDERS', 'account_view_orders_inventory');

  define('CONTENT_RECENT_ADDITIONS', 'recent_additions');

  //Following added on 20 Aug 2005
  define('CONTENT_RENTAL_QUEUE', 'rental_queue');
 //Added by Amit
  define('CONTENT_RENT_CREATE_ACCOUNT', 'create_rentaccount');
  define('CONTENT_CHECKOUT_RENTAL_PAYMENT','checkout_rental_payment');
  define('CONTENT_CHECKOUT_RENTAL_CONFIRMATION','checkout_rental_confirmation');
  define('CONTENT_CHECKOUT_RENTAL_PROCESS','checkout_rental_process');
  define('CONTENT_MEMBERSHIP','membership');
  define('CONTENT_MEMBERSHIP_DETAILS','membership_details');
  define('CONTENT_MEMBERSHIP_BILLING_INFO','membership_billing_info');
  define('CONTENT_ACCOUNT_MEMBERSHIP_CANCEL','account_membership_cancel');
  define('CONTENT_ACCOUNT_MEMBERSHIP_UPGRADE','account_membership_upgrade');

  // PWA
  define('CONTENT_PWA_PWA_LOGIN', 'login_pwa');
  define('CONTENT_PWA_ACC_LOGIN', 'login_acc');
  //define('CONTENT_CHECKOUT', 'Order_Info');
  define('CONTENT_ORDER_INFO', 'Order_Info');
  define('CONTENT_ORDER_INFO_PROCESS', 'Order_Info_Process');

	define('CONTENT_RENTED_PRODUCTS', 'rented_products');
	define('CONTENT_RENTAL_ISSUES', 'rental_issues');
	define('CONTENT_RENTAL_TOP', 'top_rentals');
	define('CONTENT_POPUP_TERMS', 'popup_terms');
	//Extra pages
	define('CONTENT_PAGES', 'extra_info_pages');
	define('CONTENT_CATEGORIES_INFO', 'index');
	//Fun_ways
	define('CONTENT_FUNWAYS', 'funways');
	define('CONTENT_POPUP_INFOBOX_HELP', 'popup_infobox_help');

	//Featured
	define('CONTENT_FEATURED', 'featured');
	define('CONTENT_UPCOMING', 'upcoming');

	//Article
	define('CONTENT_ARTICLE_INFO', 'article_info');
	define('CONTENT_ARTICLE', 'articles');
	define('CONTENT_ARTICLE_NEW', 'articles_new');
	define('CONTENT_ARTICLE_REVIEWS_WRITE', 'article_reviews_write');
	define('CONTENT_ARTICLE_REVIEWS', 'article_reviews');
	define('CONTENT_ARTICLE_LIST', 'articles_list');


  define('FILENAME_ARTICLE_INFO', 'article_info.php');
  define('FILENAME_ARTICLE_LISTING', 'article_listing.php');
  define('FILENAME_ARTICLE_REVIEWS', 'article_reviews.php');
  define('FILENAME_ARTICLE_REVIEWS_INFO', 'article_reviews_info.php');
  define('FILENAME_ARTICLE_REVIEWS_WRITE', 'article_reviews_write.php');
  define('FILENAME_ARTICLES', 'articles.php');
  define('FILENAME_ARTICLES_NEW', 'articles_new.php');
  define('FILENAME_ARTICLES_UPCOMING', 'articles_upcoming.php');
  define('FILENAME_ARTICLES_XSELL', 'articles_xsell.php');
  define('FILENAME_ARTICLES_PXSELL', 'articles_pxsell.php'); //  New since v1.5
  define('FILENAME_NEW_ARTICLES', 'new_articles.php');
  define('FILENAME_ARTICLES_LIST', 'articles_list.php');
  define('FILENAME_ARTICLE_LISTING_LIST', 'article_listing_list.php');


// define the filenames used in the project
  define('FILENAME_ACCOUNT', CONTENT_ACCOUNT . '.php');
  define('FILENAME_ACCOUNT_EDIT', CONTENT_ACCOUNT_EDIT . '.php');
  define('FILENAME_ACCOUNT_HISTORY', CONTENT_ACCOUNT_HISTORY . '.php');
  define('FILENAME_ACCOUNT_HISTORY_INFO', CONTENT_ACCOUNT_HISTORY_INFO . '.php');
  define('FILENAME_ACCOUNT_HISTORY_INVENTORY_INFO', CONTENT_ACCOUNT_HISTORY_INVENTORY_INFO . '.php');
  define('FILENAME_ACCOUNT_NEWSLETTERS', CONTENT_ACCOUNT_NEWSLETTERS . '.php');
  define('FILENAME_ACCOUNT_NOTIFICATIONS', CONTENT_ACCOUNT_NOTIFICATIONS . '.php');
  define('FILENAME_ACCOUNT_PASSWORD', CONTENT_ACCOUNT_PASSWORD . '.php');
  //

  define('FILENAME_ADDRESS_BOOK', CONTENT_ADDRESS_BOOK . '.php');
  define('FILENAME_ADDRESS_BOOK_PROCESS', CONTENT_ADDRESS_BOOK_PROCESS . '.php');
  define('FILENAME_RENTAL_ADDRESS_PROCESS', CONTENT_RENTAL_ADDRESS_PROCESS . '.php');
  define('FILENAME_BILLING_ADDRESS_PROCESS', CONTENT_BILLING_ADDRESS_PROCESS . '.php');
  define('FILENAME_ADVANCED_SEARCH', CONTENT_ADVANCED_SEARCH . '.php');
  define('FILENAME_ADVANCED_SEARCH_RESULT', CONTENT_ADVANCED_SEARCH_RESULT . '.php');
  define('FILENAME_ALSO_PURCHASED_PRODUCTS', CONTENT_ALSO_PURCHASED_PRODUCTS . '.php');
  define('FILENAME_CHECKOUT_CONFIRMATION', CONTENT_CHECKOUT_CONFIRMATION . '.php');
  define('FILENAME_CHECKOUT_PAYMENT', CONTENT_CHECKOUT_PAYMENT . '.php');
  define('FILENAME_CHECKOUT_PAYMENT_ADDRESS', CONTENT_CHECKOUT_PAYMENT_ADDRESS . '.php');
  define('FILENAME_CHECKOUT_PROCESS', CONTENT_CHECKOUT_PROCESS . '.php');
  define('FILENAME_CHECKOUT_SHIPPING', CONTENT_CHECKOUT_SHIPPING . '.php');
  define('FILENAME_CHECKOUT_SHIPPING_ADDRESS', CONTENT_CHECKOUT_SHIPPING_ADDRESS . '.php');
  define('FILENAME_CHECKOUT_SUCCESS', CONTENT_CHECKOUT_SUCCESS . '.php');
  define('FILENAME_CONTACT_US', CONTENT_CONTACT_US . '.php');
  define('FILENAME_CONDITIONS', CONTENT_CONDITIONS . '.php');
  define('FILENAME_COOKIE_USAGE', CONTENT_COOKIE_USAGE . '.php');
  define('FILENAME_CREATE_ACCOUNT', CONTENT_CREATE_ACCOUNT . '.php');
  define('FILENAME_CREATE_ACCOUNT_SUCCESS', CONTENT_CREATE_ACCOUNT_SUCCESS . '.php');
  define('FILENAME_DEFAULT', 'index.php');
  define('FILENAME_DOWNLOAD', CONTENT_DOWNLOAD . '.php');
  define('FILENAME_LOGIN', CONTENT_LOGIN . '.php');
  define('FILENAME_LOGOFF', CONTENT_LOGOFF . '.php');
  define('FILENAME_NEW_PRODUCTS', CONTENT_NEW_PRODUCTS . '.php');
  define('FILENAME_FEATURED_PRODUCTS', CONTENT_FEATURED_PRODUCTS . '.php');
  define('FILENAME_PASSWORD_FORGOTTEN', CONTENT_PASSWORD_FORGOTTEN . '.php');
  define('FILENAME_POPUP_IMAGE', CONTENT_POPUP_IMAGE . '.php');
  define('FILENAME_POPUP_SEARCH_HELP', CONTENT_POPUP_SEARCH_HELP . '.php');
  define('FILENAME_PRIVACY', CONTENT_PRIVACY . '.php');
  define('FILENAME_PRODUCT_INFO', CONTENT_PRODUCT_INFO . '.php');
  define('FILENAME_PRODUCT_LISTING', CONTENT_PRODUCT_LISTING . '.php');
  define('FILENAME_FEATURED_PRODUCT_LISTING', CONTENT_FEATURED_PRODUCT_LISTING . '.php');
  define('FILENAME_PRODUCT_REVIEWS', CONTENT_PRODUCT_REVIEWS . '.php');
  define('FILENAME_PRODUCT_REVIEWS_INFO', CONTENT_PRODUCT_REVIEWS_INFO . '.php');
  define('FILENAME_PRODUCT_REVIEWS_WRITE', CONTENT_PRODUCT_REVIEWS_WRITE . '.php');
  define('FILENAME_PRODUCTS_NEW', CONTENT_PRODUCTS_NEW . '.php');
  define('FILENAME_REDIRECT', CONTENT_REDIRECT . '.php');
  define('FILENAME_REVIEWS', CONTENT_REVIEWS . '.php');
  define('FILENAME_SHIPPING', CONTENT_SHIPPING . '.php');
  define('FILENAME_SHOPPING_CART', CONTENT_SHOPPING_CART . '.php');
  define('FILENAME_SPECIALS', CONTENT_SPECIALS . '.php');
  define('FILENAME_SSL_CHECK', CONTENT_SSL_CHECK . '.php');
  define('FILENAME_TELL_A_FRIEND', CONTENT_TELL_A_FRIEND . '.php');
  define('FILENAME_UPCOMING_PRODUCTS', CONTENT_UPCOMING_PRODUCTS . '.php');
  define('FILENAME_MANAGE_QUEUE', CONTENT_MANAGE_QUEUE . '.php');

define('FILENAME_ACCOUNT_VIEW_ORDERS', CONTENT_ACCOUNT_VIEW_ORDERS . '.php');

define('FILENAME_RECENT_ADDITIONS', CONTENT_RECENT_ADDITIONS . '.php');

  //Following added on 20 Aug 2005
  define('FILENAME_RENTAL_QUEUE', CONTENT_RENTAL_QUEUE . '.php');
 //Added by Amit
  define('FILENAME_RENT_CREATE_ACCOUNT', CONTENT_RENT_CREATE_ACCOUNT . '.php');
  define('FILENAME_CHECKOUT_RENTAL_PAYMENT', CONTENT_CHECKOUT_RENTAL_PAYMENT . '.php');
  define('FILENAME_CHECKOUT_RENTAL_CONFIRMATION', CONTENT_CHECKOUT_RENTAL_CONFIRMATION . '.php');
  define('FILENAME_CHECKOUT_RENTAL_PROCESS', CONTENT_CHECKOUT_RENTAL_PROCESS . '.php');
  define('FILENAME_MEMBERSHIP', CONTENT_MEMBERSHIP . '.php');
  define('FILENAME_MEMBERSHIP_DETAILS', CONTENT_MEMBERSHIP_DETAILS . '.php');
  define('FILENAME_MEMBERSHIP_BILLING_INFO', CONTENT_MEMBERSHIP_BILLING_INFO . '.php');

  define('FILENAME_ACCOUNT_MEMBERSHIP_CANCEL', CONTENT_ACCOUNT_MEMBERSHIP_CANCEL . '.php');
  define('FILENAME_ACCOUNT_MEMBERSHIP_UPGRADE', CONTENT_ACCOUNT_MEMBERSHIP_UPGRADE . '.php');


	//Begin Checkout Without Account Modifications
  define('FILENAME_PWA_PWA_LOGIN', 'login_pwa.php');
  define('FILENAME_PWA_ACC_LOGIN', 'login_acc.php');
  define('FILENAME_ORDER_INFO', 'Order_Info.php');
  define('FILENAME_ORDER_INFO_PROCESS', 'Order_Info_Process.php.php');

	define('FILENAME_RENTED_PRODUCTS', CONTENT_RENTED_PRODUCTS . '.php');
	define('FILENAME_RENTAL_ISSUES', CONTENT_RENTAL_ISSUES . '.php');
	define('FILENAME_RENTAL_TOP', CONTENT_RENTAL_TOP . '.php');

	define('FILENAME_POPUP_TERMS', CONTENT_POPUP_TERMS . '.php');

	//Extra pages
	define('FILENAME_PAGES', CONTENT_PAGES . '.php');
	define('FILENAME_CATEGORIES_INFO', CONTENT_INDEX_NESTED . '.php');

	//Fun_ways
	define('FILENAME_FUNWAYS', CONTENT_FUNWAYS . '.php');
	define('FILENAME_POPUP_INFOBOX_HELP', CONTENT_POPUP_INFOBOX_HELP . '.php');

	//Featured
	define('FILENAME_FEATURED', CONTENT_FEATURED . '.php');
	define('FILENAME_UPCOMING', CONTENT_UPCOMING . '.php');

	define('FILENAME_POPUP_CVV_HELP', 'cvv_help.php');

 define('FILENAME_FUNWAYS_PRODUCT_LISTING', 'funways_product_listing.php');
 
  //BEGIN allprods modification
  define('FILENAME_ALLPRODS', 'allprods.php');
  //END allprods modification

  define('CONTENT_BEST_SELLERS', 'best_sellers');
  define('FILENAME_BEST_SELLERS', CONTENT_BEST_SELLERS . '.php');

/* One Page Checkout - BEGIN */  
  define('CONTENT_CHECKOUT', 'checkout');
  define('FILENAME_CHECKOUT', CONTENT_CHECKOUT . '.php');
/* One Page Checkout - END */  
	
	define('FILENAME_CENTER_ADDRESS_CHECK', 'center_address_check.php');
?>