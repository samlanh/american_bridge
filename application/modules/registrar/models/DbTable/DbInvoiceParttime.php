<?php

class Registrar_Model_DbTable_DbInvoiceParttime extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_invoice';
    
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
	function addInvoiceParttime($data){
// 		print_r($data);exit();
		$db = $this->getAdapter();//ស្ពានភ្ជាប់ទៅកាន់Data Base
		$db->beginTransaction();//ទប់ស្កាត់មើលការErrore , មានErrore វាមិនអោយចូល
		
		$register = new Registrar_Model_DbTable_DbRegister();
// 		$stu_code = $register->getNewAccountNumber($data['dept'],0);
// 		$receipt = $register->getRecieptNo(2,0);

		//$stu_code=$data['stu_id'];
		$invoice_no=$data['invoice_no'];
		
		if($data['payment_term']==5){
			$price_per_sec = $data['price_per_section'];
			$amount_sec = $data['amount_section'];
		
			$tuitionfee = $data['tuitionfee'] * $data['amount_section'];
		}else{
			$price_per_sec = null;
			$amount_sec = null;
		
			$tuitionfee = $data['tuitionfee'];
		}
		
		try{
			$this->_name='rms_invoice';
			$arr=array(
					'branch_id'		=>$this->getBranchId(),
					'student_id'	=>$data['old_studens'],
					'invoice_no'	=>$invoice_no,
					
					'academic_year'	=>$data['study_year'],
					'degree'		=>$data['dept'],
					'grade'			=>$data['grade'],
					'session'		=>$data['session'],
					'room_id'		=>$data['room'],
					'time'			=>$data['study_time'],
					
					'payment_term'	=>$data['payment_term'],
					'price_per_sec'	=>$price_per_sec,
					'amount_sec'	=>$amount_sec,
					'start_date'	=>$data['start_date'],
					'end_date'		=>$data['end_date'],
					
					'exchange_rate'		=>$data['ex_rate'],
					'tuition_fee'		=>$tuitionfee,
					'discount_percent'	=>$data['discount'],
					'discount_fix'		=>$data['discount_fix'],
					'tuition_fee_after_discount'=>($tuitionfee - $data['discount_fix']) - (($tuitionfee - $data['discount_fix'])*($data['discount']/100)),
					'total_payment'		=>$data['total'],
					
					'other_fee'		=>$data['remark'],
					
					'grand_total_payment'			=>$data['total'],
					'grand_total_payment_in_riel'	=>$data['convert_to_riels'],
					
					'note'			=>$data['not'],
					'create_date'	=>date('Y-m-d H:i:s'),
					'payfor_type'	=>2, // Eng PT
					'user_id'		=>$this->getUserId(),
			);
			$paymentid=$this->insert($arr);
			
			$db->commit();//if not errore it do....
		}catch (Exception $e){
			$db->rollBack();//អោយវាវិលត្រលប់ទៅដើមវីញពេលណាវាជួបErrore
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	
	function updateStudentGep($data){
// 		print_r($data);exit();
// 		$db = $this->getAdapter();//ស្ពានភ្ជាប់ទៅកាន់Data Base
// 		$db->beginTransaction();//ទប់ស្កាត់មើលការErrore , មានErrore វាមិនអោយចូល
		
		return 0;
		
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
    	
    	$from_date =(empty($search['start_date']))? '1': " i.create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': " i.create_date <= '".$search['end_date']." 23:59:59'";
    	$where = " AND ".$from_date." AND ".$to_date;
    	
    	$_db = new Application_Model_DbTable_DbGlobal;
    	$branch_id = $_db->getAccessPermission('i.branch_id');
    	
    	$sql=" SELECT 
				  i.id,
				  s.stu_code,
				  s.stu_khname,
				  s.stu_enname,
				  s.sex,
				  (SELECT en_name FROM rms_dept WHERE dept_id=i.degree)AS degree,
				  (SELECT CONCAT(major_enname) FROM rms_major WHERE major_id=i.grade ) AS grade,
				  i.invoice_no,
				  i.grand_total_payment,
				  i.create_date,
				  (select CONCAT(first_name,' ',last_name) from rms_users as u where u.id=i.user_id ) as user,
				  (select name_en from rms_view where type=12 and key_code = i.is_void) as void_status 
				FROM
				  rms_invoice AS i,
				  rms_student as s
				WHERE 
				  s.stu_id = i.student_id	
				  and i.payfor_type = 2 
				  and i.status=1
				  $branch_id
    	    ";
    	
    	if(!empty($search['adv_search'])){
    		$s_where=array();
    		$s_search=addslashes(trim($search['adv_search']));
    		$s_where[]= " stu_code LIKE '%{$s_search}%'";
    		$s_where[]= " invoice_no LIKE '%{$s_search}%'";
    		$s_where[]= " stu_khname LIKE '%{$s_search}%'";
    		$s_where[]= " stu_enname LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    	if(!empty($search['degree_gep'])){
    		$where.=" AND i.degree=".$search['degree_gep'];
    	}
    	if(!empty($search['study_year'])){
    		$where.=" AND i.year=".$search['study_year'];
    	}
    	if(!empty($search['sess_gep'])){
    		$where.=" AND i.time=".$search['sess_gep'];
    	}
    	if(!empty($search['grade_gep'])){
    		$where.=" AND i.grade=".$search['grade_gep'];
    	}
    	if(!empty($search['user'])){
    		$where.=" AND i.user_id=".$search['user'];
    	}
    	//print_r($sql.$where);
    	$order=" ORDER By i.id DESC ";
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
				  
				  i.branch_id,
				  i.invoice_no,
				  i.academic_year,
				  i.session,
				  i.degree,
				  i.grade,
				  i.room_id,
				  i.is_void,
				  
				  i.payment_term,
				  i.price_per_sec,
				  i.amount_sec,
				  i.exchange_rate,
				  i.tuition_fee,
				  i.discount_percent,
				  i.discount_fix,
				  i.tuition_fee_after_discount,
				  i.other_fee,
				  
				  i.total_payment,
				  i.note,
				  i.time,
				  i.start_date,
				  i.end_date
				FROM
				  rms_student AS s,
				  rms_invoice AS i
				WHERE s.stu_id = i.student_id 
				  AND i.id =".$id;
    	
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

