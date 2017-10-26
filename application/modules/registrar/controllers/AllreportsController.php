<?php
class Registrar_AllreportsController extends Zend_Controller_Action {
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
    public function indexAction(){
    	try{
    		if($this->getRequest()->isPost()){
    			$search=$this->getRequest()->getPost();
    		}
    		else{
    			$search = array(
    					'adv_search' =>'',
    					'study_year' =>'',
    					'service_and_product'=>'',
    					'user'=>'',
    					'degree_all'=>'',
    					'grade_all'=>'',
    					'shift'=>0,
    					'start_date'=> date('Y-m-d'),
    					'end_date'=>date('Y-m-d'),
    			);
    		}
    		$db = new Registrar_Model_DbTable_DbReportStudentByuser();
    		$data=$this->view->row = $db->getAllStudentPayment($search);
			$this->view->income = $db->getAllIncome($search);
			$a = $this->view->expense = $db->getAllExpense($search);
			//print_r($a);exit();
			
			
    		$type=$db->getType();
    		
    	}catch(Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$form=new Registrar_Form_FrmSearchInfor();
    	$form->FrmSearchRegister();
    	Application_Model_Decorator::removeAllDecorator($form);
    	$this->view->form_search=$form;
    	$this->view->search = $search;
    }
    public function addAction(){
    	$this->_redirect("/registrar/allreports");
    }
    public function rptDailyAction()
    {
    
    }
}
