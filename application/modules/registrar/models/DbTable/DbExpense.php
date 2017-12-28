<?php
class Registrar_Model_DbTable_DbExpense extends Zend_Db_Table_Abstract
{
	protected $_name = 'ln_income_expense';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	function addexpense($data){
		$data = array(
				'title'			=>$data['title'],
				'invoice'		=>$data['invoice'],
				'cat_id'		=>$data['cate_expense'],
				'curr_type'		=>$data['currency_type'],
				'total_amount'	=>$data['total_amount'],
				'desc'			=>$data['Description'],
				'for_date'		=>$data['Date'],
				'status'		=>$data['Stutas'],
				'user_id'		=>$this->getUserId(),
				'branch_id'		=>$data['branch_id'],
				'create_date'	=>date('Y-m-d'),
			);
		$this->insert($data);
 	}
 	
	function updateExpense($data){
		$arr = array(
				'title'			=>$data['title'],
				'invoice'		=>$data['invoice'],
			    'cat_id'		=>$data['cate_expense'],
				'curr_type'		=>$data['currency_type'],
				'total_amount'	=>$data['total_amount'],
				'desc'			=>$data['Description'],
				'for_date'		=>$data['Date'],
				'status'		=>$data['Stutas'],
				'user_id'		=>$this->getUserId(),
				'branch_id'		=>$data['branch_id'],
			);
		$where=" id = ".$data['id'];
		$this->update($arr, $where);
	}
	function getexpensebyid($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM $this->_name where id=$id ";
		return $db->fetchRow($sql);
	}
	
	function getAllExpense($search=null){
		$db = $this->getAdapter();
		$session_user=new Zend_Session_Namespace('auth');
		
		$dbgb = new Application_Model_DbTable_DbGlobal();
		$branch_id = $dbgb->getAccessPermission('branch_id');
		
		$from_date =(empty($search['start_date']))? '1': " create_date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': " create_date <= '".$search['end_date']." 23:59:59'";
		$where = " WHERE ".$from_date." AND ".$to_date;
		$where.=$branch_id;
		$sql=" 
				SELECT 
					id,
					(select branch_namekh from rms_branch where br_id = branch_id) as branch,
					invoice,
					title,
					(SELECT c.category_name FROM rms_cate_income_expense As c WHERE c.id=ln_income_expense.cat_id AND c.parent=0 LIMIT 1) as cat_name,
					(SELECT curr_nameen FROM `ln_currency` WHERE ln_currency.id =curr_type) AS currency_type, 
					total_amount,
					`desc`,
					for_date,
					status 
				FROM 
					$this->_name 
			";
		
		if (!empty($search['adv_search'])){
			$s_where = array();
			$s_search = trim(addslashes($search['adv_search']));
			$s_where[] = " title LIKE '%{$s_search}%'";
			$s_where[] = " total_amount LIKE '%{$s_search}%'";
			$s_where[] = " invoice LIKE '%{$s_search}%'";
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		if($search['status']>-1){
			$where.= " AND status = ".$search['status'];
		}
		if($search['currency_type']>-1){
			$where.= " AND curr_type = ".$search['currency_type'];
		}
		if(!empty($search['category'])){
			$where.= " AND cat_id= ".$search['category'];
		}
		if(!empty($search['branch'])){
			$where.= " AND branch_id= ".$search['branch'];
		}
        $order=" order by id desc ";
		return $db->fetchAll($sql.$where.$order);
	}
	function getAllExpenseReport($search=null){
		$db = $this->getAdapter();
		$session_user=new Zend_Session_Namespace('auth');
		$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
		$where = " WHERE ".$from_date." AND ".$to_date;
	
		$sql=" SELECT id,
		account_id,
		(SELECT symbol FROM `ln_currency` WHERE ln_currency.id =curr_type) AS currency_type,invoice,
		curr_type,
		total_amount,disc,date,status FROM $this->_name ";
	
		if (!empty($search['adv_search'])){
			$s_where = array();
			$s_search = trim(addslashes($search['adv_search']));
			$s_where[] = " account_id LIKE '%{$s_search}%'";
			$s_where[] = " title LIKE '%{$s_search}%'";
			$s_where[] = " total_amount LIKE '%{$s_search}%'";
			$s_where[] = " invoice LIKE '%{$s_search}%'";
			
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		if($search['status']>-1){
			$where.= " AND status = ".$search['status'];
		}
		if($search['currency_type']>-1){
			$where.= " AND curr_type = ".$search['currency_type'];
		}
		$order=" order by id desc ";
		return $db->fetchAll($sql.$where.$order);
	}



}