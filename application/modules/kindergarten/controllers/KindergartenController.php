<?php
class Kindergarten_KindergartenController extends Zend_Controller_Action {
    public function init()
    {    	
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
// 			$search='';
			$db_student= new Kindergarten_Model_DbTable_DbKindergarten();
			$rs_rows = $db_student->getAllStudent($search);
			$list = new Application_Form_Frmtable();
			if(!empty($rs_rows)){
				} 
				else{
					$result = Application_Model_DbTable_DbGlobal::getResultWarning();
				}
				$collumns = array("STUDENT_CODE","NAME_KH","NAME_EN","SEX","NATIONALITY","ACADEMIC_YEAR","DEGREE","GRADE","SESSION","STATUS");
				$link=array(
						'module'=>'kindergarten','controller'=>'kindergarten','action'=>'edit',
				);
				$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('stu_code'=>$link,'stu_enname'=>$link,'stu_khname'=>$link,'grade'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}		
		
		$this->view->adv_search=$search;
	}
	function addAction(){
		if($this->getRequest()->isPost()){
			try{
				$db = new Foundation_Model_DbTable_DbKindergarten();
				$num = $this->getStuNoGenerateAction();
				$data = $this->getRequest()->getPost();
				$row = $db->addKindergarten($data,$num);
// 				print_r($row);exit();
				if($row==-1){
					Application_Form_FrmMessage::message("RECORD_EXIST");
				}else{
					if(isset($data['save_close'])){
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/kindergarten/kindergarten/index");
					}else{
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/kindergarten/kindergarten/add");
					}
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
				}
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				echo $e->getMessage();
			}
		}
		//$this->_redirect('kindergarten/kindergarten/index');
		$_db = new Application_Model_DbTable_DbGlobal(); 		
		$this->view->province = $row =$_db->getProvince();
		$db = new Foundation_Model_DbTable_DbKindergarten();
		$this->view->year = $db->getAllYear();
		$this->view->grade = $db->getAllGrade();
		$row=$db->getAllOccupation();
		array_unshift($row, array ( 'id' => -1,'name' => 'បន្ថែមថ្មី'));
		$this->view->occupation = $row;
	}
	function editAction(){
		$id=$this->getRequest()->getParam("id");
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$db = new Kindergarten_Model_DbTable_DbKindergarten();
				$row = $db->updateKindergarten($data,$id);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/kindergarten/kindergarten/index");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$_db = new Application_Model_DbTable_DbGlobal();
		$this->view->province = $row =$_db->getProvince();
		$db = new Kindergarten_Model_DbTable_DbKindergarten();
		$this->view->year = $db->getAllYear();
		$this->view->grade = $db->getAllGrade();
		$row=$db->getAllOccupation();
		array_unshift($row, array ( 'id' => -1,'name' => 'បន្ថែមថ្មី'));
		$this->view->occupation = $row;
		$this->view->rs = $db->getStudentById($id);
	}
	function getStuNoAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Foundation_Model_DbTable_DbKindergarten();
			$stu_no = $db->getNewAccountNumber($data['newid'],1);
			print_r(Zend_Json::encode($stu_no));
			exit();
		}
	}
	
	function getStuNoGenerateAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Foundation_Model_DbTable_DbKindergarten();
			$stu_no = $db->getNewAccountNumber(1,1);
			return $stu_no;
		}
	}
	
	function addJobAction(){
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$_dbmodel = new Foundation_Model_DbTable_DbKindergarten();
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
	
}
















