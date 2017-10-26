<?php
class Global_CarController extends Zend_Controller_Action {
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
						);
			}
			else{
			
				$search = array(
						'title' => '',
				);
			
			}
			$db = new Global_Model_DbTable_DbCar();
			$rs_rows= $db->getAllCars($search);
	
// 			$glClass = new Application_Model_GlobalClass();
// 			$rs = $glClass->getImgActive($rs_rows, BASE_URL, true);
			
			$this->view->search =  $search;
			$list = new Application_Form_Frmtable();
			$collumns = array("CAR_ID","DRIVER_NAME","PHONE","NOTE","CREATE_DATE");
			$link=array(
					'module'=>'global','controller'=>'car','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('carname'=>$link,'carid'=>$link,'drivername'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
	}
	
	function addAction()
	{
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$_dbcar = new Global_Model_DbTable_DbCar();
				$_dbcar->addcar($_data);
				if(isset($_data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/global/car");
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/global/car/add");
				}
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err = $e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		
	}
	
	public function editAction()
	{
		
		$db=new Global_Model_DbTable_DbCar();
		$id=$this->getRequest()->getParam("id");
		$this->view->rs = $row=$db->getCarById($id);
		if($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getPost();
			$db = new Global_Model_DbTable_DbCar();
			$db->updateCar($data);
			Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/global/car/index");
		}
		
		
	}
}

