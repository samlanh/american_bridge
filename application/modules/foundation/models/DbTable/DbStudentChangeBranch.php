<?php

class Foundation_Model_DbTable_DbStudentChangeBranch extends Zend_Db_Table_Abstract
{
	protected $_name = 'rms_student_change_branch';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	public function getAllStudentID(){
		$_db = $this->getAdapter();
		$sql = "SELECT stu_id,stu_code FROM `rms_student` where status = 1 and is_subspend=0 ";
		$orderby = " ORDER BY stu_code ";
		return $_db->fetchAll($sql.$orderby);		
	}
	
	function getAllStudentName(){
		$_db = $this->getAdapter();
		$sql = "SELECT stu_id,CONCAT(stu_khname,'-',stu_enname) as name FROM `rms_student` where status = 1 and is_subspend=0 ";
		$orderby = " ORDER BY stu_code ";
		return $_db->fetchAll($sql.$orderby);
	}
	
	public function getAllStudentIDEdit(){
		$_db = $this->getAdapter();
		$sql = "SELECT stu_id,stu_code FROM `rms_student` where stu_type=1 and status = 1 ";
		//$orderby = " ORDER BY stu_code ";
		return $_db->fetchAll($sql);
	}
	public function getAllStudentChangeBranch($search){
		$_db = $this->getAdapter();
		$sql = "select 
					cb.id,
					s.stu_code,
					CONCAT(s.stu_khname,'-',s.stu_enname) as name,
					(select name_kh from rms_view where type=2 and key_code=s.sex) as sex,
					(select CONCAT(major_enname,'(',(select shortcut from rms_dept as d where d.dept_id=rms_major.dept_id),')') from rms_major where major_id=s.grade) as grade,
					(select branch_namekh from rms_branch where br_id = cb.old_branch) as old_branch,
					(select branch_namekh from rms_branch where br_id = cb.new_branch) as new_branch,
					reason,
					(select CONCAT(first_name,'-',last_name) from rms_users as u where u.id = cb.user_id) as user,
					cb.create_date
				from 
					rms_student_change_branch as cb,
					rms_student as s
				where
					cb.stu_id = s.stu_id
					and cb.status=1
			";
		$order_by=" order by id DESC";
		$where='';
		if(empty($search)){
			return $_db->fetchAll($sql.$order_by);
		}
		if(!empty($search['title'])){
			$s_where = array();
			$s_search = addslashes(trim($search['title']));
			$s_where[] = " (SELECT stu_code FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_drop`.`stu_id` limit 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT stu_khname FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_drop`.`stu_id` limit 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT stu_enname FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_drop`.`stu_id` limit 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=`rms_student_drop`.`type` limit 1) LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		if(!empty($search['study_year'])){
			$where.=" AND rms_student.academic_year = ".$search['study_year'];
		}
		if(!empty($search['grade_bac'])){
			$where.=" AND rms_student.grade=".$search['grade_bac'];
		}
		if(!empty($search['session'])){
			$where.=" AND rms_student.session=".$search['session'];
		}
		return $_db->fetchAll($sql.$where.$order_by);
	}
	public function getStudentChangeBranchById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_student_change_branch WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	
	
