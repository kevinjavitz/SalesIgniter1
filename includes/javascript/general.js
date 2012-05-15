

var m_names = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

function urldecode(str){
       var returnVal = '';
    if (typeof str != 'object' && str.length > 0){
    	returnVal = decodeURIComponent(str.replace(/\+/g, '%20'));
    }
    return returnVal;
}

function parse_str (str, array){
	var glue1 = '=',
		glue2 = '&',
		array2 = String(str).split(glue2),
		i,
		j,
		chr,
		tmp,
		key,
		value,
		bracket,
		keys,
		evalStr,
		that = this,
		fixStr = function (str) {
			return that.urldecode(str).replace(/([\\"'])/g, '\\$1').replace(/\n/g, '\\n').replace(/\r/g, '\\r');
		};

	if (!array){
		array = this.window;
	}

	for(i = 0; i < array2.length; i++){
		tmp = array2[i].split(glue1);
		if (tmp.length < 2){
			tmp = [tmp, ''];
		}
		key   = fixStr(tmp[0]);
		value = fixStr(tmp[1]);
		while(key.charAt(0) === ' '){
			key = key.substr(1);
		}
		if (key.indexOf('\0') !== -1){
			key = key.substr(0, key.indexOf('\0'));
		}
		if (key && key.charAt(0) !== '['){
			keys    = [];
			bracket = 0;
			for(j = 0; j < key.length; j++){
				if (key.charAt(j) === '[' && !bracket){
					bracket = j + 1;
				}else if (key.charAt(j) === ']'){
					if (bracket){
						if (!keys.length){
							keys.push(key.substr(0, bracket - 1));
						}
						keys.push(key.substr(bracket, j - bracket));
						bracket = 0;
						if (key.charAt(j + 1) !== '['){
							break;
						}
					}
				}
			}
			if (!keys.length){
				keys = [key];
			}
			for(j=0; j<keys[0].length; j++){
				chr = keys[0].charAt(j);
				if (chr === ' ' || chr === '.' || chr === '['){
					keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1);
				}
				if (chr === '[') {
					break;
				}
			}
			evalStr = 'array';
			for(j=0; j<keys.length; j++){
				key = keys[j];
				if ((key !== '' && key !== ' ') || j === 0){
					key = "'" + key + "'";
				}else{
					key = eval(evalStr + '.push([]);') - 1;
				}
				evalStr += '[' + key + ']';
				if (j !== keys.length - 1 && eval('typeof ' + evalStr) === 'undefined'){
					eval(evalStr + ' = [];');
				}
			}
			evalStr += " = '" + value + "';\n";
			eval(evalStr);
		}
	}
}

function compareGetVar(varName, compareVal, compareType){
	var param = js_get_url_param(varName);
	if (param !== false){
		switch(compareType){
			case 'isEqual':
				return (param.value == compareVal);
				break;
			case 'isNotEqual':
				return (param.value != compareVal);
				break;
			case 'greaterThan':
				return (param.value > compareVal);
				break;
			case 'greaterOrEqual':
				return (param.value >= compareVal);
				break;
			case 'lessThan':
				return (param.value < compareVal);
				break;
			case 'lessOrEqual':
				return (param.value <= compareVal);
				break;
			case 'isset':
				return true;
				break;
			default:
				return false;
				break;
		}
	}else{
		return false;
	}
}

function js_get_url_param(name){
	var getVars = {}, returnVal = false;
	if (window.location.href.indexOf('?') > -1){
		parse_str(window.location.href.slice(window.location.href.indexOf('?') + 1), getVars);
		$.each(getVars, function (k, v){
			if (k == name){
				returnVal = {
					name: hash[0],
					value: hash[1]
				};
				return;
			}
		});
	}
	return returnVal;
}

function js_get_all_get_params(exclude){
	exclude = exclude || [];

    var get_url = [];
	var getVars = {};
	if (window.location.href.indexOf('?') > -1){
		parse_str(window.location.href.slice(window.location.href.indexOf('?') + 1), getVars);
		$.each(getVars, function (k, v){
			if (k != sessionName && k != 'error' && $.inArray(k, exclude) == -1){
				get_url.push(k + '=' + v);
			}
		});
	}

    return (get_url.length > 0 ? get_url.join('&') + '&' : '');
}

function js_redirect(url){
	window.location = url;
}

function js_href_link(page, params, connection){
	connection = connection || 'NONSSL';
	params = params || '';
	if (page == '') {
		alert('Error:: Unable to determine the page link!' + "\n\n" + 'Function used: js_href_link(\'' + page + '\', \'' + params + '\', \'' + connection + '\')');
	}

	var link;
	link = 'http://' + serverName + DIR_WS_CATALOG;
	if (connection == 'SSL') {
		if (ENABLE_SSL == 'true') {
			link = 'https://' + serverName + DIR_WS_CATALOG;
		}
	}

	if (params == '') {
		link = link + page + '?' + SID;
	} else {
		link = link + page + '?' + params + '&' + SID;
	}

	while ( (link.substr(-1) == '&') || (link.substr(-1) == '?') ) link = link.substr(0, link.length - 1);

	return link;
}

function js_app_link(params, connection){
	connection = connection || request_type || 'SSL';
	params = params || '';

	var protocol = 'http';
	if (connection == 'SSL') {
		if (ENABLE_SSL == 'true') {
			protocol = protocol + 's';
		}
	}
	var link = protocol + '://' + serverName + DIR_WS_CATALOG;

	if (params == '') {
		link = link + 'application.php?' + SID;
	} else {
		var paramsObj = {};
		parse_str(params, paramsObj);
		if (paramsObj.appExt){
			link = link + paramsObj.appExt + '/';
		}
		link = link + paramsObj.app + '/' + paramsObj.appPage + '.php';

		var linkParams = [];
		var requireSID = false;
		$.each(paramsObj, function (k, v){
			if (k == 'app' || k == 'appPage' || k == 'appExt') return;
			if (k == 'rType' && v == 'ajax') requireSID = true;
			linkParams.push(k + '=' + v);
		});

		if (linkParams.length > 0){
			link = link + '?' + linkParams.join('&') + '&' + (requireSID === true ? sessionName + '=' + sessionId : SID);
		}else{
			link = link + '?' + (requireSID === true ? sessionName + '=' + sessionId : SID);
		}
	}

	while ( (link.substr(-1) == '&') || (link.substr(-1) == '?') ) link = link.substr(0, link.length - 1);

	return link;
}

function js_catalog_app_link(params, connection){
	return js_app_link(params, connection);
}

function showAjaxLoader($el, size, placement){
    if($el.position() != null){
        if (!$el.data('ajaxOverlay')){
            var $overlay = $('<div></div>').addClass('ui-widget-overlay').css({
                position: 'absolute',
                width: $el.outerWidth(),
                height: $el.outerHeight(),
                left: $el.position().left,
                top: $el.position().top,
                zIndex: $el.zIndex() + 1
            });
            if (placement && placement == 'append'){
                $overlay.appendTo($el);
            }else{
                $overlay.insertAfter($el);
            }
            var $ajaxLoader;
            if (placement == 'dialog'){
                $ajaxLoader = $('<div></div>').addClass('ui-ajax-loader-back').css({
                    position: 'absolute',
                    left: $el.position().left,
                    top: $el.position().top,
                    zIndex: $overlay.zIndex() + 1
                });
                var $ajaxLoader2 = $('<div></div>').addClass('ui-ajax-loader').addClass('ui-ajax-loader-' + size).addClass('ui-ajax-loader-dialog');
                $ajaxLoader2.appendTo($ajaxLoader);
                //$ajaxLoader.css({top:'50%',left:'50%',margin:'-'+($ajaxLoader.height() / 2)+'px 0 0 -'+($ajaxLoader.width() / 2)+'px'});
            }else{
                $ajaxLoader = $('<div></div>').addClass('ui-ajax-loader').addClass('ui-ajax-loader-' + size).css({
                    position: 'absolute',
                    left: $el.position().left,
                    top: $el.position().top,
                    zIndex: $overlay.zIndex() + 1
                });
            }

            if (placement && placement == 'append'){
                $ajaxLoader.appendTo($el);
            }else{
                $ajaxLoader.insertAfter($el);
            }

            $ajaxLoader.position({
                my: 'center center',
                at: 'center center',
                offset: '0 0',
                of: $overlay,
                collision: 'fit'
            });

            $el.data('ajaxOverlay', $overlay);
            $el.data('ajaxLoader', $ajaxLoader);
        }


        /*var $curOverlay = $el.data('ajaxOverlay');
        if ($curOverlay.outerWidth() != $el.outerWidth() || $curOverlay.outerHeight() != $el.outerHeight()){
            $curOverlay.css({
                height: $el.outerHeight(),
                width: $el.outerWidth()
            });
            $el.data('ajaxOverlay', $curOverlay);
        }*/

        $el.data('ajaxOverlay').show();
        $el.data('ajaxLoader').show();
    }
}

function hideAjaxLoader($el){
	if ($el.data('ajaxOverlay')){
		$el.data('ajaxOverlay').hide();
		$el.data('ajaxLoader').hide();
	}
}

function removeAjaxLoader($el){
	if ($el.data('ajaxOverlay')){
		$el.data('ajaxOverlay').remove();
		$el.removeData('ajaxOverlay');
	}
	if ($el.data('ajaxLoader')){
		$el.data('ajaxLoader').remove();
		$el.removeData('ajaxLoader');
	}
}

function popupWindow(url, w, h, p) {
	$('<div class="popupWindow"></div>').dialog({
		autoOpen: true,
		width: w || 'auto',
		height: h || 'auto',
		position: p || 'center',
		close: function (e, ui){
			$(this).dialog('destroy').remove();
		},
		open: function (e, ui){
			$(e.target).html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
			$.ajax({
				cache: false,
				url: url,
				dataType: 'html',
				success: function (data){
					$(e.target).html(data);
				}
			});
		}
	});
	return false;
}

function alertWindow(message){
	$('<div class="alertWindow"></div>').dialog({
		autoOpen: true,
		modal: true,
		width: 'auto',
		height: 'auto',
		position: 'center',
		title: 'Alert',
		close: function (e, ui){
			$(this).dialog('destroy').remove();
		},
		open: function (e, ui){
			$(e.target).html(message);
		}
	});
}

function showToolTip(settings){
	var elOffset = settings.el.offset();

	var $toolTip = $('<div>')
		.addClass('ui-widget')
		.addClass('ui-widget-content')
		.addClass('ui-corner-all')
		.css({
			position: 'absolute',
			left: elOffset.left,
			top: elOffset.top,
			zIndex: 9999,
			padding: '5px',
			whiteSpace: 'nowrap'
		})
		.html(settings.tipText)
		.appendTo($(document.body));

	$toolTip.css('left', (elOffset.left + settings.el.width()));
	$toolTip.css('top', (elOffset.top - $toolTip.height()));

	//alert((settings.offsetLeft + 200) + ' >= ' + $(window).width());
	if ((elOffset.left + 200) >= $(window).width()){
		$toolTip.css('left', (elOffset.left - $toolTip.width()));
	}
	if ((elOffset.top - $toolTip.height()) <= 0){
		$toolTip.css('top', (elOffset.top + settings.el.height() + $toolTip.height()));
	}
	return $toolTip;
}

$(document).ready(function (){

	$('.mydm').live('click', function(){
		popupWindow(js_app_link("appExt=infoPages&app=show_page&appPage=insurance_info&dialog=true"),"400","300");
		return false;
	});

	$('a[type=button], button').each(function (){
		var disable = false;
		if ($(this).hasClass('ui-state-disabled')){
			disable = true;
		}
		$(this).button({
			disabled: disable
		}).click(function (e){
			if ($(this).hasClass('ui-state-disabled')){
				e.preventDefault();
				return false;
			}
		});
	});

	$('.searchShowMoreLink a').click(function (){
		$('li', $(this).parent().parent()).show();
		$(this).parent().remove();
		return false;
	});

	$('.phpTraceView').click(function (e){
		e.preventDefault();

		var traceTable = $(this).parent().parent().find('table.phpTrace');
		if (traceTable.is(':visible')){
			traceTable.hide();
			$(this).html('View Trace');
		}else{
			traceTable.show();
			$(this).html('Hide Trace');
		}
	});

	$('[tooltip]').live('mouseover mouseout click', function (e){
		if (e.type == 'mouseover'){
			this.Tooltip = showToolTip({
				el: $(this),
				tipText: $(this).attr('tooltip')
			});
		}else{
			this.Tooltip.remove();
		}
	});

	$('[required=true]').each(function (){
		$('<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>').insertAfter(this);
	});
});

/*
$Id: general.js,v 1.3 2003/02/10 22:30:55 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

function SetFocus(TargetFormName) {
	var target = 0;
	if (TargetFormName != "") {
		for (i=0; i<document.forms.length; i++) {
			if (document.forms[i].name == TargetFormName) {
				target = i;
				break;
			}
		}
	}

	var TargetForm = document.forms[target];

	for (i=0; i<TargetForm.length; i++) {
		if ( (TargetForm.elements[i].type != "image") && (TargetForm.elements[i].type != "hidden") && (TargetForm.elements[i].type != "reset") && (TargetForm.elements[i].type != "submit") ) {
			TargetForm.elements[i].focus();

			if ( (TargetForm.elements[i].type == "text") || (TargetForm.elements[i].type == "password") ) {
				TargetForm.elements[i].select();
			}

			break;
		}
	}
}

function RemoveFormatString(TargetElement, FormatString) {
	if (TargetElement.value == FormatString) {
		TargetElement.value = "";
	}

	TargetElement.select();
}

function CheckDateRange(from, to) {
	if (Date.parse(from.value) <= Date.parse(to.value)) {
		return true;
	} else {
		return false;
	}
}

function IsValidDate(DateToCheck, FormatString) {
	var strDateToCheck;
	var strDateToCheckArray;
	var strFormatArray;
	var strFormatString;
	var strDay;
	var strMonth;
	var strYear;
	var intday;
	var intMonth;
	var intYear;
	var intDateSeparatorIdx = -1;
	var intFormatSeparatorIdx = -1;
	var strSeparatorArray = new Array("-"," ","/",".");
	var strMonthArray = new Array("jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec");
	var intDaysArray = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

	strDateToCheck = DateToCheck.toLowerCase();
	strFormatString = FormatString.toLowerCase();

	if (strDateToCheck.length != strFormatString.length) {
		return false;
	}

	for (i=0; i<strSeparatorArray.length; i++) {
		if (strFormatString.indexOf(strSeparatorArray[i]) != -1) {
			intFormatSeparatorIdx = i;
			break;
		}
	}

	for (i=0; i<strSeparatorArray.length; i++) {
		if (strDateToCheck.indexOf(strSeparatorArray[i]) != -1) {
			intDateSeparatorIdx = i;
			break;
		}
	}

	if (intDateSeparatorIdx != intFormatSeparatorIdx) {
		return false;
	}

	if (intDateSeparatorIdx != -1) {
		strFormatArray = strFormatString.split(strSeparatorArray[intFormatSeparatorIdx]);
		if (strFormatArray.length != 3) {
			return false;
		}

		strDateToCheckArray = strDateToCheck.split(strSeparatorArray[intDateSeparatorIdx]);
		if (strDateToCheckArray.length != 3) {
			return false;
		}

		for (i=0; i<strFormatArray.length; i++) {
			if (strFormatArray[i] == 'mm' || strFormatArray[i] == 'mmm') {
				strMonth = strDateToCheckArray[i];
			}

			if (strFormatArray[i] == 'dd') {
				strDay = strDateToCheckArray[i];
			}

			if (strFormatArray[i] == 'yyyy') {
				strYear = strDateToCheckArray[i];
			}
		}
	} else {
		if (FormatString.length > 7) {
			if (strFormatString.indexOf('mmm') == -1) {
				strMonth = strDateToCheck.substring(strFormatString.indexOf('mm'), 2);
			} else {
				strMonth = strDateToCheck.substring(strFormatString.indexOf('mmm'), 3);
			}

			strDay = strDateToCheck.substring(strFormatString.indexOf('dd'), 2);
			strYear = strDateToCheck.substring(strFormatString.indexOf('yyyy'), 2);
		} else {
			return false;
		}
	}

	if (strYear.length != 4) {
		return false;
	}

	intday = parseInt(strDay, 10);
	if (isNaN(intday)) {
		return false;
	}
	if (intday < 1) {
		return false;
	}

	intMonth = parseInt(strMonth, 10);
	if (isNaN(intMonth)) {
		for (i=0; i<strMonthArray.length; i++) {
			if (strMonth == strMonthArray[i]) {
				intMonth = i+1;
				break;
			}
		}
		if (isNaN(intMonth)) {
			return false;
		}
	}
	if (intMonth > 12 || intMonth < 1) {
		return false;
	}

	intYear = parseInt(strYear, 10);
	if (isNaN(intYear)) {
		return false;
	}
	if (IsLeapYear(intYear) == true) {
		intDaysArray[1] = 29;
	}

	if (intday > intDaysArray[intMonth - 1]) {
		return false;
	}

	return true;
}

function IsLeapYear(intYear) {
	if (intYear % 100 == 0) {
		if (intYear % 400 == 0) {
			return true;
		}
	} else {
		if ((intYear % 4) == 0) {
			return true;
		}
	}

	return false;
}
