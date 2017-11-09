<?php

class Foundation_Model_DbTable_DbStudentDropTransport extends Zend_Db_Table_Abstract
{
	protected $_name = 'rms_student_drop';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	public function getAllStudentTransportID(){
		$_db = $this->getAdapter();
		
		$db=new Application_Model_DbTable_DbGlobal();
		$branch_id = $db->getAccessPermission();
		
		$sql = "SELECT s.stu_id, s.stu_code FROM `rms_student` as st,rms_service as s where s.status = 1 and s.stu_id=st.stu_id and s.type=4 and s.is_suspend=0 $branch_id ";
		$orderby = " ORDER BY s.id ";
		return $_db->fetchAll($sql.$orderby);		
	}
	
	function getAllStudentTransportName(){
		$_db = $this->getAdapter();
		
		$db=new Application_Model_DbTable_DbGlobal();
		$branch_id = $db->getAccessPermission();
		
		$sql = "SELECT s.stu_id, CONCAT(st.stu_enname,'-',st.stu_khname) as name FROM `rms_student` as st,rms_service as s where s.status = 1 and s.stu_id=st.stu_id and s.type=4 and s.is_suspend=0 $branch_id ";
		$orderby = " ORDER BY s.id ";
		return $_db->fetchAll($sql.$orderby);
	}
	
	public function getAllStudentTransportIDEdit(){
		$_db = $this->getAdapter();
		
		$db=new Application_Model_DbTable_DbGlobal();
		$branch_id = $db->getAccessPermission();
		
		$sql = "SELECT s.stu_id, s.stu_code FROM `rms_student` as st,rms_service as s where s.status = 1 and s.stu_id=st.stu_id and s.type=4 $branch_id  ";
		$orderby = " ORDER BY s.id ";
		return $_db->fetchAll($sql.$orderby);
	}
	
	public function getAllStudentTransportNameEdit(){
		$_db = $this->getAdapter();
	
		$db=new Application_Model_DbTable_DbGlobal();
		$branch_id = $db->getAccessPermission();
	
		$sql = "SELECT s.stu_id, CONCAT(st.stu_enname,'-',st.stu_khname) as name FROM `rms_student` as st,rms_service as s where s.status = 1 and s.stu_id=st.stu_id and s.type=4 $branch_id ";
		$orderby = " ORDER BY s.id ";
		return $_db->fetchAll($sql.$orderby);
	}
	public function getAllStudentDropTransport($search){
		$_db = $this->getAdapter();
		
		$db = new Application_Model_DbTable_DbGlobal();
		$branch_id = $db->getAccessPermission();
		
		$sql = "SELECT 
					sd.id,
					ser.stu_code,
					s.stu_khname,
					s.stu_enname,
					(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=2 and `rms_view`.`key_code`=s.sex)AS sex,
					
					(select title from rms_program_name where service_id = ser.service_id) as service_name,
					
					(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=`sd`.`type` limit 1) as type,
					sd.reason,
					sd.date 
				from 
					`rms_student_drop` as sd,
					rms_student as s ,
					rms_service as ser					
				where 
					s.stu_id=sd.stu_id 
					and sd.status=1 
					and ser.stu_id = sd.stu_id
					and sd.drop_from = 2
					and ser.type=4
					$branch_id
				";
		
		$order_by=" order by sd.id DESC";
		
		$where=' ';
		
		if(empty($search)){
			return $_db->fetchAll($sql.$order_by);
		}
		
		if(!empty($search['title'])){
			$s_where = array();
			$s_search = addslashes(trim($search['title']));
			$s_where[] = " ser.stu_code LIKE '%{$s_search}%'";
			$s_where[] = " s.stu_khname LIKE '%{$s_search}%'";
			$s_where[] = " s.stu_enname LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=`sd`.`type` limit 1) LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		
		$from_date =(empty($search['start_date']))? '1': "sd.date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': "sd.date <= '".$search['end_date']." 23:59:59'";
		$where = " AND ".$from_date." AND ".$to_date;
		
		if(!empty($search['branch'])){
			$where.=" AND s.branch_id = ".$search['branch'];
		}
		if(!empty($search['service'])){
			$where.=" AND ser.service_id = ".$search['service'];
		}
		
		return $_db->fetchAll($sql.$where.$order_by);
	}
	
	public function getStudentDropById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_student_drop WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	public function addStudentDropTransport($_data){
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
					'drop_from'	=>2, // drop from transport 
					);
			$id = $this->insert($_arr);
			
			$this->_name = 'rms_service';
			$where = " stu_id = ".$_data['studentid']." and type = 4 ";
			if($_data['status']==1){
				$arr=array(
					'is_suspend'	=>	$_data['type'],
				);
			}else{
				$arr=array(
					'is_suspend'	=>	0,
				);
			}
			$this->update($arr, $where);
			
			
			$this->_name='rms_student_payment';
			$sql="select id from rms_student_payment where student_id = ".$_data['studentid']." and payfor_type=3 order by id DESC limit 1";
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
			echo $e->getMessage();
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
					'status'	=>$_data['status'],
					);
			$where=$this->getAdapter()->quoteInto("id=?", $_data["id"]);
			$this->update($_arr, $where);
			
			$this->_name = 'rms_service';
			$where = " stu_id = ".$_data['studentid']." and type = 4 ";
			if($_data['status']==1){
				$arr=array(
					'is_suspend'	=>	$_data['type'],
				);
			}else{
				$arr=array(
					'is_suspend'	=>	0,
				);
			}
			$this->update($arr, $where);
			
			
			$this->_name='rms_student_payment';
			$sql="select id from rms_student_payment where student_id = ".$_data['studentid']." and payfor_type=3 order by id DESC limit 1";
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
			echo $e->getMessage();
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
		$sql = "SELECT 
					CONCAT(st.stu_khname,' - ',st.stu_enname) as name,
					st.sex,
					st.tel,
					(select s.service_id from rms_service as s where s.stu_id = st.stu_id and type=4 limit 1) as service
				FROM 
					`rms_student` as st
				WHERE 
					stu_id=$stu_id LIMIT 1 
			";
		
		return $db->fetchRow($sql);
	}
	
	function getAllTransportService(){
		$db = $this->getAdapter();
		$sql = "select service_id as id,title as name from rms_program_name where type=2 and ser_cate_id = 3 and status =1 ";
		return $db->fetchAll($sql);
	}
	
	
	
}

