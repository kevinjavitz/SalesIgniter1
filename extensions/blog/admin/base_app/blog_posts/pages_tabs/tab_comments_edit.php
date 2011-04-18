<?php

$CommentStatusEnabled = htmlBase::newElement('radio')
	->setName('comment_status')
	->setLabel('Published')
	->setValue('1');

$CommentStatusDisabled = htmlBase::newElement('radio')
	->setName('comment_status')
	->setLabel('Not Published')
	->setValue('0');

	$CommentDate = htmlBase::newElement('input')
	->setName('comment_date')
	->addClass('useDatepicker');
        if (isset($Comment)){

            if ($Comment->BlogComments['comment_status'] == '1'){
			$CommentStatusEnabled->setChecked(true);
		}else{
             if ($Comment->BlogComments['comment_status'] == '0')
			    $CommentStatusDisabled->setChecked(true);
		}
            $CommentDate->setValue($Comment->BlogComments['comment_date']);
    }
?>
<table cellpadding="0" cellspacing="0" border="0">
      <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_COMMENT_STATUS'); ?></td>
   <td class="main"><?php echo $CommentStatusEnabled->draw() . $CommentStatusDisabled->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_COMMENT_DATE'); ?><br><small>(YYYY-MM-DD)</small></td>
   <td class="main"><?php echo $CommentDate->draw(); ?></td>
  </tr>
      <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
</table>
 <?php 
		$CommentAuthor = htmlBase::newElement('input')
		->setName('comment_author');

		$CommentEmail = htmlBase::newElement('input')
		->setName('comment_email');
		
		$CommentText = htmlBase::newElement('ck_editor')
		->setName('comment_text');


		
		if (isset($Comment)){
			$CommentAuthor->setValue(stripslashes($Comment->BlogComments['comment_author']));
			$CommentEmail->setValue(stripslashes($Comment->BlogComments['comment_email']));
			$CommentText->html(stripslashes($Comment->BlogComments['comment_text']));

		}
?>

 <table cellpadding="0" cellspacing="0" border="0">

  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_COMMENT_AUTHOR'); ?></td>
   <td class="main"><?php echo $CommentAuthor->draw(); ?></td>
  </tr>
	  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_COMMENT_EMAIL'); ?></td>
   <td class="main"><?php echo $CommentEmail->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_COMMENT_TEXT'); ?></td>
   <td class="main"><?php echo $CommentText->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

 </table>
