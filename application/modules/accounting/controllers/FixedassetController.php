<?php
class accounting_FixedAssetController extends Zend_Controller_Action {
	const REDIRECT_URL = '/accounting/fixedasset';
	public function init()
	{
		/* Initialize action controller here */
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	
	public function addAction(){
		
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
// 			ripnt_r($data);exit();
			$db = new Accounting_Model_DbTable_DbAsset();
			try {
				$db->addasset($data);
				if(!empty($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL . '/index');
				}else{
					Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL . '/add');
				}
				Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL . '/add');
				
			} catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err = $e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$fm = new Accounting_Form_Frmasset();
		$frm = $fm->FrmAsset();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_fixedasset = $frm;
	}

	public function indexAction()
	{
		try{
			$db = new Accounting_Model_DbTable_DbAsset();
			    		if($this->getRequest()->isPost()){
			    			$search=$this->getRequest()->getPost();
			    		}
			    		else{
			    			$search = array(
			    					'title' => '',
			    					'branch' => '',
			    					'status' => -1,
			    					'start_date'=> date('Y-m-d'),
			    					'end_date'=>date('Y-m-d'),
			    					);
			    		}
			$rs_rows= $db->getAllAsset($search);
			
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","FIXED_ASSETNAME","ASSET_COST","USEFULL_LIFE","SALVAGEVALUE","PAID_MONTH","TOTAL_AMOUNT","START_DATE","END_DATE","NOTE","STATUS");
			$link=array(
					'module'=>'accounting','controller'=>'fixedasset','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns,$rs_rows,array('fixed_assetname'=>$link,'branch_name'=>$link,'asset_cost'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			echo $e->getMessage();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
// 		$fm = new Application_Form_FrmAdvanceSearch();
// 		$frm = $fm->AdvanceSearch();
// 		Application_Model_Decorator::removeAllDecorator($frm);
// 		$this->view->frm_search = $frm;
		
		$form=new Registrar_Form_FrmSearchInfor();
		$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->frm_fixedasset=$form;
		
		$frm = new Registrar_Form_FrmSearchexpense();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		 
	}
		public function editAction()
		{
			$id = $this->getRequest()->getParam('id');
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$data['id']=$id;
				// 			ripnt_r($data);exit();
				$db = new Accounting_Model_DbTable_DbAsset();
				try {
					$db->updatasset($data);
					if(!empty($data['save_new'])){
						Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL . '/index');
					}else{
						Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL . '/index');
					}
					Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL . '/index');
				} catch (Exception $e) {
					Application_Form_FrmMessage::message("INSERT_FAIL");
					$err = $e->getMessage();
					Application_Model_DbTable_DbUserLog::writeMessageError($err);
				}
			}
			$db = new Accounting_Model_DbTable_DbAsset();
			$row  = $db->getassetbyid($id);
			
			$pructis=new Accounting_Form_Frmasset();
			$frm = $pructis->FrmAsset($row);
			Application_Model_Decorator::removeAllDecorator($frm);
			$this->view->frm_fixedasset=$frm;
	}
}
