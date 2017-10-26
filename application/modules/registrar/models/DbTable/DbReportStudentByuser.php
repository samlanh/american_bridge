<?php

class Registrar_Model_DbTable_DbReportStudentByuser extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_student';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    public function getBranchId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->branch_id;
    }
    
    public function getType(){
    	$db=$this->getAdapter();
		$sql=" select type from rms_student_paymentdetail";
    	return $db->fetchAll($sql);
    }
	function getAllStudentPayment($search=null){
		$db=$this->getAdapter();
		try{
	    	
			$_db = new Application_Model_DbTable_DbGlobal;
			$branch_id = $_db->getAccessPermission('sp.branch_id');
			
	    	
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
	    	
	    	
	    	$sql=" SELECT 
					  spd.id,
					  sp.receipt_number,
					  
					  (select h.stu_code from rms_study_history as h where is_finished_grade=0 and h.stu_id=sp.student_id order by h.id DESC limit 1) as stu_code,
					  (select ser.stu_code from rms_service as ser where ser.stu_id= sp.student_id and ser.type=4 limit 1) as transport_code,
					  (select ser.stu_code from rms_service as ser where ser.stu_id= sp.student_id and ser.type=5 limit 1) as lunch_code,
					  
					  s.stu_khname,
					  s.stu_enname,
					  CONCAT(s.stu_khname,'-',s.stu_enname) as name,
					  (select en_name from rms_dept where dept_id = s.degree) as degree,
					  (select major_enname from rms_major where major_id = (select h.grade from rms_study_history as h where  is_finished_grade=0 and h.stu_id=sp.student_id order by h.id DESC limit 1)) as grade,
					  spd.type,
					  sp.tuition_fee,
					  spd.fee,
					  spd.qty,
					  spd.discount_percent,
					  spd.discount_fix,
					  spd.subtotal,
					  spd.paidamount,
					  spd.balance,
					  spd.admin_fee,
					  spd.other_fee,
					  sp.create_date,
					  sp.payfor_type,
					  spd.note,
					  spd.is_start,
					  spd.is_parent ,
					  spd.is_complete,
					  (SELECT CONCAT(from_academic,'-',to_academic,'(',generation,')') FROM rms_tuitionfee WHERE `status`=1 AND id=sp.year LIMIT 1) AS year,
					  (SELECT pg.title FROM rms_program_name AS pg WHERE pg.service_id=spd.service_id) AS service_id,
					  (SELECT CONCAT(last_name,' - ',first_name) FROM rms_users WHERE rms_users.id = sp.user_id) AS user_id,
					  (SELECT name_kh FROM rms_view  WHERE rms_view.type=6 AND key_code=spd.payment_term) AS payment_term
					  
					FROM
					  rms_student AS s,
					  rms_student_payment AS sp,
					  rms_student_paymentdetail AS spd 
					WHERE sp.id = spd.payment_id 
					  AND s.stu_id = sp.student_id  
		    		  and sp.reg_from=0 
		    		  $branch_id
	    		";
				  
	    	$where = " AND ".$from_date." AND ".$to_date;
	    	
	    	if(!empty($search['adv_search'])){
	    		$s_where=array();
	    		$s_search= addslashes(trim($search['adv_search']));
	    		$s_where[]= " (select h.stu_code from rms_study_history as h where is_finished_grade=0 and h.stu_id=sp.student_id order by h.id DESC limit 1) LIKE '%{$s_search}%'";   
	    		$s_where[]= " (select ser.stu_code from rms_service as ser where ser.stu_id= sp.student_id and ser.type=4 limit 1) LIKE '%{$s_search}%'";
	    		$s_where[]= " (select ser.stu_code from rms_service as ser where ser.stu_id= sp.student_id and ser.type=5 limit 1) LIKE '%{$s_search}%'";
	    		$s_where[]= " sp.receipt_number LIKE '%{$s_search}%'";
	    		$s_where[]= " s.stu_khname LIKE '%{$s_search}%'";
	    		$s_where[]= " s.stu_enname LIKE '%{$s_search}%'";
	    		$where.=' AND ('.implode(' OR ', $s_where).')';
	    	}
	    	if(($search['service_and_product']>0)){
	    		$where.= " AND spd.service_id = ".$search['service_and_product'];
	    	}
	    	if(!empty($search['user'])){
	    		$where.= " AND sp.user_id = ".$search['user'];
	    	}
	    	
	    	if(!empty($search['grade_all'])){
	    		$where.= " AND (select h.grade from rms_study_history as h where h.payment_id=sp.id) = ".$search['grade_all'];
	    	}
	    	if(!empty($search['degree_all'])){
	    		$where.= " AND (select h.degree from rms_study_history as h where h.payment_id=sp.id) = ".$search['degree_all'];
	    	}
	    	
	    	$order=" ORDER By sp.id DESC ";
	    	//print_r($sql.$where.$order);
	    	return $db->fetchAll($sql.$where.$order);
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}
	    
	function getAllIncome($search){
		
		$db=$this->getAdapter();
    	
    	$sql = "SELECT *,(SELECT c.category_name FROM rms_category As c WHERE c.id=ln_income.cat_id AND c.parent=1 LIMIT 1) As cat_name,
    			(select curr_nameen from ln_currency where ln_currency.id=ln_income.curr_type) as curr_name,
    			(select CONCAT(last_name,' - ',first_name) from rms_users as u where u.id = user_id)  as user_name
    			 from ln_income  WHERE 1";
    	$where= ' ';
    	$order=" ORDER BY id DESC ";
    	
    	$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
    	$where .= "  AND ".$from_date." AND ".$to_date;
    	
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	
    	if(!empty($search['user'])){
    		$where.=" AND user_id = ".$search['user'] ;
    	}
    	if(!empty($search['cat_all'])){
    		$where.=" AND cat_id = ".$search['cat_all'] ;
    	}
    	
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " title LIKE '%{$s_search}%'";
    		$s_where[] = " invoice LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
         
    	return $db->fetchAll($sql.$where.$order);
	}   
	
	function getAllExpense($search){
		$db=$this->getAdapter();
    	$sql = "SELECT *,(SELECT c.category_name FROM rms_category As c WHERE c.id=ln_income_expense.cat_id AND c.parent=0 LIMIT 1) As cat_name,
    			(select curr_nameen from ln_currency where ln_currency.id=curr_type) as curr_name,
    			(select CONCAT(last_name,' - ',first_name) from rms_users as u where u.id = user_id)  as user_name
    			 from ln_income_expense  WHERE 1  ";
    	$where= ' ';
    	$order=" ORDER BY id DESC ";
    	
    	$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
    	$where .= "  AND ".$from_date." AND ".$to_date;
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	
    	if(!empty($search['user'])){
    		$where.=" AND user_id = ".$search['user'] ;
    	}
    	
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " title LIKE '%{$s_search}%'";
    		$s_where[] = " invoice LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	if(!empty($search['cat_all'])){
    		$where.=" AND cat_id = ".$search['cat_all'] ;
    	}
    	return $db->fetchAll($sql.$where.$order);
		
	}
	
	   
	public function getServices($service_id){
	   	    $db=$this->getAdapter();
	   	    $sql="SELECT pn.service_id,pn.title FROM  rms_program_name AS pn,rms_student_paymentdetail AS spd 
						WHERE pn.service_id=spd.service_id AND pn.type=2 AND spd.service_id=$service_id";
	   	    return $db->fetchOne($sql);
	}
	function getCategorys(){
		$db=$this->getAdapter();
		$sql=" SELECT id,category_name AS `name` FROM rms_category WHERE `status`=1 ORDER BY id DESC";
		return $db->fetchAll($sql);
	}
	function getCatByParent($parent_id){
		$db=$this->getAdapter();
		$sql=" SELECT id,category_name AS `name` FROM rms_category WHERE `status`=1 and parent=$parent_id";
		return $db->fetchAll($sql);
	}
}

