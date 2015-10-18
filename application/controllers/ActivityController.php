<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH.'/models/ActivityModel.php';
class ActivityController extends BaseController
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        
        $activity = new ActivityModel();
        $db = $activity->getAdapter();
        $where = '1=1';
        $order = 'begin desc';
        $count = 10;
        $offset = 0;
        $result = $activity->fetchAll($where, $order, $count, $offset)->toArray();
        $this->view->result = $result;
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

}

