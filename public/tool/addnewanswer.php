<?php 


    //这里两句话很重要,第一讲话告诉浏览器返回的数据是xml格式
    header("Content-Type: text/xml;charset=utf-8");
    //告诉浏览器不要缓存数据
    header("Cache-Control: no-cache");
    
    
    $db = new SQLite3(substr(dirname(__FILE__), 0,strlen(dirname(__FILE__))-4).'testDB.db3');
   
    //获取城市的名字
    $acontent=$_REQUEST['acontent'];
    $replyid=$_REQUEST['replyid'];
    $userid=$_REQUEST['userid'];
    $questionid=$_REQUEST['questionid'];
    $inform_userid=$_REQUEST['inform_userid'];
    
    //获取当前时间
    $t = time();
    $time = date("Y-m-d H:i:s",$t);
    
    $insertsql = "insert into answer (acontent,agrees,time,replyid,userid,questionid,inform_userid)
    values('$acontent',0,'$time','$replyid','$userid','$questionid','$inform_userid')";
    
    $ret1 = $db->exec($insertsql);

    $selectsql = "select * from question where id = '$questionid'";
    $answernum = $db->query($selectsql)->fetchArray(SQLITE3_ASSOC)['answernum'];
    $answernum ++;
    $updatesql = "update question set answernum = '$answernum' where id = '$questionid'";
    $ret2 = $db->exec($updatesql);
    
    if(!$ret1&&!ret2){
        echo "err";
    } else {
        echo "success";
    } 
    $db->close();
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /* $connect;
    $error = '';
    $dbname="testDB.db3";
    $username="root";
    $password="";
    $host="127.0.0.1";
    
    $connect=mysql_connect($host,$username,$password);
    if(!$connect){
        die(mysql_error());
    }
    mysql_select_db($this->dbname,$this->conn);
    
    //获取城市的名字
    $acontent=$_REQUEST['acontent'];
    $replyid=$_REQUEST['replyid'];
    $userid=$_REQUEST['userid'];
    $questionid=$_REQUEST['questionid'];
    $inform_userid=$_REQUEST['inform_userid'];
    
    //获取当前时间
    $t = time();
    $time = date("Y-m-d H:i:s",$t);
    
    $sql = "insert into answer(acontent,agrees,time,replyid,userid,questionid,inform_userid)
    values('$acontent',0,'$time','$replyid','$userid','$questionid','$inform_userid')";
    
    $query = sqlite_exec($connect, $sql, $error); 
    
    $flag = $answer -> addAnswer($acontent, $replyid, $userid, $questionid, $inform_userid);
    if($flag>0)
        echo "success";
    else 
        echo "err"; */
    /* $result = $questiontable -> find($questionid) -> toArray()[0]['answernum'];
    ++$result;
    $numset = array(
        'answernum'=>$result
    );
    $where = $db2->quoteInto('id = ?', $questionid);
    $questiontable -> update($numset, $where); */