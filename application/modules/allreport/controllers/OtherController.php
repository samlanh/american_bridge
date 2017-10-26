<?php
class Allreport_OtherController extends Zend_Controller_Action {
	
	
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
	
	}
	
	public function rptCarAction(){
		try{
			if($this->getRequest()->isPost()){
				$_data=$this->getRequest()->getPost();
				$search = array(
						'txtsearch' => $_data['txtsearch'],
				);
			}
			else{
				$search=array(
						'txtsearch' =>'',
				);
			}
			$db = new Allreport_Model_DbTable_DbRptCar();
			$this->view->rs = $db->getAllCar($search);
			$this->view->search=$search;
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function rptAcademicYearAction(){
	
		if($this->getRequest()->isPost()){
			$_data=$this->getRequest()->getPost();
			$search = array(
					'txtsearch' => $_data['txtsearch'],
			);
		}
		else{
			$search=array(
					'txtsearch' =>'',
			);
		}
	
		$db= new Allreport_Model_DbTable_DbRptAcademicYear();
		$this->view->rs = $db->getAllAcademic($search);
		$this->view->search = $search;
			
	}
	
	public function rptLecturerAction(){
		if($this->getRequest()->isPost()){
			$_data=$this->getRequest()->getPost();
			$search = array(
					'txtsearch' => $_data['txtsearch'],
			);
		}
		else{
			$search=array(
					'txtsearch' =>'',
			);
		}
		$group= new Allreport_Model_DbTable_DbRptLecturer();
		$this->view->rs = $rs_rows = $group->getAllLecturer($search);
		$this->view->search=$search;
	}
	
	public function rptGroupAction(){
	
		if($this->getRequest()->isPost()){
			$search=$this->getRequest()->getPost();
		}
		else{
			$search=array(
					'title' 		=>'',
					'study_year' 	=>'',
					'grade' 		=>'',
					'session' 		=>'',
			);
		}
	
		$form=new Registrar_Form_FrmSearchInfor();
		$forms=$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($forms);
		$this->view->form_search=$form;
		
		$group= new Allreport_Model_DbTable_DbRptGroup();
		$this->view->rs = $rs_rows = $group->getAllGroup($search);
		$this->view->search = $search;
			
	}
	
	
	
	
}

