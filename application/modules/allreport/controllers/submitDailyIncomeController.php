<?php
class Allreport_submitDailyIncomeController extends Zend_Controller_Action {
	
	
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
	
	}
	public function khmerFulltimeAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
	
				//print_r($data);exit();
	
				$db = new Allreport_Model_DbTable_DbSubmitDailyIncome();
				$db->SubmitDailyIncome($data,1,1);// 1=payfor_type(KFT) , 1=degree_type(KFT)
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/allreport/accounting/rpt-daily-income-khmer-fulltime");
				}
			}
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function englishFulltimeAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				
 				//print_r($data);exit();
				
				$db = new Allreport_Model_DbTable_DbSubmitDailyIncome();
				$db->SubmitDailyIncome($data,6,2);// 6=payfor_type(EFT) , 2=degree_type(EFT)
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/allreport/accounting/rpt-daily-income-english-fulltime");
				}
			}
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function englishParttimeAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
	
				//print_r($data);exit();
	
				$db = new Allreport_Model_DbTable_DbSubmitDailyIncome();
				$db->SubmitDailyIncome($data,2,3);// 2=payfor_type(EPT) , 3=degree_type(EPT)
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/allreport/accounting/rpt-daily-income-english-parttime");
				}
			}
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function carAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
	
				//print_r($data);exit();
	
				$db = new Allreport_Model_DbTable_DbSubmitDailyIncome();
				$db->SubmitDailyIncome($data,3,0);// 3=payfor_type(Transport) , 0=degree_type(no need)
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/allreport/accounting/rpt-daily-income-transport");
				}
			}
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function foodAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
	
				//print_r($data);exit();
	
				$db = new Allreport_Model_DbTable_DbSubmitDailyIncome();
				$db->SubmitDailyIncome($data,4,0);// 4=payfor_type(Food) , 0=degree_type(no need)
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/allreport/accounting/rpt-daily-income-foodandstay");
				}
			}
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function productAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
	
				//print_r($data);exit();
	
				$db = new Allreport_Model_DbTable_DbSubmitDailyIncome();
				$db->SubmitDailyIncome($data,5,0);// 5=payfor_type(Product) , 0=degree_type(no need)
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/allreport/accounting/rpt-daily-income-material");
				}
			}
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	public function canteenAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
	
				//print_r($data);exit();
	
				$db = new Allreport_Model_DbTable_DbSubmitDailyIncome();
				$db->SubmitDailyIncome($data,7,0);// 7=payfor_type(canteen) , 0=degree_type(no need)
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/allreport/accounting/rpt-daily-income-parking-canteen");
				}
			}
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	function parkingAction(){
		try{
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
	
				//print_r($data);exit();
	
				$db = new Allreport_Model_DbTable_DbSubmitDailyIncome();
				$db->SubmitDailyIncome($data,8,0);// 8=payfor_type(parking) , 0=degree_type(no need)
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/allreport/accounting/rpt-parking-payment");
				}
			}
		}catch(Exception $e){
			Application_Form_FrmMessage::message("APPLICATION_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
}

