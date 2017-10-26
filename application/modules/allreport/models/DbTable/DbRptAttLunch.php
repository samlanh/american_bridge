<?php

class Allreport_Model_DbTable_DbRptAttLunch extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_service';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
    
    public function getAllAttLunch($search){
    	$db = $this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("ser.`branch_id`");
    	
    	$sql = "SELECT
				  ser.`stu_code`,
				  ser.service_id,
				  CONCAT(st.`stu_khname`,'-',st.`stu_enname`) AS name,
				  (SELECT name_en FROM rms_view WHERE TYPE=2 AND key_code=st.`sex`) AS sex,
				  st.`tel` as stu_phone,
				  pn.`title` as service_name,
				  (SELECT name_en FROM rms_view WHERE TYPE=6 AND key_code=spd.`payment_term`) AS payment_term,
				  spd.fee,
				  spd.qty,
				  spd.`start_date`,
				  spd.`validate`
				FROM
				  `rms_service` AS ser,
				  `rms_program_name` AS pn,
				  `rms_student` AS st,
				  `rms_student_payment` AS sp,
				  `rms_student_paymentdetail` AS spd
				WHERE
				  ser.`stu_id`=st.`stu_id`
				  AND ser.`service_id`=pn.`service_id`
				  AND ser.`type`=5
				  AND sp.id=spd.`payment_id`
				  AND spd.`service_id`=ser.`service_id`
				  AND spd.`is_start`=1
				  AND sp.`student_id`=ser.`stu_id`
				  $branch_id
    		  ";
    	
    	$where = " ";
    	$order=" ORDER BY ser.service_id ASC";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " ser.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	if($search['service'] > 0){
    		$where.= " AND ser.`service_id` = ".$search['service'];
    	}
    	if($search['branch'] > 0){
    		$where.= " AND ser.`branch_id` = ".$search['branch'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    function getAllLunchService(){
    	$db = $this->getAdapter();
    	$sql="select service_id as id,title as name from rms_program_name where status=1 and service_id IN(91,92,93) ";
    	return $db->fetchAll($sql);
    }
    
    
    
    
}