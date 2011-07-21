<?php
	global $currencies;

	if (is_array($listingData)){
		$listingTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0)->attr('width', '100%');
		foreach($listingData as $row => $rowInfo){
			if (!is_array($rowInfo)) continue;

			$rowColumns = array();
			foreach($rowInfo as $col => $colInfo){
				if (!is_array($colInfo)) continue;

				$rowColumns[$col] = array(
					'text' => $colInfo['text']
				);

				if (isset($colInfo['align'])){
					$rowColumns[$col]['align'] = $colInfo['align'];
				}
				if (isset($colInfo['valign'])){
					$rowColumns[$col]['valign'] = $colInfo['valign'];
				}else{
                    $rowColumns[$col]['valign'] = 'top';
				}

				if (isset($colInfo['addCls'])){
					$rowColumns[$col]['addCls'] = $colInfo['addCls'];
				}
			}

			if ($row == 0){
				$listingTable->addHeaderRow(array(
					'addCls' => (isset($rowInfo['addCls']) ? $rowInfo['addCls'] : false),
					'columns' => $rowColumns
				));
			}else{
				$listingTable->addBodyRow(array(
					'addCls' => (isset($rowInfo['addCls']) ? $rowInfo['addCls'] : false),
					'columns' => $rowColumns
				));
			}
		}
?>
<div class="ui-widget ui-widget-content ui-corner-all-big productListingRowContainer">
<?php
	if (isset($sorter)){
		if (sysConfig::get('PRODUCT_LISTING_SHOW_PRODUCT_NAME_FILTER') == 'True'){
			$selectedCss = array(
				'font-weight' => 'bold'
			);
			$allLink = htmlBase::newElement('a')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'starts_with'))))
			->html(sysLanguage::get('PRODUCT_LISTING_ALL'));
			if (!isset($_GET['starts_with']) || $_GET['starts_with'] == ''){
				$allLink->css($selectedCss);
			}

			$numLink = htmlBase::newElement('a')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'starts_with')) . 'starts_with=num'))
			->html('0-9');
			if (isset($_GET['starts_with']) && $_GET['starts_with'] == 'num'){
				$numLink->css($selectedCss);
			}

			$letterLinks = array();
			foreach(range('A', 'Z') as $letter){
				$letterLink = htmlBase::newElement('a')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'starts_with')) . 'starts_with=' . $letter))
				->html($letter);
				if (isset($_GET['starts_with']) && $_GET['starts_with'] == $letter){
					$letterLink->css($selectedCss);
				}

				$letterLinks[] = $letterLink->draw();
			}
		}

		if (sysConfig::get('PRODUCT_LISTING_ALLOW_RESULT_LIMIT') == 'True'){
			$getVars = tep_get_all_get_params(array('action', 'limit'));
			parse_str($getVars, $getArr);
			$hiddenFields = '';
			foreach($getArr as $k => $v){
				$hiddenFields .= '<input type="hidden" name="' . $k . '" value="' . $v . '" />';
			}

			$resultsPerPageMenu = htmlBase::newElement('selectbox')
			->setName('limit')
			->attr('onchange', 'this.form.submit()');

			$resultsPerPageMenu->addOption(10, 10);
			$resultsPerPageMenu->addOption(25, 25);
			$resultsPerPageMenu->addOption(50, 50);
			$resultsPerPageMenu->addOption(75, 75);
			$resultsPerPageMenu->addOption(100, 100);

			$resultsPerPageMenu->selectOptionByValue((isset($_GET['limit']) ? $_GET['limit'] : 10));

			$perPageForm = htmlBase::newElement('form')
			->attr('name', 'limit')
			->attr('method', 'get')
			->attr('action', itw_app_link(tep_get_all_get_params(array('action', 'limit'))))
			->html($hiddenFields)
			->append($resultsPerPageMenu);
		}
?>
<div class="productListingRowPager ui-corner-all">
 <table cellpadding="3" cellspacing="0" border="0" width="100%">
  <?php if (sysConfig::get('PRODUCT_LISTING_SHOW_PRODUCT_NAME_FILTER') == 'True'){ ?>
  <tr>
   <td align="center"><?php echo $allLink->draw() . ' | ' . $numLink->draw() . ' | ' . implode(' ', $letterLinks);?></td>
  </tr>
  <?php } ?>
  <tr>
   <td align="right"><table cellpadding="3" cellspacing="0" border="0">
    <tr>
     <td><b><?php echo sysLanguage::get('PRODUCT_LISTING_SORT_BY'); ?>:</b></td>
     <td align="right"><?php echo $sorter;?></td>
     <?php if (sysConfig::get('PRODUCT_LISTING_ALLOW_RESULT_LIMIT') == 'True'){ ?>
     <td><b><?php echo sysLanguage::get('PRODUCT_LISTING_RESULTS_PER_PAGE'); ?>:</b></td>
     <td align="right"><?php echo $perPageForm->draw();?></td>
     <?php } ?>
    </tr>
   </table></td>
  </tr>
 </table>
</div>
<br />
<?php } ?>
 <div class="productListingRowContents"><?php echo $listingTable->draw();?></div>
<?php if (isset($pager) || isset($sorter)){ ?>
<br />
 <div class="productListingRowPager ui-corner-all"><?php
 if (isset($pager)){
 	echo '<div style="margin:.5em;line-height:2em;text-align:right;"><b>'.sysLanguage::get('PRODUCT_LISTING_PAGE').':</b> ' . $pager . '</div>';
 }
 ?></div>
<?php } ?>
</div>
<?php
	}else{
		echo $listingData;
	}
?>