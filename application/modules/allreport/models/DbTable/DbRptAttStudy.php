<?php

class Allreport_Model_DbTable_DbRptAttStudy extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_service';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
    
    public function getAllAttStudy($search){
    	$db = $this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("st.`branch_id`");
    	
    	$sql = "SELECT
				  st.`branch_id`,
				  st.`stu_code`,
				  CONCAT(st.`stu_khname`,'-',st.`stu_enname`) AS name,
				  (SELECT name_en FROM rms_view WHERE TYPE=2 AND key_code=st.`sex`) AS sex,
				  st.`tel` AS stu_phone,
				  (SELECT en_name FROM rms_dept WHERE dept_id = st.degree limit 1) AS degree_name,
				  (SELECT major_enname FROM rms_major WHERE major_id = st.grade) AS grade_name,
				  (SELECT name_en FROM rms_view WHERE TYPE=4 AND key_code = st.session) AS session_name,
				  (SELECT room_name FROM rms_room WHERE room_id = st.room) AS room_name,
				  st.`degree`,
				  st.`grade`,
				  st.`session`,
				  st.`room`,
				  spd.`service_id`,
				  spd.`start_date`,
				  spd.`validate`
				FROM
				  `rms_student` AS st,
				  `rms_student_payment` AS sp,
				  `rms_student_paymentdetail` AS spd
				WHERE
				  sp.id=spd.`payment_id`
				  AND spd.`is_start`=1
				  AND sp.`student_id`=st.`stu_id`
				  AND spd.`service_id`=4
				  $branch_id
    		  ";
    	
    	$where = " ";
    	$order=" ORDER BY st.grade ASC,st.session ASC,st.room ASC ";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " st.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	if($search['grade'] > 0){
    		$where.= " AND st.`grade` = ".$search['grade'];
    	}
    	if($search['session'] > 0){
    		$where.= " AND st.`session` = ".$search['session'];
    	}
    	if($search['room'] > 0){
    		$where.= " AND st.`room` = ".$search['room'];
    	}
    	if($search['branch'] > 0){
    		$where.= " AND st.`branch_id` = ".$search['branch'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    function getAllGrade(){
    	$db = $this->getAdapter();
    	$sql="select major_id as id, CONCAT(major_enname,' (',(select shortcut from rms_dept as d where d.dept_id = m.dept_id),')') as name from rms_major as m where is_active = 1  ";
    	return $db->fetchAll($sql);
    }
    
    function getAllSession(){
    	$db = $this->getAdapter();
    	$sql="select key_code as id, name_en as name from rms_view where type = 4 and status = 1 and key_code IN(1,2,3) ";
    	return $db->fetchAll($sql);
    }
    
    function getAllRoom(){
    	$db = $this->getAdapter();
    	$sql="select room_id as id, room_name as name from rms_room where is_active = 1  ";
    	return $db->fetchAll($sql);
    }
    
    
    
    
    
    
    
}







