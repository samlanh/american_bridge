<?php

class Allreport_Model_DbTable_DbRptOtherExpense extends Zend_Db_Table_Abstract
{
    protected $_name = 'ln_income_expense';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function getAllOtherExpense($search){
    	$db=$this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission();
    	
    	$sql = "
    			SELECT 
	    			*,
	    			(select branch_namekh from rms_branch where br_id = branch_id) as branch,
	    			(select category_name from rms_cate_income_expense where rms_cate_income_expense.id = cat_id) as expense_category,
	    			(SELECT name_en FROM `rms_view` WHERE rms_view.type=8 and rms_view.key_code = curr_type) AS curr_name,
	    			(select CONCAT(last_name) from rms_users as u where u.id = user_id)  as name,
	    			(SELECT fixed_assetname FROM ln_fixed_asset WHERE ln_fixed_asset.id=ln_income_expense.fixedasset_id LIMIT 1) AS asset_name
	    			 
    			 from 
    				ln_income_expense 
    			 WHERE 
    			 	status=1 
    				$branch_id  
    			";
    	
    	$where= ' ';
    	$order=" ORDER BY id DESC ";
    	
    	$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
    	$where .= "  AND ".$from_date." AND ".$to_date;
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	
    	if(!empty($search['branch'])){
    		$where.=" AND branch_id = ".$search['branch'] ;
    	}
    	if(!empty($search['asset_id'])){
    		$where.=" AND fixedasset_id = ".$search['asset_id'] ;
    	}
    	if(!empty($search['user'])){
    		$where.=" AND user_id = ".$search['user'] ;
    	}
    	if(!empty($search['cate_expense'])){
    		$where.=" AND cat_id = ".$search['cate_expense'] ;
    	}
    	
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_search = str_replace(' ', '', $s_search);
    		$s_where[] = "REPLACE(title,' ','') LIKE'%{$s_search}%'";
    		$s_where[] = "REPLACE(invoice,' ','')  LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}

    	return $db->fetchAll($sql.$where.$order);
    }
   
    function getAllCategory($type){
    	$db = $this->getAdapter();
    	$sql=" select id , category_name as name from rms_cate_income_expense where status = 1 and parent = $type";
    	return $db->fetchAll($sql);
    }
    
    function getAllFixAssetName(){
    	$db = $this->getAdapter();
    	$sql=" SELECT id,fixed_assetname AS `name` FROM ln_fixed_asset WHERE STATUS=1";
    	return $db->fetchAll($sql);
    }
    
}
   
    
   