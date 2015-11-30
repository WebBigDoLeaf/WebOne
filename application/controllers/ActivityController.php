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
        $num = $db->query('SELECT COUNT(*) as num from activity')->fetchAll()[0]['num'];
     
        $pages=ceil($num/5);    
        
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
        
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
        
        $uid=$_SESSION["userinfo"][0][id];     
        //echo $uid;
        //echo $db1->quoteInto('id', $uid);
        $user = $usertable -> fetchRow($db1->quoteInto('id=?', $uid)) ->toArray();
       // print_r($user);
        //echo $user[type];
       // exit();
        
        
        //按开始时间倒序展示10条记录
        $where = '1=1';
        $order = 'begin desc';
        $count = 5;
        $offset =$page*5;
        $result = $activity->fetchAll($where, $order, $count, $offset)->toArray();
        $this->view->result = $result;
        $this->view->page=$page;
        $this->view->count=$set;
        $this -> view -> type = $user['type'];
        
        
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
        
        $time=date("y-m-d",time());
        
        $imagename=$name.$time;
      //  echo $time;
       // echo $name;
       // echo $imagename;
       // exit();
       storeimage($imagename);
        
        $set = array(
            'content' => $content,
            'nums' => 0,
            'begin' => $begin,
            'end' => $end,
            'name' => $name,
            'image'=>$imagename
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
    public function storeimage($imagename)
    {
        //上传文件类型列表
        $uptypes=array(
            'image/jpg',
            'image/jpeg',
            'image/png',
            'image/pjpeg',
            'image/gif',
            'image/bmp',
            'image/x-png'
        );
    
        $max_file_size=2000000;     //上传文件大小限制, 单位BYTE
        $destination_folder=$_SERVER['DOCUMENT_ROOT']."/WebOne/public/image/activity/"; //上传文件路径
        $watermark=1;      //是否附加水印(1为加水印,其他为不加水印);
        $watertype=1;      //水印类型(1为文字,2为图片)
        $waterposition=1;     //水印位置(1为左下角,2为右下角,3为左上角,4为右上角,5为居中);
        $waterstring="健康days";  //水印字符串
        $waterimg="xplore.gif";    //水印图片
        $imgpreview=2;      //是否生成预览图(1为生成,其他为不生成);
        $imgpreviewsize=1/2;    //缩略图比例
        $id=$imagename;
    
    
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            if (!is_uploaded_file($_FILES["upfile"]["tmp_name"]))
            //是否存在文件
            {
                echo "图片不存在!";
                exit;
            }
    
            $file = $_FILES["upfile"];
            if($max_file_size < $file["size"])
            //检查文件大小
            {
                echo "文件太大!";
                exit;
            }
    
            if(!in_array($file["type"], $uptypes))
            //检查文件类型
            {
                echo "文件类型不符!".$file["type"];
                exit;
            }
    
            if(!file_exists($destination_folder))
            {
                mkdir($destination_folder);
            }
    
            $filename=$file["tmp_name"];
            $image_size = getimagesize($filename);
            $pinfo=pathinfo($file["name"]);
            $ftype=$pinfo['extension'];
            $destination = $destination_folder.$id."."."jpg";
            /*
             if (file_exists($destination) && $overwrite != true)
             {
             echo "同名文件已经存在了";
             exit;
             }
             */
    
            if(!move_uploaded_file ($filename, $destination))
            {
                echo " <font color=red id=update_success> 移动文件出错 </font><br>";
                exit;
            }
            header("Location: http://localhost:8081/WebOne/public/Home/portrait?type=success");
            exit;
    
            $pinfo=pathinfo($destination);
            $fname=$pinfo["basename"];
            echo " <font color=red id=update_success>已经成功上传</font><br>";
            /*
             echo " 宽度:".$image_size[0];
             echo " 长度:".$image_size[1];
             echo "<br> 大小:".$file["size"]." bytes";
             */
            if($watermark==2)
            {
                $iinfo=getimagesize($destination,$iinfo);
                $nimage=imagecreatetruecolor($image_size[0],$image_size[1]);
                $white=imagecolorallocate($nimage,255,255,255);
                $black=imagecolorallocate($nimage,0,0,0);
                $red=imagecolorallocate($nimage,255,0,0);
                imagefill($nimage,0,0,$white);
                switch ($iinfo[2])
                {
                    case 1:
                        $simage =imagecreatefromgif($destination);
                        break;
                    case 2:
                        $simage =imagecreatefromjpeg($destination);
                        break;
                    case 3:
                        $simage =imagecreatefrompng($destination);
                        break;
                    case 6:
                        $simage =imagecreatefromwbmp($destination);
                        break;
                    default:
                        die("不支持的文件类型");
                        exit;
                }
    
                imagecopy($nimage,$simage,0,0,0,0,$image_size[0],$image_size[1]);
                imagefilledrectangle($nimage,1,$image_size[1]-15,80,$image_size[1],$white);
    
                switch($watertype)
                {
                    case 1:   //加水印字符串
                        imagestring($nimage,2,3,$image_size[1]-15,$waterstring,$black);
                        break;
                    case 2:   //加水印图片
                        $simage1 =imagecreatefromgif("xplore.gif");
                        imagecopy($nimage,$simage1,0,0,0,0,85,15);
                        imagedestroy($simage1);
                        break;
                }
    
                switch ($iinfo[2])
                {
                    case 1:
                        //imagegif($nimage, $destination);
                        imagejpeg($nimage, $destination);
                        break;
                    case 2:
                        imagejpeg($nimage, $destination);
                        break;
                    case 3:
                        imagepng($nimage, $destination);
                        break;
                    case 6:
                        imagewbmp($nimage, $destination);
                        //imagejpeg($nimage, $destination);
                        break;
                }
    
                //覆盖原上传文件
                imagedestroy($nimage);
                imagedestroy($simage);
            }
    
            if($imgpreview==1)
            {
                echo "<br>图片预览:<br>";
                echo "<img src=\"".$destination."\" width=".($image_size[0]*$imgpreviewsize)." height=".($image_size[1]*$imgpreviewsize);
                echo " alt=\"图片预览:\r文件名:".$destination."\r上传时间:\">";
            }
        }
    
    }

    public function showactivityAction(){
        
        
        $activityid = $this->getRequest()->getParam('activityid','0');
        
        $table = new ActivityModel();
        $result = $table -> find($activityid) -> toArray();
        
        $usertable = new UserModel();
        $db1 = $usertable->getAdapter();
        
        $uid=$_SESSION["userinfo"][0][id];
        
        
        $user = $usertable -> fetchRow($db1->quoteInto('id=?', $uid)) ->toArray();
        
        
        //echo $user['type'];
        
        //展示活动具体内容
        $this -> view -> result = $result[0];
        $this -> view -> type = $user['type'];
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
        
        $uid=$_SESSION["userinfo"][0];
        
        if($uid == null){
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
    
    
    
     public function myactivityAction()
     {

         $usertable = new UserModel();
         $db1 = $usertable->getAdapter();         
         $uid=$_SESSION["userinfo"][0][id];   
         
         $user = $usertable -> fetchRow($db1->quoteInto('id=?', $uid)) ->toArray();
         
        
         $table = new User_ActivityModel();
         $db2 = $table->getAdapter();
         
         $activitytable = new ActivityModel();
         $db3 = $activitytable->getAdapter();
         
         
         
         //$where = $db2->quoteInto('userid = ? ', $uid);
         //$flag0 = count($table->fetchAll($where) ->toArray()) ;
         
 
         
         $result= $db3->query('SELECT activity.* FROM activity,userconactivity WHERE activity.id = userconactivity.activityid and userconactivity.userid =? order by activity.begin desc',$uid)->fetchAll();
         
         
         $this ->view ->result=$result;
         $this -> view -> type = $user['type'];
          
         
         
     }
}

