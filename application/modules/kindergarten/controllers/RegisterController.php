<?php
class kindergarten_RegisterController extends Zend_Controller_Action {
	
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
			$db_student= new Foundation_Model_DbTable_DbStudent();
			$rs_rows = $db_student->getAllStudent($search);
			$list = new Application_Form_Frmtable();
			if(!empty($rs_rows)){
				} 
				else{
					$result = Application_Model_DbTable_DbGlobal::getResultWarning();
				}
				$collumns = array("STUDENT_CODE","NAME_KH","NAME_EN","SEX","GRADE","NATIONALITY","DOB","PHONE","EMAIL","STATUS");
				$link=array(
						'module'=>'foundation','controller'=>'register','action'=>'edit',
				);
				$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('stu_code'=>$link,'stu_enname'=>$link,'stu_khname'=>$link,'grade'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}		
	
	}
	function addAction(){
		$db = new Foundation_Model_DbTable_DbStudent();
		if($this->getRequest()->isPost()){
			try{
				
				$num = $this->getStuNoGenerateAction();
// 				print_r($num);exit();
				
				$_data = $this->getRequest()->getPost();
				$exist = $db->addStudent($_data,$num);
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
		$service = new Foundation_Model_DbTable_DbApplication();
		$rows = $service->getlang();
		array_unshift($rows, array ( 'id' => -1,'name' => 'បន្ថែមថ្មី'));
		$this->view->language = $rows;
		
		$_db = new Application_Model_DbTable_DbGlobal();
		
		$row =$_db->getOccupation();
		array_unshift($row, array ( 'id' => -1,'name' => 'បន្ថែមថ្មី'));
		$this->view->occupation = $row;
		
		$this->view->row = $db->getDegreeLanguage();
		
		$this->view->year = $db->getAllYear();
		
		$this->view->degree = $rows = $_db->getAllFecultyName();
		
		$this->view->province = $row =$_db->getProvince();
		
		
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
		
		$service = new Foundation_Model_DbTable_DbApplication();
		$rows = $service->getlang();
		array_unshift($rows, array ( 'id' => -1,'name' => 'បន្ថែមថ្មី'));
		$this->view->serviecename = $rows;
		$this->view->row = $db->getDegreeLanguage();
		
		$_db = new Application_Model_DbTable_DbGlobal();
		
		$row =$_db->getOccupation();
		
		array_unshift($row, array ( 'id' => -1,'name' => 'បន្ថែមថ្មី'));
		
		$this->view->occupation = $row;
		
		$this->view->degree = $_db->getAllFecultyName();
		
		//$this->view->occupation = $_db->getOccupation();
		
		$this->view->province = $_db->getProvince();
		
		$this->view->rs = $db->getStudentById($id);
		
		$this->view->year = $db->getAllYear();
		
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
			$db = new Foundation_Model_DbTable_DbStudent();
			$stu_no = $db->getNewAccountNumber($data['newid'],1);
			print_r(Zend_Json::encode($stu_no));
			exit();
		}
	}
	function getStuNoGenerateAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Foundation_Model_DbTable_DbStudent();
			$stu_no = $db->getNewAccountNumber(1,1);
			return $stu_no;
		}
	}
	
}









