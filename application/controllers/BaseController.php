<?php
require_once 'Zend/Auth/Adapter/Dbtable.php';



//初始化的数据库
class BaseController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        
        $url=constant( "APPLICATION_PATH" ).DIRECTORY_SEPARATOR. 'configs' .DIRECTORY_SEPARATOR. 'config.ini' ;        
        $dbconfig = new Zend_Config_Ini($url , "sqlite");
        $db = Zend_Db::factory( $dbconfig->db);
        Zend_Db_Table::setDefaultAdapter($db);
        $authAdapter = new Zend_Auth_Adapter_DbTable($db);//用于数据库Auth验证
        Zend_Registry::set('authAdapter', $authAdapter);//将Zend_Auth_Adapter_DbTable实例保存在全局，供后面使用       
    }

    public function indexAction()
    {
        // action body
    }


}

