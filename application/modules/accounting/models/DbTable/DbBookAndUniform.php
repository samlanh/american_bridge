<?php
class Accounting_Model_DbTable_DbBookAndUniform extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_program_name';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    public function addservice($_data){
    	$db = $this->getAdapter();
    		$_arr = array(
    				'title'		=>$_data['title'],
    				'type'		=>1, // product
    				'product_type'=>$_data['product_type'], 
    				'ser_cate_id'=>5,// product category
    				'description'=>$_data['desc'],
    				'price'		=>$_data['price'],
    				'create_date'=>Zend_Date::now(),
    				'status'	=>$_data['status'],
    				'user_id'	=>$this->getUserId(),
    		);
    		return ($this->insert($_arr));
    } 
    public function updateservice($_data){
    	$_arr=array(
	    			'title'		=>$_data['title'],
    				'product_type'=>$_data['product_type'],
    				'description'=>$_data['desc'],
    				'status'	=>$_data['status'],
    				'price'		=>$_data['price'],
    				'user_id'	=>$this->getUserId(),
    			);
    	$where=' service_id='.$_data['id'];
    	return $this->update($_arr, $where);
    }
    public function getServiceById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM rms_program_name WHERE service_id = ".$id;
    	return $db->fetchRow($sql);
    }	
    
    public function getAllServiceNames($search=''){
    	$db = $this->getAdapter();
    	$sql = "SELECT 
    				service_id,
    				title,
    				description,
    				price,
    				status,
    				create_date,
			    	(SELECT CONCAT(last_name,' ',first_name) FROM rms_users WHERE user_id=id ) AS user_name
			      FROM 
    				rms_program_name 
    			  Where 
    				type=1 
    				AND status=1 
    		";
    	
    	$order=" ORDER BY service_id DESC";
    	 
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
	    if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
		 	$s_where[] = " p.title LIKE '%{$s_search}%'";
// 	    	$s_where[] = " kh_name LIKE '%{$s_search}%'";
// 	    	$s_where[] = " en_name LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
	    }
    	return $db->fetchAll($sql.$order);
    }
}



