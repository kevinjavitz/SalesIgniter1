<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class gallery_admin_products_new_product extends Extension_gallery {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'NewProductTabHeader',
			'NewProductTabBody'
		), null, $this);
	}

	
	public function NewProductTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('TAB_GALLERY') . '</span></a></li>';
	}
	
	public function NewProductTabBody(&$Product){
		$Qcheck = Doctrine_Query::create()
		->select('MAX(products_gallery_id) as nextId')
		->from('ProductGallery')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$TableGallery = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->addClass('ui-widget ui-widget-content galleryTable')
		->css(array(
			'width' => '100%'
		))
		->attr('data-next_id', $Qcheck[0]['nextId'] + 1)
		->attr('language_id', Session::get('languages_id'));

		$TableGallery->addHeaderRow(array(
				'addCls' => 'ui-state-hover galleryTableHeader',
				'columns' => array(
					array('text' => '<div style="float:left;width:280px;">' .sysLanguage::get('TABLE_HEADING_FILE_NAME').'</div>'.
						  '<div style="float:left;width:480px;">'.sysLanguage::get('TABLE_HEADING_COMMENTS').'</div>'.
						  '<div style="float:left;width:40px;">'.htmlBase::newElement('icon')->setType('insert')->addClass('insertIconGallery')->draw().
						  '</div><br style="clear:both"/>'
					)
				)
		));

		$deleteIcon = htmlBase::newElement('icon')->setType('delete')->addClass('deleteIconGallery')->draw();

		$QGallery = Doctrine_Query::create()
		->from('ProductGallery')
		->where('products_id=?', $Product['products_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$galleryList = htmlBase::newElement('list')
		->addClass('galleryList');

		foreach($QGallery as $iGallery){
			$galleryId = $iGallery['products_gallery_id'];

			$galleryImage = htmlBase::newElement('input')
			->addClass('ui-widget-content galleryImage BrowseServerField')
			->setName('gallery[' . $galleryId . '][image]')
			->val($iGallery['file_name']);


			$galleryComments = htmlBase::newElement('textarea')
			->addClass('ui-widget-content makeCommentFCK galleryComments')
			->setName('gallery[' . $galleryId . '][comments]')
			->attr('cols', '20')
			->attr('rows', '10')
			->attr('wrap', 'soft')
			->val($iGallery['comments']);

			$divLi1 = '<div style="float:left;width:280px;">'.$galleryImage->draw().'<br/><img src="imagick_thumb.php?path=rel&width=150&height=150&imgSrc=' . $iGallery['file_name'] . '"/>'.'</div>';
			$divLi2 = '<div style="float:left;width:480px;">'.$galleryComments->draw().'</div>';
			$divLi5 = '<div style="float:left;width:40px;">'.$deleteIcon.'</div>';

			$liObj = new htmlElement('li');
			$liObj->css(array(
				'font-size' => '.8em',
				'list-style' => 'none',
				'line-height' => '1.1em',
				'border-bottom' => '1px solid #cccccc',
				'cursor' => 'crosshair'
			))
			->html($divLi1.$divLi2.$divLi5.'<br style="clear:both;"/>');
			$galleryList->addItemObj($liObj);
		}
		$TableGallery->addBodyRow(array(
				'columns' => array(
					array('align' => 'center',
					      'text' => $galleryList->draw(),
					      'addCls' => 'galleryProducts'
					)
				)
		));

		return '<div id="tab_' . $this->getExtensionKey() . '">' .
			$TableGallery->draw() .
			'<hr />' .

		'</div>';
	}
}
?>