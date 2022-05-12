<?php

namespace App\Models;

use App\Models\DataBaseModel;
use App\Helpers\Registry;

class BaseModel
{
    protected $db;

    public function __construct()
    {
        $conn = DataBaseModel::getInstance();

        DataBaseModel::connect(
            Registry::get('db.hostname'),
            Registry::get('db.port'),
            Registry::get('db.name'),
            Registry::get('db.username'),
            Registry::get('db.password')
        );

        $this->db = $conn->getConnection();
    }
}
