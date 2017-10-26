<?php

class Accounting_Model_DbTable_DbTransferstock extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_transferstock';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('authstu');
    	return $session_user->user_id;
    }
    function getAllTransfer($search=null){  
    	$db=$this->getAdapter();
    	$sql = "SELECT s.id,s.transfer_no,s.transfer_date,
    	(SELECT branch_nameen FROM `rms_branch` WHERE br_id=s.from_location LIMIT 1) as fromlocation,
    	(SELECT branch_nameen FROM `rms_branch` WHERE br_id=s.to_location LIMIT 1) as tolocation,
    	s.note,
    	(SELECT first_name FROM `rms_users` WHERE id=s.user_id LIMIT 1) as user_name,
    	(SELECT name_en FROM `rms_view` WHERE TYPE=1 AND key_code=s.status ) as status
    	FROM `rms_transferstock` AS s WHERE 1 ";
    	$from_date =(empty($search['start_date']))? '1': " s.transfer_date>= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': " s.transfer_date <= '".$search['end_date']." 23:59:59'";
    	$where = " AND ".$from_date." AND ".$to_date;
	    if(!empty($search['title'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['title']));
	    	$s_where[] = " s.transfer_no LIKE '%{$s_search}%'";
	    	$s_where[] = " s.note LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
	    }
    	$order=" ORDER BY s.id DESC ";
    	return $db->fetchAll($sql.$where.$order);
    }
    function getTransferById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT t.* ,
				(SELECT b.branch_nameen  FROM rms_branch AS b WHERE b.br_id=t.from_location)AS f_branch ,
				(SELECT b.branch_nameen  FROM rms_branch AS b WHERE b.br_id=t.to_location)AS t_branch 

			FROM 
				rms_transferstock AS t 
			WHERE 
				t.id='".$id."'";
    	return $db->fetchRow($sql);
    }
    function getTransferByIdDetail($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT *,
		(SELECT pro_name FROM `rms_product` WHERE id=rms_transferdetail.pro_id LIMIT 1) as pro_name 
    	FROM rms_transferdetail WHERE transferid=".$id;
    	return $db->fetchAll($sql);
    }
    public function addTransferStock($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
	    		$_arr = array(
	    				'transfer_no'=>$_data['transfer_no'],
	    				'transfer_date'=>$_data['date'],
	    				'from_location'=>$_data['f_branch'],
	    				'to_location'=>$_data['branch'],
	    				'note'=>$_data['remark'],
	    				'user_id'=>$this->getUserId(),
	    				'status'=>1,
	    				);
	    		$tranid= $this->insert($_arr);
	    		
	    		$ids = explode(',', $_data['identity']);
	    		foreach ($ids as $i){
    				$_arr = array(
    						'transferid'=>$tranid,
    						'pro_id'=>$_data['pro_id_'.$i],
    						'qty'=>$_data['qty_'.$i],
    						'note'=>$_data['remark_'.$i],);
    				$this->_name='rms_transferdetail';
    				$this->insert($_arr);
    				
    				$rs = $this->checkisProductSet($_data['pro_id_'.$i]);
    				if(empty($rs)){//normal product
	    				$qty_stock = $this->getProductLocation($_data['pro_id_'.$i],$_data['f_branch']);
	    				$this->_name="rms_product_location";
	    				if(!empty($qty_stock)){
	    					$qty = $qty_stock['pro_qty'] - $_data['qty_'.$i];
	    					$array = array(
	    							'pro_qty'=>$qty,
	    					);
	    					$where = " id = ".$qty_stock['id'];
	    					$this->update($array, $where);
	    				}
	    				//to location
	    				$qty_stock = $this->getProductLocation($_data['pro_id_'.$i],$_data['branch']);
	    				if(!empty($qty_stock)){
	    					$qty = $qty_stock['pro_qty'] + $_data['qty_'.$i];
	    					$array = array(
	    							'pro_qty'=>$qty,
	    					);
	    					$where = " id = ".$qty_stock['id'];
	    					$this->update($array, $where);
	    				}else{
	    					$data = array(
    							'pro_id'=>$_data['pro_id_'.$i],
								'brand_id'=>$_data['branch'], 
								'pro_qty'=>$_data['qty_'.$i],
								'note'=>'ពីផ្ទេរទំនិញចូល',
								'date' =>date("Y-m-d"),
								'user_id'=>$this->getUserId(),
								'status'=>1);
							$this->insert($data);
	    				}
    				}//prodcut set
    				else{
    					$rsset = $this->getProductSet($_data['pro_id_'.$i]);
    					if(!empty($rsset)){
    						foreach($rsset as $rs){
    							$qty_stock = $this->getProductLocation($rs['subpro_id'],$_data['f_branch']);
    							$this->_name="rms_product_location";
    							if(!empty($qty_stock)){
    								$qty = $qty_stock['pro_qty'] - $_data['qty_'.$i];
    								$array = array(
    										'pro_qty'=>$qty,
    								);
    								$where = " id = ".$qty_stock['id'];
    								$this->update($array, $where);
    							}
    							//to location
    							$qty_stock = $this->getProductLocation($rs['subpro_id'],$_data['branch']);
    							if(!empty($qty_stock)){
    								$qty = $qty_stock['pro_qty'] + $_data['qty_'.$i];
    								$array = array(
    										'pro_qty'=>$qty,
    								);
    								$where = " id = ".$qty_stock['id'];
    								$this->update($array, $where);
    							}else{
    								$data = array(
    										'pro_id'=>$_data['pro_id_'.$i],
    										'brand_id'=>$_data['branch'],
    										'pro_qty'=>$_data['qty_'.$i],
    										'note'=>'ពីផ្ទេរទំនិញចូល',
    										'date' =>date("Y-m-d"),
    										'user_id'=>$this->getUserId(),
    										'status'=>1
    								);
    								$this->insert($data);
    							}
    						}
    					}
    				}
	    		}
	    	    $db->commit();
	    	    return true;
    	}catch (Exception $e){
    		$db->rollBack();
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		echo $e->getMessage();exit();
    		return false;
    	}
    }
    function getProductSet($product_id){// select item for product set
    	$db = $this->getAdapter();
    	$sql="SELECT * FROM `rms_product_setdetail` WHERE pro_id  = ".$product_id;
    	return $db->fetchAll($sql);
    }
    function checkisProductSet($product_id){//if product is set
    	$db = $this->getAdapter();
    	$sql="SELECT * FROM  rms_product WHERE sale_set=1 AND id = ".$product_id;
    	return $db->fetchRow($sql);
    }
    function getTransferNo(){
    	$db = $this->getAdapter();
    	$sql="SELECT (id+1)FROM `rms_transferstock` ORDER BY id DESC  LIMIT 1";
    	$trans_no = $db->fetchOne($sql);
    	$acc_length = strlen((int)$trans_no);
    	$pre=0;
    	for($i = $acc_length;$i<5;$i++){
    		$pre.='0';
    	}
    	return $pre.$trans_no;
    }
    function getProductLocation($pro_id,$location_id){
    	$db = $this->getAdapter();
    	$sql="select * from rms_product_location where pro_id=".$pro_id." AND brand_id = ".$location_id;
    	return $db->fetchRow($sql);
    }
	public function updateTransferStock($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
	    		$_arr = array(
	    				'transfer_no'=>$_data['transfer_no'],
	    				'transfer_date'=>$_data['date'],
	    				'from_location'=>$_data['from_location'],
	    				'to_location'=>$_data['to_location'],
	    				'note'=>$_data['remark'],
	    				'user_id'=>$this->getUserId(),
	    				'status'=>1,
	    				);
	    		$where="id = ".$_data['id'];
	    		 $this->update($_arr, $where);
	    		$rsdetail = $this->getTransferByIdDetail($_data['id']);
	    		$this->_name='rms_transferdetail';
	    		
	    		if(!empty($rsdetail)){
	    			foreach($rsdetail as $rsd){
		    			$qty_stock = $this->getProductLocation($rsd['pro_id'],$_data['old_flocation']);
		    			if(!empty($qty_stock)){
		    				$qty = $qty_stock['pro_qty'] + $rsd['qty'];
		    				$array = array(
		    						'pro_qty'=>$qty,
		    				);
		    				$where = " id = ".$qty_stock['id'];
		    				$this->update($array, $where);
		    			}
		    			$qty_stock = $this->getProductLocation($rsd['pro_id'],$_data['old_tlocation']);
		    			if(!empty($qty_stock)){
		    				$qty = $qty_stock['pro_qty'] - $rsd['qty'];
		    				$array = array(
		    						'pro_qty'=>$qty,
		    				);
		    				$where = " id = ".$qty_stock['id'];
		    				$this->update($array, $where);
		    			}
	    			}
	    		}
	    		$this->_name='rms_transferdetail';
	    		$where = "transferid = ".$_data['id']; 
	    		$this->delete($where);
	    		
	    		$ids = explode(',', $_data['identity']);
    		foreach ($ids as $i){
    				$_arr = array(
    						'transferid'=>$_data['id'],
    						'pro_id'=>$_data['pro_id_'.$i],
    						'qty'=>$_data['qty_'.$i],
    						'note'=>$_data['remark_'.$i],);
    				$this->_name='rms_transferdetail';
    				$this->insert($_arr);
    				
    				$rs = $this->checkisProductSet($_data['pro_id_'.$i]);
    				if(empty($rs)){//normal product
	    				$qty_stock = $this->getProductLocation($_data['pro_id_'.$i],$_data['from_location']);
	    				$this->_name="rms_product_location";
	    				if(!empty($qty_stock)){
	    					$qty = $qty_stock['pro_qty'] - $_data['qty_'.$i];
	    					$array = array(
	    							'pro_qty'=>$qty,
	    					);
	    					$where = " id = ".$qty_stock['id'];
	    					$this->update($array, $where);
	    				}
	    				//to location
	    				$qty_stock = $this->getProductLocation($_data['pro_id_'.$i],$_data['to_location']);
	    				if(!empty($qty_stock)){
	    					$qty = $qty_stock['pro_qty'] + $_data['qty_'.$i];
	    					$array = array(
	    							'pro_qty'=>$qty,
	    					);
	    					$where = " id = ".$qty_stock['id'];
	    					$this->update($array, $where);
	    				}else{
	    					$data = array(
    							'pro_id'=>$_data['pro_id_'.$i],
								'brand_id'=>$_data['to_location'], 
								'pro_qty'=>$_data['qty_'.$i],
								'note'=>'ពីផ្ទេរទំនិញចូល',
								'date' =>date("Y-m-d"),
								'user_id'=>$this->getUserId(),
								'status'=>1);
							$this->insert($data);
	    				}
    				}//prodcut set
    				else{
    					$rsset = $this->getProductSet($_data['pro_id_'.$i]);
    					if(!empty($rsset)){
    						foreach($rsset as $rs){
    							$qty_stock = $this->getProductLocation($rs['subpro_id'],$_data['from_location']);
    							$this->_name="rms_product_location";
    							if(!empty($qty_stock)){
    								$qty = $qty_stock['pro_qty'] - $_data['qty_'.$i];
    								$array = array(
    										'pro_qty'=>$qty,
    								);
    								$where = " id = ".$qty_stock['id'];
    								$this->update($array, $where);
    							}
    							//to location
    							$qty_stock = $this->getProductLocation($rs['subpro_id'],$_data['to_location']);
    							if(!empty($qty_stock)){
    								$qty = $qty_stock['pro_qty'] + $_data['qty_'.$i];
    								$array = array(
    										'pro_qty'=>$qty,
    								);
    								$where = " id = ".$qty_stock['id'];
    								$this->update($array, $where);
    							}else{
    								$data = array(
    										'pro_id'=>$_data['pro_id_'.$i],
    										'brand_id'=>$_data['to_location'],
    										'pro_qty'=>$_data['qty_'.$i],
    										'note'=>'ពីផ្ទេរទំនិញចូល',
    										'date' =>date("Y-m-d"),
    										'user_id'=>$this->getUserId(),
    										'status'=>1
    								);
    								$this->insert($data);
    							}
    						}
    					}
    				}
	    		}
	    	    $db->commit();
	    	    return true;
    	}catch (Exception $e){
    		$db->rollBack();
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		return false;
    	}
    }
    
    function getCustomer($search=null){
    	//print_r($search);exit();
    	$db=$this->getAdapter();
    	$sql="SELECT c.* FROM rms_customer AS c,rms_customer_paymentdetail AS cp
            		WHERE c.id=cp.cus_id";
    	$where = '';
    	$from_date =(empty($search['start_date']))? '1': "c.start_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': "c.end_date <= '".$search['end_date']." 23:59:59'";
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
    	 
    	$order=" GROUP BY c.id ORDER BY c.id DESC ";
    	return $db->fetchAll($sql.$where.$order);
    }
    
    function getCusPaymentDetail($id){
    	$db=$this->getAdapter();
    	$sql="SELECT 
		       cp.*,
		       (SELECT CONCAT(first_name,' ',last_name) FROM rms_users WHERE rms_users.id=cp.user_id LIMIT 1) AS user_name
		       FROM rms_customer AS c,rms_customer_paymentdetail AS cp
		       WHERE c.id=cp.cus_id
		       AND cp.cus_id=$id";
    	return $db->fetchAll($sql);
    }
    
}