<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/QuestionModel.php';
require_once APPLICATION_PATH.'/models/UserModel.php';
require_once APPLICATION_PATH.'/models/AnswerModel.php';
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
        $where = '1=1';
        $order = 'time desc';
        $count = 10;
        $offset = 0;
        $result = $questiontable->fetchAll($where, $order, $count, $offset)->toArray();
        $this->view->result = $result; 
    }
    
    public function showAction()
    {
        $questionid = $this->getRequest()->getParam('questionid','0');
        
        $questiontable = new QuestionModel();
        $result = $questiontable -> find($questionid) -> toArray();
        //展示问题具体内容
        $this -> view -> result = $result[0];
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

    public function delete()
    {
        $questionid = $this->getRequest()->getParam('questionid','0');
        
        
    }

}

