<?php
class Foundation_StudentchangebranchController extends Zend_Controller_Action {
	
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
				'title'	=>'',
				'study_year'=> '',
				'grade_bac'=> '',
				'session'=> ''
			);
		}
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
		
		$db_student= new Foundation_Model_DbTable_DbStudentChangeBranch();
		$rs_rows = $db_student->getAllStudentChangeBranch($search);
		$list = new Application_Form_Frmtable();
		if(!empty($rs_rows)){
			} 
			else{
				$result = Application_Model_DbTable_DbGlobal::getResultWarning();
			}
			$collumns = array("STUDENT_ID","NAME","SEX","GRADE","OLD_BRANCH","NEW_BRANCH","REASON","USER","CREATED_DATE");
			$link=array(
					'module'=>'foundation','controller'=>'studentchangebranch','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('stu_code'=>$link,'name'=>$link,'grade'=>$link));

		$this->view->adv_search = $search;
	}
	function addAction(){
		if($this->getRequest()->isPost()){
			try{
				$_data = $this->getRequest()->getPost();
				$_add = new Foundation_Model_DbTable_DbStudentChangeBranch();
 				$_add->addStudentChangeBranch($_data);
 				if(!empty($_data['save_close'])){
 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/foundation/studentchangebranch");
 				}
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$_add = new Foundation_Model_DbTable_DbStudentChangeBranch();
		$this->view->stu_id = $_add->getAllStudentID();
		$this->view->stu_name = $_add->getAllStudentName();
		$this->view->branch = $_add->getAllBranch();
		
	}
	public function editAction(){
		$id=$this->getRequest()->getParam("id");
		if($this->getRequest()->isPost())
		{
			try{
				$data = $this->getRequest()->getPost();
				$data["id"]=$id;
				$db = new Foundation_Model_DbTable_DbStudentChangeBranch();
				$row=$db->updateStudentChangeBranch($data);
				
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/foundation/studentchangebranch/index");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}	
		
		$db= new Foundation_Model_DbTable_DbStudentChangeBranch();
		$row = $this->view->row = $db->getStudentChangeBranchById($id);
		
		$this->view->stu_id = $db->getAllStudentID();
		$this->view->stu_name = $db->getAllStudentName();
		$this->view->branch = $db->getAllBranch();
		
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
			$db = new Foundation_Model_DbTable_DbStudentChangeBranch();
			$grade = $db->getStudentInfoById($data['studentid']);
			print_r(Zend_Json::encode($grade));
			exit();
		}
	}
	
}
