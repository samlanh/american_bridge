<?php

class Allreport_Model_DbTable_DbRptParkingPayment extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_service';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
    
	public function getAllParkingPayment($search){
    	$db = $this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("pd.`branch_id`");
    	
    	$sql = "SELECT 
    				pd.id,
    				(select branch_namekh from rms_branch where br_id = pd.branch_id) as branch_name,
    				pd.receipt_no,
    				p.customer_code,
    				p.name,
		       		p.phone,
		       		p.email,
		       		parking_moto_fee,
		       		parking_bike_fee,
		       		broken_thing_fee,
		       		total_fee,
		       		pd.note,
		      		(SELECT CONCAT(first_name) FROM rms_users WHERE rms_users.id=pd.user_id LIMIT 1) AS user_name,
		      		pd.create_date
				FROM
				  	`rms_parking` AS p,
				  	rms_parking_detail as pd
				WHERE
					p.id = pd.parking_id
				  	$branch_id
    		  ";
    	
    	$where = " ";
    	
		if($search['shift']==0){
    		$from_date =(empty($search['start_date']))? '1': "pd.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "pd.create_date <= '".$search['end_date']." 23:59:59'";
    	}else if($search['shift']==1){
    		$from_date =(empty($search['start_date']))? '1': "pd.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "pd.create_date <= '".$search['end_date']." 10:00:00'";
    	}
    	else if($search['shift']==2){
    		$from_date =(empty($search['start_date']))? '1': "pd.create_date >= '".$search['start_date']." 10:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "pd.create_date <= '".$search['end_date']." 16:00:00'";
    	}
    	else if($search['shift']==3){
    		$from_date =(empty($search['start_date']))? '1': "pd.create_date >= '".$search['start_date']." 16:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "pd.create_date <= '".$search['end_date']." 23:59:59'";
    	}
    	$where .= " AND ".$from_date." AND ".$to_date;
    	
    	$order=" ORDER BY pd.`id` DESC";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " p.customer_code LIKE '%{$s_search}%'";
    		$s_where[] = " p.name LIKE '%{$s_search}%'";
    		$s_where[] = " p.phone LIKE '%{$s_search}%'";
    		$s_where[] = " pd.receipt_no LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	if($search['branch'] > 0){
    		$where.= " AND pd.`branch_id` = ".$search['branch'];
    	}
    	if($search['user'] > 0){
    		$where.= " AND pd.`user_id` = ".$search['user'];
    	}
    	
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
    
    
}