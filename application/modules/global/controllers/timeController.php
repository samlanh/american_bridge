<?php
class Global_TimeController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			if($this->getRequest()->isPost()){
				$_data=$this->getRequest()->getPost();
				$search = array(
						'title' => $_data['title'],
						'status' => $_data['status_search']);
			}
			else{
				$search = array(
						'title' => '',
						'status' => -1);
			}
			$db = new Global_Model_DbTable_DbTime();
			$rs_rows= $db->getAllTime($search);
			 
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			 
			$list = new Application_Form_Frmtable();
			$collumns = array("ROOM_NAME","MODIFY_DATE","STATUS","BY_USER");
			$link=array(
					'module'=>'global','controller'=>'time','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('from_time'=>$link,'to_time'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			echo $e->getMessage();
		}
		$frm = new Global_Form_FrmSearchMajor();
		$frm =$frm->searchRoom();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
   function addAction()
   {
	   	if($this->getRequest()->isPost()){
	   		$_data = $this->getRequest()->getPost();
	   		try {
	   			$db = new Global_Model_DbTable_DbTime();
	   			$db->addTime($_data);
	   			Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
	   
	   		} catch (Exception $e) {
	   			Application_Form_FrmMessage::message("ការ​បញ្ចូល​មិន​ជោគ​ជ័យ");
	   			echo $e->getMessage();
	   		}
	   	}
   }
   public function editAction()
   {
	   	$id=$this->getRequest()->getParam("id");
	   	$db = new Global_Model_DbTable_DbTime();
	   	
	   	if($this->getRequest()->isPost())
	   	{
	   		try{
		   		$data = $this->getRequest()->getPost();
		   		$data["id"]=$id;
		   		$db->updateTime($data);
		   		Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/global/time/index");
	   		}catch(Exception $e){
	   			Application_Form_FrmMessage::message("EDIT_FAIL");
	   			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	   		}
	   	}
	   	
	   	$this->view->row = $db->getTimeById($id);
	   	
	   	
   }
   
   
   
}

