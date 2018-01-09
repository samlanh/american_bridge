<?php

class Global_Model_DbTable_DbTime extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_time';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    
    function getAllTime($search=null){
    	$db = $this->getAdapter();
    	$sql = " SELECT 
    					id,
    					from_time, 
    					to_time,
    					status,
			    		(SELECT  CONCAT(first_name,' ', last_name) FROM rms_users WHERE id=user_id )AS user_name
				    FROM 
				     	rms_time
				    WHERE 
	    				1 
    			";
    	$order=" order by id DESC ";
    	$where = ' ';
    	
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=trim(addslashes($search['title']));
    		$s_where[]=" from_time LIKE '%{$s_search}%'";
    		$s_where[]=" to_time LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    
    	if($search['status']>-1){
    		$where.= " AND status = ".$search['status'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    }
    
	public function addTime($_data){
		$arr=array(
				'from_time'	  => $_data['from_time'],
				'to_time'	  => $_data['to_time'],
				'create_date' => date("Y-m-d"),
				'user_id'	  => $this->getUserId()
		);
		return  $this->insert($arr);
	}
	public function getTimeById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_time WHERE id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	public function updateTime($data){
		$_arr=array(
				'from_time'	  => $data['from_time'],
				'to_time'	  => $data['to_time'],
				'status' 	  => $data['status'],
				'user_id'	  => $this->getUserId()
		);
		$where=$this->getAdapter()->quoteInto("id=?", $data["id"]);
		$this->update($_arr, $where);
	}
	
}

