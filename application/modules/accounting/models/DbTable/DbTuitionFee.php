<?php

class Accounting_Model_DbTable_DbTuitionFee extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_tuitionfee';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function getAllTuitionFee($search=null){  
    	$db=$this->getAdapter();
    	$sql = "SELECT t.id,
    				  (select CONCAT(branch_namekh) from rms_branch where br_id =t.branch_id LIMIT 1) as branch,
					  CONCAT(t.from_academic,' - ',t.to_academic) AS academic, t.generation,
					  
					  (SELECT name_en FROM `rms_view`  WHERE `rms_view`.`type` = 7 AND `rms_view`.`key_code` = t.time) AS `time`,
					  t.create_date,  
					  (select name_kh from rms_view where type=1 and key_code=t.status) as status 
					  FROM `rms_tuitionfee` AS t
					 WHERE 1	";
    	$where =" ";
    	
	    if(!empty($search['txtsearch'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['txtsearch']));
		 	$s_where[] = " CONCAT(from_academic,'-',to_academic) LIKE '%{$s_search}%'";
	    	$s_where[] = " t.generation LIKE '%{$s_search}%'";
// 	    	$s_where[] = " en_name LIKE '%{$s_search}%'";
	    	$where .=' AND ( '.implode(' OR ',$s_where).')';
	    }
	    if(!empty($search['year'])){
	    	$where.=" AND t.id=".$search['year'];
	    }
	    
	    if(!empty($search['branch_id'])){
	    	$where.=" AND t.branch_id=".$search['branch_id'];
	    }
	    
	    $dbp = new Application_Model_DbTable_DbGlobal();
	    $where.=$dbp->getAccessPermission();
	    
	    $order=" GROUP BY t.branch_id,t.from_academic,t.to_academic,t.generation,t.time ORDER BY t.id DESC  ";
	    
