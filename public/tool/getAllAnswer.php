<?php
    //这里两句话很重要,第一讲话告诉浏览器返回的数据是xml格式
    header("Content-Type: text/xml;charset=utf-8");
    //告诉浏览器不要缓存数据
    header("Cache-Control: no-cache");
    $questionid=$_GET['questionid'];
    $result = '[';
    $db = new SQLite3(substr(dirname(__FILE__), 0,strlen(dirname(__FILE__))-4).'testDB.db3');
    $selectsql = "select * from answer where questionid = '$questionid'";
    $ret = $db->query($selectsql);
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
        //acontent,agrees,time,replyid,userid,questionid,inform_userid
        $result.='("acontent":"'.$row['acontent'] . '",';
        $result.='"agrees":'.$row['agrees'] . ",";
        $result.='"time":"'.$row['time'] . '",';
        $result.='"replyid":'.$row['replyid'] . ",";
        $result.='"userid":'.$row['userid'] . ",";
        $result.='"questionid":'.$row['questionid'] . ",";
        $result.='"inform_userid":'.$row['inform_userid'] . "),";
    }
    $result=substr($result, 0,strlen($result)-1);
    $result.=']';
    echo $result;
    
    