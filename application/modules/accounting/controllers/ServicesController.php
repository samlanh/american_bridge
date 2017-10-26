<?php
class Accounting_ServicesController extends Zend_Controller_Action {
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
    public function indexAction()
    {
    	try{
    		if($this->getRequest()->isPost()){
    			$_data=$this->getRequest()->getPost();
    			$search = array(
    					'txtsearch' => $_data['adv_search'],
    			);
    		}
    		else{
    			$search=array(
    					'txtsearch' => '',
    			);
    		}
    		$db = new Accounting_Model_DbTable_DbServiceType();
    		$rs_rows = $db->getAllServicesType($search);
    		$list = new Application_Form_Frmtable();
    		if(!empty($rs_rows)){
    			$glClass = new Application_Model_GlobalClass();
    			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true,1);
    		}
    		else{
    			$result = Application_Model_DbTable_DbGlobal::getResultWarning();
    		}
    		$collumns = array("PROGRAM_TITLE","DISCRIPTION","TYPE","STATUS","MODIFY_DATE","BY_USER");
    		$link=array(
    				'module'=>'accounting','controller'=>'services','action'=>'edit');
    		$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('cate_name'=>$link,'title'=>$link));
    		
    	}catch (Exception $e){
    		Application_Form_FrmMessage::message("APPLICATION_ERROR");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$frm = new Application_Form_FrmOther();
    	$this->view->add_major = $frm->FrmAddMajor(null);
    	$frm = new Global_Form_FrmSearchMajor();
    	$frm = $frm->frmServiceType();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_search = $frm;
    	$this->view->adv_search = $search;
    	
    }
	function addAction()
	{
		if($this->getRequest()->isPost()){
			try{
				$_data = $this->getRequest()->getPost();
				$_model = new Accounting_Model_DbTable_DbServiceType();
				$id=$_model->AddServiceType($_data);
				if($id==-1){
					Application_Form_FrmMessage::message("RECORD_EXIST");
				}else{
					if(isset($_data['save_close'])){
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/accounting/services");
					}else{
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/accounting/services/add");
					}
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
				}
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$item=new Accounting_Form_Frmitem();
		$frm_item=$item->FrmProgramType();
		Application_Model_Decorator::removeAllDecorator($frm_item);
		$this->view->frm_item = $frm_item;
	}
	function editAction(){
			if($this->getRequest()->isPost()){
				$_data = $this->getRequest()->getPost();
				$model = new Accounting_Model_DbTable_DbServiceType();
				$row = $model->AddServiceType($_data);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS", "/accounting/services/index");
			}
			$id = $this->getRequest()->getParam('id');
			$model = new Accounting_Model_DbTable_DbServiceType();
			$row = $model->getServiceTypeById($id);
			$item=new Accounting_Form_Frmitem();
			$frm_item=$item->FrmProgramType($row);
			Application_Model_Decorator::removeAllDecorator($frm_item);
			$this->view->frm_item = $frm_item;
	}	
}
