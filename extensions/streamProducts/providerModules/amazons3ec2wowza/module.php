<?php
class StreamProviderAmazons3ec2wowza extends StreamProviderModule {

	public function __construct($config = false) {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Amazon S3 EC2 With Wowza Media Server');
		$this->setDescription('Streams are stored in Amazon S3 and streamed through wowza media server running on an Amazon EC2 server instance');
		
		$this->init('amazons3ec2wowza');
		
		if ($config !== false && is_array($config)){
			$this->setProviderConfig($config);
		}
	}
	
	public function getStorageFolder(){
		$folder = $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONS3EC2WOWZA_FOLDER');
		if (substr($folder, -1) != '/' && substr($folder, -1) != '\\'){
			$folder .= '/';
		}
		return $folder;
	}
	
	public function getWowzaPort(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONS3EC2WOWZA_PORT');
	}
	
	public function getWowzaServer(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONS3EC2WOWZA_INSTANCE');
	}
		
	public function getStreamTypes(){
		return array(
			'rtmp',
			'rtmpe',
			'rtmpt'
		);
	}
	
	public function getFlowplayerConfig($sInfo){
		$protocol = $sInfo['stream_type'];
		$wowzaServer = $this->getWowzaServer();
		$wowzaPort = $this->getWowzaPort();
		$filePath = $this->getStorageFolder() . $sInfo['file_name'];
		$fileType = 'mp4';
		
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
				),
				$protocol => array(
					'url' => 'streamer/flowplayer/flowplayer.rtmp-3.2.3.swf',
					'netConnectionUrl' => $protocol . '://' . $wowzaServer . ':' . $wowzaPort . '/vods3'
				)
			),
			'clip' => array(
				'url' => $fileType . ':amazons3/' . $filePath,
				'provider' => $protocol,
				'autoPlay' => false,
				'autoBuffering' => false
			)
		);
	}
}
?>