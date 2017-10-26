<?php

class Foundation_Model_DbTable_DbStudentDrop extends Zend_Db_Table_Abstract
{
	protected $_name = 'rms_student_drop';
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
		
		$db=new Application_Model_DbTable_DbGlobal();
		$branch_id = $db->getAccessPermission();
		
		$sql = "SELECT stu_id,stu_code FROM `rms_student` where status = 1  $branch_id  ";
		$orderby = " ORDER BY stu_code ";
		return $_db->fetchAll($sql.$orderby);
	}
	
	public function getAllStudentNameEdit(){
		$_db = $this->getAdapter();
	
		$db=new Application_Model_DbTable_DbGlobal();
		$branch_id = $db->getAccessPermission();
	
		$sql = "SELECT stu_id,CONCAT(stu_khname,'-',stu_enname) as name FROM `rms_student` where status = 1  $branch_id  ";
		$orderby = " ORDER BY stu_enname ";
		return $_db->fetchAll($sql.$orderby);
	}
	public function getAllStudentDrop($search){
		$_db = $this->getAdapter();
		
		$db = new Application_Model_DbTable_DbGlobal();
		$branch_id = $db->getAccessPermission();
		
		$sql = "SELECT 
					id,
					(SELECT stu_code FROM `rms_student` WHERE `rms_student`.`stu_id`=`sd`.`stu_id` limit 1) AS code,
					(SELECT stu_khname FROM `rms_student` WHERE `rms_student`.`stu_id`=`sd`.`stu_id` limit 1) AS kh_name,
					(SELECT stu_enname FROM `rms_student` WHERE `rms_student`.`stu_id`=`sd`.`stu_id` limit 1) AS en_name,
					(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=2 and `rms_view`.`key_code`=s.sex)AS sex,
					
					(SELECT CONCAT(from_academic,'-',to_academic,'(',generation,')') FROM rms_tuitionfee WHERE rms_tuitionfee.id=s.academic_year) as academic_year,
					(select en_name from rms_dept where dept_id = s.degree ) as degree,
					(SELECT `major_enname` FROM `rms_major` WHERE `major_id`=s.grade ) AS grade,
					(SELECT	`rms_view`.`name_en` FROM `rms_view` WHERE `rms_view`.`type` = 4 AND `rms_view`.`key_code` = s.session ) AS session,
					
					(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=`sd`.`type` limit 1) as type,
					reason,
					date 
				from 
					`rms_student_drop` as sd,
					rms_student as s 
				where 
					s.stu_id=sd.stu_id 
					and sd.status=1 
					and drop_from=1
					$branch_id
				";
		
		$order_by=" order by sd.id DESC";
		
		$where='';
		
		if(empty($search)){
			return $_db->fetchAll($sql.$order_by);
		}
		
		if(!empty($search['title'])){
			$s_where = array();
			$s_search = addslashes(trim($search['title']));
			$s_where[] = " s.stu_code LIKE '%{$s_search}%'";
			$s_where[] = " s.stu_khname LIKE '%{$s_search}%'";
			$s_where[] = " s.stu_enname LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=`sd`.`type` limit 1) LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		if(!empty($search['branch'])){
			$where.=" AND s.branch_id = ".$search['branch'];
		}
		if(!empty($search['study_year'])){
			$where.=" AND s.academic_year = ".$search['study_year'];
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
		return $_db->fetchAll($sql.$where.$order_by);
	}
	public function getStudentDropById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_student_drop WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	public function addStudentDrop($_data){
			try{	
				$_db= $this->getAdapter();
				$_db->beginTransaction();
				
				$_arr= array(
						'user_id'	=>$this->getUserId(),
						'stu_id'	=>$_data['studentid'],
						'type'		=>$_data['type'],
						'status'	=>$_data['status'],
						'date'		=>$_data['datestop'],
						'reason'	=>$_data['reason'],
						//'note'		=>$_data['note'],
						);
				$id = $this->insert($_arr);
				
				$this->_name='rms_student';
				$where=" stu_id=".$_data['studentid'];
				if($_data['status']==1){
					$arr=array(
						'is_subspend'	=>	$_data['type'],
					);
				}else{
					$arr=array(
						'is_subspend'	=>	0,
					);
				}
				$this->update($arr, $where);
				
				
				$this->_name='rms_student_payment';
				$sql="select id from rms_student_payment where student_id = ".$_data['studentid']." and payfor_type IN(1,2,6) order by id DESC limit 1";
				$payment_id = $_db->fetchOne($sql);
				
				if(!empty($payment_id)){
					
					$where=" id = $payment_id ";
					if($_data['status']==1){
						$arr=array(
								'is_subspend'	=>	$_data['type'],
						);
					}else{
						$arr=array(
								'is_subspend'	=>	0,
						);
					}
					$this->update($arr, $where);
				}
				
				$_db->commit();
			}catch(Exception $e){
				echo $e->getMessage();exit();
			}
	}
	public function updateStudentDrop($_data){
		$db= $this->getAdapter();
		$db->beginTransaction();
		try{	
			$_arr=array(
					'user_id'	=>$this->getUserId(),
					'stu_id'	=>$_data['studentid'],
					'type'		=>$_data['type'],
					'date'		=>$_data['datestop'],
					'reason'	=>$_data['reason'],
					//'note'		=>$_data['note'],
					'status'	=>$_data['status'],
					);
			$where=$this->getAdapter()->quoteInto("id=?", $_data["id"]);
			$this->update($_arr, $where);
			
			$this->_name='rms_student';
			
			$where=" stu_id=".$_data['studentid'];
			
			if($_data['status']==0){
				$arr=array(
						'is_subspend'	=>	0,
				);
			}else{
				$arr=array(
						'is_subspend'	=>	$_data['type'],
				);
			}
			$this->update($arr, $where);
			
			
			$this->_name='rms_student_payment';
			$sql="select id from rms_student_payment where student_id = ".$_data['studentid']." order by id DESC limit 1";
			$payment_id = $db->fetchOne($sql);
			
			if(!empty($payment_id)){
					
				$where=" id = $payment_id ";
				if($_data['status']==1){
					$arr=array(
							'is_subspend'	=>	$_data['type'],
					);
				}else{
					$arr=array(
							'is_subspend'	=>	0,
					);
				}
				$this->update($arr, $where);
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
		$sql = "SELECT CONCAT(st.stu_khname,' - ',st.stu_enname) as name,st.sex,
			(SELECT CONCAT(from_academic,'-',to_academic,'(',generation,')') FROM rms_tuitionfee WHERE rms_tuitionfee.id=st.academic_year) as academic_year, 
			(SELECT `major_enname` FROM `rms_major` WHERE `major_id`=st.grade ) AS grade,
			(SELECT	`rms_view`.`name_en` FROM `rms_view` WHERE `rms_view`.`type` = 4 AND `rms_view`.`key_code` = st.session ) AS session
			FROM `rms_student` as st WHERE stu_id=$stu_id LIMIT 1 ";
		return $db->fetchRow($sql);
	}
}

