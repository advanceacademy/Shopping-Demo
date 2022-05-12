<?php

namespace App\Controllers;

use App\Helpers\Registry;
use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $products = [
            [
                'id' => 1,
                'title' => 'Суитшърт Advance Academy',
                'description' => 'Суитшърта е любимото попълнение към аутфита на нашите преподаватели)) Попълни и ти твоя гардероб ;)',
                'price' => '45.00',
                'image' => '/public/catalog/product-1.jpg'
            ], [
                'id' => 2,
                'title' => 'Тениска Code by Advance Academy',
                'description' => 'Важните стъпки, които всеки програмист следва :) Отличи се от тълпата с интересна тениска',
                'price' => '24.00',
                'image' => '/public/catalog/product-2.jpg'
            ], [
                'id' => 3,
                'title' => 'Тениска FALSE by Advance Academy',
                'description' => 'Всеки програмист трябва да я има! Задай стил на аутфита си!',
                'price' => '24.00',
                'image' => '/public/catalog/product-3.jpg'
            ]
        ];

        return $this->view->use('home.php', [
            'products' => $products,
            'paypalClient' => Registry::get('paypal.client'),
        ]);
    }
}
