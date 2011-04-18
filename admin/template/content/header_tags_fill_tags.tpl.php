<style type="text/css">
td.HTC_Head {color: sienna; font-size: 24px; font-weight: bold; } 
td.HTC_subHead {color: sienna; font-size: 14px; } 
</style>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
     <tr>
      <td class="HTC_Head"><?php echo HEADING_TITLE_FILL_TAGS; ?></td>
     </tr>
     <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
     <tr>
      <td class="HTC_subHead"><?php echo TEXT_FILL_TAGS; ?></td>
     </tr>
     
     <!-- Begin of Header Tags -->      
     
     <tr>
      <td align="right"><?php echo tep_draw_form('header_tags', FILENAME_HEADER_TAGS_FILL_TAGS, '', 'post') . tep_draw_hidden_field('action', 'process'); ?></td>
       <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
     </tr>
     <tr>
      <td><table width="100%" border="0">
       <tr>
        <td class="main" width="12%"><?php echo TEXT_LANGUAGE;?>Language:&nbsp;</td>
        <td><?php echo tep_draw_pull_down_menu('fill_language', $languages_array, $langID);?></td>
       </tr>
      </table> 

      <table width="80%" border="0">
       <tr> 
        <td class="main"><?php echo TEXT_INFO_META_TAGS;?></td>
        <td align=left><INPUT TYPE="radio" NAME="group4" VALUE="fillMetaDesc_yes"<?php echo $checkedMetaDesc['yes']; ?>> <?php echo sysLanguage::get('TEXT_YES');?></td>
        <td align=left><INPUT TYPE="radio" NAME="group4" VALUE="fillmetaDesc_no"<?php echo $checkedMetaDesc['no']; ?>> <?php echo sysLanguage::get('TEXT_NO');?></td>
        <td align="right" class="main"><?php echo 'Limit to '. tep_draw_input_field('fillMetaDescrlength', '', 'maxlength="255", size="5"', false) . ' ' . TEXT_CHARACTERS; ?> </td>
       </tr>
      </table></td> 
     </tr>     
       <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
       </tr>
       
       <tr>
        <td><table border="0" width="50%">
         <tr class="smallText">
          <th><?php echo HEADING_TITLE_CONTROLLER_CATEGORIES; ?></th>
          <th><?php echo HEADING_TITLE_CONTROLLER_MANUFACTURERS; ?></th>          
          <th><?php echo HEADING_TITLE_CONTROLLER_PRODUCTS; ?></th>
         </tr> 
         <tr class="smallText">          
          <td align=left><INPUT TYPE="radio" NAME="group1" VALUE="none" <?php echo $checkedCats['none']; ?>> <?php echo HEADING_TITLE_CONTROLLER_SKIPALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group2" VALUE="none" <?php echo $checkedManuf['none']; ?>> <?php echo HEADING_TITLE_CONTROLLER_SKIPALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group3" VALUE="none" <?php echo $checkedProds['none']; ?>> <?php echo HEADING_TITLE_CONTROLLER_SKIPALL; ?></td>
         </tr>
         <tr class="smallText"> 
          <td align=left><INPUT TYPE="radio" NAME="group1" VALUE="empty"<?php echo $checkedCats['empty']; ?> > <?php echo HEADING_TITLE_CONTROLLER_FILLONLY; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group2" VALUE="empty" <?php echo $checkedManuf['empty']; ?>> <?php echo HEADING_TITLE_CONTROLLER_FILLONLY; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group3" VALUE="empty" <?php echo $checkedProds['empty']; ?>> <?php echo HEADING_TITLE_CONTROLLER_FILLONLY; ?></td>
         </tr>
         <tr class="smallText"> 
          <td align=left><INPUT TYPE="radio" NAME="group1" VALUE="full" <?php echo $checkedCats['full']; ?>> <?php echo HEADING_TITLE_CONTROLLER_FILLALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group2" VALUE="full" <?php echo $checkedManuf['full']; ?>> <?php echo HEADING_TITLE_CONTROLLER_FILLALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group3" VALUE="full" <?php echo $checkedProds['full']; ?>> <?php echo HEADING_TITLE_CONTROLLER_FILLALL; ?></td>
         </tr>
         <tr class="smallText"> 
          <td align=left><INPUT TYPE="radio" NAME="group1" VALUE="clear" <?php echo $checkedCats['clear']; ?>> <?php echo HEADING_TITLE_CONTROLLER_CLEARALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group2" VALUE="clear" <?php echo $checkedManuf['clear']; ?>> <?php echo HEADING_TITLE_CONTROLLER_CLEARALL; ?></td>
          <td align=left><INPUT TYPE="radio" NAME="group3" VALUE="clear" <?php echo $checkedProds['clear']; ?>> <?php echo HEADING_TITLE_CONTROLLER_CLEARALL; ?></td>
         </tr>
        </table></td>
       </tr> 
       
       <tr>
        <td><table border="0" width="40%">
         <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
         </tr>
         <tr> 
          <td align="center"><?php echo (htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw() ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_ENGLISH, tep_get_all_get_params(array('action'))) .'">' . '</a>'; ?></td>
         </tr>
         <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
         </tr>         
         <?php if (tep_not_null($updateTextCat)) { ?>
          <tr>
           <td class="HTC_subHead"><?php echo $updateTextCat; ?></td>
          </tr> 
          <?php }  
           if (tep_not_null($updateTextManuf)) { ?>
          <tr>
           <td class="HTC_subHead"><?php echo $updateTextManuf; ?></td>
          </tr>
         <?php } 
           if (tep_not_null($updateTextProd)) { ?>
          <tr>
           <td class="HTC_subHead"><?php echo $updateTextProd; ?></td>
          </tr>
         <?php } ?> 
        </table></td>
       </tr>
      </form>
      </td>
     </tr>
     <!-- end of Header Tags -->

         
    </table>