<?php 
Class Registrar_Form_FrmSearchexpense extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function AdvanceSearch($data=null){
		
		$db = new Application_Model_DbTable_DbGlobal();
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside'));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL"),
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		$_status->setValue($request->getParam("status"));
		
		$_title = new Zend_Dojo_Form_Element_TextBox('adv_search');
		$_title->setAttribs(array('dojoType'=>'dijit.form.TextBox',
				'onkeyup'=>'this.submit()',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("ADVANCE_SEARCH")
		));
		$_title->setValue($request->getParam("adv_search"));
		
		
		$_currency_type = new Zend_Dojo_Form_Element_FilteringSelect('currency_type');
		$_currency_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside'
		));
		$opt = array(-1=>"--Select Currency Type--",1=>"Dollar",2=>'Khmer',3=>"Bath");
		$_currency_type->setMultiOptions($opt);
		$_currency_type->setValue($request->getParam("currency_type"));
		
		$_category = new Zend_Dojo_Form_Element_FilteringSelect('cat_income');
		$_category->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required'=>false
				//'onchange'=>'convertToDollar();',
		));
		$_dbs = new Application_Model_DbTable_DbGlobal();
		$rows = $_dbs->getCategoryName(1);
		$opt =array(''=>$this->tr->translate("PLEASE_SELECTED"));
		if(!empty($rows))foreach($rows AS $row) $opt[$row['id']]=$row['name'];
		$_category->setMultiOptions($opt);
		$_category->setValue($request->getParam("cat_income"));
		
		///category expend
		$_cat_expend = new Zend_Dojo_Form_Element_FilteringSelect('cat_expend');
		$_cat_expend->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required'=>false
				//'onchange'=>'convertToDollar();',
		));
		$rows = $_dbs->getCategoryName(0);
		$opt =array(''=>$this->tr->translate("PLEASE_SELECTED"));
		if(!empty($rows))foreach($rows AS $row) $opt[$row['id']]=$row['name'];
		$_cat_expend->setMultiOptions($opt);
		$_cat_expend->setValue($request->getParam("cat_expend"));
		
		$_releasedate = new Zend_Dojo_Form_Element_DateTextBox('start_date');
		$_releasedate->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				'onchange'=>'CalculateDate();',
				'class'=>'fullside'));
		$_date = $request->getParam("start_date");
		
		if(empty($_date)){
			$_date = date('Y-m-d');
		}
		$_releasedate->setValue($_date);
		
		
		$_dateline = new Zend_Dojo_Form_Element_DateTextBox('end_date');
		$_dateline->setAttribs(array('dojoType'=>'dijit.form.DateTextBox','required'=>'true','class'=>'fullside'
		));
		$_date = $request->getParam("end_date");
		
		if(empty($_date)){
			$_date = date("Y-m-d");
		}
		$_dateline->setValue($_date);
		
		
		
		
		$this->addElements(array($_title,$_currency_type,$_releasedate,$_category,$_cat_expend
				,$_dateline,$_status));
		return $this;
		
	}	
	
}