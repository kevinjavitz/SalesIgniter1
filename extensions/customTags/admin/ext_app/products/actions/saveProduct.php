<?php
	$GroupsToProducts =& $Product->ProductsCustomFieldsGroupsToProducts;
	$FieldsToProducts =& $Product->ProductsCustomFieldsToProducts;
		
	$GroupsToProducts->delete();
	$FieldsToProducts->delete();
		
	if (isset($_POST['products_custom_fields_group']) && $_POST['products_custom_fields_group'] != 'null'){
		if (isset($_POST['fields'])){
			$GroupsToProducts[]->group_id = $_POST['products_custom_fields_group'];
			foreach($_POST['fields'] as $fID => $val){
				$fieldValue = $val;
				if (isset($_FILES['fields_' . $fID])){
					$fieldUpload = new upload('fields_' . $fID);
					$fieldUpload->set_extensions(array('jpg', 'gif', 'png'));
					$fieldUpload->set_destination(DIR_FS_CATALOG_IMAGES);
					if ($fieldUpload->parse() && $fieldUpload->save()) {
						$fieldValue = $fieldUpload->filename;
					}else{
						$fieldValue = $val;
					}
				}
					
				$QfieldType = Doctrine_Query::create()
				->select('f.input_type')
				->from('ProductsCustomFields f')
				->where('f.field_id = ?', $fID)
				->execute(array(), Doctrine::HYDRATE_ARRAY);
					
				$FieldsToProducts[$fID]->field_id = $fID;
				$FieldsToProducts[$fID]->field_type = $QfieldType[0]['input_type'];
				$FieldsToProducts[$fID]->value = $fieldValue;
			}
		}
	}
	
	$Product->save();
?>