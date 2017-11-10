<?php
class Gep_StudentStudyController extends Zend_Controller_Action {
	
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
				$this->view->adv_search=$search;
			}
			else{
				$search = array(
						'adv_search' => '',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'));
			}
			$db_student= new Gep_Model_DbTable_DbStudent();
			$rs_rows = $db_student->getAllStudent($search);
			
			$list = new Application_Form_Frmtable();
			if(!empty($rs_rows)){
				} 
				else{
					$result = Application_Model_DbTable_DbGlobal::getResultWarning();
				}
				$collumns = array("STUDENT_CODE","NAME_KH","NAME_EN","SEX","ACADEMIC_YEAR","DEGREE","GRADE","STATUS");
				$link=array(
						'module'=>'gep','controller'=>'studentstudy','action'=>'edit',
				);
				$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('stu_code'=>$link,'stu_enname'=>$link,'stu_khname'=>$link));
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
	function addAction(){
		if($this->getRequest()->isPost()){
			try{
				$_data=$this->getRequest()->getPost();
				$_add = new Gep_Model_DbTable_DbStudent();
				$_add->addStudent($_data);
				if(isset($_data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/gep/studentstudy");
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/gep/studentstudy/add");
				}
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
// 		$db= new Gep_Model_DbTable_DbApplication();
// 		$this->view->serviecename = $rows = $db->getServiceType(1);
		
		$_hour = new Application_Model_GlobalClass();
		$this->view->hour= $row = $_hour->getHours();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$this->view->province = $row =$_db->getProvince();
		
		$db = new Gep_Model_DbTable_DbStudent();
		
		$this->view->degree = $deg = $db->getAllDegree();
		
		$this->view->year = $year = $db->getAllYear();
		
		//$this->view->id = $id = $db->getNewAccountNumber(1,2);
		

	}
	public function editAction(){
		$id=$this->getRequest()->getParam("id");
		$_db_studentGep = new Gep_Model_DbTable_DbStudent();
		$row= $_db_studentGep->getStudentGepById($id);
		$this->view->rs= $row;
		
		$roww = $_db_studentGep->getStudetnGepdetail($id);
		$this->view->row = $roww;
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$data["id"]=$id;
				$_db_student = new Gep_Model_DbTable_DbStudent();
				$_db_student->updateStudentGep($data,$id);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/gep/studentstudy/index");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}

// 		$db= new Gep_Model_DbTable_DbApplication();
// 		$this->view->serviecename = $rows = $db->getServiceType(1);
		$_hour = new Application_Model_GlobalClass();
		$this->view->hour= $row = $_hour->getHours();
		$_db = new Application_Model_DbTable_DbGlobal();
		$this->view->province = $row =$_db->getProvince();
		
		
		$db = new Gep_Model_DbTable_DbStudent();
		
		$this->view->degree = $deg = $db->getAllDegree();
		
		$this->view->year = $year = $db->getAllYear();
		
	}
	function getGradeAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Gep_Model_DbTable_DbStudent();
			$grade = $db->getAllGrade($data['dept_id']);
			//print_r($grade);exit();
			//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
			print_r(Zend_Json::encode($grade));
			exit();
		}
	}
	
	function getStuNoAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Gep_Model_DbTable_DbStudent();
			$id = $db->getNewAccountNumber($data['newid'],2);
			//print_r($grade);exit();
			//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
	
	
}
