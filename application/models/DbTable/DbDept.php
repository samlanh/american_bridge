<?php

class Application_Model_DbTable_DbDept extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_major';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
	
	public function AddNewMajor($_data){
			$_arr=array(
					'dept_id'	  => $_data['dept_id'],
					'major_enname'  => $_data['major_enname'],
					'major_khname'  => $_data['major_khname'],
					'shortcut'	  => $_data['shortcut'],
					'modify_date' => Zend_Date::now(),
					'is_active'	  => $_data['status'],
					'user_id'	  => $this->getUserId()
			);
			return  $this->insert($_arr);
	}
	
	public function AddNewDepartment($_data){
		$this->_name='rms_dept';
		$_arr=array(
				'en_name'	  => $_data['en_name'],
				'type'		  => $_data['type'],
				'shortcut'    => $_data['shortcut'],
				'modify_date' => new Zend_Date(),
				'is_active'   => $_data['status'],
				'user_id'	  => $this->getUserId()
		);
		return  $this->insert($_arr);
	}
	
	public function UpdateDepartment($_data){
		$this->_name='rms_dept';
		$_arr=array(
				'en_name'	  => $_data['en_name'],
				'type'		  => $_data['type'],
				'shortcut'    => $_data['shortcut'],
				'is_active'   => $_data['status'],
				'user_id'	  => $this->getUserId()
		);
		$where = $this->getAdapter()->quoteInto("dept_id=?",$_data["dept_id"]);
		$this->update($_arr, $where);
	}
}

