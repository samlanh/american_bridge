<?php 
class Accounting_BookAndUniformController extends Zend_Controller_Action {
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search=array(
						'txtsearch' =>'',
						);
			}
			$db = new Accounting_Model_DbTable_DbBookAndUniform();
			$rs_rows = $db->getAllServiceNames($search);
			$list = new Application_Form_Frmtable();
			if(!empty($rs_rows)){
				$glClass = new Application_Model_GlobalClass();
				$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			}
			else{
				$result = Application_Model_DbTable_DbGlobal::getResultWarning();
			}
			$collumns = array("PROGRAM_TITLE","DISCRIPTION","PRICE","STATUS","MODIFY_DATE","BY_USER");
			$link=array(
					'module'=>'accounting','controller'=>'bookanduniform','action'=>'edit',
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
				$_model = new Accounting_Model_DbTable_DbBookAndUniform();
				$_model->addservice($_data);
				if(isset($_data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/accounting/bookanduniform");
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/accounting/bookanduniform/add");
				}
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
	}
	public function editAction(){
		$id=$this->getRequest()->getParam("id");
		$db = new Accounting_Model_DbTable_DbBookAndUniform();
		if($this->getRequest()->isPost())
		{
			try{
				$data = $this->getRequest()->getPost();
				$data["id"]=$id;
				$row=$db->updateservice($data);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/accounting/bookanduniform/index");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$rows = $db->getServiceById($id);
		$this->view->row=$rows;
	}


}