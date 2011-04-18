var stopProgress = false;
var lastIndex = 0;
var tableDataSet;
var dataSetLength;
function populateReportTable(data){
	if (data){
		tableDataSet = data.files;
		dataSetLength = tableDataSet.length;
		
		$('.rootDir').html(data.root);
		$('#progressMessage').empty();

		$('.fileDiffs').data('root_dir', data.root);
		$('.fileDiffs').data('upgrade_dir', data.upgradeDir);
		
		$('#processProgressBar').progressbar('value', 0);
		$('#processProgressMessage').html('Populating Updates Table: 0/' + dataSetLength + ' Updates');
	}

	for(var i = lastIndex; i < dataSetLength; i++){
		if (tableDataSet[i]){
			var input = '<input type="checkbox" name="files[]"' + (tableDataSet[i].checked ? ' checked="checked"' : '') + '>';
			var button = '';
			if (tableDataSet[i].hasDiff){
				button = '<button class="viewDiffs"><span>View Diffs</span></button>';
			}
		
			$('.fileDiffs tbody')
			.prepend('<tr data-file_path="' + tableDataSet[i].file + '">' + 
				'<td>' + input + '</td>' + 
				'<td>' + tableDataSet[i].file + '</td>' + 
				'<td>' + tableDataSet[i].message + '</td>' + 
				'<td>' + button + '</td>' + 
			'</tr>');
		}
		
		$('#processProgressBar').progressbar('value', Math.round((i / dataSetLength) * 100));
		$('#processProgressMessage').html('Populating Updates Table: ' + i + '/' + dataSetLength + ' Updates');
		
		if (i > (lastIndex + 50)){
			lastIndex = i + 1;
			setTimeout(function (){
				populateReportTable();
			}, 100);
			break;
		}
	}
	
	if (i == dataSetLength){
		$('#processProgressBar').progressbar('value', 100);
		$('#processProgressMessage').html('Populating Updates Table: Complete, Please Review Updates And Uncheck Any Files You Do Not Want Upgraded/Deleted');
	}

	$('.viewDiffs').button();
}

function processAjaxError(XMLHttpRequest, textStatus, errorThrown){
	stopProgress = true;
	alert('There was an error.');
	$('#globalProgressMessage').html(XMLHttpRequest.responseText);
}

