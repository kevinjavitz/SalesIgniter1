<?php
class MI_Importable
{

	private $base = null;

	/**
	 * Registers this Object with the Base class
	 * @param Base $base
	 */
	final public function register(MI_Base $base) {
		$this->base = $base;
	}

	/**
	 * Calls a method of $this->base
	 * @param string $method
	 * @param string $args
	 * @return mixed
	 */
	final public function __call($method, $args) {
		return call_user_func_array(array($this->base, $method), $args);
	}

	/**
	 * @param string $var name of attribute
	 * @return mixed
	 */
	final public function __get($var) {
		return $this->base->__get_var($this, $var);
	}

	/**
	 * @param string $var name of attribute
	 * @param mixed $value
	 */
	final public function __set($var, $value) {
		return $this->base->__set_var($this, $var, $value);
	}
}

class MI_Base
{

	private $imported_objects = array();

	private $imported_functions = array();

	private $imported_class_names = array();

	private $imported_functions_mapping = array();

	/**
	 * Import method
	 * @param MI_Importable $new_import
	 */
	final public function import(MI_Importable $new_import) {
		$importedClassName = get_class($new_import);

		$new_import->register($this);
		$this->imported_class_names[] = $importedClassName;
		$this->imported_objects[] = &$new_import;
		// the new functions to import
		$import_functions = get_class_methods($importedClassName);
		$MI_Importable_functions = get_class_methods('MI_Importable');
		foreach($import_functions as $function_name){
			if (in_array($function_name, $MI_Importable_functions)) {
				continue;
			}
			if (isset($this->imported_functions[$function_name])){
				throw new Exception('Duplicated function name: ' . $function_name . ' ( ' . $this->imported_functions_mapping[$function_name]['from'] . ' -> ' . $this->imported_functions_mapping[$function_name]['to'] . ' )');
			}
			$this->imported_functions[$function_name] = &$new_import;
			$this->imported_functions_mapping[$function_name] = array(
				'from' => $importedClassName,
				'to' => get_class($this)
			);
		}
	}

	final public function imported($className){
		return in_array($className, $this->imported_class_names);
	}

	/**
	 * Calls a method
	 * @param string $method
	 * @param string $args
	 * @return mixed
	 */
	final public function __call($method, $args) {
		// make sure the function exists
		if (isset($this->imported_functions[$method])){
			return call_user_func_array(array($this->imported_functions[$method], $method), $args);
		}
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}

	/**
	 * Gets a public or protected attribute
	 * @param MI_Importable $caller
	 * @param string $var name of attribute
	 * @return mixed
	 */
	final public function __get_var(MI_Importable $caller, $var) {
		if (in_array($caller, $this->imported_objects)) {
			return $this->{$var};
		}
		else {
			throw new Exception('Unauthorized Access to "__get_var()".');
		}
	}

	/**
	 * Sets a public or protected attribute
	 * @param MI_Importable $caller
	 * @param string $var name of attribute
	 * @param mixed $value
	 */
	final public function __set_var(MI_Importable $caller, $var, $value) {
		if (in_array($caller, $this->imported_objects)) {
			$this->{$var} = $value;
		}
		else {
			throw new Exception('Unauthorized Access to "__set_var()".');
		}
	}

	/**
	 * @param string $var name of attribute
	 * @return mixed
	 */
	public function __get($var) {
		foreach(array_keys($this->imported_objects) as $key){
			if (array_key_exists($var, get_object_vars($this->imported_objects[$key]))){
				return $this->imported_objects[$key]->{$var};
			}
		}
	}

	/**
	 * @param string $var name of attribute
	 * @param mixed $value
	 */
	public function __set($var, $value) {
		foreach(array_keys($this->imported_objects) as $key){
			if (array_key_exists($var, get_object_vars($this->imported_objects[$key]))){
				$this->imported_objects[$key]->{$var} = $value;
			}
		}
	}
}