// 	    echo ($sql.$where.$order);
    	return $db->fetchAll($sql.$where.$order);
    }
    function getFeebyOther($fee_id){
    	$db = $this->getAdapter();
    	$sql = "select *,(SELECT CONCAT(name_en,'-',name_kh) FROM rms_view WHERE rms_tuitionfee_detail.session=rms_view.key_code AND rms_view.type=4)  AS `session`,
		(SELECT CONCAT(major_enname,'-',major_khname) FROM `rms_major` WHERE major_id=rms_tuitionfee_detail.class_id) as class
    	from rms_tuitionfee_detail where fee_id =".$fee_id." ORDER BY id";
    	return $db->fetchAll($sql);
    }
    function getCondition($_data){
    	$db = $this->getAdapter();
    	$find="select id from rms_tuitionfee where from_academic=".$_data['from_year']." and to_academic=".$_data['to_year']." 
    		   and generation='".$_data['generation']."' and time=".$_data['time']." AND branch_id = ".$_data['branch'];
    	
    	return $db->fetchOne($find);
    }
    ////////////////
    public function addTuitionFee($_data){
    //	print_r($_data);exit();
    	$db = $this->getAdapter();
    	$db->beginTransaction();
		
    	$fee_id = $this->getCondition($_data);
    	 
    	try{
    		if(!empty($fee_id)){
    			
    		}else{
	    		$_arr = array(
	    				'from_academic'=>$_data['from_year'],
	    				'to_academic'=>$_data['to_year'],
	    				'generation'=>$_data['generation'],
	    				'note'=>$_data['note'],
	    				'time'=>$_data['time'],
	    				'branch_id'=>$_data['branch'],
// 	    				'start_date'=>$_data['start_date'],
// 	    				'end_date'=>$_data['end_date'],
	    				'create_date'=>date("Y-m-d"),
	    				'user_id'=>$this->getUserId()
	    				);
	    		$fee_id = $this->insert($_arr);
    		}
	    		
	    		$this->_name='rms_tuitionfee_detail';
	    		$ids = explode(',', $_data['identity']);
	    		$id_term =explode(',', $_data['iden_term']);
	    		foreach ($ids as $i){
	    			foreach ($id_term as $j){
	    				$_arr = array(
	    						'fee_id'=>$fee_id,
	    						'class_id'=>$_data['class_'.$i],
	    						//'session'=>$_data['session_'.$i],
	    						'payment_term'=>$j,
	    						'tuition_fee'=>$_data['fee'.$i.'_'.$j],
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
    public function updateTuitionFee($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    
    		$_arr = array(
    				'from_academic'=>$_data['from_year'],
    				'to_academic'=>$_data['to_year'],
    				'generation'=>$_data['generation'],
    				'note'=>$_data['note'],
    				'status'=>$_data['status'],
    				'time'=>$_data['time'],
    				'branch_id'=>$_data['branch'],
//     				'start_date'=>$_data['start_date'],
//     				'end_date'=>$_data['end_date'],
    				//'create_date'=>date("Y-m-d"),
    				'user_id'=>$this->getUserId(),
    				
    				'finished'=>$_data['finished'],
    				
    		);
//     		$fee_id = $this->insert($_arr);
    		$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    		$this->update($_arr, $where);
    
    		$this->_name='rms_tuitionfee_detail';
    		$where = "fee_id = ".$_data['id'];
    		$this->delete($where);
    		$ids = explode(',', $_data['identity']);
    		$id_term =explode(',', $_data['iden_term']);
    		foreach ($ids as $i){
    			foreach ($id_term as $j){
    				$_arr = array(
    						'fee_id'=>$_data['id'],
    						'class_id'=>$_data['class_'.$i],
    						//'session'=>$_data['session_'.$i],
    						'payment_term'=>$j,
    						'tuition_fee'=>$_data['fee'.$i.'_'.$j],
    						'remark'=>$_data['remark'.$i],
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
    public function setServiceChargeExist($service_id,$pay_type){
    	$db = $this->getAdapter();
    	$sql = "SELECT servicefee_id,price FROM `rms_servicefee_detail` WHERE service_id=$service_id AND pay_type=$pay_type ";
    	return $db->fetchRow($sql);
    	//batch ,metion OR faculty,payment_term,(degree_type)
    }
    
    public function getFeeDetailById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM rms_tuitionfee_detail WHERE fee_id = ".$id ." ORDER BY id";
    	return $db->fetchAll($sql);
    
    }
    
    public function getFeeById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM rms_tuitionfee WHERE id = ".$id;
    	$dbp = new Application_Model_DbTable_DbGlobal();
    	$sql.=$dbp->getAccessPermission();
    	$sql.=" LIMIT 1 ";
    	return $db->fetchRow($sql);
    }
    public function updateFee($_data){
    	$db = $this->getAdapter();
  		try{
    		$_arr = array(
    				'from_academic'=>$_data['from_year'],
    				'to_academic'=>$_data['to_year'],
    				'generation'=>$_data['generation'],
    				'note'=>$_data['note'],
    				'status'=>1,
    				'create_date'=>$_data['create_date'],
    				'user_id'=>$this->getUserId()
    				);
    		$fee_id = $this->insert($_arr);
    		
    		$this->_name='rms_tuitionfee_detail';
    		$ids = explode(',', $_data['identity']);
    		$id_term =explode(',', $_data['iden_term']);
    		foreach ($ids as $i){
    			foreach ($id_term as $j){
    				$_arr = array(
    						'fee_id'=>$fee_id,
    						'class_id'=>$_data['class_'.$i],
    						'payment_term'=>$j,
    						'tuition_fee'=>$_data['fee'.$i.'_'.$j],
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
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
    function getSession(){
    	$db=$this->getAdapter();
    	$sql="SELECT key_code AS id,CONCAT(name_en,'-',name_kh) AS `name` FROM rms_view WHERE `type`=4 AND `status`=1 ";
    	return $db->fetchAll($sql);
    }
    function getAceYear(){
    	$db=$this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission();
    	
    	$sql="SELECT id,CONCAT(from_academic,'-',to_academic,'(',generation,')',' ',(select name_en from rms_view where type=7 and key_code=time)) AS `name`     
                     FROM rms_tuitionfee WHERE `status`=1 $branch_id  group by from_academic,to_academic,generation,time ";
        $oder=" ORDER BY id DESC ";
    	return $db->fetchAll($sql.$oder);
    }
    function getGrad(){
    	$db=$this->getAdapter();
    	$sql="SELECT major_id AS id,CONCAT(major_enname,'-',major_khname) AS `name` FROM rms_major WHERE is_active=1";
    	return $db->fetchAll($sql);
    }
 
    function getAllBranch(){
    	$db = $this->getAdapter();
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('br_id');
    	$sql="select br_id as id, CONCAT(branch_namekh) as name from rms_branch where status=1  $branch_id ";
    	return $db->fetchAll($sql);
    } 
    
    
}