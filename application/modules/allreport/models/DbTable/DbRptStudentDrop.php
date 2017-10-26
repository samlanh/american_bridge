<?php

class Allreport_Model_DbTable_DbRptStudentDrop extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_student_drop';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    
    public function getBranchId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->branch_id;
    }
    
    public function getAllStudentDrop($search){
    	$db = $this->getAdapter();
    	
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission();
    	
    	$sql = "SELECT 
    				st.stu_code as stu_id, 
    				CONCAT(st.stu_khname,' - ',st.stu_enname) as name,
			    	(select CONCAT(from_academic,'-',to_academic,'(',generation,')') from rms_tuitionfee where rms_tuitionfee.id=st.academic_year) as academic_year,
			    	(select name_en from rms_view where rms_view.type=4 and rms_view.key_code=st.session limit 1)AS session,
			    	(select en_name from rms_dept where dept_id = st.degree ) as degree,
			    	(select major_enname from rms_major where rms_major.major_id=st.grade limit 1)AS grade,
					(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=2 and `rms_view`.`key_code`=st.sex )AS sex,
					(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=stdp.`type`) as type,
					stdp.note,
					stdp.date,
					(select name_kh from `rms_view` where `rms_view`.`type`=6 and `rms_view`.`key_code`=`stdp`.`status`)AS status
		 		from 
    				rms_student_drop as stdp,
    				rms_student as st 
    			where 
    				stdp.stu_id=st.stu_id 
    				and stdp.status = 1 
    				and stdp.drop_from = 1
    				$branch_id
    		";

    	$where=' ';
    	
    	$order=" order by stdp.id DESC ";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}

    	if(!empty($search['title'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['title']));
    		$s_where[] = " st.stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
    		$s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
    		$s_where[] = " (SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=`stdp`.`type`) LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	if(!empty($search['study_year'])){
    		$where.=' AND st.academic_year='.$search['study_year'];
    	}
    	if(!empty($search['grade_all'])){
    		$where.=' AND st.grade='.$search['grade_all'];
    	}
    	if(!empty($search['degree_all'])){
    		$where.=' AND st.degree='.$search['degree_all'];
    	}
    	if(!empty($search['session'])){
    		$where.=' AND st.session='.$search['session'];
    	}
    	
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    
    public function getAllStudentDropTransport($search){
    	$db = $this->getAdapter();
    	 
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission();
    	 
    	$sql = "SELECT
			    	s.stu_code ,
			    	CONCAT(st.stu_khname,' - ',st.stu_enname) as name,
			    	(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=2 and `rms_view`.`key_code`=st.sex )AS sex,
			    	st.tel,
			    	
			    	(select p.title from rms_program_name as p where p.service_id = s.service_id) as service_name,
			    	
			    	(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=stdp.`type`) as type,
			    	stdp.reason,
			    	stdp.date,
			    	(select name_kh from `rms_view` where `rms_view`.`type`=6 and `rms_view`.`key_code`=`stdp`.`status`)AS status
			    from
			    	rms_student_drop as stdp,
			    	rms_student as st,
			    	rms_service as s
			    where
			    	stdp.stu_id=st.stu_id
			    	and s.stu_id = stdp.stu_id
			    	and stdp.status = 1
			    	and stdp.drop_from = 2
			    	and s.type=4
			    	$branch_id
    			";
    
    	$where = ' ';
    	 
    	$order = " order by stdp.id DESC ";
    	 
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
	    }
	    
	    if(!empty($search['title'])){
		    $s_where = array();
		    $s_search = addslashes(trim($search['title']));
		    $s_where[] = " st.stu_code LIKE '%{$s_search}%'";
		    $s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
		    $s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
		    $s_where[] = " (SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=`stdp`.`type`) LIKE '%{$s_search}%'";
		    $where .=' AND ( '.implode(' OR ',$s_where).')';
	    }
	     
	    if(!empty($search['service'])){
	    	$where.=' AND s.service_id='.$search['service'];
	    }
	    if(!empty($search['branch'])){
	    	$where.=' AND st.branch_id='.$search['branch'];
	    }
	    return $db->fetchAll($sql.$where.$order);
    }
    
    
    public function getAllStudentDropLunchAndStay($search){
    	$db = $this->getAdapter();
    	 
    	$_db = new Application_Model_DbTable_DbGlobal();
    	$branch_id = $_db->getAccessPermission();
    	 
    	$sql = "SELECT
			    	s.stu_code ,
			    	CONCAT(st.stu_khname,' - ',st.stu_enname) as name,
			    	(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=2 and `rms_view`.`key_code`=st.sex )AS sex,
			    	st.tel,
			    	
			    	(select p.title from rms_program_name as p where p.service_id = s.service_id) as service_name,
			    	
			    	(SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=stdp.`type`) as type,
			    	stdp.reason,
			    	stdp.date,
			    	(select name_kh from `rms_view` where `rms_view`.`type`=6 and `rms_view`.`key_code`=`stdp`.`status`)AS status
			    from
			    	rms_student_drop as stdp,
			    	rms_student as st,
			    	rms_service as s
			    where
			    	stdp.stu_id=st.stu_id
			    	and s.stu_id = stdp.stu_id
			    	and stdp.status = 1
			    	and stdp.drop_from = 3
			    	and s.type=5
			    	$branch_id
    			";
    
    	$where = ' ';
    	 
    	$order = " order by stdp.id DESC ";
    	 
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
	    }
	    
	    if(!empty($search['title'])){
		    $s_where = array();
		    $s_search = addslashes(trim($search['title']));
		    $s_where[] = " st.stu_code LIKE '%{$s_search}%'";
		    $s_where[] = " st.stu_khname LIKE '%{$s_search}%'";
		    $s_where[] = " st.stu_enname LIKE '%{$s_search}%'";
		    $s_where[] = " (SELECT name_kh FROM `rms_view` WHERE `rms_view`.`type`=5 and `rms_view`.`key_code`=`stdp`.`type`) LIKE '%{$s_search}%'";
		    $where .=' AND ( '.implode(' OR ',$s_where).')';
	    }
	     
	    if(!empty($search['service'])){
	    	$where.=' AND s.service_id='.$search['service'];
	    }
	    if(!empty($search['branch'])){
	    	$where.=' AND st.branch_id='.$search['branch'];
	    }
	    return $db->fetchAll($sql.$where.$order);
    
    }
    
    function getAllServiceByCategory($cate_id){
    	$db = $this->getAdapter();
    	$sql="select service_id as id,title as name from rms_program_name where ser_cate_id = $cate_id and type=2 and status=1 ";
    	return $db->fetchAll($sql);
    }
    
    
    
    
}