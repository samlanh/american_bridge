<?php
class Kindergarten_groupstudentchangegroupController extends Zend_Controller_Action {
	
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db_student= new Kindergarten_Model_DbTable_DbGroupStudentChangeGroup();
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$search=array(
					'txtsearch'	=>$data['adv_search'],
			);
		}else{
			$search=array(
					'txtsearch'	=>'',
			);
		}
		
		$this->view->adv_search=$search;
		
		$rs_rows = $db_student->selectAllStudentChangeGroup($search);
		$list = new Application_Form_Frmtable();
		if(!empty($rs_rows)){
			} 
			else{
				$result = Application_Model_DbTable_DbGlobal::getResultWarning();
			}
			$collumns = array("FROM_GROUP","GRADE","SESSION","TO_GROUP","GRADE","SESSION","MOVING_DATE","NOTE");
			$link=array(
					'module'=>'kindergarten','controller'=>'groupstudentchangegroup','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('group_code'=>$link,'grade'=>$link,'session'=>$link,'to_group_code'=>$link));
			
	}
	function addAction(){
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
// 				print_r($data);exit();
				$_add = new Kindergarten_Model_DbTable_DbGroupStudentChangeGroup();
 				$_add->addGroupStudentChangeGroup($data);
 				if(!empty($data['save_close'])){
 					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/kindergarten/groupstudentchangegroup");
 				}
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$_add = new Kindergarten_Model_DbTable_DbGroupStudentChangeGroup();
		
		$this->view->row = $add =$_add->getfromGroup();
		
		$this->view->rs = $add =$_add->gettoGroup();
		
		
// 		$_db = new Application_Model_DbTable_DbGlobal();
// 		$this->view->degree = $rows = $_db->getAllFecultyName();
// 		$this->view->occupation = $row =$_db->getOccupation();
// 		$this->view->province = $row =$_db->getProvince();
	}
	public function editAction(){
		$id=$this->getRequest()->getParam("id");
		if($this->getRequest()->isPost())
		{
			try{
				$data = $this->getRequest()->getPost();
				//$data["id"]=$id;
				$db = new Kindergarten_Model_DbTable_DbGroupStudentChangeGroup();
				$row=$db->updateStudentChangeGroup($data,$id);
				
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/kindergarten/groupstudentchangegroup/index");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}	
		$db= new Kindergarten_Model_DbTable_DbGroupStudentChangeGroup();
		$row = $this->view->rs = $db->getAllGroupStudentChangeGroupById($id);
		
// 		print_r($row);exit();
		
		$_add = new Kindergarten_Model_DbTable_DbGroupStudentChangeGroup();
		
		$this->view->row = $add =$_add->getfromGroup();
		
		$this->view->rows = $add =$_add->gettoGroup();
	}
	function getToGroupAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Kindergarten_Model_DbTable_DbGroupStudentChangeGroup();
			$grade = $db->getGroupStudentChangeGroup1ById($data['to_group'],$data['type']);
			print_r(Zend_Json::encode($grade));
			exit();
		}
	}
	
	function getAllStudentAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Kindergarten_Model_DbTable_DbGroupStudentChangeGroup();
			$student = $db->getAllStudentFromGroup($data['from_group']);
			print_r(Zend_Json::encode($student));
			exit();
		}
	}
	
	function getAllStudentUpdateAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Kindergarten_Model_DbTable_DbGroupStudentChangeGroup();
			$student = $db->getAllStudentFromGroupUpdate($data['from_group']);
			print_r(Zend_Json::encode($student));
			exit();
		}
	}	
	
}











