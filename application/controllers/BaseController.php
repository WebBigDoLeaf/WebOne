<?php
require_once 'Zend/Auth/Adapter/Dbtable.php';



//��ʼ�������ݿ�
class BaseController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        
        $url=constant( "APPLICATION_PATH" ).DIRECTORY_SEPARATOR. 'configs' .DIRECTORY_SEPARATOR. 'config.ini' ;        
        $dbconfig = new Zend_Config_Ini($url , "sqlite");
        $db = Zend_Db::factory( $dbconfig->db);
        Zend_Db_Table::setDefaultAdapter($db);
        $authAdapter = new Zend_Auth_Adapter_DbTable($db);//�������ݿ�Auth��֤
        Zend_Registry::set('authAdapter', $authAdapter);//��Zend_Auth_Adapter_DbTableʵ��������ȫ�֣�������ʹ��       
        
        //开启会议
        session_start();
        
    }

    public function indexAction()
    {
        // action body
    }


}

