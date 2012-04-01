<script>
	$(document).ready(function (){
		<?php if (isset($pageTitle) && !empty($pageTitle)){ ?>
		$('.popupWindowContent').parent().dialog('option', 'title', '<?php echo $pageTitle;?>');
		<?php } ?>
	});
</script>
<div class="ui-widget popupWindowContent">
	<?php echo $pageContent;?>
</div>