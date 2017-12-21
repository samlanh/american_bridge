<?php

class Allreport_Model_DbTable_DbRptInvoice extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_service';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
    
    public function getAllInvoiceParttime($search){
    	$db = $this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("i.`branch_id`");
    	
    	$sql = "SELECT
				  	*,
				  	(select branch_namekh from rms_branch where br_id = i.branch_id) as branch_name,
				  	(select en_name from rms_dept as d where d.dept_id = i.degree) as degree_name,
				  	(select major_enname from rms_major as m where m.major_id = i.grade) as grade_name,
				  	(select name_en from rms_view as v where v.type=4 and v.key_code = i.session) as session_name,
				  	(select room_name from rms_room as r where r.room_id = i.room_id) as room_name,
				  	(select name_en from rms_view as v where v.type=6 and v.key_code = i.payment_term) as payment_term,
				  	i.note,
				  	(select first_name from rms_users as u where u.id = i.user_id) as user,
				  	i.create_date
				FROM
				  	`rms_student` AS st,
				  	rms_invoice as i
				WHERE
					st.stu_id = i.student_id
					and payfor_type=2
				  	$branch_id
    		  ";
    	
    	$where = " ";
    	
    	$from_date =(empty($search['start_date']))? '1': "i.create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': "i.create_date <= '".$search['end_date']." 23:59:59'";
    	$where .= " AND ".$from_date." AND ".$to_date;
    	
    	$order=" ORDER BY i.`id` DESC";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " st.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " i.invoice_no LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	if($search['degree_gep'] > 0){
    		$where.= " AND i.`degree` = ".$search['degree_gep'];
    	}
    	if($search['grade_gep'] > 0){
    		$where.= " AND i.`grade` = ".$search['grade_gep'];
    	}
    	if($search['branch'] > 0){
    		$where.= " AND i.`branch_id` = ".$search['branch'];
    	}
    	
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    
	public function getAllInvoiceTransport($search){
    	$db = $this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission("i.`branch_id`");
    	
    	$sql = "SELECT
				  	*,
				  	(select branch_namekh from rms_branch where br_id = i.branch_id) as branch_name,
				  	(select title from rms_program_name as p where p.service_id = i.service_id) as service_name,
				  	(select name_en from rms_view as v where v.type=6 and v.key_code = i.payment_term) as payment_term,
				  	i.note,
				  	(select first_name from rms_users as u where u.id = i.user_id) as user,
				  	i.create_date
				FROM
				  	`rms_student` AS st,
				  	rms_invoice as i
				WHERE
					st.stu_id = i.student_id
					and payfor_type=3
				  	$branch_id
    		  ";
    	
    	$where = " ";
    	
    	$from_date =(empty($search['start_date']))? '1': "i.create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': "i.create_date <= '".$search['end_date']." 23:59:59'";
    	$where .= " AND ".$from_date." AND ".$to_date;
    	
    	$order=" ORDER BY i.`id` DESC";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " st.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " i.invoice_no LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	if($search['transport_service'] > 0){
    		$where.= " AND i.`service_id` = ".$search['transport_service'];
    	}
    	if($search['branch'] > 0){
    		$where.= " AND i.`branch_id` = ".$search['branch'];
    	}
    	
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
    
    
}