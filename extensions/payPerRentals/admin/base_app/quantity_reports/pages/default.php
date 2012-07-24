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
	$qtyTimes = array();

	$rows = 0;
	$products_count = 0;

	$lID = (int)Session::get('languages_id');

	/*if (isset($_GET['purchase_type_field']) && !empty($_GET['purchase_type_field'])){
		$purchaseType = $_GET['purchase_type_field'];
	}else{
		$purchaseType = 'both';
	} */

	$Qproducts = Doctrine_Query::create()
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')	
	->leftJoin('p.ProductsInventory pi')
	//->leftJoin('pi.ProductsInventoryBarcodes pib')
	//->leftJoin('pi.ProductsInventoryQuantity piq')
	//->leftJoin('pib.OrdersProductsReservation opr')
	//->leftJoin('piq.OrdersProductsReservation opr2')
	//->leftJoin('pib.RentedProducts rp')
	->where('pd.language_id = ?', $lID)
	->andWhere('p.products_status = ?','1')
	->andWhere('pi.type = ?', 'reservation');

	$Qproducts->orderBy(' pd.products_name asc, p.products_id desc');

	if (isset($_GET['productsID']) && !empty($_GET['productsID'])){
		$Qproducts->andWhere('p.products_id = ?', $_GET['productsID']);		
	}
	if(isset($_GET['prodName']) && !empty($_GET['prodName'])){
		$Qproducts->andWhere('pd.products_name LIKE ?', $_GET['prodName'].'%');
	}


	/*if(isset($_GET['limitinventory']) && $_GET['limitinventory'] == 2){
		$Qproducts->andWhere('opr.orders_products_reservations_id is not null');
		$Qproducts->andWhere('opr2.orders_products_reservations_id is not null');
	} */

	//EventManager::notify('ProductInventoryReportsListingQueryBeforeExecuteQuantity', &$Qproducts);

	$tableGrid = htmlBase::newElement('newGrid');
	if (isset($_GET['page']) && !empty($_GET['page'])){
		$page = $_GET['page'];
	}else{
		$page = 1;
	}
	if (isset($_GET['limit']) && !empty($_GET['limit'])){
		$limit = $_GET['limit'];	
	}else{
		$limit = 40;
		$_GET['limit'] = 40;
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
			if(count($product['ProductsInventory']) > 0 && (strpos($product['products_type'],'reservation') !== false)){
				$productClass = new product($product['products_id']);
				$purchaseTypeClass = $productClass->getPurchaseType('reservation');

				foreach($purchaseTypeClass->getResArr() as $productId1 => $val1){
					$qtyTimes[$productId1] = $val1;
				}

				$rowProducts[$line][] = array('addCls' => 'b'.generateSlug($product['products_id']),
							'attr' =>array('product_id' =>$product['products_id'], 'type'=>'reservation','barcode_name' =>'b'.generateSlug($product['products_id'])),
							'text' =>'<b>' . (!empty($product['ProductsDescription'][0]['products_name'])?$product['ProductsDescription'][0]['products_name']:$product['products_model']) . '</b><a target="_blank" href="'.itw_app_link('pID='.$product['products_id'],'products','new_product').'"> E </a><a target="_blank" href="'.itw_catalog_app_link('products_id='.$product['products_id'],'product','info').'"> | V</a>'.' | Total Number Of Items:<b> '.$purchaseTypeClass->getMaxQty().'</b>'
				);
				//$line++;
				$barcodesArr[] = '"'.'b'.generateSlug($product['products_id']).'"';
				//$numBarcodes = 0;
				/*addGridRow($product,
								$tableGrid,
								$startDate,
								$endDate,
								$purchaseType,
								$numCols
								,$line,
								$numBarcodes,
								$rowProducts,
								$barcodesArr,
								$timeBooked,
								$qtyTimes

				);*/

				 $rowAttr = array();
				 foreach($rowProducts as $tRows){
					$tableGrid->addBodyRow(array(
						'rowAttr' => $rowAttr,
						'columns' => $tRows
					));
				 }

				 unset($rowProducts);
				//end for
				//$line += 1;
				$line++;
			}
		}
	}

    foreach($qtyTimes as $productId1 => $val1){
		foreach($val1 as $ptype1 => $qtyDate2){
			foreach($qtyDate2 as $Type => $qtyDate1){
				foreach($qtyDate1 as $date1 => $qty1){
					$timeDateParseStart = date_parse($date1);
					if($Type == 'days'){
						$stringStart = 'new Date('.$timeDateParseStart['year'].','.($timeDateParseStart['month']-1).','.$timeDateParseStart['day'].','.$timeDateParseStart['hour'].','.$timeDateParseStart['minute'].')';
						$stringEnd = 'new Date('.$timeDateParseStart['year'].','.($timeDateParseStart['month']-1).','.$timeDateParseStart['day'].','.($timeDateParseStart['hour']+23).','.($timeDateParseStart['minute']+59).')';

						$timeBooked[] = "{title:'".$qty1."',start:".$stringStart.",end:".$stringEnd.", className:'".'b'.generateSlug($productId1)."', rid:'".'1'."', rsid:'".'1'."',type:'".$ptype1."',barcode_name:'".'b'.generateSlug($productId1)."',product_id:'".$productId1."', allDay:false}";

						$stringStart = 'new Date('.$timeDateParseStart['year'].','.($timeDateParseStart['month']-1).','.$timeDateParseStart['day'].','.$timeDateParseStart['hour'].','.$timeDateParseStart['minute'].')';
						$stringEnd = 'new Date('.$timeDateParseStart['year'].','.($timeDateParseStart['month']-1).','.$timeDateParseStart['day'].','.$timeDateParseStart['hour'].','.($timeDateParseStart['minute']+1).')';
						$timeBooked[] = "{title:'".$qty1."',start:".$stringStart.",end:".$stringEnd.", className:'".'b'.generateSlug($productId1)."', rid:'".'1'."', rsid:'".'1'."',type:'".$ptype1."',barcode_name:'".'b'.generateSlug($productId1)."',product_id:'".$productId1."', allDay:false}";
					}else{
						$stringStart = 'new Date('.$timeDateParseStart['year'].','.($timeDateParseStart['month']-1).','.$timeDateParseStart['day'].','.$timeDateParseStart['hour'].','.$timeDateParseStart['minute'].')';
						$stringEnd = 'new Date('.$timeDateParseStart['year'].','.($timeDateParseStart['month']-1).','.$timeDateParseStart['day'].','.$timeDateParseStart['hour'].','.($timeDateParseStart['minute']+1).')';

						$timeBooked[] = "{title:'".$qty1."',start:".$stringStart.",end:".$stringEnd.", className:'".'b'.generateSlug($productId1)."', rid:'".'1'."', rsid:'".'1'."',type:'".$ptype1."',barcode_name:'".'b'.generateSlug($productId1)."',product_id:'".$productId1."', allDay:false}";
					}
				}
			}
		}
	}

