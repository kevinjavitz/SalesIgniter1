//------------------------- BOX set begin block -----------------------------//
function show_box_panel(){
	if (document.getElementById('products_in_box').selectedIndex == 1){
		document.getElementById('box_panel').style.display='inline';
	}else{
		document.getElementById('box_panel').style.display='none';
	}
}
//------------------------- BOX set end block -----------------------------//

function doRound(x, places) {
	return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
}

function getTaxRate() {
	var selected_value = document.forms["new_product"].products_tax_class_id.selectedIndex;
	var parameterVal = document.forms["new_product"].products_tax_class_id[selected_value].value;

	if ( (parameterVal > 0) && (tax_rates[parameterVal] > 0) ) {
		return tax_rates[parameterVal];
	} else {
		return 0;
	}
}

function applyRowOverlay($thisRow, html, callBack){
	$('<div>').attr('id', 'overlay').css({
		position: 'absolute',
		display: 'none',
		top: $thisRow.offset().top,
		left: $thisRow.offset().left,
		width: $thisRow.width(),
		height: $thisRow.height(),
		background: '#000000',
		color: '#FFFFFF',
		textAlign: 'center'
	}).html(html).show().appendTo(document.body).fadeTo('fast', .6, callBack);
}

function removeRowOverlay($thisRow, removeRow){
	$('#overlay').remove();
	if (removeRow == true){
		$thisRow.remove();
	}else{
		$thisRow.fadeTo('fast', 1);
	}
}

function addPackageProduct(){
	var $thisRow = $(this).parent().parent();
	if ($('#packageProductName', $thisRow).val() == ''){
		alert('Must enter a product.');
		return false;
	}

	/*if ($('#packageProductType').val() != 'reservation'){
		alert('Only "Pay Per Rental" products can be added to this package.');
		return false;
	}*/
	var $tabDiv = $thisRow.parent().parent().parent();

	$thisRow.fadeTo('fast', .3, function (){
		applyRowOverlay($thisRow, 'Adding Product To Package, Please Wait', function (){
			var urlVars = $('*', $thisRow).serialize();
			$.ajax({
				cache: false,
				url: js_app_link('app=products&appPage=new_product&action=addPackageProduct&packageParentID=' + productID + '&packageProductID=' + $('#packageProductName').attr('selectedProduct') + '&' + urlVars),
				dataType: 'json',
				success: function (data){
					if (typeof data.errorMsg == 'undefined'){
						var $newRow = $(data.tableRow);
						$newRow.appendTo($('#packageProducts', $tabDiv));

						$('.deletePackageProduct', $newRow).click(deletePackageProduct);
						$('.updatePackageProduct', $newRow).click(updatePackageProduct);
						if ($newRow.prev().hasClass('rowEven')){
							$newRow.addClass('rowOdd');
						}else{
							$newRow.addClass('rowEven');
						}
					}else{
						alert(data.errorMsg);
					}
					removeRowOverlay($thisRow, false);
				}
			});
		});
	});
}

function deletePackageProduct(){
	var $thisRow = $(this).parent().parent();

	$thisRow.fadeTo('fast', .3, function (){
		applyRowOverlay($thisRow, 'Deleting Product From Package, Please Wait', function (){
			$.ajax({
				cache: false,
				url: js_app_link('app=products&appPage=new_product&action=deletePackageProduct'),
				data: $('*', $thisRow).serialize(),
				dataType: 'json',
				success: function (data){
					var removeRow = false;
					if (typeof data.errorMsg == 'undefined'){
						$thisRow.nextAll().each(function (){
							if ($(this).hasClass('rowOdd')){
								$(this).removeClass('rowOdd').addClass('rowEven');
							}else{
								$(this).removeClass('rowEven').addClass('rowOdd');
							}
						});
						removeRow = true;
					}else{
						alert(data.errorMsg);
					}
					removeRowOverlay($thisRow, removeRow);
				}
			});
		});
	});
}

function updatePackageProduct(){
	var $thisRow = $(this).parent().parent();

	$thisRow.fadeTo('fast', .3, function (){
		applyRowOverlay($thisRow, 'Updating Product In Package, Please Wait', function (){
			$.ajax({
				cache: false,
				url: js_app_link('app=products&appPage=new_product&action=updatePackageProduct'),
				data: $('*', $thisRow).serialize(),
				dataType: 'json',
				success: function (data){
					if (typeof data.errorMsg != 'undefined'){
						alert(data.errorMsg);
					}
					removeRowOverlay($thisRow, false);
				}
			});
		});
	});
}

