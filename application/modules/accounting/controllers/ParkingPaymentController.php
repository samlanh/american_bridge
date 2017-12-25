<?php
class Accounting_ParkingPaymentController extends Zend_Controller_Action {
	protected $tr;
	public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
	}
	
	public function indexAction(){
		try{
			if($this->getRequest()->isPost()){
    			$search = $this->getRequest()->getPost();
    		}
    		else{
    			$search=array(
    					'title'	        =>	'',
    					'cus_name'		=>	'',
    					'start_date'	=>	date("Y-m-d"),
    					'end_date'		=>	date("Y-m-d"),
    					'branch'		=> ""
				);
    		}
			$db = new Accounting_Model_DbTable_DbParkingPayment();
			$rs_rows = $db->getAllParkingPayment($search);
			
			$this->view->old_cus = $db->getAllCustomerName();
			
			$this->view->search = $search;
			
			$list = new Application_Form_Frmtable();
    		$collumns = array("RECEIPT_NO","CUS_ID","NAME","PHONE","ថ្លៃកិបសំបុត្រម៉ូតូ","ថ្លៃកិបសំបុត្រកង់","លក់អេតចាយ","TOTAL","NOTE","USER","CREATE_DATE");
    		$link=array(
    				'module'=>'accounting','controller'=>'parkingpayment','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0, $collumns, $rs_rows , array('customer_code'=>$link,'receipt_no'=>$link,'name'=>$link,'phone'=>$link));
			
		}catch (Exception $e){
			echo $e->getMessage();
		}
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	
    public function addAction()
    {
    	$db = new Accounting_Model_DbTable_DbParkingPayment();
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		try {
    			$db->addParkingPayment($_data);
    			if(!empty($_data['save_new'])){
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/accounting/parkingpayment/add");
    			}else{
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/accounting/parkingpayment/index");
    			}
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
    			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    			echo $e->getMessage();
    		}
    	}
    	
    	$this->view->cus_id=$db->getCusId();
    	$this->view->receipt_no=$db->getReceiptNo();
    	$this->view->cus=$db->getOldCustomer();
    	$this->view->reil=$db->getReilMoney();
    	
    	$_db = new Registrar_Model_DbTable_DbRegister();
    	$this->view->branch = $_db->getAllBranch();
    }
    
	public function editAction(){
    	$db = new Accounting_Model_DbTable_DbParkingPayment();
    	$id=$this->getRequest()->getParam('id');
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		$_data['id']=$id;
    		try {
    			$db->editCustomerPayment($_data);
    			if(!empty($_data['save_new'])){
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/accounting/parkingpayment/index");
    			}else{
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/accounting/parkingpayment/index");
    			}
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
    			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    			echo $e->getMessage();
    		}
    	}
    	$row=$this->view->row=$db->getParkingById($id);
    	
    	$this->view->cus_id=$db->getCusId();
    	$this->view->receipt_no=$db->getReceiptNo();
    	$this->view->cus=$db->getOldCustomer();
    	$this->view->reil=$db->getReilMoney();
    	
    	$_db = new Registrar_Model_DbTable_DbRegister();
    	$this->view->branch = $_db->getAllBranch();
    }
    
    public function viewAction(){
    	$db = new Accounting_Model_DbTable_DbParkingPayment();
    	$id=$this->getRequest()->getParam('id');
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		$_data['id']=$id;
    		try {
    			$db->editCustomerPayment($_data);
    			if(!empty($_data['save_new'])){
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/registrar/customerpayment/index");
    			}else{
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/registrar/customerpayment/index");
    			}
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
    			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    			echo $e->getMessage();
    		}
    	}
    	$row=$this->view->row=$db->getCustomerById($id);
//     	if($row['last_piad']==0){
//     		Application_Form_FrmMessage::Sucessfull($this->tr->translate('Can not edit.!!!'), "/accounting/customerpayment/index");
//     	}
    	$this->view->cus_id=$db->getCusId();
    	$this->view->receipt_no=$db->getReceiptNo();
    	$this->view->cus=$db->getOldCustomer();
    	$this->view->reil=$db->getReilMoney();
    	
    	$_db = new Registrar_Model_DbTable_DbRegister();
    	$this->view->branch = $_db->getAllBranch();
    }
    
    function getCustomerInfoAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbParkingPayment();
    		$cus_info= $db->getCustomerInfo($data['cus_id']);
    		print_r(Zend_Json::encode($cus_info));
    		exit();
    	}
    }
    
    function getOldCustomerByBranchAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbParkingPayment();
    		$old_cus = $db->getOldCustomerByBranch($data['branch_id']);
    		print_r(Zend_Json::encode($old_cus));
    		exit();
    	}
    }
    
    function getCustomerIdByBranchAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbParkingPayment();
    		$cus_id= $db->getCusIdByBranch($data['branch_id']);
    		print_r(Zend_Json::encode($cus_id));
    		exit();
    	}
    }
    
    function getReceiptByBranchAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbParkingPayment();
    		$cus_id= $db->getReceiptByBranch($data['branch_id']);
    		print_r(Zend_Json::encode($cus_id));
    		exit();
    	}
    }
    
    
}
