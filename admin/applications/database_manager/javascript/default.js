$(document).ready(function (){
	$('.resButton').click(function (e){
		e.preventDefault();
		
		var self = this;
		showAjaxLoader($(self), 'small');
		$.ajax({
			url: $(this).attr('href'),
			cache: false,
			dataType: 'json',
			success: function (data){
				removeAjaxLoader($(self));
				if (data.success){
					$(self).parent().parent().find('.statusIcon').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					$(self).remove();
				}else{
					$(self).attr('href', data.resUrl);
					alert('Problems still exist, please click the button again');
				}
			}
		});
	});
});