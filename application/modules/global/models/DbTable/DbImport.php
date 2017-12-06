<?php

class Global_Model_DbTable_DbImport extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_product';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    
    }
    
    function getExistStudentByStudentID($stu_code,$stu_khname){
    	$db = $this->getAdapter();
    	$sql="select stu_id from rms_student where stu_code = '$stu_code' and stu_khname='$stu_khname' ";
    	return $db->fetchOne($sql);
    }
    
    function getExistGradeByName($grade_name){
    	if(!empty($grade_name)){
	    	$db = $this->getAdapter();
	    	$sql="select major_id from rms_major where major_enname = '$grade_name' ";
	    	return $db->fetchOne($sql);
    	}else{
    		return -1;
    	}
    }
    
    function getExistRoomByName($room_name){
    	if(!empty($room_name)){
	    	$db = $this->getAdapter();
	    	$sql="select room_id from rms_room where room_name = '$room_name' ";
	    	return $db->fetchOne($sql);
    	}else{
    		return -1;
    	}
    }
    
    public function updateItemsByImport($data){
    	$db = $this->getAdapter();
    	$count = count($data);
    	//print_r($data);exit();
    	for($i=3; $i<=$count; $i++){
    		
    		if(empty($data[$i]['K']) && empty($data[$i]['M'])){
    			continue;
    		}
    		
	    	if($data[$i]['I']=="Mor"){
	    		$session = 1;
	    	}else{
	    		$session = 2;
	    	}	
    		
    /////////////////////////////////// exist grade //////////////////////////////////////		
    		$grade_id = $this->getExistGradeByName($data[$i]['F']);
    		if(empty($grade_id)){
    			$arr = array(
    					'major_enname'=>$data[$i]['F'],
    					'modify_date'=>date("Y-m-d"),
    					'is_active'=>1,
    					'user_id'=>1,
    			);
    			$this->_name='rms_major';
    			$grade_id = $this->insert($arr);
    		}
    /////////////////////////////////// exist room //////////////////////////////////////
    		$room_id = $this->getExistRoomByName($data[$i]['H']);
    		if(empty($room_id)){
    			$arr = array(
    					'room_name'=>$data[$i]['H'],
    					'modify_date'=>date("Y-m-d"),
    					'is_active'=>1,
    					'user_id'=>1,
    			);
    			$this->_name='rms_room';
    			$room_id = $this->insert($arr);
    		}
    /////////////////////////////////// exist student //////////////////////////////////////
    		$stu_id = $this->getExistStudentByStudentID($data[$i]['B'], $data[$i]['C']);
    		if(empty($stu_id)){
    			$this->_name='rms_student';
    			$arr = array(
	    				'branch_id'=>6,
	    				'user_id'=>1,
	    				'stu_code'=>$data[$i]['B'],
	    				'stu_khname'=>$data[$i]['C'],
	    				'stu_enname'=>$data[$i]['D'],
	    				'sex'=>($data[$i]['E']=="M")?1:2,
    					'degree'=>4,
	    				'grade'=>$grade_id,
	    				'room'=>$room_id,
	    				'session'=>$session,
	    				'tel'=>$data[$i]['AC'],
    					'create_date'=>date("Y-m-d",strtotime($data[$i]['K'])),
	    		);
	    		$stu_id = $this->insert($arr);
	    		
	    		$this->_name='rms_student_id';
	    		$arr1 = array(
	    				'branch_id'=>6,
	    				'stu_id'=>$stu_id,
	    				'stu_type'=>2,
	    				'degree'=>4,
	    				'status'=>1,
	    				'is_finish'=>0,
	    				'is_parent'=>null,
	    		);
	    		$this->insert($arr1);
    		}
    		
    		$qty=1;
    		if($data[$i]['R']==1){
    			$payment_term = 1;
    		}else if($data[$i]['R']==3){
    			$payment_term = 2;
    		}else if($data[$i]['R']==6){
    			$payment_term = 3;
    		}else if($data[$i]['R']==12 || $data[$i]['R']==13){
    			$payment_term = 4;
    		}else{
    			$payment_term = 5;
    			$qty = str_replace("day", "", $data[$i]['R']);
    		}
    		
    		
    	$this->_name = 'rms_student_payment';
    		$array = array(
	    				'branch_id'=>6,
		    			'student_id'=>$stu_id,
		    			'receipt_number'=>$data[$i]['L'],
    					'degree'=>4,
		    			'grade'=>$grade_id,
		    			'session'=>$session,
		    			'room_id'=>$room_id,
    				
	    				'payment_term'=>$payment_term,
    					'amount_sec'=>$qty,
    				
	    				'exchange_rate'=>4100,
	    				'tuition_fee'=>$data[$i]['M'],
	    				'discount_percent'=>$data[$i]['N'],
	    				'tuition_fee_after_discount'=>$data[$i]['O'],
	    				
	    				'material_fee'=>$data[$i]['S'],
	    				'admin_fee'=>$data[$i]['V'],
	    				
	    				'total_payment'=>$data[$i]['Z'],
	    				'receive_amount'=>$data[$i]['Z'],
	    				'paid_amount'=>$data[$i]['Z'],
	    				'balance_due'=>$data[$i]['Y'],
	    				
	    				'payfor_type'=>6,
	    				
	    				'grand_total_payment'=>$data[$i]['Z'],
	    				'grand_total_payment_in_riel'=>$data[$i]['Z'] * 4100,
	    				'grand_total_paid_amount'=>$data[$i]['Z'],
	    				'grand_total_balance'=>$data[$i]['Y'],
	    				
	    				'note'=>$data[$i]['AB'],
	    				'user_id'=>1,
	    				'create_date'=>date("Y-m-d",strtotime($data[$i]['K'])),
	    				'is_new'=>(empty($data[$i]['J']))?0:1,
	    				
	    				'student_type'=>3,
    				);
    		$payment_id = $this->insert($array);
    		
    		
    	$this->_name = 'rms_student_paymentdetail';
    		$array1 = array(
    				'payment_id'=>$payment_id,
    				'type'=>1,
    				'service_id'=>4,
    				'payment_term'=>$payment_term,
    				 
    				'fee'=>$data[$i]['M'],
    				'qty'=>$qty,
    				
    				'discount_percent'=>$data[$i]['N'],
    				 
    				'material_fee'=>$data[$i]['S'],
    				'admin_fee'=>$data[$i]['V'],
    				 
    				'subtotal'=>$data[$i]['Z'],
    				'paidamount'=>$data[$i]['Z'],
    				'balance'=>$data[$i]['Y'],
    				
    				'start_date'=>date("Y-m-d",strtotime($data[$i]['P'])),
    				'validate'=>date("Y-m-d",strtotime($data[$i]['Q'])),
    				 
    				'note'=>$data[$i]['AB'],
    				'user_id'=>1,
    				'is_complete'=>1,
    				'comment'=>'បង់រួច',
    				 
    				'is_start'=>0,
    		);
    		$this->insert($array1);
    	}
    }
}   

