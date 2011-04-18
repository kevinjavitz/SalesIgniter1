<?php
class ModulesShippingUpsReservationMethodsDescription extends Doctrine_Record {

	public function setUp(){
		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'language_id');
	}

	public function setTableDefinition(){
		$this->setTableName('modules_shipping_ups_reservation_methods_description');
		
		$this->hasColumn('method_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'autoincrement' => false
		));

		$this->hasColumn('language_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'default' => '1',
		'autoincrement' => false,
		));
		
		$this->hasColumn('method_text', 'string', 999, array(
			'type'          => 'string',
			'length'        => 999,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));

	}
	public function newLanguageProcess($fromLangId, $toLangId){
		$Qdescription = Doctrine_Query::create()
		->from('ModulesShippingUpsReservationMethodsDescription')
		->where('language_id = ?', (int) $fromLangId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qdescription as $Record){
			$toTranslate = array(
				'method_text' => $Record['method_text']
			);

			EventManager::notify('ModulesShippingUpsReservationMethodsDescriptionNewLanguageProcessBeforeTranslate', $toTranslate);

			$translated = sysLanguage::translateText($toTranslate, (int) $toLangId, (int) $fromLangId);

			$newDesc = new ModulesShippingUpsReservationMethodsDescription();
			$newDesc->method_id = $Record['method_id'];
			$newDesc->language_id = (int) $toLangId;
			$newDesc->method_text = $translated['method_text'];
			$newDesc->method_details = $translated['method_details'];

			EventManager::notify('ModulesShippingUpsReservationMethodsDescriptionNewLanguageProcessBeforeSave', $newDesc);

			$newDesc->save();
		}
	}

	public function deleteLanguageProcess($langId){
		Doctrine_Query::create()
		->delete('ModulesShippingUpsReservationMethodsDescription')
		->where('language_id = ?', (int) $langId)
		->execute();
	}
}