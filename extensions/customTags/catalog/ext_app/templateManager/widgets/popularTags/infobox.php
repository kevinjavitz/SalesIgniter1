<?php
class InfoBoxPopularTags extends InfoBoxAbstract {
	
	public function __construct(){
		global $App;
		$this->init('popularTags', __DIR__);
	}


	
	public function show(){
		global $appExtension;

		$this->setBoxContent('');
		
		return $this->draw();
	}
}
?>