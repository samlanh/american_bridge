<?php
class Registrar_StudentnearlyendserviceController extends Zend_Controller_Action {
	
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
						'adv_search' =>'',
						'service'	=>-1,
						//'start_date'=> date('Y-m-d'),
						'end_date'	=>date('Y-m-d'),
				);
			}
			
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
		$db = new Registrar_Model_DbTable_DbRptStudentNearlyEndService();
		$abc = $this->view->row = $db->getAllStudentNearlyEndService($search);
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
		
		$this->view->search = $search;
	}
	public function addAction(){
		$this->_redirect('registrar/studentnearlyendservice/index');
	}
	
}
