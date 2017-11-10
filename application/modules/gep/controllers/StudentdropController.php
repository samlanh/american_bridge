<?php
class Gep_studentdropController extends Zend_Controller_Action {
	
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$search=array(
				'txtsearch'	=>$data['adv_search'],	
					);
		}
		else{
			$search=array(
				'txtsearch'	=>'',
						);
		}
		
		$db_student= new Gep_Model_DbTable_DbStudentDrop();
		$rs_rows = $db_student->getAllStudentDrop($search);
		$list = new Application_Form_Frmtable();
		if(!empty($rs_rows)){
			} 
			else{
				$result = Application_Model_DbTable_DbGlobal::getResultWarning();
			}
			$collumns = array("STUDENT_CODE","NAME_KH","NAME_EN","SEX","TYPE","REASON","STOP_DATE","NOTE");
			$link=array(
					'module'=>'gep','controller'=>'studentdrop','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('code'=>$link,'kh_name'=>$link,'en_name'=>$link));

		$this->view->adv_search = $search;
	}
	function addAction(){
		if($this->getRequest()->isPost()){
			try{
				$_data = $this->getRequest()->getPost();
				$_add = new Gep_Model_DbTable_DbStudentDrop();
 				$_add->addStudentDrop($_data);
 				if(!empty($_data['save_close'])){
 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/gep/studentdrop");
 				}
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$_add = new Gep_Model_DbTable_DbStudentDrop();
		$this->view->rs = $add =$_add->getAllStudentID();
		
// 		$_db = new Application_Model_DbTable_DbGlobal();
// 		$this->view->degree = $rows = $_db->getAllFecultyName();
// 		$this->view->occupation = $row =$_db->getOccupation();
// 		$this->view->province = $row =$_db->getProvince();
	}
	public function editAction(){
		$id=$this->getRequest()->getParam("id");
		$db= new Gep_Model_DbTable_DbStudentDrop();
		$row = $this->view->row = $db->getStudentDropById($id);
// 		print_r($row);exit();
		if($this->getRequest()->isPost())
		{
			try{
				$data = $this->getRequest()->getPost();
				$data["id"]=$id;
				$db = new Gep_Model_DbTable_DbStudentDrop();
				$row=$db->updateStudentDrop($data);
				
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/gep/studentdrop/index");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}	
		$_add = new Gep_Model_DbTable_DbStudentDrop();
		$this->view->rs = $add =$_add->getAllStudentIDEdit();
	}

	function getGradeAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Gep_Model_DbTable_DbStudentDrop();
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
			$db = new Gep_Model_DbTable_DbStudentDrop();
			$grade = $db->getStudentInfoById($data['studentid']);
			print_r(Zend_Json::encode($grade));
			exit();
		}
	}
	
	
	
	
	
	
}
