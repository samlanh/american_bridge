<?php

class Home_IndexController extends Zend_Controller_Action
{
    public function init()
    {    	
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	
	}

    public function indexAction()
    {
    	$search = array(
    			'end_date' => date("Y-m-d"),
    			'service'  => "",
    			'degree_all'  => "",
    			'grade_all'  => "",
    			);
    	$db = new Registrar_Model_DbTable_DbRptStudentNearlyEndService();
    	$abc = $this->view->row = $db->getAllStudentNearlyEndService($search);
    }

    public function viewAction()
    {
       
    }

    public function addAction()
    {
      
    }

    public function editedAction()
    {
       
    }


}







