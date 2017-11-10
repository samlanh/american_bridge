<?php
class Kindergarten_StudentStudyController extends Zend_Controller_Action {
	
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_student= new Foundation_Model_DbTable_DbApplication();
		$rs_rows = $db_student->getAllStudentStudy(2);
		$list = new Application_Form_Frmtable();
		if(!empty($rs_rows)){
			} 
			else{
				$result = Application_Model_DbTable_DbGlobal::getResultWarning();
			}
			$collumns = array("STUDENT NAME","ឈ្មោះសិស្ស","SEX","NATIONALITY","DOB","PHONE","STATUS");
			$link=array(
					'module'=>'foundation','controller'=>'studentstudy','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('stu_enname'=>$link,'stu_khname'=>$link));
			
	}
	function addAction(){
		if($this->getRequest()->isPost()){
			try{
				$_data=$this->getRequest()->getPost();
				$_add = new Foundation_Model_DbTable_DbApplication();
				$_add->addStudent($_data);
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db= new Foundation_Model_DbTable_DbApplication();
		$this->view->serviecename = $rows = $db->getServiceType(1);
		$_hour = new Application_Model_GlobalClass();
		$this->view->hour= $row = $_hour->getHours();
		$_db = new Application_Model_DbTable_DbGlobal();
		$this->view->province = $row =$_db->getProvince();

	}
	public function editAction(){
		$id=$this->getRequest()->getParam("id");
		$_db_studentGep = new Foundation_Model_DbTable_DbApplication();
		$row= $_db_studentGep->getStudentGepById($id);
		$this->view->rs= $row;
		
		
		$roww = $_db_studentGep->getStudetnGepdetail($id);
		$this->view->row = $roww;
		if($this->getRequest()->isPost()){
			
			try{
				$data = $this->getRequest()->getPost();
				$data["id"]=$id;
				$_db_student = new Foundation_Model_DbTable_DbApplication();
				$_db_student->updateStudentGep($data);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/foundation/studentstudy/index");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}

		$db= new Foundation_Model_DbTable_DbApplication();
		$this->view->serviecename = $rows = $db->getServiceType(1);
		$_hour = new Application_Model_GlobalClass();
		$this->view->hour= $row = $_hour->getHours();
		$_db = new Application_Model_DbTable_DbGlobal();
		$this->view->province = $row =$_db->getProvince();
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
	
}
