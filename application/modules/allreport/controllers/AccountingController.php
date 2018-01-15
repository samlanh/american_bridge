<?php
class Allreport_AccountingController extends Zend_Controller_Action {
	
    public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction(){
	}
	public function rptAccountRecAction(){
		
	}
	
	public function rptAllStudentAction(){
		if($this->getRequest()->isPost()){
			$search=$this->getRequest()->getPost();
		}
		else{
			$search=array(
					'title' 		=>'',
					'branch' 		=>'',
					'study_year' 	=>'',
					'grade_all' 	=>'',
					'session' 		=>'',
					'start_date'	=> date('Y-m-d'),
					'end_date'		=> date('Y-m-d'),
					'stu_type' 		=>-1,
			);
		}
		$form=new Registrar_Form_FrmSearchInfor();
		$forms=$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($forms);
		$this->view->form_search=$form;
	
		$group= new Allreport_Model_DbTable_DbRptAllStudent();
		$this->view->rs = $rs_rows = $group->getAllStudent($search);
		$this->view->search=$search;
	}
	
	public function rptAllAmountStudentAction(){
		if($this->getRequest()->isPost()){
			$search=$this->getRequest()->getPost();
		}
		else{
			$search=array(
					'title' 		=>'',
					'branch' 		=>'',
					'study_year' 	=>'',
					'grade_all' 	=>'',
					'session' 		=>'',
					'start_date'	=> date('Y-m-d'),
					'end_date'		=> date('Y-m-d'),
			);
		}
		$form=new Registrar_Form_FrmSearchInfor();
		$forms=$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($forms);
		$this->view->form_search=$form;
	
		$group= new Allreport_Model_DbTable_DbRptAllStudent();
		$this->view->rs = $rs_rows = $group->getAllAmountStudent($search);
		$this->view->search=$search;
	}
	
	function rptStudentpaymentAction(){
		try{
			if($this->getRequest()->isPost()){
					$_data=$this->getRequest()->getPost();
					$search = array(
							'txtsearch' =>$_data['txtsearch'],
							'start_date'=> $_data['from_date'],
		      				'end_date'=> $_data['to_date']
					);
					
				}
				else{
					$search = array(
							'txtsearch' =>'',
							'start_date'=> date('Y-m-d'),
	                        'end_date'=>date('Y-m-d'),
					);
				}
			$this->view->search = $search;
			$db = new Allreport_Model_DbTable_DbRptPayment();
			$this->view->row = $db->getStudentPayment($search);
			
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
			
		}catch(Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	function  rptPaymentdetailbytypeAction(){
		try{
			if($this->getRequest()->isPost()){
				$_data=$this->getRequest()->getPost();
				//print_r($_data); exit();
				$search = array(
						'txtsearch' =>$_data['txtsearch'],
						'start_date'=> $_data['from_date'],
						'end_date'=> $_data['to_date'],
						'service_type'=>$_data['service']
				);
		
			}
			else{
				$search = array(
						'txtsearch' =>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						'service_type'=>0
				);
			}
			$db = new Allreport_Model_DbTable_DbRptPayment();
			$this->view->row = $db->getPaymentDetailByType($search);
			$this->view->service = $db->getService();
			$this->view->search = $search;
			
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
			
		}catch(Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	function rptStudentpaymentdetailAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'txtsearch' =>'',
						'branch' =>'',
						'start_date'=> date('Y-m-d'),
                        'end_date'=>date('Y-m-d'),
				);
			}
			$db = new Allreport_Model_DbTable_DbRptPayment();
			$this->view->row = $db->getStudentPaymentDetail($search);
			$this->view->service = $db->getService();
			$this->view->search = $search;
			
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
			
		}catch(Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		
	}

	function rptPaymentrecieptdetailAction(){
		$id=$this->getRequest()->getParam("id");
		$db = new Allreport_Model_DbTable_DbRptPayment();
		$row = $db->getPaymentReciptDetail($id);
		
		$this->view->row = $row;
		$this->view->rr = $db->getStudentPaymentByid($id);
	
	}
	function  rptSuspendserviceAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search=array(
						'txtsearch' => '',
						'start_date'=>date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						'service'=>'',
						'study_year'=>'',
				);
			}
		$this->view->search = $search;
		$db = new Allreport_Model_DbTable_DbSuspendService();
		$this->view->rs = $db->getStudetnSuspendServiceDetail($search);
		}catch (Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	function rptInvoiceAction(){
	
	}
	function rptStudentListDetailPart1Action(){
	
	}
	function rptStudentListDetailPart2Action(){
	
	}
	function rptStudentListDetailPart3Action(){
	
	}
	public function rptTuitionFeeAction()
	{
	
	}
	function rptGepFeeAction(){
	
	}
	function rptGepListAction(){
	
	}
	function rptListOfItemAction(){
	
	}
	
	
	
	public function rptstudentbalanceAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$search = array(
						'txtsearch' => $data['txtsearch'],
						'start_date'=> $data['start_date'],
                        'end_date'=>$data['end_date'],
						'service'=>$data['service'],
						'status'=>$data['status'],
						'payfor_type'=>$data['payfor_type'],
				);
			}else{
				$search=array(
						'txtsearch' =>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						'service'=>'',
						'status'=>'',
						'payfor_type'=>'',
				);
			}
			
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
			
			$db = new Allreport_Model_DbTable_DbRptStudentBalance();
			$this->view->rs = $db->getAllStudentBalance($search);
			$this->view->search = $search;
