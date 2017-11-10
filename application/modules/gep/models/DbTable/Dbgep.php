<?php

class Gep_Model_DbTable_Dbgep extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'rms_student';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	
	}
	public function getServiceType($type){
		$db = $this->getAdapter();
		$sql ="SELECT `service_id`,`title` FROM `rms_program_name` WHERE status=1 AND `type`=".$type;
		return $db->fetchAll($sql);
	}
	
	public function getlang(){
		$db=$this->getAdapter();
		$sql = "SELECT id, title as name FROM rms_degree_language WHERE status = 1";
		return $db->fetchAll($sql);
	}
	public function addStudent($_data){
		$db= $this->getAdapter();
		$_arr= array(
				'user_id'		=>$this->getUserId(),
				'stu_type' 		=> 2,
				'degree'		=>$_data['dept'],
				'grade'			=>$_data['grade'],
				'stu_enname'	=> $_data['name_en'],
				'stu_khname'	=> $_data['name_kh'],
				'sex' 			=> $_data['sex'],
				'nationality' 	=> $_data['studen_national'],
				'dob' 			=> $_data['date_of_birth'],
				'tel' 			=> $_data['st_phone'],
				'address' 		=> $_data['place_of_birth'],
				'home_num' 		=> $_data['home_note'],
				'street_num' 	=> $_data['way_note'],
				'village_name' 	=> $_data['village_note'],
				'commune_name' 	=> $_data['commun_note'],
				'district_name' => $_data['distric_note'],
				'province_id' 	=> $_data['student_province'],
				'stu_code' 		=> $_data['student_id'],
				'status'		=>$_data['status'],
				'remark'		=>$_data['remark'],
				'create_date'	=>date("Y-m-d H:i:s"),
				);
		$id=$this->insert($_arr);

		$this->_name='rms_study_history';
			$arr= array(
						'user_id'	=>$this->getUserId(),
						'stu_id'	=>$id,
						'stu_type' 	=> 2,
						'stu_code'	=>$_data['student_id'],
						'degree'	=>$_data['dept'],
						'grade'		=>$_data['grade'],
						'level'		=>$_data['grade'],
						'from_time'	=>$_data['from_time'],
						'to_time'	=>$_data['to_time'],
						'start_date'=>$_data['start_date'],
						'type_time'	=>$_data['type_time'],
						'status'	=>$_data['status'],
						'remark'	=>$_data['remark']
						);
				
			$this->insert($arr);
	}
	public function getStudentById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_student WHERE stu_id =".$id;
		return $db->fetchRow($sql);
	}
	public function getAllStudentStudy($search,$stu_type){
		$_db = $this->getAdapter();
		$sql = "SELECT stu_id,stu_code,stu_khname,stu_enname,
		(SELECT name_kh FROM `rms_view` WHERE type=2 AND key_code = sex) as sex,
		(select en_name from rms_dept where dept_id=rms_student.degree) as degree,
		(select major_enname from rms_major where major_id=rms_student.grade) as grade,
		(select name_en from rms_view where type=7 and key_code=(select type_time from rms_study_history where rms_study_history.stu_id=rms_student.stu_id limit 1)) as time,
		(SELECT name_kh FROM `rms_view` WHERE type=1 AND key_code = status) as status,
		(select CONCAT(last_name,'  ',first_name) from rms_users where rms_users.id=".$this->getUserId().")
		FROM rms_student where is_subspend=0 and status = 1 AND stu_type=".$stu_type;
		
		$orderby = " ORDER BY stu_id DESC ";
		
		$from_date =(empty($search['start_date']))? '1': "rms_student.create_date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': "rms_student.create_date <= '".$search['end_date']." 23:59:59'";
		
		$where = " AND ".$from_date." AND ".$to_date;
		
		if(empty($search)){
			return $_db->fetchAll($sql.$orderby);
		}
		if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['adv_search']));
			$s_where[]="stu_code LIKE '%{$s_search}%'";
			$s_where[]="stu_khname LIKE '%{$s_search}%'";
			$s_where[]="stu_enname LIKE '%{$s_search}%'";
			$s_where[]="(SELECT `major_enname` FROM `rms_major` WHERE `major_id`=grade) LIKE '%{$s_search}%'";
			$s_where[]="(SELECT name_kh FROM `rms_view` WHERE type=2 AND key_code = sex) LIKE '%{$s_search}%'";
			$s_where[]="(select en_name from rms_dept where dept_id=rms_student.degree) LIKE '%{$s_search}%'";
			$s_where[]="(select name_en from rms_view where rms_view.type=7 and key_code=(select type_time from rms_study_history where rms_study_history.stu_id=rms_student.stu_id)) LIKE '%{$s_search}%'";
			
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		//echo $sql.$where;
		return $_db->fetchAll($sql.$where.$orderby);
		
	}
	public function updateStudentGep($_data){
		try{
			$_arr= array(
						'user_id'		=>$this->getUserId(),
						'stu_type' 		=> 2,
						'degree'		=>$_data['dept'],
						'grade'			=>$_data['grade'],
						'stu_enname' 	=> $_data['name_en'],
						'stu_khname' 	=> $_data['name_kh'],
						'sex' 			=> $_data['sex'],
						'nationality' 	=> $_data['studen_national'],
						'dob' 			=> $_data['date_of_birth'],
						'tel' 			=> $_data['st_phone'],
						'address' 		=> $_data['place_of_birth'],
						'home_num' 		=> $_data['home_note'],
						'street_num' 	=> $_data['way_note'],
						'village_name' 	=> $_data['village_note'],
						'commune_name'	=> $_data['commun_note'],
						'district_name' => $_data['distric_note'],
						'province_id' 	=> $_data['student_province'],
						'stu_code' 		=> $_data['student_id'],
						'status'		=>$_data['status'],
						'remark'		=>$_data['remark']
				);
				$where = $this->getAdapter()->quoteInto("stu_id=?",  $_data["id"]);
				$this->update($_arr, $where);
				
				$this->_name='rms_study_history';
					$arr= array(
						'user_id'	=>$this->getUserId(),
						'stu_code'	=>$_data['student_id'],
						'level'		=>$_data['grade'],
						'degree'	=>$_data['dept'],
						'grade'		=>$_data['grade'],
						'from_time'	=>$_data['from_time'],
						'to_time'	=>$_data['to_time'],
						'start_date'=>$_data['start_date'],
						'type_time'	=>$_data['type_time'],
						'status'	=>$_data['status'],
						'remark'	=>$_data['remark']
						);
				$where = $this->getAdapter()->quoteInto("stu_id=?",  $_data["id"]);
				$this->update($arr, $where);
				
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}		
	}
	public function getStudentHistoryById($id){
		$_db = $this->getAdapter();
		$sql="SELECT * From rms_study_history where stu_id = ".$id;
		return $_db->fetchRow($sql);
	}
	
	
	public function getNewGepId(){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(stu_id) FROM rms_student WHERE stu_type=2";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$new_acc_no=100+$new_acc_no;
		// echo $new_acc_no;exit();
		$acc_no= strlen((int)$acc_no+1);
		
		$sql="SELECT shortcut FROM rms_dept WHERE dept_id=5 LIMIT 1";
		$shortcut=$db->fetchOne($sql);
		$pre=$shortcut;
		
		for($i=$acc_no;$i<5;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}
	
	public function getGrade(){
		$db = $this->getAdapter();
		$sql="select major_id as id,major_enname as name from rms_major where dept_id=5";
		return $db->fetchAll($sql);
	}
	
	public function getAllDept(){
		$db = $this->getAdapter();
		$sql="select dept_id as id,en_name as name from rms_dept where dept_id>4";
		return $db->fetchAll($sql);
	}
	
	
	function getAllGrade($dept_id){
		$db = $this->getAdapter();
		$sql = "SELECT major_id As id,major_enname As name FROM rms_major WHERE dept_id=".$dept_id;
		//$order=' ORDER BY id DESC';
		return $db->fetchAll($sql);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}

