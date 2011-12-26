$(document).ready(function (){
	$('select[name="meta_stores_id"]').change(function (){
		window.location.href = $(this).attr('jslink') + '?meta_stores_id=' + $(this).val();
	});
});