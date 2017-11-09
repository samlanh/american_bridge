<?php
class Foundation_studentDropLunchAndStayController extends Zend_Controller_Action {
	
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		
		if($this->getRequest()->isPost()){
			$search=$this->getRequest()->getPost();
		}
		else{
			$search=array(
				'title'		=>'',
				'branch'	=>'',
				'service'	=>0,
				'start_date'=> date('Y-m-d'),
				'end_date'	=>date('Y-m-d'),
			);
		}
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
		
		$db= new Foundation_Model_DbTable_DbStudentDropLunchAndStay();
		$rs_rows = $db->getAllStudentDropLunchAndStay($search);
		$list = new Application_Form_Frmtable();
		$collumns = array("STUDENT_ID","NAME_KH","NAME_EN","SEX","SERVICE","TYPE","REASON","STOP_DATE");
		$link=array(
				'module'=>'foundation','controller'=>'studentdroplunchandstay','action'=>'edit',
		);
		$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('stu_code'=>$link,'stu_khname'=>$link,'stu_enname'=>$link));

		$this->view->adv_search = $search;
		
		$this->view->lunch_service = $db->getAllLunchAndStayService();
		
	}
	function addAction(){
		if($this->getRequest()->isPost()){
			try{
				$_data = $this->getRequest()->getPost();
				$_add = new Foundation_Model_DbTable_DbStudentDropLunchAndStay();
 				$_add->addStudentDropLunchAndStay($_data);
 				if(!empty($_data['save_close'])){
 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/foundation/studentdroplunchandstay");
 				}
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$db = new Foundation_Model_DbTable_DbStudentDropLunchAndStay();
		$this->view->stu_id = $db->getAllStudentLunchAndStayID();
		$this->view->stu_name = $db->getAllStudentLunchAndStayName();
		$this->view->lunch_service = $db->getAllLunchAndStayService();
		
// 		print_r($a);exit();
// 		$_db = new Application_Model_DbTable_DbGlobal();
// 		$this->view->degree = $rows = $_db->getAllFecultyName();
// 		$this->view->occupation = $row =$_db->getOccupation();
// 		$this->view->province = $row =$_db->getProvince();
	}
	public function editAction(){
		$id=$this->getRequest()->getParam("id");
		
		$db= new Foundation_Model_DbTable_DbStudentDropLunchAndStay();
		$row = $this->view->row = $db->getStudentDropById($id);
		
		if($this->getRequest()->isPost())
		{
			try{
				$data = $this->getRequest()->getPost();
				$data["id"]=$id;
				$db = new Foundation_Model_DbTable_DbStudentDropLunchAndStay();
				$row=$db->updateStudentDropLunchAndStay($data);
				
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/foundation/studentdroplunchandstay/index");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}	
		
		$db = new Foundation_Model_DbTable_DbStudentDropLunchAndStay();
		$this->view->stu_id = $db->getAllStudentLunchAndStayIDEdit();
		$this->view->stu_name = $db->getAllStudentLunchAndStayNameEdit();
		$this->view->lunch_service = $db->getAllLunchAndStayService();
		//print_r($add);exit();
	}

	function getGradeAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Foundation_Model_DbTable_DbStudent();
			$grade = $db->getAllGrade($data['dept_id']);
			//print_r($grade);exit();
			//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
			print_r(Zend_Json::encode($grade));
			exit();
		}
	}
	
	
	function getStudentAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Foundation_Model_DbTable_DbStudentDropLunchAndStay();
			$grade = $db->getStudentInfoById($data['studentid']);
			print_r(Zend_Json::encode($grade));
			exit();
		}
	}
	
}
