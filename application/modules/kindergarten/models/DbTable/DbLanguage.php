<?php

class Kindergarten_Model_DbTable_DbLanguage extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'rms_degree_language';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	
	}
	public function addDegreeLanguage($data){
		try{
			$db= $this->getAdapter();
			$arr = array(
					'title'=>$data['language_title'],
					'modify_date'=> date("Y-m-d"),
					'status'=> $data['status_p'],
					'user_id'=>$this->getUserId(),
					'note'=>$data['note'],
					);
			return $this->insert($arr);
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
	
}

