<?php

class Allreport_Model_DbTable_DbRptOtherIncome extends Zend_Db_Table_Abstract
{
    protected $_name = 'ln_income';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function getAllOtherIncome($search){
    	$db=$this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission();
    	
    	$sql = "SELECT 
    				*,
    				(select branch_namekh from rms_branch where br_id = branch_id) as branch,
	    			(select category_name from rms_cate_income_expense where rms_cate_income_expense.id = cat_id) as income_category,
	    			(SELECT name_en FROM `rms_view` WHERE rms_view.type=8 and rms_view.key_code = curr_type) AS curr_name,
	    			(select CONCAT(last_name) from rms_users as u where u.id = user_id)  as name
    			 from 
    				ln_income  
    			WHERE 
    				1
    				$branch_id  
    		";
    	
    	$where = ' ';
    	
    	$order=" ORDER BY id DESC ";
    	
    	if($search['shift']==0){
    		$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
    	}else if($search['shift']==1){
    		$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 10:00:00'";
    	}
    	else if($search['shift']==2){
    		$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 10:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 16:00:00'";
    	}
    	else if($search['shift']==3){
    		$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 16:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
    	}
    	$where .= " AND ".$from_date." AND ".$to_date;
    	
    	if(!empty($search['from_receipt']) && !empty($search['to_receipt'])){
    		$where .= " AND invoice between '".$search['from_receipt']."' AND '".$search['to_receipt']."'";
    	}
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	
    	if(!empty($search['branch'])){
    		$where.=" AND branch_id = ".$search['branch'] ;
    	}
    	if(!empty($search['user'])){
    		$where.=" AND user_id = ".$search['user'] ;
    	}
    	if(!empty($search['cate_income'])){
    		$where.=" AND cat_id = ".$search['cate_income'] ;
    	}
    	
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " title LIKE '%{$s_search}%'";
    		$s_where[] = " invoice LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}

    	return $db->fetchAll($sql.$where.$order);
    }
   
    
}
   
    
   