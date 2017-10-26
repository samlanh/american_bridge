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
    	if($this->getRequest()->isPost()){
    		$post = $this->getRequest()->getPost();
    		print_r($post);exit();
    	}
       //$this->_helper->layout()->disableLayout();
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







