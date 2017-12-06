<?php
class Global_ServiceController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
	private $type = array(1=>'service',2=>'program');
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function start(){
		return ($this->getRequest()->getParam('limit_satrt',0));
	}
	public function indexAction(){
		try{
			if($this->getRequest()->isPost()){
				$_data=$this->getRequest()->getPost();
				$search = array(
						'txtsearch' => $_data['txtsearch'],
						);
			}
			else{
				$search=array(
						'txtsearch' =>'',
						);
			}
			$db = new Global_Model_DbTable_DbService();
			$rs_rows = $db->getAllServiceNames($search);
			$list = new Application_Form_Frmtable();
			if(!empty($rs_rows)){
				$glClass = new Application_Model_GlobalClass();
				$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			}
			else{
				$result = Application_Model_DbTable_DbGlobal::getResultWarning();
			}
			$collumns = array("PROGRAM_TITLE","TYPE","DISCRIPTION","STATUS","MODIFY_DATE","BY_USER");
			$link=array(
					'module'=>'global','controller'=>'service','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('cate_name'=>$link,'title'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		$frm = new Global_Form_FrmSearchMajor();
		$frm = $frm->frmSearchServiceProgram();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		$this->view->adv_search = $search;
	}
public function addAction(){
	if($this->getRequest()->isPost()){
			try{
				$_data = $this->getRequest()->getPost();
				$_model = new Global_Model_DbTable_DbService();
				$_model->addservice($_data);
				if(isset($_data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/global/service");
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/global/service/add");
				}
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
	$db = new Global_Model_DbTable_DbService();
	$rs= $db->getServiceType(1);
	array_unshift($rs, array ( 'id' => -1,'name' => 'បន្ថែមថ្មី'));
	$this->view->service = $rs;
		
	$this->view->all_car = $db->getAllCar();
	
	
	$frm=new Accounting_Form_FrmProgram();
	$this->view->frm=$frm->addProgramName();
	Application_Model_Decorator::removeAllDecorator($frm->addProgramName());
}
public function editAction(){
	$id=$this->getRequest()->getParam("id");
	$db = new Global_Model_DbTable_DbService();
	$row = $db->getServiceById($id);
// 	print_r($row);exit();
	if($this->getRequest()->isPost())
	{
		try{
			$data = $this->getRequest()->getPost();
			$data["id"]=$id;
			$db = new Global_Model_DbTable_DbService();
			$row=$db->updateservice($data);
			Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/global/service/index");
		}catch(Exception $e){
			Application_Form_FrmMessage::message("EDIT_FAIL");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	$db = new Global_Model_DbTable_DbService();
	$rs= $db->getServiceType(1);
	array_unshift($rs, array ( 'id' => -1,'name' => 'បន្ថែមថ្មី'));
	$this->view->service = $rs;
	
	$this->view->all_car = $db->getAllCar();
	
	$obj=new Accounting_Form_FrmProgram();
	$frm=$obj->addProgramName($row);
	$this->view->frm=$frm;
	Application_Model_Decorator::removeAllDecorator($frm);
	$this->view->row=$row;
}
function submitAction(){
	if($this->getRequest()->isPost()){
		try{
			$data = $this->getRequest()->getPost();
			$db = new Global_Model_DbTable_DbService();
			$row = $db->AddServiceType($data);
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