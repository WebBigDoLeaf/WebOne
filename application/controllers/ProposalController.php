<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/QuestionModel.php';
require_once APPLICATION_PATH.'/models/UserModel.php';
require_once APPLICATION_PATH.'/models/AnswerModel.php';
require_once APPLICATION_PATH.'/models/User_AgreeModel.php';
class ProposalController extends BaseController
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $questiontable = new QuestionModel();
        $db = $questiontable->getAdapter();
        //按开始时间倒序展示10条记录
        $result = $db->query('SELECT question.*,User.name as uname FROM question,User WHERE question.userid = User.id order by time desc')->fetchAll();
        $this->view->result = $result; 
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
        $answers = $db1->query('SELECT content,time,name,agrees,answer.id as id,answer.userid as uid FROM answer,User WHERE answer.userid = User.id AND questionid = ?' , $questionid)->fetchAll();
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
        $content = $this->getRequest()->getParam('content');
        
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

}

