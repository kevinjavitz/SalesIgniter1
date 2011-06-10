/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.contentsCss = ['../ext/jQuery/themes/smoothness/ui.all.css'];
	config.scayt_autoStartup = false;
	config.disableNativeSpellChecker = false;

	/* Allow php code in fck - not needed right now
		config.protectedSource.push( /<\?[\s\S]*?\?>/g ) ;
		config.protectedSource.push( /<\?php[\s\S]*?\?>/g ) ;
	*/
};
