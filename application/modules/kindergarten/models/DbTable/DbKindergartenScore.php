<?php

class Kindergarten_Model_DbTable_DbKindergartenScore extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_score';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
	
    public function getAllSubjectParent(){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,subject_titleen FROM rms_subject WHERE is_parent=1";
    	return $db->fetchAll($sql);
    }
    
    public function getAllSubjectParentByID($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM rms_subject WHERE id=".$id;
    	return $db->fetchRow($sql);
    }
    
	public function addStudentHomworkScore($_data){
		 //print_r($_data);exit();
		$db = $this->getAdapter();
		$db->beginTransaction();
		$db_sub = new Global_Model_DbTable_DbHomeWorkScore();
		try{
			$_arr = array(
					'academic_id'=>$_data['year_study'],
					'session_id'=>$_data['session'],
					'group_id'=>$_data['study_group'],
					'term_id'=>$_data['term'],
					'status'=>$_data['status'],
    		        'user_id'=>$this->getUserId()
			);
			$id=$this->insert($_arr);
			
			if(!empty($_data['identity'])){
				$ids = explode(',', $_data['identity']);
				//print_r($ids);exit();
				if(!empty($ids))foreach ($ids as $i){
					//foreach ($ids as $rs){
						
						foreach ($db_sub->getParent() as $rs_parent){
							$parent_id = $rs_parent["id"];
								if(!empty($db_sub->getSubject($parent_id))){
									$count = count($db_sub->getSubject($parent_id));
									//echo $count."<br />";
									$parent_score = 0;
									
									foreach ($db_sub->getSubject($parent_id) as $rs_subs){
										$sub_name = str_replace(' ','',$rs_subs["subject_titleen"]);
										$subject_id = $rs_parent['id'];
										$parent_score = $parent_score + $_data["$sub_name".$i];
									}
									
									//echo $rs_parent["sub_name"];
									$arr=array(
											'score_id'=>$id,
											'student_id'=>$_data['stu_id_'.$i],
											'subject_id'=> $subject_id,
											'score'=> $parent_score/$count,
											'status'=>1,
											'user_id'=>$this->getUserId(),
											'is_parent'=> $rs_parent["is_parent"]
									);
									$this->_name='rms_score_detail';
								//	$db->getProfiler()->setEnabled(true);
									$this->insert($arr);
// 									Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
// 									Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
// 									$db->getProfiler()->setEnabled(false);
									foreach ($db_sub->getSubject($parent_id) as $rs_sub){    /////////if parent have subjects
										//echo $rs_sub["sub_name"];
										$subject_id = $rs_sub["id"];
										$sub_name = str_replace(' ','',$rs_sub["subject_titleen"]);
										$arr=array(
												'score_id'=>$id,
												'student_id'=>$_data['stu_id_'.$i],
												'subject_id'=> $subject_id,
												'score'=> $_data["$sub_name".$i],
												'status'=>1,
												'user_id'=>$this->getUserId(),
												'is_parent'=> $rs_sub["is_parent"]
										);
										$this->_name='rms_score_detail';
								//		$db->getProfiler()->setEnabled(true);
										$this->insert($arr);
// 										Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
// 										Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
// 										$db->getProfiler()->setEnabled(false);
										
									}
									
								}else{/////////if parent have not subjects
									$sub_name = str_replace(' ','',$rs_parent["subject_titleen"]);
									$subject_id = $rs_parent['id'];
									//echo $rs_parent["sub_name"];
									$arr=array(
											'score_id'=>$id,
											'student_id'=>$_data['stu_id_'.$i],
											'subject_id'=> $subject_id,
											'score'=> $_data["$sub_name".$i],
											'status'=>1,
											'user_id'=>$this->getUserId(),
											'is_parent'=> $rs_parent["is_parent"]
									);
									$this->_name='rms_score_detail';
								//	$db->getProfiler()->setEnabled(true);
									$this->insert($arr);
// 									Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
// 									Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
// 									$db->getProfiler()->setEnabled(false);
								}
								//print_r($sub_name);
								//$score_val = $_data["$sub_name".$i];
// 								$arr=array(
// 										'score_id'=>$id,
// 										'student_id'=>$_data['stu_id_'.$i],
// 										'subject_id'=> $subject_id,
// 										'score'=> $score_val,
// 										'status'=>1,
// 										'user_id'=>$this->getUserId()
// 								);
								
						
						}
						//echo 'student_id'.$_data['stu_id_'.$i]."<hr />";
					//}
				}
			}
// 			exit();
		  $db->commit();
		}catch (Exception $e){
			$db->rollBack();
		}
   }
   public function updateStudentHomworkScore($_data){
		 //print_r($_data);exit();
		$db = $this->getAdapter();
		$db->beginTransaction();
		$db_sub = new Global_Model_DbTable_DbHomeWorkScore();
		try{
			$_arr = array(
					'academic_id'=>$_data['year_study'],
					'session_id'=>$_data['session'],
					'group_id'=>$_data['study_group'],
					'term_id'=>$_data['term'],
					'status'=>$_data['status'],
    		        'user_id'=>$this->getUserId()
			);
			$where="id=".$_data['score_id'];
			$db->getProfiler()->setEnabled(true);
			$id=$this->update($_arr, $where);
		   ///delect score_detail
		   $this->_name='rms_score_detail';
		   $this->delete("score_id=".$_data['score_id']);
// 		   Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
// 		   Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
// 		   $db->getProfiler()->setEnabled(false);
			if(!empty($_data['identity'])){
				$ids = explode(',', $_data['identity']);
				//print_r($ids);exit();
				if(!empty($ids))foreach ($ids as $i){
					//foreach ($ids as $rs){
						
						foreach ($db_sub->getParent() as $rs_parent){
							$parent_id = $rs_parent["id"];
								if(!empty($db_sub->getSubject($parent_id))){
									$count = count($db_sub->getSubject($parent_id));
									//echo $count."<br />";រាប់ចំនួនសិស្ស
									$parent_score = 0;
									
									foreach ($db_sub->getSubject($parent_id) as $rs_subs){
										$sub_name = str_replace(' ','',$rs_subs["subject_titleen"]);
										$subject_id = $rs_parent['id'];
										$parent_score = $parent_score + $_data["$sub_name".$i];
									}
									
									//echo $rs_parent["sub_name"];
									$arr=array(
											'score_id'=>$_data['score_id'],
											'student_id'=>$_data['stu_id_'.$i],
											'subject_id'=> $subject_id,
											'score'=> $parent_score/$count,
											'status'=>1,
											'user_id'=>$this->getUserId(),
											'is_parent'=> $rs_parent["is_parent"]
									);
									$this->_name='rms_score_detail';
									//$db->getProfiler()->setEnabled(true);
									$this->insert($arr);
// 									Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
// 									Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
// 									$db->getProfiler()->setEnabled(false);
									foreach ($db_sub->getSubject($parent_id) as $rs_sub){    /////////if parent have subjects
										//echo $rs_sub["sub_name"];
										$subject_id = $rs_sub["id"];
										$sub_name = str_replace(' ','',$rs_sub["subject_titleen"]);
										$arr=array(
												'score_id'=>$_data['score_id'],
												'student_id'=>$_data['stu_id_'.$i],
												'subject_id'=> $subject_id,
												'score'=> $_data["$sub_name".$i],
												'status'=>1,
												'user_id'=>$this->getUserId(),
												'is_parent'=> $rs_sub["is_parent"]
										);
										$this->_name='rms_score_detail';
										//$db->getProfiler()->setEnabled(true);
										$this->insert($arr);
// 										Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
// 										Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
// 										$db->getProfiler()->setEnabled(false);
										
									}
									
								}else{/////////if parent have not subjects
									$sub_name = str_replace(' ','',$rs_parent["subject_titleen"]);
									$subject_id = $rs_parent['id'];
									//echo $rs_parent["sub_name"];
									$arr=array(
											'score_id'=>$_data['score_id'],
											'student_id'=>$_data['stu_id_'.$i],
											'subject_id'=> $subject_id,
											'score'=> $_data["$sub_name".$i],
											'status'=>1,
											'user_id'=>$this->getUserId(),
											'is_parent'=> $rs_parent["is_parent"]
									);
									$this->_name='rms_score_detail';
									//$db->getProfiler()->setEnabled(true);
									$this->insert($arr);
// 									Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
// 									Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
// 									$db->getProfiler()->setEnabled(false);
								}
								//print_r($sub_name);
								//$score_val = $_data["$sub_name".$i];
// 								$arr=array(
// 										'score_id'=>$id,
// 										'student_id'=>$_data['stu_id_'.$i],
// 										'subject_id'=> $subject_id,
// 										'score'=> $score_val,
// 										'status'=>1,
// 										'user_id'=>$this->getUserId()
// 								);
								
						
						}
						//echo 'student_id'.$_data['stu_id_'.$i]."<hr />";
					//}
				}
			}
// 			exit();
		  $db->commit();
		}catch (Exception $e){
			$db->rollBack();
		}
   }
	public function getSubexamById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM rms_subject WHERE id = ".$db->quote($id);
		$sql.=" LIMIT 1";
		$row=$db->fetchRow($sql);
		return $row;
	}
	function getParentName(){
		$db=$this->getAdapter();
		$sql="SELECT id,CONCAT(subject_titleen,'-',subject_titlekh) AS parent FROM rms_subject
			      WHERE parent=0 AND is_parent=1 AND `status`=1 ";
		$order=" ORDER BY id DESC ";
		return  $db->fetchAll($sql.$order);
	}
	function getSujectById($data){
		$db=$this->getAdapter();
		$sql="SELECT id,CONCAT(subject_titleen,'-',subject_titlekh) AS name FROM rms_subject
		       WHERE  is_parent='' AND `status`=1 AND parent =".$data;
		$order=" ORDER BY id DESC ";
		return  $db->fetchAll($sql.$order);
	}
	function getStudyYears(){
		$db=$this->getAdapter();
		$sql="SELECT id,CONCAT(from_academic,'-',to_academic) AS name FROM rms_group WHERE `status`=1";
		$order=" ORDER BY id DESC";
		return $db->fetchAll($sql.$order);
	}
	function getGroupAll(){
		$db=$this->getAdapter();
		$sql="SELECT id,group_code AS `name` FROM rms_group WHERE `status`=1";
		$order=" ORDER BY id DESC";
		return $db->fetchAll($sql.$order);
	}
	function getStudent($data){
		$db=$this->getAdapter();
		$sql="SELECT s.stu_id,s.stu_code,CONCAT(s.stu_enname,' - ',s.stu_khname) AS stu_khname,s.sex,(SELECT CONCAT(major_enname,' - ',major_khname ) AS major_enname
		    FROM rms_major WHERE rms_major.major_id=s.grade) AS grade
	    	FROM rms_student AS s,rms_group_detail_student AS g  WHERE s.stu_id=g.stu_id AND g.group_id=$data";
		$order=" ORDER BY stu_code DESC";
		return $db->fetchAll($sql.$order);
	}
	function getAllHoweWorkScore($search=null){
		$db=$this->getAdapter();
		$sql="SELECT s.id,(SELECT group_code FROM rms_group WHERE id=s.group_id ) AS  group_id,
	           (SELECT CONCAT(from_academic,'-',to_academic,'(',generation,')') FROM rms_tuitionfee AS f WHERE id=s.academic_id AND `status`=1 GROUP BY from_academic,to_academic,generation) AS academic_id,
		       (SELECT CONCAT(name_en ,'-',name_kh ) FROM rms_view WHERE `type`=4 AND rms_view.key_code=s.session_id) AS session_id,
		        (SELECT CONCAT(subject_titleen,' - ',subject_titlekh) FROM rms_subject WHERE id=s.subject_id ) AS subject_id,
		        s.term_id,s.status
		        FROM rms_score AS s,rms_group AS g WHERE s.group_id=g.id AND g.degree IN(1,2) AND s.status=1";
		$where ='';
		if(!empty($search['group_name'])){
			$where.= " AND s.group_id=".$search['group_name'];
		}
		$order=" ORDER BY id DESC ";
		return $db->fetchAll($sql.$where.$order);
	}
	function getAllHoweWorkScoreOld(){
		$db=$this->getAdapter();
		$sql="SELECT s.id,d.student_no,
		(SELECT CONCAT(stu_enname,' - ',stu_khname) FROM rms_student  WHERE rms_student.stu_id=d.score_id ) AS student_id,
		(SELECT CONCAT(from_academic,'-',to_academic,'(',generation,')') FROM rms_tuitionfee AS f WHERE id=s.academic_id AND `status`=1 GROUP BY from_academic,to_academic,generation) AS academic_id,
		s.session_id,
		(select group_code from rms_group where id=s.group_id ) as  group_id,
		(SELECT CONCAT(subject_titleen,' - ',subject_titlekh) FROM rms_subject WHERE id=s.subject_id ) AS subject_id,
		s.term_id,
		d.score,d.note,d.status
		FROM rms_score AS s,rms_score_detail AS d WHERE d.score_id=s.id  ";
		$order=" ORDER BY id DESC ";
		return $db->fetchAll($sql.$order);
	}
	function getAllYears(){
		$db = $this->getAdapter();
		$sql = "SELECT id,CONCAT(from_academic,'-',to_academic,'(',generation,')') AS name FROM rms_tuitionfee WHERE `status`=1
		GROUP BY from_academic,to_academic,generation";
		$order=' ORDER BY id DESC';
		return $db->fetchAll($sql.$order);
	}
	function getHomeWorkScoreById($score_id){
		$db=$this->getAdapter();
		$sql="SELECT id,academic_id,session_id,group_id,parent_id,subject_id,term_id FROM rms_score WHERE id=$score_id";
		return $db->fetchRow($sql);
	}
	function getHomeWorkDetailScoreById($score_id){
		$db=$this->getAdapter();
		$sql="SELECT sd.id,s.id,sd.student_no,sd.student_id,sd.score_id,
              (SELECT CONCAT(stu_enname,'-',stu_khname)  FROM rms_student WHERE  stu_id=sd.student_id) AS student_name,
	           sd.sex,(SELECT CONCAT(major_enname,' - ',major_khname ) AS major_enname
	           FROM rms_major WHERE rms_major.major_id=sd.grade_id) AS grade,sd.grade_id,sd.score,sd.note
               FROM rms_score AS s,rms_score_detail AS sd WHERE s.id=sd.score_id AND sd.score_id=$score_id";
		return $db->fetchAll($sql);
	}
	function getGroupName($academic,$session){
		$db=$this->getAdapter();
		$sql="SELECT id,group_code AS `name` FROM  rms_group WHERE  `session`=$session AND academic_year=$academic  ";
		return $db->fetchAll($sql);
	}
	function getParentNameByGroupId($group_id){
		$db=$this->getAdapter();
		$sql="SELECT subject_id AS id,(SELECT CONCAT(subject_titleen,' - ',subject_titlekh)
		        FROM rms_subject WHERE rms_subject.id= rms_group_subject_detail.subject_id) AS `name`
		        FROM rms_group_subject_detail WHERE group_id=$group_id";
		return $db->fetchAll($sql);
	}
	function getGroupSearch(){
		$db=$this->getAdapter();
		$sql="SELECT group_id AS id,(SELECT group_code FROM rms_group WHERE id=rms_score.group_id AND rms_group.degree IN (1,2)) AS `name` 
		               FROM  rms_score  WHERE  `status`=1 GROUP BY group_id";
		return $db->fetchAll($sql);
	}
	///get subject id all 
	function getSubjectId(){
		$db=$this->getAdapter();
		$sql="SELECT id,parent,CONCAT(subject_titleen,'-',subject_titlekh) AS sub_name FROM rms_subject  WHERE `status` =1";
		return $db->fetchAll($sql);
	}
	function getGroupStudent($id){
		$db=$this->getAdapter();
		$sql="SELECT id,academic_id,session_id,group_id,term_id FROM rms_score WHERE id=$id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getScoreStudents($id){
		$db=$this->getAdapter();
		$sql="SELECT id,score_id,student_id,subject_id,score FROM rms_score_detail WHERE score_id=".$id;
		return $db->fetchAll($sql);
	}
	function getSubjectById($id){
		$db = $this->getAdapter();
		$sql =" SELECT 
				  sd.student_id,
				  (SELECT CONCAT(s.`stu_khname`,'-',`stu_enname`) FROM `rms_student`AS s WHERE s.`stu_id`=sd.`student_id`) AS student_name,
				  (SELECT s.`stu_code` FROM `rms_student`AS s WHERE s.`stu_id`=sd.`student_id`) AS stu_code,
				  (SELECT s.`sex` FROM `rms_student`AS s WHERE s.`stu_id`=sd.`student_id`) AS sex,
				  sd.subject_id,
				  (SELECT CONCAT(`subject_titlekh`,'-',`subject_titleen`) FROM `rms_subject` AS s WHERE s.`id`=sd.`subject_id`) AS subject_name,
				  (SELECT `subject_titleen` FROM `rms_subject` AS s WHERE s.`id`=sd.`subject_id`) AS subject_titleen,
				  sd.score ,
  				  sd.`is_parent`
				FROM
				  rms_score_detail AS sd 
				WHERE sd.score_id =$id ";
		return $db->fetchAll($sql);
	}
	function countScore($id){
		$db = $this->getAdapter();
		$sql ="SELECT s.`score_id` FROM `rms_score_detail` AS s WHERE s.`score_id`=$id GROUP BY s.`student_id`";
		return $db->fetchAll($sql);
	}
}

