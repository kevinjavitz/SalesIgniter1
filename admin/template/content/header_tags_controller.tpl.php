<style type="text/css">
td.HTC_Head {color: sienna; font-size: 24px; font-weight: bold; }
td.HTC_subHead {color: sienna; font-size: 14px; }
</style>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
     <tr>
      <td class="HTC_Head"><?php echo HEADING_TITLE_CONTROLLER; ?></td>
     </tr>
     <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
     <tr>
      <td class="HTC_subHead"><?php echo TEXT_PAGE_TAGS; ?></td>
     </tr>
     <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
     </tr>

     <!-- Begin of Header Tags - Add a Page -->
     <tr>
      <td><?php echo tep_black_line(); ?></td>
     </tr>
     <tr>
      <td class="main"><?php echo sysLanguage::get('TEXT_INFORMATION_ADD_PAGE'); ?></td>
     </tr>

     <tr>
      <td align="right"><?php echo tep_draw_form('header_tags', FILENAME_HEADER_TAGS_CONTROLLER, '', 'post') . tep_draw_hidden_field('action', 'process'); ?></td>
       <tr>
        <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">

         <tr>
          <td><table border="0" width="100%">
           <tr>
            <td class="smallText" width="10%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_PAGENAME; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field('page', tep_not_null($page) ? $page : '', 'maxlength="255", size="30"', false); ?> </td>
           <tr>
          </table></td>
         </tr>

         <tr>
          <td><table border="0" width="100%">
           <tr>
            <td class="smallText" width="13%" style="font-weight: bold;">Switches:</td>
            <td class="smallText">HTTA: </td>
            <td align="left"><?php echo tep_draw_checkbox_field('htta', '', FALSE, ''); ?> </td>
            <td class="smallText">HTDA: </td>
            <td ><?php echo tep_draw_checkbox_field('htda', '', FALSE, ''); ?> </td>
            <td class="smallText">HTKA: </td>
            <td ><?php echo tep_draw_checkbox_field('htka', '', FALSE, ''); ?> </td>
            <td class="smallText">HTCA: </td>
            <td ><?php echo tep_draw_checkbox_field('htca', '', FALSE, ''); ?> </td>
            <td width="50%" class="smallText"> <script>document.writeln('<a style="cursor:hand" onclick="javascript:popup=window.open('
                                           + '\'<?php echo tep_href_link('header_tags_popup_help.php'); ?>\',\'popup\','
                                           + '\'scrollbars,resizable,width=520,height=550,left=50,top=50\'); popup.focus(); return false;">'
                                           + '<font color="red"><u><?php echo HEADING_TITLE_CONTROLLER_EXPLAIN; ?></u></font></a>');
            </script> </td>
           </tr>
          </table></td>
         </tr>

         <tr>
          <td><table border="0" width="100%">
           <tr>
            <td class="smallText" width="10%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_TITLE; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field('title', tep_not_null($title) ? $title : '', 'maxlength="255", size="60"', false); ?> </td>
           <tr>
           <tr>
            <td class="smallText" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_DESCRIPTION; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field('desc', tep_not_null($desc) ? $desc : '', 'maxlength="255", size="60"', false); ?> </td>
           <tr>
           <tr>
            <td class="smallText" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_KEYWORDS; ?></td>
            <td class="smallText" ><?php echo tep_draw_input_field('keyword', tep_not_null($key) ? $key : '', 'maxlength="255", size="60"', false); ?> </td>
           <tr>
          </table></td>
         </tr>

       <tr>
        <td align="center"><?php echo (htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw() ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_CONTROLLER, '') .'">' . '</a>'; ?></td>
       </tr>

       <tr>
        <td><?php echo tep_black_line(); ?></td>
       </tr>

      </form>
      </td>
     </tr>
     <!-- end of Header Tags - Add a Page-->

     <!-- Begin of Header Tags - Delete a Page -->
     <tr>
      <td><?php echo tep_black_line(); ?></td>
     </tr>
     <tr>
      <td class="main"><?php echo sysLanguage::get('TEXT_INFORMATION_DELETE_PAGE'); ?></td>
     </tr>
     <tr>
      <td align="right"><?php echo tep_draw_form('header_tags_delete', FILENAME_HEADER_TAGS_CONTROLLER, '', 'post') . tep_draw_hidden_field('action_delete', 'process'); ?></td>
       <tr>
        <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
         <tr>
          <td><table border="0" width="100%">
           <tr>
            <td class="smallText" width="10%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_PAGENAME; ?></td>
            <td align="left"><?php   echo tep_draw_pull_down_menu('delete_page', $deleteArray, '', '', false);?></td>
           <tr>
          </table></td>
         </tr>
       <tr>
        <td align="center"><?php echo (htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw() ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_CONTROLLER, '') .'">' . '</a>'; ?></td>
       </tr>
       <tr>
        <td><?php echo tep_black_line(); ?></td>
       </tr>
      </form>
      </td>
     </tr>
     <!-- end of Header Tags - Delete a Page-->

     <!-- Begin of Header Tags - Auto Add Pages -->
     <tr>
      <td><?php echo tep_black_line(); ?></td>
     </tr>
     <tr>
      <td class="main"><?php echo sysLanguage::get('TEXT_INFORMATION_CHECK_PAGES'); ?></td>
     </tr>
     <tr>
      <td align="right"><?php echo tep_draw_form('header_tags_auto', FILENAME_HEADER_TAGS_CONTROLLER, '', 'post') . tep_draw_hidden_field('action_check', 'process'); ?></td>
       <tr>
        <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><table border="0" width="100%">
           <tr>
            <td class="smallText" width="10%" style="font-weight: bold;"><?php echo HEADING_TITLE_CONTROLLER_PAGENAME; ?></td>
            <td align="left"><?php   echo tep_draw_pull_down_menu('new_files', $newfiles, '', '', false);?></td>
           <tr>
          </table></td>
         </tr>
       <tr>
        <td align="center"><?php echo (htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw() ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_CONTROLLER, '') .'">' . '</a>'; ?></td>
       </tr>
       <tr>
        <td><?php echo tep_black_line(); ?></td>
       </tr>
      </form>
      </td>
     </tr>
     <!-- end of Header Tags - Auto Add Pages-->

    </table>