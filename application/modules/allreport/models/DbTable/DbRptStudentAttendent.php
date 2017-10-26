<?php

class Allreport_Model_DbTable_DbRptStudentAttendent extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_student';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
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
   function getStudentAttendent(){
   	 	$db=$this->getAdapter();
   	 	$sql="SELECT
				  a.academic_id,
				  a.session_id,
				  a.group_id,
				  a.subject_id,
				  a.date,
				  (select subject_titleen from rms_subject where rms_subject.id=a.subject_id) as subject_name,
				  ad.student_code,
				  ad.student_id,
				  ad.sex,
				  ad.grade_id,
				  ad.att_type,
				  ad.permission
				FROM rms_attendent AS a,
				  rms_attendent_detail ad
				WHERE a.id = ad.attd_id";
   	 	return $db->fetchAll($sql);
   }
  
  
}