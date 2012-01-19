<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxProductImages extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('productImages');
	}

	public function buildStylesheet() {
		ob_start();
		readfile(sysConfig::getDirFsCatalog().'ext/jQuery/external/fancybox/jquery.fancybox.css');
		readfile(sysConfig::getDirFsCatalog().'ext/jQuery/external/jqzoom/jquery.jqzoom.css');


		?>
	.productImageGalleryFeatured a { border:1px solid transparent;display:inline-block;vertical-align:middle;margin:.2em; }
	<?php
 		//include(sysConfig::getDirFsCatalog() .'ext/jQuery/external/fancybox/jquery.fancybox.css');
		$css = ob_get_contents();
		ob_end_clean();

		return $css;
	}



	public function buildJavascript() {
		$imgWidth = 560;
		$imgHeight = 560;
		ob_start();
		readfile(sysConfig::getDirFsCatalog().'ext/jQuery/external/fancybox/jquery.fancybox.js');
		readfile(sysConfig::getDirFsCatalog().'ext/jQuery/external/jqzoom/jquery.jqzoom.js');
		?>
	var mainProductImageSrc = $('#productsImageFeatured img').attr('src');
	var myind = 0;
	$('.additionalImage').live('click',function (e){
	$('.fancyBox.addSelected').removeClass('addSelected');
	$(this).parent().addClass('addSelected');

	$('#productsImageFeatured img')
	.attr('src', $(this).attr('imgSrc'))
	.attr('alt', $(this).parent().attr('href'));

	$('#productsImageFeatured')
	.attr('href', $(this).attr('imgSrc').replace('&width=<?php echo $imgWidth;?>&height=<?php echo $imgHeight;?>',''));

	myind = $(this).parent().attr('index');

	return false;
	});



	$('#productsImageFeatured').live('click', function(){
	var arr = new Array();
	$('a[rel=gallery]').each(function(){
	arr.push($(this).attr('href'));
	});

	$.fancybox(arr,{
	speedIn: 500,
	speedOut: 500,
	overlayShow: false,
	index: parseInt(myind),
	type: 'image',
	titleShow:false
	});
	return false;

	});

	<?php
 		$js = ob_get_contents();
		ob_end_clean();

		return $js;
	}


	public function show(){
		global $appExtension;
		$productsImage = '';

		$imgWidth = '440';
		$imgHeight = '440';
		if (isset($_GET['products_id'])){
			$Product = new product((int)$_GET['products_id']);
			$productImage = $Product->getImage();
			$thumbUrl = 'imagick_thumb.php?path=rel&imgSrc=';

			$image = $thumbUrl  . $productImage;
			EventManager::notify('ProductInfoProductsImageShow', &$image, &$Product);

			$productsImage = '<div>' ;

			$AdditionalImages = Doctrine_Query::create()
				->select('file_name')
				->from('ProductsAdditionalImages')
				->where('products_id = ?', $_GET['products_id'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$productsImage .=
				'<div style="margin-left:90px;text-align:center;display:inline-block;"><a id="productsImageFeatured" class="fancyBox" href="'.$image.'">' .
					'<img class="" src="' . $image . '&width='.$imgWidth.'&height='.$imgHeight.'" alt="' . $image . '" /><br />' .
					'' .
					'</a>' .
					'</div>';
			if (sizeof($AdditionalImages) > 0){
				$productsImage .= '<div style="margin-left:40px;margin-top:30px;vertical-align:top;display:inline-block;" class="productImageGalleryFeatured">' .

					'<a class="fancyBox addSelected" style="display:block" index="0" rel="gallery" href="' . $image . '"><img class="additionalImage" imgSrc="' . $image . '&width='.$imgWidth.'&height='.$imgHeight.'" src="' . $image . '&width=50&height=50"></a>';

				$imgSrc =  'images/';
				$ind = 0;
				foreach($AdditionalImages as $imgInfo){
					$addImage = $thumbUrl . $imgSrc . $imgInfo['file_name'];
					$productImageSrc = $addImage . '&width='.$imgWidth.'&height='.$imgHeight.'';
					$thumbSrc = $addImage . '&width=50&height=50';
					$ind++;

					$productsImage .= '<a style="display:block;" class="fancyBox" index="'.$ind.'" rel="gallery" href="' . $addImage . '"><img class="additionalImage" imgSrc="' . $productImageSrc . '" src="' . $thumbSrc . '"></a>';
				}

				$productsImage .= '</div>';
			}else{
				$productsImage .= '<a class="fancyBox" style="display:none" index="0" rel="gallery" href="' . $image . '"><img class="additionalImage" imgSrc="' . $image . '&width='.$imgWidth.'&height='.$imgHeight.'" src="' . $image . '&width=50&height=50"></a>';
			}


			$productsImage .= '</div>';

		}
		$this->setBoxContent($productsImage);
		return $this->draw();
	}

}
?>