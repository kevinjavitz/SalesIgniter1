$(document).ready(function (){
	$('#checkAll').click(function(){
		var self = this;
		$(this).parent().parent().parent().find('.appBox').each(function (){
			this.checked = self.checked;
		});
		$(this).parent().parent().parent().find('.pageBox').each(function (){
			this.checked = self.checked;
		});
		$(this).parent().parent().parent().find('.extensionBox').each(function (){
			this.checked = self.checked;
		});

		if (self.checked){
			$('#checkAllText').html('Uncheck All Elements');
		}else{
			$('#checkAllText').html('Check All Elements');
		}
	});
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