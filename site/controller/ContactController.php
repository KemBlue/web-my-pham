<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class ContactController
{
    // Trang lien he
    function form()
    {
        require ABSPATH_SITE . 'view/contact/form.php';
    }
    function sendEmail()
    {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $message = $_POST['content'];

       $emailService = new EmailService();
       $to = SHOP_OWNER;
       $web = get_domain();
       $subject = "Godashop - Liên hệ";
       $content = "
       Xin chào chủ cửa hàng - đây là email tự động gửi feedback, <br>
       Dưới đây là thông tin khách hàng để lại feedback: <br>
       Tên: $fullname, <br>
       Email: $email, <br>
       SDT: $mobile, <br>
       Nội dung: $message <br>
       Được gửi từ trang web: $web
       ";
       $emailService->send($to, $subject, $content);
       echo 'Đã gửi email thành công !';
    }
}
?>