<?php

namespace App\Traits;

trait CommonHttpErrorsTrait
{
    public function errorBadRequest(array $errors = [], string $message = 'Възникнаха грешки в подадените данни.')
    {
        return $this->response
            ->setCode(400)
            ->setContent([
                "message" => $message,
                "errors" => $errors
            ]);
    }

    public function errorUnauthorized(string $message = 'Неудостоверен достъп')
    {
        return $this->response
            ->setCode(401)
            ->setContent(["message" => $message]);
    }

    public function errorForbidden(string $message = 'Забранен ресурс')
    {
        return $this->response
            ->setCode(403)
            ->setContent(["message" => $message]);
    }

    public function errorNotFound(string $message = 'Ресурсът не е намерен')
    {
        return $this->response
            ->setCode(404)
            ->setContent(["message" => $message]);
    }

    public function errorInternalServerError(string $message = 'Вътрешна сървърна грешка')
    {
        return $this->response
            ->setCode(500)
            ->setContent(["message" => $message]);
    }
}
