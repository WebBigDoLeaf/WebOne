<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        
        $type=$this->getRequest()->getParam('type');
        
        if($type==1||$type==2||$type==3)
        {            
            $this->view->type=$type;
        }else{
            $this->view->type=4;
            
        }
        
       // if($_COOKIE['account']!='null'&&$_COOKIE['password']!='null')
        //{
        $this->view->account=$_COOKIE["account"];
        $this->view->password=$_COOKIE["password"];
        
        
    }


}

