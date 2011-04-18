      <table border="0" width="100%" cellspacing="0" cellpadding="2">     
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
            <table border="0" width="100%" cellspacing="0" cellpadding="2" align="center">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo sysLanguage::get('NAVBAR_TITLE'); ?></td>
              </tr>
              <tr class="dataTableRow">
                <td align="left" class="dataTableContent"><?php echo sysLanguage::get('TEXT_MAIN'); ?></td>
              </tr>
              <tr class="dataTableRow">
                <td align="left"><?php echo '&nbsp;' . htmlBase::newElement('button')->usePreset('back')->setHref(tep_href_link(FILENAME_DEFAULT))->draw() . '&nbsp;'; ?></td>
              </tr>              
            </table>
        </td>
      </tr>
    </table>