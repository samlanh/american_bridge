<?php

class Allreport_Model_DbTable_DbRptExpectIncome extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_tuitionfee';
//     public function getUserId(){
//     	$session_user=new Zend_Session_Namespace('auth');
//     	return $session_user->user_id;
    	 
//     }
    function getAllExpectIncome($search){
    	$db=$this->getAdapter();
    	
    	$date_start = date_create($search['start_date']);
    	$from_date=date_format($date_start, "d-M-Y");
    	
    	$date_end = date_create($search['end_date']);
    	$to_date=date_format($date_end, "d-m-Y");
    	
    	$sql = "select spd.id,spd.fee AS fee,spd.discount_fix AS discount,spd.subtotal AS payment,spd.paidamount AS paid,spd.is_complete AS complete,
    			spd.balance AS balance,spd.validate AS validate,spd.comment AS comment,sp.create_date AS paid_date,sp.receipt_number AS receipt,     
				(select CONCAT(from_academic,' - ',to_academic) from rms_servicefee where rms_servicefee.id=sp.year limit 1)AS year,
				(select stu_code from rms_student where rms_student.stu_id=sp.student_id limit 1)AS code,
				(select CONCAT(stu_khname,' - ',stu_enname) from rms_student where rms_student.stu_id=sp.student_id limit 1)AS name,
				(select name_en from rms_view where rms_view.type=2 and key_code=(select sex from rms_student where rms_student.stu_id=sp.student_id limit 1))AS sex,
				(select title from rms_program_name where rms_program_name.service_id=spd.service_id limit 1)AS service
				from rms_student_payment AS sp,rms_student_paymentdetail AS spd where spd.payment_id=sp.id and spd.balance>0
    		   ";
     	$order=" ORDER by sp.receipt_number ASC ";
    	//$where= " and sp.create_date between '$from_date' and '$to_date'";
    	$where = '';
    		if(!empty($search['txtsearch'])){
    			$s_where = array();
    			$s_search = addslashes(trim($search['txtsearch']));
    			$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    			$s_where[] = " (select stu_code from rms_student where rms_student.stu_id=sp.student_id) LIKE '%{$s_search}%'";
    			$s_where[] = " (select CONCAT(stu_khname,stu_enname) from rms_student where rms_student.stu_id=sp.student_id) LIKE '%{$s_search}%'";
    			$s_where[] = " (select title from rms_program_name where rms_program_name.service_id=spd.service_id) LIKE '%{$s_search}%'";
    			$s_where[] = " spd.comment LIKE '%{$s_search}%'";
    			$where .=' AND ( '.implode(' OR ',$s_where).')';
    		}
    	return $db->fetchAll($sql.$where.$order);
    }
    
}
   
    
   