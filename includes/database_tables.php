<?php
/*
  $Id: database_tables.php,v 1.1 2003/03/14 02:10:58 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// define the database table names used in the project
  define('TABLE_ADDRESS_BOOK', 'address_book');
  define('TABLE_ADDRESS_FORMAT', 'address_format');
  define('TABLE_BANNERS', 'banners');
  define('TABLE_BANNERS_HISTORY', 'banners_history');
  define('TABLE_CATEGORIES', 'categories');
  define('TABLE_CATEGORIES_DESCRIPTION', 'categories_description');
  define('TABLE_CONFIGURATION', 'configuration');
  define('TABLE_CONFIGURATION_GROUP', 'configuration_group');
  define('TABLE_COUNTER', 'counter');
  define('TABLE_COUNTER_HISTORY', 'counter_history');
  define('TABLE_COUNTRIES', 'countries');
  define('TABLE_CURRENCIES', 'currencies');
  define('TABLE_CUSTOMERS', 'customers');
  define('TABLE_CUSTOMERS_BASKET', 'customers_basket');
  define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', 'customers_basket_attributes');
  define('TABLE_CUSTOMERS_INFO', 'customers_info');
  define('TABLE_LANGUAGES', 'languages');
  define('TABLE_MANUFACTURERS', 'manufacturers');
  define('TABLE_MEMBER', 'membership');
  define('TABLE_MANUFACTURERS_INFO','manufacturers_info');
  define('TABLE_ORDERS', 'orders');
  define('TABLE_ORDERS_PRODUCTS', 'orders_products');
  define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', 'orders_products_attributes');
  define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', 'orders_products_download');
  define('TABLE_ORDERS_STATUS', 'orders_status');
  define('TABLE_ORDERS_STATUS_HISTORY', 'orders_status_history');
  define('TABLE_ORDERS_TOTAL', 'orders_total');
  define('TABLE_PRODUCTS', 'products');
  define('TABLE_PRODUCTS_ATTRIBUTES', 'products_attributes');
  define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', 'products_attributes_download');
  define('TABLE_PRODUCTS_DESCRIPTION', 'products_description');
  define('TABLE_PRODUCTS_NOTIFICATIONS', 'products_notifications');
  define('TABLE_PRODUCTS_OPTIONS', 'products_options');
  define('TABLE_PRODUCTS_OPTIONS_VALUES', 'products_options_values');
  define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', 'products_options_values_to_products_options');
  define('TABLE_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
  define('TABLE_REVIEWS', 'reviews');
  define('TABLE_REVIEWS_DESCRIPTION', 'reviews_description');
  define('TABLE_SESSIONS', 'sessions');
  define('TABLE_SPECIALS', 'specials');
  define('TABLE_TAX_CLASS', 'tax_class');
  define('TABLE_TAX_RATES', 'tax_rates');
  define('TABLE_GEO_ZONES', 'geo_zones');
  define('TABLE_ZONES_TO_GEO_ZONES', 'zones_to_geo_zones');
  define('TABLE_WHOS_ONLINE', 'whos_online');
  define('TABLE_ZONES', 'zones');
  define('TABLE_MANAGE_QUEUE', 'manage_queue');

  //Following added on 20 Aug 2005
  define('TABLE_RENTAL_QUEUE', 'rental_queue');
  define('TABLE_RENTED_QUEUE', 'rented_queue');
  define('TABLE_RENTED_PRODUCTS', 'rented_products');
  define('TABLE_MEMBERSHIP','membership');
  define('TABLE_PRODUCTS_BARCODE', 'products_barcode');
  //**********Begin Rental Issues added by Yogesh on 15th oct
  define('TABLE_RENTAL_ISSUES', 'rental_issues');


  define('TABLE_SOURCES', 'sources');//rmh referral
  define('TABLE_SOURCES_OTHER', 'sources_other');//rmh referral

  define('TABLE_RENT_INVENTORY', 'rent_inventory');
  define('TABLE_RENTAL_AVAILABILITY', 'rental_availability');
  define('TABLE_RENTAL_TOP', 'rental_top');
  define('TABLE_MEMBERSHIP_UPDATE', 'membership_update');
  define('TABLE_MEMBERSHIP_UPDATE_TEMP', 'membership_update_temp');

  define('TABLE_PRODUCTS_BOX', 'products_box');
  define('TABLE_PRODUCTS_TO_BOX', 'products_to_box');

  //Extra pages
define('TABLE_PAGES', 'pages');
define('TABLE_PAGES_DESCRIPTION', 'pages_description');


//Fun_ways
define('TABLE_FUNWAYS_PRODUCTS', 'funways_products');
define('TABLE_FUNWAYS_CATEGORIES', 'funways_categories');
define('TABLE_FUNWAYS_PTC', 'funways_products_to_categories');
  define('TABLE_THEME_CONFIGURATION', 'theme_configuration');

 //Articles
   define('TABLE_ARTICLE_REVIEWS', 'article_reviews');
  define('TABLE_ARTICLE_REVIEWS_DESCRIPTION', 'article_reviews_description');
  define('TABLE_ARTICLES', 'articles');
  define('TABLE_ARTICLES_DESCRIPTION', 'articles_description');
  define('TABLE_ARTICLES_TO_TOPICS', 'articles_to_topics');
  define('TABLE_ARTICLES_XSELL', 'articles_xsell');
  define('TABLE_AUTHORS', 'authors');
  define('TABLE_AUTHORS_INFO', 'authors_info');
  define('TABLE_TOPICS', 'topics');
  define('TABLE_TOPICS_DESCRIPTION', 'topics_description');

  define('TABLE_MEMBERSHIP_BILLING_REPORT', 'membership_billing_report');

  define('TABLE_RENTAL_RATINGS', 'rental_ratings');
  define('TABLE_RATIO_PRIORITY', 'ratio_priority');
  
define('TABLE_ORDERS_PRODUCTS_STREAM', 'orders_products_stream');
	
	define('TABLE_PRODUCTS_PAY_PER_RENTAL', 'products_pay_per_rental');
	
	define('TABLE_PRODUCTS_INVENTORY', 'products_inventory');
	define('TABLE_PRODUCTS_INVENTORY_BARCODES', 'products_inventory_barcodes');
	define('TABLE_PRODUCTS_INVENTORY_BARCODES_COMMENTS', 'products_inventory_barcodes_comments');
	define('TABLE_PRODUCTS_INVENTORY_CENTERS', 'products_inventory_centers');
	define('TABLE_PRODUCTS_INVENTORY_BARCODES_TO_INVENTORY_CENTERS', 'products_inventory_barcodes_to_inventory_centers');
	define('TABLE_CUSTOMERS_TO_INVENTORY_CENTERS', 'customers_to_inventory_centers');
	define('TABLE_PRODUCTS_INVENTORY_CENTERS_QUANTITY', 'products_inventory_centers_quantity');
	define('TABLE_PRODUCTS_INVENTORY_QUANTITY', 'products_inventory_quantity');
	
	define('TABLE_PRODUCTS_PACKAGES', 'products_packages');
	define('TABLE_ORDERS_ADDRESSES', 'orders_addresses');
	
define('TABLE_PRODUCTS_CUSTOM_FIELDS', 'products_custom_fields');
define('TABLE_PRODUCTS_CUSTOM_FIELDS_DESCRIPTION', 'products_custom_fields_description');
define('TABLE_PRODUCTS_CUSTOM_FIELDS_GROUPS', 'products_custom_fields_groups');
define('TABLE_PRODUCTS_CUSTOM_FIELDS_OPTIONS_TO_FIELDS', 'products_custom_fields_options_to_fields');
define('TABLE_PRODUCTS_CUSTOM_FIELDS_OPTIONS', 'products_custom_fields_options');
define('TABLE_PRODUCTS_CUSTOM_FIELDS_OPTIONS_DESCRIPTION', 'products_custom_fields_options_description');
define('TABLE_PRODUCTS_CUSTOM_FIELDS_TO_GROUPS', 'products_custom_fields_to_groups');
define('TABLE_PRODUCTS_CUSTOM_FIELDS_TO_PRODUCTS', 'products_custom_fields_to_products');
define('TABLE_PRODUCTS_CUSTOM_FIELDS_GROUPS_TO_PRODUCTS', 'products_custom_fields_groups_to_products');
?>