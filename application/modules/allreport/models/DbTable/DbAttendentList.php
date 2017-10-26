<?php
class Allreport_Model_DbTable_DbAttendentList extends Zend_Db_Table_Abstract
{
	public  function getStudentInfo($search){
		$_db = $this->getAdapter();
		$sql = "SELECT stu_id,stu_enname,stu_khname,
		(SELECT name_kh FROM `rms_view` WHERE type=2 AND key_code = sex) as gender
		,stu_code,dob,remark,tel,(SELECT province_kh_name FROM `rms_province` WHERE `province_id`= rms_student.province_id) as pro,
		father_phone,mother_phone,email,father_khname,father_enname,father_nation,(SELECT `occu_name` FROM `rms_occupation` WHERE `occupation_id` = father_job)as far_job,(SELECT `occu_name` FROM `rms_occupation` WHERE `occupation_id` = mother_job)as mom_job,
		mother_enname,mother_khname,mother_nation,guardian_khname,guardian_nation,guardian_document,guardian_enname,address,home_num,street_num,village_name,commune_name,district_name,
		(SELECT `occu_name` FROM `rms_occupation` WHERE `occupation_id` = guardian_job)as guar_job,guardian_tel,guardian_email,remark,
		(SELECT name_kh FROM `rms_view` WHERE type=1 AND key_code = status) as status,nationality
		FROM rms_student where status = 1";
		
		$sql.='';
		if(empty($search)){
			$_db->fetchAll($sql);
		}
		if(!empty($search['txtsearch']))
		{
			$s_where = array();
			$s_search = trim($search['txtsearch']);
			$s_where[] = " stu_enname LIKE '%{$s_search}%'";
			$s_where[] = " stu_khname LIKE '%{$s_search}%'";
			$s_where[] = " stu_code LIKE '%{$s_search}%'";
			$s_where[] = " nationality LIKE '%{$s_search}%'";
			// 			$s_where[] = " en_name LIKE '%{$s_search}%'";
			// 			$s_where[] = " sex LIKE '%{$s_search}%'";
			//			$s_where[] = " nationality LIKE '%{$s_search}%'";
			$sql .=' AND ( '.implode(' OR ',$s_where).')';
		}
		return $_db->fetchAll($sql);
	}
    function getSubjectParent(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,subject_titleen,is_parent FROM rms_subject WHERE is_parent=1 AND access_type IN (3,4) AND `status`=1";
    	return $db->fetchAll($sql);
    }
    function getSubject($is_parent){
        $db=$this->getAdapter();
        $sql="SELECT sd.id,sd.score_id,st.stu_code,(SELECT stu_enname FROM rms_student AS s WHERE s.stu_id =sd.student_id) AS stu_name,sd.student_id,
				       (SELECT subject_titlekh FROM rms_subject AS sb WHERE  sb.id=sd.subject_id )AS subject_name,
				        sd.score,sd.is_parent FROM rms_score_detail AS sd,rms_student AS st 
				 WHERE st.stu_id = sd.student_id AND sd.status=1 AND sd.subject_id=$is_parent";
        return $db->fetchAll($sql);
    }
    //get academic year 
    function getAcademicYear(){
    	$db=$this->getAdapter();
    	$sql="SELECT s.id,s.academic_id,s.session_id,s.group_id,s.term_id FROM rms_score AS s,rms_group AS g
                WHERE s.group_id = g.id AND g.degree IN(3,4) ";
        return $db->fetchAll($sql);
        
    }
    function getAttByYear(){
    	$db=$this->getAdapter();
    	$sql="SELECT
				  att.id,
				  (SELECT CONCAT(from_academic,'-',to_academic) FROM rms_tuitionfee WHERE rms_tuitionfee.id=att.academic_id) AS `year`,
				  (SELECT CONCAT(name_en,'-',name_kh) FROM rms_view WHERE att.session_id=rms_view.key_code AND `type`=4 ) AS session_name,
				  (SELECT group_code FROM rms_group WHERE rms_group.id=att.group_id) AS group_name,
				   att.subject_id,
				  att.date
				FROM rms_attendent AS att";
    	return $db->fetchAll($sql);
    }
    function getStudentById($score_id){
    	$db=$this->getAdapter();
    	$sql="SELECT id,attd_id,student_id,(SELECT CONCAT(stu_enname,'-',stu_khname) FROM rms_student AS st WHERE st.stu_id=atd.student_id) AS stu_name,
				   student_code,
				   sex
				FROM rms_attendent_detail AS atd
				WHERE attd_id=$score_id  ORDER BY atd.student_id DESC ";
    	return $db->fetchAll($sql);
    }
    function getStudentByIds(){
    	$db=$this->getAdapter();
    	$sql="SELECT id,student_id,student_code, sex, grade_id, permission, no_permission, att_type,no_att_type
			FROM rms_attendent_detail WHERE `status`=1 ORDER BY student_id   DESC ";
    	return $db->fetchAll($sql);
    }
}



