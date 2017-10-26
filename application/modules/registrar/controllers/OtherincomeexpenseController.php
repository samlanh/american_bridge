<?php
class Registrar_OtherincomeexpenseController extends Zend_Controller_Action {
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
    					'txtsearch' =>'',
    					'study_year' =>'',
    					'type'=>'1',
    					'user'=>'',
    					'cat_all'=>'',
    					'start_date'=> date('Y-m-d'),
    					'end_date'=>date('Y-m-d'),
    			);
    		}
			$this->view->rs_search=$search;
			$db = new Registrar_Model_DbTable_DbReportStudentByuser();
			
			if($search['type']==1){
				$this->view->income = $db->getAllIncome($search);
				$this->view->expense = $db->getAllExpense($search);
			}else if($search['type']==2){
				
				$this->view->income = $db->getAllIncome($search);
			
			}else if($search['type']==3){
				
				$this->view->expense = $db->getAllExpense($search);
				
			}
    		
    	}catch(Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$form=new Registrar_Form_FrmSearchInfor();
    	$form->FrmSearchRegister();
    	Application_Model_Decorator::removeAllDecorator($form);
    	$this->view->form_search=$form;
    	$this->view->search = $search;
    	$db = new Registrar_Model_DbTable_DbReportStudentByuser();
    	$this->view->cat_all=$db->getCategorys();
    }
    public function addAction(){
    	$this->_redirect("/registrar/allreports");
    }
    public function rptDailyAction()
    {
    
    }
    public function getCategoryAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$_db = new Registrar_Model_DbTable_DbReportStudentByuser();
    		$data = $_db->getCatByParent($data['type_id']);
    		print_r(Zend_Json::encode($data));
    		exit();
    	}
    }
}