// 			print_r($abc);exit();
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	public function rptexpectincomeAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$search = array(
						'txtsearch' => $data['txtsearch'],
						'start_date'=> $data['from_date'],
						'end_date'=>$data['to_date']
				);
			}else{
				$search=array(
						'txtsearch' =>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
				);;
			}
				
			$db = new Allreport_Model_DbTable_DbRptExpectIncome();
			$this->view->rs = $db->getAllExpectIncome($search);
			$this->view->search = $search;
			// 			print_r($abc);exit();
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	public function rptstudentnearlyendserviceAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$search = array(
						'txtsearch' => $data['txtsearch'],
						'branch'	=> $data['branch'],
						'degree_all'=> $data['degree_all'],
						'grade_all'	=> $data['grade_all'],
						'end_date'	=>$data['to_date'],
						'service'	=>$data['service']
				);
			}else{
				$search=array(
						'txtsearch' =>'',
						'branch'	=> '',
						'degree_all'=> '',
						'grade_all'	=> '',
						'end_date'	=>date('Y-m-d'),
						'service'	=>''
				);
			}
			$db = new Allreport_Model_DbTable_DbRptStudentNearlyEndService();
			$abc = $this->view->row = $db->getAllStudentNearlyEndService($search);
			
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
			
			$this->view->search = $search;
			
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	public function rptstudentpaymentlateAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$search = array(
						'txtsearch' => $data['txtsearch'],
						'branch'	=> $data['branch'],
						'degree_all'=> $data['degree_all'],
						'grade_all'	=> $data['grade_all'],
						'end_date'	=>$data['to_date'],
						'service'	=>$data['service'],
				);
			}else{
				$search=array(
						'txtsearch' =>'',
						'branch'	=> $data['branch'],
						'degree_all'=> '',
						'grade_all'	=> '',
						'end_date'	=>date('Y-m-d'),
						'service'	=>'',
				);;
			}
			$db = new Allreport_Model_DbTable_DbRptStudentPaymentLate();
			$abc = $this->view->row = $db->getAllStudentPaymentLate($search);
			
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
			
			$this->view->search = $search;
				
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	public function rptFeeAction(){
	
		if($this->getRequest()->isPost()){
			$search=$this->getRequest()->getPost();
		}
		else{
			$search=array(
					'txtsearch' 	=>'',
					'study_year' 	=>'',
					'grade_all' 	=>0,
					'degree_all' 	=>0,
					'branch' 		=>'',
			);
		}
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
		
		$db = new Allreport_Model_DbTable_DbRptFee();
		$group= new Allreport_Model_DbTable_DbRptFee();
		$rs_rows = $group->getAllTuitionFee($search);
	
		$year = $db->getAllYearFee();
		$this->view->row = $year;
		
		$model = new Application_Model_DbTable_DbGlobal();
		$row=0;$indexterm=1;$key=0;
		if(!empty($rs_rows)){
			foreach ($rs_rows as $i => $rs) {
				$rows = $db->getFeebyOther($rs['id'],$search['grade_all'],$search['degree_all']);
				$fee_row=1;
				
// 				print_r($rows);
				
				if(!empty($rows))foreach($rows as $payment_tran){
					if($payment_tran['payment_term']==1){
						$rs_rows[$key]=$this->headAddRecordTuitionFee($rs,$key);
						$term = $model->getAllPaymentTerm(null,null,null);
						//print_r($term);
						$rs_rows[$key]['status'] = Application_Model_DbTable_DbGlobal::getAllStatus($payment_tran['status']);
						$rs_rows[$key]['degree']=$payment_tran['degree'];
						$rs_rows[$key]['class'] = $payment_tran['class'];
						$rs_rows[$key]['remark'] = $payment_tran['remark'];
						$rs_rows[$key]['month'] = $payment_tran['tuition_fee'];
						$key_old=$key;
						$key++;
					}elseif($payment_tran['payment_term']==2){
						$term = $model->getAllPaymentTerm($payment_tran['payment_term']);
						$rs_rows[$key_old]['quarter'] = $payment_tran['tuition_fee'];
	
					}elseif($payment_tran['payment_term']==3){
						$term = $model->getAllPaymentTerm($payment_tran['payment_term']);
						$rs_rows[$key_old]['semester'] = $payment_tran['tuition_fee'];
	
					}elseif($payment_tran['payment_term']==4){
						$term = $model->getAllPaymentTerm($payment_tran['payment_term']);
						$rs_rows[$key_old]['year'] = $payment_tran['tuition_fee'];
					}
					elseif($payment_tran['payment_term']==5){
						$term = $model->getAllPaymentTerm($payment_tran['payment_term']);
						$rs_rows[$key_old]['day'] = $payment_tran['tuition_fee'];
					}
				}
			}
		}
		else{
			$rs_rows=array();
			$result = Application_Model_DbTable_DbGlobal::getResultWarning();
		}
		$this->view->rs = $rs_rows;
		$this->view->search = $search;
	}
	
	public function headAddRecordTuitionFee($rs,$key){
		$result[$key] = array(
				'id' 	  => $rs['id'],
				'academic'=> $rs['academic'],
				'generation'=> $rs['generation'],
				'branch_name'=> $rs['branch_name'],
				'degree'=>'',
				'class'=>'',
				'month'=>'',
				'quarter'=>'',
				'semester'=>'',
				'year'=>'',
				'day'=>'',
				
				'time'=>$rs['time'],
				'date'=>$rs['create_date'],
				'status'=>''
		);
		return $result[$key];
	}
	
	public function rptServiceChargeAction(){
	
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
		}
		else{
			$search=array(
					'txtsearch' =>'',
					'study_year' =>'',
					'service_type'=>-1,
					'service' =>-1,
			);
		}
	
		$db = new Allreport_Model_DbTable_DbRptServiceCharge();
		$this->view->all_service = $db->getServicesAll();
		$this->view->service_type = $db->getServicesType();
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
		
		$db = new Allreport_Model_DbTable_DbRptServiceCharge();
		$service = $db->getAllServiceFee($search);
		$year = $db->getAllYearService();
		$this->view->row = $year;
