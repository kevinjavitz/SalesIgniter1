<?php
class OrderCreatorTotal extends OrderTotal implements Serializable {

	public function serialize(){
		$data = array(
			'totalInfo' => $this->totalInfo
		);
		return serialize($data);
	}

	public function unserialize($data){
		$data = unserialize($data);
		foreach($data as $key => $dInfo){
			$this->$key = $dInfo;
		}
	}

	public function setModuleType($val){
		$this->totalInfo['module_type'] = $val;
	}

	public function setTitle($val){
		$this->totalInfo['title'] = $val;
	}

	public function setText($val){
		$this->totalInfo['text'] = $val;
	}

	public function setValue($val){
		$this->totalInfo['value'] = $val;
	}

	public function setSortOrder($val){
		$this->totalInfo['sort_order'] = $val;
	}

	public function setModule($val){
		$this->totalInfo['module'] = $val;
	}

	public function setMethod($val){
		$this->totalInfo['method'] = $val;
	}
}
?>