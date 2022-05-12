<?php

namespace App\Helpers;

class HashPassword
{
    public static function hash(string $inputPassword)
    {
        $password = password_hash($inputPassword, PASSWORD_DEFAULT);
        return $password;
    }

    public static function isValid(string $inputPassword, string $hashPassword)
    {
        return password_verify($inputPassword, $hashPassword);
    }
}
