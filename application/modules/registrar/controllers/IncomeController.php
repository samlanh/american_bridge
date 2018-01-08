<?php

class Registrar_IncomeController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/registrar/expense';
	
    public function init()
    {
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }

    public function indexAction()
    {
    	try{
    		$db = new Registrar_Model_DbTable_DbIncome();
    		if($this->getRequest()->isPost()){
    			$formdata=$this->getRequest()->getPost();
    		}
    		else{
    			$formdata = array(
    					"adv_search"=>'',
    					"currency_type"=>-1,
    					"status"=>-1,
    					'start_date'=> date('Y-m-d'),
    					'end_date'=>date('Y-m-d'),
    			);
    		}
    		
			$rs_rows= $db->getAllIncome($formdata);//call frome model
    		$glClass = new Application_Model_GlobalClass();
    		$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
    		$list = new Application_Form_Frmtable();
    		$collumns = array("BRANCH_ID","RECEIPT_NO","INCOME_TITLE","CATEGORY_INCOME","CURRENCY_TYPE","TOTAL_INCOME","NOTE","FOR_DATE","STATUS");
    		$link=array(
    				'module'=>'accounting','controller'=>'income','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0, $collumns,$rs_rows,array('branch'=>$link,'title'=>$link,'invoice'=>$link,'total_amount'=>$link,'cate_name'=>$link));
    	}catch (Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		echo $e->getMessage();
    	}
    	$frm = new Registrar_Form_FrmSearchexpense();
    	$frm = $frm->AdvanceSearch();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_search = $frm;
    }
    public function addAction()
    {
    	if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();	
			$db = new Registrar_Model_DbTable_DbIncome();				
			try {
				$db->addIncome($data);
				if(!empty($data['saveclose'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/accounting/income");
				}else{
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
				}				
			} catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				echo $e->getMessage();
			}
		}
    	$pructis=new Registrar_Form_Frmexpense();
    	$frm = $pructis->FrmAddExpense();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
    	
    	$_dbs = new Application_Model_DbTable_DbGlobal();
    	$cate_income = $_dbs->getCategoryName(1);
    	array_unshift($cate_income, array('id'=>'-1','name'=>'បន្ថែមថ្មី'));
    	array_unshift($cate_income, array('id'=>'0','name'=>'Select Category'));
    	$this->view->cate_income = $cate_income;
    }
 
    public function editAction()
    {
    	if($this->getRequest()->isPost()){
			$id = $this->getRequest()->getParam('id');
			$data=$this->getRequest()->getPost();
			$db = new Registrar_Model_DbTable_DbIncome();				
			try {
				$db->updateIncome($data,$id);				
				Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', "/accounting/income");		
			} catch (Exception $e) {
				$this->view->msg = 'ការ​បញ្ចូល​មិន​ជោគ​ជ័យ';
			}
		}
		$id = $this->getRequest()->getParam('id');
		$db = new Registrar_Model_DbTable_DbIncome();
		$row  = $db->getIncomeById($id);
		$this->view->row = $row;
		
    	$pructis=new Registrar_Form_Frmexpense();
    	$frm = $pructis->FrmAddExpense($row);
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
		
    	$_dbs = new Application_Model_DbTable_DbGlobal();
    	$cate_income = $_dbs->getCategoryName(1);
    	array_unshift($cate_income, array('id'=>'-1','name'=>'បន្ថែមថ្មី'));
    	array_unshift($cate_income, array('id'=>'0','name'=>'Select Category'));
    	$this->view->cate_income = $cate_income;
    }
    
    function getRateAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
	    	$db = new Registrar_Model_DbTable_DbIncome();
	    	$ex_rate = $db->getExchangeRate();
	    	//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
	    	print_r(Zend_Json::encode($ex_rate));
	    	exit();
    	}
    }
    
    
    function addCateIncomeAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbIncome();
    		$cate_income = $db->addNewCateIncome($data);
    		print_r(Zend_Json::encode($cate_income));
    		exit();
    	}
    }
    
    function getReceiptNoAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbIncome();
    		$receipt = $db->getReceiptNo($data['branch_id']);
    		print_r(Zend_Json::encode($receipt));
    		exit();
    	}
    }
    
    

}







