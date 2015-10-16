<?php

class HomeController extends Zend_Controller_Action
{

    public function init()
    {
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
        
       $arr=$this->getRequest()->getParam;
       
       print_r($arr);   
        
    }
    
    public function portraitAction()
    {
    
    }


}

