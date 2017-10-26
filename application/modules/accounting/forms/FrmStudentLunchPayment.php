<?php 
Class Accounting_Form_FrmStudentLunchPayment extends Zend_Dojo_Form {
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
				'dojoType'=>$this->tvalidate,'class'=>'fullside','readonly'=>'readonly',
		));
		
		$this->_enname = new Zend_Dojo_Form_Element_TextBox('en_name');
		$this->_enname->setAttribs(array('dojoType'=>$this->tvalidate,
				//'required'=>'true',
				'class'=>'fullside',
				//'style'=>'width:288px',
				'readonly'=>'readonly',));
		
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
		//	$pay_date = date('Y-m-d', mktime(date('h'), date('i'), date('s'), date('m'), date('d')+45, date('Y')));
		$this->_pay_date = new Zend_Dojo_Form_Element_DateTextBox('dob');
		$this->_pay_date->setAttribs(array('dojoType'=>$this->t_date,'class'=>'fullside',
				'constraints'=>'{datePattern:"dd/MM/yyyy"'
				));
		$this->_remark = new Zend_Dojo_Form_Element_NumberTextBox('remark');
		$this->_remark->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox','class'=>'fullside',
				'onkeyup'=>'getTotale();',
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
		$_invoice_no->setAttribs(array('dojoType'=>$this->tvalidate,'class'=>'fullside',
				//'onkeyup'=>'CheckReceipt()'
				'required'=>'true',
				'readonly'=>'true',
				'style'=>'color:red;'
				));
		$reciept=new Registrar_Model_DbTable_DbRegister();
		$opt=$reciept->getRecieptNo();
		$_invoice_no->setValue($opt);
		
		$generation = new Zend_Dojo_Form_Element_FilteringSelect('study_year');
		$generation->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',
				//'onkeyup'=>'CheckReceipt()'
				'required'=>'false',
				'class'=>'fullside',
		));
		$db_years=new Registrar_Model_DbTable_DbStudentServicePayment();
		$years=$db_years->getYearService();
        $opt = array(-1=>$this->tr->translate("SELECT_YEAR"));
        if(!empty($years))foreach($years AS $row) $opt[$row['id']]=$row['years'];
		$generation->setMultiOptions($opt);
		
		
		$_studid = new Zend_Dojo_Form_Element_TextBox('stu_id');
		$_studid->setAttribs(array('dojoType'=>$this->text,'class'=>'fullside','style'=>'color:red;','readonly'=>'true'));
		
		$_sex =  new Zend_Dojo_Form_Element_FilteringSelect('sex');
		$_sex->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside','readonly'=>'true'));
		$sex_opt = array(
				1=>$this->tr->translate("MALE"),
				2=>$this->tr->translate("FEMALE"));
		$_sex->setMultiOptions($sex_opt);
		
		$room =  new Zend_Dojo_Form_Element_FilteringSelect('room');
		$room->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside',));
		$db_room=new Application_Model_DbTable_DbGlobal();
		$opt = $db_room->getRoom();
		$opts=array();
		if(!empty($opt))foreach($opt AS $row) $opts[$row['room_id']]=$row['room_name'];
		$room->setMultiOptions($opts);
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$rows = $_db->getAllFecultyName();
		$opt = '' ;//array(-1=>$this->tr->translate("SELECT_DEPT"));
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
		  $opt_term = $_db->getAllPaymentTerm();
