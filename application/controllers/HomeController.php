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
      //  echo $_COOKIE["account"];
        
        
     //   print_r( $this->cookie);
     
        $imgpath=$_SERVER['DOCUMENT_ROOT']."/WebOne/public/image/head/";
        $id=$_SESSION["userinfo"][0][id];
        if(file_exists($imgpath.$id.".png"))
        {
            $this->view->path=$baseUrl."/WebOne/public/image/head/".$id.".png";
        }else{
        
            $this->view->path=$baseUrl."/WebOne/public/image/initial.png";
        }
        
        
        
    }
    
    
    
    public function errorAction()
    {
        
    }
    
    public function userinfoAction()
    {    
        
        
       $id=$this->getRequest()->getParam('id');
       $result=$this->getRequest()->getParam('update');
       
       
        
       //用来进行提示界面操作成功的操作
       if($result!='success')
       {
           $result=111;
       }
       
      $userinfo=new userinfoModel();    
      $res=$userinfo->getUserinfo($id);
      
      if(count($res)>0)
      {
                    
          $birth=preg_split("/-/", $res[0][birth]);         
          $this->view->info=$res;
          $this->view->birth=$birth;    
          
          $this->view->result=$result;
      }else{        
          //网页出现故障 
      }
      
    }
    
    
    public function updateinfoAction()
    {
        
      
        $res=$this->getRequest()->getParams();
        $birth=$res[year]."-".$res[month]."-".$res[day];
        
        
        $arr=array(
   //      'userid'=>$res[address],
         'sex'=>$res[gender],
         'name'=>$res[nick],
         'sheng'=>$res[province],
         'shi'=>$res[city],
         'xian'=>$res[town],
         'birth'=>$birth,
         'interest'=>$res[hobby]            
        );
        
        $userinfo=new userinfoModel();
        $rows_affected=$userinfo->updateUserinfo($arr,$res[address]);
        
        if($rows_affected>0)
        {
           $this->view->id=$res[address];
           $this->view->res=1;
        }else{
           $this->view->res=2;
        }
        
    }
    
    public function portraitAction()
    {
        
        $type=$this->getRequest()->getParam('type');
        
        
        $imgpath=$_SERVER['DOCUMENT_ROOT']."/WebOne/public/image/head/";
        $id=$_SESSION["userinfo"][0][id];
        if(file_exists($imgpath.$id.".png"))
        {
            $this->view->path=$baseUrl."/WebOne/public/image/head/".$id.".jpg";
            $this->view->type=$type;
        }else{
            
            $this->view->path=$baseUrl."/WebOne/public/image/initial.jpg";
            $this->view->type=$type;
        }
        
        
    }
    
   

    }

