<?php 
    // chưa login
    if(empty($_SESSION['email'])){
        header('location: /');
        exit;
    }

?>