// 		  $opt_term = array(
// 		  		1=>$this->tr->translate('QUARTER'),
// 		  		2=>$this->tr->translate('SEMESTER'),
// 		  		3=>$this->tr->translate('YEAR'),
// 		  		4=>$this->tr->translate('OTHER')
// 		  );
		  $_term->setMultiOptions($opt_term);
		  $_term->setAttribs(array(
		  		'dojoType'=>$this->filter,
		  		'required'=>'true',
		  		'class'=>'fullside',
		  		'onchange'=>'paymentTerm();'
		  		));
		
		$_fee = new Zend_Dojo_Form_Element_NumberTextBox('tuitionfee');
		$_fee->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'required'=>'true','class'=>'fullside',
				//'onkeyup'=>'CheckAmount();',
				));

		$_disc = new Zend_Dojo_Form_Element_NumberTextBox('discount');
		$_disc->setAttribs(array(
				'dojoType'=>$this->text,
				'class'=>'fullside',
				'onkeyup'=>'getDisccount();getTotale();'
				//'onkeyup'=>'CheckAmount();'
				//'onkeyup'=>'getTotale();',
				));
		$_disc->setValue(0);
		
		$total = new Zend_Dojo_Form_Element_NumberTextBox('total');
		$total->setAttribs(array(
				'dojoType'=>$this->text,
				'class'=>'fullside',
				'style'=>'color:red;'
				));
		$total->setValue(0);
		
		$remaining = new Zend_Dojo_Form_Element_NumberTextBox('remaining');
		$remaining->setAttribs(array(
				'dojoType'=>$this->text,
				'class'=>'fullside',
				'style'=>'color:blue'
		));
		$remaining->setValue(0);
		
		$addmin_fee = new Zend_Dojo_Form_Element_NumberTextBox('addmin_fee');
		$addmin_fee->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'onkeyup'=>'getTotale();'
		));
		$addmin_fee->setValue(0);
		
		$books = new Zend_Dojo_Form_Element_NumberTextBox('books');
		$books->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'style'=>'color:red',
				'onkeyup'=>'getRemaining();'
		));
		$books->setValue(0);
		
		$not = new Zend_Dojo_Form_Element_TextBox('not');
		$not->setAttribs(array(
				'dojoType'=>$this->text,
				//'style'=>'width:288px;',
				'class'=>'fullside',
		));
		
		$char_price = new Zend_Dojo_Form_Element_TextBox('char_price');
		$char_price->setAttribs(array(
				'dojoType'=>$this->text,
				'class'=>'fullside',
				'style'=>'width:250px;',
				
		));
		
		$start_date= new Zend_Dojo_Form_Element_DateTextBox('start_date');
		$date = date("Y-m-d")-1;
		$start_date->setAttribs(array(
				'data-dojo-Type'=>"dijit.form.DateTextBox",
				'data-dojo-props'=>"value:'$date','class':'fullside','name':'dob'",
				'required'=>true));
		$start_date->setValue($date);
		
		$end_date= new Zend_Dojo_Form_Element_DateTextBox('end_date');
		$date = date("Y-m-d");
		$end_date->setAttribs(array(
				'data-dojo-Type'=>"dijit.form.DateTextBox",
				'data-dojo-props'=>"value:'$date','class':'fullside','name':'dob'",
				'required'=>true));
		$end_date->setValue($date);
		
		$_paid = new Zend_Dojo_Form_Element_NumberTextBox('payment_paid');
		$_paid->setAttribs(array(
				'dojoType'=>$this->t_num,
				'required'=>'true','class'=>'fullside',));
		
		$_paid_kh = new Zend_Dojo_Form_Element_Textarea('paid_kh');
		$_paid_kh->setAttribs(array(
				'dojoType'=>$this->text,'class'=>'fullside',));
		$session = new Zend_Dojo_Form_Element_FilteringSelect('session');
		$session->setAttribs(array(
				'dojoType'=>$this->filter,
				'required'=>'true',
				'class'=>'fullside',));
		$opt_session = array(
				1=>$this->tr->translate('FULL_TIME'),
				2=>$this->tr->translate('PART_TIME'),
		);
		$session->setMultiOptions($opt_session);
		
