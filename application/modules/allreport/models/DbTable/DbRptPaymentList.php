<?php

class Allreport_Model_DbTable_DbRptPaymentList extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_service';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    
    function getAllAmountStudentByType($search,$type){
    	$db = $this->getAdapter();
    	 
    	//print_r($search);
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    	
    	$sql="SELECT 
    				sp.id,COUNT(sp.student_id) 
    			FROM 
    				`rms_student_payment` AS sp,
    				rms_student AS s 
    			WHERE 
    				s.`stu_id`=sp.`student_id` 
    				AND sp.payfor_type=$type 
    				and sp.is_void=0
    				$branch_id
    		";
    	
    	$where = " ";
    	
//     	$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
//     	$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	 
//     	$where = " AND ".$from_date." AND ".$to_date;
    	
//     	if(!empty($search['for_month'])){
//     		$first_day = 1;
//     		$last_day = 31;
//     		$year=$search['for_year'];
//     		$for_month = $search['for_month'];
    	
//     		$end = $year.'-'.$for_month.'-'.$last_day;
    		
//     		$to_date = (empty($end))? '1': "sp.create_date <= '".$end." 23:59:59'";
    		
//     		$where = " AND ".$to_date;
//     	}
    	
    	$today = date("Y-m-d");
    	$to_date = (empty($today))? '1': "sp.create_date <= '".$today." 23:59:59'";
    	$where .= " AND ".$to_date;
    	
    	$group_by = " GROUP BY student_id ";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$group_by);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " s.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " s.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	 
    	if(!empty($search['degree'])){
    		$where.= " AND sp.`degree` = ".$search['degree'];
    	}
    	if(!empty($search['grade'])){
    		$where.= " AND sp.`grade` = ".$search['grade'];
    	}
    	if(!empty($search['room'])){
    		$where.= " AND sp.`room_id` = ".$search['room'];
    	}
    	if(!empty($search['branch'])){
    		$where.= " AND sp.`branch_id` = ".$search['branch'];
    	}
    	
    	if(!empty($search['service'])){
    		$where.= " AND (select spd.service_id from rms_student_paymentdetail as spd where spd.payment_id=sp.id order by spd.id ASC limit 1) = ".$search['service'];
    	}
    	
    	return $db->fetchAll($sql.$where.$group_by);
    }
    
    function getAllAmountStudentDropByType($search,$type,$this_month){
//     	print_r($search);
    	
    	$db = $this->getAdapter();
    
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    	 
    	$sql="SELECT 
    				COUNT(s.stu_id) 
    			FROM 
    				`rms_student_payment` AS sp,
    				`rms_student` AS s 
    			WHERE 
    				s.`stu_id`=sp.`student_id` 
    				AND sp.`payfor_type`=$type 
    				AND s.`is_subspend`!=0
    				and sp.is_void=0
			    	$branch_id
    		";
    	 
    	$where = " ";
    	
    	 
//     	if(!empty($search['for_month'])){
//     		$first_day = 1;
//     		$last_day = 31;
//     		$year=$search['for_year'];
//     		$for_month = $search['for_month'];
    		 
//     		$end = $year.'-'.$for_month.'-'.$last_day;
    	
//     		$to_date = (empty($end))? '1': "sp.create_date <= '".$end." 23:59:59'";
    		
//     		$suspend_date = "(select sd.date from rms_student_drop as sd where sd.stu_id=sp.student_id order by id DESC limit 1) <= '".$end." 23:59:59'";
    		
//     		$where .= " AND ".$to_date." and ".$suspend_date ;
//     	}
    	
    	$today = date("Y-m-d");
    	$to_date = (empty($today))? '1': "sp.create_date <= '".$today." 23:59:59'";
    	$where .= " AND ".$to_date;
    	
    	$group_by = " GROUP BY student_id ";
    	 
//     	if(empty($search)){
//     		return $db->fetchAll($sql.$group_by);
// 	    }
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " s.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " s.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
	    
	    if(!empty($search['degree'])){
	    	$where.= " AND sp.`degree` = ".$search['degree'];
	    }
	    if(!empty($search['grade'])){
	    	$where.= " AND sp.`grade` = ".$search['grade'];
	    }
    	if(!empty($search['room'])){
    		$where.= " AND sp.`room_id` = ".$search['room'];
	    }
	    if(!empty($search['branch'])){
	    	$where.= " AND sp.`branch_id` = ".$search['branch'];
	    }
	    if(!empty($search['service'])){
	    	$where.= " AND (select spd.service_id from rms_student_paymentdetail as spd where spd.payment_id=sp.id order by spd.id ASC limit 1) = ".$search['service'];
	    }
	    
	    if(!empty($this_month)){
	    	$first_day = 1;
    		$last_day = date("t");
	    	$year=date("Y");
	    	$for_month = date("m");
    		
    		$start = $year.'-'.$for_month.'-'.$first_day;
    		$end = $year.'-'.$for_month.'-'.$last_day;
	    	
	    	$from_date =(empty($start))? '1': "(select sd.date from rms_student_drop as sd where sd.stu_id=sp.student_id and sd.status=1 order by id DESC limit 1) >= '".$start." 00:00:00'";
	    	$to_date = (empty($end))? '1': "(select sd.date from rms_student_drop as sd where sd.stu_id=sp.student_id order by id DESC limit 1) <= '".$end." 23:59:59'";
	    
	    	$where .= " AND ".$from_date." AND ".$to_date;
	    	//echo $sql.$where;
	    }
	    
	    
	    //echo $sql.$where.$group_by;
	    
	    return $db->fetchAll($sql.$where.$group_by);
    }
    
    
