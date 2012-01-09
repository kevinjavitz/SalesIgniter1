var SpreadsheetPrinting = function (LabelPrinter) {

	var mainSelf = this;
	var exportOptions = {
		'Separator' : {
			inputType : 'selectbox',
			inputName : 'field_separator',
			selected : 'tab',
			data : {
				'tab' : 'Tab',
				'semicolon' : ';',
				'colon' : ':',
				'comma' : ','
			}
		}
	};

	var splashImage = 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAA8AAD/4QMpaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjAtYzA2MCA2MS4xMzQ3NzcsIDIwMTAvMDIvMTItMTc6MzI6MDAgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzUgV2luZG93cyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxQ0VGM0EzRTMzMjYxMUUxQUI0OUE5N0U3RkQ3Q0E5OCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoxQ0VGM0EzRjMzMjYxMUUxQUI0OUE5N0U3RkQ3Q0E5OCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjFDRUYzQTNDMzMyNjExRTFBQjQ5QTk3RTdGRDdDQTk4IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjFDRUYzQTNEMzMyNjExRTFBQjQ5QTk3RTdGRDdDQTk4Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQABgQEBAUEBgUFBgkGBQYJCwgGBggL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';

	var PageOne = $('<table>' +
		'<tbody></tbody>' +
		'</table>');

	this.isAllowed = function () {
		return true;
	};

	this.addSplashImage = function (ListItem) {
		ListItem.append('<img src="' + splashImage + '"><br>Spreadsheet Export');
	};

	this.load = function () {
		this.loadPageOne();
	};

	this.loadPageOne = function (isBack){
		LabelPrinter.setUserData('labelType', 'spreadsheet');
		PageOne.find('tbody').empty();

		$.each(exportOptions, function (optionText, oInfo) {
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
			PageOne.find('tbody').append('<tr>' +
				'<td>' + optionText + ': </td>' +
				'<td>' + optionHtml + '</td>' +
				'</tr>');
		});

		LabelPrinter.setDialogTitle('Configure Export');
		LabelPrinter.setDialogBody(PageOne);
		LabelPrinter.setDialogButtons({
			'Back' : function () {
				LabelPrinter.loadSplash(true);
			},
			'Save File' : function () {
				PageOne.find('input[type=radio]:checked, select').each(function () {
					LabelPrinter.setUserData($(this).attr('name'), $(this).val());
				});

				window.open(LabelPrinter.option('printUrl') + '&' + LabelPrinter.GetPrintData());
			}
		});
	};
};
