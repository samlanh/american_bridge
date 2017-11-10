<?php

class Kindergarten_Model_DbTable_DbApplication extends Zend_Db_Table_Abstract
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
				'user_id'=>$this->getUserId(),
				'stu_type' => 2,
				'stu_enname' => $_data['name_en'],
				'stu_khname' => $_data['name_kh'],
				'sex' => $_data['sex'],
				'nationality' => $_data['studen_national'],
				'dob' => $_data['date_of_birth'],
				'tel' => $_data['st_phone'],
				'address' => $_data['place_of_birth'],
				'home_num' => $_data['home_note'],
				'street_num' => $_data['way_note'],
				'village_name' => $_data['village_note'],
				'commune_name' => $_data['commun_note'],
				'district_name' => $_data['distric_note'],
				'province_id' => $_data['student_province'],
				'stu_code' => $_data['student_id'],
				'status'=>$_data['status'],
				'remark'=>$_data['remark']
				);
		$id=$this->insert($_arr);

		$this->_name='rms_study_history';
			$arr= array(
						'user_id'=>$this->getUserId(),
						'stu_id'=>$id,
						'stu_type' => 2,
						'stu_code'=>$_data['student_id'],
						'level'=>$_data['level'],
						'from_time'=>$_data['from_time'],
						'to_time'=>$_data['from_time'],
						'start_date'=>$_data['start_date'],
						'type_time'=>$_data['type_time'],
						'status'=>$_data['status'],
						'remark'=>$_data['remark']
						);
				
			$this->insert($arr);
	}
	public function getStudentGepById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_student WHERE stu_id =".$id;
		return $db->fetchRow($sql);
	}
	public function getAllStudentStudy($stu_type){
		$_db = $this->getAdapter();
		$sql = "SELECT stu_id,stu_enname,stu_khname,
		(SELECT name_kh FROM `rms_view` WHERE type=2 AND key_code = sex) as sex
		,nationality,dob,tel,
		(SELECT name_kh FROM `rms_view` WHERE type=1 AND key_code = status) as status
		FROM rms_student where status = 1 AND stu_type=".$stu_type;
		$orderby = " ORDER BY stu_enname ";
		return $_db->fetchAll($sql.$orderby);
	}
	public function updateStudentGep($_data){
		try{
			$_arr= array(
						'user_id'=>$this->getUserId(),
						'stu_type' => 2,
						'stu_enname' => $_data['name_en'],
						'stu_khname' => $_data['name_kh'],
						'sex' => $_data['sex'],
						'nationality' => $_data['studen_national'],
						'dob' => $_data['date_of_birth'],
						'tel' => $_data['st_phone'],
						'address' => $_data['place_of_birth'],
						'home_num' => $_data['home_note'],
						'street_num' => $_data['way_note'],
						'village_name' => $_data['village_note'],
						'commune_name' => $_data['commun_note'],
						'district_name' => $_data['distric_note'],
						'province_id' => $_data['student_province'],
						'stu_code' => $_data['student_id'],
						'status'=>$_data['status'],
						'remark'=>$_data['remark']
				);
				$where = $this->getAdapter()->quoteInto("stu_id=?",  $_data["id"]);
				$this->update($_arr, $where);
				
				$this->_name='rms_study_history';
					$arr= array(
						'user_id'=>$this->getUserId(),
						'stu_code'=>$_data['student_id'],
						'level'=>$_data['level'],
						'from_time'=>$_data['from_time'],
						'to_time'=>$_data['from_time'],
						'start_date'=>$_data['start_date'],
						'type_time'=>$_data['type_time'],
						'status'=>$_data['status'],
						'remark'=>$_data['remark']
						);
				$where = $this->getAdapter()->quoteInto("stu_id=?",  $_data["id"]);
				$this->update($arr, $where);
				
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}		
	}
	public function getStudetnGepdetail($id){
		$_db = $this->getAdapter();
		$sql="SELECT * From rms_study_history where stu_id = ".$id;
		return $_db->fetchRow($sql);
	}
}

