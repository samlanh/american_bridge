<?php
class registrar_Model_DbTable_DbIncome extends Zend_Db_Table_Abstract
{
	protected $_name = 'ln_income';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	
	}
	function addexpense($data){
		// if($data['currency_type']==1){
			// $amount_in_reil = 0 ;
			// $amount_in_dollar = $data['total_amount'];
		// }else{
			// $amount_in_reil = $data['total_amount'] ;
			// $amount_in_dollar = $data['convert_to_dollar'];
		// }
		$array = array(
				'title'=>$data['title'],
				'invoice'=>$data['invoice'],
				'cat_id'=>$data['cat_income'],
				'curr_type'=>$data['currency_type'],
				'total_amount'=>$data['total_amount'],
				'desc'=>$data['Description'],
				'for_date'=>$data['Date'],
				'status'=>$data['Stutas'],
				'user_id'=>$this->getUserId(),
				'create_date'=>date('Y-m-d'),
		);
		$this->insert($array);

 }
 function updateIncome($data,$id){
 	
 	// if($data['currency_type']==1){
 		// $amount_in_reil = 0 ;
 		// $amount_in_dollar = $data['total_amount'];
 	// }else{
 		// $amount_in_reil = $data['total_amount'] ;
 		// $amount_in_dollar = $data['convert_to_dollar'];
 	// }
 	
	$arr = array(
				'title'=>$data['title'],
				'invoice'=>$data['invoice'],
				'cat_id'=>$data['cat_income'],
				'curr_type'=>$data['currency_type'],
				'total_amount'=>$data['total_amount'],
				'desc'=>$data['Description'],
				'for_date'=>$data['Date'],
				'status'=>$data['Stutas'],
				//'user_id'=>$this->getUserId(),
				//'transaction_date'=>date('Y-m-d'),
		);
	$where=" id = ".$id;
	$this->update($arr, $where);
}
function getexpensebyid($id){
	$db = $this->getAdapter();
	$sql=" SELECT * FROM $this->_name where id=$id ";
	return $db->fetchRow($sql);
}

function getAllIncome($search=null){
	$db = $this->getAdapter();
	$session_user=new Zend_Session_Namespace('auth');
	$from_date =(empty($search['start_date']))? '1': " create_date >= '".$search['start_date']." 00:00:00'";
	$to_date = (empty($search['end_date']))? '1': " create_date <= '".$search['end_date']." 23:59:59'";
	$where = " WHERE ".$from_date." AND ".$to_date;
	
	//$where='';
	
	$sql=" SELECT id, invoice,title,(SELECT c.category_name FROM rms_category As c WHERE c.id=ln_income.cat_id AND c.parent=1 LIMIT 1) as cat_name,
	(SELECT curr_nameen FROM `ln_currency` WHERE ln_currency.id =curr_type) AS currency_type,
	total_amount,`desc`,for_date,status FROM ln_income ";
	
	if (!empty($search['adv_search'])){
			$s_where = array();
			$s_search = trim(addslashes($search['adv_search']));
			// $s_where[] = " account_id LIKE '%{$s_search}%'";
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
       //$order=" order by id DESC ";
	   //echo $sql.$where;
		return $db->fetchAll($sql.$where);
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



}