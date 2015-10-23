<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/ActivityModel.php';
require_once APPLICATION_PATH.'/models/UserModel.php';
require_once APPLICATION_PATH.'/models/User_ActivityModel.php';
class ActivityController extends BaseController
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        
        //实现分页
        $page=$this->getRequest()->getParam('page');
        
        
        
        $activity = new ActivityModel();        
        $db = $activity->getAdapter();     
        if($page=="")
        {
            $page=0;
        }        
        //确定页面显示的规范
        if($page<3)
        {
            $set=array(1,2,3,4);
        }else
        {
            $set=array($page,$page+1,$page+2,$page+3);
        }
        //按开始时间倒序展示10条记录
        $where = '1=1';
        $order = 'begin desc';
        $count = 5;
        $offset =$page*5;
        $result = $activity->fetchAll($where, $order, $count, $offset)->toArray();
        $this->view->result = $result;
        $this->view->page=$page;
        $this->view->count=$set;
        
    }
    
    public function addactivityuiAction()
    {
        
    }
        
    public function addactivityAction()
    {
        $name = $this->getRequest()->getParam('name');
        $begin = $this->getRequest()->getParam('begin');
        $end = $this->getRequest()->getParam('end');
        $content = $this->getRequest()->getParam('content');
        
        $set = array(
            'content' => $content,
            'nums' => 0,
            'begin' => $begin,
            'end' => $end,
            'name' => $name
        );
        
        //添加一个新活动
        $activity = new ActivityModel();
       
        if($activity -> insert($set) > 0){
            $this->view->info = 'success';
        }
        else{
            $this->view->info = 'fail';
        }
        
        $this->_forward('result','globals');
    }

    public function showactivityAction(){
        $activityid = $this->getRequest()->getParam('activityid','0');
        
        $table = new ActivityModel();
        $result = $table -> find($activityid) -> toArray();
        
        //展示活动具体内容
        $this -> view -> result = $result[0];
    }
    
    public function modifyactivityuiAction(){
        $activityid = $this->getRequest()->getParam('activityid','0');
        
        $table = new ActivityModel();
        $result = $table -> find($activityid) -> toArray();
        
        $this -> view -> result = $result[0];
    }
    
    public function modifyactivityAction(){
        $activityid = $this->getRequest()->getParam('activityid','0');
        $name = $this->getRequest()->getParam('name');
        $begin = $this->getRequest()->getParam('begin');
        $end = $this->getRequest()->getParam('end');
        $content = $this->getRequest()->getParam('content');
        $nums = $this->getRequest()->getParam('nums');
        
        $set = array( 
            'content' => $content,
            'nums' => $nums,
            'begin' => $begin,
            'end' => $end,
            'name' => $name
        );
        
        //修改活动信息
        $table = new ActivityModel();
        $db = $table->getAdapter();
        
        $where = $db->quoteInto('id = ?', $activityid);
        
        
        if($table->update($set, $where) > 0){
            $this->view->info = 'success';
        }
        else{
            $this->view->info = 'fail';
        }
        
        $this->_forward('result2','globals');
        
    }
    
    public function deleteactivityAction(){
        $activityid = $this->getRequest()->getParam('activityid','0');
        
        //删除一条活动
        $table = new ActivityModel();
        $db = $table->getAdapter();
        
        $where = $db->quoteInto('id = ?', $activityid);
        
        if($table->delete($where) > 0){
            $this->view->info = 'success';
        }
        else{
            $this->view->info = 'fail';
        }
        
        $this->_forward('result2','globals');
    }

    public function chooseactivityAction(){
        $activityid = $this->getRequest()->getParam('activityid','0');
        $account = $_COOKIE["account"];
        
        if($account == null){
            $this->view->info = '未登录';
            $this->_forward('result3','globals');
            return;
        }
        
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
        
        $table = new User_ActivityModel();
        $db2 = $table->getAdapter();
        
        $activitytable = new ActivityModel();
        $db3 = $activitytable->getAdapter();
        
        //查找当前用户ID
        $result = $usertable -> fetchRow($db1->quoteInto('email = ?', $account)) ->toArray();
        $uid = $result['id'];
        
        //检查是否该用户参加过此活动
        $where = $db2->quoteInto('userid = ? ', $uid).$db2->quoteInto('AND activityid = ?', $activityid);
        $flag0 = count($table->fetchAll($where) ->toArray()) ;
        

        
        if($flag0 !=0){
            $this->view->info = '已参加';
            $this->_forward('result2','globals');
            return;
        }
        
        
        
        //添加一条记录
        $set = array(
            'userid'=>$uid,
            'activityid'=>$activityid
        );
        
        $flag1 = $table -> insert($set); 
        
        //活动列表更新nums
        $nums = $activitytable->find($activityid)->toArray()[0]['nums'];
        $nums++;
        $numsset = array(
            'nums'=>$nums
        );

        $where = $db3->quoteInto('id = ?', $activityid);
        
        $flag2 = $activitytable->update($numsset, $where);
        
        
        /* echo $flag1;
        echo $flag2;
        exit(); */
        //判断是否成功
        if($flag1 > 0 && $flag2 > 0){
            $this->view->info = 'success';
        }
        else{
            $this->view->info = 'fail';
        }
        
        $this->_forward('result2','globals');
        
    }
}