// 		print_r($year);exit();
	
		$model = new Application_Model_DbTable_DbGlobal();
		$row = 0;$indexterm = 1;$key = 0;$rs_rows = array();
		if(!empty($service)){
			foreach ($service as $i => $rs) {
				$rows = $db->getServiceFeebyId($rs['id'],$search['service_type'],$search['service']);
				$fee_row=1;
				if(!empty($rows))foreach($rows as $payment_tran){
					if($payment_tran['payment_term']==1){
						$rs_rows[$key]=$this->headAddRecordServiceFee($rs,$key);
						$term = $model->getAllPaymentTerm(null,null,null);
						
						$rs_rows[$key]['ser_type'] = $payment_tran['ser_type'];
						$rs_rows[$key]['service_name'] = $payment_tran['service_name'];
						$rs_rows[$key]['remark'] = $payment_tran['remark'];
						$rs_rows[$key]['monthly'] = $payment_tran['price_fee'];
						$key_old=$key;
						$key++;
					}
					elseif($payment_tran['payment_term']==2){
						$term = $model->getAllPaymentTerm($payment_tran['payment_term']);
						$rs_rows[$key_old]['quarter'] = $payment_tran['price_fee'];
							
					}
					elseif($payment_tran['payment_term']==3){
						$term = $model->getAllPaymentTerm($payment_tran['payment_term']);
						$rs_rows[$key_old]['semester'] = $payment_tran['price_fee'];
					}
					elseif($payment_tran['payment_term']==4){
						$term = $model->getAllPaymentTerm($payment_tran['payment_term']);
						$rs_rows[$key_old]['year'] = $payment_tran['price_fee'];
					}
					elseif($payment_tran['payment_term']==5){
						$term = $model->getAllPaymentTerm($payment_tran['payment_term']);
						$rs_rows[$key_old]['day'] = $payment_tran['price_fee'];
					}
					
				}
			}
		}
		else{
			$rs_rows = array();
			$result = Application_Model_DbTable_DbGlobal::getResultWarning();
		}
	
		$this->view->rs = $rs_rows;
		$this->view->search = $search;
	}
	
	public function headAddRecordServiceFee($rs,$key){
		$result[$key] = array(
				'id' 	  => $rs['id'],
				'years'		=> $rs['years'],
				'time'		=> $rs['time'],
				'generation'=> $rs['generation'],
				'monthly'=>'',
				'quarter'=>'',
				'semester'=>'',
				'year'=>'',
		);
		return $result[$key];
	}
	
	
	function rptStaffAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$search = array(
						'txtsearch' => $data['txtsearch'],
						'start_date'=> $data['from_date'],
						'end_date'=>$data['to_date'],
						'service'=>$data['service'],
				);
			}else{
				$search=array(
						'txtsearch' =>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						'service'=>'',
				);;
			}
				
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
				
			$db = new Allreport_Model_DbTable_DbRptStaff();
			$this->view->rs = $db->getAllStaff($search);
			$this->view->search = $search;
			// 			print_r($abc);exit();
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}

//////////////////////////////////////////////////////////////// rpt attendant ////////////////////////////////////////////////////////////////////
	
	function rptAttCarAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						'service'=>'',
						'carid'=>'',
						'branch'=>'',
				);
			}
		
			$db = new Allreport_Model_DbTable_DbRptAttCar();
			$this->view->rs = $db->getAllAttCar($search);
			$this->view->search = $search;
			// 			print_r($abc);exit();
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		
		$db = new Allreport_Model_DbTable_DbRptAttCar();
		$this->view->carid = $db->getAllCar();
		$this->view->service = $db->getAllService();
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	
	function rptAttLunchAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						'service'=>'',
						'branch'=>'',
				);
			}
	
			$db = new Allreport_Model_DbTable_DbRptAttLunch();
			$this->view->rs = $db->getAllAttLunch($search);
			$this->view->search = $search;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	
		$db = new Allreport_Model_DbTable_DbRptAttLunch();
		$this->view->service = $db->getAllLunchService();
	
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	
	}
	
	
	function rptAttStudyAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						'grade'=>0,
						'session'=>0,
						'room'=>0,
						'branch'=>'',
				);
			}
	
			$db = new Allreport_Model_DbTable_DbRptAttStudy();
			$this->view->rs = $db->getAllAttStudy($search);
			$this->view->search = $search;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	
		$db = new Allreport_Model_DbTable_DbRptAttStudy();
		$this->view->grade = $db->getAllGrade();
		$this->view->session = $db->getAllSession();
		$this->view->room = $db->getAllRoom();
	
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	
//////////////////////////////////////////////////////////////////// rpt payment list //////////////////////////////////////////////////////////	
	
	public function rptEnglishFulltimePaymentListAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				
				$search1=array(
						'txtsearch' 	=>$data['txtsearch'],
						'room'			=>$data['room'],
						'branch'		=>$data['branch'],
						'degree'		=>$data['degree_en_ft'],
						'grade'			=>$data['grade_en_ft'],
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
						'service'		=>0,
				);
				
				$search=$this->getRequest()->getPost();
				
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'degree_en_ft'	=>0,
						'grade_en_ft'	=>0,
						'room'			=>0,
						'branch'		=>0,
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
				);
				
				$search1=array(
						'txtsearch' 	=>'',
						'degree'		=>0,
						'grade'			=>0,
						'room'			=>0,
						'branch'		=>0,
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
						'service'		=>0,
				);
			}
		
			$db = new Allreport_Model_DbTable_DbRptPaymentList();
			$this->view->rs = $db->getAllEnglishFulltimePaymentList($search);
			//print_r($this->view->rs);

			$this->view->total_student = $db->getAllAmountStudentByType($search1,6); // 1 = khmer fulltime payment
// 			echo "all = ".count($this->view->total_student);
				
			$this->view->student_drop = $db->getAllAmountStudentDropByType($search1,6,null); // 1 = khmer fulltime payment
			$this->view->student_drop_for_month = $db->getAllAmountStudentDropByType($search1,6,1);
			//echo " , drop = ".count($this->view->student_drop);
				
			$this->view->new_student = $db->getAllAmountNewStudentByType($search1,6,null); // 1 = khmer fulltime payment
			$this->view->new_student_for_month = $db->getAllAmountNewStudentByType($search1,6,1);
			//echo " , New = ".count($this->view->new_student_for_month);
			
			$this->view->amount_student_by_grade = $db->getAllAmountStudentByGrade($search1,6); // 1 = khmer fulltime payment
