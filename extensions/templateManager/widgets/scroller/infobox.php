<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxScroller extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('scroller');

	}

	public function show(){

		global $new_products_category_id, $storeProducts;
		$corner_left = 'square';
		$corner_right = 'square';
		$box_base_name = 'new_products'; // for easy unique box template setup (added BTSv1.2)
		$box_id = $box_base_name . 'Box';  // for CSS styling paulm (editted BTSv1.2)

		$boxWidgetProperties = $this->getWidgetProperties();
		$nr_imag = $boxWidgetProperties['nr_imag'];
		$nr_space = $boxWidgetProperties['nr_space'];

		$feat_text = $boxWidgetProperties['feat_text'];
		$best_text = $boxWidgetProperties['best_text'];
		$new_text = $boxWidgetProperties['new_text'];

		$nr_best = $boxWidgetProperties['nr_best'];
		$nr_feat = $boxWidgetProperties['nr_feat'];
		$nr_new = $boxWidgetProperties['nr_new'];

		$width_imag = $boxWidgetProperties['width_imag'];
		$height_imag = $boxWidgetProperties['height_imag'];

		//bsXlider Implementation

		if (!isset($new_products_category_id) || $new_products_category_id == '0'){
			$newProducts = $storeProducts->getNew(null,$nr_new,$width_imag,$height_imag);
		} else {
			$newProducts = $storeProducts->getNew($new_products_category_id, $nr_new, $width_imag, $height_imag);
		}

		$boxContent = '';
		if ($newProducts){
			$listItems = '';

			foreach($newProducts as $pInfo){
				$link = itw_app_link('products_id=' . $pInfo['id'], 'product', 'info');

				$listItems .= '<li>' .
				 '<a href="' . $link . '">' . $pInfo['image'] . '</a>' .
				'</li>';
			}

			$boxContent = '<ul id="newProducts">' . $listItems . '</ul>';
		}

		$featuredProducts = $storeProducts->getFeatured($nr_feat,$width_imag, $height_imag);
		$boxContent2 = '';
		if ($featuredProducts){
			$listItems = '';

			foreach($featuredProducts as $pInfo){
				$link = itw_app_link('products_id=' . $pInfo['id'], 'product', 'info');

				$listItems .= '<li>' .
				 '<a href="' . $link . '">' . $pInfo['image'] . '</a>' .
				'</li>';
			}

			$boxContent2 =
			   '<ul id="featuredProducts">' . $listItems . '</ul>';
		}

		$info_box_contents = array();
		$bestSellerProducts = $storeProducts->getBestSellers($nr_best, $width_imag, $height_imag);
		$boxContent3 = '';
		if ($bestSellerProducts){
			$listItems = '';

			foreach($bestSellerProducts as $pInfo){
				$link = itw_app_link('products_id=' . $pInfo['id'], 'product', 'info');

				$listItems .= '<li>' .
				 '<a href="' . $link . '">' . $pInfo['image'] . '</a>' .
				'</li>';
			}

			$boxContent3 =
			   '<ul id="bestSeller">' . $listItems . '</ul>';
		}
		$boxContentTempStart = '<table align="center" cellpadding="0" cellspacing="0" border="0" class="scroller_table">';
		if ($boxContent != ''){
           $boxContentTempStart .= '<tr id="tab_newProducts">' .
            '<td align="center">' . $boxContent . '</td>' .
           '</tr>';
		}
		if ($boxContent2 != ''){
           $boxContentTempStart .= '<tr id="tab_featuredProducts">' .
            '<td align="center">' . $boxContent2 . '</td>' .
           '</tr>';
		}
		if($boxContent3 != ''){
           $boxContentTempStart .= '<tr id="tab_bestSellers">' .
            '<td align="center">' . $boxContent3 . '</td>' .
           '</tr>' .
           '<tr>';
		}
        $boxContentTempStart .= '<td align="center" class="index_scroller_button_bar">';
		if ($boxContent != ''){
            $boxContentTempStart .= htmlBase::newElement('button')->setText(sysLanguage::get('CATALOG_HOME_FEATURED'))->setId('button_featured')->draw();
		}
		if ($boxContent2 != ''){
            $boxContentTempStart .= htmlBase::newElement('button')->setText(sysLanguage::get('CATALOG_HOME_NEW_PRODUCTS'))->setId('button_newproducts')->draw();
		}
		if($boxContent3 != ''){
            $boxContentTempStart .= htmlBase::newElement('button')->setText(sysLanguage::get('CATALOG_HOME_BEST_SELLERS'))->setId('button_bestsellers')->draw();
		}
        $boxContentTempStart .= '</td>' .
           '</tr>' .
          '</table>';


		$boxContentTemp = '<script type="text/javascript" src="'.sysConfig::getDirWsCatalog(). 'ext/jQuery/external/reflection/reflection.js'.'"></script>'.
					  '<script type="text/javascript" src="'.sysConfig::getDirWsCatalog(). 'ext/jQuery/external/jquery.bxSlider/jquery.bxSlider.min.js'.'"></script>'.
					  '<link rel="stylesheet" href="'.sysConfig::getDirWsCatalog(). 'ext/jQuery/external/jquery.bxSlider/bx_styles/bx_styles.css" type="text/css" />'.
					  '<script type="text/javascript" src="'.sysConfig::getDirWsCatalog(). 'includes/modules/infoboxes/scroller/javascript/scroller.js'.'"></script>'.
			          '<script stype="text/javascript">$(document).ready(function (){';

		$boxContentTemp .=" $('#newProducts li').css('width','".($width_imag+$nr_space)."');
							$('#featuredProducts li').css('width','".($width_imag+$nr_space)."');
							$('#bestSeller li').css('width','".($width_imag+$nr_space)."');
						var np = $('#newProducts').bxSlider({
							displaySlideQty: ".$nr_imag.",
							moveSlideQty: 2
						});
						np.goToLastSlide();
						np.goToNextSlide();
						var ft = $('#featuredProducts').bxSlider({
									displaySlideQty: ".$nr_imag.",
									moveSlideQty: 2
						});
						ft.goToLastSlide();
						ft.goToNextSlide();
						var bs = $('#bestSeller').bxSlider({
									displaySlideQty: ".$nr_imag.",
									moveSlideQty: 2
						});
						bs.goToLastSlide();
						bs.goToNextSlide();
		                $('#button_bestsellers').click(function(){
		                	$(this).parent().parent().parent().parent().parent().parent().find('.ui-infobox-header-text').first().html('".$best_text."');
		                });
		                $('#button_featured').click(function(){
		                	$(this).parent().parent().parent().parent().parent().parent().find('.ui-infobox-header-text').first().html('".$feat_text."');
		                });
		                $('#button_newproducts').click(function(){
		                	$(this).parent().parent().parent().parent().parent().parent().find('.ui-infobox-header-text').first().html('".$new_text."');
		                });
						if(typeof afterScrollerExecute == 'function') {
							afterScrollerExecute();
						}";

			$boxContentTemp .=  '});</script>' . $boxContentTempStart;

		$this->setBoxContent($boxContentTemp);
		return $this->draw();

	}
}