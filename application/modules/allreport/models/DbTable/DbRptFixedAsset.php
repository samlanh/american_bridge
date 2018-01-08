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
    	$sql = " SELECT fs.id,(SELECT CONCAT(branch_namekh) FROM rms_branch WHERE rms_branch.br_id=fs.branch_id AND  STATUS=1 LIMIT 1)AS branch_name,
				fs.asset_code,fs.fixed_assetname,fs.usefull_life,fs.paid_month,fs.salvagevalue,fs.start_date,fs.end_date,
				fsd.asset_id,fsd.is_closing,
		        SUM(fsd.total_depre) AS total_after,
		       (SELECT CONCAT(first_name,' ',last_name) AS `name` FROM rms_users WHERE id = fs.user_id LIMIT 1) AS USER
		       FROM ln_fixed_asset AS fs,ln_fixed_assetdetail AS fsd
		       WHERE fs.id=fsd.asset_id ";
//     	$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
//     	$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
//     	$where = "  where 1 and ".$from_date." AND ".$to_date;
    	$order=" GROUP BY fsd.is_closing=1,fsd.asset_id ORDER BY id DESC";
    	$where="";
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_search = str_replace(' ', '', $s_search);
    		$s_where[] = "REPLACE(fs.fixed_assetname,' ','')   LIKE '%{$s_search}%'";
    		$s_where[] = "REPLACE(fs.asset_cost,' ','')   		LIKE '%{$s_search}%'";
    		$s_where[] = "REPLACE(fs.note,' ','')   			LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	if($search['branch'] >0){
    		$where.= " AND fs.branch_id = ".$search['branch'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    }
     
    public function getAllFixedAssetDetail($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT f.id,
				(SELECT CONCAT(branch_namekh) FROM rms_branch WHERE rms_branch.br_id=f.branch_id AND  STATUS=1 LIMIT 1)AS branch_name,
				f.asset_code,
				f.fixed_assetname,
				f.asset_cost,f.usefull_life,f.salvagevalue,f.total_amount,f.note,
				
				(SELECT CONCAT(first_name,' ',last_name) AS `name` FROM rms_users WHERE id = f.user_id LIMIT 1) AS USER,
			
				fd.total_depre AS total_after,fd.times_depre,fd.for_month,fd.is_closing,f.status
			
				 FROM ln_fixed_asset AS f,ln_fixed_assetdetail AS fd
				 WHERE f.id=fd.asset_id 
				 AND fd.asset_id=$id";
    	return $db->fetchAll($sql);
    }
    
}