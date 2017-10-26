<?php 
Class Accounting_Form_FrmSearchMajor extends Zend_Dojo_Form{
	protected $tr = null;
	protected $btn =null;//text validate
	protected $filter = null;
	protected $text =null;
	protected $validate = null;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->filter = 'dijit.form.FilteringSelect';
		$this->text = 'dijit.form.TextBox';
		$this->btn = 'dijit.form.Button';
		$this->validate = 'dijit.form.ValidationTextBox';
	}
	public function FrmMajors($_data=null){
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
	
		$_title = new Zend_Dojo_Form_Element_TextBox('title');
		$_title->setAttribs(array('dojoType'=>$this->text,'class'=>'fullside',
				'placeholder'=>$this->tr->translate("SEARCH_INFO")));
		$_title->setValue($request->getParam("title"));
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status_search');
		$_status->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside',));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL_STATUS"),
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DEACTIVE"));
		$_status->setMultiOptions($_status_opt);
		$_status->setValue($request->getParam("status_search"));
		
		//date
		$start_date= new Zend_Dojo_Form_Element_DateTextBox('start_date');
		$start_date->setAttribs(array(
				'dojoType'=>"dijit.form.DateTextBox",'class'=>'fullside',
				'required'=>false,
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				));
		$_date = $request->getParam("start_date");
		if(empty($_date)){
			$_date = date('Y-m-01');
		}
		$start_date->setValue($_date);
		
		$end_date= new Zend_Dojo_Form_Element_DateTextBox('end_date');
		$end_date->setAttribs(array(
				'dojoType'=>"dijit.form.DateTextBox",'class'=>'fullside',
				'required'=>false,
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				));
		$_date = $request->getParam("end_date");
		if(empty($_date)){
			$_date = date("Y-m-d");
		}
		$end_date->setValue($_date);
		 
		//customer name 
		$cus_name = new Zend_Dojo_Form_Element_FilteringSelect("cus_name");
		$cus_name->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true','class'=>'fullside',
		));
		$option = array("0"=>$this->tr->translate("SELECT_CUSTOMER_NAME"));
		$db_stu=new Accounting_Model_DbTable_DbCustomerPayment();
		$result = $db_stu->getAllCustomerName();
		//print_r($result);exit();
		if(!empty($result))foreach($result AS $row){
			$option[$row['id']]=$row['name'];
		}
		$cus_name->setMultiOptions($option);
		$cus_name->setValue($request->getParam('cus_name'));
		
		$this->addElements(array($cus_name,$start_date,$end_date,$_title,$_status));
		
		return $this;
	}
	
	public function frmSearchReturn($_data=null){}
}