<?php
class Accounting_Model_DbTable_DbAsset extends Zend_Db_Table_Abstract
{
	protected $_name = 'ln_fixed_asset';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	function addasset($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{	
		$arr = array(
				'branch_id'			=>$data['branch'],
				'fixed_assetname'	=>$data['asset_name'],
				'fixed_asset_type'	=>$data['asset_type'],
				'asset_cost'		=>$data['asset_cost'],
				'asset_code'		=>$data['asset_code'],
				'pay_type'			=>$data['paid_type'],
				'status'			=>$data['status'],
				'usefull_life'		=>$data['usefull_life'],
				'term_type'			=>$data['tem_type'],
				'salvagevalue'		=>$data['salvage_value'],
				'total_amount'		=>$data['amount'],
				'payment_method'	=>$data['payment_method'],
				'some_payamount'	=>$data['some_payamount'],
				'date'				=>$data['date'],
				'depreciation_start'=>$data['start_date'],
				//'auto_post'=>$data['journal'],
				'user_id'			=>$this->getUserId(),
				'note'=>$data['note']
		);
		 $ass_id = $this->insert($arr);
		 
		 $time = $data['usefull_life'];
		 $next_payment = $data['date'];
		 if($data['tem_type']==1){
			 $a_time = ($data['tem_type']=2)?1:12;			 	
			   for($t=0;$t<$time*$a_time;$t++){
			   	$db->getProfiler()->setEnabled(true);
				 $sub_arr= array(
				 		'asset_id'		=>$ass_id,
				 		'total_depre'	=>$data['amount'],
				 		'times_depre'	=>$t+1,
				 		'for_month'		=>$next_payment,
				 		);			 
				 $this->_name="ln_fixed_assetdetail";
				 $this->insert($sub_arr);
				 $next_payment = date("Y-m-d", strtotime("$next_payment +1 month"));
		   	}
		 }else {
		 	$a_time = ($data['tem_type']=2)?12:1;		 	
		 	for($t=0;$t<$time*$a_time;$t++){
		 		$db->getProfiler()->setEnabled(true);
		 		$sub_arr= array(
		 				'asset_id'		=>$ass_id,
		 				'total_depre'	=>$data['amount'],
		 				'times_depre'	=>$t+1,
		 				'for_month'		=>$next_payment,
		 		);
		 		$this->_name="ln_fixed_assetdetail";
		 		$this->insert($sub_arr);
		 		$next_payment = date("Y-m-d", strtotime("$next_payment +1 month"));
		 	}
		 }
		  $db->commit();
		}catch (Exception $e) {
			
			$db->rollBack();
			echo $e->getMessage();
		}
}
function updatasset($data){
	$db = $this->getAdapter();
		$db->beginTransaction();
		try{	
		$arr = array(
				'branch_id'			=>$data['branch'],
				'fixed_assetname'	=>$data['asset_name'],
				'fixed_asset_type'	=>$data['asset_type'],
				'asset_cost'		=>$data['asset_cost'],
				'asset_code'		=>$data['asset_code'],
				'pay_type'			=>$data['paid_type'],
				'status'			=>$data['status'],
				'usefull_life'		=>$data['usefull_life'],
				'term_type'			=>$data['tem_type'],
				'salvagevalue'		=>$data['salvage_value'],
				'total_amount'		=>$data['amount'],
				'payment_method'	=>$data['payment_method'],
				'some_payamount'	=>$data['some_payamount'],
				'date'				=>$data['date'],
				'user_id'			=>$this->getUserId(),
				'depreciation_start'=>$data['start_date'],
				//'auto_post'=>$data['journal'],
				'note'=>$data['note']
		);
	$where=" id = ".$data['id'];
	$this->update($arr, $where);
	$sql = " DELETE FROM ln_fixed_assetdetail WHERE asset_id=".$data["id"];
	$db->query($sql);
	$time = $data['usefull_life'];
	$next_payment = $data['date'];
	if($data['tem_type']==1){
		$a_time = ($data['tem_type']=2)?1:12;
		for($t=0;$t<$time*$a_time;$t++){
			$db->getProfiler()->setEnabled(true);
			$sub_arr= array(
					'asset_id'		=>$data['id'],
					'total_depre'	=>$data['amount'],
					'times_depre'	=>$t+1,
					'for_month'		=>$next_payment,
			);
			$this->_name="ln_fixed_assetdetail";
			$this->insert($sub_arr);
			$next_payment = date("Y-m-d", strtotime("$next_payment +1 month"));
		}
	}else {
		$a_time = ($data['tem_type']=2)?12:1;
		for($t=0;$t<$time*$a_time;$t++){
			$db->getProfiler()->setEnabled(true);
			$sub_arr= array(
					'asset_id'		=>$data['id'],
					'total_depre'	=>$data['amount'],
					'times_depre'	=>$t+1,
					'for_month'		=>$next_payment,
			);
			$this->_name="ln_fixed_assetdetail";
			$this->insert($sub_arr);
			$next_payment = date("Y-m-d", strtotime("$next_payment +1 month"));
		}
	}
	
	$db->commit();
	}catch (Exception $e) {
		$db->rollBack();
		echo $e->getMessage();
	}
}

function getassetbyid($id){
	$db = $this->getAdapter();
	$sql=" SELECT id,
 	branch_id,fixed_assetname,fixed_asset_type,asset_cost,asset_code,pay_type,term_type,
	status,usefull_life,salvagevalue,auto_post,total_amount,payment_method,date,depreciation_start,some_payamount,note FROM $this->_name where id=$id ";
	return $db->fetchRow($sql);
}

function getAllAsset($search=null){
	
	$db = $this->getAdapter();
	$sql="SELECT id,
		(SELECT CONCAT(branch_namekh) FROM rms_branch WHERE rms_branch.br_id=branch_id AND  STATUS=1 LIMIT 1)AS branch_name,
		fixed_assetname,
		 asset_cost,usefull_life,salvagevalue,total_amount,note,status FROM  $this->_name ";
	$where = ' WHERE 1 ';

	if(!empty($search['branch'])){
		$where.= " AND branch_id = ".$search['branch'];
	}
	if(!empty($search['title'])){
		$s_where = array();
		$s_search = str_replace(' ', '', addslashes(trim($search['title'])));
		$s_where[] = "REPLACE(asset_code,' ','')LIKE '%{$s_search}%'";
		$s_where[] = "REPLACE(asset_code,' ','')  LIKE '%{$s_search}%'";
		$s_where[] = "REPLACE(asset_cost,' ','')  LIKE '%{$s_search}%'";
		$s_where[] = "REPLACE(usefull_life,' ','')  LIKE '%{$s_search}%'";
		$s_where[] = "REPLACE(salvagevalue,' ','')  LIKE '%{$s_search}%'";
		$where.=' AND ('.implode(' OR ',$s_where).')';
	}
	$order="	ORDER BY id DESC";
	return $db->fetchAll($sql.$where.$order);
   
}



}