<?php

class Gep_Model_DbTable_DbStudent extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'rms_student';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	
	}
	public function getAllStudent($search){
		$_db = $this->getAdapter();
		$sql = "SELECT stu_id,stu_code,stu_khname,stu_enname,
		(SELECT name_kh FROM `rms_view` WHERE type=2 AND key_code = sex) as sex,
		
		(select CONCAT(from_academic,'-',to_academic,'(',generation,')') from rms_tuitionfee where rms_tuitionfee.id=academic_year) as academic,
		
		(SELECT `en_name` FROM `rms_dept` WHERE `dept_id`=degree) AS degree,
		
		(SELECT `major_enname` FROM `rms_major` WHERE `major_id`=grade) as grade,
		(SELECT name_kh FROM `rms_view` WHERE type=1 AND key_code = status) as status
		FROM rms_student where status = 1 AND stu_type = 2 and degree NOT IN(1,2,3,4) and is_subspend=0 ";
		$orderby = " ORDER BY stu_id DESC ";
		
		$from_date =(empty($search['start_date']))? '1': "rms_student.create_date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': "rms_student.create_date <= '".$search['end_date']." 23:59:59'";
		
		$where = " AND ".$from_date." AND ".$to_date;
		//$where=' ';
		if(empty($search)){
			return $_db->fetchAll($sql.$where.$orderby);
		}
		if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['adv_search']));
			$s_where[]="stu_code LIKE '%{$s_search}%'";
			$s_where[]="stu_khname LIKE '%{$s_search}%'";
			$s_where[]="stu_enname LIKE '%{$s_search}%'";
			$s_where[]="(SELECT en_name FROM rms_dept WHERE rms_dept.dept_id=degree) LIKE '%{$s_search}%'";
			$s_where[]="(select CONCAT(from_academic,'-',to_academic,'(',generation,')') from rms_tuitionfee where rms_tuitionfee.id=academic_year) LIKE '%{$s_search}%'";
			$s_where[]="(SELECT `major_enname` FROM `rms_major` WHERE `major_id`=grade) LIKE '%{$s_search}%'";
			$s_where[]="(SELECT name_kh FROM `rms_view` WHERE type=2 AND key_code = sex) LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		return $_db->fetchAll($sql.$where.$orderby);
	}
	public function getStudentById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_student WHERE stu_id =".$id;
		return $db->fetchRow($sql);
	}
	public function getDegreeLanguage(){
		try{
			$db = $this->getAdapter();
			$sql ="SELECT id,title FROM rms_degree_language WHERE status =1";
			//print_r($db->fetchRow($sql)); exit();
			return $db->fetchAll($sql);
		}catch(Exception $e){
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	function getStudentExist($name_en,$name_kh,$sex,$grade,$dob){
		$db = $this->getAdapter();
		$sql = "select * from rms_student where stu_enname="."'$name_en'"." and stu_khname="."'$name_kh'"." and sex=".$sex." and grade=".$grade." and dob="."'$dob'";
		return $db->fetchRow($sql);
	}
	
	public function addStudent($_data){
		
			$id = $this->getStudentExist($_data['name_en'],$_data['name_kh'],$_data['sex'],$_data['grade'],$_data['date_of_birth']);	
			
// 			print_r($id);exit();
			
			if(!empty($id)){
				return -1;
			}
			try{	
				$_db= $this->getAdapter();
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
						
						'academic_year' => $_data['academic_year'],
						'degree' => $_data['degree'],
						'grade' => $_data['grade'],
						
						'stu_code' => $_data['student_id'],
						'status'=>$_data['status'],
						'remark'=>$_data['remark'],
						'create_date'=>date("Y-m-d H:i:s"),
						);
				$id = $this->insert($_arr);
				
				$this->_name='rms_study_history';
				$arr= array(
						'user_id'=>$this->getUserId(),
						'stu_id'=>$id,
						'stu_type' => 2,
						'stu_code'=>$_data['student_id'],
						//'level'=>$_data['level'],
						
						'degree'=>$_data['degree'],
						'grade'=>$_data['grade'],
						
						'from_time'=>$_data['from_time'],
						'to_time'=>$_data['to_time'],
						'start_date'=>$_data['start_date'],
						'type_time'=>$_data['type_time'],
						'status'=>$_data['status'],
						'remark'=>$_data['remark']
						);
				
				$this->insert($arr);
				//$_db->commit();
			}catch(Exception $e){
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				echo $e->getMessage();
			}
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
					
					'degree' => $_data['degree'],
					'grade'=>$_data['grade'],
					'academic_year'=>$_data['academic_year'],
					
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
					//'level'=>$_data['level'],
			
					'degree' => $_data['degree'],
					'grade'=>$_data['grade'],
					
					'from_time'=>$_data['from_time'],
					'to_time'=>$_data['to_time'],
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
	function getStudyHishotryById($id){
		$db = $this->getAdapter();
		$sql="SELECT * FROM rms_study_history where".$id;
		//print_r($db->fetchRow($sql)); exit();
		return $db->fetchRow($sql);
	}
	function getAllGrade($grade_id){
		$db = $this->getAdapter();
		$sql = "SELECT major_id As id,major_enname As name FROM rms_major WHERE dept_id=".$grade_id;
		$order=' ORDER BY id DESC';
		return $db->fetchAll($sql.$order);
	}

	function getStudentInfoById($stu_id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM `rms_student` WHERE stu_id=$stu_id LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	function getSearchStudent($data){
		$db=$this->getAdapter();
		$sql="SELECT stu_id ,stu_code,stu_enname,stu_khname,sex,degree,grade from rms_student ";
		 $sql.= ' WHERE stu_type=2 and is_subspend=0 and `status`=1 AND is_setgroup = 0 ';
		 if($data['grade']>0){
		 	$sql.=" AND grade =".$data['grade'];
		 }
// 		 if($data['session']>0){
// 		 	$sql.=" AND session =".$data['session'];
// 		 }
		 
		return $db->fetchAll($sql);
	}

	public function getNewAccountNumber($newid,$stu_type){
		$db = $this->getAdapter();
		$sql="  SELECT COUNT(stu_id)  FROM rms_student WHERE stu_type=$stu_type";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$new_acc_no=100+$new_acc_no;
		$pre='GEP';
		$acc_no= strlen((int)$acc_no+1);
		for($i = $acc_no;$i<5;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}
	
	
	function getAllYear(){
		$db = $this->getAdapter();
		$sql = "select id,CONCAT(from_academic,'-',to_academic,'(',generation,')')as years from rms_tuitionfee ";
		return $db->fetchAll($sql);
	}
	
	function getAllDegree(){
		$db = $this->getAdapter();
		$sql = "select dept_id as id , en_name as name  from rms_dept where dept_id NOT IN(1,2,3,4) ";
		return $db->fetchAll($sql);
	}
	
	public function getStudentGepById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_student WHERE stu_id =".$id;
		return $db->fetchRow($sql);
	}
	
	public function getStudetnGepdetail($id){
		$_db = $this->getAdapter();
		$sql="SELECT * From rms_study_history where stu_id = ".$id;
		return $_db->fetchRow($sql);
	}
	
	
	
	
}

