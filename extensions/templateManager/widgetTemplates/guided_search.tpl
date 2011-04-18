<script type="text/javascript">
	$(document).ready(function (){
		$("form[name$='quick_find']").css('display','none');
		$('.guidedHeader').css('display','none');
			$('#searchInfoBox').each(function (){
				var $form = $('form[name=guided_search]', this);

				var newHtml = '<div class="guidedSearch">' +
					'<div class="guidedSearchBreadCrumb">Chosen:</div>' +
					'<div class="guidedSearchHeading"></div>' +
					'<div class="guidedSearchListing">';
				$(this).find('ul').each(function (){
					var heading = $(this).prev().html();

					newHtml = newHtml + '<ul title="' + heading + '">';

					$(this).find('li').each(function (){
						if (!$(this).hasClass('searchShowMoreLink')){
							newHtml = newHtml + '<li class="ui-corner-all" data-url_param="' + $(this).find('a').attr('data-url_param') + '"><span>' + $(this).find('a').html() + '</span></li>';
						}
					});

					newHtml = newHtml + '</ul>';
				});
				newHtml = newHtml +
					'</div>' +
					'<div class="guidedSearchButtonBar"><button class="resetButton" type="button"><span>Reset</span></button><button class="executeButton" type="submit"><span>Execute</span></button></div>' +
				'</div>';

				$form.html(newHtml);
				$form.find('button').button();
				$form.find('.resetButton').click(function (){
					$form.find('.main').remove();
					$form.find('ul').each(function (i, el){
						if (i == 0){
							$('.guidedSearchHeading').html($(this).attr('title'));
							$(this).css({
								left: 5
							}).show();

							$('.guidedSearchListing').css({
								height: $(this).height()
							});
						}else{
							$(this).css({
								position: 'absolute',
								left: ($(this).parent().width() + 10)
							}).hide();
						}
					});
				});

				$form.find('ul').each(function (){
					$(this).css({
						position: 'absolute',
						left: ($(this).parent().width() + 10)
					}).hide();
				});

				var allUls = $form.find('ul');
				allUls.each(function (i, el){
					if (i == 0){
						$('.guidedSearchHeading').html($(this).attr('title'));
						$(this).css({
							left: 5
						}).show();

						$('.guidedSearchListing').css({
							height: $(this).height()
						});
					}

					$(this).find('li').each(function (){
						$(this).mouseover(function (){
							$(this).addClass('ui-state-hover');
							this.style.cursor = 'pointer';
						}).mouseout(function (){
							$(this).removeClass('ui-state-hover');
							this.style.cursor = 'default';
						}).click(function (){
							if (i < (allUls.size() - 1)){
								$(this).parent().animate({
									left: -($(this).parent().width() + 10)
								}, 'slow').hide();

								$(this).parent().next().animate({
									left: 5
								}, 'slow').show();

								$('.guidedSearchListing').css({
									height: $(this).parent().next().height()
								});
							}

							if (!this.parentNode.clicked){
								this.parentNode.clicked = true;
								var urlParamObj = {};
								var urlParams = $(this).attr('data-url_param');
								var hiddenFields = '';
								$.each(urlParams.split('&'), function (i, param){
									var parsed = param.split('=');
									hiddenFields = hiddenFields + '<input type="hidden" name="' + parsed[0] + '" value="' + parsed[1] + '" />';
								});

								$('.guidedSearchBreadCrumb').append('<span class="main"> &raquo; ' + $(this).html() + hiddenFields + '</span>');
								$('.guidedSearchHeading').html($(this).parent().next().attr('title'));
							}
						});
					});
				});
			});
	});

</script>
<div <?php echo (isset($box_id) ? 'id="' . $box_id . '" ' : '');?>class="ui-widget ui-widget-content ui-infobox ui-corner-all-medium">
 <div class="ui-widget-header ui-infobox-header ui-corner-all">
  <div class="ui-infobox-header-text"><?php echo $boxHeading;?></div>
 </div>
 <div class="ui-infobox-content ui-corner-bottom-medium"><?php echo $boxContent;?></div>
</div>