<?php

class Global_Model_DbTable_DbOccupation extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_occupation';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
	public function addNewOccupation($_data){
		$_arr=array(
				'occu_name'	  => $_data['occu_name'],
				'occu_enname'	  => $_data['occu_enname'],
				'create_date' => Zend_Date::now(),
				'status'   => $_data['status'],
				'user_id'	  => $this->getUserId()
		);
		return  $this->insert($_arr);
	}
	
	public function addNewOccupationPopup($_data){
		$_arr=array(
				'occu_name'	  => $_data['occu_name'],
				'occu_enname'	  => $_data['occu_enname'],
				'create_date' => Zend_Date::now(),
				'status'   => $_data['status_j'],
				'user_id'	  => $this->getUserId()
		);
		return  $this->insert($_arr);
	}
	
	
	public function getOccupationById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_occupation WHERE occupation_id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	public function updateOccupation($_data){
		$_arr=array(
				'occu_name'	  => $_data['occu_name'],
				'occu_enname'	  => $_data['occu_enname'],
				'create_date' => Zend_Date::now(),
				'status'   => $_data['status'],
				'user_id'	  => $this->getUserId()
		);
		$where=$this->getAdapter()->quoteInto("occupation_id=?", $_data["id"]);
		$this->update($_arr, $where);
	}
	function getAllOccupation($search){
		$db = $this->getAdapter();
		$sql = " SELECT 
					occupation_id AS id,
					occu_name,
					create_date,
					status,
					(SELECT  CONCAT(first_name,' ', last_name) FROM rms_users WHERE id=user_id )AS user_name
				FROM 
					rms_occupation 
				where 
					status=1
					and occu_name != ''
			";
		
		$order = ' ORDER BY occupation_id ASC '; 
		$where = ' ';
		if(empty($search)){
			return $db->fetchAll($sql.$order);
		}
		if(!empty($search['title'])){
			$s_where = array();
			$s_search = addslashes(trim($search['title']));
			$s_where[] = " occu_name LIKE '%{$s_search}%'";
			$s_where[] = " occu_enname LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		return $db->fetchAll($sql.$where.$order);
	}	
	public function addOccupation($_data){//ajax
		$_arr=array(
				'occu_name'	  => $_data['occu_name'],
				'create_date' => Zend_Date::now(),
				'status'   => 1,
				'user_id'	  => $this->getUserId()
		);
		return  $this->insert($_arr);
	}
}

