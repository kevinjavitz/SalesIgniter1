<?php

	$start = $_POST['start'];
	$gId = $_POST['gid'];

	$Qgroup = Doctrine_Query::create()
				->from('BannerManagerGroups')
				->where('banner_group_id = ?', $gId)
				->limit(1)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$numItems = -1;
	$groupName = $Qgroup[0]['banner_group_name'];
	$spaceBetween = 20;

	if(count($Qgroup) > 0){
		$numItems = floor($Qgroup[0]['banner_group_width'] / ($Qgroup[0]['banner_group_thumbs_width'] + $spaceBetween));
	}

	$Qproducts = Doctrine_Query::create()
				->select('p.products_id, pd.products_name, p.products_image')
				->from('Products p')
				->leftJoin('p.ProductsDescription pd')
				->leftJoin('p.BannerManagerProductsToGroups bng')
				->where('pd.language_id = ?', Session::get('languages_id'))
				->andWhere('bng.banner_group_id = ?', $gId)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	if(count($Qproducts) > 0){
		if(count($Qproducts) <= $start){
			$start = 0;
		}
	}

	$QbannerImages = Doctrine_Query::create()
			->select('p.products_id, pd.products_name, p.products_image')
			->from('Products p')
			->leftJoin('p.ProductsDescription pd')
			->leftJoin('p.BannerManagerProductsToGroups bng')
			->where('pd.language_id = ?', Session::get('languages_id'))
			->andWhere('bng.banner_group_id = ?', $gId)
			->limit($numItems)
			->offset($start)
			->orderBy('rand()')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$start += $numItems;
	$html = '';
	foreach($QbannerImages as $bInfo){
		$Img = htmlBase::newElement('image')
                ->setSource(sysConfig::getDirWsCatalog() . 'images/' . $bInfo['products_image'])
	            ->setWidth($Qgroup[0]['banner_group_thumbs_width'])
				->setHeight($Qgroup[0]['banner_group_thumbs_height'])
				->thumbnailImage(true)
                ->addClass('imgrot' . $groupName);

		$link = itw_app_link('products_id=' . $bInfo['products_id'], 'product', 'info');
		$html .= '<li class="protator'.$groupName.'">' .
		'<a href="' . $link . '">' . $Img->draw() . '</a>' .
		'</li>';
	}
    unset($Qgroup);
	unset($Qproducts);
 	unset($QbannerImages);

	EventManager::attachActionResponse(array(
			'success' => true,
			'start'  => $start,
			'html'     => "<ul>".$html."</ul><br style='clear:both;'/>"
		), 'json');

?>