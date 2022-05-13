<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Helpers\Registry;
use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel;
        $products = $productModel->listProducts();

        return $this->view->use('home.php', [
            'products' => $products,
            'paypalClient' => Registry::get('paypal.client'),
        ]);
    }
}
