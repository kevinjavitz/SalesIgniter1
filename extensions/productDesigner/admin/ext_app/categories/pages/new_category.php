<?php
/*
	Product Designer Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class productDesigner_admin_categories_new_category extends Extension_productDesigner {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'NewCategoryTabHeader',
			'NewCategoryTabBody'
		), null, $this);
	}
	
	public function NewCategoryTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('TAB_PRODUCT_DESIGNER') . '</span></a></li>';
	}
	
	public function NewCategoryTabBody(&$Category){
		$Qactivities = Doctrine_Query::create()
		->from('ProductDesignerPredesignActivities')
		->orderBy('activity_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qactivities){
			$activityMenu = htmlBase::newElement('selectbox')
			->setName('product_designer_activity_correlation');
			foreach($Qactivities as $aInfo){
				$activityMenu->addOption($aInfo['activity_id'], $aInfo['activity_name']);
			}
		}
		
		$Qcategories = Doctrine_Query::create()
		->from('ProductDesignerPredesignCategories c')
		->leftJoin('c.ProductDesignerPredesignCategoriesDescription cd')
		->where('cd.language_id = ?', Session::get('languages_id'))
		->orderBy('categories_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcategories){
			$categoryMenu = htmlBase::newElement('selectbox')
			->setName('product_designer_category_correlation');
			foreach($Qcategories as $cInfo){
				$categoryMenu->addOption(
					$cInfo['categories_id'],
					$cInfo['ProductDesignerPredesignCategoriesDescription'][0]['categories_name']
				);
			}
		}
		
		$correlationTypes = htmlBase::newElement('radio')
		->addGroup(array(
			'name' => 'product_designer_correlation_type',
			'checked' => (empty($Category['product_designer_correlation_type']) ? 'category' : $Category['product_designer_correlation_type']),
			'data' => array(
				array(
					'label' => 'Activity',
					'labelPosition' => 'after',
					'value' => 'activity'
				),
				array(
					'label' => 'Category',
					'labelPosition' => 'after',
					'value' => 'category'
				)
			)
		));
		
		if ($Category['product_designer_correlation_type'] == 'activity'){
			if (isset($activityMenu)){
				$activityMenu->selectOptionByValue($Category['product_designer_correlation_id']);
			}
		}else{
			if (isset($categoryMenu)){
				$categoryMenu->selectOptionByValue($Category['product_designer_correlation_id']);
			}
		}
		
		$html = '<table>
			<tr>
				<td>' . sysLanguage::get('TEXT_LABEL_CORRELATION_TYPE') . '</td>
				<td>' . $correlationTypes->draw() . '</td>
			</tr>
			<tr>
				<td>' . sysLanguage::get('TEXT_LABEL_CORRELATION_ACTIVITY') . '</td>
				<td>' . (isset($activityMenu) ? $activityMenu->draw() : 'N/A') . '</td>
			</tr>
			<tr>
				<td>' . sysLanguage::get('TEXT_LABEL_CORRELATION_CATEGORY') . '</td>
				<td>' . (isset($categoryMenu) ? $categoryMenu->draw() : 'N/A') . '</td>
			</tr>
		</table>';
		
		return '<div id="tab_' . $this->getExtensionKey() . '">' . 
			$html . 
		'</div>';
	}
}
?>