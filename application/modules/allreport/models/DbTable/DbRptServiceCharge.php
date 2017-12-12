<?php

class Allreport_Model_DbTable_DbRptServiceCharge extends Zend_Db_Table_Abstract
{
	protected $_name = 'rms_servicefee';
//     public function getUserId(){
//     	$session_user=new Zend_Session_Namespace('auth');
//     	return $session_user->user_id;
    	 
//     }
    function getAllServiceFee($search){
    	$db=$this->getAdapter();
    	$sql = "SELECT 
    				sf.id,
    				CONCAT(from_academic,'-',to_academic,'(',(SELECT branch_namekh FROM rms_branch WHERE br_id = branch_id),')') AS years,
    				(select name_en from rms_view where type=7 and key_code=time) as time,
    		    	generation 
    			FROM 
    				`rms_servicefee` as sf,
    				rms_tuitionfee as tf 
    			where 
    				sf.academic_year=tf.id 
    				and sf.status=1 
    		";
    	
    	$order=" ORDER BY sf.id DESC ";
    	$where=' ';
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	
    	if(!empty($search['year'])){
    		$where.=" AND tf.id = ".$search['year'] ;
    	}
    	
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " CONCAT(from_academic,'-',to_academic) LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
//     	echo $sql.$where.$order;exit();
    	return $db->fetchAll($sql.$where.$order);
    	
    }    
    function getServiceFeebyId($service_id,$service_type,$serid){
    	$db = $this->getAdapter();
    	$sql = "SELECT 
    				sd.id,
    				sd.service_id,
    				sd.price_fee,
    				sd.payment_term,
    				sd.remark,
    				p.title AS service_name ,
    				(SELECT pt.title FROM `rms_program_type` AS pt WHERE pt.id=p.ser_cate_id LIMIT 1) as ser_type
    			FROM 
    				`rms_servicefee_detail` as sd,
    				rms_program_name p 
    			WHERE 
    				p.service_id=sd.service_id 
    				AND sd.service_feeid=".$service_id;
    	
    	if($service_type>0){
    		$sql.=" AND p.ser_cate_id = ".$service_type;
    	}
    	if($serid>0){
    		$sql.=" AND sd.service_id = ".$serid;
    	}
    	$sql.=" ORDER BY p.ser_cate_id DESC ";
    	return $db->fetchAll($sql);
    }

    function getAllYearService(){
    	$db=$this->getAdapter();
    	$sql=" select CONCAT(from_academic,'-',to_academic,'(',generation,')')as year ,sf.id from rms_servicefee as sf,rms_tuitionfee as tf where sf.academic_year = tf.id ";
    	return $db->fetchAll($sql);
    }	
    
    function getServicesAll(){
    	$db=$this->getAdapter();
    	$sql="SELECT service_id AS id,title FROM rms_program_name WHERE `type` = 2 AND title!='' order by ser_cate_id DESC";
    	return $db->fetchAll($sql);
    }
    
    function getServicesType(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,title FROM rms_program_type WHERE title!='' and id IN(2,3) order by id DESC";
    	return $db->fetchAll($sql);
    }
    
}




