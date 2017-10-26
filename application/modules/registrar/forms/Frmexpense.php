<?php 
Class Registrar_Form_Frmexpense extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		
	}
	public function FrmAddExpense($data=null){
		$db = new Application_Model_DbTable_DbGlobal();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$title = new Zend_Dojo_Form_Element_ValidationTextBox('title');
		$title->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				));
		
		$for_date = new Zend_Dojo_Form_Element_FilteringSelect('for_date');
		$for_date->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside'
		));
		$options= array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5",6=>"6",7=>"7",8=>"8",9=>"9",10=>"10",11=>"11",12=>"12");
		$for_date->setMultiOptions($options);
		
		$_Date = new Zend_Dojo_Form_Element_DateTextBox('Date');
		$_Date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}"
				
		));
		$_Date->setValue(date('Y-m-d'));
		
		$_branch_id = new Zend_Dojo_Form_Element_FilteringSelect('branch_id');
		$_branch_id->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>'filterClient();'
		));
		
		$db = new Application_Model_DbTable_DbGlobal();
		
		$_stutas = new Zend_Dojo_Form_Element_FilteringSelect('Stutas');
		$_stutas ->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',			
		));
		$options= array(1=>"ប្រើប្រាស់",2=>"មិនប្រើប្រាស់");
		$_stutas->setMultiOptions($options);
		
		$_Description = new Zend_Dojo_Form_Element_Textarea('Description');
		$_Description ->setAttribs(array(
				'dojoType'=>'dijit.form.SimpleTextarea',
				'class'=>'fullside',
				'style'=>'width:98%',
		));
		$total_amount=new Zend_Dojo_Form_Element_NumberTextBox('total_amount');
		$total_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				//'onkeyup'=>'convertToDollar();',
		));
		
		$convert_to_dollar=new Zend_Dojo_Form_Element_NumberTextBox('convert_to_dollar');
		$convert_to_dollar->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				//'required'=>true
		));
		
		$invoice=new Zend_Dojo_Form_Element_TextBox('invoice');
		$invoice->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
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
		
		$id = new Zend_Form_Element_Hidden("id");
		$_currency_type = new Zend_Dojo_Form_Element_FilteringSelect('currency_type');
		$_currency_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				//'onchange'=>'convertToDollar();',
		));
		$opt = $db->getViewById(8,1);
		$_currency_type->setMultiOptions($opt);
         		
		if($data!=null){
			//print_r($data);exit();
			$_currency_type->setValue($data['curr_type']);
			//$_branch_id->setValue($data['branch_id']);
			$title->setValue($data['title']);
			$_category->setValue($data['cat_id']);
			$total_amount->setValue($data['total_amount']);
			//$convert_to_dollar->setValue($data['amount_in_dollar']);
			$for_date->setValue($data['for_date']);
			$_Description->setValue($data['desc']);
			$_Date->setValue($data['create_date']);
			$_stutas->setValue($data['status']);
			$invoice->setValue($data['invoice']);
			$id->setValue($data['id']);
		}
		
		$this->addElements(array($_category,$_cat_expend,$invoice,$_currency_type,$title,$_Date ,$_stutas,$_Description,
				$total_amount,$convert_to_dollar,$for_date,$id,));
		return $this;
		
	}	
}