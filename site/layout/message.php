<?php 
$message = '';
// !empty($a) = isset($a) và $a phải có dữ liệu
// Dữ liệu rỗng: false, 0 , null. Ngược lại là dữ liệu khác rỗng
$classType = '';
if(!empty($_SESSION['success'])){
    $message =  $_SESSION['success'];
    // xoá phần tử có key là success trong biến $_SESSION
    unset($_SESSION['success']);
    $classType = 'success';
} elseif(!empty($_SESSION['error'])){
    $message = $_SESSION['error'];
    // xoá phần tử có key là error trong biến $_SESSION
    unset($_SESSION['error']);
    $classType = 'danger';
}
?>
<?php 
    if($message):?>
<div class="alert alert-<?=$classType?> mt-3 text-center"><?=$message?></div>
<?php endif ?>