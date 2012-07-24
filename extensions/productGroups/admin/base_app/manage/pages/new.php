<?php
	$Group = Doctrine_Core::getTable('ProductsGroups');
	if (isset($_GET['gID'])){
		$Group = $Group->findOneByProductGroupId((int)$_GET['gID']);
	}else{
		$Group = $Group->getRecord();
	}
	function get_category_tree_list($parent_id = '0', $checked = false, $include_itself = true, $prefix = ''){
		$langId = Session::get('languages_id');
		
		$catList = '';

		$QCategories = Doctrine_Query::create()
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('cd.language_id = ?', (int)$langId)
		->andWhere('c.parent_id = ?', (int)$parent_id)
		->orderBy('c.sort_order, cd.categories_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach($QCategories as $categories){

			$catList .= '<optgroup label="' . $prefix. $categories['CategoriesDescription'][0]['categories_name'] . '">';

			$Qproducts = Doctrine_Query::create()
			->from('Products p')
			->leftJoin('p.ProductsDescription pd')
			->leftJoin('p.ProductsToCategories p2c')
			->where('pd.language_id = ?', (int) $langId)
			->andWhere('p2c.categories_id = '. $categories['categories_id'].' OR p2c.categories_id is null')
			->orderBy('pd.products_name')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			foreach($Qproducts as $products){
				$catList .= '<option value="' . $products['products_id'] . '">'.$prefix.'(' . $products['products_model'] . ") " . $products['ProductsDescription'][0]['products_name'] . '</option>';
			}
			
			if (tep_childs_in_category_count($categories['categories_id']) > 0){
				$catList .= get_category_tree_list($categories['categories_id'], $checked, false,$prefix.'&nbsp;&nbsp;&nbsp;');
			}
			$catList .= '</optgroup>';
		}

		return $catList;
	}
       if (isset($_GET['gID'])){
	       $pgroup = explode(',', $Group->products);
	       $name = $Group->product_group_name;
	       //$limit = $Group->product_group_limit;
       }else{
	       $pgroup = array();
	       $name = '';
	       $limit = 0;
       }

		$table = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css('width', '100%');

		$table->addHeaderRow(array(
			'columns' => array(
				array('attr' => array('width' => '40%'), 'text' => 'Products'),
				array('text' => '&nbsp;'),
				array('attr' => array('width' => '30%'), 'text' => 'Related')
			)
		));
		
		$productGroups = '';



		foreach($pgroup as $pID){
			$productGroups .= '<div><a href="#" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' . tep_get_products_name($pID) . '</span>' . tep_draw_hidden_field('product_groups[]', $pID) . '</div>';
        }
		
		$table->addBodyRow(array(
			'columns' => array(
				array(
					'addCls' => 'main',
					'attr' => array(
						'valign' => 'top'
					), 
					'text' => '<select size="30" style="width:100%;" id="productList">' . get_category_tree_list() . '</select>'
				),
				array(
					'addCls' => 'main',
					'text' => '<button type="button" id="moveRight"><span>&nbsp;&nbsp;>>&nbsp;&nbsp;</span></button>'
				),
				array(
					'addCls' => 'main',
					'attr' => array(
						'id' => 'related',
						'valign' => 'top'
					), 
					'text' => $productGroups
				)
			)
		));
?>

	<form name="new_group" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) . 'action=save');?>" method="post" enctype="multipart/form-data">
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_PRODUCT_GROUPS');
	?></div>
<br />
		<table cellpadding="3" cellspacing="0" border="0">
			<tr>
				<td class="main"><?php echo sysLanguage::get('TEXT_PRODUCT_GROUPS_NAME'); ?></td>
				<td class="main"><?php echo tep_draw_input_field('product_group_name', $name); ?></td>
			</tr>
<!--			<tr>
				<td class="main"><?php echo sysLanguage::get('TEXT_PRODUCT_GROUPS_LIMIT'); ?></td>
				<td class="main"><?php echo tep_draw_input_field('product_group_limit', $limit); ?></td>
			</tr>
-->
		</table>
<?php
	echo $table->draw();
?>

<div style="text-align:right"><?php
    $saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

	echo $saveButton->draw() . $cancelButton->draw();
	?></div>
</form>
