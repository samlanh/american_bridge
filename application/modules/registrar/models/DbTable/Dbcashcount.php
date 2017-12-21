<?php
class Registrar_Model_DbTable_Dbcashcount extends Zend_Db_Table_Abstract
{
	protected $_name = 'rms_cashcount';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	function getAllcashcount($search=null){
		$db = $this->getAdapter();
		$session_user=new Zend_Session_Namespace('authstu');
		$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
		$where = " And ".$from_date." AND ".$to_date;
	
		$sql=" SELECT 
					id,
					input_date,
					total_dollar,
					total_reil,
					exchange_rate,
					dollar_fromreil,
					tototal_all,
					note,
					(select first_name from rms_users where rms_users.id = rms_cashcount.user_id LIMIT 1) as user,
		 			(select name_en from rms_view where type=1 and key_code = rms_cashcount.status LIMIT 1) as status
		 		FROM 
		 			rms_cashcount 
		 		where 
		 			status =1 
		
			";
	
		if (!empty($search['adv_search'])){
			$s_where = array();
			$s_search = trim(addslashes($search['adv_search']));
			$s_where[] = " title LIKE '%{$s_search}%'";
			$s_where[] = " total_amount LIKE '%{$s_search}%'";
			$s_where[] = " invoice LIKE '%{$s_search}%'";
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		if($search['branch']>0){
			$where.= " AND branch_id = ".$search['branch'];
		}
		if($search['user']>0){
			$where.= " AND user_id = ".$search['user'];
		}
		$order=" order by id desc ";
		//echo $sql.$where.$order;
		return $db->fetchAll($sql.$where.$order);
	}
	function addCashCount($data){
		$array = array(
				'branch_id'=>1,
				'user_id'=>$this->getUserId(),
				'receive_userid'=>0,
				'date'=>date("Y-m-d"),
				'status'=>1,
				'input_date'=>$data['date'],
				'recieved_date'=>$data['date'],
				
				'dollar_100'=>$data['d_hundred'],
				'dollar_50'=>$data['d_fifty'],
				'dollar_20'=>$data['d_tweenty'],
				'dollar_10'=>$data['d_ten'],
				'dollar_5'=>$data['d_five'],
				'dollar_1'=>$data['d_one'],
				
				'reil_50000'=>$data['r_fiftyhousend'],
				'reil_20000'=>$data['r_tweentyhousend'],
				'reil_10000'=>$data['r_tenthousend'],
				'reil_5000'=>$data['r_fivehousend'],
				'reil_2000'=>$data['r_twothousend'],
				'reil_1000'=>$data['thousend'],
				'reil_500'=>$data['r_fivehundred'],
				'reil_100'=>$data['r_onehundred'],
				'total_reil'=>$data['rs_kh_total'],
				
				'exchange_rate'=>$data['rate'],
				
				'total_dollar'=>$data['rs_dollar_total'],
				'tototal_all'=>$data['total_amount'],
				'dollar_fromreil'=>$data['total_reil_dollar'],
				
				'note'=>$data['note'],
				);
		if(!empty($data['id'])){
			$where ="id =".$data['id']; 
			$this->update($array, $where);
		}else{
			$this->insert($array);
		}
		
 	 }
 	 function getCashcountbyId($id){
 	 	$db = $this->getAdapter();
 	 	$sql=" SELECT * FROM rms_cashcount where id = $id ";
 	 	return $db->fetchRow($sql);
 	 }
 	 
	function getExchangeRate(){
		$db=$this->getAdapter();
		$sql="select reil from rms_exchange_rate where active=1 ";
		return $db->fetchOne($sql);
	}
	
	
	
	
	
}







