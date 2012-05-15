<?php
class dataAccessQueryBuilder {
	
	public function __construct(){
		$this->query = null;
		$this->select = array();
		$this->from = array();
		$this->where = array();
		$this->whereIn = array();
		$this->andWhere = array();
		$this->orWhere = array();
		$this->andWhereIn = array();
		$this->orWhereIn = array();
		$this->leftJoin = array();
		$this->innerJoin = array();
	}
	
	public function select(){
	}
	
	public function addSelect(){
	}
	
	public function from(){
	}
	
	public function addFrom(){
	}
	
	public function where(){
	}
	
	public function whereIn(){
	}
	
	public function andWhere(){
	}
	
	public function orWhere(){
	}
	
	public function andWhereIn(){
	}
	
	public function leftJoin(){
	}
	
	public function innerJoin(){
	}
}

abstract class dataAccessRecord {

	public function getProps(){
		$Reflection = new ReflectionClass($this);
		$props = $Reflection->getProperties(ReflectionProperty::IS_PUBLIC);
		$propArr = array();
		foreach($props as $Property){
			$propName = $Property->getName();
			$propArr[$propName] = $this->$propName;
		}
		return $propArr;
	}
	
	public function hasOne( $tableName, $relSettings ){
	}
	
	public function hasMany( $tableName, $relSettings ){
	}

	public function set( $fieldName, $fieldValue ) {
		$this->$fieldName = $fieldValue;
	}

	public function create(){
		return dataAccess::getTable($this->tableName);
	}

	public function save(){
		$propArr = $this->getProps();

		$pkField = $this->pkField;
		if (is_null($this->$pkField)){
			dataAccess::arrayQuery($this->tableName, $propArr, 'insert');
		}else{
			dataAccess::arrayQuery($this->tableName, $propArr, 'update', $pkField . '=' . $this->$pkField);
		}
	}

	public function delete(){
		$pkField = $this->pkField;
		dataAccess::runQuery('delete from ' . $this->tableName . ' where ' . $pkField . ' = "' . $this->$pkField . '"');
	}
	
	public function selectFields($fields){
		$this->selectFields = $fields;
	}
	
	public function getFindQuery($conditionalStatement = ''){
		$sqlStatement = 'SELECT ';
		if (!empty($this->selectFields)){
			$sqlStatement .= implode(', ', $this->selectFields);
		}else{
			$sqlStatement .= '*';
		}
		
		$sqlStatement .= ' FROM ' . $this->tableName;
		
		if (!empty($conditionalStatement)){
			$sqlStatement .= ' WHERE ' . $conditionalStatement;
		}
		
		if (is_null($this->limitResults) === false){
			$sqlStatement .= ' LIMIT ' . $this->limitResults;
		}
		return $sqlStatement;
	}
	
	public function populateTableObject(&$tableObj, $dataObj){
		$props = $tableObj->getProps();
		if (!empty($this->selectFields)){
			$fieldsLimited = true;
		}else{
			$fieldsLimited = false;
		}
		
		foreach($props as $prop){
			$key = $prop->getName();

			if ($fieldsLimited === true){
				if (in_array($key, $this->selectFields)){
					$tableObj->set($key, $dataObj->getVal($key));
				}
			}else{
				$tableObj->set($key, $dataObj->getVal($key));
			}
		}
	}

	public function findOne( $pkVal ){
		$tableObj = dataAccess::getTable($this->tableName);
		$this->limitResults = '1';

		$sqlStatement = $this->getFindQuery($pkField . ' = "' . $pkVal . '"');
		$_resultSet = dataAccess::setQuery($sqlStatement);
		
		$this->populateTableObject($tableObj, $_resultSet->runQuery());
		return $tableObj;
	}

	public function findAll( $conditionalStatement ){
		$sqlStatement = $this->getFindQuery();
		$_resultSet = dataAccess::setQuery($sqlStatement);
		$props = $newClass->getProps();

		$Collection = array();
		while(($result = $_resultSet->next()) !== false){
			$tableObj = dataAccess::getTable($this->tableName);
			$this->populateTableObject($tableObj, $result);
			array_push($Collection, $tableObj);
		}

		return $Collection;
	}
}
?>