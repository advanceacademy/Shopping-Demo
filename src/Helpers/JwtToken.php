<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\UserModel;

class JwtToken
{
    protected $iat;
    protected $exp;
    protected $alg;
    protected $publicKey;
    protected $privateKey;

    public function __construct(string $privateKeyPath, string $publicKeyPath, string $algorithm = 'RS256')
    {
        $this->iat = time();
        $this->exp = $this->iat + 60 * 60 * 24;
        $this->alg = $algorithm;
        $this->privateKey = file_get_contents($privateKeyPath);
        $this->publicKey = file_get_contents($publicKeyPath);
    }

    public function create(array $payloadAddition = [])
    {

        $payload = array(
            "iat" => $this->iat,
            "nbf" => $this->iat,
            "exp" => $this->exp
        );

        $payloadAll = array_merge($payload, $payloadAddition);

        $jwt = JWT::encode($payloadAll, $this->privateKey, $this->alg);
        return $jwt;
    }

    public function decode(string $jwt = '')
    {
        $decoded = (array) JWT::decode($jwt, new Key($this->publicKey, $this->alg));
        $userModel = new UserModel();
        $user = $userModel->findById($decoded['id']);
        $user = $userModel->findByEmail($user['email']);
        unset($user['password']);
        return $user;
    }
}
