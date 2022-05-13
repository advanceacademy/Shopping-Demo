<?php

namespace App\Helpers;

class View
{
    private $basePath;
    private $file = '';
    private $params = [];

    public function __construct($basePath)
    {
        $this->basePath = rtrim($basePath, '/') . '/';
    }

    public function use(string $file, array $params = [])
    {
        $this->file = $file;
        $this->params = $params;
        return $this;
    }

    public function render(int $httpCode = 200)
    {
        $errorReporting = ini_get("error_reporting");
        error_reporting($errorReporting & ~E_NOTICE);
        $output = $this->output($this->basePath . $this->file, $this->params);
        error_reporting($errorReporting);

        http_response_code($httpCode);
        header('Content-Type: text/html');

        return $output;
    }

    protected function output($___file, $___data)
    {
        extract($___data, EXTR_OVERWRITE);
        ob_start();
        include $___file;
        return ob_get_clean();
    }
}
