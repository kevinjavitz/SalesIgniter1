<?php
	global $current_fcategory_id, $fcPath_array, $fcPath;

	$pageTitle = '';
	if (sizeof($fcPath_array)){
		$links = array();
		$newPath = 'fcPath=';
		foreach ($fcPath_array as $key => $value) {
			if ($key == 0){
				$newPath .= $value;
			}else{
				$newPath .= '_' . $value;
			}
			$Qcategory = Doctrine_Query::create()
			->select('categories_name')
			->from('FunwaysCategories')
			->where('categories_id = ?', (int)$value)
			->fetchOne();
			if ($Qcategory){
				$category = $Qcategory->toArray();
			
				$links[] = '<a class="pageHeading" href="'.itw_app_link($newPath, 'funways', 'default') .'">'.strtoupper($category['categories_name']).'</a>';
			}
		}
		$pageTitle = implode('&nbsp;&raquo;&nbsp;', $links);
	}

	$Qcategories = Doctrine_Query::create()
	->from('FunwaysCategories')
	->where('parent_id = ?', (int)$current_fcategory_id)
	->orderBy('sort_order')
	->execute();
	if ($Qcategories->count() > 0){
		ob_start();
?>
<table border="0" cellpadding="5" cellspacing="0" width="100%">
 <tr>
<?php
      $col=0;
      foreach($Qcategories->toArray() as $category){
?>
       <td align="center" class="main"><?php
        if (tep_not_null($category['link_to'])){
            $link = $category['link_to'];
        }else{
            $link = itw_app_link('fcPath=' . $fcPath . '_' . $category['categories_id'], 'funways', 'default');
        }
        echo '<a href="' . $link . '">' .
             tep_image(DIR_WS_IMAGES . $category['categories_image'], $category['categories_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) .
             '</a><br><strong><a href="' . $link . '">' .
             $category['categories_name'] .
             '</a></strong><br>' .
             $category['categories_description'];
       ?></td>
       <td class="main" align="right"></td>
<?php
          $col++;
          if ($col > 3){
              echo '</tr>' . "\n" .
                   '<tr>' . "\n";
              $col=0;
          }
      }
?>
 </tr>
</table>
<br />
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
  }

	$Qfunways = Doctrine_Query::create()
	->select('p.products_id')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.ProductsToBox p2b')
	->leftJoin('p.FunwaysProducts fp')
	->leftJoin('fp.FunwaysProductsToCategories fp2c')
	->where('p.products_status = ?', '1')
	->andWhere('fp2c.categories_id = ?', $current_fcategory_id)
	->andWhere('p2b.products_id is null')
	->andWhere('pd.language_id = ?', (int)Session::get('languages_id'));
	
	$productListing = new productListing_row();
	$productListing->setQuery($Qfunways);
	$pageContents .= $productListing->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