	public function addStudentChangeBranch($_data){
		try{	
			$_db = $this->getAdapter();
			$_arr = array(
					'stu_id'		=>$_data['studentid'],
					'old_branch'	=>$_data['old_branch'],
					'new_branch'	=>$_data['new_branch'],
					'reason'		=>$_data['reason'],
					'create_date'	=>date('Y-m-d'),
					'user_id'		=>$this->getUserId(),
				);
			$id = $this->insert($_arr);
			
		// update student branch to new branch	
		
			$this->_name='rms_student';
			$where=" stu_id=".$_data['studentid'];
			$arr=array(
				'branch_id'	=>	$_data['new_branch'],
			);
			$this->update($arr, $where);
			
			
			$this->_name='rms_study_history';
			$sql="select * from rms_study_history where is_finished_grade = 0 and stu_id = ".$_data['studentid'];
			$study_history = $_db->fetchRow($sql);
			
			if(!empty($study_history)){
				
			//	update old study_history to finish 
				$arra = array(
						'is_finished_grade'	=>1,
						'change_branch'		=>1,
						);
				$where = " id = ".$study_history['id'];
				$this->update($arra, $where);
				
			//  insert new study_history with new branch but old study info

				$array = array(
						'branch_id'		=>$_data['new_branch'],
						
						'stu_id'		=>$study_history['stu_id'],
						'stu_type'		=>$study_history['stu_type'],
						'stu_code'		=>$study_history['stu_code'],
						
						'academic_year'	=>$study_history['academic_year'],
						'degree'		=>$study_history['degree'],
						'grade'			=>$study_history['grade'],
						'session'		=>$study_history['session'],
						'room'			=>$study_history['room'],
						
						'reg_from'		=>$study_history['reg_from'],
						'id_record_finished'=>$study_history['id'],
						
						'user_id'		=>$this->getUserId(),
						);
				$this->insert($array);
				
			}
			
			$_db->commit();
			
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function updateStudentChangeBranch($_data){
		$db= $this->getAdapter();
		try{	
			if($_data['status']==0){
				$this->_name='rms_student_change_branch';
				$arr = array(
						'status'=>0,
						);
				$where=" id = ".$_data['id'];
				$this->update($arr, $where);
				
			// update student branch to old_branch	
				$this->_name='rms_student';
				$where=" stu_id=".$_data['studentid'];
				$arr=array(
						'branch_id'	=>	$_data['old_branch'],
				);
				$this->update($arr, $where);
				
			// update study_history

				$this->_name='rms_study_history';
				$sql="select id_record_finished,id from rms_study_history where is_finished_grade = 0 and status=1  and stu_id = ".$_data['studentid']." limit 1";
				$study_history = $db->fetchRow($sql);
				if(!empty($study_history)){
				
				// update old record back to using	
					$arra = array(
							'is_finished_grade'=>0,
							);
					$where = "id = ".$study_history['id_record_finished'];
					$this->update($arra, $where);
					
				// update new record to not use(deactive)	
					$array=array(
							'status'=>0,
							);
					$where = " id = ".$study_history['id'];
					$this->update($array, $where);
				}
				
			}else{
				$this->_name='rms_student_change_branch';
				$_arr=array(
					'new_branch'	=>$_data['new_branch'],
					'reason'		=>$_data['reason'],
					'user_id'		=>$this->getUserId(),
						);
				$where=$this->getAdapter()->quoteInto("id=?", $_data["id"]);
				$this->update($_arr, $where);
				
				
				
			// update student branch to new_branch that updated	
				$this->_name='rms_student';
				$where=" stu_id=".$_data['studentid'];
				$arr=array(
						'branch_id'	=>	$_data['new_branch'],
				);
				$this->update($arr, $where);
			
				
				$this->_name='rms_study_history';
				$array=array(
						'branch_id'=>$_data['new_branch'],
				);
				$where = " stu_id = ".$_data['studentid']." and is_finished_grade = 0 and status=1 " ;
				$this->update($array, $where);
			}
			
			$db->commit();
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	function getAllGrade($grade_id){
		$db = $this->getAdapter();
		$sql = "SELECT major_id As id,major_enname As name FROM rms_major WHERE dept_id=".$grade_id;
		$order=' ORDER BY id DESC';
		return $db->fetchAll($sql.$order);
	}
	function getStudentInfoById($stu_id){
		$db = $this->getAdapter();
		$sql = "SELECT CONCAT(st.stu_khname,' - ',st.stu_enname) as name,st.sex,st.branch_id,
			(SELECT CONCAT(from_academic,'-',to_academic,'(',generation,')') FROM rms_tuitionfee WHERE rms_tuitionfee.id=st.academic_year) as academic_year, 
			(SELECT `major_enname` FROM `rms_major` WHERE `major_id`=st.grade ) AS grade,
			(SELECT	`rms_view`.`name_en` FROM `rms_view` WHERE `rms_view`.`type` = 4 AND `rms_view`.`key_code` = st.session ) AS session
			FROM `rms_student` as st WHERE stu_id=$stu_id LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	
	
	function getAllBranch(){	
		$db = $this->getAdapter();
		$sql = "SELECT br_id As id,branch_namekh as name FROM rms_branch WHERE status = 1 ";
		return $db->fetchAll($sql);
	}
	
	
	
	
	
	
	
}












