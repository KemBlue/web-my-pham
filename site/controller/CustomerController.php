<?php 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CustomerController{
    // Hiển thị trang thông tin tài khoản
    function show(){
        require ABSPATH_SITE . 'checkLogin.php';
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        require ABSPATH_SITE . 'view/customer/show.php';
    }
    // Hiển thị trangg địa chỉ giao hàng mặc định
    function shippingDefault(){
        require ABSPATH_SITE . 'checkLogin.php';
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        require ABSPATH_SITE . 'layout/variable_address.php';
        require ABSPATH_SITE . 'view/customer/shippingDefault.php';
    }
    function updateShippingDefault(){
        require ABSPATH_SITE . 'checkLogin.php';
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        $customer->setShippingName($_POST['fullname']);
        $customer->setShippingMobile($_POST['moblie']);
        $customer->setWardId($_POST['ward']);
        $customer->setHousenumberStreet($_POST['address']);

        // lưu đối tượng customer đã cập nhật xuống database
        if(!$customerRepository->update($customer)){
            // lưu thất bại
            $_SESSION['error'] = $customerRepository->getError();
            header('location: /index.php?c=customer&a=shippingDefault');
            exit;
        }
        // lưu thành công
        $_SESSION['success'] = 'Cập nhật thông tin địa chỉ giao hàng thành công';
        header('location: /index.php?c=customer&a=shippingDefault');
    }
    // Hiển thị trang danh sách đơn hàng
    function orders(){
        require ABSPATH_SITE . 'checkLogin.php';
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        $orderReponsitory = new OrderRepository();
        $orders = $orderReponsitory->getByCustomerId($customer->getId());
        require ABSPATH_SITE . 'view/customer/orders.php';
    }
    // Hiển thị trang danh sách chi tiết đơn hàng
    function orderDetail(){
        require ABSPATH_SITE . 'checkLogin.php';
        $id = $_GET['id'];
        $orderReponsitory = new OrderRepository();
        $order = $orderReponsitory->find($id);
        require ABSPATH_SITE . 'view/customer/orderDetail.php';
    }
    function updateAccount(){
        require ABSPATH_SITE . 'checkLogin.php';
        $name = $_POST['fullname'];
        $mobile = $_POST['mobile'];

        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        // update thông tin từ giao diện vào biến $customer
        $customer->setName($name);
        $customer->setMobile($mobile);

        // Kiểm tra người dùng có nhu cầu đổi mật khẩu không
        $password = $_POST['password'];
        $current_password = $_POST['current_password'];
        if($password && $current_password){
            // người dùng có nhu cầu đổi mật khẩu
            // kiểm tra mật khẩu hiện tại có đúng trong database không?
            if(!password_verify($current_password,$customer->getPassword())){
                $_SESSION['error'] = 'Error: Mật khẩu hiện tại không đúng, vui lòng nhập lại';
            header('location: /index.php?c=customer&a=show');
            exit;
            }
            // đúng mật khẩu hiện tại
            if($password == $current_password){
                $_SESSION['error'] = 'Error: Mật khẩu mới không được giống với mật khẩu hiện tại, vui lòng nhập lại';
            header('location: /index.php?c=customer&a=show');
            exit;
            }
            // mật khẩu mới đã ok
            // mã hoá mật khẩu trước khi lưu
            $encode_password = password_hash($password, PASSWORD_BCRYPT);
            $customer->setPassword($encode_password);
        }

        // lưu đối tượng customer đã cập nhật xuống database
        if(!$customerRepository->update($customer)){
            // lưu thất bại
            $_SESSION['error'] = $customerRepository->getError();
            header('location: /index.php?c=customer&a=show');
            exit;
        }
        // lưu thành công
        $_SESSION['name'] = $name;
        $_SESSION['success'] = 'Cập nhật thông tin tài khoản thành công';
        header('location: /index.php?c=customer&a=show');
    }
    function register() {
        $secret = GOOGLE_RECAPTCHA_SECRET;
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        $gRecaptchaResponse = $_POST['g-recaptcha-response'];
        $remoteIp = '127.0.0.1';
        $resp = $recaptcha->setExpectedHostname(get_host_name())->verify($gRecaptchaResponse, $remoteIp);
        if (!$resp->isSuccess()) {
            // Verified!
            $errors = $resp->getErrorCodes();
            $error_str = implode('<br>', $errors);
            $_SESSION['error'] = "Error: $error_str";
            header('location: /');
            exit;
        }
        // Nếu thành công
        // Tạo account lưu xuống database
        $data = [];
        $data["name"] = $_POST['fullname'];
        $data["password"] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $data["mobile"] = $_POST['mobile'];
        $data["email"] = $_POST['email'];
        $data["login_by"] = 'form';
        $data["shipping_name"] = $_POST['fullname'];
        $data["shipping_mobile"] = $_POST['mobile'];
        $data["ward_id"] = null;
        $data["is_active"] = 0;
        $data["housenumber_street"] = '';
        $customerRepository = new CustomerRepository();
        if(!$customerRepository->save($data)){
            $_SESSION['error'] = $customerRepository->getError();
            header('location: /');
            exit;
        }
        // Gửi email kích hoạt tài khoản
        $emailService = new EmailService();
        $to = $_POST['email'];
        $subject = 'Godashop - Active Account';
        $name = $_POST['fullname'];

        $key = JWT_KEY;
        $payload = [
            'email' => $to
        ];
        $token = JWT::encode($payload, $key, 'HS256');

        $url_active = get_domain() . '/index.php?c=customer&a=active&token=' . $token;
        $link = "<a href='$url_active'>Active Account</a>";
        $website = get_domain();
        $content = "
            Dear $name, <br>
            Vui lòng click vào link bên dưới để active account <br>
            $link <br>
            --------------------- <br>
            Được gửi từ website $website
        ";
        $emailService->send($to, $subject, $content);

        $_SESSION['success'] = "Tài khoản đã được tạo thành công. Vui lòng kiểm tra email để kích hoạt tài khoản";
        header('location: /');
    }
    function notExistingEmail(){
        $email = $_GET['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if(!empty($customer)){
            echo 'false';
            return;
        }
        echo 'true';
    }

    function active(){
        $key = JWT_KEY;
        $token = $_GET['token'];
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $email = $decoded->email;

        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        // active account
        $customer->setIsActive(1);

        if(!$customerRepository->update($customer)){
            $_SESSION['error'] = $customerRepository->getError();
            header('location: /');
            exit;
        }
        $_SESSION['name'] = $customer->getName();
        $_SESSION['email'] = $customer->getEmail();
        header('location: /index.php?c=customer&a=show');
    }

    // Mã hoá
    function test1(){
        $key = 'kemper';
        $payload = [
            'email' => 'abc@gmail.com'
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');
        echo $jwt; 
    }
    // Giải mã
    function test2(){
        $key = 'kemper';
        $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImFiY0BnbWFpbC5jb20ifQ.5Z8Ui87zrvsgyQYiC87HQRKc3xQUq6BBmPB40CsbM28';
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        print_r($decoded);
    }
}
?>