function uploadManagerField($el){
	if ($el.attr('type') == 'file'){
		alert($el.attr('name') + ' cannot be an upload manager field because it is not a text input field.');
		return;
	}
	
	var isMulti = false;
	var autoUpload = true;
	var hasPreviewContainer = false;
	var $debugger = $('#' + $el.attr('id') + '_uploadDebugOutput').addClass('uploadDebugger');

	if ($el.attr('data-is_multi')){
		isMulti = ($el.attr('data-is_multi') == 'true');
	}
	
	if ($el.attr('data-has_preview')){
		hasPreviewContainer = true;
		var $previewContainer = $('#' + $el.attr('id') + '_previewContainer');
	}
		
	if ($el.attr('data-auto_upload')){
		autoUpload = ($el.attr('data-auto_upload') == 'true');
	}
		
	var fileType = $el.attr('data-file_type');
	$el.uploadify({
		uploader: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/uploadify.swf',
		script: 'application.php',
		method: 'GET',
		multi: isMulti,
		scriptData: {
			'app': thisApp,
			'appPage': thisAppPage,
			'action': 'uploadFile',
			'rType': 'ajax',
			'osCAdminID': sessionId,
			'fileType': fileType
		},
		cancelImg: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/images/cancel.png',
		auto: autoUpload,
		onError: function (event, queueID, fileObj, errorObj){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nError Uploading: " + errorObj.type + " :: " + errorObj.info);
			}else{
				alert("Error Uploading: " + errorObj.type + " :: " + errorObj.info);
			}
		},
		onAllComplete: function (){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nAll Uploads Completed!");
			}
		},
		onOpen: function (event, queueID, fileObj){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nBeginning Upload: " + fileObj.name);
			}
		},
		onProgress: function (event, queueID, fileObj, data){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nUpload Speed: " + data.speed + ' KB/ps');
			}
		},
		onComplete: function (event, queueID, fileObj, resp, data){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nUpload Completed\nJson Response: " + resp);
			}

			var theResp = eval('(' + resp + ')');			

			if (theResp.success == true){
				if (isMulti){
					if ($el.val() != ''){
						$el.val($el.val() + ';' + theResp.image_name);
					}else{
						$el.val(theResp.image_name);
					}
				}else{
					$el.val(theResp.image_name);
				}
				
				if (hasPreviewContainer === true){
					var $deleteIcon = $('<a></a>')
					.addClass('ui-icon ui-icon-closethick');
				
					var $zoomIcon = $('<a></a>')
					.addClass('ui-icon ui-icon-zoomin');
					
					var $fancyBox = $('<a></a>')
					.addClass('fancyBox')
					.attr('href', theResp.image_path);
					
					var $img = $('<img></img>')
					.attr('src', theResp.thumb_path)
					.appendTo($fancyBox);
					
					var $thumbHolder = $('<div></div>')
					.css('text-align', 'center')
					.append($fancyBox)
					.append($zoomIcon)
					.append($deleteIcon);
					
					var $theBox = $('<div>').css({
						'float'  : 'left',
						'width'  : '80px',
						'height' : '100px',
						'border' : '1px solid #cccccc',
						'margin' : '.5em'
					}).append($thumbHolder);
				
					if (isMulti){
						$previewContainer.append($theBox);
					}else{
						$previewContainer.html($theBox);
					}
					$('.fancyBox', $theBox).trigger('loadBox');
				}
			}else{
				alert("Error Uploading: " + theResp.errorMsg);
			}
		}
	});
}

