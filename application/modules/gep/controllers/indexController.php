<?php
class Gep_indexController extends Zend_Controller_Action {
	
	
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}

    public function indexAction()
    {
    	
    
	}
	function addAction(){
		$_model =new Foundation_Model_DbTable_DbNewStudent();
		if($this->getRequest()->isPost()){
			try{
				$_data = $this->getRequest()->getPost();
					
				$_model->addNewStudent($_data);
				if(!empty($_data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/foundation/index");
				}
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$_frm = new Foundation_Form_FrmStudent();
		$_frmstudent=$_frm->FrmStudent();
		Application_Model_Decorator::removeAllDecorator($_frmstudent);
		$this->view->frm_student = $_frmstudent;
		// 		$_db = new Application_Model_DbTable_DbGlobal();
		// 		$comp = $_db->getallComposition();
		// 		array_unshift($comp, array ( 'id' => -1, 'name' => 'áž”áž“áŸ’áž�áŸ‚áž˜â€‹áž¢áŸ’áž“áž€â€‹áž‘áž‘áž½áž›â€‹áž�áŸ’áž˜áž¸') );
		// 		$this->view->compo=$comp;
			
		// 		$situation = $_db->getallSituation();
		// 		array_unshift($situation, array ( 'id' => -1, 'name' => 'áž”áž“áŸ’áž�áŸ‚áž˜â€‹áž¢áŸ’áž“áž€â€‹áž‘áž‘áž½áž›â€‹áž�áŸ’áž˜áž¸') );
		// 		$this->view->situation=$situation;
			
		// 		$school = $_db->getAllHighSchool();
		// 		array_unshift($school, array ( 'id' => -1, 'name' => 'áž”áž“áŸ’áž�áŸ‚áž˜â€‹áž¢áŸ’áž“áž€â€‹áž‘áž‘áž½áž›â€‹áž�áŸ’áž˜áž¸','province_id'=>-1) );
		// 		$this->view->highschool=$school;
			
		// 		$scholarship = $_db->getallScholarship();
		// 		array_unshift($scholarship, array ( 'id' => -1, 'name' => 'áž”áž“áŸ’áž�áŸ‚áž˜â€‹áž¢áŸ’áž“áž€â€‹áž‘áž‘áž½áž›â€‹áž�áŸ’áž˜áž¸','province_id'=>-1) );
		// 		$this->view->scholarship=$scholarship;
	}
	
	

}
