<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/UserModel.php';


class LoginController extends BaseController
{

    public function init()
    {
        parent::init();
        
        /* Initialize action controller here */
    }

  
    
    public function loginAction()
    {
              
     $name=$this->getRequest()->getParam('account');
     $password=$this->getRequest()->getParam('password');
        
        
       $name=$_POST['account'];
       $password=$_POST['password'];
       
       echo $name;
       echo $password;
       
       
       $User=new UserModel();

       $where="email='$name' And password='$password'";
       echo $where;
     
       
    $res=$User->fetchAll($where)->toArray();
          
    if(count($res)==1)
    {
      
       $this->view->info=$res;
       $this->_forward('home','Home');
    
    }else{
        
        
       $this->_forward('index','Index'); 
    }
       
        
    }
    
    public function registerAction()
    {
        $account=$this->getRequest()->getParam('account');
        $password=$this->getRequest()->getParam('password');
        $name=$this->getRequest()->getParam('name');
         
        
        $User=new UserModel();
     
        
        $where="email='$account'";
        
        $res=$User->fetchAll($where)->toArray();
        
    
    if(count($res)==0)
    {
        
        $set=array(
            'account'=>$account,
            'password'=>$password,
            'name'=>$name,
            'email'=>$account           
        );
              
       if(($User->insert($set)>0))
       {
        
           //查询成功跳转
          $this->render('ok');
                      
       }else{           
           $this->view->info='1';           
           $this->_forward('error');
           
       }
               
    }else{
          
          $this->view->info='2';       
           //账号已经创建
          $this->_forward('error');
        
        
    }
    
        
        
        
        
        
    }
       
    public function errorAction()
    {
        
    }
    
    
    


}





