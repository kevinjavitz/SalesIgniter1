<link rel="stylesheet" href="extensions/payPerRentals/catalog/ext_app/infoboxes/javascript/styles.css" type="text/css">
		<script type="text/javascript" src="ext/jQuery/ui/jquery.ui.datepicker.js"></script>
		<script type="text/javascript">
function nobeforeDays(date){

today = new Date();
if(today.getTime() < date.getTime()){
		return [true,''];
	}else{
		return [false,''];
	}


}
	$(document).ready(function (){

		 var dates = $('#dstart, #dend').datepicker({
                    dateFormat: 'yy-mm-dd',
        			defaultDate: "+1w",
        			changeMonth: true,
        			beforeShowDay: nobeforeDays,
        			/*numberOfMonths: 3,*/
        			onSelect: function(selectedDate) {
        				var option = this.id == "dstart" ? "minDate" : "maxDate";
        				var instance = $(this).data("datepicker");
        				var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
        				if(this.id == "dstart")
        				    dates.not(this).datepicker("option", option, date);
        			}});
		$('.cats').click(function(){
			var inp = "<input type='hidden' name='cPath' value='"+$(this).attr('rel')+"'>";
			$('#sd').append(inp);
			$('#sd').submit();
		}
);
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
	});

	</script>




<?php echo $boxContent;?>


 