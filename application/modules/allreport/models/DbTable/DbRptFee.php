<?php

class Allreport_Model_DbTable_DbRptFee extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_tuitionfee';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function getAllTuitionFee($search){
    	$db=$this->getAdapter();
    	$sql = "SELECT id,CONCAT(from_academic,' - ',to_academic) AS academic,note,
    		    generation,(select name_en from `rms_view` where `rms_view`.`type`=7 and `rms_view`.`key_code`=`rms_tuitionfee`.`time`)AS time,
    			create_date ,status FROM `rms_tuitionfee` where status=1 ";
    	$where= ' ';
    	$order= " ORDER BY id DESC ";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	
    	if(!empty($search['year'])){
    		$where.=" AND id = ".$search['year'] ;
    	}
    	if(!empty($search['branch'])){
    		$where.=" AND branch_id = ".$search['branch'] ;
    	}
    	
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$abc = $s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " CONCAT(from_academic,'-',to_academic) LIKE '%{$s_search}%'";
    		$s_where[] = " rms_tuitionfee.generation LIKE '%{$s_search}%'";
    		$s_where[] = " rms_tuitionfee.from_academic LIKE '%{$s_search}%'";
    		$s_where[] = " rms_tuitionfee.to_academic LIKE '%{$s_search}%'";
    		$s_where[] = " (select name_en from rms_view where rms_view.type=7 and rms_view.key_code=rms_tuitionfee.time) LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}

    	return $db->fetchAll($sql.$where.$order);
    }
    function getFeebyOther($fee_id,$grade_search,$degree_id){
    	//print_r($fee_id);exit();
    	$db = $this->getAdapter();
    	$sql = "select 
    				tf.*,
			    	m.major_enname as class,
			    	(select name_en from rms_view where type=4 and key_code=tf.session LIMIT 1) as session,
			    	(SELECT en_name FROM `rms_dept` WHERE dept_id=m.dept_id LIMIT 1) as degree
			    from 
			    	rms_tuitionfee_detail as tf,
			    	rms_major as m 
			    WHERE  
    				m.major_id=tf.class_id 
    				AND tf.fee_id = $fee_id ";
    	
    	$where = ' ';
    	$order = ' ORDER BY tf.id ASC';
    	 
    	if($degree_id>0){
    		$where.=" AND m.dept_id = ".$degree_id;
    	}
    	if($grade_search>0){
    		$where.=" AND tf.class_id = ".$grade_search;
    	}
//     	echo $sql.$where;
    	return $db->fetchAll($sql.$where);
    }
    
    function getAllYearFee(){
    	$db=$this->getAdapter();
    	$sql=" select id, CONCAT(from_academic,'-',to_academic,'(',generation,')') as year,(select name_en from rms_view where type=7 and key_code=time) as time from rms_tuitionfee ";
    	return $db->fetchAll($sql);
    }
    
    
}
   
    
   