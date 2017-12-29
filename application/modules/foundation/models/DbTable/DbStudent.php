<?php

class Foundation_Model_DbTable_DbStudent extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'rms_student';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	
	public function getUserLevel(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->level;
	}
	
	public function getBranchId(){
		$session_user = new Zend_Session_Namespace('auth');
		return $session_user->branch_id;
	}
	
	public function getAllStudent($search){
		$_db = $this->getAdapter();
		$from_date =(empty($search['start_date']))? '1': "s.create_date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': "s.create_date <= '".$search['end_date']." 23:59:59'";
		$where = " AND ".$from_date." AND ".$to_date;
		$sql = "SELECT  
				 s.stu_id,
				(select branch_namekh from rms_branch where br_id = s.branch_id) as branch,
				 s.stu_code,
				 s.stu_khname,
				 s.stu_enname,
				(SELECT name_kh FROM `rms_view` WHERE TYPE=2 AND key_code = s.sex) AS sex,nationality ,
				
				(SELECT CONCAT(from_academic,'-',to_academic,'(',generation,')',' ',(select name_en from rms_view where type=7 and key_code = time)) FROM rms_tuitionfee WHERE rms_tuitionfee.id=s.academic_year) AS academic,
				
				(SELECT `en_name` FROM `rms_dept` WHERE `dept_id`=s.degree) AS degree,
				
				(SELECT `major_enname` FROM `rms_major` WHERE `major_id`=s.grade) AS grade,
				
				(SELECT	`rms_view`.`name_en` FROM `rms_view` WHERE ((`rms_view`.`type` = 4) AND (`rms_view`.`key_code` = `s`.`session`)) LIMIT 1) AS `session`,
				(select room_name from rms_room where room_id = s.room) as room,
				(SELECT name_kh FROM `rms_view` WHERE TYPE=1 AND key_code = STATUS) AS status
				
				FROM rms_student AS s  WHERE  s.is_subspend=0 AND s.status = 1 AND s.stu_type IN (1,2,3)
			";
		$orderby = " ORDER BY stu_id DESC ";
		if(empty($search)){
			return $_db->fetchAll($sql.$orderby);
		}
		if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['adv_search']));
			$s_where[]="stu_code LIKE '%{$s_search}%'";
			$s_where[]="(select CONCAT(from_academic,'-',to_academic,'(',generation,')') from rms_tuitionfee where rms_tuitionfee.id=s.academic_year) LIKE '%{$s_search}%'";
			$s_where[]="stu_khname LIKE '%{$s_search}%'";
			$s_where[]="stu_enname LIKE '%{$s_search}%'";
			$s_where[]="(SELECT en_name FROM rms_dept WHERE rms_dept.dept_id=s.degree) LIKE '%{$s_search}%'";
			$s_where[]="(SELECT major_enname FROM rms_major WHERE rms_major.major_id=s.grade) LIKE '%{$s_search}%'";
			$s_where[]="(SELECT	rms_view.name_en FROM rms_view WHERE rms_view.type = 4 AND rms_view.key_code = s.session) LIKE '%{$s_search}%'";
			$s_where[]="(SELECT `major_enname` FROM `rms_major` WHERE `major_id`=grade) LIKE '%{$s_search}%'";
			$s_where[]="(SELECT name_kh FROM `rms_view` WHERE type=2 AND key_code = sex) LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		if(!empty($search['branch'])){
			$where.=" AND s.branch_id=".$search['branch'];
		}
		if(!empty($search['study_year'])){
			$where.=" AND s.academic_year=".$search['study_year'];
		}
		if(!empty($search['degree_all'])){
			$where.=" AND s.degree=".$search['degree_all'];
		}
		if(!empty($search['grade_all'])){
			$where.=" AND s.grade=".$search['grade_all'];
		}
		if(!empty($search['session'])){
			$where.=" AND s.session=".$search['session'];
		}
		if(!empty($search['room'])){
			$where.=" AND s.room=".$search['room'];
		}
		if(!empty($search['user'])){
			$where.=" AND s.user_id=".$search['user'];
		}
		//print_r($sql.$where);
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
	
	function getStudentExist($name_en,$name_kh,$sex,$grade,$dob,$session){
		$db = $this->getAdapter();
		$sql = "select * from rms_student where stu_enname="."'$name_en'"." and stu_khname="."'$name_kh'"." and sex=".$sex." and grade=".$grade." and dob="."'$dob'"." and session=".$session;                          
		return $db->fetchRow($sql);
	}
	public function addStudent($_data){
			$id = $this->getStudentExist($_data['name_en'],$_data['name_kh'],$_data['sex'],$_data['grade'],$_data['date_of_birth'],$_data['session']);	
			if(!empty($id)){
				return -1;
			}
			
			if($_data['degree']<=3){
				$stu_type=1;
			}else if($_data['degree']>3 && $_data['degree']<=7){
				$stu_type=2;
			}else{
				$stu_type=3;
			}
			
			$user_level = $this->getUserLevel();
			if($user_level==1 || $user_level==2){
				$branch = $_data['branch'];
			}else{
				$branch = $this->getBranchId();
			}
			
			$code = new Registrar_Model_DbTable_DbRegister();
			$stu_code = $code->getNewAccountNumber($_data['degree'], $branch);
			
			
			try{	
				$_db= $this->getAdapter();
				$_arr= array(
						'branch_id'		=>$branch,
						'stu_type'		=>$stu_type,
						'user_id'		=>$this->getUserId(),
						
						'stu_enname'	=>$_data['name_en'],
						'stu_khname'	=>$_data['name_kh'],
						'sex'			=>$_data['sex'],
						'nationality'	=>$_data['studen_national'],
						'dob'			=>$_data['date_of_birth'],
						'tel'			=>$_data['st_phone'],
						'email'			=>$_data['st_email'],
						'pob'			=>$_data['place_of_birth'],
						
						'home_num'		=>$_data['home_note'],
						'street_num'	=>$_data['way_note'],
						'village_name'	=>$_data['village_note'],
						'commune_name'	=>$_data['commun_note'],
						'district_name'	=>$_data['distric_note'],
						'province_id'	=>$_data['student_province'],
						
						'academic_year'	=>$_data['academic_year'],
						'stu_code'		=>$stu_code,
						'degree'		=>$_data['degree'],
						'grade'			=>$_data['grade'],
						'room'			=>$_data['room'],
						'session'		=>$_data['session'],
						//'school_location'=>$_data['school_location'],
				
						'father_enname'	=>$_data['fa_name_en'],
						'father_khname'	=>$_data['fa_name_kh'],
						'father_dob'	=>$_data['fa_dob'],
						'father_nation'	=>$_data['fa_national'],
						'father_job'	=>$_data['fa_job'],
						'father_phone'	=>$_data['fa_phone'],
						
						'mother_khname'	=>$_data['mom_name_kh'],
						'mother_enname'	=>$_data['mom_name_en'],
						'mother_dob'	=>$_data['mom_dob'],
						'mother_nation'	=>$_data['mom_nation'],
						'mother_job'	=>$_data['mo_job'],
						'mother_phone'	=>$_data['mon_phone'],

						'guardian_enname'=>$_data['guardian_name_en'],
						'guardian_khname'=>$_data['guardian_name_kh'],
						'guardian_dob'	=>$_data['guardian_dob'],
						'guardian_nation'=>$_data['guardian_national'],
// 						'guardian_document'=>$_data['guardian_identity'],
						'guardian_job'	=>$_data['gu_job'],
						'guardian_tel'	=>$_data['guardian_phone'],
// 						'guardian_email'=>$_data['guardian_email'],
						
						'status'		=>$_data['status'],
						'remark'		=>$_data['remark'],
						'create_date'	=>date("Y-m-d H:i:s"),
						
						);
				$id = $this->insert($_arr);
				
				$this->_name='rms_study_history';
				$arr= array(
						'branch_id'		=>$branch,
						'stu_type'		=>$stu_type,
						'stu_id'	=>$id,
						
						'stu_code'	=>$stu_code,
						'academic_year'	=>$_data['academic_year'],
						'degree'	=>$_data['degree'],
						'grade'		=>$_data['grade'],
						'room'			=>$_data['room'],
						'session'	=>$_data['session'],
						
						//'status'	=>$_data['status'],
// 						'remark'	=>$_data['remark'],
						'user_id'	=>$this->getUserId(),
						);
				
				$this->insert($arr);
				//$_db->commit();
			}catch(Exception $e){
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				echo $e->getMessage();
			}
	}
	public function updateStudent($_data){
		$db = $this->getAdapter();
		
		if($_data['degree']<=3){
			$stu_type=1;
		}else if($_data['degree']>3 && $_data['degree']<=7){
			$stu_type=2;
		}else{
			$stu_type=3;
		}
		
		try{	
			$_arr=array(
					'stu_type'		=>$stu_type,
					'branch_id'		=>$_data['branch'],
					
					'stu_enname'	=>$_data['name_en'],
					'stu_khname'	=>$_data['name_kh'],
					'sex'			=>$_data['sex'],
					'nationality'	=>$_data['studen_national'],
					'dob'			=>$_data['date_of_birth'],
					'tel'			=>$_data['st_phone'],
					'email'			=>$_data['st_email'],
					'pob'			=>$_data['place_of_birth'],
					
					'home_num'		=>$_data['home_note'],
					'street_num'	=>$_data['way_note'],
					'village_name'	=>$_data['village_note'],
					'commune_name'	=>$_data['commun_note'],
					'district_name'	=>$_data['distric_note'],
					'province_id'	=>$_data['student_province'],
					
					'academic_year'	=>$_data['academic_year'],
					'stu_code'		=>$_data['student_id'],
					'degree'		=>$_data['degree'],
					'grade'			=>$_data['grade'],
					'room'			=>$_data['room'],
					'session'		=>$_data['session'],
					//'school_location'=>$_data['school_location'],
					
					'father_enname'	=>$_data['fa_name_en'],
					'father_khname'	=>$_data['fa_name_kh'],
					'father_dob'	=>$_data['fa_dob'],
					'father_nation'	=>$_data['fa_national'],					
					'father_job'	=>$_data['fa_job'],					
					'father_phone'	=>$_data['fa_phone'],
					
					'mother_khname'	=>$_data['mom_name_kh'],
					'mother_enname'	=>$_data['mom_name_en'],
					'mother_dob'	=>$_data['mom_dob'],
					'mother_nation'	=>$_data['mom_nation'],
					'mother_job'	=>$_data['mo_job'],
					'mother_phone'	=>$_data['mon_phone'],
					
					'guardian_enname'=>$_data['guardian_name_en'],
					'guardian_khname'=>$_data['guardian_name_kh'],
					'guardian_dob'	=>$_data['guardian_dob'],
					'guardian_nation'=>$_data['guardian_national'],
// 					'guardian_document'=>$_data['guardian_identity'],
					'guardian_job'	=>$_data['gu_job'],
					'guardian_tel'	=>$_data['guardian_phone'],
// 					'guardian_email'=>$_data['guardian_email'],
					
					'status'		=>$_data['status'],
					'remark'		=>$_data['remark'],
					'user_id'		=>$this->getUserId(),
					
					);
			$where=$this->getAdapter()->quoteInto("stu_id=?", $_data["id"]);
			$this->update($_arr, $where);
			
			
			$this->_name='rms_study_history';
			$arr= array(
					'stu_type'		=>$stu_type,
					'branch_id'		=>$_data['branch'],
					
					'stu_code'=>$_data['student_id'],
					'academic_year'	=>$_data['academic_year'],
					'degree'=>$_data['degree'],
					'grade'=>$_data['grade'],
					'room'=>$_data['room'],
					'session'=>$_data['session'],
					'status'=>$_data['status'],
					'user_id'=>$this->getUserId(),
					
			);
			$where=$this->getAdapter()->quoteInto("stu_id=?", $_data["id"]);
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
	function getSearchStudent($search){
		$db=$this->getAdapter();
		$sql="SELECT stu_id ,stu_code,stu_enname,stu_khname,sex,degree,grade,academic_year from rms_student ";
		$sql.= ' WHERE `status`=1 AND is_setgroup = 0 and is_subspend=0 and stu_type=1 ';
		 if(!empty($search['grade'])){
		 	$sql.=" AND grade =".$search['grade'];
		 }
		 if(!empty($search['session'])){
		 	$sql.=" AND session =".$search['session'];
		 }
		 if(!empty($search['academy'])){
		 	$sql.=" AND academic_year =".$search['academy'];
		 }
		return $db->fetchAll($sql);
	}

	public function getNewAccountNumber($newid,$stu_type){
		$db = $this->getAdapter();
		$sql="  SELECT COUNT(stu_id)  FROM rms_student WHERE stu_type IN (1,3)";
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
	
	
	function getAllYear(){
		$db = $this->getAdapter();
		$sql = "select id,CONCAT(from_academic,'-',to_academic,'(',(select branch_namekh from rms_branch where br_id = branch_id),')')as years,(select name_en from rms_view where type=7 and key_code=time) as time from rms_tuitionfee ";
		$group = " group by from_academic,to_academic,generation,time ";
		return $db->fetchAll($sql);
	}
	public function getAllDegree(){
		$db = $this->getAdapter();
		$sql ="SELECT dept_id AS id, en_name AS NAME,en_name,dept_id,shortcut FROM rms_dept WHERE is_active=1  ORDER BY id DESC";
		return $db->fetchAll($sql);
	}
	
	public function getAllRoom(){
		$db = $this->getAdapter();
		$sql ="SELECT room_id AS id, room_name AS name FROM rms_room WHERE is_active=1 ";
		return $db->fetchAll($sql);
	}
	
	
	
	
	
	
	
}







