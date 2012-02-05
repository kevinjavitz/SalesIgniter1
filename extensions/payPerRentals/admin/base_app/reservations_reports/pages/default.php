<?php
	set_time_limit(0);
	function generateSlug($phrase){
		$result = strtolower($phrase);

		$result = preg_replace("/[^a-z0-9\s-]/", "", $result);
		$result = trim(preg_replace("/[\s-]+/", " ", $result));
		//$result = trim(substr($result, 0, $maxLength));
		$result = preg_replace("/\s/", "-", $result);

		return $result;
	}
	$gRows = false;
	$line = 0;
    $barcodesArr = array();
	$timeBooked = array();
	function getAttr($status, $type, $rID, $barcode_id, $products_id){

		$tooltip = '';

		if ($status != 0){
			$QrentalStatus = Doctrine_Query::create()
			->select('rental_status_text')
			->from('RentalStatus')
			->where('rental_status_id = ?', $status)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if (count($QrentalStatus) > 0){
				$attr = array(
					'isEdit'    => '1',
					'barcode_id' => $barcode_id,
					'products_id' => $products_id,
					'type'  =>  $type,
					'rid'   =>  $rID

				);
			}
            		unset($QrentalStatus);
		}else{
			$attr = array(
					'tooltip' => $tooltip . '<br/>Status: Available' .
								'<br/>Click to add action',
                    			'isEdit'    => '0',
					'barcode_id' => $barcode_id,
					'products_id' => $products_id,
					'type'  =>  $type,
					'rid'   =>  $rID

				);
		}

		if ($type == '0'){
			$attr['tooltip'] = '';
		}
		return $attr;
	}

	function getColor($status){
		$color = '#ffffff';
		if($status != 0){
			$QrentalStatus = Doctrine_Query::create()
							->select('rental_status_color')
							->from('RentalStatus')
							->where('rental_status_id = ?', $status)
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if (count($QrentalStatus) > 0){
			$color = '#' . $QrentalStatus[0]['rental_status_color'];
			}
            		unset($QrentalStatus);
		}				
		return $color;
	}

	function dateInBetween($startDate,$endDate, $reserveStartDate, $reserveEndDate, $day, $dayStart){
		$returned = -1;		
		
		if ($reserveStartDate <= strtotime($startDate) && strtotime($startDate) <= $reserveEndDate){
		   $returned = $day;
		}

		if ($reserveStartDate >= strtotime($startDate) && strtotime($endDate) >= $reserveStartDate){
		   $returned = $dayStart;
		}

		return $returned;
	}

	function fillColors($day, $year, $month, $numberofdays, $dayEnd, $cdayEnd, $nextMonth, $monthEnd, $numCols, $line, $color, $attr, &$rowProducts){
		global $gRows;
		$i = 0;
		$j = $day;
		$k = $dayEnd;
		$tMonth = $month;
		if ($attr['type'] == '0'){
			$cls = '';
		}else{
			$cls = 'rowData';
		}
		while(true){

			if ($j > $numberofdays){
				$j = 1;
				$tMonth = $nextMonth;
			}
			if ($i == $numCols){
				break;
			}

			if(!isset($gRows[$year][$tMonth][$j])){
				break;
			}
        	$rowProducts[$line][$gRows[$year][$tMonth][$j]+1] = array('text' => '&nbsp;',
								'align' => 'right',
		                        'addCls' => $cls,
		                        'attr'  => $attr,
								'css' => array('background'=>'none',
												'background-color'   => $color
								)
			);
			if (($j == $k && $tMonth == $monthEnd) || ($j == $cdayEnd && $tMonth == $nextMonth)){
				break;
			}

			$i++;
			$j++;
		}
	}

	function addGridRow($product, &$tableGrid, $startDate, $endDate, $purchaseType, $numCols, $line, &$numBarcodes, &$rowProducts, &$barcodesArr, &$timeBooked){

		$productId = $product['products_id'];
		$initLine = $line;

		foreach($product['ProductsInventory'] as $piInfo){

			if ($piInfo['type'] == 'reservation' && $piInfo['controller'] == $product['products_inventory_controller']){
				if ($piInfo['track_method'] == 'barcode'){
					foreach($piInfo['ProductsInventoryBarcodes'] as $pibInfo){
						if ($pibInfo['status'] != 'B'){
							$line++;
			                $htmlCheckbox = htmlBase::newElement('checkbox')
			                ->setName('selectedBarcodes[]')
			                ->setValue($pibInfo['barcode_id']);

						    $attributeV = '';
							if (!is_null($pibInfo['attributes'])){							
								$attributeValArr = attributesUtil::splitStringToArray($pibInfo['attributes']);
								foreach($attributeValArr as $k =>$v){
									$productOptions = Doctrine_Query::create()
									->from('ProductsOptionsDescription')
									->where('products_options_id = ?', $k)
									->andWhere('language_id = ?', Session::get('languages_id'))
									->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

									$optionVal = $productOptions[0]['products_options_name'];
									$attributeOptions = Doctrine_Query::create()
									->from('ProductsOptionsValuesDescription')
									->where('products_options_values_id = ?', $v)
									->andWhere('language_id = ?', Session::get('languages_id'))
									->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

									$attributeVal = $attributeOptions[0]['products_options_values_name'];
									$attributeV = '('.$optionVal . ' ->'. $attributeVal.')';
								}
							}

							$rowProducts[$line][0] = array('text' => $htmlCheckbox->draw() . ' '. $pibInfo['barcode']. $attributeV,'addCls' =>'b'.generateSlug($pibInfo['barcode']),'attr' =>array('product_id' =>$productId, 'type'=>'reservation', 'barcode_id'=>$pibInfo['barcode_id'],'barcode_name' =>'b'.generateSlug($pibInfo['barcode'])));
							$barcodesArr[] = '"'.'b'.generateSlug($pibInfo['barcode']).'"';

							foreach($pibInfo['OrdersProductsReservation'] as $oprInfo){
								$reserveStartDate = strtotime('-' . $oprInfo['shipping_days_before'] . ' days' ,strtotime($oprInfo['start_date'])); //+-ship_days
								$reserveEndDate = strtotime('+' . $oprInfo['shipping_days_after'] . ' days' , strtotime($oprInfo['end_date']));
								if ($oprInfo['rental_status_id'] == 0){
									if ($oprInfo['date_returned'] != '0000-00-00 00:00:00'){
										$reservedStatus = '4';
									}else if ($oprInfo['date_shipped'] < date('Y-m-d H:i:s')){
										$reservedStatus = '3';
									}else{
										$reservedStatus = '2';
									}
									$opReservation = Doctrine_Core::getTable('OrdersProductsReservation')->find($oprInfo['orders_products_reservations_id']);
									$opReservation->rental_status_id = $reservedStatus;
									$opReservation->save();
								}

								//here I fill the Events Array
									$timeDateParseStart = date_parse(date('Y-m-d H:i',$reserveStartDate));
									$timeDateParseEnd = date_parse(date('Y-m-d H:i',$reserveEndDate));

									$stringStart = 'new Date('.$timeDateParseStart['year'].','.($timeDateParseStart['month']-1).','.$timeDateParseStart['day'].','.$timeDateParseStart['hour'].','.$timeDateParseStart['minute'].')';
									$stringEnd = 'new Date('.$timeDateParseEnd['year'].','.($timeDateParseEnd['month']-1).','.$timeDateParseEnd['day'].','.$timeDateParseEnd['hour'].','.($timeDateParseEnd['minute']+1).')';

									$timeBooked[] = "{title:'Not Available',start:".$stringStart.",end:".$stringEnd.", className:'".'b'.generateSlug($pibInfo['barcode'])."', rid:'".$oprInfo['orders_products_reservations_id']."', rsid:'".getColor($oprInfo['rental_status_id'])."',type:'".'reservation'."',barcode_id:'".$pibInfo['barcode_id']."',barcode_name:'".'b'.generateSlug($pibInfo['barcode'])."',product_id:'".$productId."', allDay:false}";

							}
						}
					}

				}
			}else if ($piInfo['type'] == 'rental' && $piInfo['controller'] == $product['products_inventory_controller']){
					if ($piInfo['track_method'] == 'barcode'){

						foreach($piInfo['ProductsInventoryBarcodes'] as $pibInfo){
							if ($pibInfo['status'] != 'B'){
								$line++;
				                $htmlCheckbox = htmlBase::newElement('checkbox')
				                ->setName('selectedBarcodes[]')
				                ->setValue($pibInfo['barcode_id']);
								$rowProducts[$line][0] = array('text' => $htmlCheckbox->draw() . ' ' . $pibInfo['barcode'],'addCls' =>'b'.generateSlug($pibInfo['barcode']),'attr' => array('product_id' =>$productId, 'type'=>'rental', 'barcode_id'=>$pibInfo['barcode_id'],'barcode_name'=>'b'.generateSlug($pibInfo['barcode'])));
								$barcodesArr[] = '"'.'b'.generateSlug($pibInfo['barcode']).'"';

								foreach($pibInfo['RentedProducts'] as $rpInfo){

									$reserveStartDate = strtotime($rpInfo['shipment_date']);
									$reserveEndDate = strtotime($rpInfo['return_date']);
									if ($rpInfo['return_date'] == '0000-00-00'){
										$reserveEndDate = strtotime("+1 month", strtotime($startDate));										
									}

									if ($rpInfo['rental_status_id'] == 0){
										if ($rpInfo['return_date'] != '0000-00-00 00:00:00'){
											$reservedStatus = '9';
										}else if ($rpInfo['arrival_date'] < date('Y-m-d H:i:s')){
											$reservedStatus = '8';
										}else if ($rpInfo['shipment_date'] < date('Y-m-d H:i:s')){
											$reservedStatus = '7';
										}else{
											$reservedStatus = '6';											
										}
										$rpRental = Doctrine_Core::getTable('RentedProducts')->find($rpInfo['rented_products_id']);
										$rpRental->rental_status_id = $reservedStatus;
										$rpRental->save();
									}
									$timeDateParseStart = date_parse(date('Y-m-d H:i',$reserveStartDate));
									$timeDateParseEnd = date_parse(date('Y-m-d H:i',$reserveEndDate));

									$stringStart = 'new Date('.$timeDateParseStart['year'].','.($timeDateParseStart['month']-1).','.$timeDateParseStart['day'].','.$timeDateParseStart['hour'].','.$timeDateParseStart['minute'].')';
									$stringEnd = 'new Date('.$timeDateParseEnd['year'].','.($timeDateParseEnd['month']-1).','.$timeDateParseEnd['day'].','.$timeDateParseEnd['hour'].','.($timeDateParseEnd['minute']+1).')';

									$timeBooked[] = "{title:'Not Available',start:".$stringStart.",end:".$stringEnd.", className:'".'b'.generateSlug($pibInfo['barcode'])."', rid:'".$rpInfo['rented_products_id']."', rsid:'".getColor($rpInfo['rental_status_id'])."',type:'".'rental'."',barcode_id:'".$pibInfo['barcode_id']."',barcode_name:'".'b'.generateSlug($pibInfo['barcode'])."',product_id:'".$productId."', allDay:false}";
								}
							}
						}
					}
			}
        }
		$numBarcodes = $line - $initLine;
	}


	$rows = 0;
	$products_count = 0;

	$lID = (int)Session::get('languages_id');

	if (isset($_GET['purchase_type_field']) && !empty($_GET['purchase_type_field'])){
		$purchaseType = $_GET['purchase_type_field'];
	}else{
		$purchaseType = 'both';
	}

	$Qproducts = Doctrine_Query::create()
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')	
	->leftJoin('p.ProductsInventory pi')
	->leftJoin('pi.ProductsInventoryBarcodes pib')
	->leftJoin('pib.OrdersProductsReservation opr')
	->leftJoin('pib.RentedProducts rp')
	->where('pd.language_id = ?', $lID)
	->andWhere('pib.barcode_id is not null');

	if ($purchaseType == 'both'){
		$Qproducts->andWhere('pi.type = "rental" or pi.type = "reservation"');
	}else{
		$Qproducts->andWhere('pi.type = ?', $purchaseType);		
	}
	$Qproducts->orderBy(' pd.products_name asc, p.products_id desc');

	if (isset($_GET['productsID']) && !empty($_GET['productsID'])){
		$Qproducts->andWhere('p.products_id = ?', $_GET['productsID']);		
	}

	EventManager::notify('ProductInventoryReportsListingQueryBeforeExecute', &$Qproducts);

	$tableGrid = htmlBase::newElement('grid');
	if (isset($_GET['page']) && !empty($_GET['page'])){
		$page = $_GET['page'];
	}else{
		$page = 1;
	}
	if (isset($_GET['limit']) && !empty($_GET['limit'])){
		$limit = $_GET['limit'];	
	}else{
		$limit = 10;
	}

	$listingPager = new Doctrine_Pager($Qproducts, $page, $limit);
	$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}');

	$pagerRange = new Doctrine_Pager_Range_Sliding(array(
	'chunk' => 5
	));

	$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
	$pagerLayout->setMyType('products');
	$pagerLayout->setTemplate('<a href="{%url}" style="margin-left:5px;background-color:#ffffff;padding:3px;">{%page}</a>');
	$pagerLayout->setSelectedTemplate('<span style="margin-left:5px;">{%page}</span>');

	$pager = $pagerLayout->getPager();

	$products = $pager->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$pagerBar = $pagerLayout->display(array(), true);

	if (isset($_GET['num_cols']) && !empty($_GET['num_cols'])){
		$numCols = $_GET['num_cols'];
	}else{
		$numCols = '31';
	}

	if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
		$startDate = $_GET['start_date'];
		$startDateAsTime = strtotime($_GET['start_date']);
	}else{
		$startDate = date('Y-m-d');
		$startDateAsTime = strtotime($startDate);
	}
    	$endDate = date('Y-m-d', strtotime('+' . ($numCols-1) . ' days', $startDateAsTime));

	if ($numCols > 31){
		$numColsFinal = $numCols;
		$endDateFinal = date('Y-m-d', strtotime('+' . ($numColsFinal-1) . ' days', $startDateAsTime));
	}else{
		$numColsFinal = $numCols;
		$endDateFinal = $endDate;
	}

	$endMonth = date('M', strtotime($endDateFinal));
	$endYear = date('Y', strtotime($endDateFinal));

   	$headArr[] =  array('text' => 'Product'
	);

    $daysH[] = array('text' => '<span style="float:left;height:27px;">Select</span> <span style="float:right">&nbsp;</span><span style="clear:both"></span>');

	$tableGrid->addHeaderRow(array(
		'columns' => $headArr
	));


	$tableGrid->addHeaderRow(array(
		'columns' => $daysH
	));

	$line = 0;
	if (count($products) > 0){
		foreach($products as $product){

			$rowProducts[$line][] = array('addCls' => 'noHover',
						'text' =>'<b>' . $product['ProductsDescription'][0]['products_name'] . '</b>'
			);
			$numBarcodes = 0;
			addGridRow($product,
							$tableGrid,
							$startDate,
							$endDate,
							$purchaseType,
							$numCols
							,$line,
							$numBarcodes,
							$rowProducts,
							$barcodesArr,
							$timeBooked

			);
			
			 $rowAttr = array();
			 foreach($rowProducts as $tRows){
				$tableGrid->addBodyRow(array(
					'rowAttr' => $rowAttr,
					'columns' => $tRows
				));
			 }

			 unset($rowProducts);
			//end for
			$line += $numBarcodes;
			$line++;
		}
	}

