<?php
/*
	Banner Manager Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_imageRot extends ExtensionBase {
	
	public function __construct(){
		parent::__construct('imageRot');
	}
	
	public function init(){
		if ($this->isEnabled() === false) return;
		//update_banners();//change_status to published or expired
		EventManager::attachEvents(array(
				'DataExportFullQueryFileLayoutHeader',
				'DataExportFullQueryBeforeExecute',
				'DataImportBeforeSave'
			), null, $this);
	}

	public function DataExportFullQueryFileLayoutHeader(&$dataExport){
		$dataExport->setHeaders(array(
			'v_products_banner_groups'
		));
	}

	public function DataExportFullQueryBeforeExecute(&$QfileLayout){
		 $QfileLayout->addSelect('(SELECT group_concat(bmp2c.banner_group_id)
		                FROM BannerManagerProductsToGroups bmp2c
		                WHERE bmp2c.products_id = p.products_id) as v_products_banner_groups'
		 );
	}

	public function DataImportBeforeSave(&$items, &$Product){
		if (!empty($items['v_products_banner_groups'])){
			$ProductsToGroups =& $Product->BannerManagerProductsToGroups;
			$ProductsToGroups->delete();
			$productsBannerGroups = explode(',', $items['v_products_banner_groups']);
			foreach($productsBannerGroups as $groupId){
				$ProductsToGroups[]->banner_group_id = $groupId;
			}
			$Product->save();
		}
	}

	public function updateExpires(){
		$datenow = strtotime(date("Y-m-d"));

		if(strtotime($this->banners_date_scheduled)<=$datenow && strtotime($this->banners_date_scheduled) != strtotime('0000-00-00 00:00:00')){
			$this->banners_status = '2';//running
			$this->banners_date_status_changed = date("Y-m-d");
		}

		if(strtotime($this->banners_expires_date)<$datenow && strtotime($this->banners_expires_date) != strtotime('0000-00-00 00:00:00') && $this->banners_status == '2' ){
			$this->banners_status = '3';//expired
			$this->banners_date_status_changed = date("Y-m-d");
		}

		if($this->banners_views > $this->banners_expires_views && $this->banners_expires_views != 0 && $this->banners_status == '2'){
			$this->banners_status = '3';//expired
			$this->banners_date_status_changed = date("Y-m-d");
		}
		if($this->banners_clicks > $this->banners_expires_clicks && $this->banners_expires_clicks != 0 && $this->banners_status == '2'){
			$this->banners_status = '3';//expired
			$this->banners_date_status_changed = date("Y-m-d");
		}

	}


	public function showBannerGroup($group,$isnotName, $settings = null){

		require_once(sysConfig::getDirFsCatalog() . 'includes/classes/template.php');
		$templateFile = 'banner.tpl';
		$templateDir = dirname(__FILE__) . '/catalog/ext_app/banners/template/';
		if (is_null($settings) === false && isset($settings['template_file'])){
			$templateFile = $settings['template_file'];
		}
		if (is_null($settings) === false && isset($settings['template_dir'])){
			$templateDir = $settings['template_dir'];
		}
		$bannerTemplate = new Template($templateFile, $templateDir);

		$datenow = date("Y-m-d H:i:s");

		Doctrine_Query::create()
			->update('BannerManagerBanners b')
			//->leftJoin('BannerManagerBannersToGroups')
			->set('banners_status','3')
			->where('banners_expires_date >?',"0000-00-00 00:00:00")
			->andWhere('banners_expires_date<?',$datenow)
			->orWhere('banners_expires_views<banners_views AND banners_expires_views>0')    
			->orWhere('banners_expires_clicks<banners_clicks AND banners_expires_clicks>0')
			->execute();
		if($isnotName){
			$Group = Doctrine_Query::create()
					->select('g.*')
					->from('BannerManagerGroups g')
					->where('g.banner_group_id = ?', $group)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		}else{
			$Group = Doctrine_Query::create()
					->select('g.*')
					->from('BannerManagerGroups g')
					->where('g.banner_group_name = ?', $group)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		}

		$Banners =  Doctrine_Query::create()
				->select('b.*')
				->from('BannerManagerBanners b')
				->leftJoin('b.BannerManagerBannersToGroups g')
				->where('g.banner_group_id = ?', $Group[0]['banner_group_id'])
				->andWhere('b.banners_status = 2')
				->orderBy('rand()')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$bannerTemplate->setVars(array(
			'bannerG' => $Group,
			'bannerD' => $Banners,
			'groupID' => $group
		));

		return $bannerTemplate->parse();
	}

}

if (!function_exists('getFlashMovie')){
 function getFlashMovie($moviePath, $movieDesc, $w, $h){
    $movie = '<!--[if !IE]> -->';
    $movie .= '<object type="application/x-shockwave-flash" data="'.$moviePath.'" width="' . $w . '" height="' . $h . '">';
    $movie .= '<!-- <![endif]-->';
    $movie .= '<!--[if IE]>';
    $movie .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="' . $w . '" height="' . $h . '"';
    $movie .= '   codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0">';
    $movie .= '   <param name="movie" value="'.$moviePath.'" />';
    $movie .= '<!--><!--dgx-->';
    $movie .=    '<param name="loop" value="true" /> ';
    $movie .=    '<param name="menu" value="false" />';
    $movie .=	'<param name="wmode" value="transparent">';
    $movie .=   '<p>'.$movieDesc.'</p>';
    $movie .= '</object> ';
    $movie .= '<!-- <![endif]-->';

    return $movie;
}
}

if (!function_exists('tep_get_group_tree_list')){
function tep_get_group_tree_list($checked = false, $include_itself = true) {

          if (!is_array($checked)){
              $checked = array();
          }
          $catList = '<ul class="catListingUL">';

		$Qgroups = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc("select * from banner_manager_groups");
          foreach ($Qgroups as $groups) {
              $catList .= '<li>' . tep_draw_checkbox_field('groups[]', $groups['banner_group_id'], (in_array($groups['banner_group_id'], $checked)), 'id="catCheckbox_' . $groups['banner_group_id'] . '"') . '<label for="catCheckbox_' . $groups['banner_group_id'] . '">' . $groups['banner_group_name'] . '</label></li>';
          }
          $catList .= '</ul>';


    return $catList;
}
}

if (!function_exists('tep_set_banners_status')){
function tep_set_banners_status($banner_id, $status) {
		$success = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->exec("update banner_manager_banners set banners_status = " . $status . " where banners_id = '" . (int)$banner_id . "'");
		return $success;
}
}
if(!function_exists('tep_friendly_seo_url')){
	function tep_friendly_seo_url($string){
		$string = preg_replace("`\[.*\]`U","",$string);
		$string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$string);
		$string = htmlentities($string, ENT_COMPAT, 'utf-8');
		$string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $string );
		$string = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $string);
		return strtolower(trim($string, '-'));
	}
}
?>