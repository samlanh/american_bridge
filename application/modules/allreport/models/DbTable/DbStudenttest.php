<?php
class Allreport_Model_DbTable_DbStudenttest extends Zend_Db_Table_Abstract
{
	function getAllStudentTest($search=null){
		$db = $this->getAdapter();
	
		$_db = new Application_Model_DbTable_DbGlobal;
		$branch_id = $_db->getAccessPermission('branch_id');
	
		$sql="SELECT 
					  *,
					 (SELECT branch_namekh from rms_branch where branch_id = br_id LIMIT 1) AS branch_name,
					 (select name_kh from rms_view where type=2 and key_code=sex LIMIT 1) as sex,
					 (select en_name from rms_dept where dept_id=degree LIMIT 1) as degree,
		  			 (SELECT m.major_enname FROM `rms_major` AS m WHERE m.major_id = grade_result LIMIT 1) AS grade_result_title,
					 (SELECT first_name FROM `rms_users` WHERE id=rms_student_test.user_id LIMIT 1) AS user,
					 (select name_en from rms_view where type=14 and key_code=updated_result) as result_status
				FROM
					rms_student_test
				WHERE
					status=1
					and register=0
					$branch_id
			";
	
		if($search['shift']==0){
			$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
		}else if($search['shift']==1){
			$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 10:00:00'";
		}
		else if($search['shift']==2){
			$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 10:00:01'";
			$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 16:00:00'";
		}
		else if($search['shift']==3){
			$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 16:00:01'";
			$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
		}
		
		$where .= " and ".$from_date." AND ".$to_date;
		
		if(!empty($search['from_receipt']) && !empty($search['to_receipt'])){
			$where .= " AND receipt between '".$search['from_receipt']."' AND '".$search['to_receipt']."'";
		}
		
		if (!empty($search['txtsearch'])){
		$s_where = array();
		$s_search = trim(addslashes($search['txtsearch']));
				$s_where[] = " kh_name LIKE '%{$s_search}%'";
				$s_where[] = " en_name LIKE '%{$s_search}%'";
				$s_where[] = " old_school LIKE '%{$s_search}%'";
				$s_where[] = " old_grade LIKE '%{$s_search}%'";
				$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		if ($search['degree']>0){
			$where.=" AND degree = ".$search['degree'];
		}
		if($search['branch'] > 0){
			$where.= " AND `branch_id` = ".$search['branch'];
		}
		if($search['user'] > 0){
			$where.= " AND `user_id` = ".$search['user'];
		}
		$order=" order by id ASC ";
		return $db->fetchAll($sql.$where.$order);
	}
 
}



