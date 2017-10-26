<?php

class Allreport_Model_DbTable_DbRptAcademicYear extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_academicperiod';
//     public function getUserId(){
//     	$session_user=new Zend_Session_Namespace('auth');
//     	return $session_user->user_id;
    	 
//     }
    public function getAllAcademic($search){
    	$db = $this->getAdapter();
    	$sql = "select CONCAT(fromyear,' - ',toyear)AS academic,batch,study_start,study_end,duration,note,quarter_start,quarter_end,
    			semester_start,semester_end,yearly_start,yearly_end from rms_academicperiod";	
    	
    	$where=' where 1';
    	$order=" order by id DESC";
    	
    	if(empty($search)){
    		return $db->fetchAll($sql.$order);
    	}
    	if(!empty($search['txtsearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['txtsearch']));
    		$s_where[] = " CONCAT(fromyear,'-',toyear) LIKE '%{$s_search}%'";
    		$s_where[] = " batch LIKE '%{$s_search}%'";
//     		$s_where[] = " kh_name LIKE '%{$s_search}%'";
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
//     		$where.= " AND CONCAT(fromyear,toyear) LIKE '%".$searchs."%'";
//     	}
//     	if($search['searchby']==2){
//     		$where.= " AND batch LIKE '%".$searchs."%'" ;
//     	}
    	
    	return $db->fetchAll($sql.$where.$order);
    	 
    }
   
    
       
}