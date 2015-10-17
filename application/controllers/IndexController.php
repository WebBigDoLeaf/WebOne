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
        
        if($type==1||$type==2)
        {            
            $this->view->type=$type;
        }
    }


}

