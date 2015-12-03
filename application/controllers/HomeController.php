<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/userinfoModel.php';
require_once APPLICATION_PATH.'/models/UserModel.php';
require_once APPLICATION_PATH.'/models/expertinfoModel.php';
require_once APPLICATION_PATH.'/models/User_QuestionModel.php';
require_once APPLICATION_PATH.'/models/Expert_ResponseModel.php';

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
    
    public function experthomeAction()
    {
        
        
        $id = $this->getRequest()->getParam('expertid','0');
        $questionid=$this->getRequest()->getParam('questionid','0');
        $type=$this->getRequest()->getParam('type','0');
        
       // $id=$_SESSION["userinfo"][0][id];
        //echo $id;
        //exit();
        if($type==0)
        {
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
        
        $expertinfo=new expertinfoModel();
        $db2=$expertinfo->getAdapter();
        
        $expert= $db2->query('SELECT * FROM expertinfo,User WHERE expertinfo.userid = User.id and User.id =? ',$id)->fetchAll();
        //print_r($expert);   
        
        $question=new User_QuestionModel();        
        $db3=$question->getAdapter();
        
        $response=new Expert_ResponseModel();
        $db4=$response->getAdapter();
        
        $questions=$db3->query('SELECT userconquestion.*,User.name FROM userconquestion,User WHERE userconquestion.state=1 and userconquestion.userid=User.id and userconquestion.expertid=? order by time desc ',$id)->fetchAll();
       // print_r($questions);
       // exit();
       
        $responses=$db4->query('select * from expertconresponse,userconquestion,User where expertconresponse.expertid=9 and expertconresponse.questionid=userconquestion.id and userconquestion.userid=User.id order by time desc')->fetchAll();
        //print_r($responses);       
       // exit();
        $this->view->result=$expert;
        $this->view->question=$questions;
        $this->view->responses=$responses;
        $this->view->type=$type;
        $this->view->questionid=$questionid;
        }else if($type==1)
        {
         
            
            
        }
        
        
        
    }
    
    public function replyAction(){
        
        $questionid=$this->getRequest()->getParam('questionid','0');
        $response=$this->getRequest()->getParam('context');
        
        //得到expertid
        $expertid=$_SESSION["userinfo"][0][id];
        
        
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
        
        
        $response=new Expert_ResponseModel();
        $db2=$response->getAdapter();
        
        $question=new User_QuestionModel();
        $db3=$question->getAdapter();
      //  exit();
        
        $questions=$db3->query('SELECT userconquestion.* FROM userconquestion WHERE id=? ',$questionid)->fetchAll();
        
        $userid=$questions[0][userid];
        print_r($questions);
        //获取当前时间
        $t = time();
        $time = date("Y-m-d H:i:s",$t);
        
        $set=array(
            'expertid'=>$expertid,
            'userid'=>$userid,
            'questionid'=>$questionid,
            'response'=>$response,
            'time'=>$time          
        );
        
        $flag1=$response->insert($set);
        
        $state=0;
        $updateset = array(
            'state'=>$state
        );
        $question->update($updateset, $db3->quoteInto('id = ?', $questionid));
        $this->view->result='回复成功';
        $this->view->expertid=$expertid;
        $this->_forward('result7','globals');
        
        
        }
        
        
        
        
        
        
        
    
    
    
    
   

    }

