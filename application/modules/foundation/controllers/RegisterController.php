<?php
class Foundation_RegisterController extends Zend_Controller_Action {
	
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
				
			}
			else{
				$search = array(
						'adv_search'=> '',
						'study_year'=> '',
						'degree_all'=> '',
						'grade_all'	=> '',
						'room'		=> '',
						'user'		=> '',
						'session'	=> '',
						'start_date'=> date('Y-m-d'),
						'end_date'	=>date('Y-m-d')
					);
			}
			
			$this->view->adv_search=$search;
			
			$db_student= new Foundation_Model_DbTable_DbStudent();
			$rs_rows = $db_student->getAllStudent($search);
			 
			$list = new Application_Form_Frmtable();
				$collumns = array("BRANCH","STUDENT_ID","NAME_KH","NAME_EN","SEX","NATIONALITY","ACADEMIC_YEAR","DEGREE","GRADE","SESSION","ROOM","STATUS");
				$link=array(
						'module'=>'foundation','controller'=>'register','action'=>'edit',
				);
				$link_view=array(
						'module'=>'foundation','controller'=>'register','action'=>'view',
				);
				$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('stu_code'=>$link,'stu_enname'=>$link,'stu_khname'=>$link,'grade'=>$link,'profile'=>$link_view));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}	
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	}
	function addAction(){
		$db = new Foundation_Model_DbTable_DbStudent();
		if($this->getRequest()->isPost()){
			try{
				
				$_data = $this->getRequest()->getPost();
				$exist = $db->addStudent($_data);
				if($exist==-1){
					Application_Form_FrmMessage::message("RECORD_EXIST");
				}else{
					if(isset($_data['save_close'])){
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/foundation/register");
					}else{
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/foundation/register/add");
					}
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
				}
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				echo $e->getMessage();
			}
		}
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$row =$_db->getOccupation();
		array_unshift($row, array ( 'id' => -1,'name' => 'បន្ថែមថ្មី'));
		array_unshift($row, array ( 'id' => 0,'name' => 'Select Job'));
		$this->view->occupation = $row;
		
		$this->view->branch = $_db->getAllBranch();
		
		
		$this->view->row = $db->getDegreeLanguage();
		
		$this->view->year = $db->getAllYear();
		
		$this->view->degree = $db->getAllDegree();
		$this->view->room = $db->getAllRoom();
		
		$this->view->user_level = $db->getUserLevel();
		
		$this->view->province = $_db->getProvince();
	}
	public function editAction(){
		$id=$this->getRequest()->getParam("id");
		$db= new Foundation_Model_DbTable_DbStudent();
		$row = $db->getStudentById($id);
		$rr = $db->getStudyHishotryById($id);
		$this->view->rr = $rr;
		if($this->getRequest()->isPost())
		{
			try{
				$data = $this->getRequest()->getPost();
				$data["id"]=$id;
				$row=$db->updateStudent($data);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/foundation/register/index");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$_db = new Application_Model_DbTable_DbGlobal();
		
		$this->view->province = $_db->getProvince();
		$this->view->branch = $_db->getAllBranch();
		
		$row =$_db->getOccupation();
		array_unshift($row, array ( 'id' => -1,'name' => 'បន្ថែមថ្មី'));
		array_unshift($row, array ( 'id' => 0,'name' => 'Select Job'));
		$this->view->occupation = $row;
		
		$this->view->degree = $db->getAllDegree();
		$this->view->room = $db->getAllRoom();
		
		$this->view->user_level = $db->getUserLevel();
		
		$this->view->year = $db->getAllYear();
		
		//$this->view->occupation = $_db->getOccupation();
		
		$this->view->rs = $db->getStudentById($id);
		
		
	}
	
	function viewAction(){
		$id=$this->getRequest()->getParam("id");
		$db = new Foundation_Model_DbTable_DbStudent();
		
		$this->view->rs = $db->getStudentInfoById($id);
		
		
	}
	
	function getGradeAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Foundation_Model_DbTable_DbStudent();
			$grade = $db->getAllGrade($data['dept_id']);
			//print_r($grade);exit();
			array_unshift($grade, array ( 'id' => 0, 'name' => 'Select Grade'));
			print_r(Zend_Json::encode($grade));
			exit();
		}
	}
	function getStudentAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Foundation_Model_DbTable_DbStudent();
			$grade = $db->getStudentInfoById($data['studentid']);
			print_r(Zend_Json::encode($grade));
			exit();
		}
	}
	function submitAction(){
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$db = new Foundation_Model_DbTable_DbLanguage();
				$row = $db->addDegreeLanguage($data);
				$result = array("id"=>$row);
				print_r(Zend_Json::encode($row));
				exit();
				//Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
	}
	function addJobAction(){
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$_dbmodel = new Global_Model_DbTable_DbOccupation();
				$row = $_dbmodel->addNewOccupationPopup($data);
				$result = array("id"=>$row);
				print_r(Zend_Json::encode($row));
				exit();
				//Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
	}
	function getStuNoAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			
			$_db = new Foundation_Model_DbTable_DbStudent(); 
			$user_level = $_db->getUserLevel();
			if($user_level==1 || $user_level==2){
				$branch = $data['branch'];
			}else{
				$branch = $_db->getBranchId();
			}
			
			$db = new Registrar_Model_DbTable_DbRegister();
			$stu_no = $db->getNewAccountNumber($data['degree'],$branch);
			print_r(Zend_Json::encode($stu_no));
			exit();
		}
	}
}









