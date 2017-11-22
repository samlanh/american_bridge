<?php

class Registrar_Model_DbTable_DbUniformAndBook extends Zend_Db_Table_Abstract
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
    
    function getRecieptNo(){
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
    
	function addProductPayment($data){
		//print_r($data);exit();
		$db = $this->getAdapter();//ស្ពានភ្ជាប់ទៅកាន់Data Base
		$db->beginTransaction();//ទប់ស្កាត់មើលការErrore , មានErrore វាមិនអោយចូល
		
		$register = new Registrar_Model_DbTable_DbRegister();
		//$receipt_no = $register->getRecieptNo(5,0);
		
		$receipt_no = $data['receipt_no'];
		
		try{
			$arr=array(
					'branch_id'			=>$this->getBranchId(),
					'student_id'		=>$data['stu_name'],
					'receipt_number'	=>$receipt_no,
					'payfor_type'		=>5 , // product payment
					
					'buy_product'		=>1 ,
					'reg_from'			=>0 ,
					
					'exchange_rate'		=>$data['ex_rate'],
					
					'grand_total_payment'			=>$data['grand_total'],
					'grand_total_payment_in_riel'	=>$data['convert_to_riels'],
					'grand_total_paid_amount'		=>$data['grand_total'],
					'grand_total_balance'			=>0,
					
					'create_date'		=>date("Y-m-d H:i:s"),
					'user_id'			=>$this->getUserId(),
				);
			$id = $this->insert($arr);
				
			$this->_name='rms_student_paymentdetail';
			if(!empty($data['identity'])){
				
				$ids = explode(',', $data['identity']);
	    		foreach ($ids as $i){
	    			$complete=1;
		    		$status="បង់រួច";
	    			$_arr = array(
	    					'payment_id'	=>$id,
	    					'service_id'	=>$data['service_'.$i],
	    					
	    					'fee'			=>$data['price_'.$i],
	    					'qty'			=>$data['qty_'.$i],
	    					'discount_percent'=>$data['discount_'.$i],
	    					
	    					'subtotal'		=>$data['subtotal_'.$i],
	    					'paidamount'	=>$data['subtotal_'.$i],
	    					'balance'		=>0,
	    					
	    					'note'			=>$data['remark'.$i],
	    					'type'			=>4,
	    					'is_start'		=>0,
	    					'is_parent'		=>0,
	    					'is_complete'	=>$complete,
	    					'comment'		=>$status,
	    			);
	    			$id_record = $this->insert($_arr);
	    		}
			}
    			
    		$db->commit();
		}catch (Exception $e){
			echo $e->getMessage();
			$db->rollBack();//អោយវាវិលត្រលប់ទៅដើមវីញពេលណាវាជួបErrore
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
				
				$db->commit();
				return 0;
			}else{
				return 0;
			}
		}catch (Exception $e){
			echo $e->getMessage();
			$db->rollBack();
		}
		
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		
		return 1;
		
			try{
				$arr=array(
					'student_id'		=>$data['studentid'],
					'receipt_number'	=>$data['reciept_no'],
					'year'				=>$data['study_year'],
					'total_payment'		=>$data['grand_total'],
					'receive_amount'	=>$data['grand_total'],
					'paid_amount'		=>$data['grand_total'],
					'return_amount'		=>0,
					'balance_due'		=>0,
					'note'				=>$data['not'],
				);
				$where =$this->getAdapter()->quoteInto("id=?", $data['id']);
			 	$this->update($arr,$where);
			    
				$this->_name='rms_student_paymentdetail';
				$where = "payment_id = ".$data['id'];
				$this->delete($where);
				
				$ids = explode(',', $data['identity']);
				$disc = 0;
				$total = 0;
    			foreach ($ids as $i){
    				$disc=$disc+$data['discount_'.$i];
    				$total=$total+($data['price_'.$i]*$data['qty_'.$i]);
    				$complete=1;
	    			$status="បង់រួច";
    				$_arr = array(
    						'payment_id'	=>$data['id'],
    						'type'			=>4,
    						'service_id'	=>$data['service_'.$i],
    						'fee'			=>$data['price_'.$i],
    						'qty'			=>$data['qty_'.$i],
    						'paidamount'	=>$data['subtotal_'.$i],
    						'discount_percent'=>$data['discount_'.$i],
    						'note'			=>$data['remark'.$i],
    						'subtotal'		=>$data['subtotal_'.$i],
    						'is_parent'		=>0,
    						'is_start'		=>0,
    						'is_complete'   =>$complete,
    						'comment'		=>$status,
    				);
    				$this->insert($_arr);
    			}
    			$this->_name='rms_student_payment';
    			$datadisc = array(
    					'discount_percent'=>$disc,
    					'total'=>$data['grand_total'],
    					'tuition_fee'=>$total,
    					);
    			$where=$this->getAdapter()->quoteInto("id=?", $data['id']);
    			$this->update($datadisc, $where);
    			
    			$db->commit();
    			return true;
			}catch (Exception $e){
				echo $e->getMessage();
				$db->rollBack();//អោយវាវិលត្រលប់ទៅដើមវីញពេលណាវាជួបErrore
			}
			
	}
	
    function getAllProductPayment($search){
    	$user=$this->getUserId();
    	$db=$this->getAdapter();
    	$sql="select 
    			sp.id,
		    	(select CONCAT(stu_khname,' - ',stu_enname) from rms_student where rms_student.stu_id=sp.student_id limit 1)AS name,
		    	(select name_kh from rms_view where rms_view.type=2 and rms_view.key_code=(select sex from rms_student where rms_student.stu_id=sp.student_id limit 1) limit 1)AS sex,
		    	receipt_number,
		    	sp.grand_total_payment,
		    	sp.grand_total_paid_amount,
		    	create_date,
		    	(select CONCAT(last_name,' ',first_name) from rms_users where rms_users.id=sp.user_id) AS user,
		    	(select name_en from rms_view where type=12 and key_code = sp.is_void) as void_status 
	    	 from 
	    		rms_student_payment as sp 
	    	 where 
	    	 	sp.payfor_type=5
	    	 	and reg_from=0
    		";
	    	
    	$from_date =(empty($search['start_date']))? '1': " sp.create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': " sp.create_date <= '".$search['end_date']." 23:59:59'";
    	$where = " AND ".$from_date." AND ".$to_date;
    	$order=" ORDER BY id DESC ";
    	if(!empty($search['adv_search'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['adv_search']));
    		$s_where[] = "  receipt_number LIKE '%{$s_search}%'";
    		$s_where[] = " (select stu_enname from rms_student where rms_student.stu_id=sp.student_id) LIKE '%{$s_search}%'";
    		$s_where[] = " (select stu_khname from rms_student where rms_student.stu_id=sp.student_id) LIKE '%{$s_search}%'";
    		$where .=' AND ( 
    		'.implode(' OR ',$s_where).')';
    	}
    	if(!empty($search['user'])){
    		$where.=" AND sp.user_id=".$search['user'];
    	}
    	return $db->fetchAll($sql.$where.$order);
    }
    function getStudentServicePaymentByID($id){
    	$db=$this->getAdapter();
    	$sql="select * from rms_student_payment where id=".$id;
    	return $db->fetchRow($sql);
    }
    
    function getStudentServicePaymentDetailByID($id){
    	$db=$this->getAdapter();
    	$sql="select * from rms_student_paymentdetail where payment_id=".$id;
    	return $db->fetchAll($sql);
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
    public function getAllStudentCode(){
    	$db = $this->getAdapter();
    	$sql="SELECT stu_id as id,stu_code as code  FROM rms_student where status=1 and is_subspend=0 ORDER BY  stu_code DESC ";
    	return $db->fetchAll($sql);
    	
    }
    public function getAllStudentName(){
    	$db = $this->getAdapter();
    	$sql="SELECT stu_id as id,CONCAT(stu_khname,' - ',stu_enname) as name  FROM rms_student where status=1 and is_subspend=0 ORDER BY  stu_code DESC ";
    	return $db->fetchAll($sql);
    	 
    }
    public function getAllpriceByServiceTerm($studentid,$serviceid,$termid,$year){
    	$db=$this->getAdapter();
    	
    	$sql="select rms_student_paymentdetail.id,rms_student_paymentdetail.validate,balance AS price_fee from rms_student_paymentdetail,rms_student_payment where rms_student_payment.id=rms_student_paymentdetail.payment_id and rms_student_paymentdetail.service_id=$serviceid and rms_student_payment.student_id=$studentid and is_complete=0 limit 1";                               
    	$row=$db->fetchRow($sql);
    	if($row['price_fee']>0){
    		$row['sms']='លុយជំពាក់ពីមុន';
    		return $row;
    	}
    	else{
    		$sql="select price_fee from rms_servicefee_detail where  rms_servicefee_detail.service_id=$serviceid and rms_servicefee_detail.payment_term=$termid and service_feeid=$year limit 1";
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
    	$sql="select stu_enname,stu_khname,sex,tel from rms_student where stu_id=$studentid limit 1";
    	return $db->fetchRow($sql);
    }
    
    public function getStudentBalance($studentid,$serviceid,$termid){
    	$db=$this->getAdapter();
    	$sql="select stu_enname,stu_khname,sex from rms_student where stu_id=$studentid limit 1";
    	return $db->fetchRow($sql);
    }
    
    public function getAllService($year){
    	$this->_name='rms_servicefee';
    	$db=$this->getAdapter();
    	$sql="SELECT title FROM rms_program_name WHERE rms_program_name.service_id=(select service_id from rms_servicefee_detail where rms_servicefee_detail.service_feeid=$year )";
    	return $db->fetchRow($sql);
    	
    	echo $sql;
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
    	$db=$this->getAdapter();
    	$sql="SELECT sf.id,CONCAT(from_academic,'-',to_academic,' (',generation,')') AS years FROM rms_servicefee as sf , rms_tuitionfee as tf
							WHERE sf.`status`= 1 and tf.id=sf.academic_year ";
    	return $db->fetchAll($sql);
    }
    
    public function getAllServiceItemOption(){
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	$rows = $this->getAllServiceOption();
    	array_unshift($rows,array('service_id' => '0',"title"=>"Select Service"));
    	$options = '';
    	if(!empty($rows))foreach($rows as $value){
    		$options .= '<option value="'.$value['service_id'].'" >'.htmlspecialchars($value['title'], ENT_QUOTES).'</option>';
    	}
    	return $options;
    }
    
    public function getAllServiceOption(){
    	$db = $this->getAdapter();
    	$sql = " SELECT service_id,title FROM `rms_program_name` WHERE type=1 AND status = 1 AND title!=''";
    	return $db->fetchAll($sql);
    }
    
    public function getPrice($service_id){
    	$db = $this->getAdapter();
    	$sql = " SELECT price FROM `rms_program_name` WHERE type=1 AND status = 1 AND service_id=".$service_id;
    	return $db->fetchOne($sql);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
}





