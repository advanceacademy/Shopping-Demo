<?php

namespace App\Helpers;

use Rakit\Validation\ErrorBag;
use Rakit\Validation\Validator;

class Request {
    protected $vars = [];

    public function __construct()
    {
        $this->vars = json_decode(file_get_contents('php://input'), true);
    }

    public function post($key = null)
    {
        if (json_last_error() === 0) {
            return $this->vars[$key] ?? '';
        } else {
            return $this->vars;
        }
    }

    public function getErrorsByField(ErrorBag $errors)
    {
        $errorsList = $errors->toArray();

        $errorsOutput = [];

        foreach ($errorsList as $field => $data) {
            $errorsOutput[$field] = [];

            foreach ($data as $validationRule => $message) {
                $errorsOutput[$field][] = $message;
            }
        }
        return $errorsOutput;
    }

    public function getValidator()
    {
        $validator = new Validator([
            'required' => 'Въвеждането на стойност в полето :attribute е задължително.',
            'email:required' => 'Въвеждането на стойност в полето :attribute е задължително.',
            'email:email' => 'Въведената стойност на :attribute не е валиден e-mail адрес.',
            'max' => 'Максималната дължина на полето :attribute е :max символа.',
            'min' => 'Минималната дължина на полето :attribute е :min символа.',
            'regex' =>  "Въведената стойност в полето :attribute не е валиден формат.",
        ]);

        return $validator;
    }
}
