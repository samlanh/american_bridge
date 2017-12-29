<?php

class Global_Model_DbTable_DbDept extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_major';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
	private function sqlDept($search = ''){
		$db = $this->getAdapter();
		$sql = " SELECT dept_id,en_name,kh_name,shortcut,modify_date,is_active,
		       (SELECT CONCAT(last_name,' ',first_name) FROM rms_users WHERE user_id=id ) AS user_name
		       FROM rms_dept WHERE 1 ";
		$orderby = " ORDER BY en_name ";
		if(empty($search)){
			return $sql.$orderby;
		}
		$where = ' ';
		if(!empty($search['title'])){
			$where.=" AND ( en_name LIKE '%".$db->quote($search['title'])."%' OR kh_name LIKE '%".$db->quote($search['title'])."%') ";
		}
		if($search['status']>-1){
			$where.= " AND is_active = ".$db->quote($search['status']);
		}
		return $sql.$where.$orderby;
	}
	function getAllDept(){        
		$db = $this->getAdapter();
		$sql="select dept_id as id,en_name as name from rms_dept where is_active=1 and en_name != '' ";
		return $db->fetchAll($sql);
	}	
	 function getAllFacultyList($search = ''){
		$db = $this->getAdapter();
		$sql = " SELECT 
					dept_id,
					en_name,
					(select name_en from rms_view as v where v.type=13 and key_code = d.type) as type,
					shortcut,
					modify_date,
					is_active as status,
		       		(SELECT CONCAT(last_name,' ',first_name) FROM rms_users WHERE user_id=id ) AS user_name
		       	 FROM 
					rms_dept as d
				 WHERE
				 	is_active=1
				 	and en_name != ''
			";
		
		$orderby = " ORDER BY dept_id DESC ";
		if(empty($search)){
			return $db->fetchAll($sql.$orderby);
		}
		$where = ' ';
		if(!empty($search['title'])){
			$s_where = array();
    		$s_search = addslashes(trim($search['title']));
	 		$s_where[] = " en_name LIKE '%{$s_search}%'";
    		$s_where[] = " kh_name LIKE '%{$s_search}%'";
    		$s_where[] = " shortcut LIKE '%{$s_search}%'";
    		$sql .=' AND ( '.implode(' OR ',$s_where).')';	
		}
	    		
		if($search['status']>-1){
			$where.= " AND is_active = ".$db->quote($search['status']);
		}
		if($search['type']>0){
			$where.= " AND type = ".$search['type'];
		}
// 		echo $sql.$where.$orderby;
		return $db->fetchAll($sql.$where.$orderby);
	}
	
	public function getAllMajorList($search=''){
		$db = $this->getAdapter();
		$sql = " SELECT 
					m.major_id AS id, 
					m.major_enname,
        			(select d.en_name from rms_dept AS d where m.dept_id=d.dept_id )AS dept_name,
        			m.modify_date,
        			is_active as status,
        			(SELECT CONCAT(last_name,' ',first_name) FROM rms_users WHERE user_id=id ) AS user_name
        		FROM 
					rms_major AS m 
				WHERE 
					is_active=1
					and major_enname != ''
			";
		
		$order=" order by major_id desc";
		$where = '';
		if(empty($search)){
			return $db->fetchAll($sql.$order);
		}
		if(!empty($search['txtsearch'])){
			$s_where = array();
			$s_search = trim($search['txtsearch']);
			$s_where[] = " m.major_enname LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		if(!empty($search['degree'])){
			$where.= " AND m.dept_id = ".$search['degree'];
		}
		return $db->fetchAll($sql.$where.$order);
	}
	
	public function sqlMajor($search=''){
		$db = $this->getAdapter();
		$sql = " SELECT m.major_id AS id, m.major_enname,m.major_khname
       ,(select d.en_name from rms_dept AS d where m.dept_id=d.dept_id )AS dept_name
       ,m.shortcut,m.modify_date,m.is_active,
       (select first_name from rms_users where id=m.user_id) AS user_name
       FROM rms_major AS m WHERE 1";
		$order=" order by m.major_enname ,dept_name";
		$where = '';
		if(empty($search)){
			return $sql.$order;
		}
		if(!empty($search['title'])){
			$where.=" AND ( m.major_enname LIKE '%".$db->quote($search['title'])."%' OR m.major_khname LIKE '%".$db->quote($search['title'])."%') ";
		}
		if($search['status']>-1){
			$where.= " AND m.is_active = ".$db->quote($search['status']);
		}
		return $sql.$where.$order;
	}
	function getAllMajors($search, $start, $limit){
		
		$sql_rs = $this->sqlMajor($search)." LIMIT ".$start.", ".$limit;
		if ($limit == 'All') {
			$sql_rs = $this->sqlMajor($search);
		}
		$sql_count = $this->sqlMajor();
		if(!empty($search)){
			$sql_count = $this->sqlMajor($search);
		}
		$_db = new Application_Model_DbTable_DbGlobal();
		return($_db ->getGlobalResultList($sql_rs,$sql_count));
// 		return array(0=>$rows,1=>$_count);//get all result by param 0 ,get count record by param 1
	}
	
	public function AddNewMajor($_data){
		$this->_name='rms_major';
			$_arr=array(
					'dept_id'	  => $_data['dept'],
					'major_enname'  => $_data['major_enname'],
					'modify_date' => Zend_Date::now(),
					'is_active'	  => $_data['status'],
					'user_id'	  => $this->getUserId()
			);
			return  $this->insert($_arr);
	}
	public function getMajorById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_major WHERE major_id = ".$db->quote($id);
		return($db->fetchRow($sql));
		
	}
	public function updatMajorById($_data){
		$this->_name='rms_major';
		$_arr=array(
				'dept_id'	  => $_data['dept'],
				'major_enname'  => $_data['major_enname'],
				'is_active'	  => $_data['status'],
				'user_id'	  => $this->getUserId()
		);
		$where = $this->getAdapter()->quoteInto("major_id=?", $_data["major_id"]);
		$this->update($_arr, $where);
	}

	public function addDept($data){
		$this->_name='rms_dept';
		try{
			$db= $this->getAdapter();
			$arr = array(
					'en_name'=>$data['fac_enname'],
// 					'kh_name'=> $data['fac_khname'],
					'shortcut'=> $data['shortcut_fac'],
					'user_id'=>$this->getUserId(),
					'is_active'=>$data['status_fac'],
					'modify_date'=>Zend_Date::now(),
			);
			return $this->insert($arr);
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
		
	public function AddNewDepartment($_data){
		$this->_name='rms_dept';
		$_arr=array(
				'en_name'	  => $_data['en_name'],
				'type'		  => $_data['type'],
				'shortcut'    => $_data['shortcut'],
				'modify_date' => new Zend_Date(),
				'is_active'   => $_data['status'],
				'user_id'	  => $this->getUserId()
		);
		return  $this->insert($_arr);
	}
	
	public function UpdateDepartment($_data){
		$this->_name='rms_dept';
		$_arr=array(
				'en_name'	  => $_data['en_name'],
				'type'		  => $_data['type'],
				'shortcut'    => $_data['shortcut'],
				'is_active'   => $_data['status'],
				'user_id'	  => $this->getUserId()
		);
		$where = $this->getAdapter()->quoteInto("dept_id=?",$_data["dept_id"]);
		$this->update($_arr, $where);
	}
	
	public function addCatDree($data){
		$this->_name='rms_view';
		try{
			$db= $this->getAdapter();
			$arr = array(
					'name_en'=>$data['fac_enname'],
					// 					'kh_name'=> $data['fac_khname'],
					'name_kh'=> $data['shortcut_fac'],
					'user_id'=>$this->getUserId(),
					'is_active'=>$data['status_fac'],
					'modify_date'=>Zend_Date::now(),
			);
			return $this->insert($arr);
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
}