// 		$id = new Zend_Form_Element_Hidden('id');
 		if($data!=null){
// 			//print_r($data);exit();
// 			$id->setValue($data['stu_id']);
// 			$_studid->setValue($data['stu_code']);
 			$_invoice_no->setValue($data['receipt_number']);
// 			$this->_khname->setValue($data['stu_khname']);
// 			$this->_enname->setValue($data['stu_enname']);
// 			$_sex->setValue($data['sex']);
// 			$session->setValue($data['session']);
// 			$generation->setValue($data['academic_year']);
// 			$_term->setValue($data['payment_term']);
// 			$_fee->setValue($data['tuition_fee']);
// 			$_disc->setValue($data['discount_percent']);
// 			$_remark->setValue($data['other_fee']);
// 			$addmin_fee->setValue($data['admin_fee']);
// 			$total->setValue($data['total']);
// 			$books->setValue($data['paid_amount']);
// 			$remaining->setValue($data['balance_due']);
// 			$char_price->setValue($data['amount_in_khmer']);
// 			$not->setValue($data['note']);
// 			$room->setValue($data['room_id']);
// 			$old_studens->setValue($data['stu_id']);
 		}
		$this->addElements(array(
			  $room,$session,/*$id,*/$generation,$char_price,$end_date,$start_date,$not,$books,$addmin_fee,$remaining,$total ,$_invoice_no, $_pay_date, $_khname, $_enname,$_studid, $_sex,$_dob,$_degree,
			  $_phone,$_dept,$_major,$_batch,$_year,$_session,$_term,$_fee,$_disc,$_paid,$_paid_kh,$_remark  ));
		
		return $this;
	}
	public function FrmStudentRequest($data=null){
		
		$_degree = $this->_degree;
		$_khname = $this->_khname;
		$_enname = $this->_enname;
		$_phone  = $this->_phone;
		$_batch  = $this->_batch;
		$_year   = $this->_year;
		$_session= $this->_session;
		$_dob = $this->_dob;
		$_pay_date=$this->_pay_date;
		$_remark = $this->_remark;
		
		$_reciept_no = new Zend_Dojo_Form_Element_TextBox('reciept_no');
		$_reciept_no->setAttribs(array('dojoType'=>$this->t_num,'class'=>'fullside','style'=>'',
				//'onkeyup'=>'CheckReceipt()'
				'dojoType'=>$this->t_num,
				'required'=>'true',
				'style'=>'color: red;'
				));
		
		$_studid = new Zend_Dojo_Form_Element_TextBox('stu_id');
		$_studid->setAttribs(array('dojoType'=>$this->text,'class'=>'fullside',));
		
		$_pob = new Zend_Dojo_Form_Element_TextBox('pob');
		$_pob->setAttribs(array('dojoType'=>$this->text,'class'=>'fullside',));
		
		$_cur_add = new Zend_Dojo_Form_Element_TextBox('current_add');
		$_cur_add->setAttribs(array('dojoType'=>$this->text,'class'=>'fullside',));
		
		$_fee = new Zend_Dojo_Form_Element_NumberTextBox('payment_paid');
		$_fee->setAttribs(array(
				'dojoType'=>$this->t_num,
				'required'=>'true','class'=>'fullside',));
		
		$_db = new Application_Model_DbTable_DbGlobal();
		$rows = $_db->getAllFecultyName();
		//$rows = $_db->getGlobalDb('SELECT en_name,dept_id FROM rms_dept WHERE is_active=1 AND en_name !="" ');
		$opt = array();
		if(!empty($rows))foreach($rows AS $row) $opt[$row['dept_id']]=$row['en_name'];
			
		$_dept = new Zend_Dojo_Form_Element_FilteringSelect("dept");
		$_dept->setMultiOptions($opt);
		$_dept->setAttribs(array(
				'dojoType'=>$this->filter,
				'required'=>'true',
				//'missingMessage'=>'Invalid Module!',
				'class'=>'fullside',));
		
		$rows = $_db->getAllstudentRequest();
		$re_opt = array();
		if(!empty($rows))foreach($rows AS $row) $re_opt[$row['service_id']]=$row['title'];
			
		$_request = new Zend_Dojo_Form_Element_FilteringSelect("request_id");
		$_request->setMultiOptions($re_opt);
		$_request->setAttribs(array(
				'dojoType'=>$this->filter,
				'required'=>'true',
				'class'=>'fullside',));
		$_save = new Zend_Dojo_Form_Element_Button('$this->tr->translate("SAVE_PAYMENT")');
		$_save->setAttribs(array(
				'dojoType'=>'dijit.form.Button',
				));$_save->setValue("save");
		$this->addElements(array(
				$_reciept_no, $_pay_date,$_pob,$_khname, $_enname,$_studid,$_dob,$_degree,
				$_phone,$_dept,$_batch,$_year,$_session,$_fee,$_cur_add,$_remark,$_request,$_save
		));
		
		return $this;
	}
	
}