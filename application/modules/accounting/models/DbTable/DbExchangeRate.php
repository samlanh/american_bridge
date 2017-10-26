<?php
class Accounting_Model_DbTable_DbExchangeRate extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_exchange_rate';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    
    }
    public function getExchangeRate(){
    	$db=$this->getAdapter();
    	$sql=" SELECT * FROM rms_exchange_rate ";
    	return $db->fetchRow($sql);
    }
    public function addExchangerate($_data){
    	try{$db= $this->getAdapter();
		    	$arr = array(
		    			'dollar'=>$_data['dollar'],
		    			'reil'=>$_data['riel'],
		    			'user_id'=>$this->getUserId(),
		    			);
		    	$where = $this->getAdapter()->quoteInto("id=?",$_data['id']);
		    	$this->update($arr,$where);
		    	
    	}catch(Exception $e){
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    }
  
}