///////////////////////////////////////////////////// service type /////////////////////////////////////////////////////////////////////////////////////////////////    
    
    function getAllAmountServiceStudentByType($search,$payfor_type,$service_type){
    	$db = $this->getAdapter();
    
    	//print_r($search);
    	 
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    	 
    	$sql="SELECT
			    	sp.id,COUNT(sp.student_id)
			    FROM
			    	`rms_student_payment` AS sp,
			    	rms_service AS s,
			    	rms_student as st
			    WHERE
			    	s.`stu_id`=sp.`student_id`
			    	and st.stu_id = sp.student_id
			    	AND sp.payfor_type=$payfor_type
			    	and sp.is_void=0
			    	and s.type=$service_type
    				$branch_id
    		";
    	 
    	$where = " ";
    	 
    	//     	$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    	//     	$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    
    	//     	$where = " AND ".$from_date." AND ".$to_date;
    	 
    	//     	if(!empty($search['for_month'])){
    	//     		$first_day = 1;
    	//     		$last_day = 31;
    	//     		$year=$search['for_year'];
    	//     		$for_month = $search['for_month'];
    			 
    	//     		$end = $year.'-'.$for_month.'-'.$last_day;
    
    	//     		$to_date = (empty($end))? '1': "sp.create_date <= '".$end." 23:59:59'";
    
    			//     		$where = " AND ".$to_date;
    	//     	}
    	 
    	$today = date("Y-m-d");
    	$to_date = (empty($today))? '1': "sp.create_date <= '".$today." 23:59:59'";
    	$where .= " AND ".$to_date;
    			 
    	$group_by = " GROUP BY student_id ";
    			 
    	if(empty($search)){
    		return $db->fetchAll($sql.$group_by);
    	}
	    if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
	    	$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
	    }
	    
    	if(!empty($search['degree'])){
    		$where.= " AND sp.`degree` = ".$search['degree'];
    	}
    	if(!empty($search['grade'])){
    		$where.= " AND sp.`grade` = ".$search['grade'];
	    }
    	if(!empty($search['room'])){
    		$where.= " AND sp.`room_id` = ".$search['room'];
	    }
	    if(!empty($search['branch'])){
	    	$where.= " AND sp.`branch_id` = ".$search['branch'];
	    }
	     
	    if($search['service']>0){
	    	$where.= " AND (select spd.service_id from rms_student_paymentdetail as spd where spd.payment_id=sp.id order by spd.id ASC limit 1) = ".$search['service'];
	    }
	     
	    //echo $sql.$where.$group_by;
	    
	    return $db->fetchAll($sql.$where.$group_by);
    }
    
    function getAllAmountStudentDropServiceByType($search,$type,$this_month,$service_type){
    	//     	print_r($search);
    	 
    	$db = $this->getAdapter();
    
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    
    	$sql="SELECT
			    	COUNT(s.stu_id)
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_service` AS s,
			    	rms_student as st
			    WHERE
			    	s.`stu_id`=sp.`student_id`
			    	and st.stu_id = sp.student_id
			    	AND sp.`payfor_type`=$type
			    	AND s.`is_suspend`!=0
			    	and s.type = $service_type
			    	$branch_id
		    ";
    
    	$where = " ";
    	 
    
//     	if(!empty($search['for_month'])){
// 	    	$first_day = 1;
// 	    	$last_day = 31;
// 	    	$year=$search['for_year'];
// 	    	$for_month = $search['for_month'];
	    	 
// 	    	$end = $year.'-'.$for_month.'-'.$last_day;
	    	 
// 	    	$to_date = (empty($end))? '1': "sp.create_date <= '".$end." 23:59:59'";
	    
// 	    	$suspend_date = "(select sd.date from rms_student_drop as sd where sd.stu_id=sp.student_id order by id DESC limit 1) <= '".$end." 23:59:59'";
	    
// 	    	$where .= " AND ".$to_date." and ".$suspend_date ;
//     	}

    	$today = date("Y-m-d");
    	$to_date = (empty($today))? '1': "sp.create_date <= '".$today." 23:59:59'";
    	$where .= " AND ".$to_date;
    	 
    	$group_by = " GROUP BY student_id ";
    
    	//     	if(empty($search)){
    	//     		return $db->fetchAll($sql.$group_by);
    	// 	    }
    	if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
	    	$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
	    	$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	 
    	if(!empty($search['degree'])){
    		$where.= " AND sp.`degree` = ".$search['degree'];
    	}
    	if(!empty($search['grade'])){
    		$where.= " AND sp.`grade` = ".$search['grade'];
    	}
    	if(!empty($search['room'])){
    		$where.= " AND sp.`room_id` = ".$search['room'];
    	}
    	if(!empty($search['branch'])){
    		$where.= " AND sp.`branch_id` = ".$search['branch'];
    	}
    	if($search['service']>0){
    		$where.= " AND (select spd.service_id from rms_student_paymentdetail as spd where spd.payment_id=sp.id order by spd.id ASC limit 1) = ".$search['service'];
    	}
    	 
    	if(!empty($this_month)){
	    	$first_day = 1;
	    	$last_day = date("t");
	    	$year=date("Y");
	    	$for_month=date("m");
    		
	    	$start = $year.'-'.$for_month.'-'.$first_day;
    		$end = $year.'-'.$for_month.'-'.$last_day;
	    
	    	$from_date =(empty($start))? '1': "(select sd.date from rms_student_drop as sd where sd.stu_id=sp.student_id and sd.status=1 order by id DESC limit 1) >= '".$start." 00:00:00'";
	    	$to_date = (empty($end))? '1': "(select sd.date from rms_student_drop as sd where sd.stu_id=sp.student_id order by id DESC limit 1) <= '".$end." 23:59:59'";
	    	 
	    	$where .= " AND ".$from_date." AND ".$to_date;
	    	//echo $sql.$where;
    	}
    	//echo $sql.$where.$group_by;
    	 
    	return $db->fetchAll($sql.$where.$group_by);
    }
    
    function getAllAmountNewServiceStudentByType($search,$payfor_type,$this_month,$service_type){
    	$db = $this->getAdapter();
    
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    
    	$sql="SELECT
			    	COUNT(sp.student_id)
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_service` AS s,
			    	rms_student as st
			    WHERE
			    	s.`stu_id`=sp.`student_id`
			    	and st.stu_id = sp.student_id
			    	AND sp.`payfor_type` = $payfor_type
			    	AND sp.`is_new` = 1
			    	and sp.is_void=0
			    	and s.type=$service_type
			    	$branch_id
    		";
    
    	$where = " ";
    	 
    	//     	$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    	//     	$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    
    	//     	$where = " AND ".$from_date." AND ".$to_date;
    
    	$group_by = " GROUP BY student_id ";
    
    	if(empty($search)){
    		return $db->fetchAll($sql.$group_by);
    	}
    	if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
	    	$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
	    	$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    
    	if(!empty($search['degree'])){
    		$where.= " AND sp.`degree` = ".$search['degree'];
	    }
	    if(!empty($search['grade'])){
	    	$where.= " AND sp.`grade` = ".$search['grade'];
	    }
	    if(!empty($search['room'])){
	    	$where.= " AND sp.`room_id` = ".$search['room'];
	    }
	    if(!empty($search['branch'])){
	    	$where.= " AND sp.`branch_id` = ".$search['branch'];
	    }
	    if(!empty($search['service'])){
	    	$where.= " AND (select spd.service_id from rms_student_paymentdetail as spd where spd.payment_id=sp.id order by spd.id ASC limit 1) = ".$search['service'];
	    }
     
	    if(!empty($this_month)){
		    $first_day = 1;
		    $last_day = date("t");
		    $year=date("Y");
		    $for_month = date("m");
		    
		    $start = $year.'-'.$for_month.'-'.$first_day;
		    $end = $year.'-'.$for_month.'-'.$last_day;
		    
		     
		    $from_date =(empty($start))? '1': " sp.create_date >= '".$start." 00:00:00'";
		    $to_date = (empty($end))? '1': " sp.create_date <= '".$end." 23:59:59'";
		     
		    $where .= " AND ".$from_date." AND ".$to_date;
		     
		    //echo $sql.$where;
	    }
    
     
    	return $db->fetchAll($sql.$where.$group_by);
    }
    
    
    function getAllAmountStudentByService($search,$type,$detail_type,$service_type){
    
    	$db = $this->getAdapter();
    
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    
    	$sql="SELECT
			    	sp.id,
			    	spd.service_id,
			    	(select pn.title from rms_program_name as pn where pn.service_id = spd.service_id) as service_name
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_service` AS s,
			    	rms_student as st,
			    	rms_student_paymentdetail as spd
			    WHERE
			    	s.`stu_id`=sp.`student_id`
			    	and st.stu_id = sp.student_id
			    	and sp.id = spd.payment_id
			    	AND sp.`payfor_type` = $type
			    	and spd.type = $detail_type
			    	and s.is_suspend=0
			    	and s.type = $service_type
			    	and spd.is_start=1
			    	and sp.is_void=0
			    	$branch_id
    		";
    
    	$where = " ";
    
    	//     	$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    	//     	$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    
    	//     	$where = " AND ".$from_date." AND ".$to_date;
    
    	//     	if(!empty($search['for_month'])){
    	// 	    	$first_day = 1;
    	// 	    	$last_day = 31;
    	// 	    	$year=date("Y");
    			// 	    	$for_month = $search['for_month'];
    	 
    	// 	    	$end = $year.'-'.$for_month.'-'.$last_day;
    	 
    	// 	    	$to_date = (empty($end))? '1': "sp.create_date <= '".$end." 23:59:59'";
    	   
    			// 	    	$where = " AND ".$to_date;
    			//     	}
    
    	$today = date("Y-m-d");
    	$to_date = (empty($today))? '1': "sp.create_date <= '".$today." 23:59:59'";
    	$where .= " AND ".$to_date;
    
    	$order=" ORDER BY spd.`service_id` ASC";
    
    	$group_by = " GROUP BY student_id ";
    
    	if(empty($search)){
    		return $db->fetchAll($sql.$where.$group_by.$order);
    	}
    
    	if(!empty($search['txtsearch'])){
    	$s_where = array();
    	$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    		$where .= ' AND ( '.implode(' OR ',$s_where).')';
    	}
    	 
    	if($search['service'] > 0){
    		$where.= " AND spd.`service_id` = ".$search['service'];
    	}
    	if($search['branch'] > 0){
    		$where.= " AND sp.`branch_id` = ".$search['branch'];
    	}
//     	echo $sql.$where.$group_by.$order;
    
    	return $db->fetchAll($sql.$where.$group_by.$order);
    }
    
    
   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    		
    function getAllAmountNewStudentByType($search,$type,$this_month){
    	$db = $this->getAdapter();
    
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    
    	$sql="SELECT
			    	COUNT(sp.student_id)
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_student` AS s
			    WHERE
			    	s.`stu_id`=sp.`student_id`
			    	AND sp.`payfor_type` = $type
			    	AND sp.`is_new` = 1
			    	and sp.is_void=0
			    	$branch_id
    		";
    
    	$where = " ";
    	
//     	$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
//     	$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    
//     	$where = " AND ".$from_date." AND ".$to_date;
    
    	$group_by = " GROUP BY student_id ";
    
    	if(empty($search)){
    		return $db->fetchAll($sql.$group_by);
	    }
	    if(!empty($search['txtsearch'])){
		    $s_where = array();
		    $s_search = addslashes(trim($search['txtsearch']));
		    $s_where[] = " s.stu_code LIKE '%{$s_search}%'";
	    	$s_where[] = " s.stu_enname LIKE '%{$s_search}%'";
	    	$s_where[] = " s.stu_khname LIKE '%{$s_search}%'";
	    	$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
	    }
    	 
    	if(!empty($search['degree'])){
	    	$where.= " AND sp.`degree` = ".$search['degree'];
	    }
	    if(!empty($search['grade'])){
	    	$where.= " AND sp.`grade` = ".$search['grade'];
	    }
    	if(!empty($search['room'])){
    		$where.= " AND sp.`room_id` = ".$search['room'];
	    }
	    if(!empty($search['branch'])){
	    	$where.= " AND sp.`branch_id` = ".$search['branch'];
	    }
	    if(!empty($search['service'])){
	    	$where.= " AND (select spd.service_id from rms_student_paymentdetail as spd where spd.payment_id=sp.id order by spd.id ASC limit 1) = ".$search['service'];
	    }
    	
    	if(!empty($this_month)){
    		$first_day = 1;
    		$last_day = date("t");
	    	$year=date("Y");
	    	$for_month = date("m");
    		
    		$start = $year.'-'.$for_month.'-'.$first_day;
    		$end = $year.'-'.$for_month.'-'.$last_day;
    		
    	
    		$from_date =(empty($start))? '1': " sp.create_date >= '".$start." 00:00:00'";
    		$to_date = (empty($end))? '1': " sp.create_date <= '".$end." 23:59:59'";
    		 
    		$where .= " AND ".$from_date." AND ".$to_date;
    	
    		//echo $sql.$where;
    	}
    
    	
    	return $db->fetchAll($sql.$where.$group_by);
    }
    
    function getAllAmountStudentByGrade($search,$type){
    	
    	$db = $this->getAdapter();
    
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    
    	$sql="SELECT
			    	sp.id,
			    	s.stu_enname,
			    	s.stu_khname,
			    	s.grade,
			    	s.session,
			    	(select major_enname from rms_major where major_id = s.grade) as grade_name,
			    	(select en_name from rms_dept where dept_id = s.degree) as degree_name
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_student` AS s,
			    	rms_student_paymentdetail as spd
			    WHERE
			    	s.`stu_id`=sp.`student_id`
			    	and sp.id = spd.payment_id
			    	AND sp.`payfor_type` = $type
			    	and spd.service_id = 4
			    	and sp.is_subspend=0
			    	and sp.is_void=0
			    	and spd.is_start=1
			    	$branch_id
    		";
    
    	$where = " ";
    	 
    	//     	$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    	//     	$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    
    	//     	$where = " AND ".$from_date." AND ".$to_date;
    
//     	if(!empty($search['for_month'])){
//     		$first_day = 1;
//     		$last_day = 31;
//     		$year=$search['for_year'];
//     		$for_month = $search['for_month'];
    		 
//     		$end = $year.'-'.$for_month.'-'.$last_day;
    	
//     		$to_date = (empty($end))? '1': "sp.create_date <= '".$end." 23:59:59'";
    	
//     		$where = " AND ".$to_date;
//     	}
    	
    	$today = date("Y-m-d");
    	$to_date = (empty($today))? '1': "sp.create_date <= '".$today." 23:59:59'";
    	$where .= " AND ".$to_date;
    	
    	$order=" ORDER BY s.`grade` ASC";
    	
    	$group_by = " GROUP BY student_id ";
    
    	if(empty($search)){
    		return $db->fetchAll($sql.$group_by);
    	}
    	
    	if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
	    	$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
	    	$s_where[] = " s.stu_enname LIKE '%{$s_search}%'";
	    	$s_where[] = " s.stu_khname LIKE '%{$s_search}%'";
	    	$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    
    	if($search['degree'] > 0){
    		$where.= " AND s.`degree` = ".$search['degree'];
    	}
    	if($search['grade'] > 0){
    		$where.= " AND s.`grade` = ".$search['grade'];
    	}
    	if($search['room'] > 0){
    		$where.= " AND s.`room_id` = ".$search['room'];
    	}
    	if($search['branch'] > 0){
    		$where.= " AND s.`branch_id` = ".$search['branch'];
    	}
    
    	//echo $sql.$where.$group_by.$order;exit();
    	
    	return $db->fetchAll($sql.$where.$group_by.$order);
    }
    
    
    
    
    
    function getStudentPayableLastMonth($search,$payfor_type,$type){ //  
    	$db = $this->getAdapter();

    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    	
    	$sql=" SELECT 
				  sp.id,
				  sp.`student_id`,
				  spd.`validate` 
				FROM
				  rms_student as s,
				  `rms_student_payment` AS sp,
				  `rms_student_paymentdetail` AS spd 
				WHERE 
				  sp.id = spd.`payment_id`
				  and s.stu_id = sp.student_id
				  and s.stu_id = sp.student_id 
				  AND sp.is_subspend = 0 
				  AND spd.type = $type 
				  AND spd.`is_start` = 1
				  AND sp.`payfor_type` = $payfor_type
				  $branch_id
    		 ";
    	
    	$where = " ";
    	
    		
    	$first_day = 1;
    	$last_day = 31;
    	$year=date("Y");
    	$last_month = date("m") - 1;
    	 
    	$end = $year.'-'.$last_month.'-'.$last_day;
    	 
    	$to_date = (empty($end))? '1': " spd.`validate` <= '".$end." 23:59:59'";
    	 
    	$where .= " AND ".$to_date;
    	
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " s.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " s.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	
    	if(!empty($search['service'])){
    		$where.= " AND spd.`service_id` = ".$search['service'];
    	}
    	if(!empty($search['branch'])){
    		$where.= " AND sp.`branch_id` = ".$search['branch'];
    	}
    	if(!empty($search['degree'])){
    		$where.= " AND sp.`degree` = ".$search['degree'];
    	}
    	if(!empty($search['grade'] )){
    		$where.= " AND sp.`grade` = ".$search['grade'];
    	}
    	if(!empty($search['room'])){
    		$where.= " AND sp.`room_id` = ".$search['room'];
    	}
    	//echo $sql.$where;//exit();
    	return $db->fetchAll($sql.$where);
    }
    
    
    function getStudentPayableLastMonthService($search,$payfor_type,$type,$service_type){ //
    	$db = $this->getAdapter();
    
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    	 
    	$sql=" SELECT
			    	sp.id,
			    	sp.`student_id`,
			    	spd.`validate`
			    FROM
			    	rms_service as s,
			    	rms_student as st,
			    	`rms_student_payment` AS sp,
			    	`rms_student_paymentdetail` AS spd
		    	WHERE
			    	sp.id = spd.`payment_id`
			    	and st.stu_id = s.stu_id
			    	and s.stu_id = sp.student_id
			    	and s.type = $service_type
			    	AND sp.is_subspend = 0
			    	AND spd.type = $type
			    	AND spd.`is_start` = 1
			    	AND sp.`payfor_type` = $payfor_type
			    	$branch_id
    	";
    	 
    	$where = " ";
    	 
    
    	$first_day = 1;
    	$last_day = 31;
    	$year=date("Y");
    	$last_month = date("m") - 1;
    
    	$end = $year.'-'.$last_month.'-'.$last_day;
    
    	$to_date = (empty($end))? '1': " spd.`validate` <= '".$end." 23:59:59'";
    
    	$where .= " AND ".$to_date;
    	 
    	if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
	    	$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
	    	$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	 
    	 
    	if(!empty($search['service'])){
    		$where.= " AND spd.`service_id` = ".$search['service'];
    	}
    	if(!empty($search['branch'])){
	    	$where.= " AND sp.`branch_id` = ".$search['branch'];
	    }
	    if(!empty($search['degree'])){
	    $where.= " AND sp.`degree` = ".$search['degree'];
	    }
	    if(!empty($search['grade'] )){
	    $where.= " AND sp.`grade` = ".$search['grade'];
	    }
	    if(!empty($search['room'])){
	    		$where.= " AND sp.`room_id` = ".$search['room'];
	    }
	    //echo $sql.$where;//exit();
	    return $db->fetchAll($sql.$where);
    }
    
    function getStudentPayableThisMonth($search,$payfor_type,$type){ //
    	$db = $this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    	
    	$sql=" SELECT
			    	sp.id,
			    	sp.`student_id`,
			    	spd.`validate`
			    FROM
			    	rms_student as st,
			    	rms_service as s,
			    	`rms_student_payment` AS sp,
			    	`rms_student_paymentdetail` AS spd
			    WHERE 
			    	sp.id = spd.`payment_id`
			    	and st.stu_id = sp.student_id
			    	and s.stu_id = st.stu_id
			    	AND sp.is_subspend = 0
			    	AND spd.type = $type
			    	AND spd.`is_start` = 1
			    	AND sp.`payfor_type` = $payfor_type
			    	$branch_id
    		 ";
    	 
    	$where = " ";
    	 
    	$first_day = 1;
    	$last_day = date("t");
    	$year=date("Y");
    	$for_month = date("m");

    	$start = $year.'-'.$for_month.'-'.$first_day;
    	$end = $year.'-'.$for_month.'-'.$last_day;
    	 
    	$from_date = (empty($start))? '1': " spd.`validate` >= '".$start." 00:00:00'";
    	$to_date = (empty($end))? '1': " spd.`validate` <= '".$end." 23:59:59'";
    	 
    	$where .= " AND ".$from_date." AND ".$to_date;
    	
    	
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	if($search['service'] > 0){
    		$where.= " AND spd.`service_id` = ".$search['service'];
    	}
    	if($search['branch'] > 0){
    		$where.= " AND sp.`branch_id` = ".$search['branch'];
    	}
    	if(!empty($search['degree'])){
    		$where.= " AND sp.`degree` = ".$search['degree'];
    	}
    	if(!empty($search['grade'] )){
    		$where.= " AND sp.`grade` = ".$search['grade'];
    	}
    	if(!empty($search['room'])){
    		$where.= " AND sp.`room_id` = ".$search['room'];
    	}
    	//echo $sql.$where;//exit();
    	return $db->fetchAll($sql.$where);
    }
    
    function getStudentPayableThisMonthService($search,$payfor_type,$type,$service_type){ //
    	$db = $this->getAdapter();
    	 
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    	 
    	$sql=" SELECT
			    	sp.id,
			    	sp.`student_id`,
			    	spd.`validate`
			    FROM
			    	rms_student as st,
			    	rms_service as s,
			    	`rms_student_payment` AS sp,
			    	`rms_student_paymentdetail` AS spd
	    		WHERE
			    	sp.id = spd.`payment_id`
			    	and st.stu_id = sp.student_id
			    	and s.stu_id = st.stu_id
			    	and s.type=$service_type
			    	AND sp.is_subspend = 0
			    	AND spd.type = $type
			    	AND spd.`is_start` = 1
			    	AND sp.`payfor_type` = $payfor_type
			    	$branch_id
    	";
    
    	$where = " ";
    
    	$first_day = 1;
    	$last_day = date("t");
    	$year=date("Y");
    	$for_month = date("m");
    
    	$start = $year.'-'.$for_month.'-'.$first_day;
    	$end = $year.'-'.$for_month.'-'.$last_day;
    
    	$from_date = (empty($start))? '1': " spd.`validate` >= '".$start." 00:00:00'";
    	$to_date = (empty($end))? '1': " spd.`validate` <= '".$end." 23:59:59'";
    
    	$where .= " AND ".$from_date." AND ".$to_date;
    	 
    	 
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	 
    	if($search['service'] > 0){
    		$where.= " AND spd.`service_id` = ".$search['service'];
    	}
    	if($search['branch'] > 0){
    		$where.= " AND sp.`branch_id` = ".$search['branch'];
    	}
    	if(!empty($search['degree'])){
    		$where.= " AND sp.`degree` = ".$search['degree'];
    	}
    	if(!empty($search['grade'] )){
    		$where.= " AND sp.`grade` = ".$search['grade'];
    	}
    		if(!empty($search['room'])){
    				$where.= " AND sp.`room_id` = ".$search['room'];
	    }
	    //echo $sql.$where;//exit();
	    return $db->fetchAll($sql.$where);
    }
    
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    
    public function getAllEnglishFulltimePaymentList($search){
    	$db = $this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    	
    	$sql = "SELECT
    				sp.*,
					sp.id,
					
					(select branch_namekh from rms_branch where br_id = sp.branch_id) as branch_name,
					(select last_name from rms_users as u where u.id = sp.user_id) as user_name,
					
					sp.`student_id`,
					st.`stu_code`,
					st.`stu_enname`,
					st.`stu_khname`,
					(SELECT name_en FROM rms_view WHERE TYPE=2 AND key_code = st.`sex`) AS sex,
					st.`tel`,
					(select major_enname from rms_major where major_id =  st.`grade`) as current_grade,
					sp.`create_date`,
					sp.`is_new`,
					sp.`receipt_number`,
					(select en_name from rms_dept where dept_id = sp.`degree`) as degree,
					(select major_enname from rms_major where major_id =  sp.`grade`) as grade,
					
					(select room_name from rms_room where rms_room.room_id = sp.`room_id`) as room,
					sp.`time`,
					
					sp.`tuition_fee`,
					sp.`discount_percent`,
					sp.`discount_fix`,
					sp.`total_payment`,
					sp.`admin_fee`,
					sp.`other_fee`,
					sp.`material_fee`,
					
					sp.`grand_total_payment`,
					sp.`grand_total_paid_amount`,
					sp.`grand_total_balance`,
					sp.`note`,
					sp.is_subspend,
					(select v.name_en from rms_view as v where v.type=5 and key_code = sp.is_subspend) as suspend_type,
					(select date from rms_student_drop where stu_id = sp.student_id and drop_from = 1 order by id DESC limit 1) as date_stop,
					spd.qty,
					spd.`start_date`,
					spd.`validate`,
					spd.`payment_term`
				FROM 
					`rms_student_payment` AS sp,
					`rms_student_paymentdetail` AS spd,
					`rms_student` AS st
				WHERE 
					sp.id=spd.`payment_id`
					AND st.`stu_id`=sp.`student_id`
					AND sp.`payfor_type`=6
					AND spd.`service_id`=4
					and sp.is_void=0
					$branch_id
    		  ";
    	
    	$where = " ";
    	
//     	$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
//     	$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	
//     	$where = " AND ".$from_date." AND ".$to_date;
    	
    	$order=" ORDER BY sp.`student_id` ASC,sp.`grade` ASC,sp.id ASC ";
					
//     	if(!empty($search['for_month'])){
//     		$first_day = 1;
//     		$last_day = 31;
//     		$year = $search['for_year'];
//     		$for_month = $search['for_month'];
    		 
//     		$end = $year.'-'.$for_month.'-'.$last_day;
    		 
//     		$to_date = (empty($end))? '1': "sp.create_date <= '".$end." 23:59:59'";
    		 
//     		$where .= " AND ".$to_date;
//     	}
    	
    	$today = date("Y-m-d");
    	$to_date = (empty($today))? '1': "sp.create_date <= '".$today." 23:59:59'";
    	$where .= " AND ".$to_date;
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " st.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	if($search['degree_en_ft'] > 0){
    		$where.= " AND sp.`degree` = ".$search['degree_en_ft'];
    	}
    	if($search['grade_en_ft'] > 0){
    		$where.= " AND sp.`grade` = ".$search['grade_en_ft'];
    	}
    	if($search['room'] > 0){
    		$where.= " AND sp.`room_id` = ".$search['room'];
    	}
    	if($search['branch'] > 0){
    		$where.= " AND sp.`branch_id` = ".$search['branch'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    
    public function getAllKhFulltimePaymentList($search){
    	$db = $this->getAdapter();
    	 
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    	
    	//echo $search['for_month'];exit();
    	 
    	$sql = "SELECT
    				sp.*,
			    	sp.id,
			    	
			    	(select branch_namekh from rms_branch where br_id = sp.branch_id) as branch_name,
					(select last_name from rms_users as u where u.id = sp.user_id) as user_name,
			    	
			    	sp.`student_id`,
			    	st.`stu_code`,
			    	st.`stu_enname`,
			    	st.`stu_khname`,
			    	(SELECT name_en FROM rms_view WHERE TYPE=2 AND key_code = st.`sex`) AS sex,
			    	st.`tel`,
			    	(select major_enname from rms_major where major_id =  st.`grade`) as current_grade,
			    	sp.`create_date`,
			    	sp.`is_new`,
			    	sp.`receipt_number`,
			    	(select en_name from rms_dept where dept_id = sp.`degree`) as degree,
			    	(select major_enname from rms_major where major_id =  sp.`grade`) as grade,
			    	(select room_name from rms_room where rms_room.room_id = sp.`room_id`) as room,
			    	sp.`time`,
			    		
			    	sp.`tuition_fee`,
			    	sp.`discount_percent`,
			    	sp.`discount_fix`,
			    	sp.`total_payment`,
			    	sp.`admin_fee`,
			    	sp.`other_fee`,
			    	sp.`material_fee`,
			    		
			    	sp.`grand_total_payment`,
			    	sp.`grand_total_paid_amount`,
			    	sp.`grand_total_balance`,
			    	sp.`note`,
			    	sp.is_subspend,
			    	(select v.name_en from rms_view as v where v.type=5 and key_code = sp.is_subspend) as suspend_type,
			    	(select date from rms_student_drop where stu_id = sp.student_id and drop_from = 1 order by id DESC limit 1) as date_stop,
			    	
			    	spd.qty,
			    	spd.`start_date`,
			    	spd.`validate`,
			    	spd.`payment_term`
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_student_paymentdetail` AS spd,
			    	`rms_student` AS st
			    WHERE
			    	sp.id=spd.`payment_id`
			    	AND st.`stu_id`=sp.`student_id`
			    	AND sp.`payfor_type`=1
			    	AND spd.`service_id`=4
			    	and sp.is_void=0
			    	$branch_id
    			";
    	 
    	$where = " ";
    	$order=" ORDER BY sp.`student_id` ASC,sp.create_date ASC ";
    		
//     	if(!empty($search['for_month'])){
//     		$first_day = 1;
//     		$last_day = 31;
//     		$year=$search['for_year'];
//     		$for_month = $search['for_month'];
    		 
//     		$end = $year.'-'.$for_month.'-'.$last_day;
    	
//     		$to_date = (empty($end))? '1': "sp.create_date <= '".$end." 23:59:59'";
    	
//     		$where = " AND ".$to_date;
//     	}
    	
    	$today = date("Y-m-d");
    	$to_date = (empty($today))? '1': "sp.create_date <= '".$today." 23:59:59'";
    	$where .= " AND ".$to_date;
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	
    	if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
	    	$s_where[] = " st.stu_code LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
	    	$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	 
    	if($search['degree_kh_ft'] > 0){
    		$where.= " AND sp.`degree` = ".$search['degree_kh_ft'];
	    }
	    if($search['grade_kh_ft'] > 0){
	    	$where.= " AND sp.`grade` = ".$search['grade_kh_ft'];
	    }
	    if($search['room'] > 0){
	    	$where.= " AND sp.`room_id` = ".$search['room'];
	    }
	    if($search['branch'] > 0){
	    	$where.= " AND sp.`branch_id` = ".$search['branch'];
	    }
	    
	    return $db->fetchAll($sql.$where.$order);
    
    }
    
    
    public function getAllEnglishParttimePaymentList($search){
    	$db = $this->getAdapter();
    	 
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    	 
    	$sql = "SELECT
			    	sp.*,
			    	sp.id,
			    	
			    	(select branch_namekh from rms_branch where br_id = sp.branch_id) as branch_name,
					(select last_name from rms_users as u where u.id = sp.user_id) as user_name,
			    	
			    	sp.`student_id`,
			    	st.`stu_code`,
			    	st.`stu_enname`,
			    	st.`stu_khname`,
			    	(SELECT name_en FROM rms_view WHERE TYPE=2 AND key_code = st.`sex`) AS sex,
			    	st.`tel`,
			    	(select major_enname from rms_major where major_id =  st.`grade`) as current_grade,
			    	sp.`create_date`,
			    	sp.`is_new`,
			    	sp.`receipt_number`,
			    	(select en_name from rms_dept where dept_id = sp.`degree`) as degree,
			    	(select major_enname from rms_major where major_id =  sp.`grade`) as grade,
			    	(select room_name from rms_room where rms_room.room_id = sp.`room_id`) as room,
			    	sp.`time`,
			    		
			    	sp.`tuition_fee`,
			    	sp.`discount_percent`,
			    	sp.`discount_fix`,
			    	sp.`total_payment`,
			    	sp.`admin_fee`,
			    	sp.`other_fee`,
			    	sp.`material_fee`,
			    		
			    	sp.`grand_total_payment`,
			    	sp.`grand_total_paid_amount`,
			    	sp.`grand_total_balance`,
			    	sp.`note`,
			    	sp.is_subspend,
			    	(select v.name_en from rms_view as v where v.type=5 and key_code = sp.is_subspend) as suspend_type,
			    	(select date from rms_student_drop where stu_id = sp.student_id and drop_from = 1 order by id DESC limit 1) as date_stop,
			    		
			    	spd.qty,
			    	spd.`start_date`,
			    	spd.`validate`,
			    	spd.`payment_term`
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_student_paymentdetail` AS spd,
			    	`rms_student` AS st
			    WHERE
			    	sp.id=spd.`payment_id`
			    	AND st.`stu_id`=sp.`student_id`
			    	AND sp.`payfor_type`=2
			    	AND spd.`service_id`=4
			    	and sp.is_void=0
			    	$branch_id
    			";
    	 
    	$where = " ";
    	$order=" ORDER BY sp.`student_id` ASC,sp.create_date ASC ";
    		
    	 
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
	    	$s_where[] = " st.stu_code LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
	    	$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	 
    	if($search['degree_gep'] > 0){
    	$where.= " AND sp.`degree` = ".$search['degree_gep'];
	    }
	    if($search['grade_gep'] > 0){
	    	$where.= " AND sp.`grade` = ".$search['grade_gep'];
	    }
	    if($search['room'] > 0){
	    	$where.= " AND sp.`room_id` = ".$search['room'];
	    }
	    if($search['branch'] > 0){
	    	$where.= " AND sp.`branch_id` = ".$search['branch'];
	    }
	    
// 	    if(!empty($search['for_month'])){
// 	    	$first_day = 1;
// 	    	$last_day = 31;
// 	    	$year=$search['for_year'];
// 	    	$for_month = $search['for_month'];
	    	 
// 	    	$end = $year.'-'.$for_month.'-'.$last_day;
	    	 
// 	    	$to_date = (empty($end))? '1': "sp.create_date <= '".$end." 23:59:59'";
	    	 
// 	    	$where .= " AND ".$to_date;
// 	    }
	    
	    $today = date("Y-m-d");
	    $to_date = (empty($today))? '1': "sp.create_date <= '".$today." 23:59:59'";
	    $where .= " AND ".$to_date;
	    
	    return $db->fetchAll($sql.$where.$order);
    
    }
    
    
    public function getAllTransportPaymentList($search){
    	$db = $this->getAdapter();
    
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    
    	$sql = "SELECT
			    	sp.*,
			    	sp.id,
			    	
			    	(select branch_namekh from rms_branch where br_id = sp.branch_id) as branch_name,
					(select last_name from rms_users as u where u.id = sp.user_id) as user_name,
			    	
			    	sp.`student_id`,
			    	(select stu_code from rms_service where rms_service.stu_id = sp.student_id and rms_service.type=4) as stu_code ,
			    	st.`stu_enname`,
			    	st.`stu_khname`,
			    	(SELECT name_en FROM rms_view WHERE TYPE=2 AND key_code = st.`sex`) AS sex,
			    	st.`tel`,
			    	
			    	(select carid from rms_car where rms_car.id = (select car_id from rms_service as s where s.stu_id = sp.student_id and type=4)) as car_id,
			    	(select title from rms_program_name where rms_program_name.service_id = spd.service_id) as service_name,
			    	
			    	sp.`create_date`,
			    	sp.`is_new`,
			    	sp.`receipt_number`,
			    	 
			    	sp.`tuition_fee`,
			    	sp.`discount_percent`,
			    	sp.`discount_fix`,
			    	sp.`total_payment`,
			    	sp.`material_fee`,
			    	 
			    	sp.`grand_total_payment`,
			    	sp.`grand_total_paid_amount`,
			    	sp.`grand_total_balance`,
			    	sp.`note`,
			    	sp.time_for_car,
			    	sp.is_subspend,
			    	(select v.name_en from rms_view as v where v.type=5 and key_code = sp.is_subspend) as suspend_type,
			    	(select date from rms_student_drop where stu_id = sp.student_id and drop_from = 2 order by id DESC limit 1) as date_stop,
			    	 
			    	spd.`start_date`,
			    	spd.`validate`,
			    	spd.qty,
			    	spd.`payment_term`
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_student_paymentdetail` AS spd,
			    	`rms_student` AS st
		    	WHERE
			    	sp.id=spd.`payment_id`
			    	AND st.`stu_id`=sp.`student_id`
			    	AND sp.`payfor_type`=3
			    	AND spd.`type`=3
			    	and sp.is_void=0
			    	$branch_id
		    	";
    
    	$where = " ";
    	$order=" ORDER BY sp.`student_id` ASC,sp.id ASC,sp.create_date ASC  ";
    
    
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
	    	$s_where[] = " (select stu_code from rms_service where rms_service.stu_id = sp.student_id and rms_service.type=4) LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
	    	$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    
    	if(!empty($search['for_month'])){
    		$first_day = 1;
    		$last_day = 31;
    		$year=$search['for_year'];
    		$for_month = $search['for_month'];
    		 
    		$end = $year.'-'.$for_month.'-'.$last_day;
    		 
    		$to_date = (empty($end))? '1': "sp.create_date <= '".$end." 23:59:59'";
    		 
    		$where .= " AND ".$to_date;
    	}
    	
    	if($search['service'] > 0){
    		$where.= " AND spd.`service_id` = ".$search['service'];
    	}
    	if($search['branch'] > 0){
    		$where.= " AND sp.`branch_id` = ".$search['branch'];
    	}
    	//echo $sql.$where.$order;exit();
    	return $db->fetchAll($sql.$where.$order);
    
    }
    
    
    public function getAllStayAndLunchPaymentList($search){
    	$db = $this->getAdapter();
    
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("sp.`branch_id`");
    
    	$sql = "SELECT
			    	sp.*,
			    	sp.id,
			    	
			    	(select branch_namekh from rms_branch where br_id = sp.branch_id) as branch_name,
			    	(select last_name from rms_users as u where u.id = sp.user_id) as user_name,
			    	
			    	sp.`student_id`,
			    	(select stu_code from rms_service where rms_service.stu_id = sp.student_id and rms_service.type=5) as stu_code ,
			    	st.`stu_enname`,
			    	st.`stu_khname`,
			    	(SELECT name_en FROM rms_view WHERE TYPE=2 AND key_code = st.`sex`) AS sex,
			    	st.`tel`,
			    
			    	(select carid from rms_car where rms_car.id = (select car_id from rms_program_name where rms_program_name.service_id = spd.service_id)) as car_id,
			    	(select title from rms_program_name where rms_program_name.service_id = spd.service_id) as service_name,
			    
			    	sp.`create_date`,
			    	sp.`is_new`,
			    	sp.`receipt_number`,
			    	 
			    	sp.`tuition_fee`,
			    	sp.`discount_percent`,
			    	sp.`discount_fix`,
			    	sp.`total_payment`,
			    	
			    	sp.`material_fee`,
			    	 
			    	sp.`grand_total_payment`,
			    	sp.`grand_total_paid_amount`,
			    	sp.`grand_total_balance`,
			    	sp.`note`,
			    	sp.time_for_car,
			    	sp.is_subspend,
			    	(select v.name_en from rms_view as v where v.type=5 and key_code = sp.is_subspend) as suspend_type,
			    	(select date from rms_student_drop where stu_id = sp.student_id and drop_from = 3 order by id DESC limit 1) as date_stop,
			    	 
			    	spd.service_id,
			    	spd.`start_date`,
			    	spd.`validate`,
			    	spd.qty,
			    	spd.`payment_term`
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_student_paymentdetail` AS spd,
			    	`rms_student` AS st
			    WHERE
			    	sp.id=spd.`payment_id`
			    	AND st.`stu_id`=sp.`student_id`
			    	AND sp.`payfor_type`=4
			    	AND spd.`type`=5
			    	and sp.is_void=0
			    	$branch_id
    		";
    
    	$where = " ";
    	$order = " ORDER BY sp.`student_id` ASC ,sp.id ASC ";
    
    
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
	    	$s_where[] = " (select stu_code from rms_service where rms_service.stu_id = sp.student_id and rms_service.type=5) LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
	    	$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
	    	$s_where[] = " sp.receipt_number LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    
    	if(!empty($search['for_month'])){
    		$first_day = 1;
    		$last_day = 31;
    		$year = $search['for_year'];
    		$for_month = $search['for_month'];
    		 
    		$end = $year.'-'.$for_month.'-'.$last_day;
    		 
    		$to_date = (empty($end))? '1': "sp.create_date <= '".$end." 23:59:59'";
    		 
    		$where .= " AND ".$to_date;
    	}
    	
    	if($search['service'] > 0){
    		$where.= " AND spd.`service_id` = ".$search['service'];
    	}
	    if($search['branch'] > 0){
	    	$where.= " AND sp.`branch_id` = ".$search['branch'];
	    }
	     
	    //echo $sql.$where.$order;exit();
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
    
    function getAllMonth(){
    	$db = $this->getAdapter();
    	$sql="select id, month_kh as name from rms_month where status = 1  ";
    	return $db->fetchAll($sql);
    }
    
    
    
    
    
}







