<?php
class Accounting_CustomerPaymentController extends Zend_Controller_Action {
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
    					'title'	        =>'',
    					'cus_name'		=>0,
    					'status_search'	=> 1,
    					'start_date'	=> date("Y-m-d"),
    					'end_date'		=> date("Y-m-d"),
    					'branch'		=> "",
				);
    		}
			$db = new Accounting_Model_DbTable_DbCustomerPayment();
			$rs_rows = $db->getAllCustomer($search);
			
			$list = new Application_Form_Frmtable();
    		$collumns = array("BRANCH","RECEIPT_NO","CUS_ID","CUS_NAME","PHONE","EMAIL","START_DATE","END_DATE","STATUS_PAID","USER","CREATE_DATE","STATUS");
    		$link=array(
    				'module'=>'accounting','controller'=>'customerpayment','action'=>'edit',
    		);
    		$link1=array(
    				'module'=>'accounting','controller'=>'customerpayment','action'=>'view',
    		);
    		$this->view->list=$list->getCheckList(0, $collumns, $rs_rows , array('customer_code'=>$link,'rent_receipt_no'=>$link,'first_name'=>$link,'phone'=>$link,'view'=>$link1));
			
		}catch (Exception $e){
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
	
    public function addAction()
    {
    	$db = new Accounting_Model_DbTable_DbCustomerPayment();
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		try {
    			$db->addCusPayment($_data);
    			if(!empty($_data['save_new'])){
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/accounting/customerpayment/add");
    			}else{
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/accounting/customerpayment/index");
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
    	$db = new Accounting_Model_DbTable_DbCustomerPayment();
    	$id=$this->getRequest()->getParam('id');
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		$_data['id']=$id;
    		try {
    			$db->editCustomerPayment($_data);
    			if(!empty($_data['save_new'])){
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/accounting/customerpayment/index");
    			}else{
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/accounting/customerpayment/index");
    			}
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
    			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    			echo $e->getMessage();
    		}
    	}
    	$row=$this->view->row=$db->getCustomerById($id);
    	if($row['last_piad']==0){
    		Application_Form_FrmMessage::Sucessfull($this->tr->translate('Can not edit.!!!'), "/accounting/customerpayment/index");
    	}
    	$this->view->cus_id=$db->getCusId();
    	$this->view->receipt_no=$db->getReceiptNo();
    	$this->view->cus=$db->getOldCustomer();
    	$this->view->reil=$db->getReilMoney();
    	
    	$_db = new Registrar_Model_DbTable_DbRegister();
    	$this->view->branch = $_db->getAllBranch();
    }
    
    public function viewAction(){
    	$db = new Accounting_Model_DbTable_DbCustomerPayment();
    	$id=$this->getRequest()->getParam('id');
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		$_data['id']=$id;
    		try {
    			$db->editCustomerPayment($_data);
    			if(!empty($_data['save_new'])){
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/accounting/customerpayment/index");
    			}else{
    				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), "/accounting/customerpayment/index");
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
    }
    
    function getCustomerInfoAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbCustomerPayment();
    		$cus_info= $db->getCustomerInfo($data['cus_id']);
    		print_r(Zend_Json::encode($cus_info));
    		exit();
    	}
    }
    
    function getOldCustomerByBranchAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbCustomerPayment();
    		$old_cus = $db->getOldCustomerByBranch($data['branch_id']);
    		print_r(Zend_Json::encode($old_cus));
    		exit();
    	}
    }
    
    function getCustomerIdByBranchAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbCustomerPayment();
    		$cus_id= $db->getCusIdByBranch($data['branch_id']);
    		print_r(Zend_Json::encode($cus_id));
    		exit();
    	}
    }
    
    function getReceiptByBranchAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Accounting_Model_DbTable_DbCustomerPayment();
    		$cus_id= $db->getReceiptByBranch($data['branch_id']);
    		print_r(Zend_Json::encode($cus_id));
    		exit();
    	}
    }
}
