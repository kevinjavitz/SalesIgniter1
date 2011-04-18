$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});

	$('.checkButton').click(function (){
		var getVars = [];
		getVars.push('app=ses_update');
		getVars.push('appPage=default');
		getVars.push('action=getActionWindow');
		getVars.push('window=checkUpdates');
		
		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getVars.join('&')),
			onShow: function (){
				var self = this;
				
				$(self).find('.cancelButton').click(function (){
					$(self).effect('fade', {
						mode: 'hide'
					}, function (){
						$('.gridContainer').effect('fade', {
							mode: 'show'
						}, function (){
							$(self).remove();
						});
					});
				});
				
				var nextUpdate = 0;
				var updates = [];
				
				function continueUpdate(){
					updates[nextUpdate].replaceWith('<span class="ui-icon ui-icon-check"></span>');
					nextUpdate++;
					if (updates[nextUpdate]){
						removeAjaxLoader($('.' + updates[nextUpdate-1].val()));
						processNextUpdate();
					}else{
						if ($('.updatesTable').hasClass('updaterUpdate')){
							var getVars = [];
							getVars.push('app=ses_update');
							getVars.push('appPage=default');
							getVars.push('action=getActionWindow');
							getVars.push('window=checkUpdates');
							getVars.push('fromUpdater=1');

							$.ajax({
								url: js_app_link(getVars.join('&')),
								cache: false,
								dataType: 'html',
								success: function (data){
									removeAjaxLoader($('.' + updates[nextUpdate-1].val()));
									$('.updatesTable').replaceWith(data);
								}
							});
						}else{
							removeAjaxLoader($('.' + updates[nextUpdate-1].val()));
							js_redirect(js_app_link('app=ses_update&appPage=default'));
						}
					}
				}
				
				function processNextUpdate(postVars){
					postVars = postVars || false;
					var getVars = [];
					getVars.push('rType=ajax');
					getVars.push('showErrors=true');
					getVars.push('app=ses_update');
					getVars.push('appPage=default');
					getVars.push('action=processUpdate');
					if ($('.updatesTable').hasClass('updaterUpdate')){
						getVars.push('forUpdater=1');
					}
					
					if (!postVars){
						getVars.push('update_num=' + updates[nextUpdate].val());
						showAjaxLoader($('.' + updates[nextUpdate].val()), 'small');
					}
					
					var ajaxSettings = {
						cache: false,
						url: js_app_link(getVars.join('&')),
						dataType: 'json',
						success: function (data){
							if (data.success){
								continueUpdate();
							}else{
								if (data.errorInfo.resolution == 'none'){
									var $dialog = $('<div></div>');
									if (data.errorInfo.message){
										$dialog.append(data.errorInfo.message);
									}
									$dialog.append($pre);
									
									$dialog.dialog({
										title: 'Patch File Error Report',
										height: 400,
										width: 600,
										modal: true,
										allowClose: false,
										buttons: {
											'Continue': function (){
												continueUpdate();
												$dialog.dialog('close').remove();
											}
										}
									});
								}else if (data.errorInfo.resolution == 'compare'){
									var sm = new difflib.SequenceMatcher(
										data.errorInfo.source,
										data.errorInfo.destination
									);
									
									var $pre = $('<pre></pre>').append(diffview.buildView({
											baseTextLines:data.errorInfo.source,
											newTextLines:data.errorInfo.destination,
											opcodes:sm.get_opcodes(),
											// set the display titles for each resource
											baseTextName:"Current",
											newTextName:"New",
											contextSize:null,
											viewType: 1
									}));
									var $dialog = $('<div></div>');
									if (data.errorInfo.message){
										$dialog.append(data.errorInfo.message);
									}
									$dialog.append($pre);
									
									$dialog.dialog({
										title: 'Visual Compare Of Patch File',
										height: 400,
										width: 600,
										modal: true,
										allowClose: false,
										buttons: {
											/*'Update File': function (){
												var postVars = [];
												postVars.push('action=processUpdate');
												postVars.push('curPatch=' + data.errorInfo.curPatch);
												postVars.push('curDiff=' + data.errorInfo.curDiff);
												postVars.push('curBlock=' + data.errorInfo.curBlock);
												postVars.push('curPackage=' + data.errorInfo.curPackage);
												$dialog.find('ol>li').not('.delete').each(function (){
													postVars.push('lines[]=' + encodeURIComponent($(this).html()));
												});
												
												processNextUpdate(postVars);
												
												$dialog.dialog('close').remove();
											},*/
											'Proceed Without Update': function (){
												var postVars = [];
												postVars.push('action=processUpdate');
												postVars.push('curPatch=' + data.errorInfo.curPatch);
												postVars.push('curDiff=' + data.errorInfo.curDiff);
												postVars.push('curBlock=' + data.errorInfo.curBlock);
												postVars.push('curPackage=' + data.errorInfo.curPackage);

												processNextUpdate(postVars);
												
												$dialog.dialog('close').remove();
											}
										}
									});
									
									$dialog.find('ol').sortable({
										tolerance: 'pointer'
									});
								}else{
									updates[nextUpdate].replaceWith('<span class="ui-icon ui-icon-closethick"></span>');
									alert(data.errorMsg);
								}
							}
						}
					};
					
					if (postVars){
						ajaxSettings.data = postVars.join('&');
						ajaxSettings.type = 'post';
					}
					
					$.ajax(ajaxSettings);
				}
				
				$(self).find('.installButton').click(function (){
					$('input[name="update[]"]:checked').each(function (){
						updates.push($(this));
					});
					if (updates.length > 0){
						processNextUpdate();
					}
				});
			}
		});
	});
});
