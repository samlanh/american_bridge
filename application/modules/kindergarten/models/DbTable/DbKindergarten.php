<?php

class Kindergarten_Model_DbTable_DbKindergarten extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'rms_student';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	
	function getExistRecord($name_en,$name_kh,$sex,$grade,$dob){
		$db = $this->getAdapter();
		$sql = "select * from rms_student where stu_enname="."'$name_en'"." and stu_khname="."'$name_kh'"." and sex=".$sex." and grade=".$grade." and dob="."'$dob'";                  
		return $db->fetchRow($sql);
	}
	
	public function addKindergarten($data,$num){
		$db= $this->getAdapter();

		$exist = $this->getExistRecord($data['name_en'],$data['name_kh'],$data['sex'],$data['grade'],$data['dob']);
		
		if(!empty($exist)){
			return -1;
		}
		
		try{
			$arr = array(
					'stu_type'		=>1,
					'user_id'		=>$this->getUserId(),
					
					'stu_enname'	=>$data['name_en'],
					'stu_khname'	=>$data['name_kh'],
					'sex'			=>$data['sex'],
					'nationality'	=>$data['nationality'],
					'dob'			=>$data['dob'],
					'address'		=>$data['pob'],
					
					'home_num'	 	=>$data['home'],
					'street_num'	=>$data['street'],
					'village_name'	=>$data['village'],
					'commune_name'	=>$data['commun'],
					'district_name'	=>$data['distric'],
					'province_id'	=>$data['province'],
					
					'academic_year'	=>$data['academic_year'],
					'stu_code'		=>$num,
					'degree'		=>1,
					'grade'			=>$data['grade'],
					'session'		=>$data['session'],
					'status'		=>$data['status'],
					'remark'		=>$data['remarks'],
					
					'father_enname'	=>$data['fa_name_en'],
					'father_khname'	=>$data['fa_name_kh'],
					'father_old'	=>$data['fa_age'],
					'father_nation'	=>$data['fa_nationality'],
					'father_job'	=>$data['fa_job'],
					'father_phone'	=>$data['fa_phone'],
					
					'mother_khname'	=>$data['mo_name_kh'],
					'mother_enname'	=>$data['mo_name_en'],
					'mother_old'	=>$data['mo_age'],
					'mother_nation'	=>$data['mo_nationality'],
					'mother_job'	=>$data['mo_job'],
					'mother_phone'	=>$data['mo_phone'],
					
					'tr_name'		=>$data['tr_name'],
					'tr_identity_no'=>$data['tr_identity_no'],
					'tr_relate_to_stu'=>$data['tr_relationship_to_child'],
					'tr_phone'		=>$data['tr_phone'],
					'tr_address'	=>$data['tr_address'],
					
					'ur_name'		=>$data['ur_name'],
					'ur_identity_no'=>$data['ur_identity_no'],
					'ur_relate_to_stu'=>$data['ur_relationship_to_child'],
					'ur_phone'		=>$data['ur_phone'],
					'ur_address'	=>$data['ur_address'],
					
					'create_date'	=>date("Y-m-d H:i:s"),
					
					);
			return $this->insert($arr);
		}catch(Exception $e){
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				echo $e->getMessage();
		}	
	}
	
	public function updateKindergarten($data,$id){
	
		try{
			$db= $this->getAdapter();
			$arr = array(
					'stu_type'		=>3,
					'user_id'		=>$this->getUserId(),
						
					'stu_enname'	=>$data['name_en'],
					'stu_khname'	=>$data['name_kh'],
					'sex'			=>$data['sex'],
					'nationality'	=>$data['nationality'],
					'dob'			=>$data['dob'],
					'address'		=>$data['pob'],
						
					'home_num'	 	=>$data['home'],
					'street_num'	=>$data['street'],
					'village_name'	=>$data['village'],
					'commune_name'	=>$data['commun'],
					'district_name'	=>$data['distric'],
					'province_id'	=>$data['province'],
						
					'academic_year'	=>$data['academic_year'],
					'stu_code'		=>$data['student_id'],
					//'degree'		=>$data['grade'],
					'grade'			=>$data['grade'],
					'session'		=>$data['session'],
					'status'		=>$data['status'],
					'remark'		=>$data['remarks'],
						
					'father_enname'	=>$data['fa_name_en'],
					'father_khname'	=>$data['fa_name_kh'],
					'father_old'	=>$data['fa_age'],
					'father_nation'	=>$data['fa_nationality'],
					'father_job'	=>$data['fa_job'],
					'father_phone'	=>$data['fa_phone'],
						
					'mother_khname'	=>$data['mo_name_kh'],
					'mother_enname'	=>$data['mo_name_en'],
					'mother_old'	=>$data['mo_age'],
					'mother_nation'	=>$data['mo_nationality'],
					'mother_job'	=>$data['mo_job'],
					'mother_phone'	=>$data['mo_phone'],
						
				 	'tr_name'		=>$data['tr_name'],
				 	'tr_identity_no'=>$data['tr_identity_no'],
				 	'tr_relate_to_stu'=>$data['tr_relationship_to_child'],
				 	'tr_phone'		=>$data['tr_phone'],
				 	'tr_address'	=>$data['tr_address'],
						
				 	'ur_name'		=>$data['ur_name'],
				 	'ur_identity_no'=>$data['ur_identity_no'],
				 	'ur_relate_to_stu'=>$data['ur_relationship_to_child'],
				 	'ur_phone'		=>$data['ur_phone'],
				 	'ur_address'	=>$data['ur_address'],
					//'create_date'	=>date("Y-m-d H:i:s"),
			);
			
			$where=" stu_id = ".$id;
			$db->getProfiler()->setEnabled(true);
			$this->update($arr, $where);
			Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
			Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
			$db->getProfiler()->setEnabled(false);//exit();
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	
	public function getAllDegreeLanguage(){
		$db = $this->getAdapter();
		$sql = "SELECT id,`title`,`modify_date`,note,(SELECT `name_kh` FROM`rms_view` WHERE `type`=1 AND `key_code`= status) as status,(SELECT CONCAT(`last_name`,' ',`first_name`) FROM `rms_users` WHERE id=`user_id`) FROM `rms_degree_language` WHERE status = 1 ";
		return $db->fetchAll($sql);
	}
	public function editDegree($data,$id){
		try{$db= $this->getAdapter();
		$arr = array(
				'title'=>$data['language_title'],
				'modify_date'=> date("Y-m-d"),
				'status'=> $data['status'],
				'user_id'=>$this->getUserId(),
				'note'=>$data['note'],
		);
		$where = $this->getAdapter()->quoteInto("id=?",$id);
		$this->update($arr, $where);
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function getDegreeLanguageByID($id){
		$db = $this->getAdapter();
		$sql = 'SELECT `id`,`title`,`modify_date`,note,`status`,`user_id` FROM `rms_degree_language` WHERE id='.$id;
		return $db->fetchRow($sql);
	}
	
	function getAllYear(){
		$db = $this->getAdapter();
		$sql = "select id,CONCAT(from_academic,'-',to_academic,'(',generation,')')as years from rms_tuitionfee ";
		return $db->fetchAll($sql);
	}
	
	function getAllGrade(){
		$db = $this->getAdapter();
		$sql = "select major_enname as name,major_id as id  from rms_major where dept_id=1 ";
		return $db->fetchAll($sql);
	}
	
	public function getNewAccountNumber($newid,$stu_type){
		$db = $this->getAdapter();
		$sql="  SELECT COUNT(stu_id)  FROM rms_student WHERE stu_type=$stu_type";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$new_acc_no=100+$new_acc_no;
		$pre='';
		$acc_no= strlen((int)$acc_no+1);
		for($i = $acc_no;$i<5;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}
	
	
	function getAllOccupation(){
		$db = $this->getAdapter();
		$sql = "select occu_name as name,occupation_id as id  from rms_occupation";
		return $db->fetchAll($sql);
	}
	public function addNewOccupationPopup($_data){
		$this->_name='rms_occupation';
		$_arr=array(
				'occu_name'	  => $_data['occu_name'],
				'occu_enname'	  => $_data['occu_enname'],
				'create_date' => Zend_Date::now(),
				'status'   => $_data['status_j'],
				'user_id'	  => $this->getUserId()
		);
		return  $this->insert($_arr);
	}
	
	
	public function getAllStudent($search){
		$_db = $this->getAdapter();
		$sql = "SELECT stu_id,stu_code,stu_khname,stu_enname,
		(SELECT name_kh FROM `rms_view` WHERE type=2 AND key_code = sex) as sex,nationality,
		
		(select CONCAT(from_academic,'-',to_academic,'(',generation,')') from rms_tuitionfee where rms_tuitionfee.id=s.academic_year) as academic,
		
		(SELECT `en_name` FROM `rms_dept` WHERE `dept_id`=degree) AS degree,
		
		(SELECT `major_enname` FROM `rms_major` WHERE `major_id`=grade) AS grade,
		
		(SELECT	`rms_view`.`name_en` FROM `rms_view` WHERE ((`rms_view`.`type` = 4) AND (`rms_view`.`key_code` = `s`.`session`)) LIMIT 1) AS `session`,
		
		(SELECT `major_enname` FROM `rms_major` WHERE `major_id`=grade) as grade,
		(SELECT name_kh FROM `rms_view` WHERE type=1 AND key_code = status) as status
		FROM rms_student AS s where status = 1 AND stu_type = 3 and is_subspend=0  and degree=1 ";
		
		$orderby = " ORDER BY stu_id DESC ";
	
		$from_date =(empty($search['start_date']))? '1': "s.create_date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': "s.create_date <= '".$search['end_date']." 23:59:59'";
	
		$where = " AND ".$from_date." AND ".$to_date;
	
		if(empty($search)){
			return $_db->fetchAll($sql.$orderby);
		}
		if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['adv_search']));
			$s_where[]="stu_code LIKE '%{$s_search}%'";
			$s_where[]="(select CONCAT(from_academic,'-',to_academic,'(',generation,')') from rms_tuitionfee where rms_tuitionfee.id=s.academic_year) LIKE '%{$s_search}%'";
			$s_where[]="(SELECT en_name FROM rms_dept WHERE rms_dept.dept_id=s.degree) LIKE '%{$s_search}%'";
			$s_where[]="(SELECT major_enname FROM rms_major WHERE rms_major.major_id=s.grade) LIKE '%{$s_search}%'";
			$s_where[]="(SELECT	rms_view.name_en FROM rms_view WHERE rms_view.type = 4 AND rms_view.key_code = s.session) LIKE '%{$s_search}%'";
			$s_where[]="stu_khname LIKE '%{$s_search}%'";
			$s_where[]="stu_enname LIKE '%{$s_search}%'";
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
	
	
}