// 			print_r($this->view->amount_student_by_grade);
			
			$this->view->student_payable_last_month = $db->getStudentPayableLastMonth($search1,6,1); // payfor_type = 6 => EFT , type = 1 => study fulltime type                
			$this->view->student_payable_this_month = $db->getStudentPayableThisMonth($search1,6,1); // payfor_type = 6 => EFT , type = 1 => study fulltime type
			
			$this->view->search = $search;
			$this->view->search1 = $search1;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		
		$db = new Allreport_Model_DbTable_DbRptPaymentList();
		$this->view->all_month = $db->getAllMonth();
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	public function rptKhFulltimePaymentListAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				
				$search1=array(
						'txtsearch' 	=>$data['txtsearch'],
						'room'			=>$data['room'],
						'branch'		=>$data['branch'],
						'degree'		=>$data['degree_kh_ft'],
						'grade'			=>$data['grade_kh_ft'],
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
						'service'		=>0,
						);
				
				$search=$this->getRequest()->getPost();
				
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'degree_kh_ft'	=>0,
						'grade_kh_ft'	=>0,
						'room'			=>0,
						'branch'		=>0,
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
				);
				
				$search1=array(
						'txtsearch' 	=>'',
						'degree'		=>0,
						'grade'			=>0,
						'room'			=>0,
						'branch'		=>0,
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
						'service'		=>0,
				);
			}
		
			$db = new Allreport_Model_DbTable_DbRptPaymentList();
			$this->view->rs = $db->getAllKhFulltimePaymentList($search);
			
			$this->view->total_student = $db->getAllAmountStudentByType($search1,1); // 1 = khmer fulltime payment
			//echo "all = ".count($this->view->total_student);
				
			$this->view->student_drop = $db->getAllAmountStudentDropByType($search1,1,null); // 1 = khmer fulltime payment
			$this->view->student_drop_for_month = $db->getAllAmountStudentDropByType($search1,1,1);
			//echo " , drop = ".count($this->view->student_drop);
				
			$this->view->new_student = $db->getAllAmountNewStudentByType($search1,1,null); // 1 = khmer fulltime payment
			$this->view->new_student_for_month = $db->getAllAmountNewStudentByType($search1,1,1);
			//echo " , New = ".count($this->view->new_student_for_month);
			
			$this->view->amount_student_by_grade = $db->getAllAmountStudentByGrade($search1,1); // 1 = khmer fulltime payment
			//print_r($this->view->amount_student_by_grade);
			
			$this->view->student_payable_last_month = $db->getStudentPayableLastMonth($search1,1,1); // payfor_type = 6 => KFT , type = 1 => study fulltime type
			$this->view->student_payable_this_month = $db->getStudentPayableThisMonth($search1,1,1); // payfor_type = 6 => KFT , type = 1 => study fulltime type
				
			$this->view->search = $search;
			$this->view->search1 = $search1;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
		
		$db = new Allreport_Model_DbTable_DbRptPaymentList();
		$this->view->all_month = $db->getAllMonth();
		
	}
	
	
	public function rptEnglishParttimePaymentListAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$search1=array(
						'txtsearch' 	=>$data['txtsearch'],
						'room'			=>$data['room'],
						'branch'		=>$data['branch'],
						'degree'		=>$data['degree_gep'],
						'grade'			=>$data['grade_gep'],
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
						'service'		=>0,
				);
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'degree_gep'	=>0,
						'grade_gep'		=>0,
						'room'			=>0,
						'branch'		=>0,
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
				);
				
				$search1=array(
						'txtsearch' 	=>'',
						'degree'		=>0,
						'grade'			=>0,
						'room'			=>0,
						'branch'		=>0,
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
						'service'		=>0,
				);
			}
	
			$db = new Allreport_Model_DbTable_DbRptPaymentList();
			$this->view->rs = $db->getAllEnglishParttimePaymentList($search);
	
			$this->view->total_student = $db->getAllAmountStudentByType($search1,2); // 1 = khmer fulltime payment
			//echo "all = ".count($this->view->total_student);
				
			$this->view->student_drop = $db->getAllAmountStudentDropByType($search1,2,null); // 1 = khmer fulltime payment
			$this->view->student_drop_for_month = $db->getAllAmountStudentDropByType($search1,2,1);
			//echo " , drop = ".count($this->view->student_drop);
				
			$this->view->new_student = $db->getAllAmountNewStudentByType($search1,2,null); // 1 = khmer fulltime payment
			$this->view->new_student_for_month = $db->getAllAmountNewStudentByType($search1,2,1);
			//echo " , New = ".count($this->view->new_student_for_month);
			
			$this->view->amount_student_by_grade = $db->getAllAmountStudentByGrade($search1,2); // 1 = khmer fulltime payment
			//print_r($this->view->amount_student_by_grade);
				
			$this->view->student_payable_last_month = $db->getStudentPayableLastMonth($search1,2,2); // payfor_type = 2 => EPT , type = 2 => study parttime type
			$this->view->student_payable_this_month = $db->getStudentPayableThisMonth($search1,2,2); // payfor_type = 2 => EPT , type = 2 => study parttime type
			
			
			$this->view->search = $search;
			$this->view->search1 = $search1;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	
		$db = new Allreport_Model_DbTable_DbRptPaymentList();
		$this->view->all_month = $db->getAllMonth();
	
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	
	
	public function rptTransportPaymentListAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				
				$search=array(
						'txtsearch' 	=>$data['txtsearch'],
						'room'			=>0,
						'branch'		=>$data['branch'],
						'degree'		=>0,
						'grade'			=>0,
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
						'service'		=>$data['service'],
				);
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'service'		=>0,
						'branch'		=>0,
						'degree'		=>0,
						'grade'			=>0,
						'room'			=>0,
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
						
				);
			}
		
			$db = new Allreport_Model_DbTable_DbRptPaymentList();
			$this->view->rs = $db->getAllTransportPaymentList($search);
		
			$this->view->total_student = $db->getAllAmountServiceStudentByType($search,3,4); // 3=transport payfor_type , 4 = transport type
				
			$this->view->student_drop = $db->getAllAmountStudentDropServiceByType($search,3,null,4); // 3 = transport payment , 4=transport type
			$this->view->student_drop_for_month = $db->getAllAmountStudentDropServiceByType($search,3,1,4);