?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
   <td class="smallText" align="right" colspan="2"><?php

    $searchForm = htmlBase::newElement('form')
    ->attr('name', 'search')
    ->attr('id', 'searchFormReports')
    ->attr('action', itw_app_link('appExt=payPerRentals','reservations_reports', 'default', 'SSL'))
    ->attr('method', 'get');

    if (isset($_GET['productsID']) && !empty($_GET['productsID'])){
	    $htmlProductsId = htmlBase::newElement('input')
		->setType('hidden')
	    ->setName('productsID')
		->setValue($_GET['productsID']);
    }

    $startdateField = htmlBase::newElement('input')
	->setName('start_date')
    ->setLabel(sysLanguage::get('HEADING_TITLE_START_DATE'))
	->setLabelPosition('before')
	->setId('start_date');

    if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
		$startdateField->val($_GET['start_date']);
    }

    $numColsField = htmlBase::newElement('selectbox')
	->setName('num_cols')
	->attr('id','numCols')
	->setLabel('Number of Days')
	->setLabelPosition('before');

    $numColsField->addOption('7','7');
    $numColsField->addOption('14','14');
    $numColsField->addOption('21','21');
    $numColsField->addOption('31','31');
    $numColsField->addOption('365','1 year');

    if (isset($_GET['num_cols']) && !empty($_GET['num_cols'])){
		$numColsField->selectOptionByValue($_GET['num_cols']);
    }else{
	    $numColsField->selectOptionByValue('31');
    }
	                                    
	//limit
	if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
		$startDateSearch = $_GET['start_date'];
	}else{
		$startDateSearch = date('Y-m-d');
	}
    $startdateField->setValue($startDateSearch);

    $prevArrow = htmlBase::newElement('a')
	->setId('prevArrow')
	->attr('prevData', date('Y-m-d', strtotime('-' . $numCols . ' days',strtotime($startDateSearch))))
	->html('<b><</b>');
    $nextArrow = htmlBase::newElement('a')
	->setId('nextArrow')
    ->attr('nextData', date('Y-m-d', strtotime('+' . $numCols . ' days',strtotime($startDateSearch))))
	->html('<b>></b>');

	$limitField = htmlBase::newElement('selectbox')
	->setName('limit')
	->attr('id','limitProd')
	->setLabel('Products per Page')
	->setLabelPosition('before');

    $limitField->addOption('10','10');
    $limitField->addOption('20','20');
    $limitField->addOption('40','40');
    $limitField->addOption('100','100');

    if (isset($_GET['limit']) && !empty($_GET['limit'])){
		$limitField->selectOptionByValue($_GET['limit']);		
    }

    $purchaseTypeField = htmlBase::newElement('selectbox')
	->setName('purchase_type_field')
	->attr('id','purchType')
	->setLabel('Purchase Type')
	->setLabelPosition('before');

    $purchaseTypeField->addOption('both','Both');
    $purchaseTypeField->addOption('rental', 'Rental Membership');
    $purchaseTypeField->addOption('reservation','Reservation');
	   
    EventManager::notify('ProductInventoryReportsPurchaseTypeAddFilterOption', &$purchaseTypeField);

    $purchaseTypeField->selectOptionByValue($purchaseType);

    $submitButton = htmlBase::newElement('button')
	->setType('submit')
    ->usePreset('save')
    ->setText('Search');

	$changeButton = htmlBase::newElement('button')
	->attr('id','changeView')
    ->setText('Change View');

    if (isset($htmlProductsId)){
	    $searchForm->append($htmlProductsId);
    }
    $searchForm
    ->append($limitField)
    ->append($prevArrow)
	->append($startdateField)
    ->append($nextArrow)
	->append($numColsField)
	->append($purchaseTypeField)
	->append($changeButton);

    EventManager::notify('ProductsInventoryReportsDefaultAddFilterOptions', &$searchForm);

    echo $searchForm->draw();

   	   /*creating add edit reservation window*/
    $editWindow = htmlBase::newElement('form')
	->attr('action', itw_app_link('appExt=payPerRentals','reservations_reports','default'))
	->attr('method','post')
	->attr('id', 'editWindowForm');

    $editWindowHeader = htmlBase::newElement('div')
	->attr('id','header_edit');

    $editWindowStartDate = htmlBase::newElement('input')
	->setName('start_date_edit')
    ->setLabel('Start Date: ')
	->setLabelPosition('before')
	->setId('start_date_edit');

	$editWindowEndDate = htmlBase::newElement('input')
	->setName('end_date_edit')
    ->setLabel('End Date: ')
	->setLabelPosition('before')
	->setId('end_date_edit');

    $editWindowDateAdded = htmlBase::newElement('input')
	->setName('date_added_edit')
    ->setLabel('Queue added: ')
	->setLabelPosition('before')
	->setId('date_added_edit');

	$editWindowReturnDate = htmlBase::newElement('input')
	->setName('return_date_edit')
    ->setLabel('Date returned: ')
	->setLabelPosition('before')
	->setId('return_date_edit');

	$editWindowRentalStatus = htmlBase::newElement('selectbox')
	->setName('rental_status_edit')
	->setLabel('Rental Status: ')
	->setId('rental_status_edit')
	->setLabelPosition('before');

	$QRentalStatus = Doctrine_Query::create()
	->from('RentalStatus')
	->orderBy('rental_status_text')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QRentalStatus as $rInfo){
		$editWindowRentalStatus->addOption($rInfo['rental_status_id'], $rInfo['rental_status_text']);
	}

	$editWindowCustomers = htmlBase::newElement('selectbox')
	->setName('customers_edit')
	->setLabel('Customers: ')
	->setId('customers_edit')
	->setLabelPosition('before');

	$QCustomers = Doctrine_Query::create()
	->from('Customers')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QCustomers as $cInfo){
		$editWindowCustomers->addOption($cInfo['customers_id'], $cInfo['customers_firstname'] . ' ' . $cInfo['customers_lastname']);
	}

	$br = htmlBase::newElement('br');
	$editWindow->append($editWindowHeader)
	->append($br)
	->append($editWindowStartDate)
  	->append($editWindowDateAdded)
	->append($br)
	->append($editWindowEndDate)
	->append($editWindowReturnDate)
	->append($br)
	->append($editWindowRentalStatus)
	->append($br)
	->append($editWindowCustomers)
	->append($br);

   echo $editWindow->draw();
   $minSize = $numCols*39;
   ?></td>
  </tr>
 </table>

 <div id="mainWindow">
  <div id="secWindow" class="ui-widget ui-widget-content ui-corner-all" style="width:<?php echo ($minSize+330);?>px;margin-right:5px;margin-left:5px;">
   <div style="width:300px;margin:5px;float:left" id="prodTable">
   <?php echo $tableGrid->draw();
	    echo "<br/>";
		echo $pagerBar;   
   ?>
   </div>
	  <div id="calendarTime" style="float:left;width:<?php echo $minSize;?>px;margin-top:5px;"></div>
	  <br style="clear:both;"/>
  </div>
 </div>
