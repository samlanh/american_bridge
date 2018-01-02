<?php

class Allreport_Model_DbTable_DbRptDailyIncome extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_service';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    
    function getRate(){
    	$db= $this->getAdapter();
    	$sql = "select reil from rms_exchange_rate where active = 1 limit 1";
    	return $db->fetchOne($sql);
    }
    
    public function getDailyIncomeEnglishFulltime($search){
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
			    		
			    	sp.`grand_total_payment`,
			    	sp.`grand_total_paid_amount`,
			    	sp.`grand_total_balance`,
			    	sp.`note`,
			    	sp.is_subspend,
			    		
			    	spd.`start_date`,
			    	spd.`validate`,
			    	spd.`payment_term`,
			    	spd.is_complete,
			    	spd.qty
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
    	 
    	if($search['shift']==0){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}else if($search['shift']==1){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 10:00:00'";
    	}
    	else if($search['shift']==2){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 10:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 16:00:00'";
    	}
    	else if($search['shift']==3){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 16:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}
    	$where = " AND ".$from_date." AND ".$to_date;
    	 
    	 
    	$order=" ORDER BY sp.`student_id` ASC,sp.`grade` ASC,sp.id ASC ";
    		
    	 
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
	    if($search['user'] > 0){
	    	$where.= " AND sp.`user_id` = ".$search['user'];
	    }
	    //echo $sql.$where.$order;exit();
	    return $db->fetchAll($sql.$where.$order);
    
    }
    
    
    public function getDailyIncomeEnglishParttime($search){
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
					
					sp.`grand_total_payment`,
					sp.`grand_total_paid_amount`,
					sp.`grand_total_balance`,
					sp.`note`,
					sp.is_subspend,
					
					spd.`start_date`,
					spd.`validate`,
					spd.`payment_term`,
					spd.is_complete,
					spd.qty
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
    	
    	if($search['shift']==0){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}else if($search['shift']==1){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 10:00:00'";
    	}
    	else if($search['shift']==2){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 10:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 16:00:00'";
    	}
    	else if($search['shift']==3){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 16:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}
    	$where = " AND ".$from_date." AND ".$to_date;
    	
    	
    	$order=" ORDER BY sp.`student_id` ASC,sp.`grade` ASC,sp.id ASC ";
					
    	
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
    	if($search['user'] > 0){
    		$where.= " AND sp.`user_id` = ".$search['user'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    	 
    }

    public function getDailyIncomeKhmerFulltime($search){
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
			    	 
			    	sp.`grand_total_payment`,
			    	sp.`grand_total_paid_amount`,
			    	sp.`grand_total_balance`,
			    	sp.`note`,
			    	sp.is_subspend,
			    	 
			    	spd.`start_date`,
			    	spd.`validate`,
			    	spd.`payment_term`,
			    	spd.is_complete,
			    	spd.qty
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
    
    	if($search['shift']==0){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}else if($search['shift']==1){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 10:00:00'";
    	}
    	else if($search['shift']==2){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 10:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 16:00:00'";
    	}
    	else if($search['shift']==3){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 16:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}
    	$where = " AND ".$from_date." AND ".$to_date;
    
    
    	$order=" ORDER BY sp.`student_id` ASC,sp.`grade` ASC,sp.id ASC ";
    
    
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
    	if($search['user'] > 0){
    		$where.= " AND sp.`user_id` = ".$search['user'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    
    }
    
    
    public function getDailyIncomeTransport($search){
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
			    	(select title from rms_program_name as p where p.service_id = s.service_id) as service_name,
			    	(select carid from rms_car where rms_car.id = (select car_id from rms_program_name where rms_program_name.service_id = spd.service_id)) as car_id,
			    	
			    	st.`tel`,
			    	sp.`create_date`,
			    	sp.`is_new`,
			    	sp.`receipt_number`,
			    	
			    	sp.time_for_car,
			    	 
			    	sp.`tuition_fee`,
			    	sp.`discount_percent`,
			    	sp.`discount_fix`,
			    	sp.`total_payment`,
			    	sp.`admin_fee`,
			    	sp.`other_fee`,
			    	 
			    	sp.`grand_total_payment`,
			    	sp.`grand_total_paid_amount`,
			    	sp.`grand_total_balance`,
			    	sp.`note`,
			    	sp.is_subspend,
			    	 
			    	spd.`start_date`,
			    	spd.`validate`,
			    	spd.`payment_term`,
			    	spd.is_complete,
			    	spd.qty
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_student_paymentdetail` AS spd,
			    	`rms_student` AS st,
			    	rms_service as s
			    WHERE
			    	sp.id=spd.`payment_id`
			    	and s.stu_id = st.stu_id
			    	AND st.`stu_id`=sp.`student_id`
			    	AND sp.`payfor_type`=3
			    	AND spd.`type`=3
			    	and s.type=4
			    	and sp.is_void=0
			    	$branch_id
    		";
    
    	$where = " ";
    
    	if($search['shift']==0){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}else if($search['shift']==1){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 10:00:00'";
    	}
    	else if($search['shift']==2){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 10:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 16:00:00'";
    	}
    	else if($search['shift']==3){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 16:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}
    	$where = " AND ".$from_date." AND ".$to_date;
    
    
    	$order=" ORDER BY sp.`student_id` ASC,sp.`grade` ASC,sp.id ASC ";
    
    
    	if(empty($search)){
	    	return $db->fetchAll($sql.$order);
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
    	 
    	if($search['branch'] > 0){
    		$where.= " AND sp.`branch_id` = ".$search['branch'];
    	}
    	if($search['user'] > 0){
    		$where.= " AND sp.`user_id` = ".$search['user'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    
    }
    
    
    public function getDailyIncomeFoodandstay($search){
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
			    
			    	(select carid from rms_car where rms_car.id = (select car_id from rms_program_name where rms_program_name.service_id = spd.service_id)) as car_id,
			    
			    	st.`tel`,
			    	sp.`create_date`,
			    	sp.`is_new`,
			    	sp.`receipt_number`,
			    
			    	sp.time_for_car,
			    	 
			    	sp.`tuition_fee`,
			    	sp.`discount_percent`,
			    	sp.`total_payment`,
			    	sp.`admin_fee`,
			    	sp.`other_fee`,
			    	 
			    	sp.`grand_total_payment`,
			    	sp.`grand_total_paid_amount`,
			    	sp.`grand_total_balance`,
			    	sp.`note`,
			    	sp.is_subspend,
			    	 
			    	spd.service_id,
			    	spd.`start_date`,
			    	spd.`validate`,
			    	spd.`payment_term`,
			    	spd.qty,
			    	spd.is_complete
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_student_paymentdetail` AS spd,
			    	`rms_student` AS st,
			    	rms_service as s
			    WHERE
			    	sp.id=spd.`payment_id`
			    	and s.stu_id = st.stu_id
			    	AND st.`stu_id`=sp.`student_id`
			    	AND sp.`payfor_type`=4
			    	AND spd.`type`=5
			    	and s.type=5
			    	and sp.is_void=0
			    	$branch_id
    		";
    
    	$where = " ";
    
    	if($search['shift']==0){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}else if($search['shift']==1){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 10:00:00'";
    	}
    	else if($search['shift']==2){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 10:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 16:00:00'";
    	}
    	else if($search['shift']==3){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 16:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}
    	$where = " AND ".$from_date." AND ".$to_date;
    
    
    	$order=" ORDER BY sp.`student_id` ASC,sp.`grade` ASC,sp.id ASC ";
    
    
    	if(empty($search)){
	    	return $db->fetchAll($sql.$order);
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
    
    	if($search['branch'] > 0){
    		$where.= " AND sp.`branch_id` = ".$search['branch'];
    	}
    	if($search['user'] > 0){
    		$where.= " AND sp.`user_id` = ".$search['user'];
    	}
    	
    	return $db->fetchAll($sql.$where.$order);
    
    }
    
    
    public function getDailyIncomeMaterial($search){
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
			    	sp.`create_date`,
			    	sp.`is_new`,
			    	sp.`receipt_number`,
			    	(select en_name from rms_dept where dept_id = sp.`degree`) as degree,
			    	(select major_enname from rms_major where major_id =  st.`grade`) as grade,
			    	(select room_name from rms_room where rms_room.room_id = sp.`room_id`) as room,
			    	sp.`time`,
			    	 
			    	sp.`tuition_fee`,
			    	sp.`discount_percent`,
			    	sp.`total_payment`,
			    	sp.`admin_fee`,
			    	sp.`other_fee`,
			    	 
			    	sp.`grand_total_payment`,
			    	sp.`grand_total_paid_amount`,
			    	sp.`grand_total_balance`,
			    	
			    	sp.is_subspend,
			    	 
			    	spd.service_id,
			    	(select title from rms_program_name where rms_program_name.service_id = spd.service_id) as service_name,
			    	spd.`note`,
			    	spd.`start_date`,
			    	spd.`validate`,
			    	spd.`payment_term`,
			    	spd.subtotal,
			    	spd.paidamount,
			    	spd.qty
			    FROM
			    	`rms_student_payment` AS sp,
			    	`rms_student_paymentdetail` AS spd,
			    	`rms_student` AS st
			    WHERE
			    	sp.id=spd.`payment_id`
			    	AND st.`stu_id`=sp.`student_id`
			    	AND sp.`payfor_type`=5
			    	AND spd.`type`=4
			    	and sp.is_void=0
			    	$branch_id
    		";
    
    	$where = " ";
    
    	if($search['shift']==0){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}else if($search['shift']==1){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 00:00:00'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 10:00:00'";
    	}
    	else if($search['shift']==2){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 10:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 16:00:00'";
    	}
    	else if($search['shift']==3){
    		$from_date =(empty($search['start_date']))? '1': "sp.create_date >= '".$search['start_date']." 16:00:01'";
    		$to_date = (empty($search['end_date']))? '1': "sp.create_date <= '".$search['end_date']." 23:59:59'";
    	}
    	
    	$where = " AND ".$from_date." AND ".$to_date;
    
    
    	$order=" ORDER BY sp.`student_id` ASC,sp.`grade` ASC,sp.id ASC ";
    
    
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
    
    	if($search['branch'] > 0){
    		$where.= " AND sp.`branch_id` = ".$search['branch'];
    	}
    	if($search['user'] > 0){
    		$where.= " AND sp.`user_id` = ".$search['user'];
    	}
    	 
    	return $db->fetchAll($sql.$where.$order);
    
    }
    
    
    public function getDailyIncomeParkingCanteen($search){
    	$db = $this->getAdapter();
    
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("cp.`branch_id`");
    
    	$sql = "SELECT
			    	cp.id,
			    	
			    	(select branch_namekh from rms_branch where br_id = cp.branch_id) as branch_name,
			    	(select last_name from rms_users as u where u.id = cp.user_id) as user_name,
			    	
			    	c.customer_code,
			    	c.first_name,
			    	cp.rent_receipt_no,
			    	
			    	rent_paid,
			    	water_exc_rate,
			    	fire_exc_rate,
			    	hygiene_price,
			    	other_price,
			    	
			    	all_total_amount,
			    	cp.note,
			    	cp.create_date
			    FROM
			    	rms_customer as c,
			    	rms_customer_paymentdetail as cp
			    WHERE
			    	c.id = cp.cus_id
			    	$branch_id
    		";
    
    	$where = " ";
    
    	if($search['shift']==0){
	    	$from_date =(empty($search['start_date']))? '1': "cp.create_date >= '".$search['start_date']." 00:00:00'";
	    	$to_date = (empty($search['end_date']))? '1': "cp.create_date <= '".$search['end_date']." 23:59:59'";
	    }
	    else if($search['shift']==1){
	    		$from_date =(empty($search['start_date']))? '1': "cp.create_date >= '".$search['start_date']." 00:00:00'";
	    		$to_date = (empty($search['end_date']))? '1': "cp.create_date <= '".$search['end_date']." 10:00:00'";
	    }
	    else if($search['shift']==2){
		    	$from_date =(empty($search['start_date']))? '1': "cp.create_date >= '".$search['start_date']." 10:00:01'";
		    	$to_date = (empty($search['end_date']))? '1': "cp.create_date <= '".$search['end_date']." 16:00:00'";
	    }
	    else if($search['shift']==3){
		    	$from_date =(empty($search['start_date']))? '1': "cp.create_date >= '".$search['start_date']." 16:00:01'";
		    	$to_date = (empty($search['end_date']))? '1': "cp.create_date <= '".$search['end_date']." 23:59:59'";
	    }
	    $where = " AND ".$from_date." AND ".$to_date;
	    $order=" ORDER BY cp.id ASC ";
	    
	    if(empty($search)){
	    	return $db->fetchAll($sql.$order);
	    }
     
	    if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
	    	$s_where[] = " cp.stu_code LIKE '%{$s_search}%'";
	    	$s_where[] = " cp.stu_enname LIKE '%{$s_search}%'";
	    	$s_where[] = " cp.stu_khname LIKE '%{$s_search}%'";
	    	$s_where[] = " cp.receipt_number LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    
    	if($search['branch'] > 0){
    		$where.= " AND cp.`branch_id` = ".$search['branch'];
    	}
    	if($search['user'] > 0){
    		$where.= " AND cp.`user_id` = ".$search['user'];
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







