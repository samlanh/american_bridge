<?php

class Gep_Model_DbTable_DbStudentChangeGroup extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'rms_student_change_group';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	
	public function getAllStudentID(){
		$_db = $this->getAdapter();
		$sql = "SELECT st.stu_id,st.stu_code FROM `rms_student` as st,rms_group_detail_student as gds where st.stu_type=2 and is_subspend=0 and gds.is_pass=0 and gds.stu_id=st.stu_id group by gds.stu_id";
		$orderby = " ORDER BY stu_code ";
		return $_db->fetchAll($sql.$orderby);		
	}
	
	
	public function getAllStudentChangeGroup(){
		$db = $this->getAdapter();
		$sql = "SELECT group_code,id FROM `rms_group` where status = 1 ";
// 		$orderby = " ORDER BY stu_code ";
		return $db->fetchAll($sql);
	}
	
	public function selectAllStudentChangeGroup($search){
		$_db = $this->getAdapter();
		$sql = "SELECT id,(SELECT stu_code FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id`) AS code,
		(SELECT stu_khname FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id`) AS kh_name,
		(SELECT stu_enname FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id`) AS en_name,
		(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=2 and `rms_view`.`key_code`=(SELECT sex FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id` limit 1))AS sex,
		(select group_code from rms_group where rms_group.id = rms_student_change_group.from_group limit 1)AS from_group,
		(select group_code from rms_group where rms_group.id = rms_student_change_group.to_group limit 1)AS to_group,
		moving_date,note from `rms_student_change_group`,rms_student where rms_student_change_group.stu_id=rms_student.stu_id and rms_student.degree IN (5) ";
		$order_by=" order by id DESC";
		$where='';
		
		if(empty($search)){
			return $_db->fetchAll($sql.$order_by);
		}
		if(!empty($search['txtsearch'])){
			$s_where = array();
			$s_search = addslashes(trim($search['txtsearch']));
			$s_where[] = " (SELECT stu_code FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id`) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT stu_khname FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id`) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT stu_enname FROM `rms_student` WHERE `rms_student`.`stu_id`=`rms_student_change_group`.`stu_id`) LIKE '%{$s_search}%'";
			$s_where[] = " (select group_code from rms_group where rms_group.id = rms_student_change_group.from_group) LIKE '%{$s_search}%'";
			$s_where[] = " (select group_code from rms_group where rms_group.id = rms_student_change_group.to_group) LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		
		
		return $_db->fetchAll($sql.$where.$order_by);
// 		(select name_kh from `rms_view` where `rms_view`.`type`=6 and `rms_view`.`key_code`=`rms_student_change_group`.`status`)AS status
	}
	
	public function getAllStudentChangeGroupById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_student_change_group WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	
	
	public function addStudentChangeGroup($_data){
			try{	
				$_db= $this->getAdapter();
				$stu_id=$_data['studentid'];
				$_arr= array(
						'user_id'=>$this->getUserId(),
						'stu_id'=>$_data['studentid'],
						'from_group'=>$_data['from_group'],
						'to_group'=>$_data['to_group'],
						'moving_date'=>$_data['moving_date'],
						'note'=>$_data['note'],
						'status'=>$_data['status']
						);
				$id = $this->insert($_arr);
				
				$this->_name='rms_group_detail_student';
				$arr= array(
						'group_id'=>$_data['to_group'],
						'old_group'	=>$_data['from_group'],
				);
				$where="stu_id=".$stu_id." and is_pass=0";
				
				$this->update($arr, $where);
				
			}catch(Exception $e){
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
	}
	public function updateStudentChangeGroup($_data){
// 		print_r($_data);exit();
		try{	
			$stu_id=$_data['studentid'];
			$_arr=array(
						'user_id'=>$this->getUserId(),
						//'stu_id'=>$_data['studentid'],
						'from_group'=>$_data['from_group'],
						'to_group'=>$_data['to_group'],
						'moving_date'=>$_data['moving_date'],
						'note'=>$_data['note'],
						'status'=>$_data['status'],
					);
			$where=$this->getAdapter()->quoteInto("stu_id=?", $_data["studentid"]);
			$this->update($_arr, $where);
			
			
			$this->_name='rms_group_detail_student';
			$arr= array(
					'group_id'	=>$_data['to_group'],
					'old_group'	=>$_data['from_group'],
			);
			$where="stu_id=".$stu_id." and is_pass=0";
			
			$this->update($arr, $where);
			
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
	
	
	
	function getStudentChangeGroup1ById($id){
		$db = $this->getAdapter();
		$sql = "SELECT start_date,expired_date,
		(select CONCAT(from_academic,'-',to_academic,'(',generation,')') from rms_tuitionfee where rms_tuitionfee.id=rms_group.academic_year )AS year ,
		(select major_enname from `rms_major` where `rms_major`.`major_id`=`rms_group`.`grade`)AS grade,
		(select en_name from rms_dept where rms_dept.dept_id=rms_group.degree) as degree,
		(select name_en from `rms_view` where `rms_view`.`type`=4 and `rms_view`.`key_code`=`rms_group`.`session`)AS session
		FROM `rms_group` WHERE id=$id LIMIT 1 ";
		return $db->fetchRow($sql);
	}
	
	function getStudentInfoById($stu_id){
		$db = $this->getAdapter();
		$sql = "SELECT st.stu_enname,st.`sex`,gds.`group_id` FROM `rms_student` AS st,rms_group_detail_student AS gds WHERE gds.is_pass=0 and  st.stu_id=$stu_id AND st.stu_id=gds.stu_id LIMIT 1";
// 		echo $sql;exit();
		return $db->fetchRow($sql);
	}
	
	
	
	
}

