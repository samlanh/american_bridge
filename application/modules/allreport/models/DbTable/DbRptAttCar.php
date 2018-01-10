<?php

class Allreport_Model_DbTable_DbRptAttCar extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_service';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
    
    public function getAllAttCar($search){
    	$db = $this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("ser.`branch_id`");
    	
    	$sql = "SELECT
				  ser.`stu_code`,
				  
				  (select branch_namekh from rms_branch where br_id = ser.branch_id) as branch_name,
			      (select last_name from rms_users as u where u.id = sp.user_id) as user_name,
				  
				  CONCAT(st.`stu_khname`,'-',st.`stu_enname`) AS name,
				  (SELECT name_en FROM rms_view WHERE TYPE=2 AND key_code=st.`sex`) AS sex,
				  st.`tel` as stu_phone,
				  pn.`title` as service_name,
				  sp.`time_for_car`,
				  (SELECT name_en FROM rms_view WHERE TYPE=6 AND key_code=spd.`payment_term`) AS payment_term,
				  spd.fee,
				  spd.qty,
				  spd.`start_date`,
				  spd.`validate`,
				  ser.`car_id`,
				  c.carid,
				  c.`drivername`,
				  c.tel
				FROM
				  `rms_service` AS ser,
				  `rms_program_name` AS pn,
				  `rms_student` AS st,
				  `rms_student_payment` AS sp,
				  `rms_student_paymentdetail` AS spd,
				  `rms_car` AS c
				WHERE
				  ser.`stu_id`=st.`stu_id`
				  AND ser.`service_id`=pn.`service_id`
				  AND ser.`type`=4
				  AND sp.id=spd.`payment_id`
				  AND spd.`service_id`=ser.`service_id`
				  AND spd.`is_start`=1
				  AND c.id = ser.`car_id`  
				  AND sp.`student_id`=ser.`stu_id`
				  $branch_id
    		  ";
    	
    	$where = " ";
    	$order=" ORDER BY c.`carid` ASC , ser.id ASC ";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " ser.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " c.carid LIKE '%{$s_search}%'";
    		$s_where[] = " c.drivername LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	if($search['carid'] > 0){
    		$where.= " AND c.id = ".$search['carid'];
    	}
    	
    	if($search['service'] > 0){
    		$where.= " AND ser.`service_id` = ".$search['service'];
    	}
    	if($search['branch'] > 0){
    		$where.= " AND ser.`branch_id` = ".$search['branch'];
    	}
    	
    	//echo $sql.$where;
    	
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    
    function getAllCar(){
    	$db = $this->getAdapter();
    	$sql="select id,carid as name from rms_car where status=1";
    	return $db->fetchAll($sql);
    }
    
    function getAllService(){
    	$db = $this->getAdapter();
    	$sql="select service_id as id,title as name from rms_program_name where status=1 and type=2";
    	return $db->fetchAll($sql);
    }
    
    
    
    
}