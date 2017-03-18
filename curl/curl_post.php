<?php
/*
    curl模拟POST请求
   1：初始化curl
   2：向服务器发送请求
   3：就收服务器返回的数据
   4：关闭CURL

*/


$uri = "http://echoso.s3.natapp.cc/echoso/curl/upload.php";

// 参数数组
$data = array (
    'name' => 'tanteng'
);
$ch = curl_init ();

curl_setopt ( $ch, CURLOPT_URL, $uri );
curl_setopt ( $ch, CURLOPT_POST, 1 );
curl_setopt ( $ch, CURLOPT_HEADER, 0 );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
$return = curl_exec ( $ch );
curl_close ( $ch );
print_r($return);

