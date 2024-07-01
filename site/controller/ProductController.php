<?php 
class ProductController{
    // Danh sách sản phẩm
    function index($category_id = null)
    {
        $page = $_GET['page'] ?? 1;
        $conds = [];
        $sorts = [];
        $item_per_page = 10; //10 sản phẩm mỗi trang
        $productRepository = new ProductRepository();

         // Lấy danh sách sản phẩm theo danh mục
        if($category_id){
                $conds = [
                    'category_id' => [
                        'type' => '=',
                        'val' => $category_id //5
                    ]
                ];
            }
        // SELECT * FROM view_product WHERE category_id = 5 LITMIT 0,4

        $priceRange = $_GET['price-range'] ?? null;
        if($priceRange){
            $temp = explode('-', $priceRange);
            // Lấy danh sách theo khoảng giá
            $start = $temp[0];
            $end = $temp[1];
            $conds = [
                'sale_price' => [
                    'type' => 'BETWEEN',
                    'val' => "$start AND $end" // 200000 AND 300000
                    ]
                ];
                 // SELECT * FROM vỉew_product WHERE sale_price BETWWEN 20000 AND 30000
                if($end == 'greater')
                {
                    $conds = [
                        'sale_price' => [
                            'type' => '>=',
                            'val' => $start // 100000
                            ]
                        ];
                        // SELECT * FROM vỉew_product WHERE sale_price >= 100000
                }
        }

        // tìm kiếm
        // search = kem
        $search = $_GET['search'] ?? null;
       if($search){
               $conds = [
                   'name' => [
                       'type' => 'LIKE',
                       'val' => "'%$search%'" //kem
                   ]
               ];
           }
           // SELECT * FROM view_product WHERE name LIKE '%kem%'
           
        // Sắp xếp
        // sort=price-asc
        $sort = $_GET['sort'] ?? null;

        if($sort){
            $temp = explode('-', $sort);
            $dummyCol = $temp[0];

            // bảng ánh xạ
            $map = [
                'price' => 'sale_price', 
                'alpha' => 'name', 
                'created' => 'created_date'
            ];
            $colName = $map[$dummyCol]; //price chuyển thành sale-price

            $order = strtoupper($temp[1]); //asc => ASC
            $sorts = [$colName => $order];

            // SELECT * FROM view_product ORDER BY sale_price ASC
        }
            
        $products = $productRepository->getBy($conds, $sorts, $page, $item_per_page);

        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll(); // lấy hết danh sách trong mục

        $numTotalProduct = $productRepository->getByNumber($conds);

        $totalPage = ceil($numTotalProduct / $item_per_page);

        require ABSPATH_SITE . 'view/product/index.php';
    }

    function detail($id)
    {
        $productRepository = new ProductRepository();
        $product = $productRepository->find($id);
        $category_id = $product->getCategoryId();
        $conds = [
            'category_id' => [
                'type' => '=',
                'val' => $category_id //4
            ],
            'id' => [
                'type' => '!=',
                'val' => $id //5
            ]
        ];
        // SELECT * FROM view_product WHERE category_id =4 AND id != 5
        $relatedProducts = $productRepository->getBy($conds);

        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll(); // lấy hết danh sách trong mục
        
        require ABSPATH_SITE . 'view/product/detail.php';  
    }
    function storeComment()
    {
        $data = [];
        $data["email"] = $_POST['email'];
		$data["fullname"] = $_POST['fullname'];
		$data["star"] = $_POST['rating'];
		$data["created_date"] = date('Y-m-d H:i:s');
		$data["description"] = $_POST['description'];
		$data["product_id"] = $_POST['product_id'];
        $commentRepository = new CommentRepository();
        $commentRepository->save($data);
        $productRepository = new ProductRepository();
        $product = $productRepository->find($_POST['product_id']);
        require ABSPATH_SITE . 'view/product/comments.php';
    }
} 
?>