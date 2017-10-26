<?php
class Registrar_CategoryController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
    public function indexAction()
    {
    	try{
	    	$_model = new Registrar_Model_DbTable_DbCategory();
	    	if($this->getRequest()->isPost()){
	    		$search=$this->getRequest()->getPost();
	    		$this->view->search=$search;
    	   }
    	   else{
	    	   	$search = array(
	    	   			'category_name' => '',
	    	   			'status' =>1
	    	   	);
    	   }
    	   $this->view->search=$search;
	    	$rs_rows= $_model->getAllCategory($search);
	    	$list = new Application_Form_Frmtable();
	    	$collumns = array("PARENT","CATEGORY_NAME","DATE","USER","STATUS");
	    	$link=array(
	    			'module'=>'registrar','controller'=>'category','action'=>'edit',
	    	);
	    	$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('category_name'=>$link,'par_name'=>$link ));
	    	
    	}catch (Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$frm_search = new Global_Form_FrmSearchMajor();
    	$frm_search = $frm_search->FrmMajors();
    	Application_Model_Decorator::removeAllDecorator($frm_search);
    	$this->view->frm_search = $frm_search;
    }
    
    
    public function addAction(){
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		try {
    			$_dbmodel = new Registrar_Model_DbTable_DbCategory();
    			$_major_id = $_dbmodel->addCategory($_data);
    			if(!empty($_data['save_close'])){
    				Application_Form_FrmMessage::Sucessfull("ការ​បញ្ចូល​ជោគ​ជ័យ !", "/registrar/category/index");
    			}
    			
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message("ការ​បញ្ចូល​មិន​ជោគ​ជ័យ");
    			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    			echo $e->getMessage();
    		}
    			
    	}
    }
    
    public function editAction(){
    	$id = $this->getRequest()->getParam("id");
    	$_dbmodel = new Registrar_Model_DbTable_DbCategory();
    	if($this->getRequest()->isPost()){
    		$_data = $this->getRequest()->getPost();
    		$_data['id']=$id;
    		try {
    			$_major_id = $_dbmodel->addCategory($_data);
    			if(!empty($_data['save_close'])){
    				Application_Form_FrmMessage::Sucessfull("ការ​បញ្ចូល​ជោគ​ជ័យ !", "/registrar/category/index");
    			}
    			 
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message("ការ​បញ្ចូល​មិន​ជោគ​ជ័យ");
    			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    			echo $e->getMessage();
    		}
    	}
    	$d=$this->view->rs_cat=$_dbmodel->getCategoryById($id);
    }
}

