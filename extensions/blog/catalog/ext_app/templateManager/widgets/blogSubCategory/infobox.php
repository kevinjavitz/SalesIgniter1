<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxBlogSubCategory extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('blogSubCategory', __DIR__);

	}

	public function show(){
		global $appExtension, $pageContent;
		$boxWidgetProperties = $this->getWidgetProperties();

		$imageWidth = $boxWidgetProperties->image_width;
		$imageHeight = $boxWidgetProperties->image_height;
		$showImage = (isset($boxWidgetProperties->showImage)&& $boxWidgetProperties->showImage == 'showImage')?true:false;
		$showTitle = (isset($boxWidgetProperties->showTitle)&& $boxWidgetProperties->showTitle == 'showTitle')?true:false;
		$showReadMore = (isset($boxWidgetProperties->showReadMore)&& $boxWidgetProperties->showReadMore=='showReadMore')?true:false;
		$showDesc = (isset($boxWidgetProperties->showDesc)&& $boxWidgetProperties->showDesc=='showDesc')?true:false;
		$imageHasLink = (isset($boxWidgetProperties->imageHasLink)&& $boxWidgetProperties->imageHasLink=='imageHasLink')?true:false;
		$descLength = $boxWidgetProperties->descLength;
		$blog = $appExtension->getExtension('blog');

		if ($_GET['appPage'] == 'default'){
			$app_pg = null;
		}else{
			$app_pg = $_GET['appPage'];
		}


		$posts = $blog->getCategories(null, (isset($boxWidgetProperties->new_selected_category)?$boxWidgetProperties->new_selected_category:'-1'));

		$contentHtml = '';
		foreach ($posts as $post){
			$contentHtml.= '<div class="blogCategoryItem">';
			if($showImage){

				$thumbUrl = 'imagick_thumb.php?path=rel&imgSrc=images/'.$post['categories_image'];
				if($imageWidth > 0 && $imageHeight > 0){
					$thumbUrl .= '&width='.$imageWidth.'&height='.$imageHeight;
				}
				$thumbUrlOver = '';
				if(file_exists(sysConfig::getDirFsCatalog() .'images/hover'.$post['categories_image'])){
					$thumbUrlOver =  'imagick_thumb.php?path=rel&imgSrc=images/hover'.$post['categories_image'];
					if($imageWidth > 0 && $imageHeight > 0){
						$thumbUrlOver .= '&width='.$imageWidth.'&height='.$imageHeight;
					}
				}

				$contentHtml.= '<div class="pictCategoryPart">';
				if($thumbUrl !=''){
					if($imageHasLink){
						$contentHtml.= "<a class='blogcategoryimageLink' href='" . itw_app_link('appExt=blog', 'show_category', $post['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_seo_url']) . "'>";
					}
					$contentHtml .= '<img src="'.$thumbUrl.'"';
					if($thumbUrlOver != ''){
						$contentHtml .= 'onmouseover="this.src=\''.$thumbUrlOver.'\'" onmouseout="this.src=\''.$thumbUrl.'\'"';
					}
					$contentHtml .= '/>';
					if($imageHasLink){
						$contentHtml.= '</a>';
					}
				}
				$contentHtml.= '</div>';

			}

			$contentHtml.= '<div class="textCategoryPart">';
			if($showTitle){
				$contentHtml.= "<h2 class='blog_category_title'><a href='" . itw_app_link('appExt=blog', 'show_category', $post['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_seo_url']) . "'>" . $post['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_title'] . "</a></h2>";
			}

			$contentHtml.= "<div class='blog_category_text'>";
			if($descLength > 0 && $showDesc){
				$contentHtml.= substr(strip_tags($post['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_description_text'],''),0,$descLength).((strlen(strip_tags($post['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_description_text'],'')) > $descLength)?'... ':'');
			}
			$contentHtml.= "</div>";
			if($showReadMore){
				$contentHtml.= "<div class='readMoreCategoryDiv'><a style='color:red;font-weight:bold;' class='blogCategoryReadMore' href='" . itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . "'>". 'read more >>' . "</a></div>"; //sysLanguage::get('INFOBOX_BLOGLATESTARTICLES_TEXT_READ_MORE')
			}

			$contentHtml.= '</div>';
			$contentHtml.= '<br style="clear:both"/>';
			$contentHtml.= '</div>';
		}

		if ($app_pg == null){
			$contentHeading = "Blog";
		}else{
			$contentHeading = $blog->getCategoryHeaderTitle($app_pg);
		}

		$contentHtml .= "<br/><br/>";


		$this->setBoxHeading($contentHeading);
		$this->setBoxContent($contentHtml);
		return $this->draw();

	}
}