
function formatTitle(title, currentArray, currentIndex, currentOpts) {
    return '<div id="gallery-title"><span><a href="javascript:;" onclick="$.fancybox.close();"><span class="ui-icon ui-icon-circle-close"></span></a></span>' + (title && title.length ? '<b>' + title + '</b>' : '' ) + 'Image ' + (currentIndex + 1) + ' of ' + currentArray.length + '</div>';
}

$(document).ready(function (){
	$(".galleryAddon").fancybox({
			'showCloseButton'	: false,
			'titlePosition' 		: 'inside',
			'titleFormat'		: formatTitle
		});

	});