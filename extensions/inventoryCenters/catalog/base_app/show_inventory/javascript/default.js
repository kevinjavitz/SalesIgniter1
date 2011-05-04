
 $(window).load(function(){
	 $ellem = $('.invCenter');
	 showAjaxLoader($ellem, 'xlarge');
	 $.ajax({
		 type: "post",
		 url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
		 data: $('#sd').serialize() + "&rType=ajax&isInv=1",
		 success: function(data) {
			 hideAjaxLoader($ellem);
			 $('#sd .invCenter').replaceWith(data.data);
			 $('#sd .rentbbut').button();
			 $('#sd .rentbbut').click(function() {
				 $('#sd').submit();
				 return false;
			 });
		 }
	 });
 });


