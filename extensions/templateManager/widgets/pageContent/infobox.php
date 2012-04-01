<?php
class InfoBoxPageContent extends InfoBoxAbstract {
	
	public function __construct(){
		$this->init('pageContent', 'pageContent');
	}

	public function show(){
		global $App, $Template, $pageContent;
		/* @TODO: Make This Work
		$templateDir = sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir');

		$pageContent = new Template('pageContent.tpl', $templateDir);

		$checkFiles = array(
			$templateDir . '/applications/' . $App->getAppName() . '/' . $App->getPageName() . '.php',
			sysConfig::getDirFsCatalog() . '/applications/' . $App->getAppName() . '/pages/' . $App->getPageName() . '.php'
		);

		$requireFile = false;
		foreach($checkFiles as $filePath){
			if (file_exists($filePath)){
				$requireFile = $filePath;
				break;
			}
		}

		if ($requireFile !== false){
			require($requireFile);

			foreach($pageContent->getVars() as $k => $v){
				$this->setTemplateVar($k, $v);
			}
		}
*/
		/* Page Content is the only widget that parses directly into its tpl file */
		$PageContent = $Template->getVar('pageContent');
		$PageTitle = $pageContent->getVar('pageTitle');
		$PageForm = $pageContent->getVar('pageForm');
		$PageButtons = $pageContent->getVar('pageButtons');
		$boxWidget = new stdClass();
		$boxWidget->template_file = $PageContent->layoutFile;
		$this->setWidgetProperties($boxWidget);
		$this->setBoxHeading($PageTitle);
		if(isset($PageForm)){
			$this->setBoxForm($PageForm);
		}
		if(isset($PageButtons)){
			$this->setBoxButtons($PageButtons);
		}
		$this->setBoxContent($PageContent->getVar('pageContent'));
		
		return $this->draw();
	}
}
?>