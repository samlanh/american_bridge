<?php
class Registrar_RegisterController extends Zend_Controller_Action {
	protected $tr;
	const REDIRECT_URL ='/registrar';
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	
	}
    public function indexAction(){
    	try{
    		$db = new Registrar_Model_DbTable_DbRegister();
    		if($this->getRequest()->isPost()){
    			$search=$this->getRequest()->getPost();
    		}
    		else{
    		    $search = array(
    		    			'adv_search' => '',
    		    			'study_year' => '',
    		    			'degree_ft' => '',
    		    			'time'   =>'', 
    		    			'session'=>'',
    		    			'grade_ft'=>'',
    		    			'user'=>'',
    		    			'start_date'=> date('Y-m-d'),
    		    			'end_date'=>date('Y-m-d')
    		    		);
    		}
    		
    		$this->view->adv_search=$search;
    		$rs_rows= $db->getAllStudentRegister($search);
    		$glClass = new Application_Model_GlobalClass();
    		$rs_rows = $glClass->getGernder($rs_rows, BASE_URL );
    		$list = new Application_Form_Frmtable();
    		$collumns = array("STUDENT_ID","NAME_KH","NAME_EN","SEX","DEGREE","CLASS","RECEIPT_NO",
    				          "SUBTOTAL","PAID_AMOUNT","BALANCE","DATE_PAY","USER","STATUS");
    		$link=array(
    				'module'=>'registrar','controller'=>'register','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('stu_code'=>$link,'receipt_number'=>$link,'stu_khname'=>$link,'stu_enname'=>$link));
    	}catch (Exception $e){
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$data = new Registrar_Model_DbTable_DbRegister();
    	$db=$this->view->rows_degree=$data->getDegree();
    	
    	$form=new Registrar_Form_FrmSearchInfor();
    	$form->FrmSearchRegister();
    	Application_Model_Decorator::removeAllDecorator($form);
    	$this->view->form_search=$form;
    }
  
    public function addAction(){
      if($this->getRequest()->isPost()){
      	$_data = $this->getRequest()->getPost();
      	//print_r($_data);exit();
      	try {
      		$db = new Registrar_Model_DbTable_DbRegister();
      		$db->addRegister($_data);
      		if(isset($_data['save_new'])){
      			Application_Form_FrmMessage::message($this->tr->translate('INSERT_SUCCESS'));
      		}else{
      			Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL . '/register/index');
      		}
      	} catch (Exception $e) {
      		Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
      		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
      		echo $e->getMessage();
      	}
      }
       $frm = new Registrar_Form_FrmRegister();
       $frm_register=$frm->FrmRegistarWU();
       Application_Model_Decorator::removeAllDecorator($frm_register);
       $this->view->frm_register = $frm_register;
       $_db = new Application_Model_DbTable_DbGlobal();
       $data=$this->view->all_dept = $_db->getAllFecultyNamess(1);
       
       $db = new Registrar_Model_DbTable_DbRegister();
       $this->view->all_product = $db->getAllProduct();
       $this->view->exchange_rate = $db->getExchangeRate();
       $this->view->room = $db->getAllRoom();
       
       $dbg = new Application_Model_DbTable_DbGlobal();
       $this->view->branch_info = $dbg->getBranchInfo();
       $this->view->all_time = $dbg->getAllTime();
       
       $key = new Application_Model_DbTable_DbKeycode();
       $this->view->data=$key->getKeyCodeMiniInv(TRUE);
    }
    public function addoldreceiptAction(){
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		try {
    			$db = new Registrar_Model_DbTable_DbRegister();
    			$db->addRegister($_data);
    			if(isset($_data['save_new'])){
    				Application_Form_FrmMessage::message($this->tr->translate('INSERT_SUCCESS'));
    			}else{
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL . '/register/index');
    			}
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
    			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    			echo $e->getMessage();
    		}
    	}
    	$frm = new Registrar_Form_FrmRegister();
    	$frm_register=$frm->FrmRegistarWU();
    	Application_Model_Decorator::removeAllDecorator($frm_register);
    	$this->view->frm_register = $frm_register;
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$data=$this->view->all_dept = $_db->getAllFecultyNamess(1);
    	 
    	$db = new Registrar_Model_DbTable_DbRegister();
    	$this->view->all_product = $db->getAllProduct();
    	$this->view->exchange_rate = $db->getExchangeRate();
    	$this->view->room = $db->getAllRoom();
    	
    }
    public function editAction(){
    	$id=$this->getRequest()->getParam('id');
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		$_data['pay_id']=$id;
//     		print_r($_data);exit();
    		try {
    			$db = new Registrar_Model_DbTable_DbRegister();
    			$db->updateRegister($_data);
    			if(isset($_data['save_new'])){
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('EDIT_SUCCESS'), self::REDIRECT_URL . '/register/index');
    			}else{
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('EDIT_SUCCESS'), self::REDIRECT_URL . '/register/index');
    			}
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
    			$err =$e->getMessage();
    			Application_Model_DbTable_DbUserLog::writeMessageError($err);
    		}
    	}
    	$db = new Registrar_Model_DbTable_DbRegister();
        $form_row=$db->getRegisterById($id);
        $this->view->all_product = $db->getAllProduct();
        $this->view->exchange_rate = $db->getExchangeRate();
        
        $is_start=$form_row['is_start'];
        if($is_start==0){
        	Application_Form_FrmMessage::Sucessfull($this->tr->translate('Can note Edit'), self::REDIRECT_URL . '/register/index');
        }
        $this->view->degree_row=$form_row;
    	$frm = new Registrar_Form_FrmRegister();
    	$frm_register=$frm->FrmRegistarWU($form_row);
    	Application_Model_Decorator::removeAllDecorator($frm_register);
    	$this->view->frm_register = $frm_register;

    	$_db = new Application_Model_DbTable_DbGlobal();
    	$this->view->all_dept = $_db->getAllFecultyNamess(1);
    	
    	$db = new Accounting_Model_DbTable_DbRegister();
    	$this->view->room = $db->getAllRoom();
    	if($form_row['buy_product']==1){
    		$this->view->row_product = $db->getStudentBuyProductById($id);
    	}
    	
    	$dbg = new Application_Model_DbTable_DbGlobal();
    	$this->view->branch_info = $dbg->getBranchInfo();
    	$this->view->all_time = $dbg->getAllTime();
    	
    	$key = new Application_Model_DbTable_DbKeycode();
    	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
    }
    public function oldaddAction()
    {
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		$_model = new Registrar_Model_DbTable_DbwuRegister();
    		//print_r($_data);exit();
    		$_model->AddNewStudent($_data);
    	}
    	$frm = new Registrar_Form_FrmRegister();
    	$frm_register=$frm->FrmRegistarWU();
    	Application_Model_Decorator::removeAllDecorator($frm_register);
    	$this->view->frm_register = $frm_register;
    	//        $_marjor =array();
    	//        $this->view->marjorlist = $_marjor;
    	//        $model = new Application_Model_DbTable_DbGlobal();
    	//        $_marjorlist = $model->getMarjorById();
    	 
    	//        $this->view->marjorlist = $_marjorlist;
    	 
    	$key = new Application_Model_DbTable_DbKeycode();
    	$this->view->keycode=$key->getKeyCodeMiniInv(TRUE);
    	$model = new Application_Form_FrmGlobal();
    	$this->view->footer=$model->getReceiptFooter();
    	 
    	$this->view->invoice_no = Application_Model_GlobalClass::getInvoiceNo();
    	// echo Application_Model_GlobalClass::getInvoiceNo();
    	 
    	$__student_card = array();
    	$this->view->student_card = $__student_card;
    	$db = new Registrar_Model_DbTable_DbwuRegister();
    	$this->view->invoice_num = $db->getGaneratInvoiceWU();
    	// echo $db->getGaneratInvoiceWU();
    }
    public function wuReceiptAction()
    {
    	$frm = new Registrar_Form_FrmRecept();
    	$frm_recept=$frm->FrmRecept();
    	Application_Model_Decorator::removeAllDecorator($frm_recept);
    	$this->view->frm_recept = $frm_recept;
    	//$key = new Application_Model_DbTable_DbKeycode();
    	//$this->view->keycode=$key->getKeyCodeMiniInv(TRUE);
    }
    public function getStudentinfoallAction(){
    	if($this->getRequest()->isPost()){
    		$_db = new Registrar_Model_DbTable_DbGetStudentInfo();
    		$_rs_student = $_db->getAllStudent();
    		print_r(Zend_Json::encode($_rs_student));
    		exit();
    	}
    }
    function getGradeAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbRegister();
    		$grade = $db->getAllGrade($data['dept_id']);
    		//print_r($grade);exit();
    		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($grade));
    		exit();
    	}
    }
    function getStuNoAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbRegister();
    		$stu_no = $db->getNewAccountNumber($data['dept_id'],$data['branch_id']);
    		print_r(Zend_Json::encode($stu_no));
    		exit();
    	}
    }
    function getPaymentTermAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbRegister();
    		$payment = $db->getPaymentTerm($data['generat_id'],$data['pay_id'],$data['grade_id']);
    		//print_r($grade);exit();
    		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($payment));
    		exit();
    	}
    }
    function getBanlancePriceAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbRegister();
    		$payment = $db->getBalance($data['servce_id'],$data['student_id'],$data['type']);
    		print_r(Zend_Json::encode($payment));
    		exit();
    	}
    }
    function reciptAction(){
    	
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		try {
    			$db = new Registrar_Model_DbTable_DbRegister();
    			if(isset($_data['save_new'])){
    				$db->addRegister($_data);
    				Application_Form_FrmMessage::message($this->tr->translate('INSERT_SUCCESS'));
    			}else{
    				$db->addRegister($_data);
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL . '/register/index');
    			}
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
    			$err =$e->getMessage();
    			Application_Model_DbTable_DbUserLog::writeMessageError($err);
    		}
    	}
    	$frm = new Registrar_Form_FrmRegister();
    	$frm_register=$frm->FrmRegistarWU();
    	Application_Model_Decorator::removeAllDecorator($frm_register);
    	$this->view->frm_register = $frm_register;
    	$key = new Application_Model_DbTable_DbKeycode();
    	$this->view->keycode=$key->getKeyCodeMiniInv(TRUE);
    	$model = new Application_Form_FrmGlobal();
    	$this->view->footer=$model->getReceiptFooter();
    	$this->view->invoice_no = Application_Model_GlobalClass::getInvoiceNo();
    	$__student_card = array();
    	$this->view->student_card = $__student_card;
    	$db = new Registrar_Model_DbTable_DbwuRegister();
    	//$this->view->invoice_num = $db->getGaneratInvoiceWU();
    	//get all dept
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$this->view->all_dept = $_db->getAllFecultyNamess(1);
    	
    	
    }
    function getGeneralOldStudentAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbRegister();
    		$general = $db->getGeneralOldStudentById($data['student_id']);
    		//print_r($grade);exit();
    		//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
    		print_r(Zend_Json::encode($general));
    		exit();
    	}
    }
	function getreceiptAction(){
    	if($this->getRequest()->isPost()){
    	   $reciept=new Registrar_Model_DbTable_DbRegister();
	       $opt=$reciept->getRecieptNo();
		   $opt="44444";
    	   print_r(Zend_Json::encode($opt));
    	   exit();
    	}
    }
	
    function getStartDateRegisterAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbRegister();
    		$validate = $db->getStartDate($data['service_id'],$data['stu_id']);
    		print_r(Zend_Json::encode($validate));
    		exit();
    	}
    }
    
    function getProductPriceAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbRegister();
    		$product_price = $db->getProductPrice($data['product']);
    		print_r(Zend_Json::encode($product_price));
    		exit();
    	}
    }
    
    function getDegreeTypeAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbRegister();
    		$degree_type = $db->getDegreeType($data['degree']);
    		print_r(Zend_Json::encode($degree_type));
    		exit();
    	}
    }
	
}
