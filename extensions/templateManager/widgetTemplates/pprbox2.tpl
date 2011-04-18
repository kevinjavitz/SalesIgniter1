<script type="text/javascript">
	$(document).ready(function (){
        $('.rentbbut .ui-button-text').html('Submit');
    });
</script>
<div <?php echo (isset($box_id) ? 'id="' . $box_id . '" ' : '');?>class="">
 <div class="">
  <div class="ui-infobox-header-text"><?php echo $boxHeading;?></div>
 </div>
 <div class="ui-infobox-content ui-corner-bottom-medium"><?php echo ReservationInfoBoxUtil::showInfoboxBefore();?></div>
</div>