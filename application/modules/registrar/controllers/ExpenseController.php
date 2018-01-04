<?php

class Registrar_ExpenseController extends Zend_Controller_Action
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
    		$db = new Registrar_Model_DbTable_DbExpense();
    		if($this->getRequest()->isPost()){
    			$formdata=$this->getRequest()->getPost();
    		}
    		else{
    			$formdata = array(
    					"adv_search"=>'',
    					"category"=>'',
    					"branch"=>'',
    					"currency_type"=>-1,
    					"status"=>-1,
    					'start_date'=> date('Y-m-d'),
    					'end_date'=>date('Y-m-d'),
    			);
    		}
    		
			$rs_rows= $db->getAllExpense($formdata);//call frome model
    		$glClass = new Application_Model_GlobalClass();
    		$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
    		$list = new Application_Form_Frmtable();
    		$collumns = array("BRANCH_ID","RECEIPT_NO","EXPENSE_TITLE","CATEGORY_EXPENSE","CURRENCY_TYPE","TOTAL_EXPENSE","NOTE","DATE","STATUS");
    		$link=array(
    				'module'=>'registrar','controller'=>'expense','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0, $collumns,$rs_rows,array('branch'=>$link,'title'=>$link,'invoice'=>$link,'total_amount'=>$link));
    	}catch (Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		echo $e->getMessage();
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
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
			$db = new Registrar_Model_DbTable_DbExpense();				
			try {
				$db->addexpense($data);
				if(!empty($data['saveclose'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/registrar/expense");
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/registrar/expense/add");
				}
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/registrar/expense/add");
			} catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
    	$pructis=new Registrar_Form_Frmexpense();
    	$frm = $pructis->FrmAddExpense();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
    	
    	$_dbs = new Application_Model_DbTable_DbGlobal();
    	$cate_income = $_dbs->getCategoryName(0);
    	array_unshift($cate_income, array('id'=>'-1','name'=>'បន្ថែមថ្មី'));
    	array_unshift($cate_income, array('id'=>'0','name'=>'Select Category'));
    	$this->view->cate_expense = $cate_income;
    }
 
    public function editAction()
    {
    	$id = $this->getRequest()->getParam('id');
    	if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();	
			$data['id'] = $id;
			$db = new Registrar_Model_DbTable_DbExpense();				
			try {
				$db->updateExpense($data);				
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/registrar/expense");
			} catch (Exception $e) {
				$this->view->msg = 'ការ​បញ្ចូល​មិន​ជោគ​ជ័យ';
			}
		}
		$id = $this->getRequest()->getParam('id');
		$db = new Registrar_Model_DbTable_DbExpense();
		$row  = $db->getexpensebyid($id);
		$this->view->row = $row;
		
    	$pructis=new Registrar_Form_Frmexpense();
    	$frm = $pructis->FrmAddExpense($row);
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
		
    	$_dbs = new Application_Model_DbTable_DbGlobal();
    	$cate_income = $_dbs->getCategoryName(0);
    	array_unshift($cate_income, array('id'=>'-1','name'=>'បន្ថែមថ្មី'));
    	array_unshift($cate_income, array('id'=>'0','name'=>'Select Category'));
    	$this->view->cate_expense = $cate_income;
    }
    
    function getTotalassetAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$db = new Registrar_Model_DbTable_DbExpense();
    		$cate_income = $db->getTotalFixedAsset($data['fixed_id']);
    		print_r(Zend_Json::encode($cate_income));
    		exit();
    	}
    }

}







