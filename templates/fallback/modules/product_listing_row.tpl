<?php
	global $currencies;

	$purchaseTypes = array();
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

				if(isset($colInfo['purchaseType']) && !empty($colInfo['purchaseType'])){
					$purchaseTypes[$colInfo['purchaseType']] = 1;
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
			create_hidden_fields($getArr,&$hiddenFields);

			$resultsPerPageMenu = htmlBase::newElement('selectbox')
			->setName('limit')
			->attr('onchange', 'this.form.submit()');
			$limitsArray = explode(',',sysConfig::get('PRODUCT_LISTING_PRODUCTS_LIMIT_ARRAY'));
			foreach($limitsArray as $resultLimitOption){
				$resultsPerPageMenu->addOption($resultLimitOption, $resultLimitOption);
			}
			/*
			$resultsPerPageMenu->addOption(10, 10);
			$resultsPerPageMenu->addOption(25, 25);
			$resultsPerPageMenu->addOption(50, 50);
			$resultsPerPageMenu->addOption(75, 75);
			$resultsPerPageMenu->addOption(100, 100);
			*/

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

<?php
	 if(sysConfig::get('PRODUCT_LISTING_SELECT_MULTIPLES') == 'true'){
	 ?>
	<div class="productListingRowPager ui-corner-all">
		<table cellpadding="3" cellspacing="0" border="0" width="100%">
			<tr>
				<td><input type="checkbox" value="1" name="selectAllProducts" class="selectAllProductsID"/>Select All</td>
				<?php
				foreach($purchaseTypes as $pType => $pVal){
					$pButton = htmlBase::newElement('button')
					->setText(sysLanguage::get('TEXT_BUTTON_ALL_'.strtoupper($pType)))
					->addClass('buyp')
					->attr('pType', $pType);
					echo '<td>'.$pButton->draw().'</td>';
				}
				?>
			</tr>
		</table>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.selectAllProductsID').change(function(){
					if($(this).is(':checked')){

						$('.selectProductsID').each(function(){
							$(this).attr('checked', true);
						});
					}else{
						$('.selectProductsID').each(function(){
							$(this).attr('checked', false);
						});
					}

				});

				$('.buyp').click(function(){
					var data = { 'selectProduct[]' : [],'pType':$(this).attr('pType'),'selectQty[]' : [],'selectStartDate[]' : [],'selectEndDate[]' : [],'selectDaysBefore[]' : [],'selectDaysAfter[]' : [],'selectPickup[]' : [],'selectDropoff[]' : []};
					$(".selectProductsID:checked").each(function() {
						data['selectProduct[]'].push($(this).val());
						data['selectQty[]'].push($(this).parent().parent().find("input[name=rental_qty]").val());
						data['selectStartDate[]'].push($(this).parent().parent().find("input[name=start_date]").val());
						data['selectEndDate[]'].push($(this).parent().parent().find("input[name=end_date]").val());
						data['selectDaysBefore[]'].push($(this).parent().parent().find("input[name=days_before]").val());
						data['selectDaysAfter[]'].push($(this).parent().parent().find("input[name=days_before]").val());
						data['selectPickup[]'].push($(this).parent().parent().find("input[name=pickup]").val());
						data['selectDropoff[]'].push($(this).parent().parent().find("input[name=dropoff]").val());

					});
					$.ajax({
						cache: false,
						url: js_app_link('action=addMultiple&app=shoppingCart&appPage=default'),
						data: data,
						type: 'post',
						success: function (data){
							js_redirect(js_app_link('app=shoppingCart&appPage=default'));
						}
					});
				});
			});
		</script>
	</div>
	<?php
	 }
	?>

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