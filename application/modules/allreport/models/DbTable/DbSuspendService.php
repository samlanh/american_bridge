<?php

class Allreport_Model_DbTable_DbSuspendService extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_car';
//     public function getUserId(){
//     	$session_user=new Zend_Session_Namespace('auth');
//     	return $session_user->user_id;
    	 
//     }
	public function  getStudetnSuspendServiceDetail($search=null){
		//print_r($id); exit();
		try{
			$db = $this->getAdapter();
			$sql = 'SELECT 
			(SELECT `student_id` FROM `rms_suspendservice` WHERE id=`suspendservice_id`) AS stu_id,
			(SELECT (SELECT `stu_enname` FROM `rms_student` WHERE `stu_id`=`student_id`) FROM `rms_suspendservice` WHERE id =`suspendservice_id`) AS en_name,
			(SELECT (SELECT `stu_khname` FROM `rms_student` WHERE `stu_id`=`student_id`) FROM `rms_suspendservice` WHERE id =`suspendservice_id`) AS kh_name,
			(SELECT (SELECT `stu_code` FROM `rms_student` WHERE `stu_id`=`student_id`) FROM `rms_suspendservice` WHERE id =`suspendservice_id`) AS stu_code,
			(SELECT (SELECT (SELECT `name_en` FROM `rms_view` WHERE `type`=2 AND `key_code`=`sex`) FROM `rms_student` WHERE `stu_id`=`student_id`) FROM `rms_suspendservice` WHERE id =`suspendservice_id`) AS sex,
			(SELECT (SELECT CONCAT(`from_academic`,"-",`to_academic`,"(",generation,")") FROM `rms_tuitionfee` WHERE id =`year`) FROM `rms_suspendservice` WHERE id=`suspendservice_id`) as academic,
			(SELECT suspend_no FROM rms_suspendservice WHERE id = suspendservice_id) as suspend_no,
			(SELECT title FROM `rms_program_name` WHERE TYPE =2 AND `service_id`= rms_suspendservicedetail.service_id LIMIT 1) as service,
			(SELECT `name_en` FROM `rms_view` WHERE `type`=5 AND `key_code`= type_suspend LIMIT 1)AS type_suspend,
			reason,note,
			rms_suspendservicedetail.define_date
			FROM rms_suspendservicedetail,rms_suspendservice WHERE rms_suspendservicedetail.suspendservice_id=rms_suspendservice.id and suspend_status=1 ';
			$where ='';
			$order = ' ORDER BY rms_suspendservicedetail.id DESC ';
			
			$from_date =(empty($search['start_date']))? '1': "rms_suspendservice.define_date >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': "rms_suspendservice.define_date <= '".$search['end_date']." 23:59:59'";
			$where .= " AND ".$to_date." and ".$from_date;
			
			if(!empty($search['service'])){
				$where .=" and service_id = ".$search['service'];
			}
			if(!empty($search['study_year'])){
				$where .=" and year = ".$search['study_year'];
			}
			
			if (empty($search)){
				return $db->fetchAll($sql.$order);
			}
			if(!empty($search['txtsearch'])){
				$s_where = array();
				$s_search = addslashes(trim($search['txtsearch']));
				$s_where[] = " (SELECT suspend_no FROM rms_suspendservice WHERE id = suspendservice_id) LIKE '%{$s_search}%'";
				$s_where[] = " (SELECT (SELECT `stu_enname` FROM `rms_student` WHERE `stu_id`=`student_id`) FROM `rms_suspendservice` WHERE id =`suspendservice_id`) LIKE '%{$s_search}%'";
				$s_where[] = " (SELECT (SELECT `stu_khname` FROM `rms_student` WHERE `stu_id`=`student_id`) FROM `rms_suspendservice` WHERE id =`suspendservice_id`) LIKE '%{$s_search}%'";
				$s_where[] = " (SELECT (SELECT `stu_code` FROM `rms_student` WHERE `stu_id`=`student_id`) FROM `rms_suspendservice` WHERE id =`suspendservice_id`) LIKE '%{$s_search}%'";
				$where .=' AND ( '.implode(' OR ',$s_where).')';
			
			}
			//echo $sql.$where;exit();
			return $db->fetchAll($sql.$where.$order);
		}catch (Exception $e){
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				echo $e->getMessage();
		}
	}
	public function getStudetnByid($id){
		try{
			$db = $this->getAdapter();
			$sql = 'SELECT id,
			(SELECT `stu_code` FROM `rms_student` WHERE `stu_id`= student_id LIMIT 1) AS student_id,
			(SELECT CONCAT (`from_academic`,"-",`to_academic`) FROM `rms_tuitionfee`WHERE id = year LIMIT 1) as year,
			(SELECT `stu_enname` FROM `rms_student` WHERE `stu_id`= student_id LIMIT 1) AS name_en,
			(SELECT `stu_khname` FROM `rms_student` WHERE `stu_id`= student_id LIMIT 1) AS name_kh,
			(SELECT (SELECT	`name_en` FROM `rms_view` WHERE `type`= 2 AND `key_code`=`sex` LIMIT 1) FROM `rms_student` WHERE `stu_id`= student_id LIMIT 1) AS sex
			FROM rms_suspendservice WHERE id='.$id;
			return $db->fetchRow($sql);
		}catch (Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
    
}