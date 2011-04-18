<?php
 if ($canView === true){
     if ($multipleFiles === true){
         $fileNum = 1;
         while($file = tep_db_fetch_array($Qfile)){
             if ($file['type'] == 'stream'){
                 $text = 'Click here to view file: ';
             }else{
                 $text = 'Click here to download file: ';
             }
             echo '<a href="' . itw_app_link('back=m&fID=' . $file['upload_id'] . '&pID=' . (int)$_GET['pID'], 'viewStream', 'default') . '">' . $text . $file['display_name'] . '</a><br><br>';
         }
     }else{
?>
<script type="text/javascript" src="streamer/flowplayer/flowplayer-3.2.4.min.js"></script>
<center>  <div style="display:block;width:350px;height:270px" href="<?php echo sysConfig::getDirWsCatalog();?>pullStream/<?php echo implode('/', $getVars);?>" id="player"></div>
		<script>
			$f("player", "streamer/flowplayer/flowplayer-3.2.5.swf", {
			    plugins: {
			        controls: {
			            url: 'streamer/flowplayer/flowplayer.controls-3.2.3.swf'
			        }
			    },
			    clip: {
			        autoPlay: false,
			        autoBuffering: false
			    }
			});
		</script>
<?php /*
<!--<center><script type='text/javascript' src='streamer/swfobject.js'></script>

  <div id='mediaspace'>This div will be replaced</div>

  <script type='text/javascript'>
  var s1 = new SWFObject('streamer/player.swf','ply','470','320','9','#ffffff');
  s1.addParam('allowfullscreen','true');
  s1.addParam('allowscriptaccess','always');
  s1.addParam('wmode','opaque');
 // s1.addParam('flashvars','file=<?php echo HTTP_SERVER . DIR_WS_CATALOG;?>streamer/video.flv');
  s1.addVariable('file', encodeURIComponent("<?php echo tep_href_link('pullStream.php', implode('&', $getVars));?>"))
  s1.write('mediaspace');
</script></center>-->
*/ ?>
<?php
         Session::remove('viewAllowed');
     }
 }else{
     echo '<br><center>View Not Allowed</center><br>';
 }
?>
<br>
 <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
  <tr class="infoBoxContents">
   <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
     <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
     <td class="main" align="right"><?php
      if (isset($_GET['back'])){
          echo htmlBase::newElement('button')->usePreset('back')->setHref(itw_app_link('pID=' . (int)$_GET['pID'], 'account', 'downloads'))->draw();
          Session::set('viewAllowed', (int)$_GET['pID']);
      }elseif (isset($_GET['oID'])){
          echo htmlBase::newElement('button')->usePreset('back')->setHref(itw_app_link(null, 'account', 'downloads'))->draw();
      }else{
          echo htmlBase::newElement('button')->usePreset('back')->setHref(itw_app_link('products_id=' . $_GET['pID'], 'product', 'info'))->draw();
      }
     ?></td>
     <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
    </tr>
   </table></td>
  </tr>
 </table>