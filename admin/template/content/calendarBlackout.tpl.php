<script language="JavaScript" src="../ext/jQuery/plugins/ui/ui.datepicker.js"></script>
<script>
  $(document).ready(function (){
      $('.datePicker').datepicker({
          dateFormat: 'yy-mm-dd'
      });
  });
</script>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><form name="blackout" action="<?php echo tep_href_link('calendarBlackout.php', 'action=save');?>" method="post"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><fieldset>
             <legend><?php echo LEGEND_BLACKOUT_DAYS;?></legend>
             <?php
               $days = array(
                   'Sun' => 'Sunday',
                   'Mon' => 'Monday',
                   'Tue' => 'Tuesday',
                   'Wed' => 'Wednesday',
                   'Thu' => 'Thursday',
                   'Fri' => 'Friday',
                   'Sat' => 'Saturday'
               );
             ?>
             <table cellpadding="2" cellspacing="0" border="0">
             <?php
              foreach($days as $val => $text){
             ?>
              <tr>
               <td><?php echo tep_draw_checkbox_field('blackoutDays[]', $val, in_array($val, explode(',', CALENDAR_DISABLED_DAYS)));?></td>
               <td class="main"><?php echo $text;?></td>
              </tr>
             <?php
              }
             ?>              
             </table>
            </fieldset></td>
          </tr>
          <tr>
            <td valign="top"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10');?></td>
          </tr>
          <tr>
            <td valign="top"><fieldset>
             <legend><?php echo LEGEND_BLACKOUT_DATES;?></legend>
             <?php
              $Qdates = tep_db_query('select * from blackout_dates');
              while($dates = tep_db_fetch_array($Qdates)){
                  $dArr[] = array(
                      'from'    => $dates['date_from'],
                      'to'      => $dates['date_to'],
                      'repeats' => $dates['repeats']
                  );
              }
             ?>
             <table cellpadding="2" cellspacing="0" border="0">
              <tr>
               <td class="main"><?php echo TABLE_HEADING_REPEATS;?></td>
               <td class="main"><?php echo TABLE_HEADING_DATE_FROM;?></td>
               <td class="main"><?php echo TABLE_HEADING_DATE_TO;?></td>
              </tr>
             <?php
               for($i=0; $i<16; $i++){
                   $repeats = false;
                   $from = '';
                   $to = '';
                   if (isset($dArr[$i])){
                       $repeats = ($dArr[$i]['repeats'] == '1');
                       $from = $dArr[$i]['from'];
                       $to = $dArr[$i]['to'];
                   }
             ?>
              <tr>
               <td><?php echo tep_draw_checkbox_field('repeats[' . $i . ']', '1', $repeats);?></td>
               <td><?php echo tep_draw_input_field('from[' . $i . ']', $from, 'class="datePicker"');?></td>
               <td><?php echo tep_draw_input_field('to[' . $i . ']', $to, 'class="datePicker"');?></td>
              </tr>
             <?php
               }
             ?>
             </table>
            </fieldset></td>
          </tr>
          <tr>
           <td align="center"><br><?php echo htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw();?></td>
          </tr>
        </table></form></td>
      </tr>
    </table>