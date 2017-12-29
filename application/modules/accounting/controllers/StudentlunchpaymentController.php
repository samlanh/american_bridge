<?php
class Accounting_StudentlunchpaymentController extends Zend_Controller_Action {
	protected $tr;
	const REDIRECT_URL ='/accounting';
	public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
    public function indexAction()
    {
    	try{
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		    		if($this->getRequest()->isPost()){
    		    			$search=$this->getRequest()->getPost();
    		    			//print_r($search);exit();
    						
    		    		}
    		    		else{
    		    			$search = array(
    		    					'adv_search' => '',
    		    					'branch' => '',
    		    					'user' => '',
    		    					'start_date'=> date('Y-m-d'),
    		    					'end_date'=>date('Y-m-d'));
    		    		}
    		$this->view->adv_search=$search;
    		$rs_rows= $db->getAllStudenTServicePayment($search);
    		$list = new Application_Form_Frmtable();
    		$collumns = array("BRANCH","STUDENT_ID","NAME","SEX","RECEIPT_NO","SUBTOTAL","PAID_AMOUNT","BALANCE","DATE_PAY","USER","STATUS");
    		$link=array(
    				'module'=>'accounting','controller'=>'studentlunchpayment','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('year'=>$link,'receipt_number'=>$link,'name'=>$link,'service_name'=>$link,'code'=>$link));
    	}catch (Exception $e){
    		//Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		echo $e->getMessage();
    	}
    	$forms=new Registrar_Form_FrmSearchInfor();
    	$form=$forms->FrmSearchRegister();
    	Application_Model_Decorator::removeAllDecorator($form);
    	$this->view->form_search=$form;
    	
    	$_db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    	$this->view->year = $year = $_db->getYearService();
    	
    }
    public function addAction()
    {
    if($this->getRequest()->isPost()){
      	$_data = $this->getRequest()->getPost();
      	try {
      		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
      		$exist = $db->addStudentLunchPayment($_data);
      		if($exist==-1){
      			Application_Form_FrmMessage::message("RECORD_EXIST");
      		}else{
	      		if(isset($_data['save_new'])){
	      			Application_Form_FrmMessage::message($this->tr->translate('INSERT_SUCCESS'));
	      		}else{
	      			Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL . '/studentlunchpayment/index');
	      		}
      		}
      	} catch (Exception $e) {
      		Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
      		$err =$e->getMessage();
      		Application_Model_DbTable_DbUserLog::writeMessageError($err);
      	}
      }
       $frm = new Accounting_Form_FrmStudentServicePayment();
       $frm_register=$frm->FrmRegistarWU();
       Application_Model_Decorator::removeAllDecorator($frm_register);
       $this->view->frm_register = $frm_register;
       $key = new Application_Model_DbTable_DbKeycode();
       $this->view->keycode=$key->getKeyCodeMiniInv(TRUE);
       $model = new Application_Form_FrmGlobal();
      
       $db = new Application_Model_DbTable_DbGlobal();
       $abc=$this->view->payment_term = $db->getAllPaymentTerm(null,null,null);
       $this->view->branch_id = $db->getAllBranch();
       //print_r($abc);exit();
       
       $db = new Accounting_Model_DbTable_DbStudentLunchPayment();
       $this->view->rs = $db->getAllStudentCode();
       $this->view->row = $db->getAllStudentName();
       $service = $db->getAllLunchService();
       array_unshift($service, array ( 'id' => -2, 'name' => 'បន្ថែមថ្មី') );
       array_unshift($service, array ( 'id' => -1, 'name' => 'Select Service') );
       $this->view->service = $service;
       
       $servicetype = $db->getAllServiceType();
       array_unshift($servicetype, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
       array_unshift($servicetype, array ( 'id' => '', 'name' => 'Select Service') );
       $this->view->service_type = $servicetype;
       
       $db = new Registrar_Model_DbTable_DbRegister();
       $this->view->all_product = $db->getAllProduct();
       $this->view->exchange_rate = $db->getExchangeRate();
//        $_model = new Application_Model_GlobalClass();
//        $this->view->all_service = $_model->getAllServiceItemOption(2); /// for use in add row
       //$session_user=new Zend_Session_Namespace('auth'); $username = $session_user->first_name;
    }
    public function editAction()
    {
    	$id=$this->getRequest()->getParam('id');
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
     		$_data['payment_id']=$id;
     		
//      		print_r($_data);exit();
     		
    		try {
    			$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    			$db->updateStudentLunchPayment($_data);
    			if(isset($_data['save_close'])){
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL . '/studentlunchpayment/index');
    			}else{
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL . '/studentlunchpayment/index');
    			}
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
    			$err =$e->getMessage();
    			Application_Model_DbTable_DbUserLog::writeMessageError($err);
    			echo $err;
    		}
    	}
    	
    	$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    	$payment=$this->view->row=$db->getStudentServicePaymentByID($id);
    	if($payment['buy_product']==1){
    		$this->view->row_product = $db->getStudentBuyProductById($id);
    	}
    	
    	$payment_detail=$db->getStudentServicePaymentDetailByID($id);
