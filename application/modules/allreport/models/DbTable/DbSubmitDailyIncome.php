<?php
class Allreport_Model_DbTable_DbSubmitDailyIncome extends Zend_Db_Table_Abstract
{
	protected $_name = 'rms_submit_daily_income';
	
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	
	public function getBranchId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->branch_id;
	}
	
	
	
	function SubmitDailyIncome($data , $payfor_type , $degree_type){
		
		$db = $this->getAdapter();
		
		if(!empty($data['branch_id'])){
			$branch = $data['branch_id'];
		}else{
			$branch = $this->getBranchId();
		}
		
		if(!empty($data['user_id'])){
			$user = $data['user_id'];
		}else{
			$user = $this->getUserId();
		}
		
		$sql="select 
					id 
				from 
					rms_submit_daily_income 
				where 
					payfor_type = $payfor_type 
					and shift = ".$data['shift_id']."
					and branch_id = $branch
					and user_id = $user
					and for_date = '".$data['for_date']."'
					limit 1
			";		
		//echo $sql;exit();
		$exist = $db->fetchOne($sql);
		
    	$arr = array(
    			'payfor_type'	=>$payfor_type,
    			'degree_type'	=>$degree_type,
    			'total_amount'	=>$data['total_amount'],
    			'amount_usd'	=>$data['amount_usd'],
    			'amount_riel'	=>$data['amount_riel'],
    			'shift'			=>$data['shift_id'],
    			'branch_id'		=>$branch,
    			'user_id'		=>$user,
    			'for_date'		=>$data['for_date'],
    			'create_date'	=>date("Y-m-d"),
   		);
    	
    	if(!empty($exist)){
    		$where = " id = $exist";
    		$this->update($arr, $where);
    	}else{
    		$this->insert($arr);
    	}
    	return 0;
    } 
}



