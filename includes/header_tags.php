<?php
/*
/catalog/includes/header_tags.php
WebMakers.com Added: Header Tags Generator v2.0
Add META TAGS and Modify sysLanguage::get('TITLE')

NOTE: Globally replace all fields in products table with current product name just to get things started:
In phpMyAdmin use: UPDATE products_description set PRODUCTS_HEAD_TITLE_TAG = PRODUCTS_NAME

Shoppe Enhancement Controller - Copyright (c) 2003 WebMakers.com
Linda McGrath - osCommerce@WebMakers.com
*/

global $category_depth, $current_category_id, $App;

sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/header_tags.xml');

$tags_array = array();

// Define specific settings per page:
switch (true) {
	// ALLPRODS.PHP
	case ($App->getAppName() == 'products' && $App->getPageName() == 'all'):
		$the_category_query = tep_db_query("select cd.categories_name from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . $current_category_id . "' and cd.categories_id = '" . $current_category_id . "' and cd.language_id = '" . Session::get('languages_id') . "'");
		$the_category = tep_db_fetch_array($the_category_query);

		if (isset($_GET['manufacturers_id'])){
			$the_manufacturers_query= tep_db_query("select manufacturers_name from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . $_GET['manufacturers_id'] . "'");
			$the_manufacturers = tep_db_fetch_array($the_manufacturers_query);
		}

		if (HTDA_ALLPRODS_ON=='1') {
			$tags_array['desc']= HEAD_DESC_TAG_ALLPRODS . ' ' . HEAD_DESC_TAG_ALL;
		} else {
			$tags_array['desc']= HEAD_DESC_TAG_ALLPRODS;
		}

		if (HTKA_ALLPRODS_ON=='1') {
			$tags_array['keywords']= HEAD_KEY_TAG_ALL . ' ' . HEAD_KEY_TAG_ALLPRODS;
		} else {
			$tags_array['keywords']= HEAD_KEY_TAG_ALLPRODS;
		}

		if (HTTA_ALLPRODS_ON=='1') {
			$tags_array['title']= HEAD_TITLE_TAG_ALLPRODS . ' ' . HEAD_TITLE_TAG_ALL . " " . $the_category['categories_name'];
			if (isset($the_manufacturers)){
				$tags_array['title'] .= $the_manufacturers['manufacturers_name'];
			}
		} else {
			$tags_array['title']= HEAD_TITLE_TAG_ALLPRODS;
		}
		break;

		// INDEX.PHP
	case (strstr($_SERVER['PHP_SELF'],FILENAME_DEFAULT) or strstr($_SERVER['PHP_SELF'],FILENAME_DEFAULT) ):

		$showCatTags = false;
		if ($category_depth == 'nested' || $category_depth == 'products') {
			$the_category_query = tep_db_query("select categories_name as name, categories_htc_title_tag as htc_title_tag, categories_htc_desc_tag as htc_desc_tag, categories_htc_keywords_tag as htc_keywords_tag from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$current_category_id . "' and language_id = '" . (int)Session::get('languages_id') . "'");
			$showCatTags = true;
		} else if (isset($_GET['manufacturers_id'])) {
			$the_category_query= tep_db_query("select m.manufacturers_name as name, mi.manufacturers_htc_title_tag as htc_title_tag, mi.manufacturers_htc_desc_tag as htc_desc_tag, mi.manufacturers_htc_keywords_tag as htc_keywords_tag from " . TABLE_MANUFACTURERS . " m LEFT JOIN " . TABLE_MANUFACTURERS_INFO . " mi on m.manufacturers_id = mi.manufacturers_id where m.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "'");
			$showCatTags = true;
		} else {
			$the_category_query = tep_db_query("select categories_name as name, categories_htc_title_tag as htc_title_tag, categories_htc_desc_tag as htc_desc_tag, categories_htc_keywords_tag as htc_keywords_tag from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$current_category_id . "' and language_id = '" . (int)Session::get('languages_id') . "'");
		}

		$the_category = tep_db_fetch_array($the_category_query);

		if (HTDA_DEFAULT_ON=='1') {
			if ($showCatTags == true) {
				if (HTTA_CAT_DEFAULT_ON=='1') {
					$tags_array['desc']= $the_category['htc_desc_tag'] . ' ' . HEAD_DESC_TAG_DEFAULT . ' ' . HEAD_DESC_TAG_ALL;
				} else {
					$tags_array['desc']= $the_category['htc_desc_tag'] . ' ' . HEAD_DESC_TAG_ALL;
				}
			} else {
				$tags_array['desc']= HEAD_DESC_TAG_DEFAULT . ' ' . HEAD_DESC_TAG_ALL;
			}
		} else {
			if ($showCatTags == true) {
				if (HTTA_CAT_DEFAULT_ON=='1') {
					$tags_array['desc']= $the_category['htc_desc_tag'] . ' ' . HEAD_DESC_TAG_DEFAULT;
				} else {
					$tags_array['desc']= $the_category['htc_desc_tag'];
				}
			} else {
				$tags_array['desc']= HEAD_DESC_TAG_DEFAULT;
			}
		}

		if (HTKA_DEFAULT_ON=='1') {
			if ($showCatTags == true) {
				if (HTTA_CAT_DEFAULT_ON=='1') {
					$tags_array['keywords']= $the_category['htc_keywords_tag'] . ', ' . HEAD_KEY_TAG_ALL . ' ' . HEAD_KEY_TAG_DEFAULT;
				} else {
					$tags_array['keywords']= $the_category['htc_keywords_tag'] .  ', ' . HEAD_KEY_TAG_DEFAULT;
				}
			} else {
				$tags_array['keywords']= HEAD_KEY_TAG_ALL . ', ' . HEAD_KEY_TAG_DEFAULT;
			}
		} else {
			if ($showCatTags == true) {
				if (HTTA_CAT_DEFAULT_ON=='1') {
					$tags_array['keywords']= $the_category['htc_keywords_tag'] . ', ' . HEAD_KEY_TAG_DEFAULT;
				} else {
					$tags_array['keywords']= $the_category['htc_keywords_tag'];
				}
			} else {
				$tags_array['keywords']= HEAD_KEY_TAG_DEFAULT;
			}
		}

		if (HTTA_DEFAULT_ON=='1') {
			if ($showCatTags == true) {
				if (HTTA_CAT_DEFAULT_ON=='1') {
					$tags_array['title']= $the_category['htc_title_tag'] .' '.  HEAD_TITLE_TAG_DEFAULT;
					if (isset($_GET['manufacturers_id'])){
						$tags_array['title'] .= " " .  $the_category['manufacturers_name'];
					}
					$tags_array['title'] .= ' - ' . HEAD_TITLE_TAG_ALL;
				} else {
					$tags_array['title']= $the_category['htc_title_tag'] .' '.  $the_category['manufacturers_htc_title_tag'] . ' - ' . HEAD_TITLE_TAG_ALL;
				}
			} else {
				$tags_array['title']= HEAD_TITLE_TAG_DEFAULT . " " . $the_category['name'] . $the_category['manufacturers_htc_title_tag'] . ' - ' . HEAD_TITLE_TAG_ALL;
			}
		} else {
			if ($showCatTags == true) {
				if (HTTA_CAT_DEFAULT_ON=='1') {
					$tags_array['title']= $the_category['htc_title_tag'] . ' ' . HEAD_TITLE_TAG_DEFAULT;
				} else {
					$tags_array['title']= $the_category['htc_title_tag'];
				}
			} else {
				$tags_array['title']= HEAD_TITLE_TAG_DEFAULT;
			}
		}

		break;

		// PRODUCT_INFO.PHP
	case ($App->getAppName() == 'product' && $App->getPageName() == 'info'):
		//    $the_product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, pd.products_head_title_tag, pd.products_head_keywords_tag, pd.products_head_desc_tag, p.products_model, p.products_quantity, p.products_image, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . $_GET['products_id'] . "' and pd.products_id = '" . $_GET['products_id'] . "'");
		$the_product_info_query = tep_db_query("select pd.language_id, p.products_id, pd.products_name, pd.products_description, pd.products_head_title_tag, pd.products_head_keywords_tag, pd.products_head_desc_tag, p.products_model, p.products_quantity, p.products_image, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . $_GET['products_id'] . "' and pd.products_id = '" . $_GET['products_id'] . "'" . " and pd.language_id ='" .  Session::get('languages_id') . "'");
		$the_product_info = tep_db_fetch_array($the_product_info_query);

		if (empty($the_product_info['products_head_desc_tag'])) {
			if (HTTA_CAT_PRODUCT_DEFAULT_ON=='1') {
				$tags_array['desc']= HEAD_DESC_TAG_PRODUCT_INFO;
			}
			if (HTDA_PRODUCT_INFO_ON=='1' || empty($tags_array['desc'])) {
				$tags_array['desc']= HEAD_DESC_TAG_ALL;
			}
		} else {
			$tags_array['desc']= $the_product_info['products_head_desc_tag'];
			if (HTTA_CAT_PRODUCT_DEFAULT_ON=='1') {
				$tags_array['desc'] .= ' ' . HEAD_DESC_TAG_PRODUCT_INFO;
			}
			if ( HTDA_PRODUCT_INFO_ON=='1' ) {
				$tags_array['desc'] .= ' ' . HEAD_DESC_TAG_ALL;
			}
		}

		if (empty($the_product_info['products_head_keywords_tag'])) {
			if (HTTA_CAT_PRODUCT_DEFAULT_ON=='1') {
				$tags_array['keywords']= HEAD_KEY_TAG_PRODUCT_INFO;
			}
			if ( HTKA_PRODUCT_INFO_ON=='1' || empty($tags_array['keywords'])) {
				$tags_array['keywords']= HEAD_KEY_TAG_ALL;
			}
		} else {
			$tags_array['keywords']= $the_product_info['products_head_keywords_tag'];
			if (HTTA_CAT_PRODUCT_DEFAULT_ON=='1') {
				$tags_array['keywords'] .= ' ' . HEAD_KEY_TAG_PRODUCT_INFO;
			}
			if ( HTKA_PRODUCT_INFO_ON=='1' ) {
				$tags_array['keywords'] .= ' ' . HEAD_KEY_TAG_ALL;
			}
		}

		if (empty($the_product_info['products_head_title_tag'])) {   //if not HTC title in product
			if (HTTA_CAT_PRODUCT_DEFAULT_ON=='1') {                    //if HTCA checked
				$tags_array['title']= HEAD_TITLE_TAG_PRODUCT_INFO;       //show title for this section
			}
			if ( HTTA_PRODUCT_INFO_ON=='1' || empty($tags_array['title'])) { //if default switch on or no entry
				$tags_array['title']= HEAD_TITLE_TAG_ALL;               //include the default text
			}
		} else {
			$tags_array['title']= clean_html_comments($the_product_info['products_head_title_tag']);
			if (HTTA_CAT_PRODUCT_DEFAULT_ON=='1') {
				$tags_array['title'] .= ' ' . HEAD_TITLE_TAG_PRODUCT_INFO;
			}
			if ( HTTA_PRODUCT_INFO_ON=='1' ) {
				$tags_array['title'] .= ' ' . HEAD_TITLE_TAG_ALL;
			}
		}

		break;


		// PRODUCTS_NEW.PHP
	case ($App->getAppName() == 'products' && $App->getPageName() == 'new'):
		if ( HEAD_DESC_TAG_WHATS_NEW!='' ) {
			if ( HTDA_WHATS_NEW_ON=='1' ) {
				$tags_array['desc']= HEAD_DESC_TAG_WHATS_NEW . ' ' . HEAD_DESC_TAG_ALL;
			} else {
				$tags_array['desc']= HEAD_DESC_TAG_WHATS_NEW;
			}
		} else {
			$tags_array['desc']= HEAD_DESC_TAG_ALL;
		}

		if ( HEAD_KEY_TAG_WHATS_NEW!='' ) {
			if ( HTKA_WHATS_NEW_ON=='1' ) {
				$tags_array['keywords']= HEAD_KEY_TAG_WHATS_NEW . ' ' . HEAD_KEY_TAG_ALL;
			} else {
				$tags_array['keywords']= HEAD_KEY_TAG_WHATS_NEW;
			}
		} else {
			$tags_array['keywords']= HEAD_KEY_TAG_ALL;
		}

		if ( HEAD_TITLE_TAG_WHATS_NEW!='' ) {
			if ( HTTA_WHATS_NEW_ON=='1' ) {
				$tags_array['title']= HEAD_TITLE_TAG_WHATS_NEW . ' ' . HEAD_TITLE_TAG_ALL;
			} else {
				$tags_array['title']= HEAD_TITLE_TAG_WHATS_NEW;
			}
		} else {
			$tags_array['title']= HEAD_TITLE_TAG_ALL;
		}

		break;


		// SPECIALS.PHP
	case ( strstr($_SERVER['PHP_SELF'],FILENAME_SPECIALS)  or strstr($_SERVER['PHP_SELF'],FILENAME_SPECIALS) ):
		if ( HEAD_DESC_TAG_SPECIALS!='' ) {
			if ( HTDA_SPECIALS_ON=='1' ) {
				$tags_array['desc']= HEAD_DESC_TAG_SPECIALS . ' ' . HEAD_DESC_TAG_ALL;
			} else {
				$tags_array['desc']= HEAD_DESC_TAG_SPECIALS;
			}
		} else {
			$tags_array['desc']= HEAD_DESC_TAG_ALL;
		}

		if ( HEAD_KEY_TAG_SPECIALS=='' ) {
			// Build a list of ALL specials product names to put in keywords
			$new = tep_db_query("select p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.products_image, s.specials_new_products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s where p.products_status = '1' and s.products_id = p.products_id and p.products_id = pd.products_id and pd.language_id = '" . Session::get('languages_id') . "' and s.status = '1' order by s.specials_date_added DESC ");
			$row = 0;
			$the_specials='';
			while ($new_values = tep_db_fetch_array($new)) {
				$the_specials .= clean_html_comments($new_values['products_name']) . ', ';
			}
			if ( HTKA_SPECIALS_ON=='1' ) {
				$tags_array['keywords']= $the_specials . ' ' . HEAD_KEY_TAG_ALL;
			} else {
				$tags_array['keywords']= $the_specials;
			}
		} else {
			$tags_array['keywords']= HEAD_KEY_TAG_SPECIALS . ' ' . HEAD_KEY_TAG_ALL;
		}

		if ( HEAD_TITLE_TAG_SPECIALS!='' ) {
			if ( HTTA_SPECIALS_ON=='1' ) {
				$tags_array['title']= HEAD_TITLE_TAG_SPECIALS . ' ' . HEAD_TITLE_TAG_ALL;
			} else {
				$tags_array['title']= HEAD_TITLE_TAG_SPECIALS;
			}
		} else {
			$tags_array['title']= HEAD_TITLE_TAG_ALL;
		}

		break;


		// PRODUCTS_REVIEWS_INFO.PHP and PRODUCTS_REVIEWS.PHP
	case((basename($_SERVER['PHP_SELF'])==FILENAME_PRODUCT_REVIEWS) or (basename($_SERVER['PHP_SELF'])==FILENAME_PRODUCT_REVIEWS_INFO)):
		if ( HEAD_DESC_TAG_PRODUCT_REVIEWS_INFO=='' ) {
			if ( HTDA_PRODUCT_REVIEWS_INFO_ON=='1' ) {
				$tags_array['desc']= tep_get_header_tag_products_desc($_GET['reviews_id']) . ' ' . HEAD_DESC_TAG_ALL;
			} else {
				$tags_array['desc']= tep_get_header_tag_products_desc($_GET['reviews_id']);
			}
		} else {
			$tags_array['desc']= HEAD_DESC_TAG_PRODUCT_REVIEWS_INFO;
		}

		if ( HEAD_KEY_TAG_PRODUCT_REVIEWS_INFO=='' ) {
			if ( HTKA_PRODUCT_REVIEWS_INFO_ON=='1' ) {
				$tags_array['keywords']= tep_get_header_tag_products_keywords($_GET['reviews_id']) . ' ' . HEAD_KEY_TAG_ALL;
			} else {
				$tags_array['keywords']= tep_get_header_tag_products_keywords($_GET['reviews_id']);
			}
		} else {
			$tags_array['keywords']= HEAD_KEY_TAG_PRODUCT_REVIEWS_INFO;
		}

		if ( HEAD_TITLE_TAG_PRODUCT_REVIEWS_INFO=='' ) {
			if ( HTTA_PRODUCT_REVIEWS_INFO_ON=='1' ) {
				$tags_array['title']= ' Reviews: ' . tep_get_header_tag_products_title($_GET['reviews_id']) . HEAD_TITLE_TAG_ALL;
			} else {
				$tags_array['title']= tep_get_header_tag_products_title($_GET['reviews_id']);
			}
		} else {
			$tags_array['title']= HEAD_TITLE_TAG_PRODUCT_REVIEWS_INFO;
		}
		break;

		// PRODUCTS_REVIEWS_WRITE.PHP
	case((basename($_SERVER['PHP_SELF'])==FILENAME_PRODUCT_REVIEWS_WRITE)):
		if ( HEAD_DESC_TAG_PRODUCT_REVIEWS_WRITE=='' ) {
			if ( HTDA_PRODUCT_REVIEWS_WRITE_ON=='1' ) {
				$tags_array['desc']= tep_get_header_tag_products_desc($_GET['reviews_id']) . ' ' . HEAD_DESC_TAG_ALL;
			} else {
				$tags_array['desc']= tep_get_header_tag_products_desc($_GET['reviews_id']);
			}
		} else {
			$tags_array['desc']= HEAD_DESC_TAG_PRODUCT_REVIEWS_WRITE;
		}

		if ( HEAD_KEY_TAG_PRODUCT_REVIEWS_WRITE=='' ) {
			if ( HTKA_PRODUCT_REVIEWS_WRITE_ON=='1' ) {
				$tags_array['keywords']= tep_get_header_tag_products_keywords($_GET['reviews_id']) . ' ' . HEAD_KEY_TAG_ALL;
			} else {
				$tags_array['keywords']= tep_get_header_tag_products_keywords($_GET['reviews_id']);
			}
		} else {
			$tags_array['keywords']= HEAD_KEY_TAG_PRODUCT_REVIEWS_WRITE;
		}

		if ( HEAD_TITLE_TAG_PRODUCT_REVIEWS_WRITE=='' ) {
			if ( HTTA_PRODUCT_REVIEWS_WRITE_ON=='1' ) {
				$tags_array['title']= ' Reviews: ' . tep_get_header_tag_products_title($_GET['reviews_id']) . HEAD_TITLE_TAG_ALL;
			} else {
				$tags_array['title']= tep_get_header_tag_products_title($_GET['reviews_id']);
			}
		} else {
			$tags_array['title']= HEAD_TITLE_TAG_PRODUCT_REVIEWS_WRITE;
		}
		break;


		// ALL OTHER PAGES NOT DEFINED ABOVE
	default:
		$tags_array['desc'] = HEAD_DESC_TAG_ALL;
		$tags_array['keywords'] = HEAD_KEY_TAG_ALL;
		$tags_array['title'] = HEAD_TITLE_TAG_ALL;
		break;
}

EventManager::notify('HeaderTagsBeforeOutput', &$tags_array);

echo '  <title>' . stripslashes($tags_array['title']) . '</title>' . "\n";
echo '  <meta name="Description" content="' . $tags_array['desc'] . '" />' . "\n";
echo '  <meta name="Keywords" content="' . $tags_array['keywords'] . '" />' . "\n";
echo ' <meta http-equiv="Content-Type" content="text/html; charset=' . sysLanguage::getCharset()  . '" />'."\n";
//NOTE: If you want your email add to your source code, remove the two slashes on the
//following line of code. This serves no useful purpose and is not suggested tobe used
//echo '  <meta name="Reply-to" content="' . HEAD_REPLY_TAG_ALL . '"/>' . "\n";

echo '<!-- EOF: Generated Meta Tags -->' . "\n";
?>
