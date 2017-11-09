<?php

class Allreport_Model_DbTable_DbRptPayableNextMonth extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_tuitionfee';

    function getAllPayableNextMonth($search,$payfor_type){
    	$db=$this->getAdapter();
    	
    	$sql="SELECT 
				  sp.`receipt_number` AS receipt,
				  s.stu_code AS code,
				  (select ser.stu_code from rms_service as ser where ser.stu_id = s.stu_id and ser.type=4) as transport_code,
				  (select ser.stu_code from rms_service as ser where ser.stu_id = s.stu_id and ser.type=5) as lunch_code,
				  CONCAT(stu_khname,' - ',stu_enname) AS name,
				  (select name_en from rms_view where rms_view.type=2 and key_code=s.sex) AS sex,
				  (select major_enname from rms_major where major_id = s.grade) as grade,
				  pn.`title` service,
				  spd.`start_date` as start,
				  spd.`validate` as end
				FROM
				  `rms_student_paymentdetail` AS spd,
				  `rms_student_payment` AS sp,
				  `rms_program_name` AS pn,
				  rms_student as s
				WHERE spd.`is_start` = 1 
				  and s.stu_id = sp.student_id
				  AND sp.id=spd.`payment_id`
				  AND spd.`service_id`=pn.`service_id`
				  and sp.payfor_type=$payfor_type
				  and sp.is_void=0
    		";
    	
     	$order=" ORDER by spd.validate ASC ";
     	
     	$where = " ";
     	
     	if(!empty($search['for_month'])){
     		$first_day = 1;
     		$last_day = 31;
     		$year = $search['for_year'];
     		$for_month = $search['for_month'];
     		 
     		$start = $year.'-'.$for_month.'-'.$first_day;
     		$end = $year.'-'.$for_month.'-'.$last_day;
     		 
     		//$from_date = (empty($start))? '1': "spd.validate >= '".$start." 00:00:00'";
     		$to_date = (empty($end))? '1': "spd.validate <= '".$end." 23:59:59'";
     		 
     		$where .= " AND ".$to_date;
     	}
     	
     	if(!empty($search['degree_en_ft'])){
     		$where .= " AND s.degree = ".$search['degree_en_ft'];
     	}
     	if(!empty($search['grade_en_ft'])){
     		$where .= " AND s.grade = ".$search['grade_en_ft'];
     	}
     	if(!empty($search['degree_kh_ft'])){
     		$where .= " AND s.degree = ".$search['degree_kh_ft'];
     	}
     	if(!empty($search['grade_kh_ft'])){
     		$where .= " AND s.grade = ".$search['grade_kh_ft'];
     	}
     	if(!empty($search['degree_gep'])){
     		$where .= " AND s.degree = ".$search['degree_gep'];
     	}
     	if(!empty($search['grade_gep'])){
     		$where .= " AND s.grade = ".$search['grade_gep'];
     	}
     	if(!empty($search['transport_service'])){
     		$where .= " AND spd.service_id = ".$search['transport_service'];
     	}
     	if(!empty($search['lunch_service'])){
     		$where .= " AND spd.service_id = ".$search['lunch_service'];
     	}
     	
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    		$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " (select ser.stu_code from rms_service as ser where ser.stu_id = s.stu_id and ser.type=4) LIKE '%{$s_search}%'";
    		$s_where[] = " (select ser.stu_code from rms_service as ser where ser.stu_id = s.stu_id and ser.type=5) LIKE '%{$s_search}%'";
    		$s_where[] = " stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " (select title from rms_program_name where rms_program_name.service_id=spd.service_id) LIKE '%{$s_search}%'";
    		$s_where[] = " spd.comment LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    		
    		//echo $sql.$where;
    	return $db->fetchAll($sql.$where.$order);
    }
    
}
   
    
   