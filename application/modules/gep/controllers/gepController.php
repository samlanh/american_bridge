<?php
class Gep_GepController extends Zend_Controller_Action {
	
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
		$db_student= new Gep_Model_DbTable_Dbgep();
		$rs_rows = $db_student->getAllStudentStudy($search,2);
		$list = new Application_Form_Frmtable();
		if(!empty($rs_rows)){
			} 
			else{
				$result = Application_Model_DbTable_DbGlobal::getResultWarning();
			}
			$collumns = array("STUDENT_ID","NAME_KH","NAME_EN","SEX","DEGREE","GRADE","TIME","STATUS","USER");
			$link=array(
					'module'=>'gep','controller'=>'gep','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('stu_enname'=>$link,'stu_khname'=>$link,'stu_code'=>$link));
	  }catch (Exception $e){
	  	Application_Form_FrmMessage::message("INSERT_FAIL");
	  	Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	  	//echo $e->getMessage();
	  }
			
	}
	function addAction(){
		if($this->getRequest()->isPost()){
			try{
				$_data=$this->getRequest()->getPost();
				$_add = new Gep_Model_DbTable_Dbgep();
				$_add->addStudent($_data);
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db= new Gep_Model_DbTable_Dbgep();
		$this->view->serviecename = $rows = $db->getServiceType(1);
		$_hour = new Application_Model_GlobalClass();
		$this->view->hour= $row = $_hour->getHours();
		$_db = new Application_Model_DbTable_DbGlobal();
		$this->view->province = $row =$_db->getProvince();
		
		$db= new Gep_Model_DbTable_Dbgep();
		$grade = $db->getGrade();
		$this->view->grade = $grade;
		
		$dept = $db->getAllDept();
		$this->view->all_dept = $dept;

	}
	public function editAction(){
		$id=$this->getRequest()->getParam("id");
		$_db_studentGep = new Gep_Model_DbTable_Dbgep();
		$row= $_db_studentGep->getStudentHistoryById($id);
		$this->view->row= $row;
		
		
		$roww = $_db_studentGep->getStudentById($id);
		$this->view->rs = $roww;
		if($this->getRequest()->isPost()){
			
			try{
				$data = $this->getRequest()->getPost();
				$data["id"]=$id;
				$_db_student = new Gep_Model_DbTable_Dbgep();
				$_db_student->updateStudentGep($data);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/gep/gep/index");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}

		$db= new Gep_Model_DbTable_Dbgep();
		$this->view->serviecename = $rows = $db->getServiceType(1);
		$_hour = new Application_Model_GlobalClass();
		$this->view->hour= $row = $_hour->getHours();
		$_db = new Application_Model_DbTable_DbGlobal();
		$this->view->province = $row =$_db->getProvince();
		
		$db= new Gep_Model_DbTable_Dbgep();
		$grade = $db->getGrade();
		$this->view->grade = $grade;
		
		$dept = $db->getAllDept();
		$this->view->all_dept = $dept;
		
		
	}
	function getAllGradeAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Gep_Model_DbTable_Dbgep();
			$grade = $db->getAllGrade($data['dept_id']);
			//print_r($grade);exit();
			//array_unshift($makes, array ( 'id' => -1, 'name' => 'បន្ថែមថ្មី') );
			print_r(Zend_Json::encode($grade));
			exit();
		}
	}
	
	function getGepIdAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Gep_Model_DbTable_Dbgep();
			$stu_no = $db->getNewGepId();
			print_r(Zend_Json::encode($stu_no));
			exit();
		}
	}	
	
	
	
	
}
