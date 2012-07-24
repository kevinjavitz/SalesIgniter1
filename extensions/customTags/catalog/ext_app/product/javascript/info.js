$(document).ready(function(){
	$('.deleteBut').css('cursor','pointer');
	$('.editBut').css('cursor','pointer');

	$('.deleteBut').click(function(){
		var self = $(this);
		 $.ajax({
			 cache: false,
			 url: js_app_link('app=product&appPage=info&action=deleteTag&products_id=' + $(this).attr('prodid')),
			 data: 'tag_id='+$(this).attr('tagid'),
			 type: 'post',
			 dataType: 'json',
			 success: function (saveData){
				self.prev().remove();
				self.next().remove();
				self.remove();
			 }
		 });
	 });

	$('.editBut').click(function(){
		var hRef = $(this).prev().prev();
		var hInput = $('<input>').attr('name','tagName').attr('value',$(this).prev().prev().html());
		var tagId = $(this).attr('tagid');
		var prodId = $(this).attr('prodid');
		var vBut = $('<a>Save</a>').attr('type','submit').attr('class','saveTag');
		vBut.button();
		vBut.insertAfter($(this).prev().prev());
		$(this).prev().prev().prev().replaceWith(hInput);
		$('.saveTag').click(function(){
			var vval = $(this).prev().val();
			$.ajax({
				cache: false,
				url: js_app_link('app=product&appPage=info&action=saveTag&products_id=' + prodId),
				data: 'tags_names='+vval+'&tag_id='+tagId,
				type: 'post',
				dataType: 'json',
				success: function (saveData){
					hRef.html(vval);
					hRef.insertAfter(hInput);
					hInput.remove();
					vBut.remove();

				}
			});
		});

	});
});