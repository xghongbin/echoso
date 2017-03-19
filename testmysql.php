<?php
require ("Db/db.php");
$Conn = new Mysql;
//加载数据库参数
$Conn->Parameter('127.0.0.1', 'root', 'root', 'mysql', '', '');
if($Conn){
    echo "连接数据库成功！";
}else {
    echo "连接数据库失败！";
}
?>