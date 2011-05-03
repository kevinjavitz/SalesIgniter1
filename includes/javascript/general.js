
// jQuery based CSS parser
// documentation: http://youngisrael-stl.org/wordpress/2009/01/16/jquery-css-parser/
// Version: 1.3
// Copyright (c) 2011 Daniel Wachsstock
// MIT license:
// Permission is hereby granted, free of charge, to any person
// obtaining a copy of this software and associated documentation
// files (the "Software"), to deal in the Software without
// restriction, including without limitation the rights to use,
// copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the
// Software is furnished to do so, subject to the following
// conditions:

// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
// OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
// HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
// WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
// OTHER DEALINGS IN THE SOFTWARE.


(function($){

	// utility function, since we want to allow $('style') and $(document), so we need to look for elements in the jQuery object ($.fn.filter) and elements that are children of the jQuery object ($.fn.find)
	$.fn.findandfilter = function(selector){
		var ret = this.filter(selector).add(this.find(selector));
		ret.prevObject = ret.prevObject.prevObject; // maintain the filter/end chain correctly (the filter and the find both push onto the chain).
		return ret;
	};

	$.fn.parsecss = function(callback, parseAttributes){
		var parse = function(str) { $.parsecss(str, callback) }; // bind the callback
		this
			.findandfilter ('style').each (function(){
			parse(this.innerHTML);
		})
			.end()
			.findandfilter ('link[type="text/css"]').each (function(){
			// only get the stylesheet if it's not disabled, it won't trigger cross-site security (doesn't start with anything like http:) and it uses the appropriate media)
			if (!this.disabled && !/^\w+:/.test($(this).attr('href')) && $.parsecss.mediumApplies(this.media)) $.get(this.href, parse);
		})
			.end();

		if (parseAttributes){
			$.get(location.pathname+location.search, 'text', function(HTMLtext) {
				styleAttributes(HTMLtext, callback);
			});
		}

		return this;
	};

	$.parsecss = function(str, callback){
		var ret = {};
		str = munge(str).replace(/@(([^;`]|`[^b]|`b[^%])*(`b%)?);?/g, function(s,rule){
			// @rules end with ; or a block, with the semicolon not being part of the rule but the closing brace (represented by `b%) is
			processAtRule($.trim(rule), callback);
			return '';
		});

		$.each (str.split('`b%'), function(i,css){ // split on the end of a block
			css = css.split('%b`'); // css[0] is the selector; css[1] is the index in munged for the cssText
			if (css.length < 2) return; // invalid css
			css[0] = restore(css[0]);
			ret[css[0]] = $.extend(ret[css[0]] || {}, parsedeclarations(css[1]));
		});
		callback(ret);
	};
	// explanation of the above: munge(str) strips comments and encodes strings and brace-delimited blocks, so that
	// %b` corresponds to { and `b% corresponds to }
	// munge(str) replaces blocks with %b`1`b% (for example)
	//
	// str.split('`b%') splits the text by '}' (which ends every CSS statement)
	// Each so the each(munge(str... function(i,css)
	// is called with css being empty (the string after the last delimiter), an @rule, or a css statement of the form
	// selector %b`n where n is a number (the block was turned into %b`n`b% by munge). Splitting on %b` gives the selector and the
	// number corresponding to the declaration block. parsedeclarations will do restore('%b`'+n+'`b%') to get it back.

	// if anyone ever implements http://www.w3.org/TR/cssom-view/#the-media-interface, we're ready
	$.parsecss.mediumApplies = (window.media && window.media.query) || function(str){
		if (!str) return true; // if no descriptor, everything applies
		if (str in media) return media[str];
		var style = $('<style media="'+str+'">body {position: relative; z-index: 1;}</style>').appendTo('head');
		return media[str] = [$('body').css('z-index')==1, style.remove()][0]; // the [x,y][0] is a silly hack to evaluate two expressions and return the first
	};

	$.parsecss.isValidSelector = function(str){
		var s = $('<style>'+str+'{}</style>').appendTo('head')[0];
		// s.styleSheet is IE; it accepts illegal selectors but converts them to UNKNOWN. Standards-based (s.shee.cssRules) just reject the rule
		return [s.styleSheet ? !/UNKNOWN/i.test(s.styleSheet.cssText) : !!s.sheet.cssRules.length, $(s).remove()][0]; // the [x,y][0] is a silly hack to evaluate two expressions and return the first
	};

	$.parsecss.parseArguments = function(str){
		if (!str) return [];
		var ret = [], mungedArguments = munge(str, true).split(/\s+/); // can't use $.map because it flattens arrays !
		for (var i = 0; i < mungedArguments.length; ++i) {
			var a = restore(mungedArguments[i]);
			try{
				ret.push(eval('('+a+')'));
			}catch(err){
				ret.push(a);
			}
		}
		return ret;
	};

	// uses the parsed css to apply useful jQuery functions
	$.parsecss.jquery = function(css){
		for (var selector in css){
			for (var property in css[selector]){
				var match = /^-jquery(-(.*))?/.exec(property);
				if (!match) continue;
				var value = munge(css[selector][property]).split('!'); // exclamation point separates the parts of livequery actions
				var which = match[2];
				dojQuery(selector, which, restore(value[0]), restore(value[1]));
			}
		}
	};

	// expose the styleAttributes function
	$.parsecss.styleAttributes = styleAttributes;

	// caches
	var media = {}; // media description strings
	var munged = {}; // strings that were removed by the parser so they don't mess up searching for specific characters

	// private functions

	function parsedeclarations(index){ // take a string from the munged array and parse it into an object of property: value pairs
		var str = munged[index].replace(/^{|}$/g, ''); // find the string and remove the surrounding braces
		str = munge(str); // make sure any internal braces or strings are escaped
		var parsed = {};
		$.each (str.split(';'), function (i, decl){
			decl = decl.split(':');
			if (decl.length < 2) return;
			parsed[restore(decl[0])] = restore(decl.slice(1).join(':'));
		});
		return parsed;
	}

	// replace strings and brace-surrounded blocks with %s`number`s% and %b`number`b%. By successively taking out the innermost
	// blocks, we ensure that we're matching braces. No way to do this with just regular expressions. Obviously, this assumes no one
	// would use %s` in the real world.
	// Turns out this is similar to the method that Dean Edwards used for his CSS parser in IE7.js (http://code.google.com/p/ie7-js/)
	var REbraces = /{[^{}]*}/;
	var REfull = /\[[^\[\]]*\]|{[^{}]*}|\([^()]*\)|function(\s+\w+)?(\s*%b`\d+`b%){2}/; // match pairs of parentheses, brackets, and braces and function definitions.
	var REatcomment = /\/\*@((?:[^\*]|\*[^\/])*)\*\//g; // comments of the form /*@ text */ have text parsed
	// we have to combine the comments and the strings because comments can contain string delimiters and strings can contain comment delimiters
	// var REcomment = /\/\*(?:[^\*]|\*[^\/])*\*\/|<!--|-->/g; // other comments are stripped. (this is a simplification of real SGML comments (see http://htmlhelp.com/reference/wilbur/misc/comment.html) , but it's what real browsers use)
	// var REstring = /\\.|"(?:[^\\\"]|\\.|\\\n)*"|'(?:[^\\\']|\\.|\\\n)*'/g; //  match escaped characters and strings
	var REcomment_string =
		/(?:\/\*(?:[^\*]|\*[^\/])*\*\/)|(\\.|"(?:[^\\\"]|\\.|\\\n)*"|'(?:[^\\\']|\\.|\\\n)*')/g;
	var REmunged = /%\w`(\d+)`\w%/;
	var uid = 0; // unique id number
	function munge(str, full){
		str = str
			.replace(REatcomment,'$1') // strip / *@ comments but leave the text (to let invalid CSS through)
			.replace(REcomment_string, function (s, string){ // strip strings and escaped characters, leaving munged markers, and strip comments
			if (!string) return '';
			var replacement = '%s`'+(++uid)+'`s%';
			munged[uid] = string.replace(/^\\/,''); // strip the backslash now
			return replacement;
		})
			;
		// need a loop here rather than .replace since we need to replace nested braces
		var RE = full ? REfull : REbraces;
		while (match = RE.exec(str)){
			replacement = '%b`'+(++uid)+'`b%';
			munged[uid] = match[0];
			str = str.replace(RE, replacement);
		}
		return str;
	}

	function restore(str){
		if (str === undefined) return str;
		while (match = REmunged.exec(str)){
			str = str.replace(REmunged, munged[match[1]]);
		}
		return $.trim(str);
	}

	function processAtRule (rule, callback){
		var split = rule.split(/\s+/); // split on whitespace
		var type = split.shift(); // first word
		if (type=='media'){
			var css = restore(split.pop()).slice(1,-1); // last word is the rule; need to strip the outermost braces
			if ($.parsecss.mediumApplies(split.join(' '))){
				$.parsecss(css, callback);
			}
		}else if (type='import'){
			var url = restore(split.shift());
			if ($.parsecss.mediumApplies(split.join(' '))){
				url = url.replace(/^url\(|\)$/gi, '').replace(/^["']|["']$/g, ''); // remove the url('...') wrapper
				$.get(url, function(str) { $.parsecss(str, callback) });
			}
		}
	}

	function dojQuery (selector, which, value, value2){ // value2 is the value for the livequery no longer match
		if (/show|hide/.test(which)) which +=  'Default'; // -jquery-show is a shortcut for -jquery-showDefault
		if (value2 !== undefined && $.livequery){
			// mode is 0 for a static value (can be evaluated when parsed);
			// 1 for delayed (refers to "this" which means it needs to be evaluated separately for each element matched), and
			// 2 for livequery; evaluated whenever elments change
			var mode = 2;
		}else{
			mode = /\bthis\b/.test(value) ? 1 : 0;
		}
		if (which && $.fn[which]){
			// a plugin
			// late bind parseArguments so "this" is defined correctly
			function p (str) { return function() { return $.fn[which].apply($(this), $.parsecss.parseArguments.call(this, str)) } };
			switch (mode){
				case 0: return $.fn[which].apply($(selector), $.parsecss.parseArguments(value));
				case 1: return $(selector).each(p(value));
				case 2: return (new $.livequery(selector, document, undefined, p(value), value2 === '' ? undefined : p(value2))).run();
			}
		}else if (which){
			// a plugin but one that was not defined
			return undefined;
		}else{
			// straight javascript
			switch (mode){
				case 0: return eval(value);
				case 1: return $(selector).each(Function(value));
				case 2: return (new $.livequery(selector, document, undefined, Function(value), value2 === '' ? undefined : Function(value2))).run();
			}
		}
	}

	// override show and hide. $.data(el, 'showDefault') is a function that is to be used for show if no arguments are passed in (if there are arguments, they override the stored function)
	// Many of the effects call the native show/hide() with no arguments, resulting in an infinite loop.
	var _show = {show: $.fn.show, hide: $.fn.hide}; // save the originals
	$.each(['show','hide'], function(){
		var which = this, show = _show[which], plugin = which+'Default';
		$.fn[which] = function(){
			if (arguments.length > 0) return show.apply(this, arguments);
			return this.each(function(){
				var fn = $.data(this, plugin), $this = $(this);
				if (fn){
					$.removeData(this, plugin); // prevent the infinite loop
					fn.call($this);
					$this.queue(function(){$this.data(plugin, fn).dequeue()}); // put the function back at the end of the animation
				}else{
					show.call($this);
				}
			});
		};
		$.fn[plugin] = function(){
			var args = $.makeArray(arguments), name = args[0];
			if ($.fn[name]){ // a plugin
				args.shift();
				var fn = $.fn[name];
			}else if ($.effects && $.effects[name]){ // a jQuery UI effect. They require an options object as the second argument
				if (typeof args[1] != 'object') args.splice(1,0,{});
				fn = _show[which];
			}else{ // regular show/hide
				fn = _show[which];
			}
			return this.data(plugin, function(){fn.apply(this,args)});
		};
	});

	// experimental: find unrecognized style attributes in elements by reloading the code as text
	var RESGMLcomment = /<!--([^-]|-[^-])*-->/g; // as above, a simplification of real comments. Don't put -- in your HTML comments!
	var REnotATag = /(>)[^<]*/g;
	var REtag = /<(\w+)([^>]*)>/g;

	function styleAttributes (HTMLtext, callback) {
		var ret = '', style, tags = {}; //  keep track of tags so we can identify elements unambiguously
		HTMLtext = HTMLtext.replace(RESGMLcomment, '').replace(REnotATag, '$1');
		munge(HTMLtext).replace(REtag, function(s, tag, attrs){
			tag = tag.toLowerCase();
			if (tags[tag]) ++tags[tag]; else tags[tag] = 1;
			if (style = /\bstyle\s*=\s*(%s`\d+`s%)/i.exec(attrs)){ // style attributes must be of the form style = "a: bc" ; they must be in quotes. After munging, they are marked with numbers. Grab that number
				var id = /\bid\s*=\s*(\S+)/i.exec(attrs); // find the id if there is one.
				if (id) id = '#'+restore(id[1]).replace(/^['"]|['"]$/g,''); else id = tag + ':eq(' + (tags[tag]-1) + ')';
				ret += [id, '{', restore(style[1]).replace(/^['"]|['"]$/g,''),'}'].join('');
			}
		});
		$.parsecss(ret, callback);
	}
})(jQuery);

function urldecode(str){
    // Decodes URL-encoded string  
    // 
    // version: 1004.2314
    // discuss at: http://phpjs.org/functions/urldecode    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)    // +      input by: travc
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Lars Fischer
    // +      input by: Ratheous    // +   improved by: Orlando
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +      bugfixed by: Rob
    // %        note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    // %        note 2: Please be aware that this function expects to decode from UTF-8 encoded strings, as found on    // %        note 2: pages served as UTF-8
    // *     example 1: urldecode('Kevin+van+Zonneveld%21');
    // *     returns 1: 'Kevin van Zonneveld!'
    // *     example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
    // *     returns 2: 'http://kevin.vanzonneveld.net/'    // *     example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
    // *     returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'
    var returnVal = '';
    if (typeof str != 'object' && str.length > 0){
    	returnVal = decodeURIComponent(str.replace(/\+/g, '%20'));
    }
    return returnVal;
}

function parse_str (str, array){
	// Parses GET/POST/COOKIE data and sets global variables
	//
	// version: 1004.2314
	// discuss at: http://phpjs.org/functions/parse_str
	// +   original by: Cagri Ekin
	// +   improved by: Michael White (http://getsprink.com)
	// +    tweaked by: Jack
	// +   bugfixed by: Onno Marsman
	// +   reimplemented by: stag019
	// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	// +   bugfixed by: stag019
	// -    depends on: urldecode
	// %        note 1: When no argument is specified, will put variables in global scope.
	// *     example 1: var arr = {};
	// *     example 1: parse_str('first=foo&second=bar', arr);
	// *     results 1: arr == { first: 'foo', second: 'bar' }
	// *     example 2: var arr = {};
	// *     example 2: parse_str('str_a=Jack+and+Jill+didn%27t+see+the+well.', arr);
	// *     results 2: arr == { str_a: "Jack and Jill didn't see the well." }
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

$(document).ready(function (){
	$(document).parsecss($.parsecss.jquery);
	
	$('a', $('.headerMenuHeadingBlock')).each(function (){
		var $link = $(this);
		$($link.parent()).hover(function (){
			$link.css('cursor', 'pointer').addClass('ui-state-hover');

			if ($('ul', $(this)).size() > 0){
				var $menuList = $('ul:first', $(this));
				var offSetLeft = $(this).width();

				var leftMenu = $(this).parent().offset(false).left + (offSetLeft + $(this).width());
				if (leftMenu > $(window).width()){
					offSetLeft = -($menuList.width() + 5);
				}
				$menuList.css({
					visibility: 'visible',
					left: offSetLeft,
					zIndex: 9999
				});
			}
		}, function (){
			$link.css({cursor: 'default'}).removeClass('ui-state-hover');

			if ($('ul', this).size() > 0){
				$('ul:first', this).css({
					visibility: 'hidden'
				});
			}
		}).click(function (){
			document.location = $('a:first', this).attr('href');
		});
	});

	$('.headerMenuHeadingBlock').hover(function (){
		var headingBlock = this;
		var $spanObj = $('.headerMenuHeading', headingBlock);
		$spanObj.addClass('ui-state-hover').addClass('ui-corner-all').css({
			cursor: 'default',
			fontWeight: 'bold'
		});

		var offSet = $(headingBlock).offset();
		$('div:first', $(headingBlock)).each(function (){
			$(this).css({
				position: 'absolute',
				width: 'auto',
				top: offSet.top + $(headingBlock).parent().height(),
				left: offSet.left,
				zIndex: 9998
			}).show();

			$('ul:first', $(this)).css('visibility', 'visible');
		});
	}, function (){
		var $spanObj = $('.headerMenuHeading', this);
		$spanObj.removeClass('ui-state-hover').css({
			cursor: 'default'
		});
		$('.ui-menu-flyout:first', $(this)).hide();
	});

	$('#categoriesBoxMenu').accordion({
		header: 'h3',
		collapsible: true,
		autoHeight: false,
		active: $('.currentCategory', $('#categoriesBoxMenu')),
		icons: {
			header: 'ui-icon-circle-triangle-s',
			headerSelected: 'ui-icon-circle-triangle-n'
		}
	});
	
	$('a', $('#categoriesBoxMenu')).each(function (){
		var $link = $(this);
		$($link.parent()).hover(function (){
			$link.css('cursor', 'pointer').addClass('ui-state-hover');

			var linkOffset = $link.parent().offset();
			var boxOffset = $('#categoriesBoxMenu').offset();
			if ($('ul', $(this)).size() > 0){
				var $menuList = $('ul:first', $(this));
				$menuList.css({
					position: 'absolute',
					top: $link.parent().position().top,
					left: $link.parent().position().left + $link.parent().innerWidth() - 5,
					backgroundColor: '#FFFFFF',
					zIndex: 9999
				}).show();
			}
		}, function (){
			$link.css({cursor: 'default'}).removeClass('ui-state-hover');

			if ($('ul', this).size() > 0){
				$('ul:first', this).hide();
			}
		}).click(function (){
			document.location = $('a:first', this).attr('href');
		});
	});
	
	$('.ui-button').each(function (){
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
