<?php
class InfoBoxBlogPostTile extends InfoBoxAbstract {
	
	public function __construct(){
		$this->init('blogPostTitle', 'blog');
		$this->enabled = true;
		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_BLOG_CATEGORIES'));

	}



	
	public function show(){
		
		if ($this->enabled === false) return;
		$htmlTitle = '';
		$this->setBoxContent($htmlTitle);
		
		return $this->draw();
	}
}
?>