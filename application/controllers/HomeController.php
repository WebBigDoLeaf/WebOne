<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/userinfoModel.php';

class HomeController extends BaseController
{

    public function init()
    {
        parent::init();
        
        /* Initialize action controller here */
    }

    public function homeAction()
    {
        // action body   
    }
    
    
    
    public function errorAction()
    {
        
    }
    
    public function userinfoAction()
    {    
        
        
       $id=$this->getRequest()->getParam('id');
        
       $userinfo=new userinfoModel();
       
       $db=$userinfo->getAdapter();
       $where=$db->quoteInto("userid=?",'1');
       echo $where;
       $res=$userinfo->fetchAll($where,$order,$count,$offset)->toArray();
       
        print_r($res);    
        
    }
    
    public function portraitAction()
    {
    
    }


}

