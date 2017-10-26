<?php

class Allreport_Model_DbTable_DbRptStudentServicePayment extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_tuitionfee';
//     public function getUserId(){
//     	$session_user=new Zend_Session_Namespace('auth');
//     	return $session_user->user_id;
    	 
//     }
    function getAllBalance($search){
    	$db=$this->getAdapter();
    	$sql = "select spd.id,spd.fee AS fee,spd.discount_fix AS discount,spd.subtotal AS payment,spd.paidamount AS paid,spd.is_complete AS complete,
    			spd.balance AS balance,spd.validate AS validate,spd.comment AS comment,     
				(select CONCAT(from_academic,' - ',to_academic) from rms_servicefee where rms_servicefee.id=sp.year limit 1)AS year,
				(select stu_code from rms_student where rms_student.stu_id=sp.student_id limit 1)AS code,
				(select CONCAT(stu_khname,' - ',stu_enname) from rms_student where rms_student.stu_id=sp.student_id limit 1)AS name,
				(select sex from rms_student where rms_student.stu_id=sp.student_id limit 1)AS sex,
				(select title from rms_program_name where rms_program_name.service_id=spd.service_id limit 1)AS service
				from rms_student_payment AS sp,rms_student_paymentdetail AS spd where spd.payment_id=sp.id and spd.balance>0
    		   ";
    	
//     	$order=" ORDER BY id DESC ";
    	$where= '';
    	if(empty($search)){
    		return $db->fetchAll($sql);
    	}
    	$searchs=$search['txtsearch'];
    	if($search['searchby']==1){
    		$where.=" AND (select CONCAT(from_academic,to_academic) from rms_servicefee where rms_servicefee.id=sp.year) LIKE '%".$searchs."%'";
    	}
    	if($search['searchby']==2){
    		$where.=" AND (select stu_code from rms_student where rms_student.stu_id=sp.student_id) LIKE '%".$searchs."%'";
    	}
    	if($search['searchby']==3){
    		$where.=" AND (select CONCAT(stu_khname,' - ',stu_enname) from rms_student where rms_student.stu_id=sp.student_id) LIKE '%".$searchs."%'";
    	}
    	if($search['searchby']==4){
    		$where.=" AND spd.comment LIKE '%".$searchs."%'";
    	}
    	return $db->fetchAll($sql.$where);
    }
    function getFeebyOther($fee_id){
    	$db = $this->getAdapter();
    	$sql = "select *,
    	(SELECT major_enname FROM `rms_major` WHERE major_id=rms_tuitionfee_detail.class_id) as class
    	from rms_tuitionfee_detail where fee_id =".$fee_id." ORDER BY id";
    	return $db->fetchAll($sql);
    }
}
   
    
   