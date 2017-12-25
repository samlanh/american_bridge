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

//////////////////////////////////////////////////////////// english //////////////////////////////////////////////////////////////////////    
    
    public function updateItemsByImportEFT($data){
    	$db = $this->getAdapter();
    	$count = count($data);
    	//print_r($data);exit();
    	
    	for($i=3; $i<=$count; $i++){
    		
    		if(empty($data[$i]['K']) && empty($data[$i]['M']) && empty($data[$i]['Z'])){
    			continue;
    		}
    		
	    	if($data[$i]['I']=="Mor"){
	    		$session = 1;
	    	}else{
	    		$session = 2;
	    	}	
    		
    /////////////////////////////////// exist grade //////////////////////////////////////	

	    	$current_grade_id = $this->getExistGradeByName($data[$i]['G']);
	    	if(empty($current_grade_id)){
	    		$arr = array(
	    				'major_enname'=>$data[$i]['G'],
	    				'modify_date'=>date("Y-m-d"),
	    				'is_active'=>1,
	    				'user_id'=>1,
	    		);
	    		$this->_name='rms_major';
	    		$current_grade_id = $this->insert($arr);
	    	}
	    	
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
    					'stu_type'=>2,// Eng FT
	    				'user_id'=>1,
	    				'stu_code'=>$data[$i]['B'],
	    				'stu_khname'=>$data[$i]['C'],
	    				'stu_enname'=>$data[$i]['D'],
	    				'sex'=>($data[$i]['E']=="M")?1:2,
    					'degree'=>4, // Eng FT
	    				'grade'=>$current_grade_id,
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
	    				'stu_type'=>2,// Eng FT
	    				'degree'=>4, // Eng FT
	    				'status'=>1,
	    				'is_finish'=>0,
	    				'is_parent'=>null,
	    		);
	    		$this->insert($arr1);
    		}
    		
    		$qty=1;
    		$qty_day=0;
    		$mystring = $data[$i]['R'];
    		$findme   = 'day';
    		$pos = strpos($mystring, $findme);
    		if(!empty($pos)){
    			$payment_term = 5;
    			$qty_day = str_replace("day", "", $data[$i]['R']);
    			$qty = $qty_day;
    		}else if($data[$i]['R']==3){
    			$payment_term = 2;//
    		}else if($data[$i]['R']==6){
    			$payment_term = 3;
    		}else if($data[$i]['R']==12){
    			$payment_term = 4;
    		}else{
    			$payment_term = 1;
    			$qty = $data[$i]['R'];
    		}
    		
    		
    	$this->_name = 'rms_student_payment';
    		$array = array(
	    				'branch_id'=>6,
		    			'student_id'=>$stu_id,
		    			'receipt_number'=>$data[$i]['L'],
    					'degree'=>4, // Eng FT
		    			'grade'=>$grade_id,
		    			'session'=>$session,
		    			'room_id'=>$room_id,
    				
	    				'payment_term'=>$payment_term,
    					'amount_sec'=>$qty_day,
    				
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
	    				
	    				'payfor_type'=>6, // Eng FT
	    				
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
    	
	    	$is_start = 0;
	    	if($i<$count){
	    		if($data[$i]['B'] != $data[$i+1]['B']){
	    			$is_start = 1;
	    		}
	    	}else{
	    		$is_start = 1;
	    	}
    	
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
    				 
    				'is_start'=>$is_start,
    		);
    		$this->insert($array1);
    	}
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function updateItemsByImportEPT($data){
    	$db = $this->getAdapter();
    	$count = count($data);
    	//print_r($data);exit();
    	 
    	//echo date("Y-m-d",strtotime($data[24]['K']));exit();
    	 
    	for($i=3; $i<=$count; $i++){
    		//echo "here ";exit();
    		if(empty($data[$i]['K']) && empty($data[$i]['M']) && empty($data[$i]['S'])){
    			continue;
    		}
    
    		$date = $data[$i]['K'];//exit();
    		$date = str_replace("/", "-", $date);
    		$create_date = date("Y-m-d",strtotime($date));
    		//echo $date;exit();
    
    		$start = $data[$i]['O'];//exit();
    		$start = str_replace("/", "-", $start);
    		$start_date = date("Y-m-d",strtotime($start));
    
    		$end = $data[$i]['P'];//exit();
    		$end = str_replace("/", "-", $end);
    		$end_date = date("Y-m-d",strtotime($end));
    
    
    
    		/////////////////////////////////// exist grade //////////////////////////////////////
    
    		$current_grade_id = $this->getExistGradeByName($data[$i]['G']);
    		if(empty($current_grade_id)){
    			$arr = array(
    					'major_enname'=>$data[$i]['G'],
    					'modify_date'=>date("Y-m-d"),
    					'is_active'=>1,
    					'user_id'=>1,
    			);
    			$this->_name='rms_major';
    			$current_grade_id = $this->insert($arr);
    		}
    
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
    					'stu_type'=>3, // Eng PT
    					'user_id'=>1,
    					'stu_code'=>$data[$i]['B'],
    					'stu_khname'=>$data[$i]['C'],
    					'stu_enname'=>$data[$i]['D'],
    					'sex'=>($data[$i]['E']=="M")?1:2,
    					'degree'=>6, // Eng PT
    					'grade'=>$current_grade_id,
    					'room'=>$room_id,
    					'session'=>3,// everning
    					'tel'=>$data[$i]['V'],
    					'create_date'=>$create_date,
    			);
    			$stu_id = $this->insert($arr);
    
    			$this->_name='rms_student_id';
    			$arr1 = array(
    					'branch_id'=>6,
    					'stu_id'=>$stu_id,
    					'stu_type'=>3,// Eng PT
    					'degree'=>6, // Eng PT
    					'status'=>1,
    					'is_finish'=>0,
    					'is_parent'=>null,
    			);
    			$this->insert($arr1);
    		}
    
    		$qty=1;
    		$qty_day=0;
    		$mystring = $data[$i]['Q'];
    		$findme   = 'day';
    		$pos = strpos($mystring, $findme);
    		if(!empty($pos)){
    			$payment_term = 5;
    			$qty_day = str_replace("day", "", $data[$i]['Q']);
    			$qty = $qty_day;
    		}else if($data[$i]['Q']==3){
    			$payment_term = 2;//
    		}else if($data[$i]['Q']==6){
    			$payment_term = 3;
    		}else if($data[$i]['Q']==12){
    			$payment_term = 4;
    		}else{
    			$payment_term = 1;
    			$qty = $data[$i]['Q'];
    		}
    
    
    		$this->_name = 'rms_student_payment';
    		$array = array(
    				'branch_id'=>6,
    				'student_id'=>$stu_id,
    				'receipt_number'=>$data[$i]['L'],
    				'degree'=>6, // Eng PT
    				'grade'=>$grade_id,
    				'session'=>3,
    				'time'=>$data[$i]['I'],
    				'room_id'=>$room_id,
    
    				'payment_term'=>$payment_term,
    				'amount_sec'=>$qty_day,
    
    				'exchange_rate'=>4100,
    				'tuition_fee'=>$data[$i]['M'],
    				'discount_percent'=>$data[$i]['N'],
    				'tuition_fee_after_discount'=>$data[$i]['M'] - ($data[$i]['M'] * $data[$i]['N'] / 100),
    					
    				'material_fee'=>0,
    				'admin_fee'=>0,
    					
    				'total_payment'=>$data[$i]['S'],
    				'receive_amount'=>$data[$i]['S'],
    				'paid_amount'=>$data[$i]['S'],
    				'balance_due'=>$data[$i]['R'],
    					
    				'payfor_type'=>2, // Eng PT
    					
    				'grand_total_payment'=>$data[$i]['S'],
    				'grand_total_payment_in_riel'=>$data[$i]['S'] * 4100,
    				'grand_total_paid_amount'=>$data[$i]['S'],
    				'grand_total_balance'=>$data[$i]['R'],
    					
    				'note'=>$data[$i]['U'],
    				'user_id'=>1,
    				'create_date'=>$create_date,
    				'is_new'=>(empty($data[$i]['J']))?0:1,
    					
    				'student_type'=>3,
    		);
    		$payment_id = $this->insert($array);
    
    
    		$this->_name = 'rms_student_paymentdetail';
    		
    		$is_start = 0;
    		if($i<$count){
    			if($data[$i]['B'] != $data[$i+1]['B']){
    				$is_start = 1;
    			}
    		}else{
    			$is_start = 1;
    		}
    		
    		$array1 = array(
    				'payment_id'=>$payment_id,
    				'type'=>2, // part time
    				'service_id'=>4,
    				'payment_term'=>$payment_term,
    					
    				'fee'=>$data[$i]['M'],
    				'qty'=>$qty,
    
    				'discount_percent'=>$data[$i]['N'],
    					
    				'material_fee'=>0,
    				'admin_fee'=>0,
    					
    				'subtotal'=>$data[$i]['S'],
    				'paidamount'=>$data[$i]['S'],
    				'balance'=>$data[$i]['R'],
    
    				'start_date'=>$start_date,
    				'validate'=>$end_date,
    					
    				'note'=>$data[$i]['U'],
    				'user_id'=>1,
    				'is_complete'=>1,
    				'comment'=>'បង់រួច',
    					
    				'is_start'=>$is_start,
    		);
    		$this->insert($array1);
    	}
    }
    
    
    
    
    
