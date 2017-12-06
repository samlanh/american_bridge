<?php

class Accounting_Model_DbTable_DbStudentServicePayment extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_student_payment';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    
    public function getBranchId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->branch_id;
    }
    
    function getStudentPaymentStart($studentid,$service_id){
    	$db = $this->getAdapter();
    	$sql="select spd.id from rms_student_payment AS sp,rms_student_paymentdetail AS spd where
    		 sp.id=spd.payment_id and is_start=1 and service_id= $service_id and sp.student_id=$studentid limit 1 ";
//     	echo $sql;exit();
    	return $db->fetchOne($sql);
    }
    
    function getStudentExist($receipt,$studentid){
    	$db = $this->getAdapter();
    	$sql ="SELECT * FROM rms_student_payment WHERE student_id='".$studentid."' AND receipt_number= $receipt";
    	return $db->fetchRow($sql);
    }
    function setServiceToFinish($student_id,$service_id,$self_id){
    	$db = $this->getAdapter();
    	$sql=" select spd.id from rms_student_payment as sp,rms_student_paymentdetail as spd 
    			where spd.is_start = 1 and sp.id=spd.payment_id and sp.student_id = ".$student_id." and spd.service_id = ".$service_id." and spd.payment_id != ".$self_id;
    	//echo $sql;exit();
    	return $db->fetchOne($sql);
    }
    
