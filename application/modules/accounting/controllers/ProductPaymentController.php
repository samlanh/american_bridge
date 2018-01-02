<?php
class Accounting_ProductPaymentController extends Zend_Controller_Action {
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
    		$db = new Accounting_Model_DbTable_DbProductPayment();
    		    		if($this->getRequest()->isPost()){
    		    			$search=$this->getRequest()->getPost();
    		    			//print_r($search);exit();
    		    		}
    		    		else{
    		    			$search = array(
    		    					'adv_search' => '',
    		    					'year' => '',
    		    					'user' => '',
    		    					'start_date'=> date('Y-m-d'),
    								'end_date'=>date('Y-m-d'),
    		    					);
    		    		}
    		$this->view->adv_search=$search;
    		$rs_rows= $db->getAllProductPayment($search);
    		$list = new Application_Form_Frmtable();
    		$collumns = array("BRANCH","NAME","SEX","RECEIPT_NO","TOTAL_PAYMENT","PAID_AMOUNT","DATE_PAY","USER","STATUS");
    				         
    		$link=array(
    				'module'=>'accounting','controller'=>'productpayment','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('branch'=>$link,'receipt_number'=>$link,'name'=>$link,'service_name'=>$link,'code'=>$link));
    	}catch (Exception $e){
    		//Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		echo $e->getMessage();
    	}
    	$forms=new Registrar_Form_FrmSearchInfor();
    	$form=$forms->FrmSearchRegister();
    	Application_Model_Decorator::removeAllDecorator($form);
    	$this->view->form_search=$form;
    	
//     	$_db = new Accounting_Model_DbTable_DbProductPayment();
//     	$this->view->year = $year = $_db->getYearService();
    	
    }
    
    public function addAction()
    {
    	if($this->getRequest()->isPost()){
      	$_data = $this->getRequest()->getPost();
      	try {
      		$db = new Accounting_Model_DbTable_DbProductPayment();
      		$db->addProductPayment($_data);
      		if(isset($_data['save_new'])){
      			Application_Form_FrmMessage::message($this->tr->translate('INSERT_SUCCESS'));
      		}else{
      			Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL . '/productpayment/index');
      		}
      	} catch (Exception $e) {
      		Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
      		echo $e->getMessage();
      	}
      }
       $frm = new Registrar_Form_FrmUniformAndBook();
       $frm_unifrom_and_book=$frm->FrmRegistarWU();
       Application_Model_Decorator::removeAllDecorator($frm_unifrom_and_book);
       $this->view->frm_unifrom_and_book = $frm_unifrom_and_book;
       $key = new Application_Model_DbTable_DbKeycode();
       $this->view->keycode=$key->getKeyCodeMiniInv(TRUE);
       $model = new Application_Form_FrmGlobal();
      
       $db = new Accounting_Model_DbTable_DbProductPayment();
       $this->view->rs = $db->getAllStudentCode();
       $this->view->row = $db->getAllStudentName();
       
       $_db = new Registrar_Model_DbTable_DbRegister();
       $this->view->exchange_rate = $_db->getExchangeRate();
       
       $db = new Accounting_Model_DbTable_DbProductPayment();
       $this->view->all_service = $db->getAllServiceItemOption();
       
       $_db = new Application_Model_DbTable_DbGlobal();
       $this->view->branch = $_db->getAllBranch();
       
    }
    
    public function editAction()
    {
    	$id=$this->getRequest()->getParam('id');
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
     		$_data['payment_id']=$id;
     		
     		//print_r($_data);exit();
     		
    		try {
    			$db = new Accounting_Model_DbTable_DbProductPayment();
    			$db->updateStudentServicePayment($_data);
    			if(isset($_data['save_close'])){
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL . '/productpayment/index');
    			}else{
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL . '/productpayment/index');
    			}
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
    			$err =$e->getMessage();
    			Application_Model_DbTable_DbUserLog::writeMessageError($err);
    		}
    	}
    	
    	$db = new Accounting_Model_DbTable_DbProductPayment();
    	$this->view->row=$db->getStudentServicePaymentByID($id);
    	
    	$payment=$db->getStudentServicePaymentDetailByID($id);
    	$this->view->rows = $payment;
    	
//     	print_r($payment);exit();
    	
    	$frm = new Registrar_Form_FrmUniformAndBook();
    	$frm_register=$frm->FrmRegistarWU($payment);
    	Application_Model_Decorator::removeAllDecorator($frm_register);
    	$this->view->frm_register = $frm_register;
    	$key = new Application_Model_DbTable_DbKeycode();
    	$this->view->keycode=$key->getKeyCodeMiniInv(TRUE);
    	
    	$db = new Accounting_Model_DbTable_DbProductPayment();
    	$this->view->rs = $db->getAllStudentCode();
    	$this->view->row_name = $db->getAllStudentName();
    	
    	$db = new Accounting_Model_DbTable_DbProductPayment();
        $this->view->all_service = $db->getAllServiceItemOption();
        
        $_db = new Application_Model_DbTable_DbGlobal();
        $this->view->branch = $_db->getAllBranch();
    }
    
    function getGradeAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbRegister();
    		$grade = $db->getAllGrade($data['dept_id']);
    		print_r(Zend_Json::encode($grade));
    		exit();
    	}
    }
    
//     function getPriceAction(){
//     	if($this->getRequest()->isPost()){
//     		$data=$this->getRequest()->getPost();
//     		$db = new Accounting_Model_DbTable_DbProductPayment();
//     		$price = $db->getAllpriceByServiceTerm($data['studentid'],$data['service'],$data['term'],$data['year']);
//     		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
//     		print_r(Zend_Json::encode($price));
//     		exit();
//     	}
//     }
    
//     function getPriceEditAction(){
//     	if($this->getRequest()->isPost()){
//     		$data=$this->getRequest()->getPost();
//     		$db = new Accounting_Model_DbTable_DbProductPayment();
//     		$price = $db->getAllpriceByServiceTermEdit($data['service'],$data['term'],$data['year']);
//     		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
//     		print_r(Zend_Json::encode($price));
//     		exit();
//     	}
//     }
    
    function getStudentAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbProductPayment();
    		$studentinfo = $db->getAllStudentInfo($data['studentid']);
    		print_r(Zend_Json::encode($studentinfo));
    		exit();
    	}
    }
    
    function getServiceAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbProductPayment();
    		$year = $db->getAllService($data['year']);
    		print_r(Zend_Json::encode($year));
    		exit();
    	}
    }
    
    function getStudentIdAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbProductPayment();
    		$year = $db->getStudentID($data['study_year'],$data['type']);
    		print_r(Zend_Json::encode($year));
    		exit();
    	}
    }
    
    function getPriceAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbProductPayment();
    		$price = $db->getPrice($data['service_price']);
    		print_r(Zend_Json::encode($price));
    		exit();
    	}
    }
    
    
    function getStudentByBranchAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbProductPayment();
    		$price = $db->getStudentByBranch($data['branch_id']);
    		print_r(Zend_Json::encode($price));
    		exit();
    	}
    }
    
    
    
    
}