/////////////////////////////////////////////////////////// khmer //////////////////////////////////////////////////////////////////////////////////    
    
    public function updateItemsByImportKID($data){
    	$db = $this->getAdapter();
    	$count = count($data);
    	//print_r($data);exit();
    	 
    	//echo date("Y-m-d",strtotime($data[26]['K']));exit();
    	
//     	$date = $data[22]['K'];//exit();
//     	$date = str_replace("/", "-", $date);
//     	$create_date = date("Y-m-d",strtotime($date));
//     	echo $create_date;exit();
    	
    	
    	
    	for($i=3; $i<=$count; $i++){
    
//     		if(empty($data[$i]['K']) && empty($data[$i]['M']) && empty($data[$i]['Z'])){
//     			continue;
//     		}
    
    		$date = $data[$i]['K'];//exit();
    		$date = str_replace("/", "-", $date);
    		$create_date = date("Y-m-d",strtotime($date));
    		//echo $create_date;exit();
    		 
    		$start = $data[$i]['P'];//exit();
    		$start = str_replace("/", "-", $start);
    		$start_date = date("Y-m-d",strtotime($start));
    		
    		$end = $data[$i]['Q'];//exit();
    		$end = str_replace("/", "-", $end);
    		$end_date = date("Y-m-d",strtotime($end));
    		
    		if($data[$i]['I']=="Mor"){
    			$session = 1;
    		}else{
    			$session = 2;
    		}
    
    		/////////////////////////////////// exist grade //////////////////////////////////////
    
    		$current_grade_id = $this->getExistGradeByName($data[$i]['G']);
    		if(empty($current_grade_id)){
    			$arr = array(
    					'major_enname'=>$data[$i]['G'],
    					'dept_id'=>1,
    					'modify_date'=>date("Y-m-d"),
    					'is_active'=>1,
    					'user_id'=>1,
    			);
    			$this->_name='rms_major';
    			$current_grade_id = $this->insert($arr);
    		}
    
    		$grade_id = $this->getExistGradeByName($data[$i]['F']);
    		if(empty($grade_id)){
    			$arr = array(
    					'major_enname'=>$data[$i]['F'],
    					'dept_id'=>1,
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
    					'stu_type'=>1,// Khmer FT
    					'user_id'=>1,
    					'stu_code'=>$data[$i]['B'],
    					'stu_khname'=>$data[$i]['C'],
    					'stu_enname'=>$data[$i]['D'],
    					'sex'=>($data[$i]['E']=="M")?1:2,
    					'degree'=>1,
    					'grade'=>$current_grade_id,
    					'room'=>$room_id,
    					'session'=>$session,
    					'tel'=>$data[$i]['AC'],
    					
    					'is_subspend'=>(empty($data[$i]['AE']))?0:2,
    					
    					'create_date'=>$create_date,
    			);
    			$stu_id = $this->insert($arr);
    	   
    			$this->_name='rms_student_id';
    			$arr1 = array(
    					'branch_id'=>6,
    					'stu_id'=>$stu_id,
    					'stu_type'=>1,// Khmer FT
    					'degree'=>1,
    					'status'=>1,
    					'is_finish'=>0,
    					'is_parent'=>null,
    			);
    			$this->insert($arr1);
    		}
    		
    		$this->_name='rms_student_drop';
    		if(!empty($data[$i]['AE'])){
    			$arr_drop = array(
    					'type'=>2,
    					'stu_id'=>$stu_id,
    					'user_id'=>1,
    					'reason'=>$data[$i]['AD'],
    					);
    			$this->insert($arr_drop);
    		}
    		
//     		$this->_name='rms_student';
//     		if($i!=$count && !empty($data[$i]['AE'])){	
// 	    		if($data[$i]['B'] != $data[$i+1]['B']){
// 	    			$arra = array(
// 	    					'is_subspend'=>2,
// 	    					);
// 	    			$where = " stu_id = $stu_id ";
// 	    			$this->update($arra, $where);
// 	    		}else{
// 	    			$arra = array(
// 	    					'is_comeback'=>1,
// 	    			);
// 	    			$where = " stu_id = $stu_id ";
// 	    			$this->update($arra, $where);
// 	    		}
//     		}
    
    		
    		if(empty($data[$i]['AE'])){
	    		$qty=1;
	    		$qty_day=0;
	    		$mystring = $data[$i]['R'];
	    		$findme   = 'day';
	    		$pos = strpos($mystring, $findme);
	    		if(!empty($pos)){
	    			$payment_term = 5;
	    			$qty_day = str_replace("day", "", $data[$i]['R']);
	    			$qty = $qty_day;
	    		}else if($data[$i]['R']==3){
	    			$payment_term = 2;//
	    		}else if($data[$i]['R']==6){
	    			$payment_term = 3;
	    		}else if($data[$i]['R']==12){
	    			$payment_term = 4;
	    		}else{
	    			$payment_term = 1;
	    			$qty = $data[$i]['R'];
	    		}
	    
	    		$this->_name = 'rms_student_payment';
	    		$_arr = array(
	    				'branch_id'=>6,
	    				'student_id'=>$stu_id,
	    				'receipt_number'=>$data[$i]['L'],
	    				'degree'=>1,
	    				'grade'=>$grade_id,
	    				'session'=>$session,
	    				'room_id'=>$room_id,
	    
	    				'payment_term'=>$payment_term,
	    				'amount_sec'=>$qty_day,
	    
	    				'exchange_rate'=>4100,
	    				'tuition_fee'=>$data[$i]['M'],
	    				'discount_percent'=>$data[$i]['N'],
	    				'tuition_fee_after_discount'=>$data[$i]['O'],
	    	    
	    				'material_fee'=>$data[$i]['V'],
	    				'admin_fee'=>$data[$i]['S'],
	    	    
	    				'total_payment'=>$data[$i]['Z'],
	    				'receive_amount'=>$data[$i]['Z'],
	    				'paid_amount'=>$data[$i]['Z'],
	    				'balance_due'=>$data[$i]['Y'],
	    	    
	    				'payfor_type'=>1,
	    	    
	    				'grand_total_payment'=>$data[$i]['Z'],
	    				'grand_total_payment_in_riel'=>$data[$i]['Z'] * 4100,
	    				'grand_total_paid_amount'=>$data[$i]['Z'],
	    				'grand_total_balance'=>$data[$i]['Y'],
	    	    
	    				'note'=>$data[$i]['AB'],
	    				'user_id'=>1,
	    				'create_date'=>$create_date,
	    				'is_new'=>(empty($data[$i]['J']))?0:1,
	    				
	    				'is_subspend'=>(empty($data[$i]['AE']))?0:2,
	    	    
	    				'student_type'=>3,
	    		);
	    		$paymentid = $this->insert($_arr);
	    
	    		
	    		$is_start = 1;
// 	    		if($i<$count){
// 		    		if($data[$i]['B'] != $data[$i+1]['B']){
// 		    			$is_start = 1;
// 		    		}
// 	    		}else{
// 	    			$is_start = 1;
// 	    		}
	    		
	    		
	    		$_arr2 = array(
	    				'payment_id'=>$paymentid,
	    				'type'=>1,
	    				'service_id'=>4,
	    				'payment_term'=>$payment_term,
	    					
	    				'fee'=>$data[$i]['M'],
	    				'qty'=>$qty,
	    
	    				'discount_percent'=>$data[$i]['N'],
	    					
	    				'material_fee'=>$data[$i]['V'],
	    				'admin_fee'=>$data[$i]['S'],
	    					
	    				'subtotal'=>$data[$i]['Z'],
	    				'paidamount'=>$data[$i]['Z'],
	    				'balance'=>$data[$i]['Y'],
	    
	    				'start_date'=>$start_date,
	    				'validate'=>$end_date,
	    					
	    				'note'=>$data[$i]['AB'],
	    				'user_id'=>1,
	    				'is_complete'=>1,
	    				'comment'=>'បង់រួច',
	    					
	    				'is_start'=>$is_start,
	    		);
	    		$this->_name ='rms_student_paymentdetail';
	    		$this->insert($_arr2);
	    		//exit();
    		}
    	}
    }
    
    
    
    public function updateItemsByImportPrimary($data){
    	$db = $this->getAdapter();
    	$count = count($data);
    	//print_r($data);exit();
    
//     	echo date("Y-m-d",strtotime($data[4]['K']));exit();
    	 
// 		$date = $data[4]['K'];//exit();
// 		$date = str_replace("/", "-", $date);
// 		$create_date = date("Y-m-d",strtotime($date));
// 		echo $create_date;exit();    	    	
    	 
    	 
    	for($i=3; $i<=$count; $i++){
    
    		if(empty($data[$i]['K']) && empty($data[$i]['M']) && empty($data[$i]['W'])){
    			continue;
    		}
    
    		$date = $data[$i]['K'];//exit();
    		$date = str_replace("/", "-", $date);
    		$create_date = date("Y-m-d",strtotime($date));
    		//echo $create_date;exit();
    		 
    		$start = $data[$i]['P'];//exit();
    		$start = str_replace("/", "-", $start);
    		$start_date = date("Y-m-d",strtotime($start));
    
    		$end = $data[$i]['Q'];//exit();
    		$end = str_replace("/", "-", $end);
    		$end_date = date("Y-m-d",strtotime($end));
    
    		if($data[$i]['I']=="Mor"){
    			$session = 1;
    		}else{
    			$session = 2;
    		}
    
    		/////////////////////////////////// exist grade //////////////////////////////////////
    
    		$current_grade_id = $this->getExistGradeByName($data[$i]['G']);
    		if(empty($current_grade_id)){
    			$arr = array(
    					'major_enname'=>$data[$i]['G'],
    					'modify_date'=>date("Y-m-d"),
    					'is_active'=>1,
    					'user_id'=>1,
    			);
    			$this->_name='rms_major';
    			$current_grade_id = $this->insert($arr);
    		}
    
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
    					'stu_type'=>1,// Khmer FT
    					'user_id'=>1,
    					'stu_code'=>$data[$i]['B'],
    					'stu_khname'=>$data[$i]['C'],
    					'stu_enname'=>$data[$i]['D'],
    					'sex'=>($data[$i]['E']=="M")?1:2,
    					'degree'=>2,//primary
    					'grade'=>$current_grade_id,
    					'room'=>$room_id,
    					'session'=>$session,
    					'tel'=>$data[$i]['Z'],
    					'create_date'=>$create_date,
    			);
    			$stu_id = $this->insert($arr);
    
    			$this->_name='rms_student_id';
    			$arr1 = array(
    					'branch_id'=>6,
    					'stu_id'=>$stu_id,
    					'stu_type'=>1,// Khmer FT
    					'degree'=>2,//primary
    					'status'=>1,
    					'is_finish'=>0,
    					'is_parent'=>null,
    			);
    			$this->insert($arr1);
    		}
    
    		$this->_name='rms_student_drop';
    		if(!empty($data[$i]['AB'])){
    			$arr_drop = array(
    					'type'=>2,
    					'stu_id'=>$stu_id,
    					'user_id'=>1,
    					'reason'=>$data[$i]['AA'],
    			);
    			$this->insert($arr_drop);
    		}
    
    		$this->_name='rms_student';
    		if($i!=$count && !empty($data[$i]['AB'])){
    			if($data[$i]['B'] != $data[$i+1]['B']){
    				$arra = array(
    						'is_subspend'=>2,
    				);
    				$where = " stu_id = $stu_id ";
    				$this->update($arra, $where);
    			}else{
    				$arra = array(
    						'is_comeback'=>1,
    				);
    				$where = " stu_id = $stu_id ";
    				$this->update($arra, $where);
    			}
    		}
    
    
    
    		$qty=1;
    		$qty_day=0;
    		$mystring = $data[$i]['R'];
    		$findme   = 'day';
    		$pos = strpos($mystring, $findme);
    		if(!empty($pos)){
    			$payment_term = 5;
    			$qty_day = str_replace("day", "", $data[$i]['R']);
    			$qty = $qty_day;
    		}else if($data[$i]['R']==3){
    			$payment_term = 2;//
    		}else if($data[$i]['R']==6){
    			$payment_term = 3;
    		}else if($data[$i]['R']==12){
    			$payment_term = 4;
    		}else{
    			$payment_term = 1;
    			$qty = $data[$i]['R'];
    		}
    
    
    		$this->_name = 'rms_student_payment';
    		$array = array(
    				'branch_id'=>6,
    				'student_id'=>$stu_id,
    				'receipt_number'=>$data[$i]['L'],
    				'degree'=>2,//primary
    				'grade'=>$grade_id,
    				'session'=>$session,
    				'room_id'=>$room_id,
    
    				'payment_term'=>$payment_term,
    				'amount_sec'=>$qty_day,
    
    				'exchange_rate'=>4100,
    				'tuition_fee'=>$data[$i]['M'],
    				'discount_percent'=>$data[$i]['N'],
    				'tuition_fee_after_discount'=>$data[$i]['O'],
    					
    				'material_fee'=>0,
    				'admin_fee'=>$data[$i]['S'],
    					
    				'total_payment'=>$data[$i]['W'],
    				'receive_amount'=>$data[$i]['W'],
    				'paid_amount'=>$data[$i]['W'],
    				'balance_due'=>$data[$i]['V'],
    					
    				'payfor_type'=>1,//Khmer FT
    					
    				'grand_total_payment'=>$data[$i]['W'],
    				'grand_total_payment_in_riel'=>$data[$i]['W'] * 4100,
    				'grand_total_paid_amount'=>$data[$i]['W'],
    				'grand_total_balance'=>$data[$i]['V'],
    					
    				'note'=>$data[$i]['Y'],
    				'user_id'=>1,
    				'create_date'=>$create_date,
    				'is_new'=>(empty($data[$i]['J']))?0:1,
    
    				'is_subspend'=>(empty($data[$i]['AB']))?0:2,
    					
    				'student_type'=>3,
    		);
    		$payment_id = $this->insert($array);
    
    
    		$this->_name = 'rms_student_paymentdetail';
    
    		$is_start = 0;
    		if($i<$count){
    			if($data[$i]['B'] != $data[$i+1]['B']){
    				$is_start = 1;
    			}
    		}else{
    			$is_start = 1;
    		}
    
    
    		$array1 = array(
    				'payment_id'=>$payment_id,
    				'type'=>1,
    				'service_id'=>4,
    				'payment_term'=>$payment_term,
    					
    				'fee'=>$data[$i]['M'],
    				'qty'=>$qty,
    
    				'discount_percent'=>$data[$i]['N'],
    					
    				'material_fee'=>0,
    				'admin_fee'=>$data[$i]['S'],
    					
    				'subtotal'=>$data[$i]['W'],
    				'paidamount'=>$data[$i]['W'],
    				'balance'=>$data[$i]['V'],
    
    				'start_date'=>$start_date,
    				'validate'=>$end_date,
    					
    				'note'=>$data[$i]['Y'],
    				'user_id'=>1,
    				'is_complete'=>1,
    				'comment'=>'បង់រួច',
    					
    				'is_start'=>$is_start,
    		);
    		$this->insert($array1);
    	}
    }
    
    
    
    public function updateItemsByImportHigh($data){
    	$db = $this->getAdapter();
    	$count = count($data);
    	//print_r($data);exit();
    
    	//     	echo date("Y-m-d",strtotime($data[4]['K']));exit();
    
    	// 		$date = $data[4]['K'];//exit();
    	// 		$date = str_replace("/", "-", $date);
    	// 		$create_date = date("Y-m-d",strtotime($date));
    	// 		echo $create_date;exit();
    
    
    	for($i=3; $i<=$count; $i++){
    
    		if(empty($data[$i]['K']) && empty($data[$i]['M']) && empty($data[$i]['W'])){
    			continue;
    		}
    
    		$date = $data[$i]['K'];//exit();
    		$date = str_replace("/", "-", $date);
    		$create_date = date("Y-m-d",strtotime($date));
    		//echo $create_date;exit();
    		 
    		$start = $data[$i]['P'];//exit();
    		$start = str_replace("/", "-", $start);
    		$start_date = date("Y-m-d",strtotime($start));
    
    		$end = $data[$i]['Q'];//exit();
    		$end = str_replace("/", "-", $end);
    		$end_date = date("Y-m-d",strtotime($end));
    
    		if($data[$i]['I']=="Mor"){
    			$session = 1;
    		}else{
    			$session = 2;
    		}
    
    		/////////////////////////////////// exist grade //////////////////////////////////////
    
    		$current_grade_id = $this->getExistGradeByName($data[$i]['G']);
    		if(empty($current_grade_id)){
    			$arr = array(
    					'major_enname'=>$data[$i]['G'],
    					'modify_date'=>date("Y-m-d"),
    					'is_active'=>1,
    					'user_id'=>1,
    			);
    			$this->_name='rms_major';
    			$current_grade_id = $this->insert($arr);
    		}
    
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
    					'stu_type'=>1,// Khmer FT
    					'user_id'=>1,
    					'stu_code'=>$data[$i]['B'],
    					'stu_khname'=>$data[$i]['C'],
    					'stu_enname'=>$data[$i]['D'],
    					'sex'=>($data[$i]['E']=="M")?1:2,
    					'degree'=>3, //high school
    					'grade'=>$current_grade_id,
    					'room'=>$room_id,
    					'session'=>$session,
    					'tel'=>$data[$i]['Z'],
    					'create_date'=>$create_date,
    			);
    			$stu_id = $this->insert($arr);
    
    			$this->_name='rms_student_id';
    			$arr1 = array(
    					'branch_id'=>6,
    					'stu_id'=>$stu_id,
    					'stu_type'=>1,// Khmer FT
    					'degree'=>3, //high school
    					'status'=>1,
    					'is_finish'=>0,
    					'is_parent'=>null,
    			);
    			$this->insert($arr1);
    		}
    
    		$this->_name='rms_student_drop';
    		if(!empty($data[$i]['AB'])){
    			$arr_drop = array(
    					'type'=>2,
    					'stu_id'=>$stu_id,
    					'user_id'=>1,
    					'reason'=>$data[$i]['AA'],
    			);
    			$this->insert($arr_drop);
    		}
    
    		$this->_name='rms_student';
    		if($i!=$count && !empty($data[$i]['AB'])){
    			if($data[$i]['B'] != $data[$i+1]['B']){
    				$arra = array(
    						'is_subspend'=>2,
    				);
    				$where = " stu_id = $stu_id ";
    				$this->update($arra, $where);
    			}else{
    				$arra = array(
    						'is_comeback'=>1,
    				);
    				$where = " stu_id = $stu_id ";
    				$this->update($arra, $where);
    			}
    		}
    
    
    
    		$qty=1;
    		$qty_day=0;
    		$mystring = $data[$i]['R'];
    		$findme   = 'day';
    		$pos = strpos($mystring, $findme);
    		if(!empty($pos)){
    			$payment_term = 5;
    			$qty_day = str_replace("day", "", $data[$i]['R']);
    			$qty = $qty_day;
    		}else if($data[$i]['R']==3){
    			$payment_term = 2;//
    		}else if($data[$i]['R']==6){
    			$payment_term = 3;
    		}else if($data[$i]['R']==12){
    			$payment_term = 4;
    		}else{
    			$payment_term = 1;
    			$qty = $data[$i]['R'];
    		}
    
    
    		$this->_name = 'rms_student_payment';
    		$array = array(
    				'branch_id'=>6,
    				'student_id'=>$stu_id,
    				'receipt_number'=>$data[$i]['L'],
    				'degree'=>3, //high school
    				'grade'=>$grade_id,
    				'session'=>$session,
    				'room_id'=>$room_id,
    
    				'payment_term'=>$payment_term,
    				'amount_sec'=>$qty_day,
    
    				'exchange_rate'=>4100,
    				'tuition_fee'=>$data[$i]['M'],
    				'discount_percent'=>$data[$i]['N'],
    				'tuition_fee_after_discount'=>$data[$i]['O'],
    					
    				'material_fee'=>0,
    				'admin_fee'=>$data[$i]['S'],
    					
    				'total_payment'=>$data[$i]['W'],
    				'receive_amount'=>$data[$i]['W'],
    				'paid_amount'=>$data[$i]['W'],
    				'balance_due'=>$data[$i]['V'],
    					
    				'payfor_type'=>1,
    					
    				'grand_total_payment'=>$data[$i]['W'],
    				'grand_total_payment_in_riel'=>$data[$i]['W'] * 4100,
    				'grand_total_paid_amount'=>$data[$i]['W'],
    				'grand_total_balance'=>$data[$i]['V'],
    					
    				'note'=>$data[$i]['Y'],
    				'user_id'=>1,
    				'create_date'=>$create_date,
    				'is_new'=>(empty($data[$i]['J']))?0:1,
    
    				'is_subspend'=>(empty($data[$i]['AB']))?0:2,
    					
    				'student_type'=>3,
    		);
    		$payment_id = $this->insert($array);
    
    
    		$this->_name = 'rms_student_paymentdetail';
    
    		$is_start = 0;
    		if($i<$count){
    			if($data[$i]['B'] != $data[$i+1]['B']){
    				$is_start = 1;
    			}
    		}else{
    			$is_start = 1;
    		}
    
    
    		$array1 = array(
    				'payment_id'=>$payment_id,
    				'type'=>1,
    				'service_id'=>4,
    				'payment_term'=>$payment_term,
    					
    				'fee'=>$data[$i]['M'],
    				'qty'=>$qty,
    
    				'discount_percent'=>$data[$i]['N'],
    					
    				'material_fee'=>0,
    				'admin_fee'=>$data[$i]['S'],
    					
    				'subtotal'=>$data[$i]['W'],
    				'paidamount'=>$data[$i]['W'],
    				'balance'=>$data[$i]['V'],
    
    				'start_date'=>$start_date,
    				'validate'=>$end_date,
    					
    				'note'=>$data[$i]['Y'],
    				'user_id'=>1,
    				'is_complete'=>1,
    				'comment'=>'បង់រួច',
    					
    				'is_start'=>$is_start,
    		);
    		$this->insert($array1);
    	}
    }
    
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
   

    
    function getExistStudentInfoByStudentName($stu_khname){
    	$db=$this->getAdapter();
    	$sql="select stu_id from rms_student where stu_khname='$stu_khname' limit 1";
    	return $db->fetchOne($sql);
    }
    
    function insertFirstRecord($stu_id,$service_id){
    	$db=$this->getAdapter();
    	$sql="select id from rms_service where stu_id = $stu_id and service_id = $service_id limit 1";
    	return $db->fetchOne($sql);
    }
    
    function getExistCarByCarID($car_id){
    	$db=$this->getAdapter();
    	$sql="select id from rms_car where carid = '$car_id' limit 1";
    	return $db->fetchOne($sql);
    }
    
    function getExistServiceNameByCarID($car_id){
    	$db=$this->getAdapter();
    	$sql="select service_id as id from rms_program_name where title = '$car_id' limit 1";
    	return $db->fetchOne($sql);
    }
    
