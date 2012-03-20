<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxManufacturers extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('manufacturers');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_MANUFACTURERS'));
	}

	public function show(){
		$Qmanufacturers = Doctrine_Query::create()
		->select('manufacturers_id, manufacturers_name')
		->from('Manufacturers')
		->orderBy('manufacturers_name')
		->execute();
		if ($Qmanufacturers->count() > MAX_DISPLAY_MANUFACTURERS_IN_A_LIST){
			// Display a list
			$manufacturersList = array();
			foreach($Qmanufacturers->toArray() as $manufacturer){
				$manufacturers_name = ((strlen($manufacturer['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr($manufacturer['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..' : $manufacturer['manufacturers_name']);
				if (isset($_GET['manufacturers_id']) && ($_GET['manufacturers_id'] == $manufacturer['manufacturers_id'])){
					$manufacturers_name = '<b>' . $manufacturers_name .'</b>';
				}
				$manufacturersList[] = '<a href="' . itw_app_link('manufacturers_id=' . $manufacturer['manufacturers_id'], 'index', 'default') . '">' . $manufacturers_name . '</a>';
			}

			$manufacturersList = implode('<br />', $manufacturersList);
		}elseif ($Qmanufacturers->count() > 0){
			// Display a drop-down
			$manufacturers_array = array();
			if (MAX_MANUFACTURERS_LIST < 2){
				$manufacturers_array[] = array('id' => '', 'text' => sysLanguage::get('PULL_DOWN_DEFAULT'));
			}

			foreach($Qmanufacturers->toArray() as $manufacturer){
				$manufacturers_name = ((strlen($manufacturer['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr($manufacturer['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..' : $manufacturer['manufacturers_name']);
				$manufacturers_array[] = array(
				'id' => $manufacturer['manufacturers_id'],
				'text' => $manufacturers_name
				);
			}

			$manufacturersList = tep_draw_form('manufacturers', itw_app_link(null, 'index', 'default', 'NONSSL', false), 'get') .
			tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, (isset($_GET['manufacturers_id']) ? $_GET['manufacturers_id'] : ''), 'onChange="this.form.submit();" size="' . MAX_MANUFACTURERS_LIST . '" style="width: 100%"') .
			tep_hide_session_id() .
			'</form>';
			unset($manufacturers_array);
		}

		if (isset($manufacturersList) && !empty($manufacturersList)){
			$this->setBoxContent($manufacturersList);

			return $this->draw();
		}
		return false;
	}
}
?>