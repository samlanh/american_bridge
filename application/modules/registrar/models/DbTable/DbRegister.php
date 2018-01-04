<?php

class Registrar_Model_DbTable_DbRegister extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_student';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
    
    public function getBranchId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->branch_id;
    }
    
    function getStudentPaymentStart($studentid,$service_id,$type){
    	$db = $this->getAdapter();
    	$sql="select spd.id from rms_student_payment AS sp,rms_student_paymentdetail AS spd where
    	sp.id=spd.payment_id and is_start=1 and service_id= $service_id and sp.student_id=$studentid and spd.type=$type limit 1 ";
    	//echo $sql;exit();
    	return $db->fetchOne($sql);
    }
    function getStuidExist($stu_code){
    	$db=$this->getAdapter();
    	$sql="SELECT stu_id,stu_code FROM rms_student WHERE stu_code=$stu_code LIMIT 1";
    	return $db->fetchRow($sql);
    }
    
    
	function addRegister($data){
		
		//$stu_code = $this->getNewAccountNumber($data['dept'],0);
		//$receipt_no = $this->getRecieptNo($type,0);
		
		$stu_code=$data['stu_id'];
		$receipt_no=$data['reciept_no'];
		
		$db = $this->getAdapter();//ស្ពានភ្ជាប់ទៅកាន់Data Base
		$db->beginTransaction();//ទប់ស្កាត់មើលការErrore , មានErrore វាមិនអោយចូល
			try{
				if($data['student_type']==1){//new student
					$this->_name = "rms_student";
					if($data['degree_type']==1){
						$stu_type=1;  // khmer fulltime
						$payfor_type = 1;  // khmer fulltime
					}else{
						$stu_type=2;  // english fulltime
						$payfor_type = 6;  // english fulltime
					}
					
					$arr=array(
							'stu_code'		=>$stu_code,
							'academic_year'	=>$data['study_year'],
							'stu_khname'	=>$data['kh_name'],
							'stu_enname'	=>$data['en_name'],
							'sex'			=>$data['sex'],
					
							'tel'			=>$data['phone'],
							'address'		=>$data['address'],
							'dob'			=>$data['dob'],
							'room'			=>$data['room'],
							
							'session'		=>$data['session'],
							'degree'		=>$data['dept'],
							'grade'			=>$data['grade'],
							'stu_type'		=>$stu_type,
							'create_date'	=>date('Y-m-d H:i:s'),
							'user_id'		=>$this->getUserId(),
							'branch_id'		=>$this->getBranchId(),
							
							'is_stu_new' =>1,
					
					);
					$id= $this->insert($arr);
					
				}else { // old student or drop student
					
					$this->_name = "rms_student";
						
					if($data['degree_type']==1){
						$stu_type=1;  // khmer fulltime
						$payfor_type = 1;  // khmer fulltime
					}else{
						$stu_type=2;  // english fulltime
						$payfor_type = 6;  // english fulltime
					}
					
					// សិក្សាប្រសិនបើប្តូរ រឺ ឡើងកម្រិត Generate new stu_code ឲ្យ , else stu_code នៅដដែល
					if($data['old_degree']==$data['dept']){
						$stu_code = $data['old_stu_code'];
					}else{
						$stu_code = $this->getNewAccountNumber($data['dept'],0);
					}
				////////////////////////////////////////////////////////////////////////
					
					
					if($data['student_type']==3){
						$id = $data['old_studens'];
						$is_comeback = 0;
						$is_stu_new = 0;
					}else{
						$id = $data['drop_studens'];
						$is_comeback = 1;
						$is_stu_new = 1;
					}
					
				// update student information to grade that input
					$arr = array(
							'stu_code'	=>$stu_code,
							'session'	=>$data['session'],
							'degree'	=>$data['dept'],
							'grade'		=>$data['grade'],
							
							'stu_khname'=>$data['kh_name'],
							'stu_enname'=>$data['en_name'],
							'sex'		=>$data['sex'],
							'tel'		=>$data['phone'],
							'address'	=>$data['address'],
							'dob'		=>$data['dob'],
							'room'		=>$data['room'],
							'academic_year'=>$data['study_year'],
							'stu_type'	=>$stu_type,
							'is_subspend'=>0,
							'is_stu_new' =>$is_stu_new,
							'is_comeback'=>$is_comeback,
						);
					$where = ' stu_id = '.$id;
					$this->update($arr, $where);
				}

			
				if($data['payment_term']==5){
					$price_per_sec = $data['price_per_section'];
					$amount_sec = $data['amount_section'];
					
					$tuitionfee = $data['tuitionfee'] * $data['amount_section'];
				}else{
					$price_per_sec = null;
					$amount_sec = null;
					
					$tuitionfee = $data['tuitionfee'];
				}
				
				if($data['student_type']==3){
					$is_new = 0;
				}else{
					$is_new = 1;
				}
				
				
				if(!empty($data['buy_product'])){
					$buy_product = 1;
				}else{
					$buy_product = 0;
				}
				
				$this->_name='rms_student_payment';
				$arr=array(
						'student_id'	=>$id,
						'receipt_number'=>$receipt_no,
						
						'buy_product'	=>$buy_product,
						
						'year'			=>$data['study_year'],
						'degree'		=>$data['dept'],
						'grade'			=>$data['grade'],
						'time'			=>$data['study_hour'],
						'session'		=>$data['session'],
						'room_id'		=>$data['room'],
						'payment_term'	=>$data['payment_term'],
						
						'price_per_sec'	=>$price_per_sec,
						'amount_sec'	=>$amount_sec,				
						
						'exchange_rate'	=>$data['ex_rate'],
						'tuition_fee'	=>$tuitionfee,
						'discount_percent'=>$data['discount'],
						'discount_fix'	=>$data['discount_fix'],
						'tuition_fee_after_discount'=>($tuitionfee - $data['discount_fix']) - (($tuitionfee - $data['discount_fix'])*($data['discount']/100)),
						'other_fee'		=>$data['remark'],
						'admin_fee'		=>$data['addmin_fee'],
						'material_fee'	=>$data['material_fee'],
						'total_payment'	=>$data['total'],
						'paid_amount'	=>$data['books'],
						'receive_amount'=>$data['books'],
						'balance_due'	=>$data['remaining'],
						'note'			=>$data['not'],
						
						
						'grand_total_payment'			=>$data['grand_total'],
						'grand_total_payment_in_riel'	=>$data['convert_to_riels'],
						'grand_total_paid_amount'		=>$data['total_received'],
						'grand_total_balance'			=>$data['total_balance'],
						
						'is_new'		=>$is_new,
						'student_type'	=>$data['student_type'],
						'create_date'	=>date('Y-m-d H:i:s'),
						'payfor_type'	=>$payfor_type,
						//'amount_in_khmer'=>$data['char_price'],
						'user_id'		=>$this->getUserId(),
						'branch_id'		=>$this->getBranchId(),
				);
				$paymentid = $this->insert($arr);
				
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
				
				$this->_name='rms_study_history';
				if($data['student_type']==1){
						
					if($data['dept']<=3){
						$stu_type=1;
					}else{
						$stu_type=2;
					}
						
					$arr=array(
							'stu_id'		=>$id,
							'stu_type'		=>$stu_type,
							'stu_code'		=>$stu_code,
							'academic_year'	=>$data['study_year'],
							'degree'		=>$data['dept'],
							'grade'			=>$data['grade'],
							'session'		=>$data['session'],
							'room'			=>$data['room'],
							'user_id'		=>$this->getUserId(),
							'branch_id'		=>$this->getBranchId(),
							'payment_id'	=>$paymentid,
							'reg_from'		=>0, // from registrar
					);
					$this->insert($arr);
						
				}else{
					if($data['old_grade']!=$data['grade']){
							
						if($data['old_degree']!=$data['dept']){
							$sql="select id from rms_study_history where stu_id=$id and is_finished_grade=0 and degree=".$data['old_degree'];
							$finished_record = $db->fetchOne($sql);
							if(!empty($finished_record)){
								$array=array(
										'is_finished_grade'=>1,
										'is_finished_degree'=>1
								);
								$where=" id = $finished_record ";
								$this->update($array, $where);
							}
						}else if($data['old_grade']!=$data['grade']){
								
							$sql="select id from rms_study_history where stu_id=$id and is_finished_grade=0 and grade=".$data['old_grade'];
							$finished_record = $db->fetchOne($sql);
							if(!empty($finished_record)){
								$array=array(
										'is_finished_grade'=>1,
								);
								$where=" id = $finished_record ";
								$this->update($array, $where);
							}
						}
				
						if($data['degree_type']==1){ // khmer FT
							$stu_type=1;
						}else{ // Eng FT
							$stu_type=2;
						}
							
						if($data['old_degree']==$data['dept']){
							$stu_code = $data['old_stu_code'];
						}else{
							$stu_code = $this->getNewAccountNumber($data['dept'],0);
						}
				
						$arr=array(
								'stu_id'		=>$id,
								'stu_type'		=>$stu_type,
								'stu_code'		=>$stu_code,
								'academic_year'	=>$data['study_year'],
								'degree'		=>$data['dept'],
								'grade'			=>$data['grade'],
								'session'		=>$data['session'],
								'room'			=>$data['room'],
								'user_id'		=>$this->getUserId(),
				
								'id_record_finished'	=>$finished_record,
								'payment_id'	=>$paymentid,
								
								'branch_id'		=>$this->getBranchId(),
								'reg_from'		=>0, // from registrar
				
						);
						$this->insert($arr);
				
					}
				}
				
				
		////////////////////// rms_student_id  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				$this->_name='rms_student_id';
				
				if($data['student_type']==1){ // new student
				
					if($data['degree_type']==1){ // khmer FT
						$stu_type=1;
					}else{ // Eng FT
						$stu_type=2;
					}
				
					$arr=array(
							'branch_id'		=>$this->getBranchId(),
							'stu_type'		=>$stu_type,
							'stu_id'		=>$id,
							'degree'		=>$data['dept'],
					);
					$this->insert($arr);
				 
				}else{ // old student
					if($data['old_degree']!=$data['dept']){
						$sql="select id from rms_student_id where stu_id = $id and degree = ".$data['old_degree']." and status=1 ";
						$finished_degree_id = $db->fetchOne($sql);
						if(!empty($finished_degree_id)){
							$arr = array(
									'is_finish'=>1,
									);
							$where = " id = $finished_degree_id ";
							$this->update($arr, $where);
						}
						
						if($data['degree_type']==1){ // khmer FT
							$stu_type=1;
						}else{ // Eng FT
							$stu_type=2;
						}
						
						$arr=array(
								'branch_id'		=>$this->getBranchId(),
								'stu_id'		=>$id,
								'stu_type'		=>$stu_type,
								'degree'		=>$data['dept'],
								'is_parent'		=>$finished_degree_id,
							);
						
						$this->insert($arr);
					}
				}
				
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				
				$this->_name='rms_student_paymentdetail';
				
	//update is_start=0 ដើម្បីអោយដឹងថា Service និង ឈប់ប្រើ រឺ  expired នៅពេលដែលសិស្សចាស់បង់លុយម្តងទៀត រួច store ID record that updated in is_parent of new record

				$service = 4; // លេខ 4 ជាសេវាកម្មចុះឈ្មោះចូលរៀន
				$type=1; 	  // លេខ 1 ជាប្រភេទសិស្សពី kindergarten ដល់ ទី12
				$expired_record_id = $this->getStudentPaymentStart($id,$service,$type);
				if(empty($expired_record_id)){
					$expired_record_id=0;
				}
				$where=" id = $expired_record_id ";
				$arr = array(
						'is_start'=>0
				);
				$this->update($arr,$where);
				
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
				
				
	///////////// update is_complet = 1 becuse for get balance price //////////////////////////
	
				$this->_name='rms_student_paymentdetail';
				if(!empty($data['id_balance_record'])){
					$where="id=".$data['id_balance_record'];
					$arr = array(
							'is_complete'=>1,
							'comment'=>"បង់រួច",
					);
					$this->update($arr,$where);
				}
				
	//////////////////////////////////////////////////////////////////////////////
				
				$complete=1;
				if($data['remaining']>0){
					$complete=0;
					$comment="មិនទាន់បង់";
				}else{
					$complete=1;
					$comment="បង់រួច";
				}
                 //add rms_student_paymentdetail 3 rows if (tuitionfee !=0 or admin_fee!=0 or other_fee!=0) 		
	             if($data){
					
	             	$type=4; // tuition_fee service
					
					$fee = $data["tuitionfee"];
					if($data['payment_term']==5){
						$qty=$data['amount_section'];
					}else{
						$qty=1;
					}
					
					
// 					$subtotal=$data["tuitionfee"]-($data["tuitionfee"]*$data['discount']/100);
// 					$subtotal = $subtotal + $data["remark"]+$data["addmin_fee"] ;
					$subtotal = $data['total'];
					
					$discount=$data['discount'];
					$paidamount=$data['books'];
					//$paidamount=$paidamount-($data["remark"]+$data["addmin_fee"]);
					$balance= $subtotal - $data['books'];
					 
					 $arr=array(
	             			'payment_id'=>$paymentid,
	             			'type'=>1,
	             			'service_id'=>$type,
	             			'payment_term'=>$data['payment_term'],
	             			'fee'=>$fee,
	             			'qty'=>$qty,
	             			//'subtotal'=>$data['total'],
					 
					 		'admin_fee'=>$data['addmin_fee'],
					 		'other_fee'=>$data['remark'],
					 		'material_fee'	=>$data['material_fee'],
					 		
	             			'subtotal'=>$subtotal,//$subtotal,
	             			'paidamount'=>$paidamount,//$paidamount,
	             			'balance'=>$balance,
	             			'discount_percent'=>$discount,//$discount,
					 		'discount_fix'	=>$data['discount_fix'],
	             			'note'=>$data['not'],
	             			'start_date'=>$data['start_date'],
	             			'validate'=>$data['end_date'],
	             			'references'=>'frome registration',
	             			'is_parent'		=>$expired_record_id,
	             			'is_complete'	=>$complete,
	             			'comment'		=>$comment,
	             			'user_id'=>$this->getUserId(),
	             	);
	             	$this->insert($arr);
	             }
	             
	             if(!empty($data['buy_product'])){
	             	
	             	$ids = explode(',', $data['identity']);
	             	foreach ($ids as $i){
	             		
	             		$array = array(
	             				'payment_id'	=>$paymentid,
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
	             
				 $db->commit();//if not errore it do....
			}catch (Exception $e){
				$db->rollBack();//អោយវាវិលត្រលប់ទៅដើមវីញពេលណាវាជួបErrore
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				echo $e->getMessage();
			}
		}
		
		
	function updateRegister($data){
		$db = $this->getAdapter();//ស្ពានភ្ជាប់ទៅកាន់Data Base
		$db->beginTransaction();//ទប់ស្កាត់មើលការErrore , មានErrore វាមិនអោយចូល
		//print_r($data);exit();
		try{
			if(!empty($data['is_void'])){
				
				///////////////////////////////// rms_student_payment ////////////////////////////////////////////
					
				$this->_name='rms_student_payment';
					
				$arr = array(
						'is_void'=>$data['is_void'],
				);
				$where = " id = ".$data['pay_id'];
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
	
				//////////////////////// study history ///////////////////////////
				
				$this->_name='rms_study_history';
				
				$sql="select id,id_record_finished from rms_study_history where payment_id = ".$data['pay_id']." and stu_id = ".$data['old_studens'] ;
				$result = $db->fetchRow($sql);
				if(!empty($result['id_record_finished'])){
					
					$sql1 = "select * from rms_study_history where id = ".$result['id_record_finished'];
					$row = $db->fetchRow($sql1);
					if(!empty($row)){
					//////////// update student info back //////////////
						$this->_name='rms_student';
						$arr = array(
								'stu_type'		=>$row['stu_type'],
								'stu_code'		=>$row['stu_code'],
								'academic_year'	=>$row['academic_year'],
								'degree'		=>$row['degree'],
								'grade'			=>$row['grade'],
								'session'		=>$row['session'],
								'room'			=>$row['room'],
								);
						$where2 = " stu_id = ".$row['stu_id'];
						$this->update($arr, $where2);
					}
					
					//////////////////// update old study_history to active ///////////
					$this->_name='rms_study_history';
					$array = array(
								'is_finished_grade'=>0,
								'is_finished_degree'=>0,
							);
					$where = " id = ".$result['id_record_finished'];
					$this->update($array, $where);
					
					////////////////////////// delete new study history that voided ////////////
					$where1 = "id = ".$result['id'];
					$this->delete($where1);
				}
				
				
				
				///////////////////////////////// rms_student ////////////////////////////////////////////
				
				if($data['student_type']==4){
					$this->_name='rms_student';
					
					$arr = array(
							'is_subspend'=>2,
					);
					$where = " stu_id = ".$data['old_studens'];
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
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		try{
			//សំរាប់ update សិស្សចាស់ is start = 1 វិញ
			if($data['parent_id']!=0){
				if($data['id']!=$data['old_studens']){
					$arr = array(
							'is_start'=>1
					);
					$this->_name='rms_student_paymentdetail';
					$where=" id = ".$data['parent_id'];
					$this->update($arr,$where);
				}
			}
			$this->_name='rms_student';
			if($data['student_type']==3){//old stu
				$this->_name = "rms_student";
				$stu_type='';
				if($data['dept']==1){
					$stu_type=3;
				}else{
					$stu_type=1;
				}
				// update student information to grade that input
				$id=$data['old_studens'];
				$arr = array(
						'session'	=>$data['session'],
						'degree'	=>$data['dept'],
						'grade'		=>$data['grade'],
						'academic_year'=>$data['study_year'],
						'stu_type'	=>$stu_type,
				);
				$where = ' stu_id = '.$id;
				$this->update($arr, $where);
			}else{
				$stu_type='';
				if($data['dept']==1){
					$stu_type=3;
				}else{
					$stu_type=1;
				}  
				$arr=array(
						'stu_code'=>$data['stu_id'],
						'academic_year'=>$data['study_year'],
						'stu_khname'=>$data['kh_name'],
						'stu_enname'=>$data['en_name'],
						'sex'=>$data['sex'],
						'session'=>$data['session'],
						'degree'=>$data['dept'],
						'grade'=>$data['grade'],
						//'create_date'=>	date('Y-m-d'),
						'stu_type'=>$stu_type,
						//'stu_type'=>1,
				);
				$where=" stu_id=".$data['id'];
				$this->update($arr, $where);
			}
				
			$this->_name='rms_study_history';
			$arr=array(
					'stu_id'		=>$id,
					'stu_type'		=>$stu_type,
					'stu_code'		=>$data['stu_id'],
					'academic_year'	=>$data['study_year'],
					'degree'		=>$data['dept'],
					'grade'			=>$data['grade'],
					'session'		=>$data['session'],
					'user_id'		=>$this->getUserId(),
			);
			$where=" stu_id=".$data['id'];
			$this->update($arr, $where);
			
			$this->_name='rms_student_payment';
		  // print_r($data);exit();
			$arr=array(
				'student_id'=>$data['old_studens'],
				'receipt_number'=>$data['reciept_no'],
				'session'	=>$data['session'],
				'time'=>$data['study_hour'],
// 				'end_hour'=>$data['to_time'],
				'payment_term'=>$data['payment_term'],
				'tuition_fee'=>$data['tuitionfee'],
				'total_payment'=>$data['tuitionfee'] - ($data['tuitionfee']*($data['discount']/100)),
				'discount_percent'=>$data['discount'],
				'other_fee'=>$data['remark'],
				'admin_fee'=>$data['addmin_fee'],
				'total'=>$data['total'],
				'paid_amount'=>$data['books'],
				'receive_amount'=>$data['books'],
				'balance_due'=>$data['remaining'],
				'note'=>$data['not'],
// 				'create_date'=>	date('Y-m-d'),
// 				'amount_in_khmer'=>$data['char_price'],
				'payfor_type'=>1,
// 				'user_id'=>$this->getUserId(),
			);
			$where="id=".$data['pay_id'];
			$this->update($arr, $where);
				

			$this->_name='rms_student_paymentdetail';
			//update is_start=0 ដើម្បីអោយដឹងថា Service និង ឈប់ប្រើ រឺ  ផ្អាក នៅពេលដែលសិស្សចាស់បង់លុយម្តងទៀត រួច store ID record that updated in is_parent of new record
			$service = 4; // លេខ 4 ជាសេវាកម្មចុះឈ្មោះចូលរៀន
			$type=1; 	  // លេខ 1 ជាប្រភេទសិស្សពី kindergarten ដល់ ទី12
			$payment_id_ser = $this->getStudentPaymentStart($id,$service,$type);
			if(empty($payment_id_ser)){
				$payment_id_ser=0;
			}
			$where=" id = $payment_id_ser ";
			$arr = array(
					'is_start'=>0
			);
			$this->update($arr,$where);
			
			
			$this->_name='rms_student_paymentdetail';
			if(!empty($data['ids'])){
				$where="id=".$data['ids'];
				$arr = array(
						'is_complete'=>1,
						'comment'=>"បង់រួច",
				);
				$this->update($arr,$where);
			}
			$complete=1;
			if($data['remaining']>0){
				$complete=0;
				$comment="មិនទាន់បង់";
			}else{
				$complete=1;
				$comment="បង់រួច";
			}
			//update rms_student_paymentdetail 3 rows if (tuitionfee !=0 or admin_fee!=0 or other_fee!=0)
			$this->_name='rms_student_paymentdetail';
			$where="payment_id=".$data['pay_id'];
			$this->delete($where);
			if($data){
				$paymentid=$data['pay_id'];
				$type=4; // tuition_fee service
				$fee = $data["tuitionfee"];
				$subtotal=$data["tuitionfee"]-($data["tuitionfee"]*$data['discount']/100);
				$subtotal = $subtotal + $data["remark"]+$data["addmin_fee"] ; //money include admin fee and other fee
				$discount=$data['discount'];
				$paidamount=$data['books'];
				//$paidamount=$paidamount-($data["remark"]+$data["addmin_fee"]);
				$balance= $subtotal - $data['books'];
				 
				$arr=array(
             			'payment_id'=>$paymentid,
             			'type'=>1,
             			'service_id'=>$type,
             			'payment_term'=>$data['payment_term'],
             			'fee'=>$fee,
             			'qty'=>1,
             			//'subtotal'=>$data['total'],
             			'subtotal'=>$subtotal,//$subtotal,
             			'paidamount'=>$paidamount,//$paidamount,
             			'balance'=>$balance,
             			'discount_percent'=>$discount,//$discount,
             			'discount_fix'=>0,
             			'note'=>$data['not'],
             			'start_date'=>$data['start_date'],
             			'validate'=>$data['end_date'],
             			'references'=>'frome registration',
             			'is_parent'		=>$payment_id_ser,
             			'is_complete'	=>$complete,
             			'comment'		=>$comment,
             			'user_id'=>$this->getUserId(),
             	);
             	$this->insert($arr);
			}
			 $db->commit();//if not errore it do....
		}catch (Exception $e){
			echo $e->getMessage();
			$db->rollBack();//អោយវាវិលត្រលប់ទៅដើមវីញពេលណាវាជួបErrore
		}
	}
    function getAllStudentRegister($search=null){
    	
    	$_db = new Application_Model_DbTable_DbGlobal;
    	$branch_id = $_db->getAccessPermission('sp.branch_id');
    	
    	$db=$this->getAdapter();
    	$sql=" SELECT
			    	sp.id,
			    	(select h.stu_code from rms_study_history as h where s.stu_id=h.stu_id and status = 1 limit 1) as stu_code,
			    	s.stu_khname,
			    	s.stu_enname,
			    	s.sex,
			    	(SELECT en_name FROM rms_dept WHERE dept_id=sp.degree LIMIT 1)AS degree,
			    	(SELECT major_enname FROM rms_major WHERE major_id=sp.grade LIMIT 1) AS grade,
			    	sp.receipt_number,
			    	sp.grand_total_payment,
			    	sp.grand_total_paid_amount,
			    	sp.grand_total_balance,
			    	sp.create_date,
			    	(select first_name from rms_users as u where u.id=sp.user_id LIMIT 1) as user,
			    	(select name_en from rms_view where type=12 and key_code = sp.is_void LIMIT 1) as void_status
			    FROM
			    	rms_student AS s,
			    	rms_student_payment AS sp
			   	WHERE sp.payfor_type IN (1,6)
			    	AND s.stu_id = sp.student_id
			    	AND s.stu_type IN (1, 2)
			    	AND sp.reg_from = 0
			    	$branch_id ";
    	
    	$where=" ";
    	
    	$from_date =(empty($search['start_date']))? '1': " sp.create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': " sp.create_date <= '".$search['end_date']." 23:59:59'";
    	$where = " AND ".$from_date." AND ".$to_date;
    	
    	if(!empty($search['adv_search'])){
    		$s_where=array();
    		$s_search=addslashes(trim($search['adv_search']));
    		$s_where[]= " stu_code LIKE '%{$s_search}%'";
    		$s_where[]=" receipt_number LIKE '%{$s_search}%'";
    		$s_where[]= " stu_khname LIKE '%{$s_search}%'";
    		$s_where[]= " stu_enname LIKE '%{$s_search}%'";
    		$s_where[]= " sp.grade LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    	if(!empty($search['degree_ft'])){
    		$where.=" AND sp.degree=".$search['degree_ft'];
    	}
    	if(!empty($search['study_year'])){
    		$where.=" AND sp.year=".$search['study_year'];
    	}
//     	if(!empty($search['time'])){
//     		$where.=" AND sp.time=".$search['time'];
//     	}
    	if(!empty($search['session'])){
    		$where.=" AND sp.session=".$search['session'];
    	}
    	if(!empty($search['grade_ft'])){
    		$where.=" AND sp.grade=".$search['grade_ft'];
    	}
    	if(!empty($search['user'])){
    		$where.=" AND sp.user_id=".$search['user'];
    	}
    	$order=" ORDER BY sp.id DESC";
    	return $db->fetchAll($sql.$where.$order);
    }
    function getRegisterById($id){
    	$db=$this->getAdapter();
    	$sql="SELECT 
				  s.stu_id,
				  s.stu_code,
				  s.stu_khname,
				  s.stu_enname,
				  s.sex,
				  s.dob,
				  s.tel,
				  s.address,
				  sp.branch_id,
				  sp.student_id,
				  sp.receipt_number,
				  sp.year as academic_year,
				  sp.session,
				  sp.degree,
				  sp.grade,
				  sp.room_id,
				  sp.buy_product,
				  sp.is_void,
				  sp.payment_term,
				  sp.price_per_sec,
				  sp.amount_sec,
				  sp.exchange_rate,
				  sp.tuition_fee,
				  sp.discount_percent,
				  sp.discount_fix,
				  sp.tuition_fee_after_discount,
				  sp.other_fee,
				  sp.admin_fee,
				  sp.material_fee,
				  sp.total_payment,
				  sp.paid_amount,
				  sp.balance_due,
				  sp.amount_in_khmer,
				  sp.note,
				  sp.student_type,
				  sp.time,
				  sp.end_hour,
				  spd.fee,
				  spd.start_date,
				  spd.validate,
				  spd.is_start,
				  spd.is_parent 
				FROM
				  rms_student AS s,
				  rms_student_payment AS sp,
				  rms_student_paymentdetail AS spd 
				WHERE s.stu_id = sp.student_id 
				  AND sp.id = spd.payment_id 
				  and spd.service_id = 4
				  AND sp.id =".$id;
    	
    	return $db->fetchRow($sql);
    }
    function getAllGrade($dept_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT major_id As id,major_enname As name FROM rms_major WHERE dept_id=".$dept_id;
    	$order=' AND is_active =1 ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    function getPaymentTerm($generat,$payment_term,$grade){
    	$db = $this->getAdapter();
    	$sql="SELECT tfd.id,tfd.tuition_fee FROM rms_tuitionfee AS tf,rms_tuitionfee_detail AS tfd WHERE tf.id = fee_id
    	 AND tfd.fee_id=$generat AND tfd.class_id=$grade AND tfd.payment_term=$payment_term LIMIT 1";
    	return $db->fetchRow($sql);
    }
    function getBalance($serviceid,$studentid,$type){
    	$db = $this->getAdapter();
//     	$sql="select rms_student_paymentdetail.id,rms_student_paymentdetail.validate,balance AS price_fee
//     	from rms_student_paymentdetail,rms_student_payment where rms_student_payment.id=rms_student_paymentdetail.payment_id
//     	and rms_student_paymentdetail.service_id=$serviceid and rms_student_payment.student_id=$studentid and is_complete=0 limit 1";
    	
    	$sql = "SELECT 
				  spd.id,
				  spd.start_date,
				  spd.validate,
				  spd.balance,
				  sp.year,
				  spd.payment_term,
				  sp.session
				FROM
				  rms_student_paymentdetail AS spd,
				  rms_student_payment AS sp
				WHERE sp.id = spd.payment_id 
				  AND spd.service_id = $serviceid 
				  AND sp.student_id = $studentid 
				  AND is_complete = 0 
				  AND spd.type = $type
    			limit 1
    		";
    	
    	$row=$db->fetchRow($sql);
    	if($row['balance'] > 0){
    	    $row['sms']='លុយជំពាក់ពីមុន';
    		return $row;
    	}
    }
   
    function getAllYearsProgramFee(){
    	$db = $this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('branch_id');
    	
    	$sql = "SELECT id,CONCAT(from_academic,'-',to_academic,'(',(SELECT branch_namekh FROM rms_branch WHERE br_id = branch_id),')') AS years,(select name_en from rms_view where type=7 and key_code=time) as time FROM rms_tuitionfee
    	        WHERE `status`=1 $branch_id GROUP BY from_academic,to_academic,generation,time,branch_id ";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    
    function getAllYears(){
    	$db = $this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('branch_id');
    	
    	$sql = "SELECT 
    				id,CONCAT(from_academic,'-',to_academic,'(',(SELECT branch_namekh FROM rms_branch WHERE br_id = branch_id),')') AS years , (select name_en from rms_view where type=7 and key_code=time) as time FROM rms_tuitionfee 
    			WHERE 
    				`status`=1 
    	        	$branch_id
    			GROUP BY 
    				from_academic,
    				to_academic,
    				generation,
    				time,
    				branch_id
    	";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    
    function getAllYearsServiceFee(){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,CONCAT(from_academic,'-',to_academic) AS years FROM rms_servicefee WHERE `status`=1";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
 
    public function getNewAccountNumber($degree,$branch){
    	$db = $this->getAdapter();
    	
    	if($branch>0){
    		$branch_id = $branch;
    	}else{
    		$branch_id = $this->getBranchId();
    	}
    	
    	$sql="SELECT COUNT(stu_id) FROM rms_student_id WHERE degree = $degree and  branch_id=$branch_id  LIMIT 1";
    	
    	$length = '';
    	$acc_no = $db->fetchOne($sql);
    	$new_acc_no= (int)$acc_no+1;

    	$length = strlen((int)$new_acc_no);
    	
    	$sql="SELECT shortcut FROM rms_dept WHERE dept_id=$degree LIMIT 1";
    	$shortcut=$db->fetchOne($sql);
    	$pre=$shortcut;
    	
    	
    	for($i = $length;$i<5;$i++){
    		$pre.='0';
    	}
    	return $pre.$new_acc_no;
    }
    public function getRecieptNo($payfor_type,$branch){
    	$db = $this->getAdapter();
    	
    	if($branch>0){
    		$branch_id = $branch;
    	}else{
    		$branch_id = $this->getBranchId();
    	}
    	
    	$sql="SELECT count(id) FROM rms_student_payment where payfor_type = $payfor_type and branch_id = $branch_id LIMIT 1 ";
    	$acc_no = $db->fetchOne($sql);
    	$new_acc_no= (int)$acc_no+1;
    	$acc_no= strlen((int)$acc_no+1);
    	
    	$pre="";
    	
    	if($payfor_type==1){
    		$pre="K";
    	}
    	else if($payfor_type==6){
    		$pre="FE";
    	}
    	else if($payfor_type==2){
    		$pre="PE";
    	}
    	else if($payfor_type==3){
    		$pre="TR";
    	}
    	else if($payfor_type==4){
    		$pre="F";
    	}
    	else if($payfor_type==5){
    		$pre="";
    	}
    	
    	for($i = $acc_no;$i<6;$i++){
    		$pre.='0';
    	}
    	return $pre.$new_acc_no;
    }
    
    
    public function getInvoiceNo($payfor_type,$branch){
    	$db = $this->getAdapter();
    	 
    	if($branch>0){
    		$branch_id = $branch;
    	}else{
    		$branch_id = $this->getBranchId();
    	}
    	 
    	$sql="SELECT count(id) FROM rms_invoice where payfor_type = $payfor_type and branch_id = $branch_id LIMIT 1 ";
    	$acc_no = $db->fetchOne($sql);
    	$new_acc_no= (int)$acc_no+1;
    	$acc_no= strlen((int)$acc_no+1);
    	 
    	$pre="";
    	 
    	if($payfor_type==1){
    		$pre="K";
    	}
    	else if($payfor_type==6){
    		$pre="FE";
    	}
    	else if($payfor_type==2){
    		$pre="Inc-PE";
    	}
    	else if($payfor_type==3){
    		$pre="Inc-TR";
    	}
    	else if($payfor_type==4){
    		$pre="F";
    	}
    	else if($payfor_type==5){
    		$pre="";
    	}
    	 
    	for($i = $acc_no;$i<6;$i++){
    		$pre.='0';
    	}
    	return $pre.$new_acc_no;
    }
    
    //select GEP all old student
    function getAllGepOldStudent(){
    	$db=$this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('s.branch_id');
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	$action = $request->getActionName();
    	if($action == "edit"){
    		$is_suspend = "";
    	}else{
    		$is_suspend = " and s.is_subspend=0 ";
    	}
    	
    	$sql="SELECT s.stu_id As stu_id,s.stu_code As stu_code FROM rms_student AS s
    	      WHERE s.stu_type=3 AND s.status=1 and reg_from=0 $is_suspend $branch_id ORDER BY stu_id DESC  ";
    	return $db->fetchAll($sql);
    }
    //select Gep old student by id 
    function getAllGepOldStudentName(){
    	$db=$this->getAdapter();
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	$action = $request->getActionName();
    	if($action == "edit"){
    		$is_suspend = "";
    	}else{
    		$is_suspend = " and s.is_subspend=0 ";
    	}
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('s.branch_id');
    	
    	$sql="SELECT s.stu_id As stu_id,CONCAT(s.stu_khname,'-',s.stu_enname) As name FROM rms_student AS s
    	WHERE s.stu_type=3 AND s.status=1 and reg_from=0 $is_suspend $branch_id ORDER BY stu_id DESC ";
    	return $db->fetchAll($sql);
    }
    //select Gep old student by name
    function getGepOldStudent($stu_id){
    	$db=$this->getAdapter();
    	$sql="SELECT * FROM rms_student 
    	       WHERE  stu_type=3 AND stu_id=$stu_id LIMIT 1";
    	return $db->fetchRow($sql);
    }
    //select all Gerneral old student
    function getAllGerneralOldStudent(){
    	$db=$this->getAdapter();
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	$action = $request->getActionName();
    	if($action == "edit"){
    		$is_suspend = "";
    	}else{
    		$is_suspend = " and s.is_subspend=0 ";
    	}
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('s.branch_id');
    	
    	$sql="SELECT 
    				s.stu_id,
    				s.stu_code 
    			FROM 
    				rms_student AS s
    			WHERE 
    				s.stu_type!=3 
    				AND s.status=1 
    				and reg_from=0 
    				$is_suspend 
    				$branch_id  
    			ORDER BY 
    				stu_id DESC 
    		";
    	
    	return $db->fetchAll($sql);
    }
    //select general  old student by id
    
    function getAllGerneralOldStudentName(){
    	$db=$this->getAdapter();
    	
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	$action = $request->getActionName();
    	if($action == "edit"){
    		$is_suspend = "";
    	}else{
    		$is_suspend = " and s.is_subspend=0 ";
    	}
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('s.branch_id');
    	
    	$sql="SELECT s.stu_id As stu_id,CONCAT(s.stu_enname,'-',s.stu_khname) as name FROM rms_student AS s
    	WHERE s.stu_type!=3 AND s.status=1 and reg_from=0 $is_suspend $branch_id  ORDER BY stu_id DESC ";
    	return $db->fetchAll($sql);
    }
    
    
    function getAllDropStudentName($type){
    	$db=$this->getAdapter();
    	 
//     	$request=Zend_Controller_Front::getInstance()->getRequest();
//     	$action = $request->getActionName();
//     	if($action == "edit"){
//     		$is_comeback = " ";
//     	}else{
//     		$is_comeback = " and s.is_comeback = 0 ";
//     	}
    	
    	if($type==1){
    		$stu_type = " AND s.stu_type != 3";
    	}else{
    		$stu_type = " AND s.stu_type = 3";
    	}
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('s.branch_id');
    	
    	$sql="SELECT 
    				s.stu_id As stu_id,
    				CONCAT(s.stu_enname,'-',s.stu_khname) as name 
    			FROM 
    				rms_student AS s
    			WHERE 
    				s.status=1 
    				and reg_from=0 
    				and s.is_subspend!=0 
    				$stu_type
    				$branch_id  
    			ORDER BY 
    				stu_id DESC 
    		";
    	return $db->fetchAll($sql);
    }
    
    
    function getAllDropStudentID($type){
    	$db=$this->getAdapter();
    
//     	$request=Zend_Controller_Front::getInstance()->getRequest();
//     	$action = $request->getActionName();
//     	if($action == "edit"){
//     		$is_comeback = " ";
//     	}else{
//     		$is_comeback = " and s.is_comeback = 0 ";
//     	}
    	
    	if($type==1){ 
    		$stu_type = " AND s.stu_type != 3";
    	}else{
    		$stu_type = " AND s.stu_type = 3";
    	}
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission('s.branch_id');
    
    	$sql="SELECT
			    	s.stu_id As stu_id,
			    	stu_code
			    FROM
			    	rms_student AS s
			    WHERE
			    	s.status=1
			    	and reg_from=0
			    	and s.is_subspend!=0
			    	$stu_type
			    	$branch_id
			    ORDER BY
			    	stu_id DESC
		    ";
    	
//     	echo $sql;//exit();
    	
    	return $db->fetchAll($sql);
    }
    
    //select general  old student by name
    
    function getGeneralOldStudentById($stu_id){
    	$db=$this->getAdapter();
    	$sql="SELECT * FROM rms_student
    	WHERE  stu_type IN (1,2) AND stu_id=$stu_id LIMIT 1";
    	return $db->fetchRow($sql);
    }
    ///select degree searching 
    function getDegree(){
    	$db=$this->getAdapter();
    	$sql="SELECT dept_id AS id,CONCAT(en_name,'-',kh_name) AS `name` FROM rms_dept  WHERE dept_id  IN(1,2,3,4)";
    	return $db->fetchAll($sql);
    }
    //function add rms_student_detailpayment
    function addStudentPaymentDetail($data,$type,$paymentid,$complete,$comment,$payment_id_ser){
    	$db=$this->getAdapter();
    	    if($type==4){
    	    	$fee = $data["tuitionfee"];
    	    	$subtotal=$data["tuitionfee"]-($data["tuitionfee"]*$data['discount']/100);
    	    	$discount=$data['discount'];
    	    	$paidamount=$data['books'];
    	    	$paidamount=$paidamount-($data["remark"]+$data["addmin_fee"]);
    	    	$balance= $data['total'] - $data['books'];
    	    }elseif($type==5){
    	    	$fee = $data["remark"];
    	    	$subtotal = $data["remark"];
    	    	$paidamount = $data['remark'];
    	    	$discount = 0;
    	    	$balance = 0;
    	    	$comment="បង់រួច";
    	    }elseif($type==6){
    	    	$fee = $data["addmin_fee"];
    	    	$subtotal = $data["addmin_fee"];
    	    	$paidamount=$data['addmin_fee'];
    	    	$discount=0;
    	    	$balance=0;
    	    	$comment="បង់រួច";
    	    }
    		$arr=array(
    				'payment_id'=>$paymentid,
    				'type'=>1,
    				'service_id'=>$type,
    				'payment_term'=>$data['payment_term'],
    				'fee'=>$fee,
    				'qty'=>1,
    				//'subtotal'=>$data['total'],
    				'subtotal'=>$subtotal,
    				'paidamount'=>$paidamount,
    				'balance'=>$balance,
    				'discount_percent'=>$discount,
    				'discount_fix'=>0,
    				'note'=>$data['not'],
    				'start_date'=>$data['start_date'],
    				'validate'=>$data['end_date'],
    				'references'=>'frome registration',
    				'is_parent'		=>$payment_id_ser,
    				'is_complete'	=>$complete,
    				'comment'		=>$comment,
    				'user_id'=>$this->getUserId(),
    		);
    		
    		$this->insert($arr);
    }
    
    function getGradeAllDept(){
    	$db=$this->getAdapter();
    	$sql="SELECT 
    				major_id AS id,
    				CONCAT(major_enname,' (',d.shortcut,')') AS `name`
    			FROM
    				rms_major AS m,
    				rms_dept AS d
    			WHERE 
    				d.dept_id = m.dept_id	
    				AND m.is_active=1
    				AND m.major_enname != ''
    		";
    	return $db->fetchAll($sql);
    }
    
    function getGradeAll(){
    	$db=$this->getAdapter();
    	$sql="SELECT major_id AS id,major_enname AS `name` FROM rms_major WHERE dept_id IN(1,2,3,4) AND is_active=1 ";
    	return $db->fetchAll($sql);
    }
    
    function getAllDegree(){
    	$db=$this->getAdapter();
    	$sql="SELECT dept_id AS id,en_name AS `name` FROM rms_dept WHERE is_active=1 and en_name!='' ";
    	return $db->fetchAll($sql);
    }
    
    function getAllDegreeKhmer(){
    	$db=$this->getAdapter();
    	$sql="SELECT dept_id AS id,en_name AS `name` FROM rms_dept WHERE type=1 AND is_active=1 and en_name!='' ";
    	return $db->fetchAll($sql);
    }
    
    function getAllDegreeFulltime(){
    	$db=$this->getAdapter();
    	$sql="SELECT dept_id AS id,en_name AS `name` FROM rms_dept WHERE type IN(1,2) AND is_active=1 and en_name!='' ";
    	return $db->fetchAll($sql);
    }
    
    function getAllDegreeEnglishFulltime(){
    	$db=$this->getAdapter();
    	$sql="SELECT dept_id AS id,en_name AS `name` FROM rms_dept WHERE type=2 AND is_active=1 and en_name!='' ";
    	return $db->fetchAll($sql);
    }
    
    function getAllDegreeGEP(){
    	$db=$this->getAdapter();
    	$sql="SELECT dept_id AS id,en_name AS `name` FROM rms_dept WHERE type=3 AND is_active=1 and en_name!=''  ";
    	return $db->fetchAll($sql);
    }
   
    function getAllGradeFulltime(){
    	$db=$this->getAdapter();
    	$sql="SELECT 
    				major_id AS id,
    				CONCAT(major_enname,' (',d.shortcut,')') AS `name`
    			FROM
    				rms_major AS m,
    				rms_dept AS d
    			WHERE 
    				d.dept_id = m.dept_id	
    				AND m.is_active=1
    				AND m.major_enname != ''
    				AND d.`type` IN(1,2)
    		";
    	return $db->fetchAll($sql);
    }
    
    function getGradeEnglishFulltime(){
    	$db=$this->getAdapter();
    	$sql="SELECT 
    				major_id AS id,
    				CONCAT(major_enname,' (',d.shortcut,')') AS `name`
    			FROM
    				rms_major AS m,
    				rms_dept AS d
    			WHERE 
    				d.dept_id = m.dept_id	
    				AND m.is_active=1
    				AND m.major_enname != ''
    				AND d.`type`=2
    		";
    	
    	return $db->fetchAll($sql);
    }
    
    function getGradeKhmerFulltime(){
    	$db=$this->getAdapter();
    	$sql="SELECT 
    				major_id AS id,
    				CONCAT(major_enname,' (',d.shortcut,')') AS `name`
    			FROM
    				rms_major AS m,
    				rms_dept AS d
    			WHERE 
    				d.dept_id = m.dept_id	
    				AND m.is_active=1
    				AND m.major_enname != ''
    				AND d.`type`=1
    		";
    	
    	return $db->fetchAll($sql);
    }
    
    function getGradeAllKid(){
    	$db=$this->getAdapter();
    	$sql="SELECT major_id AS id,major_enname AS `name` FROM rms_major WHERE dept_id =1  AND is_active=1 ";
    	return $db->fetchAll($sql);
    }
    
    function getAllUser(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,CONCAT(last_name,' - ',first_name) as name FROM rms_users WHERE active=1 order by id desc ";
    	return $db->fetchAll($sql);
    }
    
    function getGradeGepAll(){
    	$db=$this->getAdapter();
    	$sql="SELECT 
    				major_id AS id,
    				CONCAT(major_enname,' (',d.shortcut,')') AS `name`
    			FROM
    				rms_major AS m,
    				rms_dept AS d
    			WHERE 
    				d.dept_id = m.dept_id	
    				AND m.is_active=1
    				AND m.major_enname != ''
    				AND d.`type` = 3
    		 ";
    	return $db->fetchAll($sql);
    }
    function getAllGrades(){
    	$db=$this->getAdapter();
    	$sql="SELECT major_id AS id,major_enname AS `name` FROM rms_major WHERE is_active=1";
    	return $db->fetchAll($sql);
    }
    function getServicesAll(){
    	$db=$this->getAdapter();
    	$sql="SELECT service_id AS id,title FROM rms_program_name WHERE `type` = 2 and status=1";
    	return $db->fetchAll($sql);
    }
    
    function getTransportService(){
    	$db=$this->getAdapter();
    	$sql="SELECT service_id AS id,title FROM rms_program_name WHERE `type` = 2 and status=1 and ser_cate_id = 3 ";
    	return $db->fetchAll($sql);
    }
    
    function getLunchService(){
    	$db=$this->getAdapter();
    	$sql="SELECT service_id AS id,title FROM rms_program_name WHERE `type` = 2 and status=1 and ser_cate_id = 2 ";
    	return $db->fetchAll($sql);
    }
    
    function getAllServiceAndProduct(){
    	$db=$this->getAdapter();
    	$sql="SELECT service_id AS id,title FROM rms_program_name WHERE status=1 ";
    	return $db->fetchAll($sql);
    }
    
   
    function getAllGradeGEP($dept_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT CONCAT(major_enname) As name,major_id As id FROM rms_major WHERE dept_id = ".$dept_id;
    	$order=' AND is_active =1 ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    
    function getStartDate($service_id , $stu_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT spd.validate from rms_student_payment as sp,rms_student_paymentdetail as spd 
    			where sp.student_id = $stu_id and spd.service_id = $service_id and spd.is_complete=1 and spd.is_start=1 AND sp.`id`=spd.`payment_id` and sp.is_void=0 ";
    	$order=' ORDER BY spd.id DESC';
    	return $db->fetchOne($sql.$order);
    }
    
    function getAllProduct(){
    	$db=$this->getAdapter();
    	$sql=" select service_id as id , title as name from rms_program_name where type=1 and status=1 ";
    	return $db->fetchAll($sql);
    }
    
    function getProductPrice($product){
    	$db=$this->getAdapter();
    	$sql=" select price from rms_program_name where type=1 and status=1 and service_id = $product ";
    	return $db->fetchOne($sql);
    }
    
    function getExchangeRate(){
    	$db=$this->getAdapter();
    	$sql=" select reil from rms_exchange_rate where active=1 ";
    	return $db->fetchOne($sql);
    }
    
    function getAllRoom(){
    	$db=$this->getAdapter();
    	$sql=" select room_id as id,room_name as name from rms_room where is_active=1 ";
    	return $db->fetchAll($sql);
    }
    
    function getAllBranch(){
    	$db=$this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch = $_db->getAccessPermission("br_id");
    	
    	$sql=" select br_id as id,branch_namekh as name from rms_branch where status=1 $branch ";
    	return $db->fetchAll($sql);
    }
    
    function getDegreeType($degree){
    	$db=$this->getAdapter();
    	$sql=" select type from rms_dept where is_active=1 and dept_id = $degree ";
    	return $db->fetchOne($sql);
    }
    
    
}

