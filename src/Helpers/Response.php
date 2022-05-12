<?php

namespace App\Helpers;

class Response
{
    public $content;
    public $code = 200;

    public function __construct()
    {
    }

    public function setContent(array $content = [])
    {
        $this->content = $content;
        return $this;
    }

    public function setCode(int $code = 200)
    {
        $this->code = $code;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getOutput()
    {
        http_response_code($this->code);
        header('Content-Type: application/json');
        return $this->content ? json_encode($this->content) : '';
    }
}
