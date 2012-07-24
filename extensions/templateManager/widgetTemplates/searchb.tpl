<script type="text/javascript">
	$(document).ready(function(){
		$('.searchb').css('cursor','pointer');
		$('.searchb').click(function(){
			$(this).closest('form').submit();
			return false;
		});
	});
</script>
<?php
echo $boxContent;
?>
