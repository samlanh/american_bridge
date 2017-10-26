<?php
class Registrar_RptotherincomeexpenseController extends Zend_Controller_Action {
	
    public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}else{
				$search=array(
						'txtsearch' =>'',
						'branch_id'	=>'',
						'user'	=>'',
						'type'	=>1,
						'start_date'=>date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
				);
			}
			//echo $search['type'];
			if($search['type']==1){
				
				$db = new Allreport_Model_DbTable_DbRptOtherIncome();
				$this->view->income = $db->getAllOtherIncome($search);

				$db = new Allreport_Model_DbTable_DbRptOtherExpense();
				$this->view->expense = $db->getAllOtherExpense($search);
				
			}else if($search['type']==2){
				
				$db = new Allreport_Model_DbTable_DbRptOtherIncome();
				$this->view->income = $db->getAllOtherIncome($search);
			
			}else if($search['type']==3){
				
				$db = new Allreport_Model_DbTable_DbRptOtherExpense();
				$this->view->expense = $db->getAllOtherExpense($search);
				
			}
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
	public function addAction(){
		$this->_redirect('registrar/rptotherincomeexpense/index');
	}
	
}