//     	print_r($payment);exit();
    	$this->view->detail = $payment_detail;
    	
//     	print_r($payment);exit();
    	
    	$frm = new Accounting_Form_FrmStudentServicePayment();
    	$frm_register=$frm->FrmRegistarWU($payment);
    	Application_Model_Decorator::removeAllDecorator($frm_register);
    	$this->view->frm_register = $frm_register;
    	$key = new Application_Model_DbTable_DbKeycode();
    	$this->view->keycode=$key->getKeyCodeMiniInv(TRUE);
    	
    	$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    	$this->view->rs = $db->getAllStudentCode();
    	$this->view->row_name = $db->getAllStudentName();
    	$service = $db->getAllLunchService();
    	array_unshift($service, array ( 'id' => -1, 'name' => 'Select Service') );
    	$this->view->service = $service;
    	
    	$this->view->old_stu_name = $db->getAllOldStudentName();
    	$this->view->old_car_id = $db->getAllOldLunchId();
    	
    	$db = new Application_Model_DbTable_DbGlobal();
    	$abc=$this->view->payment_term = $db->getAllPaymentTerm();
    	$this->view->branch_id = $db->getAllBranch();
    	
    	$db = new Registrar_Model_DbTable_DbRegister();
    	$this->view->all_product = $db->getAllProduct();
    	$this->view->exchange_rate = $db->getExchangeRate();
    	
    }
    
    
    function getPriceAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$price = $db->getAllpriceByServiceTerm($data['studentid'],$data['service'],$data['term']);
    		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($price));
    		exit();
    	}
    }
    
    function getPriceEditAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$price = $db->getAllpriceByServiceTermEdit($data['service'],$data['term'],$data['year']);
    		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($price));
    		exit();
    	}
    }
    
    function getStudentAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$studentinfo = $db->getAllStudentInfo($data['studentid']);
    		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($studentinfo));
    		exit();
    	}
    }
    
    
    function getServiceAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$year = $db->getAllService($data['year']);
    		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($year));
    		exit();
    	}
    }
    function getStudentIdAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$year = $db->getStudentID($data['study_year'],$data['type']);
    		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($year));
    		exit();
    	}
    }
    
    function addPopupLunchServiceAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$service = $db->addService($data);
    		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($service));
    		exit();
    	}
    }
	
	function getServiceCateAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$service_cate = $db->getServiceCate($data['service_id']);
    		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($service_cate));
    		exit();
    	}
    }
    
    function getStartDateServiceAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$service_start_date = $db->getServiceStartDate($data['service_id'],$data['stu_id']);
    		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($service_start_date));
    		exit();
    	}
    }
    
    function getNewLunchIdAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$new_car_id = $db->getNewLunchId();
    		print_r(Zend_Json::encode($new_car_id));
    		exit();
    	}
    }
    
    
    
    function getNewStudentByBranchAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$new_student = $db->getAllNewStudentName($data['branch_id']);
    		print_r(Zend_Json::encode($new_student));
    		exit();
    	}
    }
    
    function getOldStudentByBranchAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$old_student = $db->getAllOldStudent($data['branch_id']);
    		print_r(Zend_Json::encode($old_student));
    		exit();
    	}
    }
    
    function getDropStudentByBranchAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbStudentLunchPayment();
    		$drop_student = $db->getAllDropStudent($data['branch_id']);
    		print_r(Zend_Json::encode($drop_student));
    		exit();
    	}
    }
    
    
    
    
    
    
    
}







