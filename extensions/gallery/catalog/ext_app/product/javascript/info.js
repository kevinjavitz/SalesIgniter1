
function formatTitle(title, currentArray, currentIndex, currentOpts) {
    return '<div id="gallery-title"><span><a href="javascript:;" onclick="$.fancybox.close();"><img src="'+DIR_WS_CATALOG+'images/closelabel.gif" /></a></span>' + (title && title.length ? '<b>' + title + '</b>' : '' ) + 'Image ' + (currentIndex + 1) + ' of ' + currentArray.length + '</div>';
}

$(document).ready(function (){
	$(".galleryAddon").fancybox({
			'showCloseButton'	: false,
			'titlePosition' 		: 'inside',
			'titleFormat'		: formatTitle
		});

	});