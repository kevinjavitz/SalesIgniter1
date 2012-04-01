<?php
class InfoBoxBlogArchives extends InfoBoxAbstract {

	public function __construct(){

		$this->init('blogArchives', __DIR__);
		$this->enabled = true;

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_BLOG_ARCHIVES'));
	}

	public function getArch(){
		global $appExtension;
		$blog = $appExtension->getExtension('blog');
		$cats = $blog->getArchives();

		$menuContainer = htmlBase::newElement('div')
		->setId('blogarchivesModuleMenu');

		$ulElement = htmlBase::newElement('list');
		foreach ($cats as $cat){
			$childLinkEl = htmlBase::newElement('a')
			->addClass('blogInfoboxLink')
			->html('<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet" style="vertical-align:middle;"></span><span class="ui-categories-text" style="vertical-align:middle;">'.date("F", mktime(0, 0, 0, $cat['month'], 10)) . '-' . $cat['year'])
			->setHref(itw_app_link('appExt=blog', 'show_archive', date("F", mktime(0, 0, 0, $cat['month'], 10)) . '-' . $cat['year'] . ''));
			$liElement = htmlBase::newElement('li')
			->append($childLinkEl);

			$ulElement->addItemObj($liElement);
		}

		$menuContainer->append($ulElement);
		return $menuContainer->draw();
	}

	public function show(){
		if ($this->enabled === false) return;

		$this->setBoxContent($this->getArch());

		return $this->draw();
	}
}
?>