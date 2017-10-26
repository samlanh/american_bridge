<?php
class Accounting_ExchangeRateController extends Zend_Controller_Action {
	public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}

    public function indexAction()
    {	
    	$db = new Accounting_Model_DbTable_DbExchangeRate();
    	if($this->getRequest()->isPost()){
	    	try{
	    		$data = $this->getRequest()->getPost();
	    		$db->addExchangerate($data);
	    		Application_Form_FrmMessage::message("INSERT_SUCCESS");
	    	}catch(Exception $e){
	    		Application_Form_FrmMessage::message("APPLICATION_ERROR");
	    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());

	    	}
    	}
    	$id=$this->getRequest()->getParam("id");
    	$this->view->rs = $db->getExchangeRate();
    }
	public function addAction(){
		$this->_redirect('/accounting/exchangerate');
	}

}