// 			echo " , drop = ".count($this->view->student_drop);
				
			$this->view->new_student = $db->getAllAmountNewServiceStudentByType($search,3,null,4); // 3 = transport payment , 4=transport service
			$this->view->new_student_for_month = $db->getAllAmountNewServiceStudentByType($search,3,1,4);
				
			$this->view->amount_student_by_service = $db->getAllAmountStudentByService($search,3,3,4);
			
			$this->view->student_payable_last_month = $db->getStudentPayableLastMonthService($search,3,3,4); // payfor_type = 3 => Transport , type = 3 => Transport type
			$this->view->student_payable_this_month = $db->getStudentPayableThisMonthService($search,3,3,4); // payfor_type = 3 => Transport , type = 3 => Transport type
					
			$this->view->search = $search;
			
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		
		$db = new Allreport_Model_DbTable_DbRptPaymentList();
		$this->view->all_month = $db->getAllMonth();
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	public function rptStayAndLunchPaymentListAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				
				$search=array(
						'txtsearch' 	=>$data['txtsearch'],
						'room'			=>0,
						'branch'		=>$data['branch'],
						'degree'		=>0,
						'grade'			=>0,
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
						'service'		=>$data['service'],
				);
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'service'		=>0,
						'branch'		=>0,
						'degree'		=>0,
						'grade'			=>0,
						'room'			=>0,
						'for_month'		=>date("m"),
						'for_year'		=>date("Y"),
				);
			}
		
			$db = new Allreport_Model_DbTable_DbRptPaymentList();
			$this->view->rs = $db->getAllStayAndLunchPaymentList($search);
		
			$this->view->total_student = $db->getAllAmountServiceStudentByType($search,4,5); // 4=lunch payfor_type , 5=lunch type
			
			$this->view->student_drop = $db->getAllAmountStudentDropServiceByType($search,4,null,5); // 4 = lunch payment , 5=lunch type
			$this->view->student_drop_for_month = $db->getAllAmountStudentDropServiceByType($search,4,1,5);
			//echo " , drop = ".count($this->view->student_drop);
			
			$this->view->new_student = $db->getAllAmountNewServiceStudentByType($search,4,null,5); // 1 = khmer fulltime payment , 5=lunch type
			$this->view->new_student_for_month = $db->getAllAmountNewServiceStudentByType($search,4,1,5);
			
			$this->view->amount_student_by_service = $db->getAllAmountStudentByService($search,4,5,5);
			
			$this->view->student_payable_last_month = $db->getStudentPayableLastMonthService($search,4,5,5); // payfor_type = 4 => Lunch , type = 5 => Lunch type
			$this->view->student_payable_this_month = $db->getStudentPayableThisMonthService($search,4,5,5); // payfor_type = 4 => Lunch , type = 5 => Lunch type
			
			
			$this->view->search = $search;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		
		$db = new Allreport_Model_DbTable_DbRptPaymentList();
		$this->view->all_month = $db->getAllMonth();
		
		$db = new Registrar_Model_DbTable_DbStudentLunchPayment();
		$service = $db->getAllLunchService();
		array_unshift($service, array ( 'id' => -1, 'name' => 'Select Service') );
		$this->view->service = $service;
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	 
	
	public function rptBracketServicelistAction(){
	
	}
	
	
////////////////////////////////////////////////////////////// daily income ///////////////////////////////////////////////////////////	
	
	
	
	public function rptDailyIncomeEnglishFulltimeAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'degree_en_ft'	=>0,
						'grade_en_ft'	=>0,
						'room'			=>0,
						'branch'		=>0,
						'user'			=>0,
						'shift'			=>0,
						'from_receipt'	=>'',
						'to_receipt'	=>'',
						'start_date'	=>date("Y-m-d"),
						'end_date'		=>date("Y-m-d"),
				);
			}
			
			$db = new Allreport_Model_DbTable_DbRptDailyIncome();
			$this->view->rs = $db->getDailyIncomeEnglishFulltime($search);
	
			//print_r($this->view->rs);
				
			$this->view->search = $search;
			
			$this->view->rate = $db->getRate();
			
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	
	
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	public function rptDailyIncomeEnglishParttimeAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'degree_gep'	=>0,
						'grade_gep'		=>0,
						'room'			=>0,
						'branch'		=>0,
						'user'			=>0,
						'shift'			=>0,
						'from_receipt'	=>'',
						'to_receipt'	=>'',
						'start_date'	=>date("Y-m-d"),
						'end_date'		=>date("Y-m-d"),
				);
			}
	
			$db = new Allreport_Model_DbTable_DbRptDailyIncome();
			$this->view->rs = $db->getDailyIncomeEnglishParttime($search);
	
			// 			print_r($this->view->rs);
	
			$this->view->search = $search;
				
			$this->view->rate = $db->getRate();
				
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	
	
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	
	public function rptDailyIncomeKhmerFulltimeAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'degree_kh_ft'	=>0,
						'grade_kh_ft'	=>0,
						'room'			=>0,
						'branch'		=>0,
						'user'			=>0,
						'shift'			=>0,
						'from_receipt'	=>'',
						'to_receipt'	=>'',
						'start_date'	=>date("Y-m-d"),
						'end_date'		=>date("Y-m-d"),
				);
			}
	
			$db = new Allreport_Model_DbTable_DbRptDailyIncome();
			$this->view->rs = $db->getDailyIncomeKhmerFulltime($search);
	
			// 			print_r($this->view->rs);
	
			$this->view->search = $search;
				
			$this->view->rate = $db->getRate();
				
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	
	
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	
	public function rptDailyIncomeTransportAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'branch'		=>0,
						'user'			=>0,
						'shift'			=>0,
						'from_receipt'	=>'',
						'to_receipt'	=>'',
						'start_date'	=>date("Y-m-d"),
						'end_date'		=>date("Y-m-d"),
				);
			}
	
			$db = new Allreport_Model_DbTable_DbRptDailyIncome();
			$this->view->rs = $db->getDailyIncomeTransport($search);
	
			// 			print_r($this->view->rs);
	
			$this->view->search = $search;
	
			$this->view->rate = $db->getRate();
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	
	
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	
	public function rptDailyIncomeFoodandstayAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'branch'		=>0,
						'user'			=>0,
						'shift'			=>0,
						'from_receipt'	=>'',
						'to_receipt'	=>'',
						'start_date'	=>date("Y-m-d"),
						'end_date'		=>date("Y-m-d"),
				);
			}
	
			$db = new Allreport_Model_DbTable_DbRptDailyIncome();
			$this->view->rs = $db->getDailyIncomeFoodandstay($search);
	
			// 			print_r($this->view->rs);
	
			$this->view->search = $search;
	
			$this->view->rate = $db->getRate();
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	
	
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	
	public function rptDailyIncomeMaterialAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'branch'		=>0,
						'user'			=>0,
						'shift'			=>0,
						'from_receipt'	=>'',
						'to_receipt'	=>'',
						'start_date'	=>date("Y-m-d"),
						'end_date'		=>date("Y-m-d"),
				);
			}
	
			$db = new Allreport_Model_DbTable_DbRptDailyIncome();
			$this->view->rs = $db->getDailyIncomeMaterial($search);
	
