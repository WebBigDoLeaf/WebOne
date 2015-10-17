<?php

class UserModel extends Zend_Db_Table_Abstract{

    protected $_name = 'User'; //
  
    public function validate($name,$password)
    {
        
        $where="email='$name' And password='$password'";
        $res=$this->fetchAll($where)->toArray();
        
        return $res;
    }
    
    public function ifRegister($account)
    {
        $where="email='$account'";
        $res=$this->fetchAll($where)->toArray();
        
        if(count($res)==0)
        {
            return true;
        }else{
            return false;
        }
        
        
    }
    
    
    
    
}