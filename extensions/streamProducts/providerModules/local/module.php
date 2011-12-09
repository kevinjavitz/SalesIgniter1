<?php
class StreamProviderLocal extends StreamProviderModule {

	public function __construct($config = false) {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Local');
		$this->setDescription('Streams are stored on this server');
		
		$this->init('local');
		
		if ($config !== false && is_array($config)){
			$this->setProviderConfig($config);
		}
	}
	
	public function getStorageFolder(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_LOCAL_FOLDER');
	}
	
	public function getFlowplayerConfig($sInfo){
		$previewFile = $sInfo['file_name'];
		$ext = substr($previewFile, strpos($previewFile, '.')+1);
		$movieName = 'preview.' . $ext;
		$movieName = $sInfo['file_name'];
		$params = '';
		if(isset($sInfo['oID']) && $sInfo['oID'] > 0){
			$params = $sInfo['oID'] . '/' . $sInfo['opID'] . '/';
		}
		$link = itw_app_link(null, 'pullStream', $sInfo['products_id'] . '/' . $params . $movieName);
		$link = str_replace('pullStream/', 'pullStream.php/', $link);
		
		return array(
/*			'key' => 'Commercial Key',
			'logo' => array(
				'url' => 'streamer/flowplayer/images/logo.png',
				'top' => 20,
				'right' => 20,
				'opacity' => 0.4,
				'displayTime' => 0,
				'linkUrl' => itw_app_link(null, 'index', 'default')
			),*/
			'plugins' => array(
				'controls' => array(
					'url' => 'streamer/flowplayer/flowplayer.controls-3.2.3.swf'
				)
			),
			'clip' => array(
				'url' => $link,
				'autoPlay' => false,
				'autoBuffering' => false
			)
		);
	}
}
?>