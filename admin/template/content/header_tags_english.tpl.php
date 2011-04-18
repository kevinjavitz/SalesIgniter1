<style type="text/css">
td.HTC_Head {color: sienna; font-size: 24px; font-weight: bold; } 
td.HTC_subHead {color: sienna; font-size: 14px; } 
</style> 
<table border="0" width="100%" cellspacing="0" cellpadding="2">
     <tr>
      <td class="HTC_Head"><?php echo HEADING_TITLE_ENGLISH; ?></td>
     </tr>
     <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
     <tr>
      <td class="HTC_subHead"><?php echo TEXT_ENGLISH_TAGS; ?></td>
     </tr>
     <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
     </tr>
     
     <!-- Begin of Header Tags -->
     <tr>
      <td align="right"><?php echo tep_draw_form('header_tags', FILENAME_HEADER_TAGS_ENGLISH, '', 'post') . tep_draw_hidden_field('action', 'process'); ?></td>
       <tr>
        <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
     
         <tr>
          <td class="smallText" width="20%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_DEFAULT_TITLE; ?></td>
          <td class="smallText" ><?php echo tep_draw_input_field('main_title', tep_not_null($main_title) ? $main_title : '', 'maxlength="255", size="60"', false); ?> </td>
         <tr> 
         <tr>
          <td class="smallText" width="20%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_DEFAULT_DESCRIPTION; ?></td>
          <td class="smallText" ><?php echo tep_draw_input_field('main_desc', tep_not_null($main_desc) ? $main_desc : '', 'maxlength="255", size="60"', false); ?> </td>
         <tr> 
         <tr>
          <td class="smallText" width="20%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_DEFAULT_KEYWORDS; ?></td>
          <td class="smallText" ><?php echo tep_draw_input_field('main_keyword', tep_not_null($main_key) ? $main_key : '', 'maxlength="255", size="60"', false); ?> </td>
         <tr> 
         
         <?php for ($i = 0, $id = 0; $i < count($sections['titles']); ++$i, $id += 3) { ?>
         <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
         </tr>         
         <tr>
          <td colspan="3" ><table border="0" width="100%">
         <tr>
          <td colspan="3" class="smallText" width="20%" style="font-weight: bold; color: <?php echo getcolor($sections['titles'][$i]); ?>;"><?php echo $sections['titles'][$i]; ?></td>
          <td class="smallText">HTTA: </td>
          <td align="left"><?php echo tep_draw_checkbox_field($args['title_switch_name'][$i], '', $args['title_switch'][$i], ''); ?> </td>
          <td class="smallText">HTDA: </td>
          <td align="left"><?php echo tep_draw_checkbox_field($args['desc_switch_name'][$i], '', $args['desc_switch'][$i], ''); ?> </td>
          <td class="smallText">HTKA: </td>
          <td align="left"><?php echo tep_draw_checkbox_field($args['keyword_switch_name'][$i], '', $args['keyword_switch'][$i], ''); ?> </td>
          <td class="smallText">HTCA: </td>
          <td align="left"><?php echo tep_draw_checkbox_field($args['cat_switch_name'][$i], '', $args['cat_switch'][$i], ''); ?> </td>
         
          <td width="50%" class="smallText"> <script>document.writeln('<a style="cursor:hand" onclick="javascript:popup=window.open('
                                           + '\'<?php echo tep_href_link('header_tags_popup_help.php'); ?>\',\'popup\','
                                           + '\'scrollbars,resizable,width=520,height=550,left=50,top=50\'); popup.focus(); return false;">'
                                           + '<font color="red"><u><?php echo HEADING_TITLE_CONTROLLER_EXPLAIN; ?></u></font></a>');
         </script> </td>
     
         </tr>
          </table></td>
         </tr>
         
         <tr>
          <td colspan="3" ><table border="0" width="100%">
           <tr>
            <td width="2%">&nbsp;</td>
            <td class="smallText" width="12%"><?php echo HEADING_TITLE_CONTROLLER_TITLE; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field($id, $args['title'][$i], 'maxlength="255", size="60"', false, 300); ?> </td>
           </tr>
           <tr>
            <td width="2%">&nbsp;</td>
            <td class="smallText" width="12%"><?php echo HEADING_TITLE_CONTROLLER_DESCRIPTION; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field($id+1, $args['desc'][$i], 'maxlength="255", size="60"', false); ?> </td>
           </tr>
           <tr>
            <td width="2%">&nbsp;</td>
            <td class="smallText" width="12%"><?php echo HEADING_TITLE_CONTROLLER_KEYWORDS; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field($id+2, $args['keyword'][$i], 'maxlength="255", size="60"', false); ?> </td>
           </tr>
          </table></td>
         </tr>
         <?php } ?> 
        </table>
        </td>
       </tr>  
       <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
       </tr>
       <tr> 
        <td align="center"><?php echo (htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw() ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_ENGLISH, tep_get_all_get_params(array('action'))) .'">' . '</a>'; ?></td>
       </tr>
      </form>
      </td>
     </tr>
     <!-- end of Header Tags -->

         
    </table>