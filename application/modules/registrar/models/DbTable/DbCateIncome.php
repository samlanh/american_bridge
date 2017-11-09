<?php
class Registrar_Model_DbTable_DbCateIncome extends Zend_Db_Table_Abstract
{
	protected $_name = 'rms_cate_income_expense';
	
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	
	public function getBranchId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->branch_id;
	}
	
	function addCateIncome($data){
		$array = array(
					'category_name'	=>$data['title'],
					'user_id'		=>$this->getUserId(),
					'create_date'	=>date('Y-m-d'),
					'parent'		=>1, // 1 =  income category
				);
		$this->insert($array);
 	 }
 	 
	 function updateCateIncome($data){
		$arr = array(
					'category_name'	=>$data['title'],
					'status'		=>$data['status'],
					'user_id'		=>$this->getUserId(),
				);
		$where=" id = ".$data['id'];
		$this->update($arr, $where);
	}
	
	function getCateIncomeById($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM rms_cate_income_expense where id=$id ";
		return $db->fetchRow($sql);
	}
	
	function getAllCateIncome($search=null){
		$db = $this->getAdapter();
		$sql=" SELECT 
					ci.id,
					ci.category_name,
					(select first_name from rms_users where rms_users.id = ci.user_id) as user,
					create_date,
					ci.status
				FROM 
					rms_cate_income_expense as ci 
				where 
					parent = 1
					and status = 1 
			";
		$where = " ";
		if (!empty($search['adv_search'])){
			$s_where = array();
			$s_search = trim(addslashes($search['adv_search']));
			$s_where[] = " category_name LIKE '%{$s_search}%'";
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		if($search['status']>-1){
			$where.= " AND status = ".$search['status'];
		}
		
        $order=" order by id desc ";
		return $db->fetchAll($sql.$where.$order);
	}
	
	
	
}







