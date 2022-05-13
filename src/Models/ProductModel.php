<?php

namespace App\Models;

use App\Helpers\HashPassword;
use PDOException;
use PDO;

class ProductModel extends BaseModel
{
    public function findById($id)
    {
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `price`,
                    `quantity`,
                    `image`,
                    `created_at` as 'created'
                FROM `products`
                WHERE `id` = :id
            ";

        $query = $this->db->prepare($sql);
        $query->execute([
            'id' => $id,
        ]);
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        $result['created'] =  date('c', strtotime($result['created']));

        return $result;
    }

    public function listProducts()
    {
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `price`,
                    `quantity`,
                    `image`,
                    `created_at` as 'created'
                FROM `products`
                WHERE `status` = 'active' AND `quantity` > 0
            ";

        $query = $this->db->prepare($sql);
        $query->execute();

        $result = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $row['created'] =  date('c', strtotime($row['created']));
            $result[] = $row;
        }

        return $result;
    }
}
