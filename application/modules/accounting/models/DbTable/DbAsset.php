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
				'paid_month'		=>$data['amount'],
				'total_amount'		=>($data['amount'])*($data['usefull_life']),
				'payment_method'	=>$data['payment_method'],
				'some_payamount'	=>$data['some_payamount'],
				'create_date'		=>date("Y-m-d"),
				'depreciation_start'=>$data['start_date'],
				'start_date'		=>$data['start_date'],
				'end_date'			=>$data['date'],
				//'auto_post'=>$data['journal'],
				'user_id'			=>$this->getUserId(),
				'note'=>$data['note']
		);
		 $ass_id = $this->insert($arr);
		 
		 $time = $data['usefull_life'];
		 $next_payment = $data['start_date'];
		 if($data['tem_type']==1){
			 $a_time = ($data['tem_type']=2)?1:12;	
			   for($t=0;$t<$time*$a_time;$t++){
			   	$db->getProfiler()->setEnabled(true);
				 $sub_arr= array(
				 		'asset_id'		=>$ass_id,
				 		'total_depre'	=>$data['amount'],
				 		'times_depre'	=>$t+1,
				 		'for_month'		=>$next_payment,
				 		'is_closing'	=>1
				 		);			 
				 $this->_name="ln_fixed_assetdetail";
				 $this->insert($sub_arr);
				 $next_payment = date("Y-m-d", strtotime("$next_payment +1 month"));
		   	}
		 }else {
		 	$a_time = ($data['tem_type']=2)?12:1;
		 	//echo $time*$a_time;exit();
		 	for($t=0;$t<$time*$a_time;$t++){
		 		$db->getProfiler()->setEnabled(true);
		 		$sub_arr= array(
		 				'asset_id'		=>$ass_id,
		 				'total_depre'	=>$data['amount'],
		 				'times_depre'	=>$t+1,
		 				'for_month'		=>$next_payment,
		 				'is_closing'	=>1
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
				'paid_month'		=>$data['amount'],
				'total_amount'		=>($data['amount'])*($data['usefull_life']),
				'payment_method'	=>$data['payment_method'],
				'some_payamount'	=>$data['some_payamount'],
				'create_date'		=>date("Y-m-d"),
				'depreciation_start'=>$data['start_date'],
				'start_date'		=>$data['start_date'],
				'end_date'			=>$data['date'],
				//'auto_post'=>$data['journal'],
				'user_id'			=>$this->getUserId(),
				'note'=>$data['note']
		);
	$where=" id = ".$data['id'];
	$this->update($arr, $where);
	$sql = " DELETE FROM ln_fixed_assetdetail WHERE asset_id=".$data["id"];
	$db->query($sql);
	//delelet ln_income_expense
	$sql = " DELETE FROM ln_income_expense WHERE fixedasset_id=".$data["id"];
	$db->query($sql);
	
	$time = $data['usefull_life'];
	$next_payment = $data['start_date'];
	if($data['tem_type']==1){
		$a_time = ($data['tem_type']=2)?1:12;
		for($t=0;$t<$time*$a_time;$t++){
			$db->getProfiler()->setEnabled(true);
			$sub_arr= array(
					'asset_id'		=>$data['id'],
					'total_depre'	=>$data['amount'],
					'times_depre'	=>$t+1,
					'for_month'		=>$next_payment,
					'is_closing'	=>1
			);
			$this->_name="ln_fixed_assetdetail";
			$this->insert($sub_arr);
			$next_payment = date("Y-m-d", strtotime("$next_payment +1 month"));
		}
	}else {
		$a_time = ($data['tem_type']=2)?12:1;
		//echo $time*$a_time;exit();
		for($t=0;$t<$time*$a_time;$t++){
			$db->getProfiler()->setEnabled(true);
			$sub_arr= array(
					'asset_id'		=>$data['id'],
					'total_depre'	=>$data['amount'],
					'times_depre'	=>$t+1,
					'for_month'		=>$next_payment,
					'is_closing'	=>1
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
 	 branch_id,fixed_assetname,fixed_asset_type,asset_cost,asset_code,pay_type,term_type,start_date,end_date,
	`status`,usefull_life,salvagevalue,auto_post,total_amount,payment_method,create_date,depreciation_start,some_payamount,note FROM $this->_name where id=$id ";
	return $db->fetchRow($sql);
}

function getAllAsset($search=null){
	
	$db = $this->getAdapter();
	$sql=" SELECT a.id,
			(SELECT CONCAT(branch_namekh) FROM rms_branch WHERE rms_branch.br_id=branch_id AND  rms_branch.status=1 LIMIT 1)AS branch_name,
			a.fixed_assetname,
			 CONCAT('($)',a.asset_cost),CONCAT(FORMAT(a.usefull_life,0),' Month'),CONCAT('($)',a.salvagevalue),CONCAT('($)',a.paid_month),
	                 CONCAT('($)',SUM(asetd.total_depre)) AS total_amount,	 
			 a.start_date,a.end_date,a.note,a.status 
			 FROM ln_fixed_asset AS a,ln_fixed_assetdetail AS asetd
			 WHERE a.id=asetd.asset_id ";
	$from_date =(empty($search['start_date']))? '1': " a.start_date >= '".$search['start_date']." 00:00:00'";
	$to_date = (empty($search['end_date']))? '1': " a.start_date <= '".$search['end_date']." 23:59:59'";
	$where = " AND ".$from_date." AND ".$to_date;
	
	if(!empty($search['branch'])){
		$where.=" AND a.branch_id = ".$search['branch'];
	}
	if(!empty($search['title'])){
		$s_where = array();
		$s_search = str_replace(' ', '', addslashes(trim($search['title'])));
		$s_where[] = "REPLACE(a.fixed_assetname,' ','')LIKE '%{$s_search}%'";
		$s_where[] = "REPLACE(a.asset_code,' ','')LIKE '%{$s_search}%'";
		$s_where[] = "REPLACE(a.asset_code,' ','')  LIKE '%{$s_search}%'";
		$s_where[] = "REPLACE(a.asset_cost,' ','')  LIKE '%{$s_search}%'";
		$s_where[] = "REPLACE(a.usefull_life,' ','')  LIKE '%{$s_search}%'";
		$s_where[] = "REPLACE(a.salvagevalue,' ','')  LIKE '%{$s_search}%'";
		$where.=' AND ('.implode(' OR ',$s_where).')';
	}
	 
	$order=" GROUP  BY asetd.asset_id  ";
	//echo $sql.$where;
	return $db->fetchAll($sql.$where.$order);
   
}



}