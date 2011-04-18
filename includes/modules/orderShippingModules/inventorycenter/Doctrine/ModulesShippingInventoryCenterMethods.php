<?php
class ModulesShippingInventoryCenterMethods extends Doctrine_Record {
	
	public function setTableDefinition(){
		$this->setTableName('modules_shipping_inventory_center_methods');
		
		$this->hasColumn('method_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('method_status', 'string', 16, array(
			'type'          => 'string',
			'length'        => 16,
			'fixed'         => false,
			'primary'       => false,
			'default'       => 'False',
			'notnull'       => true,
			'autoincrement' => false
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
		
		$this->hasColumn('method_cost', 'float', 15, array(
			'type'          => 'float',
			'length'        => 15,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('method_default', 'integer', 2, array(
			'type'          => 'integer',
			'length'        => 2,
			'default'       => '0',
			'autoincrement' => false
		));
		
		$this->hasColumn('sort_order', 'integer', 2, array(
			'type'          => 'integer',
			'length'        => 2,
			'autoincrement' => false
		));
	}
}