// 						print_r($this->view->rs);
	
			$this->view->search = $search;
	
			$this->view->rate = $db->getRate();
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	
	
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	
	public function rptDailyIncomeParkingCanteenAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'branch'		=>0,
						'user'			=>0,
						'shift'			=>0,
						'start_date'	=>date("Y-m-d"),
						'end_date'		=>date("Y-m-d"),
				);
			}
	
			$db = new Allreport_Model_DbTable_DbRptDailyIncome();
			$this->view->rs = $db->getDailyIncomeParkingCanteen($search);
	
			// 						print_r($this->view->rs);
	
			$this->view->search = $search;
	
			$this->view->rate = $db->getRate();
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	
	
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	
	
	public function rptResultIncomeAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'start_date'=> date('Y-m-d'),
						'end_date'	=>date('Y-m-d'),
						'branch'	=>'',
						'user'		=>'',
						'shift'		=>0,
				);
			}
		
			$db = new Allreport_Model_DbTable_DbRptResultIncome();
			$this->view->kft = $db->getAllResultIncome($search,1);
			$this->view->kft_amount_money = $db->getRielAndDollarAmount($search,1);
			
			$this->view->eft = $db->getAllResultIncome($search,6);
			$this->view->eft_amount_money = $db->getRielAndDollarAmount($search,6);
			
			$this->view->ept = $db->getAllResultIncome($search,2);
			$this->view->ept_amount_money = $db->getRielAndDollarAmount($search,2);
			
			$this->view->car = $db->getAllResultIncome($search,3);
			$this->view->transport_amount_money = $db->getRielAndDollarAmount($search,3);
			
			$this->view->food = $db->getAllResultIncome($search,4);
			$this->view->food_amount_money = $db->getRielAndDollarAmount($search,4);
			
			$this->view->product = $db->getAllResultIncome($search,5);
			$this->view->product_amount_money = $db->getRielAndDollarAmount($search,5);
			
			$this->view->rent_payment = $db->getAllRentPayment($search);
			$this->view->rent_amount_money = $db->getRielAndDollarAmount($search,7);
			
			$this->view->parking_payment = $db->getAllParkingPayment($search);
			$this->view->parking_amount_money = $db->getRielAndDollarAmount($search,8);
			
			$this->view->other_income = $db->getAllOtherIncome($search);
			$this->view->other_income_amount_money = $db->getRielAndDollarAmount($search,9);
			
			$this->view->student_test = $db->getAllStudentTest($search);
			$this->view->student_test_amount_money = $db->getRielAndDollarAmount($search,10);
			
			$this->view->search = $search;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		$db = new Allreport_Model_DbTable_DbRptDailyIncome();
		$this->view->rate = $db->getRate();
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	
	public function rptSummaryTotalIncomeAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'start_date'=> date('Y-m-d'),
						'end_date'	=>date('Y-m-d'),
						'branch'	=>'',
						'user'		=>'',
						'shift'		=>0,
				);
			}
	
			$db = new Allreport_Model_DbTable_DbRptResultIncome();
			
			$this->view->khmerft = $db->getKhmerFullTimePayment($search);
			$this->view->kft_amount_money = $db->getRielAndDollarAmount($search,1);
			
			$this->view->englishft = $db->getEnglishFullTimePayment($search);
			$this->view->eft_amount_money = $db->getRielAndDollarAmount($search,6);
			
			$this->view->englishpt = $db->getEnglishPartTimePayment($search);
			$this->view->ept_amount_money = $db->getRielAndDollarAmount($search,2);
			
			$this->view->study_material = $db->getStudyMaterialPayment($search);
			$this->view->product_amount_money = $db->getRielAndDollarAmount($search,5);
			
			$this->view->transportation = $db->getTransportationPayment($search);
			$this->view->transport_amount_money = $db->getRielAndDollarAmount($search,3);
			
			$this->view->foodandstay = $db->getFoodAndStayPayment($search);
			$this->view->food_amount_money = $db->getRielAndDollarAmount($search,4);
			
			$this->view->rent_payment = $db->getAllRentPayment($search);
			$this->view->rent_amount_money = $db->getRielAndDollarAmount($search,7);
			
			$this->view->parking_payment = $db->getAllParkingPayment($search);
			$this->view->parking_amount_money = $db->getRielAndDollarAmount($search,8);
			
			$this->view->other_income = $db->getAllOtherIncome($search);
			$this->view->other_income_amount_money = $db->getRielAndDollarAmount($search,9);
			
			$this->view->student_test = $db->getAllStudentTest($search);
			$this->view->student_test_amount_money = $db->getRielAndDollarAmount($search,10);
			
			$this->view->search = $search;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			echo $e->getMessage();
		}
	
		$db = new Allreport_Model_DbTable_DbRptDailyIncome();
		$this->view->rate = $db->getRate();
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	
	
	public function rptRentAndPaymentListAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
					
			}else{
				$search = array(
						'title'	        =>	'',
						'branch'	    =>	'',
						'cus_name'		=>	0,
						'start_date'	=>	date('Y-m-01'),
						'end_date'		=>	date('Y-m-d'),
						'status_search'	=> 1
				);
			}
			$this->view->search = $search;
			$db=new Allreport_Model_DbTable_DbRenAndPaymentList();
			$ds=$this->view->cus=$db->getCustomer($search);
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm_major = new Accounting_Form_FrmSearchMajor();
		$frm_search = $frm_major->FrmMajors();
		Application_Model_Decorator::removeAllDecorator($frm_search);
		$this->view->frm_search = $frm_search;
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
	public function rptCusNearlyEndServiceAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search = array(
						'title'	        =>	'',
						'cus_name'		=>	'',
						'end_date'		=>	date('Y-m-d'),
						'status_search'	=> 1
				);
			}
			$this->view->search = $search;
			$db=new Allreport_Model_DbTable_DbRenAndPaymentList();
			$ds=$this->view->cus=$db->getNearlydayEndServiceCustomer($search);
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm_major = new Accounting_Form_FrmSearchMajor();
		$frm_search = $frm_major->FrmMajors();
		Application_Model_Decorator::removeAllDecorator($frm_search);
		$this->view->frm_search = $frm_search;
	}
	 
	
	public function rptStudentDropAction(){
	
		if($this->getRequest()->isPost()){
			$search=$this->getRequest()->getPost();
		}
		else{
			$search=array(
					'title' =>'',
					'study_year' =>'',
					'degree_all' =>'',
					'grade_all' =>'',
					'session' =>'',
					'branch' =>'',
					'user' =>'',
					'start_date'=> date('Y-m-d'),
					'end_date'	=>date('Y-m-d'),
			);
		}
	
		$form=new Registrar_Form_FrmSearchInfor();
		$forms=$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($forms);
		$this->view->form_search=$form;
	
		$group= new Allreport_Model_DbTable_DbRptStudentDrop();
		$this->view->rs = $rs_rows = $group->getAllStudentDrop($search);
		$this->view->search=$search;
	}
	
	public function rptStudentDropTransportAction(){
	
		if($this->getRequest()->isPost()){
			$search=$this->getRequest()->getPost();
		}
		else{
			$search=array(
					'title' =>'',
					'service' =>0,
					'start_date'=> date('Y-m-d'),
					'end_date'	=>date('Y-m-d'),
					'branch' =>'',
			);
		}
	
		$form=new Registrar_Form_FrmSearchInfor();
		$forms=$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($forms);
		$this->view->form_search=$form;
	
		$db= new Allreport_Model_DbTable_DbRptStudentDrop();
		$this->view->rs = $db->getAllStudentDropTransport($search);
		$this->view->search=$search;
		
		$this->view->service = $db->getAllServiceByCategory(3); // 3 = transport service type
		
	}
	
	public function rptStudentDropLunchAndStayAction(){
	
		if($this->getRequest()->isPost()){
			$search=$this->getRequest()->getPost();
		}
		else{
			$search=array(
					'title' =>'',
					'service' =>0,
					'start_date'=> date('Y-m-d'),
					'end_date'	=>date('Y-m-d'),
					'branch' =>'',
			);
		}
	
		$form=new Registrar_Form_FrmSearchInfor();
		$forms=$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($forms);
		$this->view->form_search=$form;
	
		$db= new Allreport_Model_DbTable_DbRptStudentDrop();
		$this->view->rs = $db->getAllStudentDropLunchAndStay($search);
		$this->view->search=$search;
		
		$this->view->service = $db->getAllServiceByCategory(2); // 2 = lunch service type
	}

	
