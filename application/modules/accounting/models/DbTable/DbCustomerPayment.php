<?php

class Accounting_Model_DbTable_DbCustomerPayment extends Zend_Db_Table_Abstract
{
 	protected $_name = 'rms_customer';
 	
	public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
    
    public function getBranchId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->branch_id;
    }
    
    function getAllCustomer($search=null){
    	$db=$this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('cp.branch_id');
    	
    	$sql="SELECT 
    				cp.id,
    				(select branch_namekh from rms_branch where br_id = cp.branch_id) as branch_name,
    				cp.rent_receipt_no,
    				c.customer_code,
    				c.first_name,
		      		c.phone,
		      		c.email,
		      		c.start_date,
		      		c.end_date,
			        (SELECT name_en FROM rms_view WHERE key_code=cp.last_piad AND TYPE=11 LIMIT 1 ) AS `last_piad`,
			        (SELECT CONCAT(first_name,' ',last_name) FROM rms_users WHERE rms_users.id=cp.user_id LIMIT 1) AS user_name,
			        cp.create_date,
			        (SELECT name_en FROM rms_view WHERE key_code=cp.status AND TYPE=1 LIMIT 1) AS `status` 
		      	FROM 
		      		rms_customer AS c,
		      		rms_customer_paymentdetail AS cp
		      	WHERE 
    				c.id=cp.cus_id  
    				and cp.status=1
    				$branch_id
    		";
    	
    	$where = '';
    	
    	$from_date =(empty($search['start_date']))? '1': "cp.create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': "cp.create_date <= '".$search['end_date']." 23:59:59'";
    	$where = " AND ".$from_date." AND ".$to_date;
    	if(!empty($search["title"])){
    		$s_where=array();
    		$s_search = addslashes(trim($search['title']));
    		$s_where[]="  c.customer_code LIKE '%{$s_search}%'";
    		$s_where[]="  c.first_name LIKE '%{$s_search}%'";
    		$s_where[]="  c.phone LIKE '%{$s_search}%'";
    		$s_where[]="  c.email LIKE '%{$s_search}%'";
    		$s_where[]="  c.address LIKE '%{$s_search}%'";
    		
    		$s_where[]="  cp.rent_receipt_no LIKE '%{$s_search}%'";
    		$s_where[]="  cp.water_total LIKE '%{$s_search}%'";
    		$s_where[]="  cp.fire_total LIKE '%{$s_search}%'";
    		$s_where[]="  cp.all_total_amount LIKE '%{$s_search}%'";
    		$s_where[]="  cp.paid LIKE '%{$s_search}%'";
    		$s_where[]="  cp.balance LIKE '%{$s_search}%'";
    		$s_where[]="  cp.rent_paid LIKE '%{$s_search}%'";
    		$s_where[]="  cp.hygiene_price LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    	
    	if($search["status_search"]>-1){
    		$where.=' AND cp.status='.$search["status_search"];
    	}
    	
    	if($search['cus_name']>0){
    		$where.=' AND c.id='.$search["cus_name"];
    	}
    	if($search['branch']>0){
    		$where.=' AND cp.branch_id='.$search["branch"];
    	}
    	
    	$group=" GROUP BY cp.rent_receipt_no ";
    	$order=" ORDER BY c.customer_code DESC";
    	//echo $sql.$where;
    	return $db->fetchAll($sql.$where.$group.$order);
    }
    
    function getCheckCustomer($id){
    	$db=$this->getAdapter();
    	$sql="SELECT cus_id FROM rms_customer_paymentdetail WHERE status=1 AND cus_id=$id";
    	return $db->fetchRow($sql);
    }
 
	public function addCusPayment($data){
		 
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$arr=array(
					"branch_id" 	=> 	$data["branch"],
					"customer_code" => 	$data["cus_id"],
					"first_name"    => 	$data["cus_name"],
					"sex"  			=> 	$data["sex"],
					"phone"  		=> 	$data["phone"],
					"start_date"	=> 	date("Y-m-d",strtotime($data['start_date'])),
					"end_date"      => 	date("Y-m-d",strtotime($data['end_date'])),
					"email"  		=> 	$data['email'],
					"address"		=> 	$data['address'],
					"user_id"       => 	$this->getUserId(),
					"status"        => 	$data['status'],
			);
			if(empty($data['is_new_cu'])){
				$cus_id= $this->insert($arr); 
			}else{
				$where="id=".$data['old_cus'];
				$cus_id=$data['old_cus'];
				$this->update($arr, $where);
			}
			
			//check customer 
			$cus=$this->getCheckCustomer($cus_id);
			if(!empty($cus)){
				$arr_status=array(
						"last_piad"  		=> 	0,
						);
				$this->_name="rms_customer_paymentdetail";
				$where=" cus_id=".$cus['cus_id'];
				$this->update($arr_status, $where);
			}
			$arr_payment=array(
					"cus_id"     		=> 	$cus_id,
					"water_old_congtor" => 	$data["water_old_congtor"],
					"water_new_congtor" => 	$data['water_new_congtor'],
					"water_qty"   		=> 	$data['water_qty'],
					"water_cost"		=> 	$data['water_cost'],
					"water_total"       => 	$data['water_total'],
					"water_exc_rate"    => 	$data['water_exc_rate'],
					"water_start_date"  => 	date("Y-m-d",strtotime($data['water_start_date'])),
					"water_end_date"  	=> 	date("Y-m-d",strtotime($data['water_end_date'])),
					
					"fire_old_congtor"  => 	$data['fire_old_congtor'],
					"fire_new_congtor"  => 	$data['fire_new_congtor'],
					"fire_qty"     		=> 	$data["fire_qty"],
					"fire_cost"     	=> 	$data["fire_cost"],
					"fire_total"   		=> 	$data["fire_total"],
					"fire_exc_rate"   	=> 	$data["fire_exc_rate"],
					"fire_start_date"   => 	date("Y-m-d",strtotime($data['fire_start_date'])),
					"fire_end_date"     => 	date("Y-m-d",strtotime($data['fire_end_date'])),
					
					"rent_date_paid"    => 	date("Y-m-d",strtotime($data['rent_date'])),
					"rent_receipt_no"   => 	$data["receipt_no"],
					"rent_paid"     	=> 	$data["rent_paid"],
					"rent_start_date"   => 	date("Y-m-d",strtotime($data['rent_start_date'])),
					"rent_end_date"   	=> 	date("Y-m-d",strtotime($data['rent_end_date'])),
					
					"hygiene_price"     => 	$data['hygiene_price'],
					"hygiene_start_date"=> 	date("Y-m-d",strtotime($data['hygiene_start_date'])),
					"hygiene_end_date"  => 	date("Y-m-d",strtotime($data['hygiene_end_date'])),
					"hygiene_note"  	=> 	$data['hygiene_note'],
					
					
					"other_price"     	=> 	$data['other_price'],
					"other_start_date"	=> 	date("Y-m-d",strtotime($data['other_start_date'])),
					"other_end_date"  	=> 	date("Y-m-d",strtotime($data['other_end_date'])),
					"other_note"  		=> 	$data['other_note'],
					
					"create_date"		=>	$data['create_date'],
					
					"all_total_amount"  => 	$data["all_total_amount"],
					"paid"     			=> 	$data["paid"],
					"balance"     		=> 	$data["balance"],
					"status"  			=> 	$data["status"],
					"note"  			=> 	$data['note'],
					
					"branch_id" 		=> 	$data["branch"],
					"user_id"     		=> 	$this->getUserId(),
					"last_piad"  		=> 	1,
			);
			$this->_name="rms_customer_paymentdetail";
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
					"customer_code" => 	$data["cus_id"],
					"first_name"    => 	$data["cus_name"],
					"sex"  			=> 	$data["sex"],
					"phone"  		=> 	$data["phone"],
					"start_date"	=> 	date("Y-m-d",strtotime($data['start_date'])),
					"end_date"      => 	date("Y-m-d",strtotime($data['end_date'])),
					"email"  		=> 	$data['email'],
					"address"		=> 	$data['address'],
					"user_id"       => 	$this->getUserId(),
					//"status"        => 	$data['status'],
			);
			if (empty($data['is_new_cu'])){
				$where="id=".$data['customer_id'];
				$this->update($arr, $where);
				$cus_id=$data['customer_id'];
			}else{
				$where="id=".$data['old_cus'];
				$cus_id=$data['old_cus'];
				$this->update($arr, $where);
			}
			//check customer
			$cus=$this->getCheckCustomer($cus_id);
			if(!empty($cus)){
				$arr_status=array(
						"last_piad"  		=> 	0,
				);
				$this->_name="rms_customer_paymentdetail";
				$where=" cus_id=".$cus['cus_id'];
				$this->update($arr_status, $where);
			}else{}
			$arr_payment=array(
					"cus_id"     		=> 	$data['customer_id'],
					"water_old_congtor" => 	$data["water_old_congtor"],
					"water_new_congtor" => 	$data['water_new_congtor'],
					"water_qty"   		=> 	$data['water_qty'],
					"water_cost"		=> 	$data['water_cost'],
					"water_total"       => 	$data['water_total'],
					"water_exc_rate"    => 	$data['water_exc_rate'],
					"water_start_date"  => 	date("Y-m-d",strtotime($data['water_start_date'])),
					"water_end_date"  	=> 	date("Y-m-d",strtotime($data['water_end_date'])),
						
					"fire_old_congtor"  => 	$data['fire_old_congtor'],
					"fire_new_congtor"  => 	$data['fire_new_congtor'],
					"fire_qty"     		=> 	$data["fire_qty"],
					"fire_cost"     	=> 	$data["fire_cost"],
					"fire_total"   		=> 	$data["fire_total"],
					"fire_exc_rate"   	=> 	$data["fire_exc_rate"],
					"fire_start_date"   => 	date("Y-m-d",strtotime($data['fire_start_date'])),
					"fire_end_date"     => 	date("Y-m-d",strtotime($data['fire_end_date'])),
						
					"rent_date_paid"    => 	date("Y-m-d",strtotime($data['rent_date'])),
					"rent_receipt_no"   => 	$data['receipt_no'],
					"rent_paid"     	=> 	$data["rent_paid"],
					"rent_start_date"   => 	date("Y-m-d",strtotime($data['rent_start_date'])),
					"rent_end_date"   	=> 	date("Y-m-d",strtotime($data['rent_end_date'])),
						
					"hygiene_price"     => 	$data['hygiene_price'],
					"hygiene_start_date"=> 	date("Y-m-d",strtotime($data['hygiene_start_date'])),
					"hygiene_end_date"  => 	date("Y-m-d",strtotime($data['hygiene_end_date'])),
					"hygiene_note"  	=> 	$data['hygiene_note'],
					
					"parking_price"     => 	$data['parking_price'],
					"parking_start_date"=> 	date("Y-m-d",strtotime($data['parking_start_date'])),
					"parking_end_date"  => 	date("Y-m-d",strtotime($data['parking_end_date'])),
					"parking_note"  	=> 	$data['parking_note'],
					
					"other_price"     	=> 	$data['other_price'],
					"other_start_date"	=> 	date("Y-m-d",strtotime($data['other_start_date'])),
					"other_end_date"  	=> 	date("Y-m-d",strtotime($data['other_end_date'])),
					"other_note"  		=> 	$data['other_note'],
					
					"all_total_amount"  => 	$data["all_total_amount"],
					"paid"     			=> 	$data["paid"],
					"balance"     		=> 	$data["balance"],
					"status"  			=> 	$data["status"],
					"note"  			=> 	$data['note'],
					
					"branch_id"     	=> 	$this->getBranchId(),
					"user_id"     		=> 	$this->getUserId(),
					"last_piad"  		=> 	1,
			);
			$this->_name="rms_customer_paymentdetail";
			$where=" id=".$data['id'];
			$this->update($arr_payment, $where);
			$db->commit();
			
		}catch(Exception $e){
			$db->rollBack();
			echo $e->getMessage();
		}
	}
	
	function getCustomerById($id){
		$db=$this->getAdapter();
		$sql="SELECT  c.customer_code,c.first_name,c.sex,c.phone,c.email,c.address,c.start_date,c.end_date,
		      cp.*,cp.cus_id As customer_id FROM rms_customer AS c,rms_customer_paymentdetail AS cp
				WHERE c.id=cp.cus_id
				AND cp.id=$id";
		return $db->fetchRow($sql);
	}
	
	function getCusId(){
		$db=$this->getAdapter();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$branch_id = $_db->getAccessPermission('branch_id');
		
		$sql="SELECT id FROM rms_customer where 1 $branch_id ORDER BY id DESC";
		$amount=$db->fetchOne($sql);
		
		$new_amount = $amount + 1;
		
		$length = strlen($new_amount);
		
		$prefix = 'C';
		
		for($i=$length;$i<4;$i++){
			$prefix.='0';
		}
		return $prefix.$new_amount;
	} 
	
	function getReceiptNo(){
		$db=$this->getAdapter();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$branch_id = $_db->getAccessPermission('branch_id');
		
		$sql="SELECT id FROM rms_customer_paymentdetail WHERE 1 $branch_id ORDER BY id DESC";
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
		
		$sql="SELECT id,first_name AS cus_name FROM rms_customer WHERE status=1 $branch_id ";
		return $db->fetchAll($sql);
	}
	
	function getCustomerInfo($id){
		$db=$this->getAdapter();
		$sql="SELECT  c.customer_code,c.first_name,c.sex,c.phone,c.email,c.address,c.start_date,c.end_date,
			    cp.*  FROM rms_customer AS c,rms_customer_paymentdetail AS cp
				WHERE c.id=cp.cus_id
				AND cp.last_piad=1
				AND cp.cus_id=$id";
		return $db->fetchRow($sql);
	}
	
	//select custoemr name
	function getAllCustomerName(){
		$db=$this->getAdapter();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$branch_id = $_db->getAccessPermission('branch_id');
		
		$sql="SELECT id,first_name AS `name` FROM rms_customer WHERE status=1 $branch_id ";
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
		$sql="SELECT id,first_name as name FROM rms_customer WHERE status=1 and branch_id = $branch_id ";
		$order=" ORDER BY id DESC ";
		return $db->fetchAll($sql.$order);
	}
	
	function getCusIdByBranch($branch_id){
		$db=$this->getAdapter();
		$sql="SELECT count(id) as id FROM rms_customer where 1 and branch_id=$branch_id LIMIT 1 ";
		$amount=$db->fetchOne($sql);
		
		$new_amount = $amount + 1;
		
		$length = strlen($new_amount);
		
		$prefix = 'C';
		
		for($i=$length;$i<4;$i++){
			$prefix.='0';
		}
		
		$cus_id = $prefix.$new_amount;
		
		return $cus_id;
	}
	
	function getReceiptByBranch($branch_id){
		$db=$this->getAdapter();
		$sql="SELECT count(id) FROM rms_customer_paymentdetail WHERE 1 and branch_id=$branch_id limit 1 ";
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



