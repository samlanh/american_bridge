<?php

class Accounting_Model_DbTable_DbServiceCharge extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_servicefee';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
    
    function getAceYear(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,
    				CONCAT(from_academic,'-',to_academic,'(',(select branch_namekh from rms_branch where br_id = branch_id),')') as name
    		  	FROM 
    				rms_servicefee 
    			WHERE 
    				`status`=1 
    		";
    	$oder=" ORDER BY id DESC ";
    	return $db->fetchAll($sql.$oder);
    }
    
    
	public function sqltuitionfee($search=''){
    	$sql = "SELECT p.service_id as id,p.`title` AS service_name,p.status,t.title as cate_name FROM `rms_program_name` AS p,`rms_program_type` AS t
					WHERE t.id=p.ser_cate_id ";
    	$order=" ORDER BY p.title";
    	$where = '';
    	if(empty($search)){
    		return $sql.$order;
    	}
    	if(!empty($search['txtsearch'])){
    		$where.=" AND title LIKE '%".$search['txtsearch']."%'";
    	}
    	if($search['type']>-1){
    		$where.= " AND type = ".$search['type'];
    	}
    	if($search['status']>-1){
    		$where.= " AND status = '".$search['status']."'";
    	}
    	return $sql.$where.$order;
    }
    
    function getAllServiceFee($search){
	
    try{		
    	$db=$this->getAdapter();
    	$sql = "SELECT 
    				sf.id,
    				CONCAT(from_academic,'-',to_academic) as academic_year,
    				(select branch_namekh from rms_branch where br_id = branch_id) as branch_name,
    		 		note,
    		 		create_date,
    		 		(select name_kh from rms_view where type=1 and key_code =status) as status, 
    				(select first_name from rms_users where rms_users.id = user_id) as user
    			 FROM 
    				rms_servicefee as sf 
    			where 
    				status = 1
    			";
    	$order=" ORDER BY id DESC ";
    	$where = " ";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	
    	if(!empty($search['year'])){
    		$where .=" AND id=".$search['year'];
    	}
    	
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " CONCAT(from_academic,'-',to_academic) LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	//echo $sql.$where.$order;
    	return $db->fetchAll($sql.$where.$order);
    }catch(Exception $e){
    	echo $e->getMessage();
    }
    }
    
    
    function getCondition($_data){
    	$db = $this->getAdapter();
//     	$find="select id from rms_servicefee where from_academic=".$_data['from_year']." and to_academic=".$_data['to_year']."
//     	and generation='".$_data['generation']."'";
    	 
    	$sql = "select 
    					sf.id 
    				from 
    					rms_servicefee as sf
    				where 
    					sf.from_academic = ".$_data['from_academic']." 
    					and sf.to_academic = ".$_data['to_academic']."
    					and sf.branch_id = ".$_data['branch_id']
    			;
    	
    	return $db->fetchOne($sql);
    }
    
    public function addServiceCharge($_data){
    	
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	//$service_id='';
    	$service_id = $this->getCondition($_data);
    	
    	try{   	

    		if(!empty($service_id)){
    			 
    		}else{
	    		$_arr = array(
	    				'from_academic'	=>$_data['from_academic'],
	    				'to_academic'	=>$_data['to_academic'],
	    				'branch_id'		=>$_data['branch_id'],
	    				'note'			=>$_data['note'],
	    				'status'		=>$_data['status'],
	    				'create_date'	=>date("Y-m-d"),
	    				'user_id'		=>$this->getUserId()
	    				);
	    		$service_id = $this->insert($_arr);
    		}
    		
    		$this->_name='rms_servicefee_detail';
    		$ids = explode(',', $_data['identity']);
    		$id_term =explode(',', $_data['iden_term']);
    		foreach ($ids as $i){
    			foreach ($id_term as $j){
    				$_arr = array(
    						'service_feeid'=>$service_id,
    						'service_id'=>$_data['class_'.$i],
    						'payment_term'=>$j,
    						'price_fee'=>$_data['fee'.$i.'_'.$j],
    						'remark'=>$_data['remark'.$i]
    				);
    				$this->insert($_arr);
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
    public function updateServiceCharge($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{    
    		$_arr = array(
    			'from_academic'	=>$_data['from_academic'],
	    		'to_academic'	=>$_data['to_academic'],
    			'branch_id'		=>$_data['branch_id'],
    			'note'			=>$_data['note'],
    			'status'		=>$_data['status'],
    			'is_finished'	=>$_data['finished'],
    			'user_id'		=>$this->getUserId()
	    	);
	    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
	    	$this->update($_arr, $where);
    
	    	if($_data['finished']==0 && $_data['status']==1 ){	
	    		
	    		$this->_name='rms_servicefee_detail';
	    		$where = "service_feeid = ".$_data['id'];
	    		$this->delete($where);
	    		
	    		
	    		$ids = explode(',', $_data['identity']);
	    		$id_term =explode(',', $_data['iden_term']);
	    		foreach ($ids as $i){
	    			foreach ($id_term as $j){
	    				$_arr = array(
	    						'service_feeid'	=>$_data['id'],
	    						'service_id'	=>$_data['class_'.$i],
	    						'payment_term'	=>$j,
	    						'price_fee'		=>$_data['fee'.$i.'_'.$j],
	    						'remark'		=>$_data['remark'.$i]
	    				);
	     				$this->insert($_arr);
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
    function getServiceFeebyId($service_id){
    	
    	$db = $this->getAdapter();
    	$sql = "SELECT id,service_id,price_fee,payment_term,remark,(select title from rms_program_name where rms_program_name.service_id=rms_servicefee_detail.service_id limit 1)AS service_name FROM `rms_servicefee_detail` WHERE service_feeid=".$service_id." ORDER BY id ";
    		 
    	return $db->fetchAll($sql);
    	 
    }
    public function setServiceChargeExist($service_id,$pay_type){
    	$db = $this->getAdapter();
    	$sql = "SELECT servicefee_id,price FROM `rms_servicefee_detail` WHERE service_id=$service_id AND pay_type=$pay_type ";
    	return $db->fetchRow($sql);
    }
    public function getServiceChargeById($service_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM rms_servicefee WHERE id=$service_id LIMIT 1";
    	
    /*	$sql = "SELECT ser_cate_id,status,
    	sd.service_id,pay_type,price,remark,s.create_date
    	FROM `rms_program_name` AS s,`rms_servicefee_detail` AS sd
    	WHERE sd.service_id=s.service_id AND s.service_id=$service_id";*/
    	return $db->fetchRow($sql);
    
    }
}



