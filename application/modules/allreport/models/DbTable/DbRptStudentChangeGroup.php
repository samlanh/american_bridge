<?php

class Allreport_Model_DbTable_DbRptStudentChangeGroup extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_student_change_group';
//     public function getUserId(){
//     	$session_user=new Zend_Session_Namespace('auth');
//     	return $session_user->user_id;
    	 
//     }
    public function getAllStudentChangeGroup($search){
    	$db = $this->getAdapter();
    	$sql = "SELECT stu_id,(SELECT CONCAT(stu_khname,' - ',stu_enname) FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id` limit 1) AS name,
    	(SELECT stu_code FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id` limit 1) AS stu_code,
		(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=2 and `rms_view`.`key_code`=(SELECT sex FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id` limit 1) limit 1)AS sex,
		(select group_code from rms_group where rms_group.id=rms_student_change_group.from_group limit 1) AS code,
		
		(select CONCAT(from_academic,'-',to_academic,' (',generation,')') from rms_tuitionfee where rms_tuitionfee.id=(select academic_year from rms_group where rms_group.id=rms_student_change_group.from_group)) as academic,
		
		(select semester from rms_group where rms_group.id=rms_student_change_group.from_group limit 1 ) AS semester,
		(select name_en from rms_view where rms_view.type=4 and rms_view.key_code=(select session from rms_group where rms_student_change_group.from_group=rms_group.id) limit 1) AS session,
		(select major_enname from rms_major where rms_major.major_id=(select grade from rms_group where rms_student_change_group.from_group=rms_group.id) limit 1) AS grade,
		(select room_name from rms_room where rms_room.room_id=(select room_id from rms_group where rms_student_change_group.from_group=rms_group.id) limit 1) AS room_name,
		(select start_date from rms_group where rms_group.id=rms_student_change_group.from_group limit 1) AS start_date,
		(select amount_month from rms_group where rms_group.id=rms_student_change_group.from_group limit 1) AS amount_month,
		(select expired_date from rms_group where rms_group.id=rms_student_change_group.from_group limit 1) AS expired_date,

		(select group_code from rms_group where rms_group.id=rms_student_change_group.to_group limit 1) AS to_code,
		
		(select CONCAT(from_academic,'-',to_academic,' (',generation,')') from rms_tuitionfee where rms_tuitionfee.id=(select academic_year from rms_group where rms_group.id=rms_student_change_group.to_group)) as to_academic,
		
		(select semester from rms_group where rms_group.id=rms_student_change_group.to_group limit 1) AS to_semester,
		(select name_en from rms_view where rms_view.type=4 and rms_view.key_code=(select session from rms_group where rms_student_change_group.to_group=rms_group.id) limit 1) AS to_session,
		(select major_enname from rms_major where rms_major.major_id=(select grade from rms_group where rms_student_change_group.to_group=rms_group.id) limit 1) AS to_grade,
		(select room_name from rms_room where rms_room.room_id=(select room_id from rms_group where rms_student_change_group.to_group=rms_group.id) limit 1) AS to_room_name,
	 	 moving_date,rms_student_change_group.note,
		(select name_kh from `rms_view` where `rms_view`.`type`=6 and `rms_view`.`key_code`=`rms_student_change_group`.`status`)AS status
		 from `rms_student_change_group`,rms_group where rms_student_change_group.to_group=rms_group.id ";
    	
    	//echo $sql;exit();
    	
    	$where=' ';
    	$order=" order by rms_student_change_group.id DESC";
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['title'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['title']));
    		$s_where[] = " (SELECT stu_code FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id` limit 1) LIKE '%{$s_search}%'";
    		$s_where[] = " (SELECT CONCAT(stu_khname,' - ',stu_enname) FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id` limit 1) LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	if(!empty($search['study_year'])){
    		$where.=' AND rms_group.academic_year='.$search['study_year'];
    	}
    	if(!empty($search['grade_bac'])){
    		$where.=' AND rms_group.grade='.$search['grade_bac'];
    	}
    	if(!empty($search['session'])){
    		$where.=' AND rms_group.session='.$search['session'];
    	}
    	
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}