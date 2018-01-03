<?php

class Allreport_Model_DbTable_DbRptFixedAsset extends Zend_Db_Table_Abstract
{

    protected $_name = 'ln_fixed_asset';
//     public function getUserId(){
//     	$session_user=new Zend_Session_Namespace('auth');
//     	return $session_user->user_id;
    	 
//     }
    public function getAllFixedAsset($search){
    	$db = $this->getAdapter();
    	$sql = " SELECT id,
				(SELECT CONCAT(branch_namekh) FROM rms_branch WHERE rms_branch.br_id=branch_id AND  STATUS=1 LIMIT 1)AS branch_name,
				fixed_assetname,
				 asset_cost,usefull_life,salvagevalue,total_amount,note,
    			 (SELECT CONCAT(first_name,' ',last_name) AS `name` FROM rms_users WHERE id = ln_fixed_asset.user_id LIMIT 1) AS USER,		
    			STATUS FROM ln_fixed_asset";
    	$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
    	$where = "  where 1 and ".$from_date." AND ".$to_date;
    	$order=" order by id DESC";
    	
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_search = str_replace(' ', '', $s_search);
    		$s_where[] = "REPLACE(fixed_assetname,' ','')   LIKE '%{$s_search}%'";
    		$s_where[] = "REPLACE(asset_cost,' ','')   		LIKE '%{$s_search}%'";
    		$s_where[] = "REPLACE(note,' ','')   			LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	if($search['branch'] >0){
    		$where.= " AND branch_id = ".$search['branch'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    }
     
    public function getAllFixedAssetDetail($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT f.id,
				(SELECT CONCAT(branch_namekh) FROM rms_branch WHERE rms_branch.br_id=f.branch_id AND  STATUS=1 LIMIT 1)AS branch_name,
				f.fixed_assetname,
				f.asset_cost,f.usefull_life,f.salvagevalue,f.total_amount,f.note,
		 		(SELECT CONCAT(first_name,' ',last_name) AS `name` FROM rms_users WHERE id = f.user_id LIMIT 1) AS USER,
                       
                 fd.total_depre ,fd.times_depre AS `month`,
                 
				 f.status FROM ln_fixed_asset AS f,ln_fixed_assetdetail AS fd
				 WHERE f.id=fd.asset_id 
				 AND fd.asset_id=$id";
    	return $db->fetchAll($sql);
    }
    
}