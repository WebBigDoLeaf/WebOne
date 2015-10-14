<?php

require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/testDB.php';

class DbdemoController extends BaseController
{

    public function init()
    {
        /* Initialize action controller here */
        parent::init();
        
    }

    public function indexAction()
    {
        // action body
        $table=new testDB();
        $db=$table->getAdapter();
        $select = $db->select();
        $select->from('contacts', '*');
        $result = $db->fetchAll($select);
        print_r ($result);
    }


}

