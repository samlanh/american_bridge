
<?php
class Gep_LanguageController extends Zend_Controller_Action {
	
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db= new Gep_Model_DbTable_DbLanguage();
		$rows = $db->getAllDegreeLanguage();
		$list = new Application_Form_Frmtable();
		if(!empty($rows)){
			} 
			else{
				$result = Application_Model_DbTable_DbGlobal::getResultWarning();
			}
			$collumns = array("LANGUAGE_LEVEL","MODIFY_DATE","DISCRIPTION","STATUS","USER");
			$link=array(
					'module'=>'gep','controller'=>'language','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rows,array('title'=>$link,'modify_date'=>$link));
	}
	function addAction(){
		if($this->getRequest()->isPost()){
			try{
			$data = $this->getRequest()->getPost();
			$db = new Gep_Model_DbTable_DbLanguage();
			$row = $db->addDegreeLanguage($data);
			if(isset($data['save_close'])){
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/gep/language/index");
			}else{
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/gep/language/add");
			}
			Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
	}
	function editAction(){
		$id = $this->getRequest()->getParam('id');
		$db = new Gep_Model_DbTable_DbLanguage();
		$row = $db->getDegreeLanguageByID($id);
		$this->view->rr = $row;
		
		if($this->getRequest()->isPost())
		{
			try{
				$data = $this->getRequest()->getPost();
				$rows=$db->editDegree($data, $id);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/gep/language/index");
			}catch(Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
	}
	
}
?>
