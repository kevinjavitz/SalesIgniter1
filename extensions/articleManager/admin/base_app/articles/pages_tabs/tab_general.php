<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_ARTICLES_STATUS'); ?></td>
		<td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('articles_status', '0', $out_status) . '&nbsp;' . sysLanguage::get('TEXT_ARTICLE_NOT_AVAILABLE') . '&nbsp;' . tep_draw_radio_field('articles_status', '1', $in_status) . '&nbsp;' . sysLanguage::get('TEXT_ARTICLE_AVAILABLE'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_ARTICLES_DATE_AVAILABLE'); ?><br><small>(YYYY-MM-DD)</small></td>
		<td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . htmlBase::newElement('input')->setName('articles_date_available')->addClass('useDatepicker')->val($Article->articles_date_available)->draw(); ?></td>
	</tr>
</table>