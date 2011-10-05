(function( $, undefined ) {

	$.fn.layoutEditor = function(o){

		this.tabs = {};

		this.initMainTab = function (id){
			this.tabs[id].init();
		};
	};

})(jQuery);
