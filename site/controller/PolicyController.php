<?php 
class PolicyController
{
    // Chính sách đổi trả
    function return()
    {
        require ABSPATH_SITE . 'view/policy/return.php';
    }

    // Chính sách thanh toán
    function payment()
    {
        require ABSPATH_SITE . 'view/policy/payment.php';
    }

    // Chính sách giao hàng
    function delivery()
    {
        require ABSPATH_SITE . 'view/policy/delivery.php';
    }
}
?>