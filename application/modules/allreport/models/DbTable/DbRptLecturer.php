<?php

class Allreport_Model_DbTable_DbRptLecturer extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_teacher';
//     public function getUserId(){
//     	$session_user=new Zend_Session_Namespace('auth');
//     	return $session_user->user_id;
    	 
//     }
    public function getAllLecturer($search){
    	$db = $this->getAdapter();
    	$sql = 'select teacher_code,CONCAT(teacher_name_en," - ",teacher_name_kh)AS name,tel,dob,address,email,nationality,
    			(select name_en from rms_view where rms_view.type=3 and rms_view.key_code=rms_teacher.degree limit 1)AS degree,note,
    			(select name_en from rms_view where rms_view.type=2 and rms_view.key_code=rms_teacher.sex limit 1)AS sex,
				id_card_no,pars_id from rms_teacher	';	
    	
    	$where=' where 1 ';
    	$order=" order by id DESC";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " teacher_code LIKE '%{$s_search}%'";
    		$s_where[] = " CONCAT(teacher_name_en,teacher_name_kh) LIKE '%{$s_search}%'";
    		$s_where[] = " (select name_en from rms_view where rms_view.type=3 and rms_view.key_code=rms_teacher.degree limit 1) LIKE '%{$s_search}%'";
    		//     		$s_where[] = " en_name LIKE '%{$s_search}%'";
    		$where .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	
//     	if(empty($search)){
//     		return $db->fetchAll($sql.$order);
//     	}
    	
// //     	if(!empty($search['txtsearch'])){
// //     		$where.=" AND g.group_code LIKE '%".$search['txtsearch']."%'";
// //     	}

//     	$searchs = $search['txtsearch'];
//     	if($search['searchby']==0){
//     		$where.='';
//     	}
//     	if($search['searchby']==1){
//     		$where.= " AND teacher_code  LIKE  '%".$searchs."%'";
//     	}
//     	if($search['searchby']==2){
//     		$where.= " AND CONCAT(teacher_name_en,teacher_name_kh) LIKE '%".$searchs."%'" ;
//     	}
//     	if($search['searchby']==3){
//     		$where.= " AND (select name_en from rms_view where rms_view.type=3 and rms_view.key_code=rms_teacher.degree limit 1)  LIKE '%".$searchs."%'" ;
//     	}
    	
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    
       
}