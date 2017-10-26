<?php
class Accounting_Model_DbTable_DbService extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_program_name';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    
    }
    public function getServiceType($type=null){
    	$db = $this->getAdapter();
    	$sql ="SELECT DISTINCT title as name,id FROM rms_program_type WHERE title!='' AND status=1 ";
    	if(!empty($type)){
    		$sql.=" AND type=$type";
    	}
    	$order = " ORDER BY title ";
    	return $db->fetchAll($sql.$order);
    }
    public function addservice($_data){
    	$db = $this->getAdapter();
    		$_arr = array(
    				'title'=>$_data['add_title'],
    				'type'=>2,
    				'ser_cate_id'=>$_data['title'],
    				'description'=>$_data['description'],
    				'car_id'=>$_data['car'],
    				'create_date'=>Zend_Date::now(),
    				'status'=>$_data['status'],
    				'user_id'=>$this->getUserId(),
    		);
    		return ($this->insert($_arr));
    	
    } 
    
    public function addServicePopup($_data){
    	$db = $this->getAdapter();
    	$_arr = array(
    			'title'=>$_data['service_name'],
    			'type'=>2,
    			'ser_cate_id'=>$_data['service_type'],
    			'description'=>$_data['description'],
    			'create_date'=>Zend_Date::now(),
    			'status'=>$_data['status_service'],
    			'user_id'=>$this->getUserId(),
    	);
    	return ($this->insert($_arr));
    	 
    }
    
    public function serviceExist($service_name,$_type){
    	$db = $this->getAdapter();
    	$sql = "SELECT service_id FROM `rms_program_name` WHERE title= '".$service_name."' AND ser_cate_id=$_type";
    	return $db->fetchRow($sql);
    }
    public function updateservice($_data){
    	$_arr=array(
	    			'title'=>$_data['add_title'],
	    			'ser_cate_id'=>$_data['title'],
    				'car_id'=>$_data['car'],
    				'description'=>$_data['description'],
    				'status'=>$_data['status'],
    				'user_id'=>$this->getUserId()
    	);
    	$where=$this->getAdapter()->quoteInto("service_id=?", $_data["id"]);
    	$this->update($_arr, $where);
    }
    public function getServiceById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT *
    	 FROM rms_program_name WHERE service_id = ".$id;
    	return $db->fetchRow($sql);
    }	
    
    public function getAllServiceNames($search=''){
    	$db = $this->getAdapter();
    	$where='';
    	
    	$sql = "SELECT service_id AS id,p.title
    	,(SELECT title FROM `rms_program_type` WHERE id=ser_cate_id LIMIT 1) AS cate_name
    	,`description`,p.`status`,p.`create_date`
    	,(SELECT CONCAT(last_name,' ',first_name) FROM rms_users WHERE p.user_id=id ) AS user_name
    	FROM `rms_program_name` AS p Where type=2 ";
    	$order=" ORDER BY service_id DESC";
    	 
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
	    if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
		 	$s_where[] = " p.title LIKE '%{$s_search}%'";
	    	$s_where[] = " (SELECT title FROM `rms_program_type` WHERE id=ser_cate_id LIMIT 1) LIKE '%{$s_search}%'";
// 	    	$s_where[] = " kh_name LIKE '%{$s_search}%'";
// 	    	$s_where[] = " en_name LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
	    }
    	return $db->fetchAll($sql.$where.$order);
    }
    public function AddServiceType($_data){
    	try{
    	$this->_name='rms_program_type';
    	$_db = $this->getAdapter();
	    	$_arr = array(
	    			'title'=>$_data['p_title'],
	    			'item_desc'=>$_data['note'],
	    			'status'=>$_data['status_p'],
	    			'type'=>$_data['type'],
	    			'create_date'=> new Zend_Date(),
	    			'user_id' => $this->getUserId(),
	    	);
    		return $this->insert($_arr);
    	}catch(Exception $e){
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    }

    function getAllCar(){
    	$db=$this->getAdapter();
    	$sql=" select id,carid as name from rms_car where status=1 ";
    	return $db->fetchAll($sql);
    }
    
    
    
    
    
    
    
    
    
}







