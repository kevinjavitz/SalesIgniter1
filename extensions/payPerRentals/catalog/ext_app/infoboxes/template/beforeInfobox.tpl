		<link rel="stylesheet" href="extensions/payPerRentals/catalog/ext_app/infoboxes/javascript/styles.css" type="text/css">
		<script type="text/javascript" src="ext/jQuery/ui/jquery.ui.datepicker.js"></script>
		<script type="text/javascript">

<?php
	$datePadding = sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_PADDING');
    if(Session::exists('isppr_nextDay') && Session::get('isppr_nextDay') == '1'){
		$datePadding += 1;
    }
?>
function nobeforeDays(date){

today = new Date();
if(today.getTime() <= date.getTime() - (1000 * 60 * 60 * 24 * <?php echo $datePadding;?> - (24 - date.getHours()) * 1000 * 60 * 60)){
		return [true,''];
	}else{
		return [false,''];
	}


}
	$(document).ready(function (){
     var minRentalDays = <?php
        if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GLOBAL_MIN_RENTAL_DAYS') == 'True'){
            echo (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS');
            $minDays = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS');
        }else{
            $minDays = 0;
            echo '0';
        }
            $butText = sysLanguage::get('TEXT_BUTTON_SUBMIT');
      ?>;
     var butText = '<?php echo $butText;?>';
     $('.rentbbut').text(butText);
    if ($.browser.msie) $('.eventf') 
        .bind('focus mouseover', function() { $(this).addClass('expand').removeClass('clicked'); })
        .bind('click', function() { $(this).toggleClass('clicked'); })
        .bind('mouseout', function() { if (!$(this).hasClass('clicked')) { $(this).removeClass('expand'); }})
        .bind('blur', function() { $(this).removeClass('expand clicked'); });

	$('#categoriesPPRBoxMenu').accordion({
		header: 'h3',
		collapsible: true,
		autoHeight: false,
		active: $('.currentCategory', $('#categoriesPPRBoxMenu')),
		icons: {
			header: 'ui-icon-circle-triangle-s',
			headerSelected: 'ui-icon-circle-triangle-n'
		}
	});

	$('a', $('#categoriesPPRBoxMenu')).each(function (){
		var $link = $(this);
		$($link.parent()).hover(function (){
			$link.css('cursor', 'pointer').addClass('ui-state-hover');

			var linkOffset = $link.parent().offset();
			var boxOffset = $('#categoriesPPRBoxMenu').offset();
			if ($('ul', $(this)).size() > 0){
				var $menuList = $('ul:first', $(this));
				$menuList.css({
					position: 'absolute',
					top: $link.parent().position().top,
					left: $link.parent().position().left + $link.parent().innerWidth() - 5,
					backgroundColor: '#FFFFFF',
					zIndex: 9999
				}).show();
			}
		}, function (){
			$link.css({cursor: 'default'}).removeClass('ui-state-hover');

			if ($('ul', this).size() > 0){
				$('ul:first', this).hide();
			}
		}).click(function (){			
			document.location = $('a:first', this).attr('href');
		});
	});

/*Tranform all the ids in classes*/

			$('.cats').click(function(){
				var inp = "<input type='hidden' name='cPath' value='"+$(this).attr('rel')+"'>";
				$(this).parents('form[name$="selectPPR"]').append(inp);
				$(this).parents('form[name$="selectPPR"]').submit();
				//$('#sd').append(inp);
				//$('#sd').submit();
				return false;
		    });
        	 

		$('button[name="no_dates_selected"]').each(function(){$(this).click(function(){alert('<?php echo sysLanguage::get('EXTENSION_PAY_PER_RENTALS_ERROR_RESERVE');?>');return false;})});
          var selectedDateId = null;
          var startSelectedDate;
        var dates = $('#dstart, #dend').datepicker({
                    dateFormat: '<?php echo getJsDateFormat(); ?>',
        			
        			changeMonth: true,
        			beforeShowDay: nobeforeDays,
        			/*numberOfMonths: 3,*/
        			onSelect: function(selectedDate) {

        				var option = this.id == "dstart" ? "minDate" : "maxDate";
        				var instance = $(this).data("datepicker");
        				var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
						var dateC = new Date('<?php echo Session::get('isppr_curDate');?>');

						if(date.getTime() == dateC.getTime()){
							if(this.id == "dstart"){
	                    	    $('#hstart').html('<?php echo Session::get('isppr_selectOptionscurdays');?>');
							}else{
								$('#hend').html('<?php echo Session::get('isppr_selectOptionscurdaye');?>');
							}
						}else{
							if(this.id == "dstart"){
	                    	    $('#hstart').html('<?php echo Session::get('isppr_selectOptionsnormaldays');?>');
							}else{
								$('#hend').html('<?php echo Session::get('isppr_selectOptionsnormaldaye');?>');
							}
						}


        				if(this.id == "dstart"){
        				    var days = "0";
        				    if ($('select#pickupz option:selected').attr('days')){
        				        days = $('select#pickupz option:selected').attr('days');								      				         						
        				    }
                            //startSelectedDate = new Date(selectedDate);
							dateFut = new Date(date.setDate(date.getDate() + parseInt(days)));
        				    dates.not(this).datepicker("option", option, dateFut);
        				}
        				f = true;
        				if(this.id == "dend"){
                            datest = new Date(selectedDate);
							if ($('#dstart').val() != ''){
								startSelectedDate = new Date($('#dstart').val());
								if (datest.getTime() - startSelectedDate.getTime() < minRentalDays *24*60*60*1000){
									alert('<?php echo sprintf(sysLanguage::get('EXTENSION_PAY_PER_RENTALS_ERROR_MIN_DAYS'), $minDays);?>');
									$(this).val('');
									f = false;
								}
							}else{
								f = false;
							}
        				}

			    	if (selectedDateId != this.id && selectedDateId != null && f){                           
		    			alert('<?php echo sprintf(sysLanguage::get('EXTENSION_PAY_PER_RENTALS_NOW_PLEASE_CLICK'), $butText);?>');
                            		selectedDateId = null;
                      	  	}
                        	if (f){
 					selectedDateId = this.id;
 				}
        					$.ajax({
							 type: "post",
							 url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
							 data: "rType=ajax&dstart="+$('#dstart').val()+"&dend="+$('#dend').val()+"&hstart="+$('#hstart').val()+"&hend="+$('#hend').val()+"&pickup="+$('#pickupz').val()+"&dropoff="+$('#dropoffz').val(),
							 success: function() {
							}});

        			}});

        	/*$('#eventz').change(function(){
						link = js_app_link('appExt=payPerRentals&app=show_event&appPage=default&ev_id='+$(this).val());
						$('.myev1').attr('href',link);
						myel = $(this).parent();
						showAjaxLoader(myel,'xlarge');
                        $.ajax({
							 type: "post",
							 url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
							 data: "rType=ajax&event="+$('#eventz').val(),
							 success: function(data) {

								if(typeof data.data != undefined){									
									$('#shipz').html(data.data);
									$('#shipz').change();					
								}
								hideAjaxLoader(myel);
									
							}});

                });*/

                $('.eventf').change(function(){
						link = js_app_link('appExt=payPerRentals&app=show_event&appPage=default&ev_id='+$(this).val());
						$('.myev1').attr('href',link);
						myel = $(this).parent();
						showAjaxLoader(myel,'xlarge');
						var self = $(this);
                        $.ajax({
							 type: "post",
							 url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
							 data: "rType=ajax&event="+self.val()+'&ship_method='+$('#shipz').val(),
							 success: function(data) {

								if(typeof data.data != undefined){
									self.parent().parent().find('.shipf').html(data.data);
									self.parent().parent().find('.shipf').change();
									if ($.browser.msie) $('.eventf').removeClass('expand clicked');
									if (data.nr == 0){
										alert('<?php echo sysLanguage::get('EXTENSION_PAY_PER_RENTALS_DELIVERY_PASSED');?>');
									}
									//$('#shipz').html(data.data);
									//$('#shipz').change();
								}
								hideAjaxLoader(myel);

							}});

                });

			//$('#eventz').change();

								$('.mysh1').attr('href',"#");
								$('.mysh1').live('click', function(){
									link = js_app_link('appExt=payPerRentals&app=show_shipping&appPage=default&sh_id='+$('#shipz').val());
									popupWindow(link,'400','300');
									return false;
								});

                 $('.shipf').change(function(){
					myel1 = $(this).parent();
					showAjaxLoader(myel1,'xlarge');
                        $.ajax({
							 type: "post",
							 url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
							 data: "rType=ajax&ship_method="+$(this).val()+'&event='+$('#eventz').val(),
							 success: function() {
								hideAjaxLoader(myel1);

							}});
                });

$('#dropoffz, #pickupz, #hstart, #hend').change(function(){
			$.ajax({
							 type: "post",
							 url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
							 data: "rType=ajax&dstart="+$('#dstart').val()+"&dend="+$('#dend').val()+"&hstart="+$('#hstart').val()+"&hend="+$('#hend').val()+"&pickup="+$('#pickupz').val()+"&dropoff="+$('#dropoffz').val(),
							 success: function() {
							}});
});


		$('.myf').change(function(){

			link = js_app_link('appExt=inventoryCenters&app=show_inventory&appPage=default&inv_id='+$(this).val());			
			$('.myf1').attr('href',link);

		});

		$('.myg').change(function(){
			if($(this).val()){
				link = js_app_link('appExt=inventoryCenters&app=show_inventory&appPage=default&inv_id='+$(this).val());
				$('.myg1').attr('href',link);
			}

		});
		$('#ui-datepicker-div').css('z-index','10000');
	});

	</script>

<?php
echo $formD;
?>