/////////////////////////////////////////////////////////// Lunch Service //////////////////////////////////////////////////////////////////////////////////
    
    public function updateItemsByImportCar($data){
    	$db = $this->getAdapter();
    	$count = count($data);
    	//print_r($data);exit();
    	
    	//echo date("Y-m-d",strtotime($data[4]['L']));exit();
    	 
//     	    	$date = $data[5]['L'];//exit();
//     	    	$date = str_replace("/", "-", $date);
//     	    	$create_date = date("Y-m-d",strtotime($date));
//     	    	echo "creatd".$create_date;exit();

    	//echo "checked = ".$data[3]['H'];exit();
    	
    	
    	for($i=3; $i<=$count; $i++){
    
    		$date = $data[$i]['L'];//exit();
    		$date = str_replace("/", "-", $date);
    		$create_date = date("Y-m-d",strtotime($date));
    		//echo $create_date;exit();
    		 
    		$start = $data[$i]['P'];//exit();
    		$start = str_replace("/", "-", $start);
    		$start_date = date("Y-m-d",strtotime($start));
    
    		$end = $data[$i]['Q'];//exit();
    		$end = str_replace("/", "-", $end);
    		$end_date = date("Y-m-d",strtotime($end));
    
    		if($data[$i]['G']=='P' || $data[$i]['G']=='ü'){
    			$time1 = 1;
    			$time1 = $time1.",";
    		}else{
    			$time1=null;
    		}
    		
    		if($data[$i]['H']=='P' || $data[$i]['H']=='ü'){
    			$time2 = 2;
    			$time2 = $time2.",";
    		}else{
    			$time2=null;
    		}
    		
    		if($data[$i]['I']=='P' || $data[$i]['I']=='ü'){
    			$time3 = 3;
    			$time3 = $time3.",";
    		}else{
    			$time3=null;
    		}
    		
    		if($data[$i]['J']=='P' || $data[$i]['J']=='ü'){
    			$time4 = 4;
    		}else{
    			$time4=null;
    		}
    		$arr_time = $time1.$time2.$time3.$time4;
    		

    	//////////////////////////////////// insert into rms_car ////////////////////////////////////////////////////////////////	
    		
    		$car_id = $this->getExistCarByCarID($data[$i]['F']);
    		if(empty($car_id)){
    			$this->_name="rms_car";
    			$arr = array(
    					'carid'=>$data[$i]['F'],
    					'userid'=>1,
    					);
    			$car_id = $this->insert($arr);
    		}
    		
    	//////////////////////////////////// insert into rms_program_name ////////////////////////////////////////////////////////////////	
    		
    		$service_id = $this->getExistServiceNameByCarID($data[$i]['F']);
    		if(empty($service_id)){
    			$this->_name="rms_program_name";
    			$arr = array(
    					'ser_cate_id'=>4,
    					'title'=>$data[$i]['F'],
    					'car_id'=>$car_id,
    					'status'=>1,
    					'user_id'=>1,
    					'type'=>2,
    			);
    			$service_id = $this->insert($arr);
    		}
    		
    
    		/////////////////////////////////// exist student //////////////////////////////////////
    		$stu_id = $this->getExistStudentInfoByStudentName($data[$i]['C']);
    
    		if(!empty($stu_id)){
    			$result = $this->insertFirstRecord($stu_id, $service_id);
	    		if(empty($result)){
	    			$this->_name='rms_service';
	    			$arr = array(
	    					'branch_id'=>6,
	    					'type'=>4,// Transport
	    					'stu_id'=>$stu_id,
	    					'stu_code'=>$data[$i]['B'],
	    					'service_id'=>$service_id ,//car service
	    					'create_date'=>$create_date,
	    					'is_new'=>0,
	    			);
	    			$this->insert($arr);
    			}
    		}
    
    
    
    
    		$qty=1;
    		$qty_day=0;
    		$mystring = $data[$i]['R'];
    		$findme   = 'day';
    		$pos = strpos($mystring, $findme);
    		if(!empty($pos)){
    			$payment_term = 5;
    			$qty_day = str_replace("day", "", $data[$i]['R']);
    			$qty = $qty_day;
    		}else if($data[$i]['R']==3){
    			$payment_term = 2;//
    		}else if($data[$i]['R']==6){
    			$payment_term = 3;
    		}else if($data[$i]['R']==12){
    			$payment_term = 4;
    		}else{
    			$payment_term = 1;
    			$qty = $data[$i]['R'];
    		}
    
    
    		$this->_name = 'rms_student_payment';
    		$array = array(
    				'branch_id'=>6,
    				'student_id'=>$stu_id,
    				'receipt_number'=>$data[$i]['M'],
    
    				'payment_term'=>$payment_term,
    				'amount_sec'=>$qty_day,
    
    				'exchange_rate'=>4100,
    				'tuition_fee'=>$data[$i]['N'],
    				'discount_percent'=>0,
    				'tuition_fee_after_discount'=>$data[$i]['N'],
    					
    				'material_fee'=>0,
    				'admin_fee'=>0,
    					
    				'total_payment'=>$data[$i]['T'],
    				'receive_amount'=>$data[$i]['T'],
    				'paid_amount'=>$data[$i]['T'],
    				'balance_due'=>$data[$i]['S'],
    					
    				'payfor_type'=>3, // transport
    				'time_for_car'=>$arr_time,
    					
    				'grand_total_payment'=>$data[$i]['T'],
    				'grand_total_payment_in_riel'=>$data[$i]['T'] * 4100,
    				'grand_total_paid_amount'=>$data[$i]['T'],
    				'grand_total_balance'=>$data[$i]['S'],
    					
    				'note'=>$data[$i]['V'],
    				'user_id'=>1,
    				'create_date'=>$create_date,
    				'is_new'=>(empty($data[$i]['K']))?0:1,
    
    				'student_type'=>3,
    		);
    		$payment_id = $this->insert($array);
    
    
    		$this->_name = 'rms_student_paymentdetail';
    
    		$is_start = 0;
    		if($i<$count){
    			if($data[$i]['B'] != $data[$i+1]['B']){
    				$is_start = 1;
    			}
    		}else{
    			$is_start = 1;
    		}
    
    
    		$array1 = array(
    				'payment_id'=>$payment_id,
    				'type'=>3, // car service
    				'service_id'=>$service_id,
    				'payment_term'=>$payment_term,
    					
    				'fee'=>$data[$i]['N'],
    				'qty'=>$qty,
    
    				'discount_percent'=>0,
    					
    				'material_fee'=>0,
    				'admin_fee'=>0,
    					
    				'subtotal'=>$data[$i]['T'],
    				'paidamount'=>$data[$i]['T'],
    				'balance'=>$data[$i]['S'],
    
    				'start_date'=>$start_date,
    				'validate'=>$end_date,
    					
    				'note'=>$data[$i]['V'],
    				'user_id'=>1,
    				'is_complete'=>1,
    				'comment'=>'បង់រួច',
    					
    				'is_start'=>$is_start,
    		);
    		$this->insert($array1);
    	}
    }    
    
    
    public function updateItemsByImportFood($data){
    	$db = $this->getAdapter();
    	$count = count($data);
    	//print_r($data);exit();
    
//     	echo date("Y-m-d",strtotime($data[3]['J']));exit();
    
//     	$date = $data[3]['J'];//exit();
//     	$date = str_replace("/", "-", $date);
//     	$create_date = date("Y-m-d",strtotime($date));
//     	echo $create_date;exit();
    	 
//     	for($i=3; $i<=$count; $i++){
//     		$stu_id = $this->getExistStudentInfoByStudentName($data[$i]['C']);
//     		if(!empty($stu_id)){
//     			echo "have <br/>";
//     		}else{
//     			echo "null = ".$data[$i]['B']."<br/>";
//     		}
//     	}
//     	exit();
    	
    	for($i=3; $i<=$count; $i++){
    
    	
	    	$date = $data[$i]['J'];//exit();
	    	$date = str_replace("/", "-", $date);
	    	$create_date = date("Y-m-d",strtotime($date));
	    	//echo $create_date;exit();
	    	 
	    	$start = $data[$i]['N'];//exit();
	    	$start = str_replace("/", "-", $start);
	    	$start_date = date("Y-m-d",strtotime($start));
	    
	    	$end = $data[$i]['O'];//exit();
	    	$end = str_replace("/", "-", $end);
	    	$end_date = date("Y-m-d",strtotime($end));
    
    
    	/////////////////////////////////// exist student //////////////////////////////////////
    	$stu_id = $this->getExistStudentInfoByStudentName($data[$i]['C']);
    
    	$service_name = $data[$i]['E'];
    	if($service_name == 'ស្នាក់នៅ'){
    		$service_id = 92;
    	}else{
    		$service_id = 93;
    	}
    	
	    if(!empty($stu_id)){
	    	$result = $this->insertFirstRecord($stu_id,$service_id);
	    	if(empty($result)){
		    	$this->_name='rms_service';
		    	$arr = array(
		    			'branch_id'	=>6,
		    			'type'		=>5,// Food
		    			'stu_id'	=>$stu_id,
		    			'stu_code'	=>$data[$i]['B'],
		    			'service_id'=>$service_id,
		    			'status'	=>1,
		    			'create_date'=>$create_date,
		    			'is_new'	=>0,
		    	);
		    	$this->insert($arr);
	    	}
	    }
    
    
    	$qty=1;
    	$qty_day=0;
    	$mystring = $data[$i]['P'];
    	$findme   = 'day';
    	$pos = strpos($mystring, $findme);
    	if(!empty($pos)){
	    	$payment_term = 5;
	    	$qty_day = str_replace("day", "", $data[$i]['P']);
	    	$qty = $qty_day;
    	}else if($data[$i]['P']==3){
    		$payment_term = 2;//
    	}else if($data[$i]['P']==6){
    		$payment_term = 3;
    	}else if($data[$i]['P']==12){
    		$payment_term = 4;
    	}else{
	    	$payment_term = 1;
	    	$qty = $data[$i]['P'];
    	}
    
    
    	$this->_name = 'rms_student_payment';
    	$array = array(
    			'branch_id'=>6,
    			'student_id'=>$stu_id,
    			'receipt_number'=>$data[$i]['K'],
    
    			'payment_term'=>$payment_term,
    			'amount_sec'=>$qty_day,
    
    			'exchange_rate'=>4100,
    			'tuition_fee'=>$data[$i]['L'],
    			'discount_percent'=>0,
    			'tuition_fee_after_discount'=>$data[$i]['L'],
    				
    			'material_fee'=>0,
    			'admin_fee'=>0,
    				
    			'total_payment'=>$data[$i]['R'],
    			'receive_amount'=>$data[$i]['R'],
    			'paid_amount'=>$data[$i]['R'],
    			'balance_due'=>$data[$i]['Q'],
    				
    			'payfor_type'=>4,//food
    				
    			'grand_total_payment'=>$data[$i]['R'],
    			'grand_total_payment_in_riel'=>$data[$i]['R'] * 4100,
    			'grand_total_paid_amount'=>$data[$i]['R'],
    			'grand_total_balance'=>$data[$i]['Q'],
    				
    			'note'=>$data[$i]['T'],
    			'user_id'=>1,
    			'create_date'=>$create_date,
    			'is_new'=>(empty($data[$i]['I']))?0:1,
    				
    			'student_type'=>3,
    	);
    	$payment_id = $this->insert($array);
    
    
    	$this->_name = 'rms_student_paymentdetail';
    
    	$is_start = 0;
    	if($i<$count){
	    	if($data[$i]['B'] != $data[$i+1]['B']){
	    		$is_start = 1;
	    	}
    	}else{
    		$is_start = 1;
    	}
    
    
    	$array1 = array(
    			'payment_id'=>$payment_id,
    			'type'=>5, // lunch
    			'service_id'=>$service_id,
    			'payment_term'=>$payment_term,
    				
    			'fee'=>$data[$i]['L'],
    			'qty'=>$qty,
    
    			'discount_percent'=>0,
    				
    			'material_fee'=>0,
    			'admin_fee'=>0,
    				
    			'subtotal'=>$data[$i]['R'],
    			'paidamount'=>$data[$i]['R'],
    			'balance'=>$data[$i]['Q'],
    
    			'start_date'=>$start_date,
    			'validate'=>$end_date,
    				
    			'note'=>$data[$i]['T'],
    			'user_id'=>1,
    			'is_complete'=>1,
    			'comment'=>'បង់រួច',
    				
    			'is_start'=>$is_start,
    	);
    	$this->insert($array1);
    	}
    }
    
    
