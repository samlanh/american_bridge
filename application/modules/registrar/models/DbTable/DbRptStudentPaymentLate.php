<?php

class Registrar_Model_DbTable_DbRptStudentPaymentLate extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_tuitionfee';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    
    function getAllStudentPaymentLate($search){
    	$db=$this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal;
    	$branch_id = $_db->getAccessPermission('sp.branch_id');
    	
    	$sql="SELECT 
				  sp.`receipt_number` AS receipt,
				  sp.payfor_type,
				  s.stu_code,
				  (select ser.stu_code from rms_service as ser where ser.stu_id= sp.student_id and ser.type=4 limit 1) as transport_code,
				  (select ser.stu_code from rms_service as ser where ser.stu_id= sp.student_id and ser.type=5 limit 1) as lunch_code,
				  
				  CONCAT(s.stu_khname,' - ',s.stu_enname) AS name,
				  (select name_en from rms_view where rms_view.type=2 and key_code=s.sex) AS sex,
				  s.tel,
				  
				  (select en_name from rms_dept where dept_id=s.degree) as degree,
				  (select major_enname from rms_major where major_id=s.grade) as grade,
				  
				  pn.`title` service,
				  spd.`start_date` as start,
				  spd.`validate` as end
				  
				FROM
				  `rms_student_paymentdetail` AS spd,
				  `rms_student_payment` AS sp,
				  `rms_program_name` AS pn,
				  rms_student as s
				WHERE spd.`is_start` = 1 
				  AND sp.id=spd.`payment_id`
				  AND spd.`service_id`=pn.`service_id` 
    			  and s.stu_id=sp.student_id
    			  and sp.reg_from=0 
    			  $branch_id
    		";
    	
     	$order=" ORDER by spd.validate DESC ";
//      	$from_date =(empty($search['start_date']))? '1': " sp.create_date >= '".$search['start_date']." 00:00:00'";
     	$to_date = (empty($search['end_date']))? '1': " spd.validate < '".$search['end_date']." 23:59:59'";
     	$where = " AND ".$to_date;
     	
    		if(!empty($search['adv_search'])){
    			$s_where = array();
    			$s_search = addslashes(trim($search['adv_search']));
    			$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    			$s_where[] = " (select stu_code from rms_student where rms_student.stu_id=sp.student_id) LIKE '%{$s_search}%'";
    			$s_where[] = " (select CONCAT(stu_khname,stu_enname) from rms_student where rms_student.stu_id=sp.student_id) LIKE '%{$s_search}%'";
    			$s_where[] = " (select title from rms_program_name where rms_program_name.service_id=spd.service_id) LIKE '%{$s_search}%'";
    			$s_where[] = " spd.comment LIKE '%{$s_search}%'";
    			$where .=' AND ( '.implode(' OR ',$s_where).')';
    		}
    		if(!empty($search['service']) AND $search['service']>0){
    			$where.= " AND spd.service_id = ".$search['service'];
    		}
//     		if($search['study_year']>0){
//     			$where.= " AND spd.service_id = ".$search['study_year'];
//     		}
     		//echo $sql.$where;
    	return $db->fetchAll($sql.$where.$order);
    }
    
}
   
    
   