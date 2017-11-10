<?php

class Kindergarten_Model_DbTable_DbStudentDrop extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'rms_student_drop';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	
	public function getAllStudentID(){
		$_db = $this->getAdapter();
		$sql = "SELECT stu_id,stu_code FROM `rms_student` where stu_type=3 and is_subspend=0 and status = 1 ";
		//$orderby = " ORDER BY stu_code ";
		return $_db->fetchAll($sql);		
	}
	
	public function getAllStudentIDEdit(){
		$_db = $this->getAdapter();
		$sql = "SELECT stu_id,stu_code FROM `rms_student` where stu_type=3 and status = 1 ";
		//$orderby = " ORDER BY stu_code ";
		return $_db->fetchAll($sql);
	}
	
	
	public function getAllStudentDrop($search){
		$_db = $this->getAdapter();
		$sql = "SELECT id,(SELECT stu_code FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_drop`.`stu_id` limit 1) AS code,
		(SELECT stu_khname FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_drop`.`stu_id` limit 1) AS kh_name,
		(SELECT stu_enname FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_drop`.`stu_id` limit 1) AS en_name,
		(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=2 and `rms_view`.`key_code`=(SELECT sex FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_drop`.`stu_id` limit 1))AS sex,
		(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=`rms_student_drop`.`type` limit 1) as type,
		reason,date,note from `rms_student_drop`,rms_student where rms_student.stu_id=rms_student_drop.stu_id and rms_student_drop.status=1 and rms_student.degree IN (1)";
		$order_by=" order by id DESC";
		$where='';
		if(empty($search)){
			return $_db->fetchAll($sql.$order_by);
		}
		if(!empty($search['txtsearch'])){
			$s_where = array();
			$s_search = addslashes(trim($search['txtsearch']));
			$s_where[] = " (SELECT stu_code FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_drop`.`stu_id` limit 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT stu_khname FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_drop`.`stu_id` limit 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT stu_enname FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_drop`.`stu_id` limit 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=`rms_student_drop`.`type` limit 1) LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		
		return $_db->fetchAll($sql.$where.$order_by);
// 		(select name_kh from `rms_view` where `rms_view`.`type`=6 and `rms_view`.`key_code`=`rms_student_drop`.`status`)AS status
	}
	public function getStudentDropById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_student_drop WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	public function addStudentDrop($_data){
			try{	
				$_db= $this->getAdapter();
				$_arr= array(
						'user_id'=>$this->getUserId(),
						'stu_id'=>$_data['studentid'],
						'type'=>$_data['type'],
						'status'=>$_data['status'],
						'date'=>$_data['datestop'],
						'reason'=>$_data['reason'],
						'note'=>$_data['note']
						);
				$id = $this->insert($_arr);
				
				$this->_name='rms_student';
				
				$where=" stu_id=".$_data['studentid'];
				$arr=array(
					'is_subspend'	=>	$_data['type'],
						);
				$this->update($arr, $where);
				$_db->commit();
			}catch(Exception $e){
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
	}
	public function updateStudentDrop($_data){
		$db= $this->getAdapter();
		try{	
			$_arr=array(
					'user_id'=>$this->getUserId(),
					'stu_id'=>$_data['studentid'],
					'type'=>$_data['type'],
					'date'=>$_data['datestop'],
					'note'=>$_data['reason'],
					'status'=>$_data['status'],
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
		$sql = "SELECT * FROM `rms_student` WHERE stu_id=$stu_id LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	
	
	
	
	
	
	
	
	
	
	
}

