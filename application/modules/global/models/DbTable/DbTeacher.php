<?php

class Global_Model_DbTable_DbTeacher extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_teacher';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
	public function AddNewTeacher($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			
			$photoname = str_replace(" ", "_", $_data['en_name'].'-teacher') . '.jpg';
			$upload = new Zend_File_Transfer();
			$upload->addFilter('Rename',
					array('target' => PUBLIC_PATH . '/images/'. $photoname, 'overwrite' => true) ,'photo');
			$receive = $upload->receive();
			if($receive)
			{
				$_data['photo'] = $photoname;
			}
			else{
				$_data['photo']="";
			}
			$_arr=array(
					'teacher_code' => $_data['code'],
					'teacher_name_kh' => $_data['kh_name'],
					'teacher_name_en' => $_data['en_name'],
					'sex' => $_data['sex'],
					"photo" => $_data['photo'],
					'dob' => $_data['dob'],
					'pob' => $_data['pob'],
			        'tel'   => $_data['phone'],
					'address' => $_data['address'],
					'email' => $_data['email'],
					'degree' => $_data['degree'],
					'note' => $_data['note'],
					'status'   => $_data['status'],
					'nationality'  => $_data['nationality'],
					'group'  => $_data['group'],
					'home'  => $_data['home'],
					'commnue'  => $_data['commnue'],
					'district' => $_data['district'],
					'province'	=> $_data['province'],
					'id_card_no' => $_data['idcard'],
					'issued_date' => $_data['issued'],
					'expired' => $_data['expired'],
			        'pars_id' => $_data['pars'],
					'issued_date1' => $_data['issued1'],
					'expired1' => $_data['expired1'],
			        'date' => Zend_Date::now(),
			        'user_id'	  => $this->getUserId(),
					
			);
				$this->insert($_arr);
// 			}
			return $db->commit();
		}catch (Exception $e){
		    echo $e->getMessage();exit();
			$db->rollBack();
		}
	}
	public function getTeacherById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_teacher WHERE id = ".$db->quote($id);
		$sql.=" LIMIT 1";
		$row=$db->fetchRow($sql);
		return $row;
	}
	public function getallSubjectTeacherById($teacher_id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM `rms_teacher_subject` WHERE id= ".$db->quote($teacher_id);
		return $db->fetchAll($sql);;
	}
	public function updateTeacher($_data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$photoname = str_replace(" ", "_", $_data['en_name'].'-teacher') . '.jpg';
			$upload = new Zend_File_Transfer();
			$upload->addFilter('Rename',
					array('target' => PUBLIC_PATH . '/images/'. $photoname, 'overwrite' => true) ,'photo');
			$receive = $upload->receive();
			if($receive)
			{
				$_data['photo'] = $photoname;
			}
			else{
				$_data['photo']=$_data['photo'];
			}
		
		$_arr=array(
					'teacher_code' => $_data['code'],
					'teacher_name_kh' => $_data['kh_name'],
					'teacher_name_en' => $_data['en_name'],
					'sex' => $_data['sex'],
					"photo" => $_data['photo'],
					'dob' => $_data['dob'],
					'pob' => $_data['pob'],
			        'tel'   => $_data['phone'],
					'address' => $_data['address'],
					'email' => $_data['email'],
					'degree' => $_data['degree'],
					'note' => $_data['note'],
					'status'   => $_data['status'],
					'nationality'  => $_data['nationality'],
					'group'  => $_data['group'],
					'home'  => $_data['home'],
					'commnue'  => $_data['commnue'],
					'district' => $_data['district'],
					'province'	=> $_data['province'],
					'id_card_no' => $_data['idcard'],
					'issued_date' => $_data['issued'],
					'expired' => $_data['expired'],
			        'pars_id' => $_data['pars'],
					'issued_date1' => $_data['issued1'],
					'expired1' => $_data['expired1'],
			        'date' => Zend_Date::now(),
			        'user_id'	 => $this->getUserId(),
		);
		$where=$this->getAdapter()->quoteInto("id=?", $_data["id"]);
		
		$this->update($_arr, $where);
		return $db->commit();
		}catch (Exception $e){
// 			echo $e->getMessage();exit();
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	function getAllTeacher($search){
		$db = $this->getAdapter();
		$sql = 'SELECT id, teacher_code,teacher_name_kh, teacher_name_en, 
				(select name_kh from rms_view where rms_view.type=2 and rms_view.key_code=rms_teacher.sex)AS sex, 
				tel,email, 
				(select name_kh from rms_view where type=3 and rms_view.key_code=rms_teacher.degree)AS degree, 
				note  FROM rms_teacher WHERE 1';
		$order_by=" order by id DESC";
		$where = '';
		if(!empty($search['title'])){
		    $s_where = array();
			$s_search = addslashes(trim($search['title']));
			
			$s_where[] = " teacher_code LIKE '%{$s_search}%'";
			$s_where[] = " teacher_name_en LIKE '%{$s_search}%'";
			$s_where[] = " teacher_name_kh LIKE '%{$s_search}%'";
			$s_where[] = " (select name_kh from rms_view where rms_view.type=2 and rms_view.key_code=rms_teacher.sex) LIKE '%{$s_search}%'";
			$s_where[] = " tel LIKE '%{$s_search}%'";
			$s_where[] = " email LIKE '%{$s_search}%'";
			$s_where[] = " (select name_kh from rms_view where type=3 and rms_view.key_code=rms_teacher.degree) LIKE '%{$s_search}%'";
			$s_where[] = " status LIKE '%{$s_search}%'";
			$s_where[] = " user_id LIKE '%{$s_search}%'";
			
			$where .=' AND ('.implode(' OR ',$s_where).')';
			
		}
		return $db->fetchAll($sql.$where.$order_by);
	}
	public function addTeacherSubject($_data){//ajax
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			
			$_arr=array(
					'teacher_code' => $_data['code'],
					'teacher_name_kh' => $_data['kh_name'],
					'teacher_name_en' => $_data['en_name'],
					'sex' => $_data['sex'],
					'phone' => $_data['phone'],
					'dob' => $_data['dob'],
					'pob' => $_data['pob'],
					'address' => $_data['address'],
					'email' => $_data['email'],
					'degree' => $_data['degree'],
					'note'=>$_data['note'],
					'date' => Zend_Date::now(),
					'status'   => 1,
					'user_id'	=> $this->getUserId()
			);
			$teacher_id = $this->insert($_arr);
			
			$this->_name='rms_subject';
			$_arr=array(
					'subject_titlekh' => $_data['subject_kh'],
					'subject_titleen' => $_data['subject_en'],
					'date' 	=> Zend_Date::now(),
					'status'   	=> 1,
					'user_id'	  => $this->getUserId()
			);
			 $subject_id = $this->insert($_arr);
	
			$this->_name='rms_teacher_subject';
				$arr = array(
						'subject_id'=>$subject_id,
						'teacher_id'=>$teacher_id,
						'status'   => 1,
						'date' => Zend_Date::now(),
						'user_id'	  => $this->getUserId()
	
				);
			$teacher_subject = $this->insert($arr);
			$db->commit();
			return $teacher_subject;
		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($err =$e->getMessage());
		}
	}
	
}

