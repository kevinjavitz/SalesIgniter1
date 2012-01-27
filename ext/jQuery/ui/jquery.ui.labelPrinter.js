(function ($) {

	var labelsToLoad = [
		DIR_WS_CATALOG + 'ext/dymo_label_framework.js',
		DIR_WS_CATALOG + 'ext/labelPrinterMethods/DymoPrinting.js',
		DIR_WS_CATALOG + 'ext/labelPrinterMethods/PdfPrinting.js',
		DIR_WS_CATALOG + 'ext/labelPrinterMethods/SpreadsheetPrinting.js'
	];
	var labelTypesLoaded = false;
	var totalToLoad = labelsToLoad.length;
	var totalLoaded = 0;

	$.each(labelsToLoad, function (){
		$.ajax({
			url: this,
			dataType: "script",
			cache: false,
			success: function (){
				totalLoaded++;
				if (totalLoaded == totalToLoad){
					labelTypesLoaded = true;
				}
			}
		});
	});

	function checkLoadStatus(){
		var Def = $.Deferred();

		var interval = setInterval(function (){
			if (labelTypesLoaded === true){
				clearInterval(interval);
				Def.resolve();
			}
		}, 500);

		return Def.promise();
	}

	$.widget("ui.labelPrinter", {
		options : {
			_needsAllowedCheck : [],
			_userData : {},
			_printMethod : '',
			_printMethodScripts : {},
			_dialog : {},
			labelTypes: [],
			printUrl : '',
			dataFile : '',
			getData : function () {
				return {};
			},
			beforeShow : function () {
				return true;
			}
		},
		_create : function () {
			var self = this, o = this.options;

			if (this.options.labelTypes.length == 0){
				this.options.labelTypes = ['8160-b', '8160-s', '8164'];
			}

			$.when(checkLoadStatus()).then(function (){
				o._printMethodScripts.pdf = new PdfPrinting(self);
				self._checkAllowed('pdf', o._printMethodScripts.pdf);

				o._printMethodScripts.spreadsheet = new SpreadsheetPrinting(self);
				self._checkAllowed('spreadsheet', o._printMethodScripts.spreadsheet);

				o._printMethodScripts.dymo = new DymoPrinting(self);
				self._checkAllowed('dymo', o._printMethodScripts.dymo);
			});

			o._dialog = $('<div></div>').dialog({
				autoOpen : false,
				width : 600,
				height : 400,
				open : function () {
					self.loadSplash();
				}
			});

			$(this.element[0]).click(function () {
				if (o.beforeShow() === true){
					$(o._dialog).dialog('open');
				}
			});
		},
		_init : function () {
		},
		_setPrintMethod : function (val) {
			this.options._printMethod = val;
		},
		_checkAllowed : function (key, obj) {
			var self = this, o = this.options;
			var isAllowed = obj.isAllowed();
			if (isAllowed == 'PleaseWait'){
				o._needsAllowedCheck.push(key);
			}
			else if (isAllowed === false){
				delete o._printMethodScripts[key];
			}
		},
		_getPageOne : function () {
			var self = this, o = this.options;
			var List = $('<ul></ul>')
				.addClass('labelPrinterSplashList')
				.css({
					'list-style' : 'none',
					'margin' : 0,
					'padding' : 0
				});

			$.each(o._printMethodScripts, function (k, v) {
				var arrKey = $.inArray(k, o._needsAllowedCheck);
				if (arrKey > -1){
					if (this.isAllowed() === true){
						delete o._needsAllowedCheck[arrKey];
					}
					else {
						delete o._printMethodScripts[k];
						return;
					}
				}

				var listItem = $('<li></li>')
					.data('print_method', k)
					.addClass('ui-corner-all')
					.css({
						textAlign : 'center',
						border : '2px solid #fff',
						margin : '10px',
						padding : '10px',
						float : 'left'
					});
				this.addSplashImage(listItem);

				List.append(listItem);
			});

			return List;
		},
		GetPrintData : function () {
			var self = this, o = this.options;
			var urlData = o.getData.apply();
			$.each(this.options._userData, function (k, v) {
				urlData += '&' + k + '=' + v;
			});
			return urlData;
		},
		loadSplash : function (isBack) {
			var self = this, o = this.options;
			var PageOne = $(self._getPageOne());

			self.setDialogTitle('Select Print Method');
			self.setDialogButtons({});
			self.setDialogBody(PageOne);

			PageOne.find('li').each(function () {
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
						self.setUserData('printMethod', $(this).data('print_method'));
						o._printMethodScripts[$(this).data('print_method')].load();
					});
			});
		},
		setDialogTitle : function (title) {
			$(this.options._dialog).dialog('option', 'title', title);
		},
		setDialogBody : function (el) {
			$(this.options._dialog).html(el);
		},
		setDialogButtons : function (data) {
			$(this.options._dialog).dialog('option', 'buttons', data);
		},
		setUserData : function (k, v) {
			this.options._userData[k] = v;
		},
		getUserData : function (k) {
			return this.options._userData[k];
		}
	});
})(jQuery);
