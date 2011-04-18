<script type="text/javascript">
 $('.popupWindow').parent().find('.ui-dialog-title').html('<?php echo addslashes($pageHeader);?>');
</script>
<div class="ui-widget ui-widget-all ui-corner-all pageContent"><?php
	echo $pageContent;
?></div>