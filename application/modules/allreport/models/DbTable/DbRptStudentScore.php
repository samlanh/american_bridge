<?php

class Allreport_Model_DbTable_DbRptStudentScore extends Zend_Db_Table_Abstract
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
    	$sql="SELECT COUNT(gds.`stu_id`) AS amount,gds.stu_id,g.`academic_year`,
    		  (select from_academic from rms_tuitionfee where rms_tuitionfee.id=g.academic_year limit 1) as from_academic,
    		  (select to_academic from rms_tuitionfee where rms_tuitionfee.id=g.academic_year limit 1) as to_academic,
    		  (select generation from rms_tuitionfee where rms_tuitionfee.id=g.academic_year limit 1) as generation,
			  (select name_en from rms_view where rms_view.type=4 and rms_view.key_code=g.session) as session,
			  (select major_enname from rms_major where rms_major.major_id=g.`grade`) as grade
			 FROM
			  `rms_group_detail_student` AS gds,
			  `rms_group` AS g 
			 WHERE g.id = gds.`group_id` 
			  ";
    	$groupby=" GROUP BY g.`academic_year`,g.`grade`,g.`session`";
    	$where  = '';
    	
   		 if(empty($search)){
    		return $db->fetchAll($sql.$groupby);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = trim($search['txtsearch']);
    		$s_where[] = " (select CONCAT(from_academic,'-',to_academic,' ',generation) from rms_tuitionfee where rms_tuitionfee.id=g.academic_year) LIKE '%{$s_search}%'";
    		$s_where[] = " (select major_enname from rms_major where rms_major.major_id=g.grade) LIKE '%{$s_search}%'";
    		$s_where[] = " (select name_en from rms_view where rms_view.type=4 and rms_view.key_code=g.session) LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
    	$row = $db->fetchAll($sql.$where.$groupby);
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
   function getParentName(){
   		$db=$this->getAdapter();
   		$sql="select  (select subject_titleen from rms_subject where rms_subject.id=rms_score.parent_id) As parent_id from rms_score,rms_score_detail as sd 
              where   sd.score_id=rms_score.id  GROUP BY rms_score.academic_id,rms_score.session_id,rms_score.group_id,rms_score.parent_id ";
        return $db->fetchAll($sql);
   } 
   function getSubjectdByParent(){
   	$db=$this->getAdapter();
   	$sql="SELECT subject_id,parent_id,(SELECT subject_titleen FROM rms_subject WHERE rms_subject.id=rms_score.subject_id) AS subject_name 
          FROM rms_score WHERE `status`=1 GROUP BY academic_id,session_id,group_id,parent_id,subject_id ,term_id";
    return $db->fetchAll($sql);
   }   
   function getAllSubjectByStudent(){
   		$db=$this->getAdapter();
   		$sql="SELECT
				  stu_id,
				  stu_enname,
				  stu_code,
				  SUM(sd.score) AS score,
				  sd.note,
				  sc.academic_id,
				    (SELECT CONCAT(from_academic,'-',to_academic,'-',generation) FROM rms_tuitionfee WHERE rms_tuitionfee.id=sc.academic_id ) AS academic_name,
				  sc.session_id,
				  sc.group_id,
				    (SELECT group_code FROM rms_group WHERE rms_group.id=sc.group_id) AS group_name,
				  sc.parent_id,
				     (SELECT subject_titleen FROM rms_subject WHERE rms_subject.id=sc.parent_id) AS parent_name,
				  sc.subject_id,
				     (SELECT subject_titleen FROM rms_subject WHERE rms_subject.id=sc.subject_id) AS subject_name
				FROM rms_student,
				  rms_score_detail AS sd,
				  rms_score AS sc
				WHERE sd.student_id = stu_id
				    AND sd.score_id = sc.id
				GROUP BY sc.subject_id,stu_id
				ORDER BY stu_id";
   		return $db->fetchAll($sql);
   }
   function getAcademic(){
   	    $db=$this->getAdapter();
   	    $sql=" SELECT id,(SELECT CONCAT(from_academic,'-',to_academic)  FROM rms_tuitionfee WHERE rms_tuitionfee.id=rms_score.academic_id) AS academic_id,
                     session_id,group_id,subject_id,term_id FROM  rms_score";
   	    return $db->fetchAll($sql);
   }
   function getStudenetGroupSubject(){
   		$db=$this->getAdapter();
   		$sql="SELECT s.stu_id,s.stu_enname,s.stu_code,g.*,g.id as group_id,
   		   (SELECT subject_titleen FROM rms_subject AS sj WHERE sj.id=gsd.subject_id LIMIT 1) AS subject_name,
   		    gsd.subject_id 
	 		FROM rms_student AS s,rms_group AS g,rms_group_detail_student AS gd,rms_group_subject_detail AS gsd
	 		WHERE s.stu_id=gd.stu_id AND  g.id=gd.group_id AND gsd.group_id=g.id  ORDER BY g.id,gsd.subject_id,s.stu_id";
   		return $db->fetchAll($sql);
   }
   function getScoreByGroupId($student_id,$subject_id,$group_id){
   	$db = $this->getAdapter();
   	$sql = "select (select subject_titleen from rms_subject where rms_subject.id=s.subject_id) as subject_name,
            SUM(sd.score) As total_score from rms_score as s,rms_score_detail as sd,rms_student as st
           where  s.id=sd.score_id and sd.student_id=st.stu_id and s.group_id=$group_id and s.parent_id=$subject_id GROUP BY s.subject_id ";
   	return $db->fetchAll($sql);
   	
   }
   function getSubjectItem($subject_id,$group_id){
   	$db = $this->getAdapter();
   	$sql = " select (select subject_titleen from rms_subject where rms_subject.id=s.subject_id) as subject_name
    from rms_score as s,rms_score_detail as sd
   	where s.id=sd.score_id and s.group_id=$group_id and s.parent_id=$subject_id GROUP BY s.subject_id ";
   	return $db->fetchAll($sql);
   
   }
  
}