////////////////////////////////////////////////////////// Rental ///////////////////////////////////////////////////////////////////    
    
    function getExistCustomerByName($name){
    	$db=$this->getAdapter();
    	$sql="select id from rms_customer where last_name='$name' limit 1";
    	return $db->fetchOne($sql);
    }
    
    
    
    public function updateItemsByImportRental($data){
    	$db = $this->getAdapter();
    	$count = count($data);
    	//print_r($data);exit();
    
    	//     	echo date("Y-m-d",strtotime($data[3]['J']));exit();
    
    	//     	$date = $data[3]['J'];//exit();
    	//     	$date = str_replace("/", "-", $date);
    	//     	$create_date = date("Y-m-d",strtotime($date));
    	//     	echo $create_date;exit();
    
    	//     	for($i=3; $i<=$count; $i++){
    	//     		$stu_id = $this->getExistStudentInfoByStudentName($data[$i]['C']);
    	//     		if(!empty($stu_id)){
    	//     			echo "have <br/>";
    	//     		}else{
    	//     			echo "null = ".$data[$i]['B']."<br/>";
    	//     		}
    	//     	}
    	//     	exit();
    	 
    	for($i=3; $i<=$count; $i++){
    
    		 
    		$date = $data[$i]['J'];//exit();
    		$date = str_replace("/", "-", $date);
    		$create_date = date("Y-m-d",strtotime($date));
    		//echo $create_date;exit();
    		 
    		$start = $data[$i]['N'];//exit();
    		$start = str_replace("/", "-", $start);
    		$start_date = date("Y-m-d",strtotime($start));
    	  
    		$end = $data[$i]['O'];//exit();
    		$end = str_replace("/", "-", $end);
    		$end_date = date("Y-m-d",strtotime($end));
    
    
    		/////////////////////////////////// exist customer //////////////////////////////////////
    		$cus_id = $this->getExistCustomerByName($data[$i]['B']);
    		 
    		if(!empty($cus_id)){
    			$this->_name='rms_customer';
    			$arr = array(
    					'first_name'	=>$data[$i]['B'],
    					'last_name'		=>$data[$i]['B'],
    					'phone'			=>$data[$i]['D'],// Food
    					'user_id'		=>1,
    			);
    			$cus_id = $this->insert($arr);
    		}
    
    
    		$qty=1;
    		$qty_day=0;
    		$mystring = $data[$i]['P'];
    		$findme   = 'day';
    		$pos = strpos($mystring, $findme);
    		if(!empty($pos)){
    			$payment_term = 5;
    			$qty_day = str_replace("day", "", $data[$i]['P']);
    			$qty = $qty_day;
    		}else if($data[$i]['P']==3){
    			$payment_term = 2;//
    		}else if($data[$i]['P']==6){
    			$payment_term = 3;
    		}else if($data[$i]['P']==12){
    			$payment_term = 4;
    		}else{
    			$payment_term = 1;
    			$qty = $data[$i]['P'];
    		}
    
    
    		$this->_name = 'rms_student_payment';
    		$array = array(
    				'branch_id'=>6,
    				'student_id'=>$stu_id,
    				'receipt_number'=>$data[$i]['K'],
    
    				'payment_term'=>$payment_term,
    				'amount_sec'=>$qty_day,
    
    				'exchange_rate'=>4100,
    				'tuition_fee'=>$data[$i]['L'],
    				'discount_percent'=>0,
    				'tuition_fee_after_discount'=>$data[$i]['L'],
    
    				'material_fee'=>0,
    				'admin_fee'=>0,
    
    				'total_payment'=>$data[$i]['R'],
    				'receive_amount'=>$data[$i]['R'],
    				'paid_amount'=>$data[$i]['R'],
    				'balance_due'=>$data[$i]['Q'],
    
    				'payfor_type'=>4,//food
    
    				'grand_total_payment'=>$data[$i]['R'],
    				'grand_total_payment_in_riel'=>$data[$i]['R'] * 4100,
    				'grand_total_paid_amount'=>$data[$i]['R'],
    				'grand_total_balance'=>$data[$i]['Q'],
    
    				'note'=>$data[$i]['T'],
    				'user_id'=>1,
    				'create_date'=>$create_date,
    				'is_new'=>(empty($data[$i]['I']))?0:1,
    
    				'student_type'=>3,
    		);
    		$payment_id = $this->insert($array);
    
    
    		$this->_name = 'rms_student_paymentdetail';
    
    		$is_start = 0;
    		if($i<$count){
    			if($data[$i]['B'] != $data[$i+1]['B']){
    				$is_start = 1;
    			}
    		}else{
    			$is_start = 1;
    		}
    
    
    		$array1 = array(
    				'payment_id'=>$payment_id,
    				'type'=>5, // lunch
    				'service_id'=>$service_id,
    				'payment_term'=>$payment_term,
    
    				'fee'=>$data[$i]['L'],
    				'qty'=>$qty,
    
    				'discount_percent'=>0,
    
    				'material_fee'=>0,
    				'admin_fee'=>0,
    
    				'subtotal'=>$data[$i]['R'],
    				'paidamount'=>$data[$i]['R'],
    				'balance'=>$data[$i]['Q'],
    
    				'start_date'=>$start_date,
    				'validate'=>$end_date,
    
    				'note'=>$data[$i]['T'],
    				'user_id'=>1,
    				'is_complete'=>1,
    				'comment'=>'បង់រួច',
    
    				'is_start'=>$is_start,
    		);
    		$this->insert($array1);
    	}
    }
    
    
    
    
}   