?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_REPORTS');?></div>
 <br />
 <table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
   <td class="smallText" align="right" colspan="2"><?php

    $searchForm = htmlBase::newElement('form')
    ->attr('name', 'search')
    ->attr('id', 'searchFormReports')
    ->attr('action', itw_app_link('appExt=payPerRentals','quantity_reports', 'default', 'SSL'))
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

    /*$purchaseTypeField = htmlBase::newElement('selectbox')
	->setName('purchase_type_field')
	->attr('id','purchType')
	->setLabel('Purchase Type')
	->setLabelPosition('before');

    $purchaseTypeField->addOption('both','Both');
    $purchaseTypeField->addOption('reservation','Reservation');
    $purchaseTypeField->addOption('rental', 'Rental Membership');
	   
    EventManager::notify('ProductInventoryReportsPurchaseTypeAddFilterOption', &$purchaseTypeField);

    $purchaseTypeField->selectOptionByValue($purchaseType);

   $limitByInventory = htmlBase::newElement('selectbox')
	   ->setName('limitinventory')
	   ->attr('id','limitInventory')
	   ->setLabel('Limit by Inventory')
	   ->setLabelPosition('before');

   $limitByInventory->addOption('1','Show All');
   $limitByInventory->addOption('2','Show only inventory with reservations');
    if(isset($_GET['limitinventory'])){
	    $limitByInventory->selectOptionByValue($_GET['limitinventory']);
    }
*/
    $submitButton = htmlBase::newElement('button')
	->setType('submit')
    ->usePreset('save')
    ->setText('Search');

	$changeButton = htmlBase::newElement('button')
	->attr('id','changeView')
    ->setText('Change Calendar View');

    if (isset($htmlProductsId)){
	    $searchForm->append($htmlProductsId);
    }

   $htmlProductInput = htmlBase::newElement('input')
   ->setName('prodName')
   ->setLabel('Product: ')
   ->setLabelPosition('before')
   ->addClass('prodName');

   if(isset($_GET['prodName']) && !empty($_GET['prodName'])){
	   $htmlProductInput->setValue($_GET['prodName']);
   }

    //$htmlBr = htmlBase::newElement('br');
	$htmlBarcodeButton = htmlBase::newElement('button')
	->setText('Search')
	->setType('submit')
	->addClass('checkBarcode');

    $searchForm
	->append($htmlProductInput)
	->append($htmlBarcodeButton)
    ->append($limitField)
  //  ->append($limitByInventory)
    ->append($prevArrow)
	->append($startdateField)
    ->append($nextArrow)
	->append($numColsField)
	//->append($purchaseTypeField)

   // ->append($htmlBr)
	->append($changeButton);

    EventManager::notify('ProductsInventoryReportsDefaultAddFilterOptions', &$searchForm);

    echo $searchForm->draw();
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
	  <table id="header-fixed" style="float:left;width:<?php echo $minSize;?>px;table-layout: fixed;border-spacing: 1px;"></table>
	  <table id="header-fixed2" style="float:left;width:1918px;border-spacing: 1px;"></table>
	  <br style="clear:both;"/>
  </div>
 </div>
