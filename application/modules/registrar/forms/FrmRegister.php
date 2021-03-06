<?php 
Class Registrar_Form_FrmRegister extends Zend_Dojo_Form {
	protected $tr=null;
	protected $tvalidate=null ;//text validate
	protected $filter=null;
	protected $t_date=null;
	protected $t_num=null;
	protected $text=null;
	protected $_degree=null;
	protected $_khname=null;
	protected $_enname=null;
	protected $_phone=null;
	protected $_batch=null;
	protected $_year=null;
	protected $_session=null;
	protected $_dob=null;
	protected $_pay_date=null;
	protected $_remark=null;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->tvalidate = 'dijit.form.ValidationTextBox';
		$this->filter = 'dijit.form.FilteringSelect';
		$this->t_date = 'dijit.form.DateTextBox';
		$this->t_num = 'dijit.form.NumberTextBox';
		$this->text = 'dijit.form.TextBox';
		
		$this->_khname = new Zend_Dojo_Form_Element_TextBox('kh_name');
		$this->_khname->setAttribs(array(
				'dojoType'=>$this->text,'class'=>'fullside',
		));
		
		$this->_enname = new Zend_Dojo_Form_Element_TextBox('en_name');
		$this->_enname->setAttribs(array('dojoType'=>$this->tvalidate,
				'required'=>'true',
				'class'=>'fullside',));
		
		$this->_dob = new Zend_Dojo_Form_Element_DateTextBox('dob');
		
		$date = date("Y-m-d")-20;
		$this->_dob->setAttribs(array(
				'data-dojo-Type'=>"dijit.form.DateTextBox",
				//'dojoType'=>"dijit.form.DateTextBox",
				'data-dojo-props'=>"value:'$date','class':'fullside','name':'dob'",
				'required'=>true));
		$this->_dob->setValue($date);
		
		$this->_phone = new Zend_Dojo_Form_Element_TextBox('phone');
		$this->_phone->setAttribs(array(
					'data-dojo-Type'=>$this->tvalidate,
					'data-dojo-props'=>"regExp:'[0-9]{9,10}',
				    'name':'phone',
					'class':'fullside',
				 	'placeHolder': '012345678',
				 	'invalidMessage':'មិនមាន​  ចន្លោះ ឬ សញ្ញា​ពិសេស រឺលើសចំនួនឡើយ'"));
		
		$this->_degree =  new Zend_Dojo_Form_Element_FilteringSelect('degree');
		$this->_degree->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				//'onchange'=>'getTuitionFee();'
		
		));
		$arr_opt = Application_Model_DbTable_DbGlobal::getAllDegreeById();
// 		$arr_opt = array(
// 				1=>$this->tr->translate("ASSOCIATE"),
// 				2=>$this->tr->translate("BACHELOR"),
// 				3=>$this->tr->translate('MASTER'),
// 				4=>$this->tr->translate('DOCTORATE'));
		$this->_degree->setMultiOptions($arr_opt);
		
		
		$this->_batch =  new Zend_Dojo_Form_Element_NumberTextBox("batch");
		$this->_batch->setAttribs(array(
				'onclick'=>'alert(3)',
				'data-dojo-Type'=>$this->tvalidate,'onclick'=>'alert(2)',
				'data-dojo-props'=>"regExp:'[0-9]{1,2}','required':true,
				'name':'batch',
				'onclick':'alert(1)',
				'class':'fullside',
				'invalidMessage':'អាចបញ្ជូលពី 1 ដល់ 99'"));		
		
		
		$this->_year =  new Zend_Dojo_Form_Element_TextBox("year");
		$this->_year->setAttribs(array(
				'data-dojo-Type'=>$this->tvalidate,
				'data-dojo-props'=>"regExp:'[0-5]{1}',
				'name':'year',
				'required':true,'class':'fullside',
				'invalidMessage':'អាចបញ្ជូលពី 1 ដល់  5'"));
		
		$this->_session = new Zend_Dojo_Form_Element_FilteringSelect("session");
		$opt_session = array(
// 				1=>$this->tr->translate('MORNING'),
// 				2=>$this->tr->translate('AFTERNOON'),
// 				3=>$this->tr->translate('EVERNING'),
// 				4=>$this->tr->translate('WEEKEND'),
		);
		$opt_ses=new Application_Model_DbTable_DbGlobal();
		$opt_sesion=$opt_ses->getSession();
		if(!empty($opt_sesion))foreach ($opt_sesion As $rs)$opt_session[$rs['key_code']]=$rs['view_name'];
		$this->_session->setMultiOptions($opt_session);
		$this->_session->setAttribs(array(
				'dojoType'=>$this->filter,
				'required'=>'true',
				'class'=>'fullside',
				'onchange'=>'paymentTerm();'
				));
		//	$pay_date = date('Y-m-d', mktime(date('h'), date('i'), date('s'), date('m'), date('d')+45, date('Y')));
		$this->_pay_date = new Zend_Dojo_Form_Element_DateTextBox('dob');
		$this->_pay_date->setAttribs(array('dojoType'=>$this->t_date,'class'=>'fullside',
				'constraints'=>'{datePattern:"dd/MM/yyyy"'
				));
		$this->_remark = new Zend_Dojo_Form_Element_NumberTextBox('remark');
		$this->_remark->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox','class'=>'fullside',
				'onkeyup'=>'getTotale();netTotal();',
		));
		$this->_remark->setValue(0);
		$this->_pay_date = new Zend_Dojo_Form_Element_TextBox('pay_date');
		$this->_pay_date->setAttribs(array('dojoType'=>$this->t_date,'class'=>'fullside',
				//	'constraints'=>'{datePattern:"dd/MM/yyyy"'
		));
	}
	public function FrmRegistarWU($data=null){
		$_degree = $this->_degree;
		$_khname = $this->_khname;
		$_enname = $this->_enname;
		$_phone  = $this->_phone;
		$_batch  = $this->_batch;
		$_year   = $this->_year;
		$_session= $this->_session;
		$_dob = $this->_dob;
		$_pay_date=$this->_pay_date;
		$_remark=$this->_remark;
		
		$_dob->setValue(date("Y-m-d"));
		
		$_invoice_no = new Zend_Dojo_Form_Element_TextBox('reciept_no');
		$_invoice_no->setAttribs(array(
				'dojoType'=>$this->tvalidate,
				'class'=>'fullside',
				'required'=>'true',
				'readonly'=>'true',
				'style'=>'color:red;'
				));
		$reciept=new Registrar_Model_DbTable_DbRegister();
		$opt=$reciept->getRecieptNo(1,0);
		$_invoice_no->setValue($opt);
		
		$generation = new Zend_Dojo_Form_Element_FilteringSelect('study_year');
		$generation->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside',
				'autoComplete'=>'false',
				'queryExpr'=>'*${0}*',
				'onchange'=>'paymentTerm();',
		));
		$db_years=new Registrar_Model_DbTable_DbRegister();
        $years=$db_years->getAllYears();
        $opt = array(-1=>$this->tr->translate("SELECT_YEAR"));
        if(!empty($years))foreach($years AS $row) $opt[$row['id']]=$row['years'].' '.$row['time'];
		$generation->setMultiOptions($opt);
		
		$rs_metion_opt = Application_Model_DbTable_DbGlobal::getAllMention();
		$metion = new Zend_Dojo_Form_Element_FilteringSelect('metion');
		$metion->setAttribs(array('dojoType'=>$this->filter,
				'class'=>'fullside',
				//'onchange'=>'getTuitionFee();]
		));
		$metion->setMultiOptions($rs_metion_opt);
		
		$_new_student = new Zend_Form_Element_Checkbox('is_new');
		$_new_student->setAttribs(array('dojoType'=>"dijit.form.CheckBox",
				'class'=>'fullside',
				'Onchange'=>"getNewStudent();"));
		
		$_is_hold = new Zend_Form_Element_Checkbox('is_hold');
		$_is_hold->setAttribs(array('dojoType'=>"dijit.form.CheckBox",
				'class'=>'fullside',
			));
		//$_is_hold->setValue(1);
		
		$_year_one = new Zend_Dojo_Form_Element_TextBox('is_year_one');
		$_year_one->setAttribs(array('dojoType'=>"dijit.form.CheckBox",
				'class'=>'fullside',
				'Onchange'=>"getNewStudent();"));
		
		$_studid = new Zend_Dojo_Form_Element_TextBox('stu_id');
		$_studid->setAttribs(array(
				'dojoType'=>$this->text,
				'class'=>'fullside',
				'style'=>'color:red;',
				'readonly'=>'true',
				));
		
		$_sex =  new Zend_Dojo_Form_Element_FilteringSelect('sex');
		$_sex->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside',));
		$sex_opt = array(
				1=>$this->tr->translate("MALE"),
				2=>$this->tr->translate("FEMALE"));
		$_sex->setMultiOptions($sex_opt);
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$rows = $_db->getAllFecultyNamess(1);
		$opt = array();//array(-1=>$this->tr->translate("SELECT_DEPT"));
		if(!empty($rows))foreach($rows AS $row) $opt[$row['dept_id']]=$row['en_name'];
		 
		$_dept = new Zend_Dojo_Form_Element_FilteringSelect("dept");
		$_dept->setMultiOptions($opt);
		$_dept->setAttribs(array(
				'dojoType'=>$this->filter,
				'required'=>'true',
				'class'=>'fullside',
				'onchange'=>'changeMajor();'));
		
		$opt_marjor = array(-1=>$this->tr->translate("SELECT_MAJOR"));
		$_major = new Zend_Dojo_Form_Element_FilteringSelect("major");
		$_major->setAttribs(array(
				'dojoType'=>$this->filter,
				'required'=>'true',
				'class'=>'fullside'));
		
		  $_term = new Zend_Dojo_Form_Element_FilteringSelect("payment_term");
		  $opt_term = $_db->getAllPaymentTerm(null,null);
		  $_term->setMultiOptions($opt_term);
		  $_term->setAttribs(array(
		  		'dojoType'=>$this->filter,
		  		'required'=>'true',
		  		'class'=>'fullside',
		  		'autoComplete'=>'false',
		  		'queryExpr'=>'*${0}*',
		  		'onchange'=>'paymentTerm();getDateTerm(1);displayAmountSection();'
		  		));
		
		$_fee = new Zend_Dojo_Form_Element_NumberTextBox('tuitionfee');
		$_fee->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'required'=>'true','class'=>'fullside',
				
				//'onkeyup'=>'CheckAmount();',
				'onkeyup'=>'getDisccount();getTotale();netTotal();',
				'readOnly'=>'true'
				));

		$_disc_percent = new Zend_Dojo_Form_Element_NumberTextBox('discount');
		$_disc_percent->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'onkeyup'=>'getDisccount();getTotale();netTotal();'
				//'onkeyup'=>'CheckAmount();'
				//'onkeyup'=>'getTotale();',
				));
		$_disc_percent->setValue(0);
		
		$_disc_fix = new Zend_Dojo_Form_Element_NumberTextBox('discount_fix');
		$_disc_fix->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'onkeyup'=>'getDisccount();getTotale();netTotal();'
				//'onkeyup'=>'CheckAmount();'
				//'onkeyup'=>'getTotale();',
		));
		$_disc_fix->setValue(0);
		
		$total = new Zend_Dojo_Form_Element_NumberTextBox('total');
		$total->setAttribs(array(
				'dojoType'=>$this->text,
				'class'=>'fullside',
				'style'=>'color:red;',
				'readOnly'=>'true'
				));
		$total->setValue(0);
		
		$remaining = new Zend_Dojo_Form_Element_NumberTextBox('remaining');
		$remaining->setAttribs(array(
				'dojoType'=>$this->text,
				'class'=>'fullside',
				'style'=>'color:blue',
				'readOnly'=>'true'
		));
		$remaining->setValue(0);
		
		$addmin_fee = new Zend_Dojo_Form_Element_NumberTextBox('addmin_fee');
		$addmin_fee->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'onkeyup'=>'getTotale();netTotal();'
		));
		$addmin_fee->setValue(0);
		
		$material_fee = new Zend_Dojo_Form_Element_NumberTextBox('material_fee');
		$material_fee->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'onkeyup'=>'getTotale();netTotal();'
		));
		$material_fee->setValue(0);
		
		$books = new Zend_Dojo_Form_Element_NumberTextBox('books');
		$books->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'style'=>'color:red',
				'onkeyup'=>'getRemaining();netTotal();'
		));
		$books->setValue(0);
		
		$not = new Zend_Dojo_Form_Element_TextBox('not');
		$not->setAttribs(array(
				'dojoType'=>$this->text,
				'class'=>'fullside',
		));
		
		$char_price = new Zend_Dojo_Form_Element_TextBox('char_price');
		$char_price->setAttribs(array(
				'dojoType'=>$this->text,
				'class'=>'fullside',
		));
		
		$start_date= new Zend_Dojo_Form_Element_DateTextBox('start_date');
		$date = date("Y-m-d");
		$start_date->setAttribs(array(
				'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'onChange'=>'getDateTerm(1);',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'required'=>true));
		$start_date->setValue($date);
		
		$end_date= new Zend_Dojo_Form_Element_DateTextBox('end_date');
		$date = date("Y-m-d");
		$end_date->setAttribs(array(
				'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'onChange'=>'getDateTerm(2);',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'required'=>true));
		$end_date->setValue($date);
		
		$_paid = new Zend_Dojo_Form_Element_NumberTextBox('payment_paid');
		$_paid->setAttribs(array(
				'dojoType'=>$this->t_num,
				'required'=>'true','class'=>'fullside',));
		
		$student_type = new Zend_Dojo_Form_Element_FilteringSelect('student_type');
		$student_type->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required'=>'true',
				'class'=>'fullside',
				'autoComplete'=>'false',
				'queryExpr'=>'*${0}*',
				'onchange'=>'changControll();paymentTerm();',
		));
		$opts = array(  3=>$this->tr->translate('OLD_STUDENT'),
						1=>$this->tr->translate('NEW_STUDENT'),
						4=>$this->tr->translate('DROP_STUDENT')
				  );
	    $student_type->setMultiOptions($opts);
				
		$old_studens = new Zend_Dojo_Form_Element_FilteringSelect('old_studens');
		$old_studens->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect',
						'class'=>'fullside',
						'autoComplete'=>'false',
						'queryExpr'=>'*${0}*',
						'class'=>'fullside',
				        'onchange'=>'getGeneralOldStudentById(1);paymentTerm();getStartDate();',
				));
		
		$opt_ger=$reciept->getAllGerneralOldStudent();
		$opts=array(-1=>$this->tr->translate("student id"));
		if(!empty($opt_ger))foreach($opt_ger AS $row) $opts[$row['stu_id']]=$row['stu_code'];
		$old_studens->setMultiOptions($opts);
		
		
		$old_studen_name = new Zend_Dojo_Form_Element_FilteringSelect('old_stu_name');
		$old_studen_name->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>'false',
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onchange'=>'setID(1);',
		));
		$opt_ger_name=$reciept->getAllGerneralOldStudentName();
		$opts=array(-1=>$this->tr->translate("STUDENT_NAME"));
		if(!empty($opt_ger_name))foreach($opt_ger_name AS $row) $opts[$row['stu_id']]=$row['name'];
		$old_studen_name->setMultiOptions($opts);
		
