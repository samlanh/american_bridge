<?php

class Registrar_Model_DbTable_DbCategory extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_category';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
	public function addCategory($_data){
		$arr = array(
				'category_name'=>$_data['category_name'],
				'parent'	   =>$_data['parent'],
				'user_id'	  	=> $this->getUserId(),
				'create_date' 	=> Zend_Date::now(),
		);
		if(!empty($_data['id'])){
			$where=" id=".$_data['id'];
			$this->update($arr, $where);
		}else{$this->insert($arr);}
		
	}	
	function getCategoryById($id){
		$db=$this->getAdapter();
		$sql="SELECT id,category_name,create_date,parent,user_id,status FROM rms_category WHERE id=$id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getAllCategory($search){
		$db=$this->getAdapter();
		$sql="SELECT id,(SELECT name_en FROM rms_view WHERE rms_view.key_code=rms_category.parent AND rms_view.type=10 LIMIT 1) As par_name,
		       category_name,create_date,(SELECT user_name FROM rms_users WHERE rms_users.id=rms_category.user_id) AS user_id,
				(SELECT name_en FROM rms_view WHERE rms_view.key_code=rms_category.status AND rms_view.type=1) AS `status`
       			  FROM rms_category WHERE 1";
		$where='';
		if(!empty($search['category_name'])){
			$s_where=array();
			$s_search= addslashes(trim($search['category_name']));
			$s_where[]= " category_name LIKE '%{$s_search}%'";
			$where.=' AND ('.implode(' OR ', $s_where).')';
		}
		if($search['status'] > -1){
			$where.= " AND status=".$search['status'];
		}
	   //echo $sql.$where;
		return $db->fetchAll($sql.$where." ORDER BY id DESC");
	}
}