<?php
	$minTime = 30;
?>
<style type="text/css">
	#header-fixed {
		display:none;
		position:absolute;
		left:316px;
		top:150px;
	}
	#header-fixed2 {
		display:none;
		position:absolute;
		left:316px;
		top:150px;
	}
	.fc-agenda-bg{
		display:none;
	}
	?
</style>
	<script type="text/javascript">
	var tableOffset;
	var $fixedHeader;
	var $header;
	var tableOffset2;
	var $fixedHeader2;
	var $header2;
	var $p = 0;
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
			$('.gridBodyRow').each(function(){
				//if( $(this).children(":first").attr('class').indexOf('noHover') >0){

				//}else{
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
				//}

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

				eventAfterRender:function( event, element, view ) {
					//if(view)
					$('.fc-event-time').hide();
					element.find('.fc-event-bg').css('background-color',event.rsid);
					element.attr('rid', event.rid);
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
						element.css('top', (barcodePos[element.attr('barcode_name')]+startHeight-16)+'px');
						element.css('width', (element.height()*posy/posx)+'px');
						element.css('left', (pos.top*posy/posx)+'px');
						element.css('height', '16px');
					}

				}

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
					//$p++;
					//if($p == 1){
						tableOffset2 = $("#calendarTime .fc-agenda-body table").offset().top;
						$header2 = $("#calendarTime .fc-agenda-body table").find('tr:first').clone();
						$fixedHeader2 = $("#header-fixed2").empty().append($header2);
					//	$p++;
					//}
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
			tableOffset = $("#calendarTime .fc-view-basicWeek table").offset().top;
			$header = $("#calendarTime .fc-view-basicWeek table > thead").clone();
			$fixedHeader = $("#header-fixed").empty().append($header);
		});
		$(window).bind("scroll", function() {
			if(viewType == 'day'){
				var offset = $(this).scrollTop();
				$fixedHeader.css('top', (window.pageYOffset-150) + 'px');
				if (offset >= tableOffset && $fixedHeader.is(":hidden")) {
					$fixedHeader.show();
				}
				else if (offset < tableOffset) {
					$fixedHeader.hide();
				}
			}else{
				var offset2 = $(this).scrollTop();
				$fixedHeader2.css('top', (window.pageYOffset-150) + 'px');
				if (offset2 >= tableOffset2 && $fixedHeader2.is(":hidden")) {
					$fixedHeader2.show();
				}
				else if (offset2 < tableOffset2) {
					$fixedHeader2.hide();
				}
			}
		});
	</script>