/////////////////////////////////////////////////// drop student ///////////////////////////////////////////////////////////////////////////////////////////////////		
		
		$drop_studens = new Zend_Dojo_Form_Element_FilteringSelect('drop_studens');
		$drop_studens->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>'false',
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onchange'=>'getGeneralOldStudentById(2);paymentTerm();getStartDate();',
		));
		
		$opt_ger=$reciept->getAllDropStudentID(1);
		
		$opts=array(-1=>$this->tr->translate("student id"));
		if(!empty($opt_ger))foreach($opt_ger AS $row) $opts[$row['stu_id']]=$row['stu_code'];
		$drop_studens->setMultiOptions($opts);
		
	////////////////////////////////////////////////////////////////////////////////////////
		
		$drop_stu_name = new Zend_Dojo_Form_Element_FilteringSelect('drop_stu_name');
		$drop_stu_name->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>'false',
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onchange'=>'setID(2);',
		));
		$opt_ger_name=$reciept->getAllDropStudentName(1);
		$opts=array(-1=>$this->tr->translate("STUDENT_NAME"));
		if(!empty($opt_ger_name))foreach($opt_ger_name AS $row) $opts[$row['stu_id']]=$row['name'];
		$drop_stu_name->setMultiOptions($opts);
		
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		
		$_studname = new Zend_Dojo_Form_Element_TextBox('stu_name');
		$_studname->setAttribs(array('dojoType'=>$this->text,'class'=>'fullside',
				'style'=>'color:red;','readonly'=>'true'));
		
		
		$_paid_kh = new Zend_Dojo_Form_Element_Textarea('paid_kh');
		$_paid_kh->setAttribs(array(
				'dojoType'=>$this->text,'class'=>'fullside',));
		
		$id = new Zend_Form_Element_Hidden('id');
		
		$ids = new Zend_Form_Element_Hidden('ids');
		$ids->setAttribs(array('dojoType'=>$this->text,
						'class'=>'fullside',
						'required'=>'true',
						'class'=>'fullside',
				));
		$parent = new Zend_Form_Element_Hidden("parent_id");
		
		if($data!=null){
			$parent->setValue($data["is_parent"]);
			$id->setValue($data['stu_id']);
			$_studid->setValue($data['stu_code']);
			$_invoice_no->setValue($data['receipt_number']);
			$this->_khname->setValue($data['stu_khname']);
			$this->_enname->setValue($data['stu_enname']);
			$_sex->setValue($data['sex']);
			$_session->setValue($data['session']);
			$generation->setValue($data['academic_year']);
			$_term->setValue($data['payment_term']);
			$_fee->setValue($data['fee']);
			$_disc_percent->setValue($data['discount_percent']);
			$_disc_fix->setValue($data['discount_fix']);
			$_remark->setValue($data['other_fee']);
			$addmin_fee->setValue($data['admin_fee']);
			$material_fee->setValue($data['material_fee']);
			$total->setValue($data['total_payment']);
			$books->setValue($data['paid_amount']);
			$remaining->setValue($data['balance_due']);
			$char_price->setValue($data['amount_in_khmer']);
			$not->setValue($data['note']);
			$student_type->setValue($data['student_type']);
			$old_studens->setValue($data['stu_id']);
			$start_date->setValue($data['start_date']);
			$end_date->setValue($data['validate']);
			$old_studen_name->setValue($data['stu_id']);
			$drop_studens->setValue($data['stu_id']);
			$drop_stu_name->setValue($data['stu_id']);	
		}
		$this->addElements(array(
			 $material_fee,$drop_stu_name,$drop_studens,$parent,$old_studens,$old_studen_name,$_studname,$student_type,$ids,$id,$generation,$char_price,$end_date,$start_date,$not,$books,$addmin_fee,$remaining,$total, $_year_one,$_new_student,$_invoice_no, $_pay_date, $_khname, $_enname,$_studid, $_sex,$_dob,$_degree,$metion,
			  $_phone,$_dept,$_major,$_batch,$_year,$_session,$_term,$_fee,$_disc_fix,$_disc_percent,$_paid,$_paid_kh,$_remark,$_is_hold ));
		
		return $this;
	}
		
	
}