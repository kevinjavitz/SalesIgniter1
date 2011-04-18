$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
		$('.gridButtonBar').find('.restoreButton').button('disable');
	});
	
	$('.gridButtonBar').find('.deleteButton').click(function (){
		var fileName = $('.gridBodyRow.state-active').attr('data-file_name');
		
		confirmDialog({
			confirmUrl: js_app_link('app=server_manager&appPage=default&action=deleteConfirm&snap=' + fileName),
			title: 'Confirm Delete',
			content: 'Are you sure you want to delete this snapshot?',
			success: function (){
				js_redirect(js_app_link('app=server_manager&appPage=default'));
			}
		});
	});
	
	$('.gridButtonBar').find('.snapshotButton').click(function (){
		var fileName = $('.gridBodyRow.state-active').attr('data-file_name');

		$('<div class="snapshotWindow" title="Create Snapshot"><div id="progressBar"></div><br><div id="progressMessage">Click begin to start creating the snapshot.</div></div>').dialog({
			width: 600,
			height: 400,
			autoShow: true,
			buttons: {
				'Begin': function (){
					var self = this;
					$('#progressBar').progressbar({
						value: 0
					});
					
					xhRequest = $.ajax({
						cache: false,
						url: js_app_link('rType=ajax&app=server_manager&appPage=default&action=generateSnapshot'),
						dataType: 'json',
						success: function (data){
							if (data.success){
								setTimeout(function (){
									js_redirect(js_app_link('app=server_manager&appPage=default'));
								}, '2000');
							}else{
								alert('There was an error during the file compare');
							}
						}
					});
					
					updateSnapshotStatus();
				}
			}
		});
	});
	
	$('.gridButtonBar').find('.compareButton').click(function (){
		var fileName = $('.gridBodyRow.state-active').attr('data-file_name');

		$('<div class="compareWindow" title="Compare Files"><div id="progressBar"></div><br><div id="progressMessage">This process will take a few minutes and you will be redirected to the report when it completes.</div></div>').dialog({
			width: 600,
			height: 400,
			autoShow: true,
			buttons: {
				'Begin Compare': function (){
					var self = this;
					$('#progressBar').progressbar({
						value: 0
					});
					
					$.ajax({
						cache: false,
						url: js_app_link('rType=ajax&app=server_manager&appPage=default&action=compare&left=current&right=' + fileName),
						dataType: 'json',
						success: function (data){
							if (data.success){
								setTimeout(function (){
									js_redirect(js_app_link('app=server_manager&appPage=compareReport&report_id=' + data.report_id));
								}, '2000');
							}else{
								alert('There was an error during the file compare');
							}
						}
					});
					
					updateCompareStatus();
				}
			}
		});
	});
});

function updateSnapshotStatus(){
	$.ajax({
		cache: false,
		url: js_app_link('app=server_manager&appPage=default&action=snapshotStatus&rType=ajax'),
		dataType: 'json',
		type: 'post',
		success: function (data){
			if (data.percent == null){
				updateSnapshotStatus();
			}else{
				var percent = parseInt(data.percent);
				$('#progressBar').progressbar('value', percent);
				$('#progressMessage').html(data.message);
				if (percent < 100){
					updateSnapshotStatus();
				}
			}
		}
	});
}

function updateCompareStatus(){
	$.ajax({
		cache: false,
		url: js_app_link('app=server_manager&appPage=default&action=compareStatus&rType=ajax'),
		dataType: 'json',
		type: 'post',
		success: function (data){
			if (data.percent == null){
				updateCompareStatus();
			}else{
				var percent = parseInt(data.percent);
				$('#progressBar').progressbar('value', percent);
				$('#progressMessage').html(data.message);
				if (percent < 100){
					updateCompareStatus();
				}
			}
		}
	});
}