<?php
	$minTime = 30;
?>
	<script type="text/javascript">
var barcodesArr = [<?php echo implode(',', $barcodesArr);?>];
var barcodePos = Array();
var $linesArray = Array();
var ratio = 1;
var startSec = 0;
var startCal = 0;
var viewType = 'day';
var startArray =[<?php echo implode(',', $timeBooked);?>];
		$(window).load(function (){
			//$('#calendarTime').height($('#prodTable').height()-50);
			startCal = $('#calendarTime').css('width');
			startSec = $('#secWindow').css('width');
			startHeight = -$('.adminHeader').height()-88;
			$('.ui-grid-row').each(function(){
				if( $(this).attr('class').indexOf('ui-grid-heading-row') >0){

				}else{
					barcode_name = $(this).find('td').attr('barcode_name');

					if(barcode_name !== undefined){
						pos = $(this).offset();
						$lineTop =$('<div></div>');
						$lineTop.css('position','absolute');
						$lineTop.css('top', (pos.top-1)+'px');
						$lineTop.css('width', $('#calendarTime').width()+'px');
						$lineTop.css('height', '1px');
						$lineTop.css('z-index', '100');
						$lineTop.css('border-top', '1px solid #cccccc');
						$lineTop.css('left', (pos.left+300)+'px');
						$lineTop.appendTo($('body'));
						barcodePos[barcode_name] = pos.top;
						$linesArray.push($lineTop);

					}
				}

			});

			$('#calendarTime').fullCalendar({
				header: {
					 left:   '',
					center: '',
					right:  ''
				},
				theme: true,
				allDaySlot:false,
				contentHeight:$('#prodTable').height()-30,
				slotMinutes:<?php echo $minTime;?>,
				weekDayNumber:<?php echo $numCols;?>,
				//axisFormat:'h:mm',
				editable: false,
				disableDragging: true,
				disableResizing: true,
				defaultView: 'basicWeek',
				height: $('#prodTable').height()-30,
				events: startArray,
				dayClick: function(date, allDay, jsEvent, view) {
					var clicky = jsEvent.pageY;
					var clickedBarcode = '';
					for(i=0;i<=barcodesArr.length;i++){
						if(barcodePos[barcodesArr[i]] < clicky && barcodePos[barcodesArr[i]]+19 > clicky){
							clickedBarcode = barcodesArr[i];
						}
					}
					if(clickedBarcode != ''){
						rID = 0;
						type = $('.'+clickedBarcode).attr('type');
						barcode_id = $('.'+clickedBarcode).attr('barcode_id');
						selectedBarcodes = new Array();
						$('input[name="selectedBarcodes[]"]:checked').each(function() {selectedBarcodes.push($(this).val());});
						products_id = $('.'+clickedBarcode).attr('products_id');
						popupWindowEditReservation(type,rID, barcode_id, selectedBarcodes, products_id,500,300);
					}

				},
				eventAfterRender:function( event, element, view ) {
					//if(view)
					$('.fc-event-time').hide();
					element.find('.fc-event-bg').css('background-color',event.rsid);
					element.attr('rid', event.rid);
					element.attr('barcode_id', event.barcode_id);
					element.attr('barcode_name', event.barcode_name);
					element.attr('type', event.type);

					if(viewType == 'day'){
						for(i=0;i<=barcodesArr.length;i++){
							$('.'+barcodesArr[i]).each(function(){
								if($(this).parent().get(0).tagName == 'TR'){
									var pos = $(this).offset();
									$('.'+barcodesArr[i]).css('top', (pos.top+startHeight)+'px');
									$('.'+barcodesArr[i]).css('height', '16px');
								}
							});
						}
						if(element.width()<32){
							element.width(32);
						}
					} else{
						posx = ($('.fc-agenda-body table').height())/(24*(60/<?php echo $minTime;?>));
						posy = 40;

						pos = element.position();
						element.css('top', (barcodePos[element.attr('barcode_name')]+startHeight)+'px');
						element.css('width', (element.height()*posy/posx)+'px');
						element.css('left', (pos.top*posy/posx)+'px');
						element.css('height', '16px');
					}

					$('.fc-event').each(function(){
						type = $(this).attr('type');
                        rID  = $(this).attr('rID');
						$(this).qtip(
							  {
								 content: {
									// Set the text to an image HTML string with the correct src URL to the loading image you want to use
									 text: '<img align="center" src="'+DIR_WS_CATALOG+'ext/jQuery/themes/icons/ajax_loader_xlarge.gif"/>',
									 ajax: {
											 url:$('#editWindowForm').attr('action') + '?action=getResInfo' + '&type=' + type + '&rID=' + rID,  // Use the rel attribute of each element for the url to load
											 type: 'GET', // POST or GET
											 data: {}, // Data to pass along with your request
											 success: function(data, status) {
												this.set('content.text', data.tooltip);

											 }
										  },
								 title: {
											  text: ''
							   }

								 },

								 position: {
									target: 'mouse',
									adjust: {
										x: -10, y: -150,
											 mouse: false
								},

								container: $('#mainWindow')

								 },
								 show: {
									/*event: 'click',*/
									 delay: 0,
									 ready:false,
									solo: true // Only show one tooltip at a time
								 },
								 style: {
									classes: 'ui-tooltip-wiki ui-tooltip-light ui-tooltip-shadow'
								 }
							  });


					});

				}

			});
			$('.fc-event').click(function(){
						rID = $(this).attr('rid');
						type = $(this).attr('type');
						barcode_id = $(this).attr('barcode_id');
						selectedBarcodes = new Array();
						$('input[name="selectedBarcodes[]"]:checked').each(function() {selectedBarcodes.push($(this).val());});
						products_id = $(this).attr('products_id');
						popupWindowEditReservation(type,rID, barcode_id, selectedBarcodes, products_id,500,300);
			});
			$('.headerDay').css('cursor','pointer');
			$('.fc-content table td').css('cursor','pointer');
			$('.headerDay').click(function() {

				curentDay = $(this).html().replace(' ', '');
				curDate = new Date(curentDay);
				if (curDate.getMonth() + 1 < 10) {
					tmonth = '0' + (curDate.getMonth() + 1);
				} else {
					tmonth = (curDate.getMonth() + 1);
				}

				if (curDate.getDate() < 10) {
					tday = '0' + (curDate.getDate());
				} else {
					tday = (curDate.getDate());
				}

				$('#changeView').trigger('click');
				$('#start_date').val(curDate.getFullYear() + '-' + tmonth + '-' + tday);
				$('#start_date').trigger('change');
			});
			$('#changeView').click(function(){
				if(viewType == 'day'){
					viewType = 'hour';
					$('#secWindow').css('width','2250px');
					$('#calendarTime').css('width','1920px');
					for(i7=0;i7<$linesArray.length;i7++){
						$linesArray[i7].css('width','1920px');
					}
					$('#calendarTime').fullCalendar( 'changeView','agendaDay');
					$('.fc-agenda-body table').tableTranspose();
					$('.fc-agenda-body').height($('#prodTable').height()-50+startHeight);
					$('.fc-agenda-body table th:first').css('width','40px');
					$('.fc-agenda-body table tr:nth-child(2)').each(function(){
						$(this).height($('#prodTable').height()-83+startHeight);

					});
					$('.fc-content table td').css('cursor','pointer');
				}else{
					viewType = 'day';
					$('#secWindow').css('width',startSec);
					$('#calendarTime').css('width',startCal);
					for(i7=0;i7<$linesArray.length;i7++){
						$linesArray[i7].css('width',startCal);
					}
					$('.fc-agenda-body table').tableTranspose();
					$('#calendarTime').fullCalendar( 'changeView','basicWeek');
					$('.fc-content table td').css('cursor','pointer');
				}
			});

			$('div.ui-tooltip-wiki').css('min-width', '300px');
		});
	</script>

