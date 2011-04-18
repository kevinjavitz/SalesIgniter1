$(document).ready(function (){
	$('.checkAllPages').click(function (){
		var self = this;
		$(self).parent().parent().find('.pageBox').each(function (){
			this.checked = self.checked;
		});
	});
	
	$('.checkAllApps').click(function (){
		var self = this;
		$(self).parent().parent().find('.appBox').each(function (){
			this.checked = self.checked;
		});
		$(self).parent().parent().find('.pageBox').each(function (){
			this.checked = self.checked;
		});
	});
});