function popupWindowComments(url, barcodeId, w, h) {
	$('<div id="commentsWindow"></div>').dialog({
		autoOpen: true,
		width: w,
		height: h,
		close: function (e, ui){			
			$(this).dialog('destroy').remove();
		},
		open: function (e, ui){
			$(e.target).html('<textarea id="commentFCK" rows="30" cols="10" name="commentBarcode"></textarea>');
			showAjaxLoader($('#commentsWindow'), 'xlarge');
			var urlGet = [].concat(url);
			urlGet.push("barcode_id="+barcodeId);
			urlGet.push("action=getBarcodeComment");
			$.ajax({
				cache: false,
				url: js_app_link(urlGet.join("&")),
				dataType: 'json',
				success: function (data){
					hideAjaxLoader($('#commentsWindow'));
					$('#commentFCK').val(data.html);
					var instance = CKEDITOR.instances['commentFCK'];
					if(instance){
						CKEDITOR.remove(instance);
					}
					CKEDITOR.replace('commentFCK');
				}
			});
		},
		buttons: {
				'Save': function() {
					 //ajax call to save comment on success
						dialog = $(this); 
					    showAjaxLoader($('#commentsWindow'), 'xlarge');
						var instance = CKEDITOR.instances['commentFCK'];
						var urlSave = [].concat(url);
						urlSave.push("action=saveBarcodeComment");
						$.ajax({
							cache: false,
							url: js_app_link(urlSave.join("&")),
							data: "barcode_id=" + barcodeId + "&comments=" + instance.getData(),
							type: 'post',
							dataType: 'json',
							success: function (data){
								hideAjaxLoader($('#commentsWindow'));
								dialog.dialog('close');								
							}
						});
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
	});
	return false;
}

var distance = 10;
var time = 250;
var hideDelay = 500;

var hideDelayTimer = null;
var currentTd = null;

// tracker
var beingShown = false;
var shown = false;

var trigger = $(this);
var popup = $('.events ul', this).css('opacity', 0);

$(document).ready(function (){
	$('#turnOnDebugger').each(function (){
		$(this).data('turnedOn', false);
		$(this).click(function (){
			if ($(this).data('turnedOn') === false){
				$('.uploadDebugger').show();
				$(this).val('Turn Off Upload Debugger');
				$(this).data('turnedOn', true);
			}else{
				$('.uploadDebugger').hide();
				$(this).val('Turn On Upload Debugger');
				$(this).data('turnedOn', false);
			}
		});
	});

	$('.makeFCK').each(function (){
		$(this).data('editorInstance', CKEDITOR.replace(this, {
			filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
		}));
	});

	$('#page-2').tabs();
	$('#pricingTabs').tabs();
	$('#inventory_tab_normal_tabs').tabs();
	$('#inventory_tab_attribute_tabs').tabs();
	$('#inventory_tabs').tabs();
	$('#tabs_packages').tabs();
	$('#tab_container').tabs();
	
	makeTabsVertical('#tab_container');
	
	$('.netPricing').keyup(function (){
		var taxRate = getTaxRate();
		var grossValue = $(this).val();

		if (taxRate > 0) {
			grossValue = grossValue * ((taxRate / 100) + 1);
		}
		$('#' + $(this).attr('id') + '_gross').val(doRound(grossValue, 4));
	});

	$('.grossPricing').keyup(function (){
		var taxRate = getTaxRate();
		var netValue = $(this).val();

		if (taxRate > 0) {
			netValue = netValue / ((taxRate / 100) + 1);
		}
		var id = $(this).attr('id');
		$('#' + id.replace('_gross', '')).val(doRound(netValue, 4));
	});

	$('#tax_class_id').change(function (){
		$('.netPricing').trigger('keyup');
	}).trigger('change');

	$('#printLabels').click(function (){
		var $this = $(this);
		var labelsType = $('#labelsType').val();
		var totalLabels = $('.barcode_new:checked, .barcode_used:checked, .barcode_reservation:checked, .barcode_rental:checked');
		if (labelsType == '5164' && totalLabels.size() == 1){
			$('#5164_dialog').dialog({
				modal: true,
				closable: false,
				title: 'Please Select Location',
				buttons: {
					'Generate': function (){
						if ($('input[name="labelPos"]:checked', this).size() <= 0){
							alert('Please Select A Position For The Label');
						}else{
							$('#5164_dialog').dialog('close');
							window.open(js_app_link('app=products&appPage=new_product&action=genLabels&pID=' + productID + '&' + totalLabels.serialize() + '&labelType=' + labelsType + '&loc=' + $('input[name="labelPos"]:checked', this).val()));
						}
					},
					'Cancel': function (){
						$('#5164_dialog').dialog('close');
					}
				}
			}).show();
		}else{
			window.open(js_app_link('app=products&appPage=new_product&action=genLabels&pID=' + productID + '&' + totalLabels.serialize() + '&labelType=' + labelsType));
		}
	});

	var productsArr = [];
	$('option', $('#productDropMenu')).each(function (){
		var productTypes = $(this).attr('productTypes');
		var obj = {
			id: $(this).val(),
			text: $(this).html(),
			types: productTypes
		};
		productsArr.push(obj);
	});

	$('#packageProductName').autocomplete(productsArr, {
		formatItem: function(row, i, max) {
			return row.text;
		},
		formatMatch: function(row, i, max) {
			return row.text;
		},
		formatResult: function(row) {
			return row.text;
		}
	});
	$('#packageProductName').result(function (event, data, formatted){
		$('option', $('#packageProductType')).remove();
		var productTypes = data.types.split(';');
		$(productTypes).each(function (){
			var tInfo = this.split(',');
			$('#packageProductType').append('<option value="' + tInfo[0] + '">' + tInfo[1] + '</option>');
		});
		$('#packageProductName').attr('selectedProduct', data.id);
	});

	$('.addBarcode').live('click', function (){
		var $thisRow = $(this).parent().parent();
		if ($('.barcodeNumber', $thisRow).val() == '' && $('.autogen:checked', $thisRow).size() == 0){
			alert('Must enter a barcode number.');
			return false;
		}else if ($('.autogen:checked', $thisRow).size() == 1 && $('.autogenTotal', $thisRow).val() == ''){
			alert('Must enter an amount of barcode to auto generate.');
			return false;
		}
		var $tabDiv = $thisRow.parent().parent().parent();

		var linkParams = [];
		linkParams.push('app=products');
		linkParams.push('appPage=new_product');
		linkParams.push('action=addBarcode');
		linkParams.push('pID=' + productID);
		
		if ($(this).attr('data-purchase_type')){
			linkParams.push('purchaseType=' + $(this).attr('data-purchase_type'));
		}
		
		if ($(this).attr('data-attribute_string')){
			linkParams.push('aID_string=' + $(this).attr('data-attribute_string'));
		}

        if ($thisRow.find('.acquisitionCost')){
            linkParams.push('acquisitionCost=' + $thisRow.find('.acquisitionCost').val());
        }

        var suppliersId = $thisRow.find('.suppliersId option:selected');

        if (suppliersId){
            linkParams.push('suppliersId=' + suppliersId.val());
        }

		$thisRow.fadeTo('fast', .3, function (){
			applyRowOverlay($thisRow, 'Adding Barcode, Please Wait', function (){
				$.ajax({
					cache: false,
					url: js_app_link(linkParams.join('&')),
					dataType: 'json',
					type: 'post',
					data: $thisRow.parent().find('input, select').serialize(),
					success: function (data){
						if (typeof data.errorMsg == 'undefined'){
							var $newRow = $(data.tableRow);
							$newRow.appendTo($thisRow.parent().parent().parent().find('.currentBarcodeTable'));
						}else{
							alert(data.errorMsg);
						}
						removeRowOverlay($thisRow, false);
					}
				});
			});
		});
	});
	
	$('.deleteBarcode').live('click', function (){
		var $thisRow = $(this).parent().parent();
		var barcodeID = $(this).attr('data-barcode_id');

		confirmDialog({
				title: 'Delete Barcode',
				content: 'Are you sure you want to delete this barcode?',
				onConfirm: function (){
					var linkParams = [];
					linkParams.push('app=products');
					linkParams.push('appPage=new_product');
					linkParams.push('action=deleteBarcode');
					linkParams.push('bID=' + barcodeID);
					linkParams.push('pID=' + productID);

					if ($(this).attr('data-purchase_type')){
						linkParams.push('purchaseType=' + $(this).attr('data-purchase_type'));
					}

					if ($(this).attr('data-attribute_string')){
						linkParams.push('aID_string=' + $(this).attr('data-attribute_string'));
					}

					$thisRow.fadeTo('fast', .3, function (){
						applyRowOverlay($thisRow, 'Deleting Barcode, Please Wait', function (){
							$.ajax({
								cache: false,
								url: js_app_link(linkParams.join('&')),
								dataType: 'json',
								success: function (data){
									var removeRow = false;
									if (typeof data.errorMsg == 'undefined'){
										removeRow = true;
									}else{
										alert(data.errorMsg);
									}
									removeRowOverlay($thisRow, removeRow);
								}
							});
						});
					});
					$(this).dialog('close').remove();
				}
			});

	});
	
	$('.updateBarcode').live('click', function (){
		var $thisRow = $(this).parent().parent();

		var linkParams = [];
		linkParams.push('app=products');
		linkParams.push('appPage=new_product');
		linkParams.push('action=updateBarcode');
		linkParams.push('pID=' + productID);
		linkParams.push('barcode_id=' + $(this).attr('data-barcode_id'))
		
		if ($(this).attr('data-purchase_type')){
			linkParams.push('purchaseType=' + $(this).attr('data-purchase_type'));
		}
		
		if ($(this).attr('data-attribute_string')){
			linkParams.push('aID_string=' + $(this).attr('data-attribute_string'));
		}
		
		$thisRow.fadeTo('fast', .3, function (){
			applyRowOverlay($thisRow, 'Updating Barcode, Please Wait', function (){
				$.ajax({
					cache: false,
					url: js_app_link(linkParams.join('&')),
					data: $('*', $thisRow).serialize(),
					dataType: 'json',
					success: function (data){
						if (typeof data.errorMsg != 'undefined'){
							alert(data.errorMsg);
						}
						removeRowOverlay($thisRow, false);
					}
				});
			});
		});
	});

	/*Edit comments popup*/

	$('.commentBarcode').live('click', function (){
		var linkParams = [];
		linkParams.push('app=products');
		linkParams.push('appPage=new_product');

		/*if ($(this).attr('data-purchase_type')){
			linkParams.push('purchaseType=' + $(this).attr('data-purchase_type'));
		}

		if ($(this).attr('data-attribute_string')){
			linkParams.push('aID_string=' + $(this).attr('data-attribute_string'));
		}*/

		popupWindowComments(linkParams, $(this).attr('data-barcode_id'), 800, 500);

		return false;

	});
	
	$('.checkAll').live('click', function (){
		var className = 'barcode_' + $(this).val();
		var allChecked = this.checked;
		$(this).parent().parent().parent().parent().find('.' + className).each(function (){
			this.checked = allChecked;
		});
	});

	$('.addPackageProduct').click(addPackageProduct);
	$('.deletePackageProduct').click(deletePackageProduct);
	$('.updatePackageProduct').click(updatePackageProduct);

	$('.useDatepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$('#productOnOrder').click(function (){
		var $calendar = $('#productOnOrderCal');
		if (this.checked){
			$calendar.show();
		}else{
			$calendar.hide();
		}
	});

	$('.autogen').live('click', function (){
		if (this.checked){
			$('.barcodeNumber', $(this).parent().parent()).attr('disabled', 'disabled').addClass('ui-state-disabled');
			$('.autogenTotal', $(this).parent()).removeAttr('disabled').removeClass('ui-state-disabled');
		}else{
			$('.barcodeNumber', $(this).parent().parent()).removeAttr('disabled').removeClass('ui-state-disabled');
			$('.autogenTotal', $(this).parent()).attr('disabled', 'disabled').addClass('ui-state-disabled');
		}
	});

	$('.ajaxSave').click(function (){
		showAjaxLoader($(document.body), 'xlarge');
		
		$('.makeFCK').each(function (){
			if ($(this).data('editorInstance')){
				var ckEditor = $(this).data('editorInstance');
				
				$(this).val(ckEditor.getData());
			}
		});
		
		$.ajax({
			cache: false,
			type: 'post',
			url: js_app_link('app=products&appPage=new_product&action=saveProduct&rType=ajax' + (productID > 0 ? '&pID=' + productID : '')),
			data: $('form[name="new_product"]').serialize(),
			dataType: 'json',
			success: function (data){
				$('.programDisabled').removeAttr('disabled').removeClass('ui-state-disabled').removeClass('programDisabled');
				productID = data.pID;
				
				var $form = $('form[name=new_product]');
				if ($('input[name=product_id]', $form).size() <= 0){
					$('<input type="hidden"></input>').attr('name', 'product_id').val(productID).appendTo($form);
				}else{
					$('input[name=product_id]', $form).val(productID);
				}
				
				$('#newProductMessage').remove();
				hideAjaxLoader($(document.body));
			}
		});
		return false;
	});
	
	$('.ui-state-disabled').each(function (){
		$('input', this).each(function (){
			if (!$(this).attr('disabled')){
				$(this).attr('disabled', 'disabled').addClass('programDisabled');
			}
		});
		$('.ui-button', this).addClass('ui-state-disabled').addClass('programDisabled');
		$(this).addClass('programDisabled');
	});
	
	$('.ajaxUpload, .ajaxUploadMulti, .uploadManagerInput').each(function (){
		uploadManagerField($(this));
	});
	
	$('.fancyBox').live('loadBox', function (){
		$(this).fancybox({
			speedIn: 500,
			speedOut: 500,
			overlayShow: false,
			type: 'image'
		});
	}).trigger('loadBox');
	
	$('.ui-icon-zoomin').live('click mouseover mouseout', function (event){
		switch(event.type){
			case 'click':
				$(this).parent().find('.fancyBox').click();
				break;
			case 'mouseover':
				this.style.cursor = 'pointer';
				//$(this).addClass('ui-state-hover');
				break;
			case 'mouseout':
				this.style.cursor = 'default';
				//$(this).removeClass('ui-state-hover');
				break;
		}
	});
	
	$('.deleteImage').live('click', function (event){
		var newVal = [];
		var imageInput = $('#' + $(this).parent().attr('data-input_id'));
		var currentVal = imageInput.val();
		var images = currentVal.split(';');
		for(var i=0; i<images.length; i++){
			if (images[i] != $(this).parent().attr('data-image_file_name')){
				newVal.push(images[i]);
			}
		}
		imageInput.val(newVal.join(';'));
		$(this).parent().parent().remove();
	});

	$('.ui-icon-closethick').live('mouseover mouseout', function (event){
		switch(event.type){
			case 'mouseover':
				this.style.cursor = 'pointer';
				//$(this).addClass('ui-state-hover');
				break;
			case 'mouseout':
				this.style.cursor = 'default';
				//$(this).removeClass('ui-state-hover');
				break;
		}
	});

	/*
	var blockCache = [];
	$('.productType').each(function (){
		$(this).click(function (){
			var $blocks = $('*[depends="productType_' + $(this).val() + '"]');
			if (this.checked){
				$blocks.show();
			}else{
				$blocks.hide();
			}
		});

		var $blocks = $('*[depends="productType_' + $(this).val() + '"]');
		if (this.checked){
			$blocks.each(function (){
				$(this).show();
			});
		}else{
			$blocks.each(function (){
				$(this).hide();
			});
		}
	});
	*/
	
	$('.inventoryCalander').datepick({
		dayNamesMin: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
		beforeShow: function (input, inst){
			alert($(inst.dpDiv).find('.datepick-days-cell').size());
			$(inst.dpDiv).find('.datepick-days-cell').each(function (){
				//if (!$(this).hasClass('datepick-other-month')){
					$(this).append('<div style="border:1px solid black;"></div>');
				//}
			});
		}
   	});
   	
   	$('.calFilter').change(function (){
   		var $calDiv = $('.htmlcal', $(this).parent().parent().parent().parent()).parent();
   		var o = {
   			el: $calDiv,
   			month: $('select[name=cal_month]', $calDiv).val(),
   			year: $('select[name=cal_year]', $calDiv).val(),
   			purchaseType: $('.htmlcal', $calDiv).attr('purchase_type')
   		};
   		
   		changeCalDate(o);
   	});
   	
   	$('.htmlcal .htmlcal-curmonthyear .ui-icon-circle-triangle-w').live('click', function (){
   		var $calDiv = $(this).parent().parent().parent().parent().parent().parent();
   		var o = {
   			el: $calDiv,
   			month: (parseInt($('select[name=cal_month]', $calDiv).val()) - 1),
   			year: $('select[name=cal_year]', $calDiv).val(),
   			purchaseType: $('.htmlcal', $calDiv).attr('purchase_type')
   		};
   		
   		changeCalDate(o);
   	});
   	
   	$('.htmlcal .htmlcal-curmonthyear .ui-icon-circle-triangle-e').live('click', function (){
   		var $calDiv = $(this).parent().parent().parent().parent().parent().parent();
   		var o = {
   			el: $calDiv,
   			month: (parseInt($('select[name=cal_month]', $calDiv).val()) + 1),
   			year: $('select[name=cal_year]', $calDiv).val(),
   			purchaseType: $('.htmlcal', $calDiv).attr('purchase_type')
   		};
   		
   		changeCalDate(o);
   	});
   	
   	$('select[name=cal_month], select[name=cal_year]').live('change', function (){
   		var $calDiv = $(this).parent().parent().parent().parent().parent().parent().parent();
   		var o = {
   			el: $calDiv,
   			month: $('select[name=cal_month]', $calDiv).val(),
   			year: $('select[name=cal_year]', $calDiv).val(),
   			purchaseType: $('.htmlcal', $calDiv).attr('purchase_type')
   		};
   		
   		changeCalDate(o);
   	});
   	
   	$('.date_has_popup').live('mouseover mouseout', function (e){
 		popup = $('.events ul', this);
  		if (e.type == 'mouseover'){
			currentTd = this;
			var windowWidth = $(window).width();
			popup.css({
				bottom: 30,
				left: -76
			}).show();
			
			if ((popup.outerWidth() + popup.offset().left) > windowWidth){
				popup.css('left', -(popup.outerWidth() - 76));
			}
 		}else{
			popup.hide();
  		}
   	});
   	
   	$('.htmlcal').each(function (){
   		var $calDiv = $(this).parent();
   		changeCalDate({
   			el: $calDiv,
   			month: $('select[name=cal_month]', $calDiv).val(),
   			year: $('select[name=cal_year]', $calDiv).val(),
   			purchaseType: $(this).attr('purchase_type')
   		});
   	});
   	
   	$('.viewCal').live('click', function (){
   		var $calDiv = $('.htmlcal', $(this).parent().parent().parent().parent().parent().parent()).parent();
   		changeCalDate({
   			el: $calDiv,
   			month: $('select[name=cal_month]', $calDiv).val(),
   			year: $('select[name=cal_year]', $calDiv).val(),
   			purchaseType: $('.htmlcal', $calDiv).attr('purchase_type'),
   			barcodeId: $(this).attr('barcode_id')
   		});
   	});
	$('.trackMethodButton').change(function(){
		if($(this).val() == 'quantity'){
			$('.quantityTable').show();
			$('.quantityTable').next().show();
			$('.barcodeTable').hide();
		}else{
			$('.quantityTable').hide();
			$('.quantityTable').next().hide();
			$('.barcodeTable').show();
		}
	});
	$('.invController').click(function(){
		if($(this).val() == 'attribute'){
			$('.clstattribute').show();
			$('.clstattribute a').click();
			$('.clstnormal').hide();
		}else{
			$('.clstattribute').hide();
			$('.clstnormal').show();
			$('.clstnormal a').click();
		}
	});
	$('.invController:checked').each(function(){
		if($(this).val() == 'attribute'){
			$('.clstattribute').show();
			$('.clstattribute a').click();
			$('.clstnormal').hide();
		}else{
			$('.clstattribute').hide();
			$('.clstnormal').show();
			$('.clstnormal a').click();
		}
	});
	$('.trackMethodButton:checked').each(function(){
		if($(this).val() == 'quantity'){
			$('.quantityTable').show();
			$('.quantityTable').next().show();
			$('.barcodeTable').hide();
		}else{
			$('.quantityTable').hide();
			$('.quantityTable').next().hide();
			$('.barcodeTable').show();
		}
	});

});

function changeCalDate(o){
	showAjaxLoader(o.el, 'xlarge');
  	if ($('.calFilter', o.el.parent().parent()).val() != 'all'){
		var filterString = '&' + $('.calFilter', o.el.parent().parent()).attr('name') + '=' + $('.calFilter', o.el.parent().parent()).val();
   	}
	$.ajax({
		cache: false,
		url: js_app_link('app=products&appPage=new_product&action=loadCalendar&purchase_type=' + o.purchaseType + '&products_id=' + productID + '&month=' + o.month + '&year=' + o.year + (o.barcodeId ? '&barcode_id=' + o.barcodeId : '') + (filterString ? filterString : '')),
		dataType: 'html',
		success: function (data){
			removeAjaxLoader(o.el);
			o.el.html(data);
		}
	});
}