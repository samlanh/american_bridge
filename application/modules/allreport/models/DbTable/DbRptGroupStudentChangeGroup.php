<?php

class Allreport_Model_DbTable_DbRptGroupStudentChangeGroup extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_student';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
    public function getAllCar($search){
    	$db = $this->getAdapter();
    	$sql = "SELECT carid,carname,drivername,tel,zone,note,status FROM rms_car ";
    	$where=' where 1';
    	$order=" order by id DESC";
    	
//     	if(empty($search)){
//     		return $db->fetchAll($sql.$order);
//     	}
//     	if(!empty($search['txtsearch'])){
//     		$s_where = array();
//     		$s_search = trim($search['txtsearch']);
//     		$s_where[] = " carid LIKE '%{$s_search}%'";
//     		$s_where[] = " carname LIKE '%{$s_search}%'";
//     		$s_where[] = " drivername LIKE '%{$s_search}%'";
//     		$where .=' AND ( '.implode(' OR ',$s_where).')';
//     	}
    	
//     	if(empty($search)){
//     		return $db->fetchAll($sql);
//     	}
// //     	if(!empty($search['txtsearch'])){
// //     		$where.=" AND rms_car.carid LIKE '%".$search['txtsearch']."%'";
// //     	}
		
//     	$searchs = $search['txtsearch'];
//     	if($search['searchby'] == 0){
//     		$where.= '';
//     	}
//     	if($search['searchby'] == 1){
//     		$where.= " AND carid = ".$search['txtsearch'];
//     	}
//     	if($search['searchby'] == 2){
//     		$where.= " AND carname LIKE '%".$searchs."%'";
//     	}
//     	if($search['searchby'] == 3){
//     		$where.= " AND drivername LIKE '%".$searchs."%'";
//     	}
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    function getAllTitle(){
    	$db=$this->getAdapter();
    	$sql="select major_id,major_enname from rms_major";
    	return $db->fetchAll($sql);
    }
    
    function getAllSession(){
    	$db=$this->getAdapter();
    	$sql="select key_code,name_en from rms_view where type=4";
    	return $db->fetchAll($sql);
    }
    
    function getAllStu($search){
    	$db= $this->getAdapter();
    	$sql="SELECT 
				  gds.`stu_id`,
				  gscg.`from_group`,
				  
				  st.stu_code,
				  CONCAT(st.stu_khname,' - ',st.stu_enname) AS stu_name,
				  (SELECT name_en FROM rms_view WHERE rms_view.`type`=2 AND rms_view.`key_code`=st.sex) AS sex,
  
				  (select CONCAT(from_academic,'-',to_academic,'(',generation,')') from rms_tuitionfee where rms_tuitionfee.id=(SELECT rms_group.academic_year FROM rms_group WHERE rms_group.id=gscg.`from_group`)) AS academic_year,
				  (select major_enname from rms_major where rms_major.major_id=(SELECT rms_group.grade FROM rms_group WHERE rms_group.id=gscg.`from_group`)) AS grade,
				  (select name_en from rms_view where rms_view.type=4 and key_code=(SELECT rms_group.session FROM rms_group WHERE rms_group.id=gscg.`from_group`)) AS session,
				  gscg.`to_group` ,
				  (select CONCAT(from_academic,'-',to_academic,'(',generation,')') from rms_tuitionfee where rms_tuitionfee.id=g.academic_year ) AS to_academic_year,
				  (select major_enname from rms_major where rms_major.major_id=g.grade) AS to_grade,
				  (select name_en from rms_view where rms_view.type=4 and key_code=g.session) AS to_session
				
				FROM
				  `rms_group_detail_student` AS gds,
				  `rms_group_student_change_group` AS gscg,
				   rms_group as g,
				   rms_student as st
				WHERE 
					gds.`group_id` = gscg.`to_group` 
				  	AND gds.`old_group` = gscg.`from_group`
				  	and gscg.to_group=g.id
				  	and gds.stu_id=st.stu_id   
    	";
    	
    	$order=" ORDER BY gscg.`id` ASC";
    	
    	//$groupby=" GROUP BY g.`academic_year`,g.`grade`,g.`session`";
    	$where  = '';
    	//echo $sql;exit();
   		 if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['title'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['title']));
    		$s_where[] = " (select CONCAT(from_academic,'-',to_academic,' ',generation) from rms_tuitionfee where rms_tuitionfee.id=g.academic_year) LIKE '%{$s_search}%'";
    		$s_where[] = " (select major_enname from rms_major where rms_major.major_id=g.grade) LIKE '%{$s_search}%'";
    		$s_where[] = " (select name_en from rms_view where rms_view.type=4 and rms_view.key_code=g.session) LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	if(!empty($search['study_year'])){
    		$where.=' AND g.academic_year='.$search['study_year'];
    	}
    	if(!empty($search['grade_bac'])){
    		$where.=' AND g.grade='.$search['grade_bac'];
    	}
    	if(!empty($search['session'])){
    		$where.=' AND g.session='.$search['session'];
    	}
    	
    	$row = $db->fetchAll($sql.$where);
    	if($row){
    		return $row;
    	}
    }
    function getAllYearGeneration(){
    	$db= $this->getAdapter();
    	$sql="	SELECT  tf.id as id,tf.`from_academic`, tf.`to_academic`,tf.`generation`,tf.`time`,s.`stu_id`
				FROM
				  `rms_tuitionfee` AS tf,
				  `rms_student` AS s,
				  `rms_group_detail_student` AS gds 
				WHERE s.`stu_id` = gds.`stu_id` 
				  AND s.`academic_year` = tf.`id` GROUP BY tf.`from_academic`,tf.`to_academic`,tf.`generation`";
    	$row = $db->fetchAll($sql);
    	if($row){
    		return $row;
    	}
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}