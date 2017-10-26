<?php

class Allreport_Model_DbTable_DbRptAllStudent extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_student';
//     public function getUserId(){
//     	$session_user=new Zend_Session_Namespace('auth');
//     	return $session_user->user_id;
    	 
//     }
    public function getAllStudent($search){
    	$db = $this->getAdapter();
    	$sql ='select stu_id,CONCAT(stu_enname," - ",stu_khname)AS name,nationality,tel,email,stu_code,home_num,street_num,village_name,
    		  commune_name,district_name,
    		  (select CONCAT(from_academic,"-",to_academic,"(",generation,")") from rms_tuitionfee where rms_tuitionfee.id=academic_year) as academic_year,
    		  (select name_en from rms_view where rms_view.type=4 and rms_view.key_code=rms_student.session limit 1)AS session,
    		  (select major_enname from rms_major where rms_major.major_id=rms_student.grade limit 1)AS grade,
    		  (select en_name from rms_dept where rms_dept.dept_id=rms_student.degree limit 1)AS degree,
    		  
    		  (select name_en from rms_view where type=5 and key_code=is_subspend) as status,
    		  
    		  (select province_en_name from rms_province where rms_province.province_id = rms_student.province_id limit 1)AS province,	   	
    		  (select name_en from rms_view where rms_view.type=2 and rms_view.key_code=rms_student.sex limit 1)AS sex
    		  from rms_student ';
    	$where=' where 1 ';
    	
    	$from_date =(empty($search['start_date']))? '1': "rms_student.create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': "rms_student.create_date <= '".$search['end_date']." 23:59:59'";
    	$where .= " AND ".$from_date." AND ".$to_date;
    	
    	$order="  order by stu_id,degree,grade,academic_year DESC";
    	//$group_by=" GROUP BY academic_year,grade,session,degree ";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['title'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['title']));
    		$s_where[] = " stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " CONCAT(stu_enname,stu_khname) LIKE '%{$s_search}%'";
    		$s_where[] = " (select en_name from rms_dept where rms_dept.dept_id=rms_student.degree limit 1) LIKE '%{$s_search}%'";
    		$s_where[] = " (select major_enname from rms_major where rms_major.major_id=rms_student.grade limit 1) LIKE '%{$s_search}%'";
    		$s_where[] = " (select name_en from rms_view where rms_view.type=4 and rms_view.key_code=rms_student.session limit 1) LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	if(!empty($search['study_year'])){
    		$where.=' AND academic_year='.$search['study_year'];
    	}
    	if(!empty($search['grade_all'])){
    		$where.=' AND grade='.$search['grade_all'];
    	}
    	if(!empty($search['session'])){
    		$where.=' AND session='.$search['session'];
    	}
    	
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    public function getAllAmountStudent($search){
    	$db = $this->getAdapter();
    	$sql ='select stu_id,CONCAT(stu_enname," - ",stu_khname)AS name,nationality,tel,email,stu_code,home_num,street_num,village_name,
    	commune_name,district_name,
    	(select CONCAT(from_academic,"-",to_academic,"(",generation,")") from rms_tuitionfee where rms_tuitionfee.id=academic_year) as academic_year,
    	(select name_en from rms_view where rms_view.type=4 and rms_view.key_code=rms_student.session limit 1)AS session,
    	(select major_enname from rms_major where rms_major.major_id=rms_student.grade limit 1)AS grade,
    	(select en_name from rms_dept where rms_dept.dept_id=rms_student.degree limit 1)AS degree,
    
    	(select name_en from rms_view where type=5 and key_code=is_subspend) as status,
    
    	(select province_en_name from rms_province where rms_province.province_id = rms_student.province_id limit 1)AS province,
    	(select name_en from rms_view where rms_view.type=2 and rms_view.key_code=rms_student.sex limit 1)AS sex
    	from rms_student ';
    	$where=' where 1 ';
    	 
    	$from_date =(empty($search['start_date']))? '1': "rms_student.create_date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': "rms_student.create_date <= '".$search['end_date']." 23:59:59'";
    	$where .= " AND ".$from_date." AND ".$to_date;
    	 
    	$order="  order by academic_year DESC,degree ASC,grade DESC,session ASC,stu_id DESC";
    	//$group_by=" GROUP BY academic_year,grade,session,degree ";
    	 
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['title'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['title']));
    		$s_where[] = " stu_code LIKE '%{$s_search}%'";
    		$s_where[] = " CONCAT(stu_enname,stu_khname) LIKE '%{$s_search}%'";
    		$s_where[] = " (select en_name from rms_dept where rms_dept.dept_id=rms_student.degree limit 1) LIKE '%{$s_search}%'";
    		$s_where[] = " (select major_enname from rms_major where rms_major.major_id=rms_student.grade limit 1) LIKE '%{$s_search}%'";
    		$s_where[] = " (select name_en from rms_view where rms_view.type=4 and rms_view.key_code=rms_student.session limit 1) LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	if(!empty($search['study_year'])){
    		$where.=' AND academic_year='.$search['study_year'];
    	}
    	if(!empty($search['grade_all'])){
    		$where.=' AND grade='.$search['grade_all'];
    	}
    	if(!empty($search['session'])){
    		$where.=' AND session='.$search['session'];
    	}
    	 
    	return $db->fetchAll($sql.$where.$order);
    
    }
       
}