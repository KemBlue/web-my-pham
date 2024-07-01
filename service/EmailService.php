<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class EmailService
{
    function send($to, $subject, $content)
    {
         //Create an instance; passing `true` enables exceptions
         $mail = new PHPMailer(true);
         $mail->CharSet = 'UFT-8'; // FIX lỗi tiếng việt
 
         try {
             //Server settings
             $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
             $mail->isSMTP();                                            //Send using SMTP
             $mail->Host       = SMTP_HOST;                     //Set the SMTP server to send through
             $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
             $mail->Username   = SMTP_USERNAME;                     //SMTP username
            //  SMTP_USERNAME chính là qthien576@gmail.com
            // có nghĩa đây sẽ là email tự động gửi thông tin những khách hàng có feedback
             $mail->Password   = SMTP_SECRET;                               //SMTP password
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
             $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
 
             //Recipients
             $mail->setFrom(SMTP_USERNAME);
             //  SMTP_USERNAME chính là qthien576@gmail.com
            // có nghĩa đây sẽ là email tự động gửi thông tin những khách hàng có feedback
             $mail->addAddress($to);     //Add a recipient
            //  $to là vinhq2505@gmail.com nghĩa là email của chủ cửa hàng
            // email này của chủ sẽ xem được nội dung của các feedback từ khách hàng thông qua email tự động gửi kia
 
             // $mail->addAddress('ellen@example.com');               //Name is optional
             // $mail->addReplyTo('info@example.com', 'Information');
             // $mail->addCC('cc@example.com');
             // $mail->addBCC('bcc@example.com');
 
             //Attachments
             // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
             // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
 
             //Content
             $mail->isHTML(true);                                  //Set email format to HTML
             $mail->Subject = $subject;
             $mail->Body    = $content;
             // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
 
             $mail->send();
                 } catch (Exception $e) {
                     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                 }
    }
}
?>