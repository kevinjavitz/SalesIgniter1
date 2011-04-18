<script type="text/javascript">
	$(document).ready(function (){
		$("form[name$='quick_find']").css('display','none');
		$('.guidedHeader').css('display','none');
	});

</script>
<div <?php echo (isset($box_id) ? 'id="' . $box_id . '" ' : '');?>class="ui-widget ui-widget-content ui-infobox ui-corner-all-medium">
 <div class="ui-widget-header ui-infobox-header ui-corner-all">
  <div class="ui-infobox-header-text"><?php echo $boxHeading;?></div>
 </div>
 <div class="ui-infobox-content ui-corner-bottom-medium"><?php echo $boxContent;?></div>
</div>