function addStudentServicePayment($data){
		//print_r($data);exit();
		$db = $this->getAdapter();//ស្ពានភ្ជាប់ទៅកាន់Data Base
		$db->beginTransaction();//ទប់ស្កាត់មើលការErrore , មានErrore វាមិនអោយចូល
		
		$receipt = new Registrar_Model_DbTable_DbRegister();
		$type = 3;
		//$receipt_no = $receipt->getRecieptNo($type,$data['branch']);
		
		$receipt_no = $data['reciept_no'];
		
		// សិក្សាពេល User ច្រលំចុច submit 2 ដង​​ អោយវាចូលតែ1
// 		$rs = $this->getStudentExist($data['reciept_no'],$data['studentid']);
// 		if(!empty($rs)){
// 			return -1;
// 		}

		$this->_name = "rms_service";
		if($data['student_type']==1){
			//$new_car_id = $this->getNewCarId();
			$new_car_id = $data['new_car_id'];
			$array = array(
				'branch_id'=>$data['branch'],
				'type'=>4,
				'stu_id'=>$data['student_name_new'],
				'stu_code'=>$new_car_id,
				'service_id'=>$data['service'],
				'create_date'=>date('Y-m-d'),
				'reg_from'			=>1,
				'is_new'=>1,
				);
			$this->insert($array);
		}else{
			
			if($data['student_type']==3){
				$is_comeback = 0;
				$stu_id = $data['student_name_old'];
				$is_new=0;
			}else{
				$is_comeback = 1;
				$stu_id = $data['stu_drop_name'];
				$is_new=1;
			}
			
			$array = array(
				'service_id'=>$data['service'],
				'is_suspend'=>0,
				'is_comeback'=>$is_comeback,
				'is_new'=>$is_new,
			);
			$where = " type=4 and stu_id = ".$stu_id;
			$this->update($array, $where);
		}
		
		$finish=0;
		
	////////////////////////////////  ទាល់តែសិស្សចាស់បានចូលធ្វើ   //////////////////////////////////////////////////////////////////////////////
				
		if($data['student_type']!=1){
			//get id service ដែលយើងបង់ ដើម្បី update វាទៅ Finish រួចចាំ insert new service and new validate
			$finish = $this->setServiceToFinish($stu_id, $data['service'],0);
			if(!empty($finish)){
				$this->_name = "rms_student_paymentdetail";
				$array=array(
						'is_start' => 0,
						);
				$where = ' id='.$finish;
				$this->update($array, $where);
			}
			
			// សិក្សាពេល សិស្សជំពាក់ អោយវាទៅ update record នោះទៅជាបង់រួច
			if(!empty($data['balance_id'])){
				$this->_name = 'rms_student_paymentdetail';
				$ar = array(
						'is_complete'=>1,
						'comment'	 =>'បង់រួច',
						);
				$where = ' id = '.$data['balance_id'];
				$this->update($ar, $where);
			}
		}
		
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		if($data['student_type']==1){
			$student_id = $data['student_name_new'];	
			$is_new = 1;
		}else if($data['student_type']==3){
			$student_id = $data['student_name_old'];	
			$is_new = 0;
		}else{
			$student_id = $data['stu_drop_name'];
			$is_new = 1;
		}
		
		if(!empty($data['buy_product'])){
			$buy_product = 1;
		}else{
			$buy_product = 0;
		}
		
		$tuitionfee = $data['service_fee'] * $data['qty'];
		
		$this->_name = 'rms_student_payment';
		try{
			$arr=array(
					'student_id'		=>$student_id,
					'receipt_number'	=>$receipt_no,
					
					'buy_product'		=>$buy_product,
					
					//'year'				=>$data['study_year'],
					'exchange_rate'		=>$data['ex_rate'],
					'tuition_fee'		=>$tuitionfee,
					'material_fee'		=>$data['material_fee'],
					
					
					'discount_percent'	=>$data['discount'],
					'discount_fix'		=>$data['discount_fix'],
					
					'tuition_fee_after_discount'=>($tuitionfee-$data['discount_fix']) - (($tuitionfee-$data['discount_fix'])*($data['discount']/100)),
					
					'total_payment'		=>$data['total_payment'],
					'receive_amount'	=>$data['paid_amount'],
					'paid_amount'		=>$data['paid_amount'],
					'balance_due'		=>$data['balance'],
					//'amount_in_khmer'	=>$data['char_price'],
					'note'				=>$data['other'],
					'time_for_car'		=>$data['time_identity'],
					
					'grand_total_payment'			=>$data['grand_total'],
					'grand_total_payment_in_riel'	=>$data['convert_to_riels'],
					'grand_total_paid_amount'		=>$data['total_received'],
					'grand_total_balance'			=>$data['total_balance'],
					
					'is_new'			=>$is_new,
					'student_type'		=>$data['student_type'],
					'payfor_type'		=>3 , // for service
					'create_date'		=>date("Y-m-d H:i:s"),
					'user_id'			=>$this->getUserId(),
					
					'branch_id'			=>$data['branch'],
					
					'reg_from'			=>1,  // from accounting
				);
				$payment_id = $this->insert($arr);
				
				// សិក្សា​ថា​តើមាន balance រឺអត់
				$this->_name='rms_student_paymentdetail';
				$balance = $data['total_payment'] - $data['paid_amount'];
				if($balance>0){
					$is_complete = 0;
					$comment = 'មិនទាន់បង់';
				}else{
					$is_complete = 1;
					$comment = 'បង់រួច';
				}
				
				$array = array(
						'payment_id'	=>$payment_id,
						'type'			=>3,	// car service
						'service_id'	=>$data['service'],
						'payment_term'	=>$data['term'],
						'fee'			=>$data['service_fee'],
						'material_fee'	=>$data['material_fee'],
						'qty'			=>$data['qty'],
						'discount_percent'=>$data['discount'],
						'discount_fix'	=>$data['discount_fix'],
						'subtotal'		=>$data['total_payment'],
						'paidamount'	=>$data['paid_amount'],
						'balance'		=>$balance,
						'start_date'	=>$data['start_date'],
						'validate'		=>$data['end_date'],
						'references'	=>'from service payment',
						'note'			=>$data['other'],
						'user_id'		=>$this->getUserId(),
						'is_complete'	=>$is_complete,
						'comment'		=>$comment,
						'is_parent'		=>$finish,
						);
			$this->insert($array);
			
			if(!empty($data['buy_product'])){
				$ids = explode(',', $data['identity']);
				foreach ($ids as $i){
					$array = array(
							'payment_id'	=>$payment_id,
							'service_id'	=>$data['product_'.$i],
							'fee'			=>$data['price_'.$i],
							'qty'			=>$data['qty_'.$i],
							'discount_percent'	=>$data['discount_'.$i],
							'subtotal'		=>$data['subtotal_'.$i],
							'paidamount'	=>$data['subtotal_'.$i],
							'balance'		=>0,
							'note'			=>$data['remark'.$i],
							'type'			=>4, // 4 => type of product
							'is_complete'   =>1,
							'comment'		=>'បង់រួច',
							'user_id'		=>$this->getUserId(),
					);
					$this->insert($array);
				}
			}
			
	    	$db->commit();
		}catch (Exception $e){
			$db->rollBack();//អោយវាវិលត្រលប់ទៅដើមវីញពេលណាវាជួបErrore
			echo $e->getMessage();
		}
	}
	
	function updateStudentServicePayment($data){
		$db = $this->getAdapter();//ស្ពានភ្ជាប់ទៅកាន់Data Base
		$db->beginTransaction();//ទប់ស្កាត់មើលការErrore , មានErrore វាមិនអោយចូល
		
		
		try{
			if(!empty($data['is_void'])){
		
				///////////////////////////////// rms_student_payment ////////////////////////////////////////////
					
				$this->_name='rms_student_payment';
					
				$arr = array(
						'is_void'=>$data['is_void'],
				);
				$where = " id = ".$data['payment_id'];
				$this->update($arr, $where);
		
				///////////////////////////////// rms_student_paymentdetail ////////////////////////////////////////////
		
				if(!empty($data['parent_id'])){
					$arr = array(
							'is_start'=>1
					);
					$this->_name='rms_student_paymentdetail';
					$where=" id = ".$data['parent_id'];
					$this->update($arr,$where);
				}
		
		
				///////////////////////////////// rms_service ////////////////////////////////////////////
		
				if($data['student_type']==4){
					$this->_name='rms_service';
		
					$arr = array(
							'is_suspend'=>2,
					);
					$where = " type = 4 and stu_id = ".$data['student_name_old'];
					$this->update($arr, $where);
				}
		
				////////////////////////////////////////////////////////////////////////////////////////////
		
				$db->commit();
				return 0;
					
			}else{
				$db->commit();
				return 0;
			}
		}catch (Exception $e){
			echo $e->getMessage();
			$db->rollBack();
		}
		
		return 0;

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		
		// update service មុនទៅជាប្រើប្រាស់វិញសិន
		if(!empty($data['is_parent'])){
			$this->_name = "rms_student_paymentdetail";
			$arr = array(
					'is_start' => 1,
					);
			$where =" id = ".$data['is_parent'];
			$this->update($arr, $where);
		}
		
		//get id service ដែលយើងបង់ ដើម្បី update វាទៅ Finish រួចចាំ insert new service and new validate
		$finish = $this->setServiceToFinish($data['studentid'], $data['service'] , $data['id']);
		if(!empty($finish)){
			$this->_name = "rms_student_paymentdetail";
			$array=array(
					'is_start' => 0,
			);
			$where = ' id = '.$finish;
			$this->update($array, $where);
		}
		$this->_name = 'rms_student_payment';
		try{
			$arr=array(
				'student_id'		=>$data['studentid'],
				'receipt_number'	=>$data['reciept_no'],
				'year'				=>$data['study_year'],
				'tuition_fee'		=>$data['service_fee'],
				'discount_percent'	=>$data['discount'],
				'total_payment'		=>$data['total_payment'],
				'receive_amount'	=>$data['paid_amount'],
				'paid_amount'		=>$data['paid_amount'],
				'total'				=>$data['paid_amount'],
				'return_amount'		=>$data['return'],
				'balance_due'		=>$data['balance'],
				'note'				=>$data['other'],
				'time'				=>$data['time'],
				'payfor_type'		=>3 ,
				'user_id'			=>$this->getUserId(),
				'branch_id'			=>$data['branch'],
			);
			$where =$this->getAdapter()->quoteInto("id=?", $data['id']);
			$this->update($arr,$where);
			  
			$this->_name='rms_student_paymentdetail';
			$balance = $data['total_payment'] - $data['paid_amount'];
			if($balance>0){
				$is_complete = 0;
				$comment = 'មិនទាន់បង់';
			}else{
				$is_complete = 1;
				$comment = 'បង់រួច';
			}
			$array = array(
					'type'			=>3,
					'service_id'	=>$data['service'],
					'payment_term'	=>$data['term'],
					'fee'			=>$data['service_fee'],
					'qty'			=>$data['qty'],
					'discount_percent'=>$data['discount'],
					'subtotal'		=>$data['total_payment'],
					'paidamount'	=>$data['paid_amount'],
					'balance'		=>$data['balance'],
					'start_date'	=>$data['start_date'],
					'validate'		=>$data['end_date'],
					'references'	=>'from service payment',
					'note'			=>$data['other'],
					'user_id'		=>$this->getUserId(),
					'is_complete'	=>$is_complete,
					'comment'		=>$comment,
					'is_parent'		=>$finish,
			);
			$where = ' payment_id = '.$data['id'];
			$this->update($array, $where);
    		$db->commit();
    		return true;
		}catch (Exception $e){
			echo $e->getMessage();
			$db->rollBack();//អោយវាវិលត្រលប់ទៅដើមវីញពេលណាវាជួបErrore
		}
	}
		
	function getServicePaymentDetail($id) {
		$db = $this->getAdapter();
		$sql="select * from rms_student_payment AS sp,rms_student_paymentdetail AS spd where
		sp.id=spd.payment_id and sp.id=$id";
		return $db->fetchAll($sql);
	}
		
    function getAllStudenTServicePayment($search){
    	$user=$this->getUserId();
    	$db=$this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal;
    	$branch_id = $_db->getAccessPermission('sp.branch_id');
    	
    	$sql="select sp.id,
	    		(select branch_namekh from rms_branch where br_id = sp.branch_id) as branch,
				(select sv.stu_code from rms_service as sv where sv.stu_id=sp.student_id and sv.type=4 limit 1)AS code,
		    	(select CONCAT(stu_khname,' - ',stu_enname) from rms_student where rms_student.stu_id=sp.student_id limit 1)AS name,
		    	(select name_kh from rms_view where rms_view.type=2 and rms_view.key_code=(select sex from rms_student where rms_student.stu_id=sp.student_id limit 1) limit 1)AS sex,
		    	receipt_number,
		    	sp.grand_total_payment,
		    	sp.grand_total_paid_amount,
		    	sp.grand_total_balance,
		    	create_date,
		    	(select CONCAT(last_name,' ',first_name) from rms_users where rms_users.id=sp.user_id) AS user,
		    	(select name_en from rms_view where type=12 and key_code = sp.is_void) as void_status 
	    	from 
	    		rms_student_payment as sp 
	    	where 1 
	    		and (select type from rms_student_paymentdetail where rms_student_paymentdetail.payment_id=sp.id limit 1)=3 
    			$branch_id
    		";
    	
    	$from_date =(empty($search['start_date']))? '1': " sp.create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': " sp.create_date <= '".$search['end_date']." 23:59:59'";
    	$where = " AND ".$from_date." AND ".$to_date;
		
    	$order=" ORDER BY id DESC ";
    	
    	if(!empty($search['adv_search'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['adv_search']));
    		$s_where[] = " receipt_number LIKE '%{$s_search}%'";
    		$s_where[] = " (select sv.stu_code from rms_service as sv where sv.stu_id=sp.student_id and sv.type=4 ) LIKE '%{$s_search}%'";
    		$s_where[] = " (select stu_enname from rms_student where rms_student.stu_id=sp.student_id) LIKE '%{$s_search}%'";
    		$s_where[] = " (select stu_khname from rms_student where rms_student.stu_id=sp.student_id) LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	if(!empty($search['branch'])){
    		$where.=" AND sp.branch_id=".$search['branch'];
    	}
    	if(!empty($search['user'])){
    		$where.=" AND sp.user_id=".$search['user'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    }
    function getStudentServicePaymentByID($id){
    	$db=$this->getAdapter();
    	$sql="select 
    				*,
    				(select stu_code from rms_service where student_id = stu_id and rms_service.type=4 limit 1) as code 
    			from 
    				rms_student_payment AS sp,
    				rms_student_paymentdetail as spd
    			where 
    				sp.id = spd.payment_id	
    				and spd.type=3
    				and sp.id=".$id;
    	return $db->fetchRow($sql);
    }
    function getStudentBuyProductById($id){
    	$db=$this->getAdapter();
    	$sql=" SELECT
			    	spd.service_id,
			    	spd.fee,
			    	spd.qty,
			    	spd.discount_percent,
			    	spd.subtotal,
			    	spd.note
			    FROM
			    	rms_student_paymentdetail AS spd
			    WHERE
			    	spd.type = 4
			    	AND spd.payment_id = ".$id;
    
    	return $db->fetchAll($sql);
    }
    
    function getStudentServicePaymentDetailByID($id){
    	$db=$this->getAdapter();
    	$sql="select * from rms_student_paymentdetail where type=3 and payment_id=".$id;
    	return $db->fetchRow($sql);
    }
    
    function getAllPaymentTerm($id){
    	$db=$this->getAdapter();
    	$sql="select * from rms_student_paymentdetail where payment_id=".$id;
    	return $db->fetchAll($sql);
    }
    
    
    
    function getAllGrade($grade_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT major_id As id,major_enname As name FROM rms_major WHERE dept_id=".$grade_id;
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    function getPaymentTerm($generat,$payment_term,$grade){
    	$db = $this->getAdapter();
    	$sql="SELECT td.tuition_fee FROM rms_tuitionfee_detail AS td,`rms_tuitionfee` AS tu
    	WHERE tu.id= td.fee_id AND td.class_id=$grade AND td.payment_term=$payment_term AND tu.generation=$generat LIMIT 1";
    	return $db->fetchRow($sql);
    }
    function getPaymentGep($study_year,$levele,$payment_term){
    	$db = $this->getAdapter();
    	$sql="SELECT id,tuition_fee FROM rms_tuitionfee_detail WHERE fee_id=$study_year AND class_id=$levele AND payment_term=$payment_term LIMIT 1";
    	return $db->fetchRow($sql);
    }
    function getAllYears(){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,CONCAT(from_academic,'-',to_academic) AS years FROM rms_tuitionfee WHERE `status`=1";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    function getAllYearsProgramFee(){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,CONCAT(start_year,'-',end_year) AS years FROM mrs_program_fee WHERE `status`=1";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    public function getNewAccountNumber($type){
    	$db = $this->getAdapter();
    	$sql="  SELECT stu_id  FROM rms_student ORDER BY  stu_id DESC LIMIT 1 ";
    	$acc_no = $db->fetchOne($sql);
    	$new_acc_no= (int)$acc_no+1;
    	//echo $new_acc_no;exit();
    	$acc_no= strlen((int)$acc_no+1);
    	if($type==1){
    		$pre='K';
    	}
    	else if($type==2){
    		$pre='P';
    	}
    	else if($type==3){
    		$pre='S';
    	}else {
    		$pre='H';
    	}
    	for($i = $acc_no;$i<5;$i++){
    		$pre.='0';
    	}
    	return $pre.$new_acc_no;
    }
    public function getRecieptNo(){
    	$db = $this->getAdapter();
    	$sql="SELECT id  FROM rms_student_payment ORDER BY  id DESC LIMIT 1 ";
    	$acc_no = $db->fetchOne($sql);
    	$new_acc_no= (int)$acc_no+1;
    	$acc_no= strlen((int)$acc_no+1);
    	$pre=0;
    	for($i = $acc_no;$i<5;$i++){
    		$pre.='0';
    	}
    	return $pre.$new_acc_no;
    }
    
    public function getAllStudentCode(){
    	$db = $this->getAdapter();
    	$sql="SELECT stu_id as id,stu_code as code  FROM rms_student ORDER BY  stu_code DESC ";
    	return $db->fetchAll($sql);
    	
    }
    public function getAllStudentName(){
    	$db = $this->getAdapter();
    	$sql="SELECT stu_id as id,CONCAT(stu_khname,' - ',stu_enname) as name  FROM rms_student ORDER BY  stu_code DESC ";
    	return $db->fetchAll($sql);
    	 
    }
    public function getAllpriceByServiceTerm($studentid,$serviceid,$termid){
    	$db=$this->getAdapter();
    	$sql="select spd.id,spd.validate,spd.start_date,balance from rms_student_paymentdetail as spd,rms_student_payment as sp where sp.id=spd.payment_id and spd.service_id=$serviceid and sp.student_id=$studentid and is_complete=0 limit 1";                               
    	$row=$db->fetchRow($sql);
    	if($row['balance']>0){
    		//$row['balance']='លុយជំពាក់ពីមុន';
    		return $row;
    	}
    	else{
    		$sql="select price_fee from rms_servicefee_detail where  rms_servicefee_detail.service_id=$serviceid and rms_servicefee_detail.payment_term=$termid limit 1";
    		return $db->fetchRow($sql);
    	}
    }
    
    public function getAllpriceByServiceTermEdit($serviceid,$termid,$year){
    	$db=$this->getAdapter();
    	$sql="select price_fee from rms_servicefee_detail where  rms_servicefee_detail.service_id=$serviceid and rms_servicefee_detail.payment_term=$termid and service_feeid=$year limit 1";
    	return $db->fetchRow($sql);
    }
    
    public function getAllStudentInfo($studentid){
    	$db=$this->getAdapter();
    	$sql="select 
	    			stu_enname,
	    			stu_khname,
	    			sex,
		    		tel,
		    		(select sv.service_id from rms_service as sv where sv.type=4 and sv.stu_id = s.stu_id ) as service_id
	    		 from 
	    			rms_student as s
	    		 where
	    		 	s.stu_id=$studentid 
	    		 limit 
	    			1
    		";
    	return $db->fetchRow($sql);
    }
    
    public function getStudentBalance($studentid,$serviceid,$termid){
    	$db=$this->getAdapter();
    	$sql="select stu_enname,stu_khname,sex from rms_student where stu_id=$studentid limit 1";
    	return $db->fetchRow($sql);
    }
    
    public function getAllService(){
    	$db=$this->getAdapter();
    	$sql="SELECT service_id as id , title as name  FROM rms_program_name WHERE type=2 and status=1 and ser_cate_id=3 ";
    	return $db->fetchAll($sql);
    }
    public function getAllServiceType(){
    	$db=$this->getAdapter();
    	$sql="SELECT id , title as name  FROM rms_program_type WHERE status=1";
    	return $db->fetchAll($sql);
    }
    
    function getStudentID($acacemic,$type){
    	$db=$this->getAdapter();
    	if($type==1){
    		$sql="SELECT stu_id As id,stu_code As name  FROM rms_student  WHERE academic_year=$acacemic";
    	}else{
    		$sql="SELECT stu_id As id,CONCAT(stu_khname,' - ',stu_khname) As name  FROM rms_student  WHERE academic_year=$acacemic";
    	}
    	return $db->fetchAll($sql);
    }
    function getYearService(){
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission();
    	
    	$db=$this->getAdapter();
    	$sql="SELECT sf.id,CONCAT(from_academic,'-',to_academic,'(',generation,')',(SELECT name_en FROM rms_view WHERE type=7 AND key_code=time)) AS years 
    		  FROM rms_servicefee as sf,rms_tuitionfee as tf WHERE tf.id=sf.academic_year and tf.`status`= 1 $branch_id ORDER BY sf.id DESC";
    	return $db->fetchAll($sql);
    }
    function addService($data){
    	$this->_name="rms_program_name";
    	$db = $this->getAdapter();
    	$arr = array(
    			'ser_cate_id'=>$data['service_type'] ,
    			'title'=>$data['service_name'] ,
    			'description'=>$data['description'] ,
    			'status'=>$data['status_popup'] ,
    			'user_id'=>$this->getUserId() ,
    			'type'=>2 ,
    			'price'=>0 ,
    			'create_date'=>Zend_Date::now(),
    			);
    	return $this->insert($arr);
    }
	function getServiceCate($service_id){
    	$db=$this->getAdapter();
    	$sql="SELECT pt.title FROM rms_program_type AS pt,rms_program_name AS pn WHERE pt.id=pn.ser_cate_id AND pn.service_id=$service_id";
    	return $db->fetchOne($sql);
    }
	
    function getServiceStartDate($service_id,$stu_id){
    	$db=$this->getAdapter();
    	$sql="SELECT 
				  spd.validate 
				FROM
				  rms_student_paymentdetail AS spd,
				  rms_student_payment AS sp 
				WHERE sp.id = spd.payment_id 
				  AND sp.student_id = $stu_id 
				  AND spd.service_id = $service_id 
				  AND is_start = 1
				  and is_complete=1 LIMIT 1 
			";	
    	return $db->fetchOne($sql);
    }
	
    function getAllYearByBranch($branch_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT 
				  sf.id,
				  CONCAT(from_academic,'-',to_academic,'(',generation,')',(SELECT name_en FROM rms_view WHERE TYPE=7 AND key_code=time)) AS name
				FROM
				  rms_tuitionfee as tf,
				  rms_servicefee as sf 
				WHERE `status` = 1 
				  and tf.id=sf.academic_Year
				  AND branch_id = $branch_id 
				";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
	
    
    function getNewCarId(){
    	$db=$this->getAdapter();
    	$sql="SELECT
    	count(stu_id)
    	FROM
    	rms_service
    	where
    	status=1
    	and type=4
    	";
    	$acc_no =  $db->fetchOne($sql);
    	 
    	$length = '';
    	$pre='TR';
    	 
    	$new_acc_no= (int)$acc_no+1;
    	$length = strlen((int)$new_acc_no);
    	 
    	for($i = $length;$i<4;$i++){
    		$pre.='0';
    	}
    	 
    	return $pre.$new_acc_no;
    }
    
    function getAllNewStudentName($branch_id){
    	$db=$this->getAdapter();
    	 
    	$sql="SELECT s.stu_id as id, CONCAT(s.stu_khname,'-',s.stu_enname) as name from rms_student as s  where s.status=1 and s.is_subspend=0
    	and s.stu_id NOT IN (select sv.stu_id from rms_service as sv where sv.status=1 and sv.type=4) and s.branch_id = $branch_id ";
    	 
    	return $db->fetchAll($sql);
    }
    
    function getAllOldStudent($branch_id){
    	$db=$this->getAdapter();
    	
    	$sql="SELECT s.stu_id as id, CONCAT(s.stu_khname,'-',s.stu_enname) as name from rms_student as s,rms_service as sv
    	where sv.stu_id = s.stu_id and sv.type=4 and sv.status=1 and sv.is_suspend=0 and sv.branch_id = $branch_id ";
    	$stu_name = $db->fetchAll($sql);
    	
    	$sql="SELECT s.stu_id as id, sv.stu_code as name from rms_student as s,rms_service as sv
    	where sv.stu_id = s.stu_id and sv.type=4 and sv.status=1 and sv.is_suspend=0 and sv.branch_id = $branch_id  ";
    	$stu_id = $db->fetchAll($sql);
    	
    	$result = array(0=>$stu_id,1=>$stu_name);
    	return $result;
    }
    
    
    function getAllDropStudent($branch_id){
    	$db=$this->getAdapter();
    	 
    	$sql="SELECT 
    				sv.stu_id as id,
    				CONCAT(s.stu_khname,'-',s.stu_enname) as name 
    			from 
    				rms_student as s,
    				rms_service as sv
    			where 
    				sv.stu_id = s.stu_id 
    				and sv.type=4 
    				and sv.status=1 
    				and sv.is_suspend != 0
    				and sv.branch_id = $branch_id
    		";
    	$stu_name = $db->fetchAll($sql);
    	 
    	$sql="SELECT 
    				sv.stu_id as id,
    				sv.stu_code as name
    			from 
    				rms_student as s,
    				rms_service as sv
    			where 
    				sv.stu_id = s.stu_id 
    				and sv.type=4 
    				and sv.status=1 
    				and sv.is_suspend != 0
    				and sv.branch_id = $branch_id ";
    	$stu_id = $db->fetchAll($sql);
    	 
    	$result = array(0=>$stu_id,1=>$stu_name);
    	return $result;
    	 
    }
    
    
    function getAllOldStudentName(){
    	$db=$this->getAdapter();
    	 
    	$_db = new Application_Model_DbTable_DbGlobal;
    	$branch_id = $_db->getAccessPermission('s.branch_id');
    	 
    	$sql="SELECT s.stu_id , CONCAT(s.stu_khname,'-',s.stu_enname) as stu_name from rms_student as s,rms_service as sv
    	where sv.stu_id = s.stu_id and sv.type=4 and sv.status=1 and s.is_subspend=0 $branch_id ";
    	return $db->fetchAll($sql);
    }
    
    function getAllOldCarId(){
    $db=$this->getAdapter();
    	 
    	$_db = new Application_Model_DbTable_DbGlobal;
    	$branch_id = $_db->getAccessPermission('s.branch_id');
    	 
    	$sql="SELECT s.stu_id , sv.stu_code from rms_student as s,rms_service as sv where sv.stu_id = s.stu_id and sv.type=4 and sv.status=1
    	and s.is_subspend=0 $branch_id ";
    	return $db->fetchAll($sql);
    }
    
    
    function getCarIdByService($service){
    $db=$this->getAdapter();
    	$sql=" select car_id from rms_program_name where service_id = $service ";
    	$car_id = $db->fetchOne($sql);
    	 
    	if(!empty($car_id)){
    	$sql="select carid from rms_car where id = $car_id ";
    	return $db->fetchOne($sql);
    	}
    }
    
    
	
}





