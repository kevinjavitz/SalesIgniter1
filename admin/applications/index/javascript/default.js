$(document).ready(function () {
	$('.gridBody > .gridBodyRow').click(function () {
		if ($(this).hasClass('state-active')){
			return;
		}

		$('.gridButtonBar').find('button').button('enable');
		if ($(this).attr('data-has_orders') == 'false'){
			$('.gridButtonBar').find('.ordersButton').button('disable');
		}
	});

	$('.gridButtonBar').find('.ordersButton').click(function () {
		var customerId = $('.gridBodyRow.state-active').attr('data-customer_id');
		js_redirect(js_app_link('app=orders&appPage=default&cID=' + customerId));
	});

	$('.gridButtonBar').find('.emailButton').click(function () {
		var customerEmail = $('.gridBodyRow.state-active').attr('data-customer_email');
		js_redirect(js_href_link('mail.php', 'customer=' + customerEmail));
	});

	$('.gridButtonBar').find('.editButton').click(function () {
		var customerId = $('.gridBodyRow.state-active').attr('data-customer_id');
		js_redirect(js_app_link('app=customers&appPage=edit&cID=' + customerId));
	});
	$('#saveSet').click(function(){
		$('<div id="favoritesSetDialog"></div>').dialog({
			autoOpen: true,
			width: 400,
			height:300,
			close: function (e, ui){
				$(this).dialog('destroy').remove();
			},
			open: function (e, ui){
				$(e.target).html('Set name: <input type="text" name="set_name" id="set_name" />');
			},
			buttons: {
				'Save': function() {
					//ajax call to save comment on success
					dialog = $(this);
					showAjaxLoader($('#favoritesSetDialog'), 'xlarge');
					$.ajax({
						cache: false,
						url: js_app_link('app=index&appPage=default&action=saveSet'),
						data: "set_name=" + $('#set_name').val()+'&selected_fav='+$('#favoritesSetDialog .favSelectbox option:selected').val(),
						type: 'post',
						dataType: 'json',
						success: function (data){
							hideAjaxLoader($('#favoritesSetDialog'));
							dialog.dialog('close');
						}
					});
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		});
	});
	$('.favoritesLinks').sortable({
		update: function(event, ui) {
			myFav = $('.favoritesLinks').html();
			showAjaxLoader($('body'), 'xlarge');
			$.ajax({
				cache: false,
				url: js_app_link('app=index&appPage=default&action=saveFavorites'),
				data: 'favs=' + escape(myFav),
				type: 'post',
				dataType: 'json',
				success: function (data) {
					removeAjaxLoader($('body'));
				}
			});
		}
	});
	$('.gridButtonBar').find('.deleteButton').click(function () {
		var customerId = $('.gridBodyRow.state-active').attr('data-customer_id');

		confirmDialog({
			confirmUrl: js_app_link('app=customers&appPage=default&action=deleteConfirm&cID=' + customerId),
			title: 'Confirm Delete',
			content: 'Are you sure you want to delete this customer?',
			success: function () {
				js_redirect(js_app_link('app=customers&appPage=default&action=confirm&cID=' + customerId));
			}
		});
	});
});
