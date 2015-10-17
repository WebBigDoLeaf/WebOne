<?php

class userinfoModel extends Zend_Db_Table_Abstract{

    protected $_name = 'userinfo'; //
    
    public function getUserinfo($id)
    {
        $db=$this->getAdapter();
        $where=$db->quoteInto("userid=?",$id);
        $res=$this->fetchAll($where,$order,$count,$offset)->toArray();
        
        return $res;
    }
    
    public function updateUserinfo($arr,$userid)
    {
        $db=$this->getAdapter();        
        $where=$db->quoteinto('userid= ?',$userid);        
        $rows_affected = $this->update($arr, $where);
        
        return $rows_affected;
    }
}