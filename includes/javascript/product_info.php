<?php
  if (isset($_GET['action']) && $_GET['action'] == 'editReservation'){
      $rInfo = false;
      if ($ShoppingCart->inCart($_GET['products_id'], 'reservation')){
      	$cartProduct = $ShoppingCart->getProduct($_GET['products_id'], 'reservation');
          $rInfo = $cartProduct->getInfo('reservationInfo');
      }
  }
?>
<script>
  function popupWindow(url) {
      window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
  }
  
  function changeAction( frm, val ){
      if (frm.action.indexOf('?') != -1){
          frm.action = frm.action +'&action='+val
      }else{
          frm.action = frm.action +'?action='+val
      }
  }
  
  var disabledDates = [<?php
      $QdisabledDates = tep_db_query('select * from blackout_dates');
      $disabledJS = array();
      while($disabledDates = tep_db_fetch_array($QdisabledDates)){
          $dateFrom = explode('-', $disabledDates['date_from']);
          foreach($dateFrom as $index => $number){
              $dateFrom[$index] = (int)$number;
          }
          $dateTo = explode('-', $disabledDates['date_to']);
          foreach($dateTo as $index => $number){
              $dateTo[$index] = (int)$number;
          }
          $disabledJS[] = '[[' . implode(',', $dateFrom) . '], ' . 
                           '[' . implode(',', $dateTo) . '], ' . 
                           '"' . $disabledDates['repeats'] . '"' . 
                          ']';
      }
      echo implode(',', $disabledJS);
  ?>];
  var disabledDays = ['<?php echo implode('\', \'', explode(',', CALENDAR_DISABLED_DAYS));?>'];
  var dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  var bookedDates = [];
    
  var getReservationLink = js_app_link('app=product&appPage=info&action=getReservedDates');
  var productsID = '<?php echo tep_get_prid($_GET['products_id']);?>';
  
  function fixButtons(){
      $('#inCart').hide();
      $('#checkAvail').show();
  }
  
  function reloadStartCal(){
      loadBookedDays(mktime(0,0,0,parseFloat(date('n'))-1,parseFloat(date('d'))+1,date('Y')), function (start){
          $('#DP_startDate').datepicker('refresh');
      });
  }

  function initialCalLoad(){
      loadBookedDays(mktime(0,0,0,parseFloat(date('n'))-1,parseFloat(date('d'))+1,date('Y')), function (start){
          $('#DP_startDate').datepicker({
              minDate: '+1',
              dateFormat: 'yy-mm-dd',
              gotoCurrent: true,
              altField: '#start_date',
              dayNamesMin: dayShortNames,
              beforeShowDay: disableDays,
              onChangeMonthYear: function (year, month, inst){
                  showStartLoader();
                  showEndLoader();
                  fixButtons();
                  
                  loadBookedDays(mktime(0,0,0,month-1,1,year), function (){
                      hideStartLoader();
                      hideEndLoader();
                      $('#DP_startDate').datepicker('refresh');
                  });
              },
              onSelect: function (dateText){
                  showEndLoader();
                  fixButtons();
                  
                  var selectedDate = $.datepicker.parseDate('y-m-d', dateText);
                  loadBookedDays(mktime(0,0,0,selectedDate.getMonth(),1,selectedDate.getFullYear()), function (){
                      setupEndCal(mktime(0,0,0,selectedDate.getMonth(),selectedDate.getDate(),selectedDate.getFullYear()), $('#DP_endDate'));
                      $('#end_ajaxLoader').hide();
                      $('#DP_endDate').show();
                      <?php if (isset($rInfo)){ ?>
                      var dateObj = $.datepicker.parseDate('yy-mm-dd', '<?php echo $rInfo['end_date'];?>');
                      $('#DP_endDate').datepicker('setDate', dateObj);
                      $('.ui-state-active', $('#DP_endDate')).click();
                      <?php } ?>
                  });
              }
          });
          <?php if (isset($rInfo)){ ?>
          var dateObj = $.datepicker.parseDate('yy-mm-dd', '<?php echo $rInfo['start_date'];?>');
          $('#DP_startDate').datepicker('setDate', dateObj);
          $('.ui-state-active', $('#DP_startDate')).click();
          <?php } ?>
          $('input[id="shipping"]:checked').trigger('click');
      });
  }
    
  $(document).ready(function (){
      fixButtons();
      initialCalLoad();
        
      $(':radio[name="shipping"]').each(function (){
          $(this).click(function (){
              var shippingDays = $(this).attr('days');
              $('#start_date, #end_date').val('');
              $('#DP_startDate').datepicker('option', 'minDate', '+' + shippingDays);
              $('#DP_endDate').hide();
          });
      });
        
      $('#resetButton').click(function (){
          $('#DP_endDate').hide();
          this.form.reset();
          fixButtons();
          initialCalLoad();
      });
      
      $('#rental_qty').blur(function (){
          $('#DP_endDate').hide();
          fixButtons();
          reloadStartCal();
      });
                
      $('#checkAvail').click(function (){
          if ($('#start_date').val() == '' || $('#end_date').val() == '' || $('input[id="shipping"]:checked').val() == ''){
              var errorMsg = '';
              if ($('input[id="shipping"]:checked').size() <= 0){
                  errorMsg += "\n" + 'A Shipping Method';
              }
              if ($('#start_date').val() == ''){
                  errorMsg += "\n" + 'A Start Date';
              }
              if ($('#end_date').val() == ''){
                  errorMsg += "\n" + 'An End Date';
              }
              alert('Error: Please Choose ' + errorMsg);
          }else{
              $.ajax({
                  cache: false,
                  dataType: 'json',
                  type: 'post',
                  url: js_app_link('app=product&appPage=info&action=checkRes'),
                  data: $('*', $('.reservationTable')).serialize(),
                  success: function (data){
                      if (data.success == true){
                          $('#priceQuote').html(data.price + ' ' + data.msg);
                          $('#inCart').show();
                          $('#checkAvail').hide();
                      }else if (data.success == 'not_supported'){
                          $('#priceQuote').html(data.price);
                      }else{
                          alert('Reservation Not Available, Please Select Another Date.');
                      }
                  }
              });
          }
      });
      
      $('input[id="shipping"]:checked').trigger('click');
  });
</script>