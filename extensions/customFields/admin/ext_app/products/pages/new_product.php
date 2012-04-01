<?php
/*
	Products Custom Fields Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class customFields_admin_products_new_product extends Extension_customFields {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'NewProductTabHeader',
			'NewProductTabBody',
			'NewProductQueryBeforeExecute'
		), null, $this);
	}
	
	public function NewProductQueryBeforeExecute(&$productQuery){
		$productQuery->addSelect('group_id')
		->leftJoin('p.ProductsCustomFieldsGroupsToProducts g2p');
	}
	
	public function NewProductTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . 'Custom Fields' . '</span></a></li>';
	}
	
	public function NewProductTabBody(&$pInfo){
		$table = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0);

		$selectBox = htmlBase::newElement('selectbox')
		->setName('products_custom_fields_group')
		->setId('products_custom_fields_group')
		->addOption('null', 'Please Select');
		
		/*
		 * @todo: Remove this when the product query is made for Doctine
		 */
		$Qfields = Doctrine_Query::create()
		->select('group_id')
		->from('ProductsCustomFieldsGroupsToProducts')
		->where('product_id = ?', $pInfo['products_id'])
		->execute(array(), Doctrine::HYDRATE_ARRAY);
		
		if ($Qfields){
			$selectBox->selectOptionByValue($Qfields[0]['group_id']);
		}

		$Qtypes = Doctrine_Query::create()
		->select('g.group_id, g.group_name')
		->from('ProductsCustomFieldsGroups g')
		->orderBy('sort_order')
		->orderBy('g.group_name')
		->execute(array(), Doctrine::HYDRATE_ARRAY);
		if ($Qtypes){
			foreach($Qtypes as $tInfo){
				$selectBox->addOption($tInfo['group_id'], $tInfo['group_name']);
			}
		}
		$table->addBodyRow(array(
			'columns' => array(
				array(
					'addCls' => 'main',
					'attr' => array(
						'valign' => 'top',
						'style' => 'color:red'
					), 
					'text' => 'Choose Custom Field Set:'
				),
				array(
					'addCls' => 'main',
					'text' => $selectBox->draw() . '<div id="productsCustomFields"></div>'
				)
			)
		));
		return '<div id="tab_' . $this->getExtensionKey() . '">' . $table->draw() . '</div>';
	}
}
?>