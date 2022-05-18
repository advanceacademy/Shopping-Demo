<?php

namespace App\Models;

use PDO;

class OrderModel extends BaseModel
{
    public function getDetails($order)
    {
        $orderId = $order->id ?? '';
        $orderStatus = $order->status ?? '';

        $purchaseUnits = $order->purchase_units[0] ?? null;

        $userId = (int) ($purchaseUnits->custom_id ?? 0);
        $orderAmount = $purchaseUnits->amount->value ?? 0.00;
        $orderAmountCurrency = $purchaseUnits->amount->currency_code ?? 'EUR';
        $items = array_map(function($item) {
            return [
                'id' => (int) str_replace('PRODUCT-ID-', '', $item->sku ?? ''),
                'quantity' => (int) ($item->quantity ?? 0),
                'amount' => (float) ($item->unit_amount->value ?? 0),
                'currency' => substr($item->unit_amount->currency_code ?? 'EUR', 0, 3),
            ];
        }, $purchaseUnits->items);

        $transaction = $purchaseUnits->payments->captures[0] ?? null;
        $transactionId = $transaction->id ?? null;
        $transactionStatus = $transaction->status ?? '';

        return [
            'user' => $userId,
            'reference' => $orderId,
            'status' => $orderStatus,
            'amount' => $orderAmount,
            'currency' => $orderAmountCurrency,
            'transaction_id' => $transactionId,
            'transaction_status' => $transactionStatus,
            'items' => $items,
        ];
    }

    public function findByReference($reference)
    {
        $sql = "SELECT
                    `id`,
                    `reference`,
                    `status`,
                    `amount`,
                    `currency`,
                    `transaction_id`,
                    `transaction_status`,
                    `user_id`,
                    `created_at` as 'created'
                FROM `orders`
                WHERE `reference` = :reference
            ";

        $query = $this->db->prepare($sql);
        $query->execute([
            'reference' => $reference,
        ]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $result['created'] =  date('c', strtotime($result['created']));

        $sql = "SELECT
                    op.`id`,
                    op.`quantity`,
                    op.`amount`,
                    op.`currency`,
                    p.`id` AS 'product_id',
                    p.`title`,
                    p.`description`,
                    p.`price`,
                    p.`image`,
                    p.`quantity` AS 'product_quantity'
                FROM
                    `order_product` op,
                    `products` p
                WHERE
                    p.`id` = op.`product_id`
                    AND op.`order_id` = :order_id
            ";
        $query = $this->db->prepare($sql);
        $query->execute(['order_id' => $result['id']]);

        $result['items'] = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $result['items'][] = $row;
        }

        return $result;
    }

    public function store($orderDetails)
    {
        $params = [
            'reference' => substr($orderDetails['reference'] ?? '', 0, 255),
            'status' => substr($orderDetails['status'] ?? '', 0, 255),
            'amount' => (float) ($orderDetails['amount'] ?? 0),
            'currency' => substr($orderDetails['currency'] ?? '', 0, 3),
            'transaction_id' => substr($orderDetails['transaction_id'] ?? '', 0, 255),
            'transaction_status' => substr($orderDetails['transaction_status'] ?? '', 0, 255),
            'user_id' => (int) ($orderDetails['user'] ?? 0),
        ];

        $sql = "INSERT INTO `orders` (
                    `reference`,
                    `status`,
                    `amount`,
                    `currency`,
                    `transaction_id`,
                    `transaction_status`,
                    `user_id`,
                    `created_at`,
                    `updated_at`
                ) VALUES (
                    :reference,
                    :status,
                    :amount,
                    :currency,
                    :transaction_id,
                    :transaction_status,
                    :user_id,
                    NOW(),
                    NOW()
                )";
        $query = $this->db->prepare($sql);
        $result = $query->execute($params);
        $orderId = $this->db->lastInsertId();

        if ($orderId) {
            $updateProducts = [];

            $params = [];
            $values = [];

            $sql = "INSERT INTO `order_product` (
                        `order_id`,
                        `product_id`,
                        `quantity`,
                        `amount`,
                        `currency`
                    ) VALUES ";

            foreach ($orderDetails['items'] as $item) {
                $values[] = "(" . implode(',', array_fill(0, 5, '?')) . ')';

                $params[] = $orderId;
                $params[] = $item['id'] ?? 0;
                $params[] = $item['quantity'] ?? 0;
                $params[] = $item['amount'] ?? 0;
                $params[] = $item['currency'] ?? '';

                $updateProducts[] = [
                    'query' => 'UPDATE `products`
                            SET `quantity` = `quantity` - :qty
                            WHERE `id` = :id
                            LIMIT 1',
                    'params' => [
                        'id' => $item['id'] ?? 0,
                        'qty' => $item['quantity'] ?? 0,
                    ]
                ];
            }

            $sql .= implode(',', $values);
            $query = $this->db->prepare($sql);
            $query->execute($params);

            // Update Quantities to each Ordered product
            foreach ($updateProducts as $update) {
                $query = $this->db->prepare($update['query']);
                $query->execute($update['params']);
            }
        }

        return $result;
    }

    public function updateStatuses($id, string $status, string $transactionStatus)
    {
        $params = [
            'status' => substr($status ?? '', 0, 255),
            'transaction_status' => substr($transactionStatus ?? '', 0, 255),
            'id' => (int) ($id ?? 0),
        ];

        $sql = "UPDATE `orders` SET
                    `status` = :status,
                    `transaction_status` = :transaction_status,
                    `updated_at` = NOW()
                WHERE `id` = :id
        ";
        $query = $this->db->prepare($sql);
        return $query->execute($params);
    }
}
