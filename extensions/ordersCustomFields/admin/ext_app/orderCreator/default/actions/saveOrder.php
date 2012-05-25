<?php
if ($success === true){
	foreach($_POST['orders_custom_field'] as $fieldId => $val){
		if (is_array($val)) continue;

		$Qfield = Doctrine_Query::create()
			->select('f.field_id, f.input_type, fd.field_name')
			->from('OrdersCustomFields f')
			->leftJoin('f.OrdersCustomFieldsDescription fd')
			->where('f.field_id = ?', $fieldId)
			->andWhere('fd.language_id = ?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if ($Qfield[0]['input_type'] == 'select_other' && $val == 'Other'){
			if (isset($_POST['orders_custom_field_other'][$fieldId])){
				if (!empty($_POST['orders_custom_field_other'][$fieldId])){
					$val = $_POST['orders_custom_field_other'][$fieldId];
				}
			}
		}

		$QCustomField = Doctrine_Query::create()
		->from('OrdersCustomFieldsToOrders')
		->where('orders_id = ?', $NewOrder->orders_id)
		->andWhere('field_id = ?', $Qfield[0]['field_id'])
		->andWhere('field_type = ?', $Qfield[0]['input_type'])
		->fetchOne();
		if($QCustomField){
			$QCustomField->value = $val;
			$QCustomField->save();
		}else{
			$field = new OrdersCustomFieldsToOrders();
			$field->value = $val;
			$field->orders_id = $NewOrder->orders_id;
			$field->field_id = $Qfield[0]['field_id'];
			$field->field_label = $Qfield[0]['OrdersCustomFieldsDescription'][0]['field_name'];
			$field->field_type = $Qfield[0]['input_type'];
			$field->save();
		}
	}
}
?>