<?php
namespace App\Models;

use PDO;
use PDOException;

class DataBaseModel
{
    private static $instance = null;
    private static $conn;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new DataBaseModel();
        }

        return self::$instance;
    }

    public static function connect($host, $port, $database, $user, $password)
    {
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$database";
            self::$conn = new PDO($dsn, $user, $password);
            $query = self::$conn->prepare("SET @@session.time_zone = '+00:00'");
            $query->execute();
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getConnection()
    {
        return self::$conn;
    }
}
