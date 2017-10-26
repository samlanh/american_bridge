<?php

class Allreport_Model_DbTable_DbRptStaff extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_staff';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
    
    public function getAllStaff($search){
    	$db = $this->getAdapter();
    	$sql = "SELECT 
    				staff_code,
    				name,
    				(select name_kh from rms_view where type=2 and key_code=sex) as sex,
    				phone,
    				(select title from rms_staff_position where rms_staff_position.id=position) as position,
    				salary,
    				note
    			FROM 
    				rms_staff 
    			where 1
    		  ";
    	
    	$order=" order by id DESC";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " carid LIKE '%{$s_search}%'";
    		$s_where[] = " carname LIKE '%{$s_search}%'";
    		$s_where[] = " drivername LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	
    	if($search['searchby'] == 1){
    		$where.= " AND carid = ".$search['txtsearch'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    
    
    
    
    
    
}