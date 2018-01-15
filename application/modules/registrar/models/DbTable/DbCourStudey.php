<?php

class Registrar_Model_DbTable_DbCourStudey extends Zend_Db_Table_Abstract
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
    	sp.id=spd.payment_id and is_start=1 and spd.service_id= $service_id and sp.student_id=$studentid and spd.type=$type limit 1 ";
//     	echo $sql;exit();
    	return $db->fetchOne($sql);
    }
	function addStudentGep($data){
// 		print_r($data);exit();
		$db = $this->getAdapter();//ស្ពានភ្ជាប់ទៅកាន់Data Base
		$db->beginTransaction();//ទប់ស្កាត់មើលការErrore , មានErrore វាមិនអោយចូល
		
		$register = new Registrar_Model_DbTable_DbRegister();
		$stu_code = $register->getNewAccountNumber($data['dept'],0);
		$receipt = $register->getRecieptNo(2,0);

// 		$stu_code=$data['stu_id'];
// 		$receipt=$data['reciept_no'];
		
		if($data['dob']==""){
			$dob = null;
		}else{
			$dob = $data['dob'];
		}
		
		//print_r($this->getBranchId());exit();
		try{
			if($data['student_type']==1){ // new student
				$this->_name="rms_student";
				$arr=array(
						'branch_id'		=>$this->getBranchId(),
						'stu_code'		=>$stu_code,
						'academic_year'	=>$data['study_year'],
						'stu_khname'	=>$data['kh_name'],
						'stu_enname'	=>$data['en_name'],
							
						'dob'			=>$dob,
						'tel'			=>$data['phone'],
						'address'		=>$data['address'],
							
						'session'		=>$data['session'],
						'sex'			=>$data['sex'],
						'degree'		=>$data['dept'],
						'grade'			=>$data['grade'],
						'room'			=>$data['room'],
						
						'is_stu_new' 	=>1,
						'stu_type'		=>3,
						'create_date'	=>date("Y-m-d H:i:s"),
						'user_id'		=>$this->getUserId(),
				);
				$id= $this->insert($arr);
					
			}else { // old or drop student
				// សិក្សាប្រសិនបើប្តូរ រឺ ឡើងកម្រិត Generate new stu_code ឲ្យ , else stu_code នៅដដែល
				if($data['old_degree']==$data['dept']){
					$stu_code = $data['old_stu_code'];
				}else{
					$stu_code = $register->getNewAccountNumber($data['dept'],0);
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
				
				$arr = array(
						'stu_code'	=>$stu_code,
						'session'	=>$data['session'],
						'degree'	=>$data['dept'],
						'grade'		=>$data['grade'],
						'room'		=>$data['room'],
						'stu_khname'=>$data['kh_name'],
						'stu_enname'=>$data['en_name'],
						'sex'		=>$data['sex'],
						'tel'		=>$data['phone'],
						'address'	=>$data['address'],
						'dob'		=>$dob,
						'academic_year'=>$data['study_year'],
						'stu_type'	=>3,
						'is_stu_new' =>$is_stu_new,
						'is_subspend'=>0,
						'is_comeback'=>$is_comeback,
				);
				$where = ' stu_id = '.$id;
				$this->update($arr, $where);
			}
				
			
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
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
					'student_id'=>$id,
					'receipt_number'=>$receipt,
					
					'buy_product'=>$buy_product,
					
					'year'		=>$data['study_year'],
					'degree'	=>$data['dept'],
					'grade'		=>$data['grade'],
					'session'	=>$data['session'],
					'time'		=>$data['study_time'],
					
					'payment_term'=>$data['payment_term'],
					'price_per_sec'	=>$price_per_sec,
					'amount_sec'=>$amount_sec,
					
					'exchange_rate'		=>$data['ex_rate'],
					'tuition_fee'		=>$tuitionfee,
					'discount_percent'	=>$data['discount'],
					'discount_fix'		=>$data['discount_fix'],
					'tuition_fee_after_discount'=>($tuitionfee - $data['discount_fix']) - (($tuitionfee - $data['discount_fix'])*($data['discount']/100)),
					'other_fee'			=>$data['remark'],
// 					'admin_fee'			=>$data['addmin_fee'],
// 					'material_fee'		=>$data['material_fee'],
					'total_payment'		=>$data['total'],
					'paid_amount'		=>$data['books'],
					'receive_amount'	=>$data['books'],
					'balance_due'		=>$data['remaining'],
					
					
					'grand_total_payment'			=>$data['grand_total'],
					'grand_total_payment_in_riel'	=>$data['convert_to_riels'],
					'grand_total_paid_amount'		=>$data['total_received'],
					'grand_total_balance'			=>$data['total_balance'],
					
					'is_new'		=>$is_new,
					'note'			=>$data['not'],
					'room_id'		=>$data['room'],
					'student_type'	=>$data['student_type'],
					'create_date'	=>	date('Y-m-d H:i:s'),
					'payfor_type'	=>2,
					'user_id'		=>$this->getUserId(),
					'branch_id'		=>$this->getBranchId(),
			);
			$paymentid=$this->insert($arr);
			
				
		////////////////////  rms_study_history  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
			$this->_name='rms_study_history';
			if($data['student_type']==1){
			
				$arr=array(
						'stu_id'		=>$id,
						'stu_type'		=>3,
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
			
						
					if($data['old_degree']==$data['dept']){
						$stu_code = $data['old_stu_code'];
					}else{
						$stu_code = $register->getNewAccountNumber($data['dept'],0);
					}
			
					$arr=array(
							'stu_id'		=>$id,
							'stu_type'		=>3,
							'stu_code'		=>$stu_code,
							'academic_year'	=>$data['study_year'],
							'degree'		=>$data['dept'],
							'grade'			=>$data['grade'],
							'room'			=>$data['room'],
							
							'session'		=>$data['session'],
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
			
				$stu_type=3; // parttime student (gep)
			
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
			
					$stu_type=3; // parttime student (gep)
			
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
			$type=2; 	  // លេខ 2 ជាប្រភេទសិស្ស GEP
			$expired_record_id = $this->getStudentPaymentStart($id,$service,$type);
			if(empty($expired_record_id)){
				$expired_record_id=0;
			}
			$where=" id = $expired_record_id ";
			$arr = array(
					'is_start'=>0
			);
			$this->update($arr,$where);
			//update is_complet = 1 becuse for get balance price
			if(!empty($data['ids'])){
				$this->updateIsComplete($data['ids']);
			}
			$complete=1;
			if($data['remaining']>0){
				$complete=0;
				$comment="មិនទាន់បង់";
			}else{
				$complete=1;
				$comment="បង់រួច";
			}
			//add rms_student_paymentdetail 3 service (tuttionfee,remake,admin_fee)		
			if($data){
				$type=4;
				$fee=$data['tuitionfee'];
					
				if($data['payment_term']==5){
					$qty=$data['amount_section'];
				}else{
					$qty=1;
				}
// 				$subtotal=$data["tuitionfee"]-($data["tuitionfee"]*$data['discount']/100);
// 				$subtotal = $subtotal + $data["remark"]+$data["addmin_fee"];
					
				$subtotal = $data["total"];
				
				$discount=$data['discount'];
				$paidamount=$data['books'];
					
// 				$paidamount=$paidamount-($data["remark"]+$data["addmin_fee"]);
// 				$balance= $subtotal - $data['books'];
					
				$balance = $data['remaining'];
					
				$arr=array(
						'payment_id'=>$paymentid,
						'type'=>2,
						'service_id'=>$type,
						'payment_term'=>$data['payment_term'],
						'fee'=>$fee,
						'qty'=>$qty,
						
						'discount_percent'=>$discount,
						'discount_fix'	=>$data['discount_fix'],
						
// 						'admin_fee'=>$data['addmin_fee'],
// 						'material_fee'	=>$data['material_fee'],
						'other_fee'=>$data['remark'],
						
						'subtotal'=>$subtotal,
						'paidamount'=>$paidamount,
						'balance'=>$balance,
							
						'note'=>$data['not'],
						'start_date'=>$data['start_date'],
						'validate'=>$data['end_date'],
						'references'=>'frome registration',
						'is_parent'		=>$expired_record_id,
						'is_complete'	=>$complete,
						'comment'		=>$comment,
						'user_id'=>$this->getUserId(),
				);
				$this->_name='rms_student_paymentdetail';
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
		}
	}
	
	
	function updateStudentGep($data){
// 		print_r($data);exit();
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
		
				//////////////////////// study history ///////////////////////////
		
				$this->_name='rms_study_history';
		
				$sql="select id,id_record_finished from rms_study_history where payment_id = ".$data['payment_id']." and stu_id = ".$data['old_studens'] ;
				$result = $db->fetchRow($sql);
				if(!empty($result['id_record_finished'])){
						
					$sql1 = "select * from rms_study_history where id = ".$result['id_record_finished'];
					$row = $db->fetchRow($sql1);
					if(!empty($row)){
		
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
						
					$this->_name='rms_study_history';
					$array = array(
							'is_finished_grade'=>0,
							'is_finished_degree'=>0,
					);
					$where = " id = ".$result['id_record_finished'];
					$this->update($array, $where);
						
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
		
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		
		try{
			//សំរាប់ update សិស្សចាស់ is start = 1 វិញ
			if($data['parent_id']!=0){
				if($data['stus_id']!=$data['old_studens']){
					$arr = array(
							'is_start'=>1
					);
					$this->_name='rms_student_paymentdetail';
					$where=" id = " .$data['parent_id'];
					$this->update($arr,$where);
				}
			}
			$this->_name = "rms_student";
		    if($data['student_type']==3){
		    	$id=$data['old_studens'];
		    	$arr = array(
		    			'session'	=>$data['session'],
		    			'degree'	=>$data['dept'],
		    			'grade'		=>$data['grade'],
		    			'academic_year'=>$data['study_year'],
		    	);
		    	$where = ' stu_id = '.$id;
		    	$this->update($arr, $where);
		    }else{  
		    	$arr=array(
		    			'stu_code'=>$data['stu_id'],
		    			'academic_year'=>$data['study_year'],
		    			'stu_khname'=>$data['kh_name'],
		    			'stu_enname'=>$data['en_name'],
		    			'session'=>$data['session'],
		    			'sex'=>$data['sex'],
		    			'degree'=>$data['dept'],
		    			'grade'=>$data['grade'],
		    			'stu_type'=>2,
		    			'user_id'=>$this->getUserId(),
		    	);
		    	$where=" stu_id=".$data['stus_id'];
		    	$this->update($arr, $where);
		    	
		    	$this->_name='rms_study_history';
		    	$array=array(
		    			'stu_code'	=>$data['stu_id'],
		    			'stu_type'	=>2,
		    			'degree'	=>$data['dept'],
		    			'grade'		=>$data['grade'],
		    			'session'	=>$data['session'],
						'from_time'	=>$data['study_time'],
		    			'remark'	=>$data['not'],
		    			//'start_date'=>$data['start_date'],
		    			'user_id'	=>$this->getUserId(),
		    	);
		    	$where="stu_id=".$data['stus_id'];
		    	$this->update($array, $where);
		    	
		    }
			    
		    if($data['payment_term']==5){
		    	$price_per_sec = $data['price_per_section'];
		    	$amount_sec = $data['amount_section'];
		    }else{
		    	$price_per_sec = null;
		    	$amount_sec = null;
		    }
			    
		    $this->_name='rms_student_payment';
		    $arr=array(
		    		'student_id'=>$data['old_studens'],
		    		'receipt_number'=>$data['reciept_no'],
		    		'year'=>$data['study_year'],
		    		'degree'=>$data['dept'],
		    		'grade'=>$data['grade'],
		    		'time'=>$data['study_time'],
		    		'session'=>$data['session'],
		    		'payment_term'=>$data['payment_term'],
		    		'price_per_sec'=>$price_per_sec,
		    		'amount_sec'=>$amount_sec,
		    		
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
		    		'room_id'=>$data['room'],
		    		'payfor_type'=>2,
		    		'user_id'=>$this->getUserId(),
		    );
		    $where=" id = ".$data['payment_id'];
		    $this->update($arr, $where);
			    
			    
			$this->_name='rms_student_paymentdetail';
		//update is_start=0 ដើម្បីអោយដឹងថា Service និង ឈប់ប្រើ រឺ  expired នៅពេលដែលសិស្សចាស់បង់លុយម្តងទៀត រួច store ID record that updated in is_parent of new record
			$service = 4; // លេខ 4 ជាសេវាកម្មចុះឈ្មោះចូលរៀន
			$type=2; 	  // លេខ 2 ជាប្រភេទសិស្ស GEP
		    $expired_record_id = $this->getStudentPaymentStart($data['old_studens'],$service,$type);
		    if(empty($expired_record_id)){
		    	$expired_record_id=0;
		    }
		    $where=" id = $expired_record_id ";
		    $arr = array(
		    		'is_start'=>0
		    );
		    $this->update($arr,$where);
		    
		    //update is_complet = 1 becuse for get balance price
		    if(!empty($data['ids'])){
		    	$this->updateIsComplete($data['ids']);
		    }
		    $complete=1;
		    if($data['remaining']>0){
		    	$complete=0;
		    	$comment="មិនទាន់បង់";
		    }else{
		    	$complete=1;
		    	$comment="បង់រួច";
		    }
		    //update rms_student_paymentdetail 3 service (tuttionfee,remake,admin_fee)	
		    $this->_name='rms_student_paymentdetail';
		    $where="payment_id=".$data['payment_id'];
		    $this->delete($where);
		    if($data){
		    	$paymentid=$data['payment_id'];
		    	$type=4; // tuition_fee service
				$fee=$data['tuitionfee'];
				$subtotal=$data["tuitionfee"]-($data["tuitionfee"]*$data['discount']/100);
				$subtotal = $subtotal + $data["remark"]+$data["addmin_fee"];
				$discount=$data['discount'];
				$paidamount=$data['books'];
				//$paidamount=$paidamount-($data["remark"]+$data["addmin_fee"]);
				$balance= $subtotal - $data['books'];
				$arr=array(
						'payment_id'=>$paymentid,
						'type'=>2,
						'service_id'=>$type,
						'payment_term'=>$data['payment_term'],
						'fee'=>$fee,
						'qty'=>1,
						'subtotal'=>$subtotal,
						'paidamount'=>$paidamount,
						'balance'=>$balance,
						'discount_percent'=>$discount,
						'discount_fix'=>0,
						'note'=>$data['not'],
						'start_date'=>$data['start_date'],
						'validate'=>$data['end_date'],
						'references'=>'frome registration',
						'is_parent'		=>$expired_record_id,
						'is_complete'	=>$complete,
						'comment'		=>$comment,
						//'user_id'=>$this->getUserId(),
				);
				$this->_name='rms_student_paymentdetail';
				$this->insert($arr);
		    }
                //exit();
		     $db->commit();//if not errore it do....
		}catch (Exception $e){
			$db->rollBack();//អោយវាវិលត្រលប់ទៅដើមវីញពេលណាវាជួបErrore
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
    function getAllStudentGep($search=null){
    	$db=$this->getAdapter();
    	$from_date =(empty($search['start_date']))? '1': " sp.create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': " sp.create_date <= '".$search['end_date']." 23:59:59'";
    	$where = " AND ".$from_date." AND ".$to_date;
    	
    	$_db = new Application_Model_DbTable_DbGlobal;
    	$branch_id = $_db->getAccessPermission('sp.branch_id');
    	
    	$sql=" SELECT 
				  sp.id,
				  (select h.stu_code from rms_study_history as h where s.stu_id=h.stu_id limit 1) as stu_code,
				  s.stu_khname,
				  s.stu_enname,
				  s.sex,
				  (SELECT en_name FROM rms_dept WHERE dept_id=sp.degree LIMIT 1)AS degree,
				  (SELECT CONCAT(major_enname) FROM rms_major WHERE major_id=sp.grade LIMIT 1) AS grade,
				  sp.receipt_number,
				  sp.grand_total_payment,
			      sp.grand_total_paid_amount,
			      sp.grand_total_balance,
				  sp.create_date,
				  (select (first_name) from rms_users as u where u.id=sp.user_id LIMIT 1) as user,
				  (select name_en from rms_view where type=12 and key_code = sp.is_void LIMIT 1) as void_status 
				FROM
				  rms_student AS s,
				  rms_student_payment AS sp 
				WHERE s.stu_id = sp.student_id 
				  AND s.stu_type = 3 
				  AND sp.payfor_type = 2 
				  and s.reg_from=0 
				  $branch_id ";
    	
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
    	if(!empty($search['degree_gep'])){
    		$where.=" AND sp.degree=".$search['degree_gep'];
    	}
    	if(!empty($search['study_year'])){
    		$where.=" AND sp.year=".$search['study_year'];
    	}
    	if(!empty($search['sess_gep'])){
    		$where.=" AND sp.time=".$search['sess_gep'];
    	}
    	if(!empty($search['grade_gep'])){
    		$where.=" AND sp.grade=".$search['grade_gep'];
    	}
    	if(!empty($search['user'])){
    		$where.=" AND sp.user_id=".$search['user'];
    	}
    	$order=" ORDER By id DESC ";
        //echo $sql.$where.$order;
    	return $db->fetchAll($sql.$where.$order);
    }
    function getStuentGepById($id){
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
				  sp.receipt_number,
				  sp.year as academic_year,
				  sp.session,
				  sp.time,
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
    	$pay=$payment_term;
    	$db = $this->getAdapter();
    	$sql="SELECT tf.id,tf.tuition_fee FROM rms_tuitionfee_detail AS tf,rms_tuitionfee As t
    	 	  WHERE tf.fee_id=t.id AND tf.fee_id=$study_year AND tf.class_id=$levele AND tf.payment_term=$pay LIMIT 1";
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
    function getBalance($serviceid,$studentid){
    	$db = $this->getAdapter();
    	$sql="select rms_student_paymentdetail.id,rms_student_paymentdetail.validate,balance AS price_fee
    	from rms_student_paymentdetail,rms_student_payment where rms_student_payment.id=rms_student_paymentdetail.payment_id
    	and rms_student_paymentdetail.service_id=$serviceid and rms_student_payment.student_id=$studentid and is_complete=0 limit 1";
    	$row=$db->fetchRow($sql);
    	if($row['price_fee'] > 0){
    		$row['sms']='លុយជំពាក់ពីមុន';
    		return $row;
    	}
    }
    function updateIsComplete($id){
    	$db = $this->getAdapter();
    	$where="id=".$id;
    	$arr = array(
    			'is_complete'=>1,
    			'comment'=>"បង់រួច",
    	);
    	$this->_name='rms_student_paymentdetail';
    	$this->update($arr,$where);
    }
    function getDegree(){
    	$db=$this->getAdapter();
    	$sql="SELECT dept_id AS id,en_name AS `name`  FROM rms_dept WHERE dept_id NOT IN(1,2,3,4)";
    	return $db->fetchAll($sql);
    }
    
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
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
    
}

