<?php

class Accounting_Model_DbTable_DbRegister extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_student';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    
    public function getBranchId(){
    	$session_user = new Zend_Session_Namespace('auth');
    	return $session_user->branch_id;
    }
    
    
    function getAllStudentRegister($search=null){
    	 
    	$_db = new Application_Model_DbTable_DbGlobal;
    	$branch_id = $_db->getAccessPermission('s.branch_id');
    	
    	$db=$this->getAdapter();
    	$sql=" SELECT
			    	sp.id,
			    	(select branch_namekh from rms_branch where br_id = s.branch_id) as branch,
			    	(select h.stu_code from rms_study_history as h where s.stu_id=h.stu_id) as stu_code,
			    	s.stu_khname,
			    	s.stu_enname,
			    	s.sex,
			    	(SELECT en_name FROM rms_dept WHERE dept_id=sp.degree)AS degree,
			    	(SELECT major_enname FROM rms_major WHERE major_id=sp.grade ) AS grade,
			    	sp.receipt_number,
			    	sp.grand_total_payment,
			    	sp.grand_total_paid_amount,
			    	sp.grand_total_balance,
			    	sp.create_date,
			    	(select CONCAT(first_name,' ',last_name) as name from rms_users where id = s.user_id) as user,
			    	(select name_en from rms_view where type=12 and key_code = sp.is_void) as void_status
			    FROM
			    	rms_student AS s,
			    	rms_student_payment AS sp
			    WHERE sp.payfor_type IN (1,6)
			    	AND s.stu_id = sp.student_id
			    	AND s.stu_type IN (1,2)
			    	and s.status=1
    		";
    	 
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
    	
    	if(!empty($search['branch'])){
    		$where.=" AND sp.branch_id=".$search['branch'];
    	}
    	
    	if(!empty($search['degree_ft'])){
    		$where.=" AND sp.degree=".$search['degree_ft'];
    	}
    	if(!empty($search['study_year'])){
    		$where.=" AND sp.year=".$search['study_year'];
    	}
    	if(!empty($search['session'])){
    		$where.=" AND sp.session=".$search['session'];
    	}
    	if(!empty($search['grade_ft'])){
    		$where.=" AND sp.grade=".$search['grade_ft'];
    	}
    	if(!empty($search['user'])){
    		$where.=" AND sp.user_id=".$search['user'];
    	}
    	//$order=" ORDER By stu_id DESC ";
    	$order=" ORDER BY sp.id DESC";
    	//echo $sql.$where.$order;exit();
    	return $db->fetchAll($sql.$where.$order);
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
		
		$register = new Registrar_Model_DbTable_DbRegister();
		$stu_code = $register->getNewAccountNumber($data['dept'],$data['branch']);
		
		if($data['dept']<=3){
			$type = 1; // khmer fulltime
		}else{
			$type = 6; // English fulltime
		}
		$receipt = $register->getRecieptNo($type,$data['branch']);
		
		$db = $this->getAdapter();//ស្ពានភ្ជាប់ទៅកាន់Data Base
		$db->beginTransaction();//ទប់ស្កាត់មើលការErrore , មានErrore វាមិនអោយចូល
			try{
				if($data['student_type']==1){//new
					$this->_name = "rms_student";
						
					if($data['dept']<=3){
						$stu_type=1;	 // khmer fulltime
					}else{
						$stu_type=2;	// english fulltime
					}
						
					$arr=array(
							'stu_code'		=>$stu_code,
							'academic_year'	=>$data['study_year'],
							'stu_khname'	=>$data['kh_name'],
							'stu_enname'	=>$data['en_name'],
							'sex'			=>$data['sex'],
					
							'dob'			=>$data['dob'],
							'tel'			=>$data['phone'],
							'address'		=>$data['address'],
					
							'session'		=>$data['session'],
							'degree'		=>$data['dept'],
							'grade'			=>$data['grade'],
							'room'			=>$data['room'],
					
							'is_stu_new' 	=>1,
							'stu_type'		=>$stu_type,
							'create_date'	=>date('Y-m-d H:i:s'),
							'user_id'		=>$this->getUserId(),
							'branch_id'		=>$data['branch'],
							'reg_from'		=>1,
					);
					$id= $this->insert($arr);
				}else {// old or drop
						
					$this->_name = "rms_student";
						
					if($data['dept']<=3){
						$stu_type=1;  // khmer fulltime
					}else{
						$stu_type=2;  // english fulltime
					}
					
					// សិក្សាប្រសិនបើប្តូរ រឺ ឡើងកម្រិត Generate new stu_code ឲ្យ , else stu_code នៅដដែល
					if($data['old_degree']==$data['dept']){
						$stu_code = $data['old_stu_code'];
					}else{
						$stu_code = $register->getNewAccountNumber($data['dept'],$data['branch']);
					}
					////////////////////////////////////////////////////////////////////////
					
					// update student information to grade that input
					if($data['student_type']==3){
						$id = $data['old_studens'];
						$is_comeback = 0;
					}else{
						$id = $data['drop_studens'];
						$is_comeback = 1;
					}
					
					$arr = array(
							'stu_code'	=>$stu_code,
							'session'	=>$data['session'],
							'degree'	=>$data['dept'],
							'grade'		=>$data['grade'],
							'room'		=>$data['room'],
							'academic_year'=>$data['study_year'],
							'stu_type'	=>$stu_type,
							
							'is_subspend'=>0,
							'is_stu_new' =>0,
							'is_comeback'=>$is_comeback,
					);
					$where = ' stu_id = '.$id;
					$this->update($arr, $where);
				}
				
				
				if($data['payment_term']==5){
					$price_per_sec = $data['price_per_section'];
					$amount_sec = $data['amount_section'];
				}else{
					$price_per_sec = null;
					$amount_sec = null;
				}
				
				if(!empty($data['buy_product'])){
					$buy_product = 1;
				}else{
					$buy_product = 0;
				}
				
				if($data['student_type']==3){
					$is_new = 0;
				}else{
					$is_new = 1;
				}
				
				$this->_name='rms_student_payment';
				$arr=array(
						'student_id'	=>$id,
						'receipt_number'=>$receipt,
						
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
						
						'tuition_fee'	=>$data['tuitionfee'],
						'discount_percent'=>$data['discount'],
						'other_fee'		=>$data['remark'],
						'admin_fee'		=>$data['addmin_fee'],
						'total'			=>$data['total'],
						'total_payment'	=>$data['tuitionfee']-($data['tuitionfee']*($data['discount']/100)),
						'paid_amount'	=>$data['books'],
						'receive_amount'=>$data['books'],
						'balance_due'	=>$data['remaining'],
						'note'			=>$data['not'],
						
						'grand_total_payment'			=>$data['grand_total'],
						'grand_total_payment_in_riel'	=>$data['convert_to_riels'],
						'grand_total_paid_amount'		=>$data['total_received'],
						'grand_total_balance'			=>$data['total_balance'],
						
						'student_type'	=>$data['student_type'],
						'create_date'	=>date('Y-m-d H:i:s'),
						'payfor_type'	=>$type,
						
						'is_new'		=>$is_new,
						
						//'amount_in_khmer'=>$data['char_price'],
						'user_id'		=>$this->getUserId(),
						'branch_id'		=>$data['branch'],
						'reg_from'		=>1,
				);
				$paymentid = $this->insert($arr);
				
				
				
		//////////////////  rms_study_history  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
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
							'branch_id'		=>$data['branch'],
							'payment_id'	=>$paymentid,
							'reg_from'		=>1, // from registrar
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
				
						if($data['dept']<=3){
							$stu_type=1;
						}else{
							$stu_type=2;
						}
							
						if($data['old_degree']==$data['dept']){
							$stu_code = $data['old_stu_code'];
						}else{
							$stu_code = $register->getNewAccountNumber($data['dept'],$data['branch']);
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
				
								'branch_id'		=>$data['branch'],
								'reg_from'		=>1, // from registrar
				
						);
						$this->insert($arr);
				
					}
				}
				
				
		////////////////  rms_student_id  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				$this->_name='rms_student_id';
				
				if($data['student_type']==1){ // new student
				
					if($data['dept']<=3){
						$stu_type=1; // khmer fulltime
					}else{
						$stu_type=2; // english fulltime
					}
				
					$arr=array(
							'branch_id'		=>$data['branch'],
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
				
						if($data['dept']<=3){
							$stu_type=1; // khmer fulltime
						}else{
							$stu_type=2; // english fulltime
						}
				
						$arr=array(
								'branch_id'		=>$data['branch'],
								'stu_id'		=>$id,
								'stu_type'		=>$stu_type,
								'degree'		=>$data['dept'],
								'is_parent'		=>$finished_degree_id,
						);
				
						$this->insert($arr);
					}
				}
				
				
	/////////////////////////   rms_student_paymentdetail  /////////////////////////////////////////////////////////////////////////////
				
				$this->_name='rms_student_paymentdetail';
				
	//////// update is_start=0 ដើម្បីអោយដឹងថា Service និង ឈប់ប្រើ រឺ  expired នៅពេលដែលសិស្សចាស់បង់លុយម្តងទៀត រួច store ID record that updated in is_parent of new record
	            
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
				
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
	////////// update is_complete = 1  ប្រសិនបើ record និងជា record ដែលជំបាក់ពីមុន	រួចបានគិត  balance again ////////////////////////////////////////////////////
	
				$this->_name='rms_student_paymentdetail';
				if(!empty($data['id_balance_record'])){
					$where="id=".$data['id_balance_record'];
					$arr = array(
							'is_complete'=>1,
							'comment'=>"បង់រួច",
					);
					$this->update($arr,$where);
				}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
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

					$subtotal = $data["total"];
					
					$discount=$data['discount'];
					$paidamount=$data['books'];
					//$paidamount=$paidamount-($data["remark"]+$data["addmin_fee"]);
					$balance= $data['remaining'];
					 
					$arr=array(
	             			'payment_id'=>$paymentid,
	             			'type'=>1,
	             			'service_id'=>$type,
	             			'payment_term'=>$data['payment_term'],
	             			'fee'=>$fee,
	             			'qty'=>$qty,
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
		
		//	print_r($data);exit();
		
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
			
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			
			return false;
			
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
							'stu_code'		=>$data['stu_id'],
							'academic_year'	=>$data['study_year'],
							'stu_khname'	=>$data['kh_name'],
							'stu_enname'	=>$data['en_name'],
							'sex'			=>$data['sex'],
							'session'		=>$data['session'],
							'degree'		=>$data['dept'],
							'grade'			=>$data['grade'],
							'stu_type'		=>$stu_type,
							'user_id'		=>$this->getUserId(),
							'branch_id'		=>$data['branch'],
					);
					$where=" stu_id=".$data['id'];
					$this->update($arr, $where);
				}
				
				$this->_name='rms_study_history';
				$arr=array(
						'stu_type'		=>$stu_type,
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
		  
				if($data['payment_term']==5){
					$price_per_sec = $data['price_per_section'];
					$amount_sec = $data['amount_section'];
				}else{
					$price_per_sec = null;
					$amount_sec = null;
				}
				
				$arr=array(
						'student_id'		=>$data['old_studens'],
						'receipt_number'	=>$data['reciept_no'],
						'session'			=>$data['session'],
						'time'				=>$data['study_hour'],
// 						'end_hour'=>$data['to_time'],
						'payment_term'		=>$data['payment_term'],
						'price_per_sec'		=>$price_per_sec,
						'amount_sec'		=>$amount_sec,
						
						'tuition_fee'		=>$data['tuitionfee'],
						'total_payment'		=>$data['tuitionfee'] - ($data['tuitionfee']*($data['discount']/100)),
						'discount_percent'	=>$data['discount'],
						'other_fee'			=>$data['remark'],
						'admin_fee'			=>$data['addmin_fee'],
						'total'				=>$data['total'],
						'paid_amount'		=>$data['books'],
						'receive_amount'	=>$data['books'],
						'balance_due'		=>$data['remaining'],
						'note'				=>$data['not'],
						'payfor_type'		=>1,
						'user_id'			=>$this->getUserId(),
						'branch_id'			=>$data['branch'],
				);
				$where="id=".$data['pay_id'];
				$this->update($arr, $where);
				

				$this->_name='rms_student_paymentdetail';
				//update is_start=0 ដើម្បីអោយដឹងថា Service និង ឈប់ប្រើ រឺ  ផ្អាក នៅពេលដែលសិស្សចាស់បង់លុយម្តងទៀត រួច store ID record that updated in is_parent of new record
				$service = 4; // លេខ 4 ជាសេវាកម្មចុះឈ្មោះចូលរៀន
				$type=1; 	  // លេខ 1 ជាប្រភេទសិស្សពី kindergarten ដល់ ទី12
				$payment_id_ser = $this->getStudentPaymentStart($data['id'],$service,$type);
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
	             			'user_id'		=>$this->getUserId(),
	             	);
	             	$this->insert($arr);
				}
			 	$db->commit();//if not errore it do....
			}catch (Exception $e){
				$db->rollBack();//អោយវាវិលត្រលប់ទៅដើមវីញពេលណាវាជួបErrore
			}
		}
    
    function getRegisterById($id){
    	$db=$this->getAdapter();
    	$sql=" SELECT 
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
				  sp.degree,
				  sp.grade,
				  sp.room_id,
				  sp.buy_product,
				  sp.is_void,
				  
				  sp.payment_term,
				  sp.price_per_sec,
				  sp.amount_sec,
				  sp.tuition_fee,
				  sp.discount_percent,
				  sp.other_fee,
				  sp.admin_fee,
				  sp.total,
				  sp.paid_amount,
				  sp.balance_due,
				  sp.amount_in_khmer,
				  sp.note,
				  sp.student_type,
				  sp.time,
				  sp.end_hour,
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
			    	spd.service_id != 4
			    	AND spd.payment_id = ".$id;
    	
    	return $db->fetchAll($sql);
    }
    
    
    function getAllGrade($grade_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT major_id As id,major_enname As name FROM rms_major WHERE dept_id=".$grade_id;
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    function getPaymentTerm($generat,$payment_term,$grade){
    	$pay=$payment_term;
    	$db = $this->getAdapter();
    	$sql="SELECT tfd.id,tfd.tuition_fee FROM rms_tuitionfee AS tf,rms_tuitionfee_detail AS tfd WHERE tf.id = fee_id
    	 AND tfd.fee_id=$generat AND tfd.class_id=$grade AND tfd.payment_term=$pay LIMIT 1";
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
    	$sql = "SELECT id,CONCAT(from_academic,'-',to_academic,'(',generation,')') AS years,(select name_en from rms_view where type=7 and key_code=time) as time FROM rms_tuitionfee
    	        WHERE `status`=1 GROUP BY from_academic,to_academic,generation,time ";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    
    function getAllYears(){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,CONCAT(from_academic,'-',to_academic,'(',generation,')') AS years , (select name_en from rms_view where type=7 and key_code=time) as time FROM rms_tuitionfee WHERE `status`=1 
    	        GROUP BY from_academic,to_academic,generation,time";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    
    function getAllYearsServiceFee(){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,CONCAT(from_academic,'-',to_academic) AS years FROM rms_servicefee WHERE `status`=1";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
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
    	      WHERE s.stu_type=3 AND s.status=1 $is_suspend $branch_id ORDER BY stu_id DESC  ";
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
    	WHERE s.stu_type=3 AND s.status=1 $is_suspend $branch_id ORDER BY stu_id DESC ";
    	return $db->fetchAll($sql);
    }
    
    //select Gep old student by name
    function getGepOldStudent($stu_id){
    	$db=$this->getAdapter();
    	$sql="SELECT stu_id,stu_enname,stu_khname,sex,`session` As ses,degree,grade FROM rms_student 
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
    	
    	$sql="SELECT 
    				s.stu_id,
    				CONCAT(s.stu_enname,'-',s.stu_khname) as name
    			FROM 
    				rms_student AS s
    			WHERE 
    				s.stu_type!=3 
    				AND s.status=1 
    				$is_suspend 
    				$branch_id  
    			ORDER BY 
    				stu_id DESC ";
    	return $db->fetchAll($sql);
    }
    
    //select general  old student by name
    
    function getGeneralOldStudentById($stu_id){
    	$db=$this->getAdapter();
    	$sql="SELECT stu_id,stu_enname,stu_khname,sex,`session` As ses,degree,grade FROM rms_student
    	WHERE  stu_type IN (1,3) AND stu_id=$stu_id LIMIT 1";
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
    	$sql="SELECT major_id AS id,CONCAT(major_enname,major_khname) AS `name` FROM rms_major where is_active=1 ";
    	return $db->fetchAll($sql);
    }
    
    function getGradeAll(){
    	$db=$this->getAdapter();
    	$sql="SELECT major_id AS id,major_enname AS `name` FROM rms_major WHERE dept_id IN(1,2,3,4) AND is_active=1 ";
    	return $db->fetchAll($sql);
    }
    function getAllDegree(){
    	$db=$this->getAdapter();
    	$sql="SELECT dept_id AS id,en_name AS `name` FROM rms_dept WHERE dept_id IN(1,2,3,4) AND is_active=1 ";
    	return $db->fetchAll($sql);
    }
    function getAllDegreeGEP(){
    	$db=$this->getAdapter();
    	$sql="SELECT dept_id AS id,en_name AS `name` FROM rms_dept WHERE dept_id NOT IN(1,2,3,4) AND is_active=1 ";
    	return $db->fetchAll($sql);
    }
   
    function getGradeAllBac(){
    	$db=$this->getAdapter();
    	$sql="SELECT major_id AS id,major_enname AS `name` FROM rms_major WHERE dept_id IN(2,3,4) AND is_active=1 ";
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
    	$sql="SELECT major_id AS id,major_enname AS `name` FROM rms_major WHERE dept_id NOT IN(1,2,3,4) AND is_active=1 ";
    	return $db->fetchAll($sql);
    }
    function getAllGrades(){
    	$db=$this->getAdapter();
    	$sql="SELECT major_id AS id,major_enname AS `name` FROM rms_major WHERE is_active=1";
    	return $db->fetchAll($sql);
    }
    function getServicesAll(){
    	$db=$this->getAdapter();
    	$sql="SELECT service_id AS id,title FROM rms_program_name WHERE `type` = 2";
    	return $db->fetchAll($sql);
    }
    public function getNewStudent($newid,$stu_type){
    	$db = $this->getAdapter();
    	$sql="  SELECT COUNT(stu_id)  FROM rms_student WHERE stu_type IN (1,3)";
    	$acc_no = $db->fetchOne($sql);
    	$new_acc_no= (int)$acc_no+1;
    	$new_acc_no=100+$new_acc_no;
    	$pre='';
    	$acc_no= strlen((int)$acc_no+1);
    	for($i = $acc_no;$i<=5;$i++){
    		$pre.='0';
    	}
    	return $pre.$new_acc_no;
    }
    function getAllGradeGEP($grade_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT CONCAT(major_enname,'-',major_khname) As name,major_id As id FROM rms_major WHERE dept_id=".$grade_id;
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    

    function getAllRoom(){
    	$db = $this->getAdapter();
    	$sql="select room_id as id , room_name as name from rms_room where is_active=1 ";
    	return $db->fetchAll($sql);
    }
    
    function getAllStudentByBranch($branch_id){
    	$db = $this->getAdapter();
    	$sql="select stu_id as id , CONCAT(stu_khname,'-',stu_enname) as name from rms_student where is_subspend=0 and status=1 and degree IN(1,2,3,4,5,6) and branch_id = $branch_id ";
    	$stu_name =  $db->fetchAll($sql);
    
    	
    	$sql1="select stu_id as id , stu_code name from rms_student where is_subspend=0 and status=1 and degree IN(1,2,3,4,5,6) and branch_id = $branch_id ";
    	$stu_code =  $db->fetchAll($sql1);
    	//return $stu_code;
    	
    	array_unshift($stu_code, array ( 'id' => -1, 'name' => '------ select student --------') );
    	array_unshift($stu_name, array ( 'id' => -1, 'name' => '------ select student --------') );
    	
    	//$result = array_merge($stu_name, $stu_code);
    	
    	$result = array(0=>$stu_code, 1=>$stu_name);
    	return $result;
    }
    
    function getDropStudentByBranch($branch_id){
    	$db = $this->getAdapter();
    	$sql="select stu_id as id , CONCAT(stu_khname,'-',stu_enname) as name from rms_student where is_subspend!=0 and status=1 and degree IN(1,2,3,4,5,6) and branch_id = $branch_id ";
    	$stu_name =  $db->fetchAll($sql);
    
    	 
    	$sql1="select stu_id as id , stu_code name from rms_student where is_subspend!=0 and status=1 and degree IN(1,2,3,4,5,6) and branch_id = $branch_id ";
    	$stu_code =  $db->fetchAll($sql1);
    	//return $stu_code;
    	 
    	//$result = array_merge($stu_name, $stu_code);
    	
    	array_unshift($stu_code, array ( 'id' => -1, 'name' => '------ select student --------') );
    	array_unshift($stu_name, array ( 'id' => -1, 'name' => '------ select student --------') );
    	 
    	$result = array(0=>$stu_code, 1=>$stu_name);
    	return $result;
    }
    
    public function getRecieptNo($type,$branch){
    	$db = $this->getAdapter();
    	 
    	if($branch>0){
    		$branch_id = $branch;
    	}else{
    		$branch_id = $this->getBranchId();
    	}
    	 
    	$sql="SELECT count(id) FROM rms_student_payment where payfor_type = $type and branch_id = $branch_id LIMIT 1 ";
    	$acc_no = $db->fetchOne($sql);
    	$new_acc_no= (int)$acc_no+1;
    	$acc_no= strlen((int)$acc_no+1);
    	 
    	$pre="";
    	 
    	if($type==1){
    		$pre="K";
    	}
    	else if($type==6){
    		$pre="FE";
    	}
    	else if($type==2){
    		$pre="PE";
    	}
    	else if($type==3){
    		$pre="TR";
    	}
    	else if($type==4){
    		$pre="F";
    	}
    	else if($type==5){
    		$pre="";
    	}
    	 
    	for($i = $acc_no;$i<6;$i++){
    		$pre.='0';
    	}
    	return $pre.$new_acc_no;
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
			    	and s.is_subspend!=0
			    	$stu_type
			    	$branch_id
		    	ORDER BY
	    			stu_id DESC
    	";
    	 
    	//     	echo $sql;//exit();
    	 
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
    
    
    
    
}










