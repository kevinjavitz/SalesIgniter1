var PdfPrinting = function (LabelPrinter) {

	var mainSelf = this;
	var labelTypeSpecs = {
		'8160-b' : {
			perPage : 30,
			options : {
				'Starting Row' : {
					inputType : 'selectbox',
					inputName : 'row_start',
					selected : '1',
					data : {
						'1' : '1',
						'2' : '2',
						'3' : '3',
						'4' : '4',
						'5' : '5',
						'6' : '6',
						'7' : '7',
						'8' : '8',
						'9' : '9',
						'10' : '10'
					}
				},
				'Starting Col' : {
					inputType : 'selectbox',
					inputName : 'col_start',
					selected : '1',
					data : {
						'1' : '1',
						'2' : '2',
						'3' : '3'
					}
				}
			}
		},
		'8160-s' : {
			perPage : 30,
			options : {
				'Starting Row' : {
					inputType : 'selectbox',
					inputName : 'row_start',
					selected : '1',
					data : {
						'1' : '1',
						'2' : '2',
						'3' : '3',
						'4' : '4',
						'5' : '5',
						'6' : '6',
						'7' : '7',
						'8' : '8',
						'9' : '9',
						'10' : '10'
					}
				},
				'Starting Col' : {
					inputType : 'selectbox',
					inputName : 'col_start',
					selected : '1',
					data : {
						'1' : '1',
						'2' : '2',
						'3' : '3'
					}
				}
			}
		},
		8164 : {
			perPage : 6,
			options : {
				'Starting Row' : {
					inputType : 'selectbox',
					inputName : 'row_start',
					selected : '1',
					data : {
						'1' : '1',
						'2' : '2',
						'3' : '3'
					}
				},
				'Starting Col' : {
					inputType : 'selectbox',
					inputName : 'col_start',
					selected : '1',
					data : {
						'1' : '1',
						'2' : '2'
					}
				}
			}
		}
	};

	var splashImage = 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAA8AAD/4QMpaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjAtYzA2MCA2MS4xMzQ3NzcsIDIwMTAvMDIvMTItMTc6MzI6MDAgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzUgV2luZG93cyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGNzlEMTcwODI4MEQxMUUxQjQ0NEZEMjJBQTFCQjVCOCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGNzlEMTcwOTI4MEQxMUUxQjQ0NEZEMjJBQTFCQjVCOCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkY3OUQxNzA2MjgwRDExRTFCNDQ0RkQyMkFBMUJCNUI4IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY3OUQxNzA3MjgwRDExRTFCNDQ0RkQyMkFBMUJCNUI4Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQABgQEBAUEBgUFBgkGBQYJCwgGBggL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';
	var Image8160 = 'data:image/jpeg;base64,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';
	var Image8164 = 'data:image/jpeg;base64,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';

	var PageTwo = $('<table>' +
		'<tbody></tbody>' +
		'</table>');

	this.isAllowed = function () {
		return true;
	};

	this.addSplashImage = function (ListItem) {
		ListItem.append('<img src="' + splashImage + '"><br>PDF For Standard Printer');
	};

	this.load = function () {
		this.loadPageOne();
	};

	this.loadPageOne = function (isBack){
		var PageOne = $('<table width="100%" cellspacing="20">' +
			'<tbody>' +
			'<tr>' +
			($.inArray('8160-b', LabelPrinter.option('labelTypes')) > -1 ? '<td data-label_type="8160-b" align="center" class="ui-corner-all" style="border:2px solid white;"><img src="' + Image8160 + '"><br>Avery 5160/8160<br>( For: Barcode )</td>' : '') +
			($.inArray('8160-s', LabelPrinter.option('labelTypes')) > -1 ? '<td data-label_type="8160-s" align="center" class="ui-corner-all" style="border:2px solid white;"><img src="' + Image8160 + '"><br>Avery 5160/8160<br>( For: Shipping )</td>' : '') +
			($.inArray('8164', LabelPrinter.option('labelTypes')) > -1 ? '<td data-label_type="8164" align="center" class="ui-corner-all" style="border:2px solid white;"><img src="' + Image8164 + '"><br>Avery 5164/8164<br>( For: Product Info )</td>' : '') +
			'</tr>' +
			'</tbody>' +
			'</table>');

		PageOne.find('td').each(function () {
			$(this)
				.mouseover(function () {
					$(this).css({
						'cursor' : 'pointer',
						'border-color' : '#ccc'
					});
				})
				.mouseout(function () {
					$(this).css({
						'cursor' : 'default',
						'border-color' : '#fff'
					});
				})
				.click(function () {
					$(this).css({
						'cursor' : 'default',
						'border-color' : '#fff'
					});

					LabelPrinter.setUserData('labelType', $(this).data('label_type'));
					mainSelf.loadPageTwo();
				});
		});

		LabelPrinter.setDialogTitle('Select Label Type')
		LabelPrinter.setDialogBody(PageOne);
		LabelPrinter.setDialogButtons({
			'Back' : function () {
				LabelPrinter.loadSplash(true);
			}
		});
	};

	this.loadPageTwo = function (isBack){
		var selectedSpecs = labelTypeSpecs[LabelPrinter.getUserData('labelType')];

		PageTwo.find('tbody').empty();
		PageTwo.find('tbody').append('<tr>' +
			'<td>Labels Per Page: </td>' +
			'<td>' + selectedSpecs.perPage + '</td>' +
			'</tr>');

		$.each(selectedSpecs.options, function (optionText, oInfo) {
			var optionHtml = '';
			if (oInfo.inputType == 'radio'){
				$.each(oInfo.data, function (k, v) {
					var checked = (k == oInfo.selected ? ' checked="checked"' : '');
					optionHtml += '<input type="radio" name="' + oInfo.inputName + '" value="' + k + '"' + checked + '> ' + v + '<br>';
				});
			}
			else {
				if (oInfo.inputType == 'selectbox'){
					optionHtml += '<select name="' + oInfo.inputName + '">';
					$.each(oInfo.data, function (k, v) {
						var selected = (k == oInfo.selected ? ' selected="selected"' : '');
						optionHtml += '<option value="' + k + '"' + selected + '> ' + v + '</option>';
					});
					optionHtml += '</select>';
				}
			}
			PageTwo.find('tbody').append('<tr>' +
				'<td>' + optionText + ': </td>' +
				'<td>' + optionHtml + '</td>' +
				'</tr>');
		});

		LabelPrinter.setDialogTitle('Configure Label Sheet');
		LabelPrinter.setDialogBody(PageTwo);
		LabelPrinter.setDialogButtons({
			'Back' : function () {
				mainSelf.loadPageOne(true);
			},
			'Print Labels' : function () {
				PageTwo.find('input[type=radio]:checked, select').each(function () {
					LabelPrinter.setUserData($(this).attr('name'), $(this).val());
				});

				window.open(LabelPrinter.option('printUrl') + '&' + LabelPrinter.GetPrintData());
			}
		});
	};
};
