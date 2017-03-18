<?php
if(isset($_POST['name'])){
    if(!empty($_POST['name'])){
        echo '您好，',$_POST['name'].'！';
    }
}
