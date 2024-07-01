<?php 
class AuthController{
    function login(){
        $email = $_POST['email'];
        // kiểm tra email có tồn tại trong database không?
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if(!$customer){
            $_SESSION['error'] = "ERROR: Không tồn tại $email trong hệ thống. Vui lòng chọn email khác";
            header('location: /'); // trở về trangg chủ
        }
        // kiểm tra mật khẩu đúng không
        $password = $_POST['password'];
       // password_verify($p1,$p2);
       // $p1 là mật khẩu chưa mã hoá, $p2 là mật khẩu đã mã hoá 
       // password_verify kiểm tra xem mật khẩu đã mã hoá $p2 có phải được sinh ra từ mật khẩu $p1 không
       // Nếu đúng trả về True sai trả về False
        if(!password_verify($password,$customer->getPassword())){
            $_SESSION['error'] = "ERROR: Sai mật khẩu. Vui lòng nhập mật khẩu khác";
            header('location: /'); // trở về trangg chủ
            exit;
        }
        if(!$customer->getIsActive()){
            $_SESSION['error'] = "ERROR: Tài khoản của bạn chưa được kích hoạt. Vui active account";
            header('location: /'); // trở về trangg chủ
            exit;
        }

        // login thành công
        // lưu dữ liệu vào session
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $customer->getName();
        header('location: /index.php?c=customer&a=show'); // trở về trang thông tin tài khoản
    }
    function logout(){
        // xoá tất cả session khi logout
         setcookie("cart",null); // hết hạn
        session_destroy();
        header('location: /'); // trở về trangg chủ
    }
}
?>