////////////////////////////////////// rpt income and expense //////////////////////////////////////////////////////////////////////////////////////
	
	
	public function rptOtherIncomeAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'branch'	=>'',
						'shift'		=>0,
						'cate_income'=>"",
						'user'		=>'',
						'from_receipt'	=>'',
						'to_receipt'	=>'',
						'start_date'=>date('Y-m-d'),
						'end_date'	=>date('Y-m-d'),
				);
			}
			$db = new Allreport_Model_DbTable_DbRptOtherIncome();
			$abc = $this->view->row = $db->getAllOtherIncome($search);
			
			$_db = new Allreport_Model_DbTable_DbRptOtherExpense();
			$this->view->income_category = $_db->getAllCategory(1);
			//print_r($abc);exit();
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
				
			$this->view->search = $search;
			
			$_db = new Allreport_Model_DbTable_DbRptDailyIncome();
			$this->view->rate = $_db->getRate();
				
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	
	public function rptOtherExpenseAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'branch_id'	=>'',
						'asset_id'	=>'',
						'cate_expense'	=>"",
						'user'	=>'',
						'start_date'=>date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
				);
			}
			$db = new Allreport_Model_DbTable_DbRptOtherExpense();
			$abc = $this->view->row = $db->getAllOtherExpense($search);
			$this->view->expense_category = $db->getAllCategory(0);
			$this->view->fix_asset=$db->getAllFixAssetName();
	
			//print_r($abc);exit();
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
	
			$this->view->search = $search;
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
/////////////////////////////////////////////// payable next month ////////////////////////////////////////////////////////////////////////////////////////////////////	
	
	public function rptPayableNextmonthEngFulltimeAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'branch'		=>'',
						'grade_en_ft'	=>'',
						'degree_en_ft'	=>'',
						'for_month'		=>date('m'),
						'for_year'		=>date('Y'),
				);
			}
			$db = new Allreport_Model_DbTable_DbRptPayableNextMonth();
			$abc = $this->view->row = $db->getAllPayableNextMonth($search,6); // 6=payfor_type(eng fulltme) 
				
			$db = new Allreport_Model_DbTable_DbRptPaymentList();
			$this->view->all_month = $db->getAllMonth();
			
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
				
			$this->view->search = $search;
				
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	public function rptPayableNextmonthKhFulltimeAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' 	=>'',
						'branch'		=>'',
						'grade_kh_ft'	=>'',
						'degree_kh_ft'	=>'',
						'for_month'	=>date('m'),
						'for_year'	=>date('Y'),
				);;
			}
			$db = new Allreport_Model_DbTable_DbRptPayableNextMonth();
			$abc = $this->view->row = $db->getAllPayableNextMonth($search,1); // 1=payfor_type(kh fulltme)
	
			$db = new Allreport_Model_DbTable_DbRptPaymentList();
			$this->view->all_month = $db->getAllMonth();
				
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
	
			$this->view->search = $search;
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	public function rptPayableNextmonthEngParttimeAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'branch'	=>'',
						'degree_gep'=>'',
						'grade_gep'	=>'',
						'service'	=>'',
						'for_month'	=>date('m'),
						'for_year'	=>date('Y'),
				);;
			}
			$db = new Allreport_Model_DbTable_DbRptPayableNextMonth();
			$abc = $this->view->row = $db->getAllPayableNextMonth($search,2); // 2=payfor_type(eng parttime)
	
			$db = new Allreport_Model_DbTable_DbRptPaymentList();
			$this->view->all_month = $db->getAllMonth();
				
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
	
			$this->view->search = $search;
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	public function rptPayableNextmonthTransportAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'branch'	=>'',
						'service'	=>'',
						'for_month'	=>date('m'),
						'for_year'	=>date('Y'),
				);;
			}
			$db = new Allreport_Model_DbTable_DbRptPayableNextMonth();
			$abc = $this->view->row = $db->getAllPayableNextMonth($search,3); // 3=payfor_type(transport)
	
			$db = new Allreport_Model_DbTable_DbRptPaymentList();
			$this->view->all_month = $db->getAllMonth();
	
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
	
			$this->view->search = $search;
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	public function rptPayableNextmonthLunchAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'branch'	=>'',
						'service'	=>'',
						'for_month'	=>date('m'),
						'for_year'	=>date('Y'),
				);;
			}
			$db = new Allreport_Model_DbTable_DbRptPayableNextMonth();
			$abc = $this->view->row = $db->getAllPayableNextMonth($search,4); // 4=payfor_type(lunch)
	
			$db = new Allreport_Model_DbTable_DbRptPaymentList();
			$this->view->all_month = $db->getAllMonth();
	
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
	
			$this->view->search = $search;
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	
	function rptInvoiceParttimeAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'branch'	=>'',
						'degree_gep'=>'',
						'grade_gep'	=>'',
						'start_date'=>date('Y-m-d'),
						'end_date'	=>date('Y-m-d'),
				);
			}
			$db = new Allreport_Model_DbTable_DbRptInvoice();
			$abc = $this->view->row = $db->getAllInvoiceParttime($search); // 4=payfor_type(lunch)
		
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
		
			$this->view->search = $search;
		
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	function rptInvoiceTransportAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'branch'	=>'',
						'transport_service'=>'',
						'start_date'=>date('Y-m-d'),
						'end_date'	=>date('Y-m-d'),
				);
			}
			$db = new Allreport_Model_DbTable_DbRptInvoice();
			$abc = $this->view->row = $db->getAllInvoiceTransport($search); // 4=payfor_type(lunch)
	
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
	
			$this->view->search = $search;
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	function rptParkingPaymentAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'branch'	=>'',
						'shift'		=>'',
						'user'		=>'',
						'from_receipt'	=>'',
						'to_receipt'	=>'',
						'start_date'=>date('Y-m-d'),
						'end_date'	=>date('Y-m-d'),
				);
			}
			$db = new Allreport_Model_DbTable_DbRptParkingPayment();
			$abc = $this->view->row = $db->getAllParkingPayment($search);
			
			$_db = new Allreport_Model_DbTable_DbRptDailyIncome();
			$this->view->rate = $_db->getRate();
			
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
	
			$this->view->search = $search;
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	function rptDailyIncomeStudenttestAction(){
		try{
			$db = new Allreport_Model_DbTable_DbStudenttest();
			if($this->getRequest()->isPost()){
    			$search=$this->getRequest()->getPost();
    		}
    		else{
    			$search = array(
    					'txtsearch'		=>'',
    					'degree'		=>0,
    					'start_date'	=> date('Y-m-d'),
    					'end_date'		=> date('Y-m-d'),
    					'from_receipt'	=>'',
    					'to_receipt'	=>'',
    					'user'			=>'',
    					'branch'		=>'',
    					'shift'			=>0,
    			);
    		}
    		$this->view->search = $search;
    		$rs_rows= $db->getAllStudentTest($search);//call frome model
    		$this->view->row = $rs_rows;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
		
		$db = new Registrar_Model_DbTable_DbStudentTest();
		$this->view->degree = $db->getAllDegreeName();
		
		$_db = new Allreport_Model_DbTable_DbRptDailyIncome();
		$this->view->rate = $_db->getRate();
	}
	
	function rptFixedAssetAction(){
		try{
			$db = new Allreport_Model_DbTable_DbRptFixedAsset();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'txtsearch'=>'',
						'branch'=>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
				);
			}
			$this->view->search = $search;
			$rs_rows= $db->getAllFixedAsset($search);//call frome model
			$this->view->row = $rs_rows;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	
		$db = new Registrar_Model_DbTable_DbStudentTest();
		$this->view->degree = $db->getAllDegreeName();
	
		$_db = new Allreport_Model_DbTable_DbRptDailyIncome();
		$this->view->rate = $_db->getRate();
	}
	
	function rptFixedAssetDetailAction(){
		$id=$this->getRequest()->getParam('id');
		try{
			$db = new Allreport_Model_DbTable_DbRptFixedAsset();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
				$this->view->search = $search;
			}
			else{
				$search = array(
						'txtsearch'=>'',
						'degree'=>0,
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						'user'=>'',
						'branch'=>'',
				);
			}
			$rs_rows= $db->getAllFixedAssetDetail($id);//call frome model
			$this->view->row = $rs_rows;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	function rptSubmitDailyIncomeAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'type'		=>'',
						'branch'	=>'',
						'shift'		=>'',
						'user'		=>'',
						'start_date'=>date('Y-m-d'),
						'end_date'	=>date('Y-m-d'),
				);
			}
			$db = new Allreport_Model_DbTable_DbSubmitDailyIncome();
			$abc = $this->view->row = $db->getAllSubmitDailyIncome($search);
				
			$_db = new Allreport_Model_DbTable_DbRptDailyIncome();
			$this->view->rate = $_db->getRate();
				
			$form=new Registrar_Form_FrmSearchInfor();
			$form->FrmSearchRegister();
			Application_Model_Decorator::removeAllDecorator($form);
			$this->view->form_search=$form;
	
			$this->view->search = $search;
	
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
	}
	
	
	
	
}
