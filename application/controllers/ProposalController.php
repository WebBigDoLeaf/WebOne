<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/QuestionModel.php';
require_once APPLICATION_PATH.'/models/UserModel.php';
require_once APPLICATION_PATH.'/models/AnswerModel.php';
require_once APPLICATION_PATH.'/models/User_AgreeModel.php';
require_once APPLICATION_PATH.'/models/expertinfoModel.php';
require_once APPLICATION_PATH.'/models/User_QuestionModel.php';
require_once APPLICATION_PATH.'/models/User_ExpertModel.php';
require_once APPLICATION_PATH.'/models/Expert_ResponseModel.php';

class ProposalController extends BaseController
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        
        $page=$this->getRequest()->getParam('page');
        
        
        
        $questiontable = new QuestionModel();
        $db = $questiontable->getAdapter();
        //按开始时间倒序展示10条记录
        $num = $db->query('SELECT count(*) as num FROM question order by time desc')->fetchAll()[0][num];
        
        

        $pages=ceil($num/3);
        
        if($page=="")
        {
            $page=0;
        }
        
        //确定页面显示的规范  处理分页的逻辑
        if($page<3)
        {
            if($pages>3)
            {
                $set=array(1,2,3,4);
            }else{
                $set=array();
                for($i=1;$i<=$pages;$i++)
                {
                    $set[]=$i;
                }
            }
        }else if($page>=3)
        {
            if($page+3<=$pages)
            {
                $set=array($page,$page+1,$page+2,$page+3);
            }else{
                $set=array();
                for($i=$page;$i<=$pages;$i++)
                {
                    $set[]=$i;
                }
        
            }
        }
        
        $where = '1=1';
        $order = 'begin by time desc';
        $count = 3;
        $offset =$page*3;
        $result = $questiontable->fetchAll($where, $order, $count, $offset)->toArray();
        
        
        $this->view->result = $result; 
        $this->view->page=$page;
        $this->view->count=$set;
        
    }
    
    public function showAction()
    {
        $questionid = $this->getRequest()->getParam('questionid','0');
        
        $questiontable = new QuestionModel();
        $result = $questiontable -> find($questionid) -> toArray();
        //展示问题具体内容
        $this -> view -> result = $result[0];
        
        
        
        //所有一级回答
        $answertable = new AnswerModel();
        $db1 = $answertable->getAdapter();
        $answers = $db1->query('SELECT acontent,time,name,agrees,answer.id as id,answer.userid as uid FROM answer,User WHERE answer.userid = User.id AND questionid = ?' , $questionid)->fetchAll();
        $this -> view -> answers = $answers;
    }
    
    public function replyuiAction()
    {
        $questionid = $this->getRequest()->getParam('questionid','0');
        $uid = $this->getRequest()->getParam('id','0');
        $this->view->qid = $questionid;
        $this->view->uid = $uid;
    }
    
    public function replyAction()
    {
        $questionid = $this->getRequest()->getParam('questionid','0');
        $informuid = $this->getRequest()->getParam('id','0');
        $content = $this->getRequest()->getParam('content');
        
        $account = $_COOKIE["account"];
        $account=$_SESSION["userinfo"][0][account];
        
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
        
        $questiontable = new QuestionModel();
        $db2 = $questiontable->getAdapter();
        
        $answertable = new AnswerModel();
        $db3 = $answertable->getAdapter();
        
        //查找当前用户ID
        $result = $usertable -> fetchRow($db1->quoteInto('email = ?', $account)) ->toArray();
        //$uid = $result['id'];
        $uid=$_SESSION["userinfo"][0][id];
        
        //获取当前时间
        $t = time();
        $time = date("Y-m-d H:i:s",$t);
        
        $set = array(
            'content'=>$content,
            'agrees'=>0,
            'time'=>$time,
            'replyid'=>-1,
            'userid'=>$uid,
            'questionid'=>$questionid,
            'inform_userid'=>$informuid
        );

        $flag0 = $answertable -> insert($set);
        
        //更改question表中answernum值
        $result = $questiontable -> find($questionid) -> toArray()[0]['answernum'];
        ++$result;
        $numset = array(
            'answernum'=>$result
        );
        $where = $db2->quoteInto('id = ?', $questionid);
        $flag1 = $questiontable -> update($numset, $where);
        
        if($flag0 > 0 && $flag1 > 0){
            $this->view->info = 'success';
        }
        else{
            $this->view->info = 'fail';
        }
        
        $this -> view -> id = $questionid;
        $this->_forward('result5','globals');
    }
    
    public function answerreplyuiAction()
    {
        $questionid = $this->getRequest()->getParam('questionid','0');
        $answerid = $this->getRequest()->getParam('answerid','0');
        $uid = $this->getRequest()->getParam('id','0');
        $this->view->qid = $questionid;
        $this->view->aid = $answerid;
        $this->view->uid = $uid;
        
        
    }
    
    public function answerreplyAction()
    {
        $questionid = $this->getRequest()->getParam('questionid','0');
        $answerid = $this->getRequest()->getParam('answerid','0');
        $informuid = $this->getRequest()->getParam('id','0');
        $content = $this->getRequest()->getParam('content');
        $account = $_COOKIE["account"];
    
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
    
        $questiontable = new QuestionModel();
        $db2 = $questiontable->getAdapter();
    
        $answertable = new AnswerModel();
        $db3 = $answertable->getAdapter();
    
        //查找当前用户ID
        $result = $usertable -> fetchRow($db1->quoteInto('email = ?', $account)) ->toArray();
        $uid = $result['id'];
    
        //获取当前时间
        $t = time();
        $time = date("Y-m-d H:i:s",$t);
    
        $set = array(
            'content'=>$content,
            'agrees'=>0,
            'time'=>$time,
            'replyid'=>$answerid,
            'userid'=>$uid,
            'questionid'=>$questionid,
            'inform_userid'=>$informuid
        );
    
        $flag0 = $answertable -> insert($set);
    
        //更改question表中answernum值
        $result = $questiontable -> find($questionid) -> toArray()[0]['answernum'];
        ++$result;
        $numset = array(
            'answernum'=>$result
        );
        $where = $db2->quoteInto('id = ?', $questionid);
        $flag1 = $questiontable -> update($numset, $where);
    
        if($flag0 > 0 && $flag1 > 0){
            $this->view->info = 'success';
        }
        else{
            $this->view->info = 'fail';
        }
    
        $this -> view -> id = $questionid;
        $this->_forward('result5','globals');
    }
    
    public function newpostAction()
    {
        $account = $_COOKIE["account"];
        
        if($account == 'null'){
            $this->view->info = '未登录';
            $this->_forward('result3','globals');
            return;
        }
        
    
    }
    
    public function addnewpostAction()
    {
        $account = $_COOKIE["account"];
        $title = $this->getRequest()->getParam('title');
        $content = $this->getRequest()->getParam('context');
        
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
        
        $questiontable = new QuestionModel();
        $db2 = $questiontable->getAdapter();
        
        $answertable = new AnswerModel();
        $db3 = $answertable->getAdapter();
        
        //查找当前用户ID
        $result = $usertable -> fetchRow($db1->quoteInto('email = ?', $account)) ->toArray();
        $uid = $result['id'];
        
        //获取当前时间
        $t = time();
        $time = date("Y-m-d H:i:s",$t);
        
        //添加question信息
        $set = array(
            'userid'=>$uid,
            'title'=>$title,
            'content'=>$content,
            'answernum'=>0,
            'time'=>$time
        );
        
        if($questiontable -> insert($set) > 0){
            $this->view->info = 'success';
        }
        else{
            $this->view->info = 'fail';
        }
        
        $this->_forward('result4','globals');
    }
    
    public function expertquestionAction()
    {
        $account = $_COOKIE["account"];
        $title = $this->getRequest()->getParam('title');
        $content = $this->getRequest()->getParam('context');
        $uid=$this->getRequest()->getParam('userid');
        $expertid=$this->getRequest()->getParam('expertid');
        
        
        
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
    
        $questiontable = new User_QuestionModel();
        $db2 = $questiontable->getAdapter();
    
    
        //获取当前时间
        $t = time();
        $time = date("Y-m-d H:i:s",$t);
    
        //添加question信息
        $set = array(
            'userid'=>$uid,
            'title'=>$title,
            'question'=>$content,
            'expertid'=>$expertid,
            'time'=>$time,
            'state'=>1
        );
    
        if($questiontable -> insert($set) > 0){
            $this->view->info = 'success';
        }
        else{
            $this->view->info = 'fail';
        }
    
        $this->_forward('result4','globals');
    }
    
    
    

    public function deleteAction()
    {
        $questionid = $this->getRequest()->getParam('questionid','0');
        
        //删除问题
        $questiontable = new QuestionModel();
        $db = $questiontable->getAdapter();
        
        $where = $db->quoteInto('id = ?', $questionid);
        $flag0 = $questiontable->delete($where);
        
        //删除回复
        $answertable = new AnswerModel();
        $db1 = $answertable->getAdapter();
        $where = $db1->quoteInto('questionid = ?', $questionid);
        $answertable->delete($where);

        //查看是否删除干净
        $flag1 = count($db1->query('SELECT * FROM answer WHERE questionid = ?' , $questionid)->fetchAll());
        
        if($flag0 > 0 && $flag1 ==0){
            $this->view->info = 'success';
        }
        else{
            $this->view->info = 'fail';
        }
        
        $this->_forward('result4','globals');
        
    }
    
    public function agreeAction(){
        $answerid = $this->getRequest()->getParam('answerid','0');
        $questionid = $this->getRequest()->getParam('questionid','0');
        $account = $_COOKIE["account"];
        
        if($account == 'null'){
            $this->view->info = '未登录';
            $this->_forward('result3','globals');
            return;
        }
        
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
        
        $user_agreetable = new User_AgreeModel();
        $db2 = $user_agreetable->getAdapter();
        
        $answertable = new AnswerModel();
        $db3 = $answertable->getAdapter();
        //查找当前用户ID
        $result = $usertable -> fetchRow($db1->quoteInto('email = ?', $account)) ->toArray();
        $uid = $result['id'];
        
        $isAgreed = $db2->query('SELECT COUNT(*) as num FROM userconagree WHERE answerid = ? AND userid = ?' , Array($answerid,$uid))->fetchAll()[0]['num'];

        if($isAgreed!=0){
            //取消赞
            $where = $db2->quoteInto('answerid = ? AND ', $answerid).$db2->quoteInto('userid = ?',$uid);
            $user_agreetable->delete($where);
            
            $agrees = $db3->query('SELECT agrees FROM answer WHERE id = ?' , $answerid)->fetchAll()[0]['agrees'];
            --$agrees;
            $updateset = array(
                'agrees'=>$agrees
            );
            $answertable->update($updateset, $db3->quoteInto('id = ?', $answerid));
        }
        else{
            //赞
            $set = array(
                'answerid'=>$answerid,
                'userid'=>$uid
            );
            $user_agreetable->insert($set);
            
            $agrees = $db3->query('SELECT agrees FROM answer WHERE id = ?' , $answerid)->fetchAll()[0]['agrees'];
            ++$agrees;
            $updateset = array(
                'agrees'=>$agrees
            );
            $answertable->update($updateset, $db3->quoteInto('id = ?', $answerid));
        }
        
        $this->view->info = 'success';
        $this->view->id = $questionid;
        $this->_forward('result5','globals');
    }
    
    public function myquestionAction()
    {
        $page=$this->getRequest()->getParam('page');
        
        $account = $_COOKIE["account"];
      
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
        
        
        $questiontable = new QuestionModel();
        $db2 = $questiontable->getAdapter();
        
        
        //查找当前用户ID
        $result = $usertable -> fetchRow($db1->quoteInto('email = ?', $account)) ->toArray();
        $uid = $result['id'];
        
        //查找用户question
       // $questions = $questiontable-> fetchRow($db2->quoteInto('userid = ?', $uid)) ->toArray();
        
        $num= $db2->query('SELECT COUNT(*) as num FROM question,User WHERE question.userid = User.id and User.id =? order by time desc',$uid)->fetchAll()[0][num];
        
        
        //$num=count($questions);
        
        
        $pages=ceil($num/3);
        
        if($page=="")
        {
            $page=0;
        }
        
        //确定页面显示的规范  处理分页的逻辑
        if($page<3)
        {
            if($pages>3)
            {
                $set=array(1,2,3,4);
            }else{
                $set=array();
                for($i=1;$i<=$pages;$i++)
                {
                    $set[]=$i;
                }
            }
        }else if($page>=3)
        {
            if($page+3<=$pages)
            {
                $set=array($page,$page+1,$page+2,$page+3);
            }else{
                $set=array();
                for($i=$page;$i<=$pages;$i++)
                {
                    $set[]=$i;
                }
        
            }
        }
        
        
        $where =  $db2->quoteInto('userid=?', $uid);
   
        $order = 'begin desc';
        $count = 3;
        $offset =$page*3;
        $questions = $questiontable->fetchAll($where, $order, $count, $offset)->toArray();
        
        
        
        
        $this->view->results=$questions;
        $this->view->user=$result;
        $this->view->page=$page;
        $this->view->count=$set;
        
      
        
    }
    public function myreplyAction()
    {
        
        $page=$this->getRequest()->getParam('page');
        
        
        $account = $_COOKIE["account"];
        
        
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
        
        $answertable = new AnswerModel();
        $db3 = $answertable->getAdapter();
        

        //查找当前用户ID
        $result = $usertable -> fetchRow($db1->quoteInto('email = ?', $account)) ->toArray();
        $uid = $result['id'];
        
        
      $num= $db3->query('SELECT COUNT(*) as num FROM answer,User WHERE answer.userid = User.id and User.id =? order by time desc',$uid)->fetchAll()[0][num];
        
        
      $pages=ceil($num/3);
      
      if($page=="")
      {
          $page=0;
      }
      
        
      if($page<3)
      {
          if($pages>3)
          {
              $set=array(1,2,3,4);
          }else{
              $set=array();
              for($i=1;$i<=$pages;$i++)
              {
                  $set[]=$i;
              }
          }
      }else if($page>=3)
      {
          if($page+3<=$pages)
          {
              $set=array($page,$page+1,$page+2,$page+3);
          }else{
              $set=array();
              for($i=$page;$i<=$pages;$i++)
              {
                  $set[]=$i;
              }
      
          }
      }
      
      $count = 6;
      $offset =$page*6;
    
      
      $select=$db3->select();
      $select->from('answer','*');
      $select->join('question','answer.questionid=question.id','*');
      $select->where('answer.userid=?',7)
             ->limit($count,$offset);
      
      $sql = $select->__toString();
      $questions = $db3->fetchAll($sql);
   //   print_r($questions);
      
      /*
            
      $where = $db3->quoteInto('userid = ?', 7);
      $order = 'begin by time desc';
      $count = 6;
      $offset =$page*6;
      $questions = $answertable->fetchAll($where, $order, $count, $offset)->toArray();
 */
      
      $this->view->results=$questions;
      $this->view->user=$result;
      $this->view->page=$page;
      $this->view->count=$set;
      
        
    }
    public function expertAction()
    {
        
        $page=$this->getRequest()->getParam('page');
        
        $uid=$_SESSION["userinfo"][0][id];
        
    
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();               
        $expertinfo=new expertinfoModel();   
        $db2=$expertinfo->getAdapter();
        
        
        $concerntable=new  User_ExpertModel();
        $db3 = $concerntable->getAdapter();
        
            
        
        $num= $db2->query('SELECT COUNT(*) as num FROM expertinfo,User WHERE expertinfo.userid = User.id and User.type =2 ')->fetchAll()[0][num];
        
        $pages=ceil($num/4);
        
        
        if($page=="")
        {
            $page=0;
        } 
        if($page<3)
        {
            if($pages>3)
            {
                $set=array(1,2,3,4);
            }else{
                $set=array();
                for($i=1;$i<=$pages;$i++)
                {
                    $set[]=$i;
                }
            }
        }else if($page>=3)
        {
            if($page+3<=$pages)
            {
                $set=array($page,$page+1,$page+2,$page+3);
            }else{
                $set=array();
                for($i=$page;$i<=$pages;$i++)
                {
                    $set[]=$i;
                }
        
            }
        }
        
        
        
        
        
        
        $count = 4;
        $offset =$page*4;
        
        $select=$db2->select();
        $select->from('expertinfo','*');
        $select->join('User','expertinfo.userid=User.id','*');
        $select->where('User.type=?',2)
        ->limit($count,$offset);
        $sql = $select->__toString();
        $result = $db2->fetchAll($sql);
        
        $results=array();
        $i=0;
        foreach ($result as $info)
        {
            $id=$info[userid];         
            $num= $db3->query('SELECT COUNT(*) as num FROM userconexpert where expertid=? and userid=? ',Array($id,$uid))->fetchAll()[0][num];
           
            $info[Isconcern]=$num;
            $results[$i++]=$info;          
        }
      //  print_r($results);
        
        
        //print_r($result);      
        $this->view->result=$results;
        $this->view->page=$page;
        $this->view->count=$set;
        
       // exit();
    }
    
    public function concernAction()
    {
        $expertid = $this->getRequest()->getParam('expertid','0');
        $location=$this->getRequest()->getParam('location');
        $uid=$_SESSION["userinfo"][0][id];
        
        
        if($uid == 'null'){
            $this->view->info = '未登录';
            $this->_forward('result3','globals');
            return;
        }
        
        /*
        echo $expertid;
        echo $uid;
        exit();
        */
        
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
  
        
        $concerntable=new  User_ExpertModel();
        $db2 = $concerntable->getAdapter();
        
        $expertinfo=new expertinfoModel();
        $db3=$expertinfo->getAdapter();
   
        $isAgreed = $db2->query('SELECT COUNT(*) as num FROM userconexpert WHERE expertid = ? AND userid = ?' , Array($expertid,$uid))->fetchAll()[0][num];
       /* echo $isAgreed;
        exit();*/
        if($isAgreed!=0){
            //取消关注
            $where = $db2->quoteInto('expertid = ? AND ', $expertid).$db2->quoteInto('userid = ?',$uid);
            $concerntable->delete($where);
        
            $agrees = $db3->query('SELECT concern FROM expertinfo WHERE userid = ?' , $expertid)->fetchAll()[0]['concern'];
            --$agrees;
            $updateset = array(
                'concern'=>$agrees
            );
             $expertinfo->update($updateset, $db3->quoteInto('userid = ?', $expertid));
             $this->view->info = '取消关注成功';
             $this->view->location=$location;
             $this->view->expertid=$expertid;
             $this->_forward('result6','globals');
        }
        else{
            //关注
            $set = array(
                'userid'=>$uid,
                'expertid'=>$expertid
            );
           // print_r($set);
           // exit();
            
            $flag1=$concerntable ->insert($set);
           //exit();
        
            $agrees = $db3->query('SELECT concern FROM expertinfo WHERE userid = ?' , $expertid)->fetchAll()[0]['concern'];
            ++$agrees;
            $updateset = array(
                'concern'=>$agrees
            );
            $expertinfo->update($updateset, $db3->quoteInto('userid = ?', $expertid));
            
            
            $this->view->info = '关注成功';
            $this->view->location=$location;
            $this->view->expertid=$expertid;
            $this->_forward('result6','globals');
            
            
        }
    }
    public function expertdetailAction()
    {
        
        $id = $this->getRequest()->getParam('expertid','0');
        $type = $this->getRequest()->getParam('type','0');
        
        $userid=$_SESSION["userinfo"][0][id];
        
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
        
        $questions=$db3->query('SELECT * FROM userconquestion,User WHERE userconquestion.state=1 and userconquestion.userid=User.id and userconquestion.expertid=? order by time desc ',$id)->fetchAll();
        //  exit();
        //print_r($questions);
        
        $responses=$db4->query('select * from expertconresponse,userconquestion,User where expertconresponse.expertid=? and expertconresponse.questionid=userconquestion.id and userconquestion.userid=User.id order by time desc',$id)->fetchAll();
        //print_r($responses);
       
        $Isconcern= $db3->query('SELECT COUNT(*) as num FROM userconexpert where expertid=? and userid=? ',Array($id,$userid))->fetchAll()[0][num];
        
      
        
        
        $this->view->expertid=$id;
        $this->view->result=$expert;
        $this->view->question=$questions;
        $this->view->responses=$responses;
        $this->view->type=$type;
        $this->view->userid=$userid;
        $this->view->Isconcern=$Isconcern;
        
    }
    

}

