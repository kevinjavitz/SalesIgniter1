<?php
	$product = new product((int)$_GET['products_id']);
	if ($product->isValid() === false || $product->isActive() === false){
		$pageTitle = sysLanguage::get('TEXT_PRODUCT_NOT_FOUND');
		$pageContents = '';

		$pageButtons = htmlBase::newElement('button')
		->usePreset('continue')
		->setHref(itw_app_link(null, 'index', 'default'))
		->draw();
	
		$pageContent->set('pageTitle', $pageTitle);
		$pageContent->set('pageContent', $pageContents);
		$pageContent->set('pageButtons', $pageButtons);
	} else {
		$product_id = htmlBase::newElement('input')
		->setName('products_id')
		->setType('hidden')
		->setValue($_GET['products_id']);

		$review_rating = htmlBase::newElement('radio')
	    ->addGroup(array(
			'name'      => 'rating',
			'data'      => array(
				array('label' => sysLanguage::get('TEXT_BAD'), 'value' => '1', 'labelPosition' => 'before'),
				array('label' => '', 'value' => '2', 'labelPosition' => 'after'),
				array('label' => '', 'value' => '3', 'labelPosition' => 'after'),
				array('label' => '', 'value' => '4', 'labelPosition' => 'after'),
				array('label' => sysLanguage::get('TEXT_GOOD'), 'value' => '5', 'labelPosition' => 'after'),
			),
			'separator' => ''
		));
		
		$review_text = htmlBase::newElement('textarea')
		->setName('review')
		->addClass('reviewText')
		->setRows(10)
		//->setCols(20)
		->setLabelPosition('before')
		->setLabel('<br/><br/><b>Review:</b><br/>')
		->addClass('makeFCK')
		->css(array(
			'width' => '90%'
		));
		
		ob_start();
		include($pageTabsFolder . 'tab_image.php');
		$pageTab = ob_get_contents();
		ob_end_clean();
	
		$pageContents = '<input type="hidden" name="products_id" value="' . $_GET['products_id'] . '">' . 
		'<div id="tabs">' . 
			'<ul>' . 
				'<li><a href="#tabImage"><span>' . sysLanguage::get('WRITE_REVIEW') . '</span></a></li>' . 
			'</ul>' . 
			'<div id="tabImage">' . $pageTab . '</div>' . 
		'</div>' . 
		'<div class="ui-widget ui-widget-content ui-corner-all" style="margin-top:.3em;">' . 
			'<div style="margin:.3em;">' . 
				'<p>' . 
					'<b>' . sysLanguage::get('REVIEW_FROM') . '</b>' . 
					$userAccount->getFirstName() . ' ' . $userAccount->getLastName() . 
				'</p>' . 
				'<br>' . 
				$review_rating->draw() . $review_text->draw() . 
			'</div>' . 
		'</div>';

		$pageTitle = sysLanguage::get('WRITE_REVIEW');

		$pageButtons = htmlBase::newElement('button')
		->setType('submit')
		->usePreset('continue')
		->setText(sysLanguage::get('WRITE_REVIEW'))
		->draw();
	
		$pageContent->set('pageForm', array(
			'name' => 'write_review',
			'action' => itw_app_link('appExt=reviews&action=saveReview', 'product_review', 'default'),
			'method' => 'post'
		));
	
		$pageContent->set('pageTitle', $pageTitle);
		$pageContent->set('pageContent', $pageContents);
		$pageContent->set('pageButtons', $pageButtons);
	}
?>