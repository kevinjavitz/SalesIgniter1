$(document).ready(function (){
	$('.insertIcon, .editIcon').click(function (){
		var $thisRow = $(this).parent().parent();
		var key = 'parent_id';
		if ($(this).hasClass('editIcon')){
			key = 'cID';
		}
		js_redirect(js_app_link('appExt=photoGallery&app=photo_categories&appPage=new_category&' + key + '=' + $thisRow.attr('data-category_id')));
	});

	$('.deleteIcon').click(function (e){
		e.stopPropagation();
		var $thisRow = $(this).parent().parent();
		var categoryName = jQuery.trim($thisRow.find('.categoryListing-name').html());
		showAjaxLoader($thisRow, 'small');
		$('<div></div>').dialog({
			resizable: false,
			allowClose: false,
			height:180,
			modal: true,
			title: 'Confirm Delete',
			open: function (){
				$(this).html('<b>' + categoryName + '</b><br /><br />Are you sure you want to delete this category and all it\'s subcategories?');
			},
			buttons: {
				Confirm: function() {
					var dialogEl = this;
					$.ajax({
						cache: false,
						url: js_app_link('appExt=photoGallery&app=photo_categories&appPage=default&action=deleteCategoryConfirm'),
						dataType: 'json',
						type: 'POST',
						data: 'categories_id=' + $thisRow.attr('data-category_id'),
						success: function (data){
							removeAjaxLoader($thisRow);
							if (data.success == true){
								$thisRow.remove();
							}else{
								alert('Category Was Not Deleted.');
							}
							$(dialogEl).dialog('close').remove();
						}
					});
				},
				Cancel: function() {
					$(this).dialog('close').remove();
				}
			}
		});
	});
});