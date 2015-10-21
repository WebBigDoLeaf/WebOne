<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/UserModel.php';
require_once APPLICATION_PATH.'/models/userinfoModel.php';


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
     
     $check=$this->getRequest()->getParam('check');
     
     
    
     if($check==1)
     {     
         setcookie("account",$name,time()+86400,"/");
         setcookie("password",$password,time()+86400,"/");          
     }else{
         setcookie("account","null",time()+86400,"/");
         setcookie("password","null",time()+86400,"/");
         
     }
     

     
     $User=new UserModel();
      
     $res=$User->validate($name, $password);
     
     
    if(count($res)==1)  
    {
	   $_SESSION["userinfo"] = $res;       
       $this->view->info=$res;
       $this->view->cookie=$_COOKIE;
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
        $code=$this->getRequest()->getParam('surecode');
       
    if($code==$_SESSION["validcode"])
    {
        $User=new UserModel();        
   if($User->ifRegister($account))
    {
         
        $set=array(
            'account'=>$account,
            'password'=>$password,
            'name'=>$name,
            'email'=>$account           
        );

         //在注册用户的时候顺带着要建一个用户个人信息
       if(($User->insert($set)>0))
       {
            
         $where="email='$account'";
         $result=$User->fetchAll($where)->toArray();
           
           $info=array(
               'userid'=>$result[0][id],
               'sex'=>'M',  
               'name'=>$result[0][name],
               'sheng'=>'北京市',
               'shi'=>'北京市',
               'xian'=>'东城区',
               'birth'=>'1994-5-29',
               'interest'=>'跑步'
           );
           
           $userinfo=new userinfoModel();
           if($userinfo->insert($info)>0)
           {              //注册账号成功
          $this->render('ok');
           }
           }else{           
           $this->view->info='1';           
           $this->_forward('error');
           
            }
               
    }else{
          
          $this->view->info='2';       
           //账号已经创建
          $this->_forward('error');
        
        
    }
    
    
    }else{
        $this->view->info='3';
        
        $this->render('error');
        
        //验证码输入错误
        
    }
                   
    }
       
    public function errorAction()
    {
        
    }
    
    
    


}





