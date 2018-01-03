<?php

class Global_Model_DbTable_DbBranch extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_branch';
    function addbranch($_data){
    	$identity = $_data['identity'];
    	$ids = explode(',', $identity);
    	$phone_list="";
    	foreach ($ids as $i){
    		if (!empty($_data['phone'.$i])){
    			if (empty($phone_list)){
    				$phone_list=$_data['phone'.$i];
    			}else{
    				if (!empty($_data['phone'.$i])){
    					$phone_list=$phone_list.",".$_data['phone'.$i];
    				}
    			}
    		}
    	}
    	$_arr = array(
    			//'parent'=>$_data['main_branch_id'],
    			'branch_namekh'=>$_data['branch_nameen'],
    			'prefix'=>$_data['prefix_code'],
    			'br_address'=>$_data['br_address'],
    			'branch_code'=>$_data['branch_code'],
    			'branch_tel'=>$phone_list,
    			'fax'=>$_data['fax'],
    			'other'=>$_data['branch_note'],
    			'status'=>$_data['branch_status'],
    			'displayby'=>2,
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateBranch($_data,$id){
    	$identity = $_data['identity'];
    	$ids = explode(',', $identity);
    	$phone_list="";
    	foreach ($ids as $i){
    		if (!empty($_data['phone'.$i])){
    			if (empty($phone_list)){
    				$phone_list=$_data['phone'.$i];
    			}else{
    				if (!empty($_data['phone'.$i])){
    					$phone_list=$phone_list.",".$_data['phone'.$i];
    				}
    			}
    		}
    	}
    	$_arr = array(
    			//'parent'=>$_data['main_branch_id'],
    			'branch_namekh'=>$_data['branch_nameen'],
    			'prefix'      => $_data['prefix_code'],
    			'br_address'=>$_data['br_address'],
    			'branch_code'=>$_data['branch_code'],
    			'branch_tel'=>$phone_list,
    			'fax'=>$_data['fax'],
    			'other'=>$_data['branch_note'],
    			'status'=>$_data['branch_status'],
    			'displayby'=>2,
    			);
    	$where=$this->getAdapter()->quoteInto("br_id=?", $id);
    	$this->update($_arr, $where);
    }
    	
    function getAllBranch($search=null){
    	$db = $this->getAdapter();
    	$sql = "SELECT 
    				b.br_id,
    				b.branch_namekh,
    				b.prefix,
    				b.branch_code,
    				b.br_address,
    				b.branch_tel,
    				b.fax,
    				b.other,
    				b.`status` 
    			FROM 
    				rms_branch AS b  
    			WHERE  
    				b.branch_namekh !=''
    				and status=1
    		";
    	$where = '  ';
    	
    	if($search['status_search']>-1){
    		$where.= " AND b.status = ".$search['status_search'];
    	}
//     	(SELECT name_en FROM `rms_view` AS v WHERE v.`type` = 4 AND v.key_code = b.displayby)AS displayby,
    	if(!empty($search['adv_search'])){
    		$s_where=array();
    		$s_search=trim(addslashes($search['adv_search']));
    		$s_where[]=" b.prefix LIKE '%{$s_search}%'";
    		$s_where[]=" b.branch_namekh LIKE '%{$s_search}%'";
    		$s_where[]=" b.branch_nameen LIKE '%{$s_search}%'";
    		$s_where[]=" b.br_address LIKE '%{$s_search}%'";
    		$s_where[]=" b.branch_code LIKE '%{$s_search}%'";
    		$s_where[]=" b.branch_tel LIKE '%{$s_search}%'";
    		$s_where[]=" b.fax LIKE '%{$s_search}%'";
    		$s_where[]=" b.other LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order=' ORDER BY b.br_id DESC';
   		//echo $sql.$where;
  	 	return $db->fetchAll($sql.$where.$order);
    }
    
	function getBranchById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT 
    				br_id,
    				prefix,
    				branch_namekh,
    				branch_nameen,
    				br_address,
    				branch_code,
    				branch_tel,
    				fax,
    				displayby,
    				other,
    				status 
    			FROM
    				rms_branch
    			WHERE 
    				`br_id`= $id
    		";
   		return $db->fetchRow($sql);
    }
    public static function getBranchCode(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$sql = "SELECT COUNT(br_id) AS amount FROM `rms_branch`";
    	$acc_no= $db->getGlobalDbRow($sql);
    	$acc_no=$acc_no['amount'];
    	$new_acc_no= (int)$acc_no+1;
    	$acc_no= strlen((int)$acc_no+1);
    	$pre = "";
    	for($i = $acc_no;$i<3;$i++){
    		$pre.='0';
    	}
    	return "C-".$pre.$new_acc_no;
    }
	
	function addajaxs($_data){
		//return '0000';
    	$_arr = array(
				'branch_namekh'=>$_data['branch_nameen'],
				'branch_nameen'=>$_data['branch_nameen'],
				'fax'=>$_data['fax'], 
				'br_address'=>$_data['br_address'],
    			);
    	return $this->insert($_arr);//insert data
    }
}  
	  

