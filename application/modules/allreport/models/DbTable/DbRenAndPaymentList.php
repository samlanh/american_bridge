<?php

class Allreport_Model_DbTable_DbRenAndPaymentList extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_customer';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    
    }
     
function getCustomer($search=null){
    	//print_r($search);exit();
    	$db=$this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('cp.branch_id');
    	
    	$sql="SELECT 
    				c.*,
    				(select branch_namekh from rms_branch where br_id = cp.branch_id) as branch_name,
    				cp.status As cp_status 
    			FROM 
    				rms_customer AS c,
    				rms_customer_paymentdetail AS cp
            	WHERE 
            		c.id=cp.cus_id
    				and cp.status=1 
    				$branch_id
    		";
    	$where = '';
    	
    	if(!empty($search["title"])){
    		$s_where=array();
    		$s_search = addslashes(trim($search['title']));
    		$s_where[]="  c.customer_code LIKE '%{$s_search}%'";
    		$s_where[]="  c.first_name LIKE '%{$s_search}%'";
    		$s_where[]="  c.phone LIKE '%{$s_search}%'";
    		$s_where[]="  c.email LIKE '%{$s_search}%'";
    		$s_where[]="  c.address LIKE '%{$s_search}%'";
    		
    		$s_where[]="  cp.rent_receipt_no LIKE '%{$s_search}%'";
    		$s_where[]="  cp.water_total LIKE '%{$s_search}%'";
    		$s_where[]="  cp.fire_total LIKE '%{$s_search}%'";
    		$s_where[]="  cp.all_total_amount LIKE '%{$s_search}%'";
    		$s_where[]="  cp.paid LIKE '%{$s_search}%'";
    		$s_where[]="  cp.balance LIKE '%{$s_search}%'";
    		$s_where[]="  cp.rent_paid LIKE '%{$s_search}%'";
    		$s_where[]="  cp.hygiene_price LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    		
    	if($search["status_search"]>-1){
    		$where.=' AND cp.status='.$search["status_search"];
    	}
    	 
    	if($search['cus_name']>0){
    		$where.=' AND c.id='.$search["cus_name"];
    	}
    	if($search['branch']>0){
    		$where.=' AND cp.branch_id='.$search["branch"];
    	}
    	
    	$group_by = " GROUP BY c.id ";
    	
    	$order = " ORDER BY c.customer_code DESC ";
    	
    	//echo $sql.$where.$group_by.$order;//exit();
    	
    	return $db->fetchAll($sql.$where.$order);
    }
    
    function getCusPaymentDetail($id){
    	$db=$this->getAdapter();
    	$sql="SELECT (SELECT rms_view.name_kh FROM rms_view WHERE rms_view.key_code=cp.status AND rms_view.type=1 ) AS status,
		       cp.*,
		       (SELECT CONCAT(first_name,' ',last_name) FROM rms_users WHERE rms_users.id=cp.user_id LIMIT 1) AS user_name,
		       
		       FROM rms_customer AS c,rms_customer_paymentdetail AS cp
		       WHERE c.id=cp.cus_id
		       AND cp.cus_id=$id ";
    	//echo $sql;
    	return $db->fetchAll($sql);
    }
    
    function getNearlydayEndServiceCustomer($search=null){
    	$db=$this->getAdapter();
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('sp.branch_id');
    	$sql="SELECT c.*,(SELECT v.name_en FROM rms_view AS v WHERE v.key_code=c.sex AND v.type=2 LIMIT 1 ) AS c_sex,
    	(SELECT v.name_en FROM rms_view AS v WHERE v.key_code=c.status AND v.type=1 LIMIT 1 ) AS c_status,
    	(SELECT CONCAT(first_name,' ',last_name) FROM rms_users WHERE rms_users.id=c.user_id LIMIT 1) AS user_name
    	FROM rms_customer AS c WHERE 1";
    	$where = '';
    	if(!empty($search["title"])){
    		$s_where=array();
    		$s_search = addslashes(trim($search['title']));
    		$s_where[]="  c.customer_code LIKE '%{$s_search}%'";
    		$s_where[]="  c.first_name LIKE '%{$s_search}%'";
    		$s_where[]="  c.address LIKE '%{$s_search}%'";
    		$s_where[]="  c.phone LIKE '%{$s_search}%'";
    		$s_where[]="  c.email LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    
    	if($search["status_search"]>-1){
    		$where.=' AND c.status='.$search["status_search"];
    	}
    
    	if($search['cus_name']>0){
    		$where.=' AND c.id='.$search["cus_name"];
    	}
    
    	$order=" ORDER BY c.id DESC ";
    	$str_next = '+1 3 days';
    	$search['end_date']=date("Y-m-d", strtotime($search['end_date'].$str_next));
    	$to_date = (empty($search['end_date']))? '1': " c.end_date <= '".$search['end_date']." 23:59:59'";
    	$where .= " AND ".$to_date;
    	//echo $sql.$where;
    	return $db->fetchAll($sql.$where);
    }
    
}