$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')){
			return;
		}

		$('.gridButtonBar').find('button').button('enable');
	});

	$('.continueButton').click(function (){
		$('.formTable').hide();
		$('.upgradeInfo').show();
		if ($('.noValidation:checked').size() > 0){
			$('.fileDiffs, .processFiles').hide();
		}

		$('#globalProgressBar').progressbar({value: 0});
		$('#processProgressBar').progressbar({value: 0});

		$.ajax({
			cache: false,
			url: js_app_link('rType=ajax&app=server_manager&appPage=upgrade&action=upgradeProcess&version=' + $('select[name=version]').val() + '&part=download'),
			dataType: 'json',
			error: processAjaxError,
			success: function (data){
				if (data.success){
					var upgradeVersion = data.upgVersion;
					var upgradeDir = data.upgDir;
					$.ajax({
						cache: false,
						url: js_app_link('rType=ajax&app=server_manager&appPage=upgrade&action=upgradeProcess&version=' + upgradeVersion + '&upgDir=' + upgradeDir + '&part=unzip'),
						dataType: 'json',
						error: processAjaxError,
						success: function (data){
							if (data.success){
								$.ajax({
									cache: false,
									url: js_app_link('rType=ajax&app=server_manager&appPage=upgrade&action=upgradeProcess&version=' + upgradeVersion + '&upgDir=' + upgradeDir + '&part=compare'),
									dataType: 'json',
									error: processAjaxError,
									success: function (data){
										if (data.success){
											stopProgress = true;
											if ($('.noValidation:checked').size() > 0){
												$('.processFiles').trigger('click', [data]);
											}else{
												populateReportTable(data);
											}
										}
										else{
											stopProgress = true;
											alert('There was an error during the file compare');
										}
									}
								});
							}
							else{
								stopProgress = true;
								alert('There was an error during the file unzip');
							}
						}
					});
				}
				else{
					stopProgress = true;
					alert('There was an error during the upgrade download');
				}
			}
		});

		setTimeout(function (){
			updateCompareStatus();
		}, 1000);
	});

	$('.processFiles').click(function (e, data){
		var postVars = [];
		postVars.push('version=' + $('select[name=version]').val());
		if ($('.noValidation:checked').size() > 0 && data.files){
			postVars.push('rootPath=' + data.root);
			postVars.push('upgradePath=' + data.upgradeDir);
			for(var i=0; i<data.files.length; i++){
				postVars.push('files[]=' + data.files[i].file);
			}
		}else{
			postVars.push('rootPath=' + $('.fileDiffs').data('root_dir'));
			postVars.push('upgradePath=' + $('.fileDiffs').data('upgrade_dir'));
			$('input[type=checkbox]:checked').each(function (){
				postVars.push('files[]=' + $(this).parent().parent().attr('data-file_path'));
			});
		}

		stopProgress = false;
		updateCompareStatus();
		$.ajax({
			cache: false,
			url: js_app_link('rType=ajax&app=server_manager&appPage=upgrade&action=upgradeProcess&part=upgradeFiles'),
			dataType: 'json',
			type: 'post',
			data: postVars.join('&'),
			error: processAjaxError,
			success: function (data){
				if (data.success){
					$.ajax({
						cache: false,
						url: js_app_link('rType=ajax&app=server_manager&appPage=upgrade&action=upgradeProcess&part=upgradeDatabase'),
						dataType: 'json',
						type: 'post',
						data: 'version=' + $('select[name=version]').val(),
						error: processAjaxError,
						success: function (){
							if (data.success){
								alert('!!!!!!!!!!!!!!' + "\n" + 'UPGRADE COMPLETE' + "\n" + '!!!!!!!!!!!!!!!!');
							}else{
								stopProgress = true;
								alert('There was an error during the file upgrade');
							}
						}
					});
				}else{
					stopProgress = true;
					alert('There was an error during the file upgrade');
				}
			}
		});
	});

	$('.viewDiffs').live('click', function (){
		var filePath = $(this).parent().parent().data('file_path');

		var rootDir = $('.fileDiffs').data('root_dir');
		var upgradeDir = $('.fileDiffs').data('upgrade_dir');

		var left = rootDir + filePath;
		var right = upgradeDir + filePath;
		window.open(js_app_link('app=server_manager&appPage=default&action=viewDiffs&left=' + left + '&right=' + right), 'diffWindow', 'width=600,height=600')
	});
});

var errorCount = 0;
function updateCompareStatus (){
	if (stopProgress){
		$('#globalProgressBar').progressbar('value', 100);
		$('#processProgressBar').progressbar('value', 100);
		return;
	}

	$.ajax({
		cache: false,
		url: js_app_link('app=server_manager&appPage=default&action=getUpgradeProgress&rType=ajax'),
		dataType: 'json',
		type: 'post',
		success: function (data){
			if (stopProgress){
				return;
			}

			if (data.globalPercent == null && data.processPercent == null){
				updateCompareStatus();
			}
			else{
				var globalPercent = parseInt(data.globalPercent);
				var processPercent = parseInt(data.processPercent);

				$('#globalProgressBar').progressbar('value', globalPercent);
				$('#globalProgressMessage').html(data.globalMessage);
				$('#processProgressBar').progressbar('value', processPercent);
				$('#processProgressMessage').html(data.processMessage);

				if (globalPercent < 100){
					updateCompareStatus();
				}
			}
		},
		error: function (){
			errorCount++;
			if (errorCount > 5){
				stopProgress = true;
			}else{
				setTimeout(function (){
					updateCompareStatus();
				}, 1500);
			}
		}
	});
}