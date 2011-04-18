$(document).ready(function (){
	$('.deleteButton').click(function (){
		var selfButton = this;
		var id = $(selfButton).attr('data-id');
		$('<div></div>').dialog({
			title: 'Confirm Delete',
			resizable: false,
			draggable: false,
			allowClose: false,
			autoOpen: true,
			modal: true,
			open: function (){
				$(this).html('<br />' + 
					'Are you sure you want to delete this manufacturer?' + 
					'<br />' + 
					'<br /><b>' + $(selfButton).attr('data-name') + '</b>' +
					'<br /><br /><input type="checkbox" name="delete_image" value="1" />Delete image?' + 
					($(selfButton).attr('data-products_count') > 0 ? '<br /><input type="checkbox" name="delete_products" value="1" />Delete products?': '') + 
				'<br /><br />');
			},
			buttons: {
				'Confirm': function (){
					$.ajax({
						cache: false,
						url: js_app_link('app=products&appPage=manufacturers&action=deleteManufacturer&mID=' + id),
						data: $(this).find('input').serialize(),
						type: 'post',
						dataType: 'json',
						success: function (){
							$(this).dialog('destroy').remove();
						}
					});
				},
				'Cancel': function (){
					$(this).dialog('destroy').remove();
					js_redirect(js_app_link('app=products&appPage=manufacturers'));
				}
			}
		});
	});
});