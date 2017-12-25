<?php

class Accounting_Model_DbTable_DbParkingPayment extends Zend_Db_Table_Abstract
{
 	protected $_name = 'rms_parking';
 	
	public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
    
    public function getBranchId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->branch_id;
    }
    
    function getAllParkingPayment($search=null){
    	$db=$this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('pd.branch_id');
    	
    	$sql=" SELECT 
    				pd.id,
    				pd.receipt_no,
    				p.customer_code,
    				p.name,
		       		p.phone,
		       		parking_moto_fee,
		       		parking_bike_fee,
		       		broken_thing_fee,
		       		total_fee,
		       		pd.note,
		      		(SELECT CONCAT(first_name) FROM rms_users WHERE rms_users.id=pd.user_id LIMIT 1) AS user_name,
		      		pd.create_date
		      	FROM 
		      		rms_parking AS p,
		      		rms_parking_detail AS pd
		      	WHERE 
    				p.id=pd.parking_id
    				and pd.status=1
    				and is_void=0
    				$branch_id
    		";
    	$where = '';
    	
    	$from_date =(empty($search['start_date']))? '1': "pd.create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': "pd.create_date <= '".$search['end_date']." 23:59:59'";
    	$where .= " AND ".$from_date." AND ".$to_date;
    	
    	if(!empty($search["title"])){
    		$s_where=array();
    		$s_search = addslashes(trim($search['title']));
    		$s_where[]="  p.customer_code LIKE '%{$s_search}%'";
    		$s_where[]="  p.name LIKE '%{$s_search}%'";
    		$s_where[]="  p.phone LIKE '%{$s_search}%'";
    		$s_where[]="  p.email LIKE '%{$s_search}%'";
    		$s_where[]="  p.address LIKE '%{$s_search}%'";
    		
    		$s_where[]="  pd.receipt_no LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    	
    	
    	if($search['cus_name']>0){
    		$where.=' AND p.id='.$search["cus_name"];
    	}
    	if($search['branch']>0){
    		$where.=' AND pd.branch_id='.$search["branch"];
    	}
    	
    	$order=" ORDER BY pd.id DESC";
    	//echo $sql.$where;
    	return $db->fetchAll($sql.$where.$order);
    }
    
    function getCheckCustomer($id){
    	$db=$this->getAdapter();
    	$sql="SELECT cus_id FROM rms_customer_paymentdetail WHERE STATUS=1 AND cus_id=$id";
    	return $db->fetchRow($sql);
    }
 
	public function addParkingPayment($data){
		 
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$arr=array(
					"branch_id" 	=> 	$data["branch"],
					"customer_code" => 	$data["cus_id"],
					"name"    		=> 	$data["cus_name"],
					"sex"  			=> 	$data["sex"],
					"phone"  		=> 	$data["phone"],
					"email"  		=> 	$data['email'],
					"address"		=> 	$data['address'],
					"user_id"       => 	$this->getUserId(),
					"create_date"   => 	$data["create_date"],
					
			);
			if(empty($data['is_new_cu'])){
				$parking_id = $this->insert($arr); 
			}else{
				$where="id = ".$data['old_cus'];
				$parking_id=$data['old_cus'];
				$this->update($arr, $where);
			}
			
			$arr_payment=array(
					"parking_id"     	=> 	$parking_id,
					"receipt_no"  		=> 	$data["receipt_no"],
					
					"parking_moto_fee_in_riel"  => 	$data["moto_total_in_riel"],
					"parking_moto_fee"  => 	$data["moto_total_in_dollar"],
					"moto_for_date"     => 	$data["moto_for_date"],
					
					"parking_bike_fee_in_riel"  => 	$data["bike_total_in_riel"],
					"parking_bike_fee"  => 	$data["bike_total_in_dollar"],
					"bike_for_date"  	=> 	$data['bike_for_date'],
					
					"broken_thing_fee_in_riel"  => 	$data["broken_in_riel"],
					"broken_thing_fee"  => 	$data["broken_total_in_dollar"],
					"broken_for_date"  	=> 	$data['broken_for_date'],
					
					"total_fee"  		=> 	$data["all_total_amount"],
					
					"note"  			=> 	$data["note"],
					
					"create_date"   	=> 	$data["create_date"],
					"branch_id" 		=> 	$data["branch"],
					"user_id"     		=> 	$this->getUserId(),
			);
			$this->_name="rms_parking_detail";
			$this->insert($arr_payment);
			$db->commit();
			
		}catch(Exception $e){
			$db->rollBack();
			echo $e->getMessage();
		}
	}
	 
	public function editCustomerPayment($data){
		 
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$arr=array(
					"branch_id" 	=> 	$data["branch"],
					"customer_code" => 	$data["cus_id"],
					"name"    		=> 	$data["cus_name"],
					"sex"  			=> 	$data["sex"],
					"phone"  		=> 	$data["phone"],
					"email"  		=> 	$data['email'],
					"address"		=> 	$data['address'],
					"user_id"       => 	$this->getUserId(),
					//"status"        => 	$data['status'],
			);
			
			$where="id=".$data['old_cus'];
			$parking_id = $data['old_cus'];
			$this->update($arr, $where);
			
			
			$arr_payment=array(
					"parking_id"     	=> 	$parking_id,
					"receipt_no"  		=> 	$data["receipt_no"],
					
					"parking_moto_fee_in_riel"  => 	$data["moto_total_in_riel"],
					"parking_moto_fee"  => 	$data["moto_total_in_dollar"],
					"moto_for_date"     => 	$data["moto_for_date"],
					
					"parking_bike_fee_in_riel"  => 	$data["bike_total_in_riel"],
					"parking_bike_fee"  => 	$data["bike_total_in_dollar"],
					"bike_for_date"  	=> 	$data['bike_for_date'],
					
					"broken_thing_fee_in_riel"  => 	$data["broken_in_riel"],
					"broken_thing_fee"  => 	$data["broken_total_in_dollar"],
					"broken_for_date"  	=> 	$data['broken_for_date'],
					
					"total_fee"  		=> 	$data["all_total_amount"],
					
					"note"  			=> 	$data["note"],
					
					"branch_id" 		=> 	$data["branch"],
					"user_id"     		=> 	$this->getUserId(),
			);
			$this->_name="rms_parking_detail";
			$where=" id = ".$data['id'];
			$this->update($arr_payment, $where);
			$db->commit();
			
		}catch(Exception $e){
			$db->rollBack();
			echo $e->getMessage();
		}
	}
	
	function getParkingById($id){
		$db=$this->getAdapter();
		$sql="SELECT 
					*
		      	FROM 
		      		rms_parking AS p,
		      		rms_parking_detail AS pd
				WHERE 
					p.id=pd.parking_id
					AND pd.id=$id
			";
		return $db->fetchRow($sql);
	}
	
	function getCusId(){
		$db=$this->getAdapter();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$branch_id = $_db->getAccessPermission('branch_id');
		
		$sql="SELECT id FROM rms_parking where status=1 $branch_id ORDER BY id DESC";
		$amount=$db->fetchOne($sql);
		
		$new_amount = $amount + 1;
		
		$length = strlen($new_amount);
		
		$prefix = 'P';
		
		for($i=$length;$i<4;$i++){
			$prefix.='0';
		}
		return $prefix.$new_amount;
	} 
	
	function getReceiptNo(){
		$db=$this->getAdapter();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$branch_id = $_db->getAccessPermission('branch_id');
		
		$sql="SELECT id FROM rms_parking_detail WHERE 1 $branch_id ORDER BY id DESC";
		$amount=$db->fetchOne($sql);
		
		$new_amount = $amount + 1;
		
		$length = strlen($new_amount);
		
		$prefix = '';
		
		for($i=$length;$i<6;$i++){
			$prefix.='0';
		}
		return $prefix.$new_amount;
	}
	
	function getOldCustomer(){
		$db=$this->getAdapter();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$branch_id = $_db->getAccessPermission('branch_id');
		
		$sql="SELECT id,name FROM rms_parking WHERE status=1 $branch_id ";
		return $db->fetchAll($sql);
	}
	
	function getCustomerInfo($id){
		$db=$this->getAdapter();
		$sql="SELECT  
					p.customer_code,
					p.name,
					p.sex,
					p.phone,
					p.email,
					p.address,
			    	pd.*  
			    FROM 
			    	rms_parking AS p,
			    	rms_parking_detail AS pd
				WHERE 
					p.id=pd.parking_id
					AND pd.parking_id=$id
			";
		return $db->fetchRow($sql);
	}
	
	//select custoemr name
	function getAllCustomerName(){
		$db=$this->getAdapter();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$branch_id = $_db->getAccessPermission('branch_id');
		
		$sql="SELECT id,name FROM rms_parking WHERE status=1 $branch_id ";
		$order=" ORDER BY id DESC";
		return $db->fetchAll($sql.$order);
	}
	
	function getReilMoney(){
		$db=$this->getAdapter();
		$sql="SELECT reil FROM rms_exchange_rate WHERE active=1";
		return $db->fetchRow($sql);
	}
	
	function getOldCustomerByBranch($branch_id){
		$db=$this->getAdapter();
		$sql="SELECT id,name FROM rms_parking WHERE status=1 and branch_id = $branch_id ";
		$order=" ORDER BY id DESC ";
		return $db->fetchAll($sql.$order);
	}
	
	function getCusIdByBranch($branch_id){
		$db=$this->getAdapter();
		$sql="SELECT count(id) as id FROM rms_parking where 1 and branch_id=$branch_id LIMIT 1 ";
		$amount=$db->fetchOne($sql);
	
		$new_amount = $amount + 1;
	
		$length = strlen($new_amount);
	
		$prefix = 'P';
	
		for($i=$length;$i<4;$i++){
			$prefix.='0';
		}
	
		$cus_id = $prefix.$new_amount;
	
		return $cus_id;
	}
	
	function getReceiptByBranch($branch_id){
		$db=$this->getAdapter();
		$sql="SELECT count(id) FROM rms_parking_detail WHERE 1 and branch_id=$branch_id limit 1 ";
		$amount=$db->fetchOne($sql);
	
		$new_amount = $amount + 1;
	
		$length = strlen($new_amount);
	
		$prefix = '';
	
		for($i=$length;$i<6;$i++){
			$prefix.='0';
		}
		return $prefix.$new_amount;
	}
	
	
}



