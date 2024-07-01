<?php
class HomeController
{
    function index()
    {
        $productRepository = new ProductRepository();
        $conds = [];
        $sorts = ['featured' => 'DESC'];
        $page = 1;
        $item_per_page = 4;
        // 4 sản phẩn nổi bật
        $featuredProducts = $productRepository->getBy($conds,$sorts,$page,$item_per_page);


        $sorts = ['created_date' => 'DESC'];
        // 4 sản phẩm mới nhất
        $latestProducts = $productRepository->getBy($conds,$sorts,$page,$item_per_page);

        // xây dựng danh sách danh mục và sản phẩm kèm theo
        // xây dựng danh sách khối, mỗi khối bao gồm tên danh mục và danh sách kèm theo

        $categoryProducts =[];

        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll(); // lấy danh sách danh mục trong database
        foreach ($categories as $category){
            // lấy danh sách sản phẩm theo danh mục
            $conds = [
                'category_id' => [
                    'type' => '=',
                    'val' => $category->getId() //5
                ]
                ];
                // SELECT * FROM view_product WHERE category_id = 5 LITMIT 0,4
            $products = $productRepository->getBy($conds,[],$page, $item_per_page);
            $categoryProducts[] = [
                'categoryName' => $category->getName(),
                'products' => $products
            ];
        }
        require ABSPATH_SITE . 'view/home/index.php';
    }
}