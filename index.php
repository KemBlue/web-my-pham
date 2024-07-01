<?php
// File này sẽ đóng vai trò router trong mô hình mvc
// Dùng 2 tham số trên đường dẫn trang web để điều khiển chạy hàm trong controller. Dùng 2 tham số c và a. C là controller và a là action (hàm). Ví dụ
// qlsvmvc.com?c=subject&a=create => chạy hàm create() của SubjectController
// qlsvmvc.com  => chạy hàm index() của StudentController

// => nếu không có c thì ta mặc định c là student
// => nếu không có a thì ta mặc đinh a là index

// Viết code để chạy theo ý tưởng trên:
use Cocur\Slugify\Slugify;

session_start();

// import config & connectDb
require 'config.php';
require ABSPATH . 'connectDb.php';

// import autoload
require 'vendor/autoload.php';

// chỉ định model
require ABSPATH . 'bootstrap.php';

// chỉ định các controller
require ABSPATH_SITE . 'load.php';

// sử dụng cho đường dẫn đẹp
$router = new AltoRouter();
$slugify = new Slugify();

// Trang chủ
$router->map( 'GET', '/', function() {
	$controller =  new HomeController();
    $controller->index();
}, 'home');

// Sản phẩm
$router->map( 'GET', '/san-pham.html', function() {
	$controller =  new ProductController();
    $controller->index();
}, 'product');

// Chính sách thanh toán
$router->map( 'GET', '/chinh-sach-thanh-toan.html', function() {
	$controller =  new PolicyController();
    $controller->payment();
}, 'payment');

// Chính sách trả hàng
$router->map( 'GET', '/chinh-sach-tra-hang.html', function() {
	$controller =  new PolicyController();
    $controller->return();
}, 'return');

// Chính sách giao hàng
$router->map( 'GET', '/chinh-sach-giao-hang.html', function() {
	$controller =  new PolicyController();
    $controller->delivery();
}, 'delivery');

// Liên hệ
$router->map( 'GET', '/lien-he.html', function() {
	$controller =  new ContactController();
    $controller->form();
}, 'contact');

// danh-muc/kem-lam-trang-da-c1257.html
$router->map( 'GET', '/danh-muc/[*:slug]-c[i:id].html', function($slug, $id) {
	$controller =  new ProductController();
    $controller->index($id);
}, 'category');

// san-pham/sua-chong-nang-sunplay-duong-da-sang-min-spf50-pa-55g-12064.html
$router->map( 'GET', '/san-pham/[*:slug]-[i:id].html', function($slug, $id) {
	$controller =  new ProductController();
    $controller->detail($id);
}, 'productDetail');

// match current request url
$match = $router->match();
$routeName = is_array($match) ? $match['name'] : '';

// call closure or throw 404 status
if( is_array($match) && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] );
} else {
	$c = $_GET['c'] ?? 'home';
    $a = $_GET['a'] ?? 'index';

    // tạo tên class 
    // ucfirst() là viết hoa ký tự đầu tiên của chuỗi
    $controllerClass = ucfirst($c) . 'Controller';

    // Tạo đối tượng controller từ class
    $objController = new $controllerClass();

    // Gọi hàm chạy
    $objController->$a();
}