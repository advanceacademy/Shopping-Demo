<?php

namespace App\Controllers;

use Throwable;
use App\Helpers\Registry;
use App\models\OrderModel;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalHttp\HttpException;

class PaymentController extends BaseController
{
    public function capture(string $order)
    {
        $client = $this->getClient();

        $orderRequest = new OrdersGetRequest($order);
        $response = $client->execute($orderRequest);

        $orderModel = new OrderModel();
        $details = $orderModel->getDetails($response->result);

        $order = $orderModel->findByReference($details['reference']);
        if ($order && (($order['status'] !== $details['status']) || $order['transaction_status'] !== $details['transaction_status'])) {
            $orderModel->updateStatuses($order['id'], $details['status'], $details['transaction_status']);
            return $this->response->setCode(204);
        } elseif (!$order) {
            $orderModel->store($details);
            return $this->response->setCode(201);
        } else {
            return $this->response->setCode(404);
        }
    }

    protected function getClient()
    {
        $clientId = Registry::get('paypal.client');
        $clientSecret = Registry::get('paypal.secret');

        $environment = Registry::get('paypal.live')
            ? new ProductionEnvironment($clientId, $clientSecret)
            : new SandboxEnvironment($clientId, $clientSecret)
        ;
        return new PayPalHttpClient($environment);
    }

}
