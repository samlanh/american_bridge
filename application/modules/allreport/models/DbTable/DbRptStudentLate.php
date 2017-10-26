<?php

class Allreport_Model_DbTable_DbRptStudentLate extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_tuitionfee';
//     public function getUserId(){
//     	$session_user=new Zend_Session_Namespace('auth');
//     	return $session_user->user_id;
    	 
//     }
    function getAllStudentLate($search){
    	$db=$this->getAdapter();
    	
    	//$now=date("d-m-y");
//     	print_r($now);exit();

   // 	$date=date('')
    	
    	
    	
    	
    	$date_start = date_create($search['start_date']);
    	$from_date=date_format($date_start, "d-M-Y");
    	
    	$date_end = date_create($search['end_date']);
    	$to_date=date_format($date_end, "d-m-Y");
    	
    	$sql="SELECT 
				  sp.`receipt_number` AS receipt,
				  (select stu_code from rms_student where rms_student.stu_id=sp.student_id limit 1)AS code,
				  (select CONCAT(stu_khname,' - ',stu_enname) from rms_student where rms_student.stu_id=sp.student_id limit 1)AS name,
				  (select name_en from rms_view where rms_view.type=2 and key_code=(select sex from rms_student where rms_student.stu_id=sp.student_id limit 1))AS sex,
				  pn.`title` service,
				  spd.`start_date` as start,
				  spd.`validate` as end
				  
				FROM
				  `rms_student_paymentdetail` AS spd,
				  `rms_student_payment` AS sp,
				  `rms_program_name` AS pn
				WHERE spd.`is_start` = 1 
				  AND DATEDIFF(spd.`validate`, NOW()) <= 7 
				  AND sp.id=spd.`payment_id`
				  AND spd.`service_id`=pn.`service_id`";
    	
//     	$sql = "SELECT 
// 				  sp.`receipt_number` AS receipt,
// 				  s.`stu_code` as code,
// 				  (select CONCAT(stu_khname,' - ',stu_enname) from rms_student where s.stu_id=sp.student_id limit 1) as name,
// 				  pn.`title` as service,
// 				  spd.`start_date` as start,
// 				  spd.`validate` as end
// 				FROM
// 				  `rms_student_paymentdetail` AS spd,
// 				  `rms_student_payment` AS sp,
// 				  `rms_student` AS s ,
// 				  `rms_program_name` AS pn
// 				WHERE spd.`is_start` = 1 
// 				  AND DATEDIFF(spd.`validate`, NOW()) <= 7 
// 				  AND sp.id=spd.`payment_id`
// 				  AND sp.`student_id`=s.`stu_id`
// 				  AND spd.`service_id`=pn.`service_id`
//     		   ";
     	$order=" ORDER by sp.receipt_number ASC ";
    	//$where= " and sp.create_date between '$from_date' and '$to_date'";
    		if(!empty($search['txtsearch'])){
    			$s_where = array();
    			$s_search = trim($search['txtsearch']);
    			$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    			$s_where[] = " (select stu_code from rms_student where rms_student.stu_id=sp.student_id) LIKE '%{$s_search}%'";
    			$s_where[] = " (select CONCAT(stu_khname,stu_enname) from rms_student where rms_student.stu_id=sp.student_id) LIKE '%{$s_search}%'";
    			$s_where[] = " (select title from rms_program_name where rms_program_name.service_id=spd.service_id) LIKE '%{$s_search}%'";
    			$s_where[] = " spd.comment LIKE '%{$s_search}%'";
    			$where .=' AND ( '.implode(' OR ',$s_where).')';
    		}
    	return $db->fetchAll($sql.$order);
    }
    
}
   
    
   