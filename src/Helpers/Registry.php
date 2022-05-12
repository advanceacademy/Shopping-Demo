<?php

namespace App\Helpers;

class Registry
{
    private static $data = [];

    private function __construct() {}

    public static function set(string $key, $value = null)
    {
        self::$data[$key] = $value;
    }

    public static function get(string $key, $defaultValue = null)
    {
        return self::$data[$key] ?? $defaultValue;
    }
}
