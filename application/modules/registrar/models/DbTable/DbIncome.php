<?php
class registrar_Model_DbTable_DbIncome extends Zend_Db_Table_Abstract
{
	protected $_name = 'ln_income';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	
	}
	function addIncome($data){
		$array = array(
				'name'			=>$data['name'],
				'sex'			=>$data['sex'],
				'phone'			=>$data['phone'],
				
				'title'			=>$data['title'],
				'invoice'		=>$data['invoice'],
				'cat_id'		=>$data['cate_income'],
				
				'total_amount'	=>$data['total_amount'],
				'desc'			=>$data['Description'],
				'for_date'		=>$data['Date'],
				
				'status'		=>$data['Stutas'],
				'branch_id'		=>$data['branch_id'],
				'user_id'		=>$this->getUserId(),
				'create_date'	=>date('Y-m-d H:i:s'),
		);
		$this->insert($array);
 	}
	function updateIncome($data,$id){
		$arr = array(
					'name'			=>$data['name'],
					'sex'			=>$data['sex'],
					'phone'			=>$data['phone'],
				
					'title'			=>$data['title'],
					'invoice'		=>$data['invoice'],
					'cat_id'		=>$data['cate_income'],
				
					'total_amount'	=>$data['total_amount'],
					'desc'			=>$data['Description'],
					'for_date'		=>$data['Date'],
				
					'status'		=>$data['Stutas'],
					'branch_id'		=>$data['branch_id'],
					'user_id'		=>$this->getUserId(),
			);
		$where=" id = ".$id;
		$this->update($arr, $where);
	}
	function getIncomeById($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM $this->_name where id=$id ";
		return $db->fetchRow($sql);
	}
	
	function getAllIncome($search=null){
		$db = $this->getAdapter();
		$session_user=new Zend_Session_Namespace('auth');
		$from_date =(empty($search['start_date']))? '1': " create_date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': " create_date <= '".$search['end_date']." 23:59:59'";
		$where = " and ".$from_date." AND ".$to_date;
		
		$sql="SELECT 
					id, 
					(select branch_namekh from rms_branch where br_id = branch_id) as branch,
					invoice,
					title,
					(SELECT c.category_name FROM rms_cate_income_expense As c WHERE c.id=ln_income.cat_id AND c.parent=1 LIMIT 1) as cate_name,
					(SELECT curr_nameen FROM `ln_currency` WHERE ln_currency.id =curr_type) AS currency_type,
					total_amount,`desc`,for_date,status 
				FROM 
					ln_income 
				where 	
					reg_from = 0
			";
		
		$order = " order by id DESC";
		
		if (!empty($search['adv_search'])){
			$s_where = array();
			$s_search = trim(addslashes($search['adv_search']));
			$s_where[] = " title LIKE '%{$s_search}%'";
			$s_where[] = " total_amount LIKE '%{$s_search}%'";
			$s_where[] = " invoice LIKE '%{$s_search}%'";
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		if($search['status']>0){
			$where.= " AND status = ".$search['status'];
		}
		if($search['currency_type']>0){
			$where.= " AND curr_type = ".$search['currency_type'];
		}
		if(!empty($search['category'])){
			$where.= " AND cat_id= ".$search['category'];
		}
		if(!empty($search['branch'])){
			$where.= " AND branch_id= ".$search['branch'];
		}
		return $db->fetchAll($sql.$where.$order);
	}
	function getAllExpenseReport($search=null){
		$db = $this->getAdapter();
		$session_user=new Zend_Session_Namespace('auth');
		$from_date =(empty($search['start_date']))? '1': " transaction_date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': " transaction_date <= '".$search['end_date']." 23:59:59'";
		$where = " WHERE ".$from_date." AND ".$to_date;
	
		$sql=" SELECT id,
		title,
		(SELECT symbol FROM `ln_currency` WHERE ln_currency.id =curr_type) AS currency_type,invoice,
		curr_type,
		total_amount,desc,date,status FROM $this->_name ";
	
		if (!empty($search['adv_search'])){
			$s_where = array();
			$s_search = trim(addslashes($search['adv_search']));
			// $s_where[] = " title LIKE '%{$s_search}%'";
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
	
	function getExchangeRate(){
		$db = $this->getAdapter();
		$sql="select * from rms_exchange_rate where active = 1 ";
		return $db->fetchRow($sql);
	}

	function addNewCateIncome($data){
		$this->_name="rms_cate_income_expense";
		$array = array(
				'category_name'	=>$data['cate_title'],
				'parent'		=>$data['type'],
				'create_date'	=>date('Y-m-d'),
				'user_id'		=>$this->getUserId(),
		);
		return $this->insert($array);
	}

	function getReceiptNo($branch_id){
		$db = $this->getAdapter();
		$sql= "select count(id) from ln_income where branch_id = $branch_id limit 1 ";
		
		$acc_no =  $db->fetchOne($sql);
		
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen($new_acc_no);
		
		$pre="";
		
		for($i = $acc_no;$i<6;$i++){
			$pre.='0';
		}
		
		return $pre.$new_acc_no;
		
	}
	
	
	
	
	
}