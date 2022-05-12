<?php

namespace App\Controllers;

use App\Helpers\Response;
use App\Helpers\Request;
use App\Helpers\Registry;
use App\Helpers\JwtToken;
use App\Helpers\View;
use App\Traits\CommonHttpErrorsTrait;

class BaseController
{
    use CommonHttpErrorsTrait;

    protected $response;
    protected $request;

    public function __construct()
    {
        date_default_timezone_set('UTC');
        $this->response = new Response;
        $this->request = new Request;
        $this->view = new View(Registry::get('app.views'));
    }

    protected function auth()
    {
        $jwt = new JwtToken(
            Registry::get('jwt.private'),
            Registry::get('jwt.public'),
            Registry::get('jwt.algorithm')
        );

        $headers = explode(" ", $_SERVER['HTTP_AUTHORIZATION'] ?? '')[1] ?? '';
        try {
            return $jwt->decode($headers);
        } catch (\Exception $e) {
            return null;
        }
    }
}
