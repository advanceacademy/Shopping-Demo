<?php

namespace App\Helpers;

use Throwable;
use App\Helpers\Registry;
use App\Helpers\Response;
use App\Helpers\View;
use App\Helpers\Router;
use ReflectionMethod;
use ReflectionFunction;
use Dotenv\Dotenv;

class Application
{
    protected $root = __DIR__;
    protected $router = null;

    public function __construct(string $root = __DIR__)
    {
        $this->router = new Router;
        $this->root = $root;
        $this->loadSettings();
    }

    /**
     * Run application
     */
    public function run()
    {
        $match = $this->router->match(rtrim($_SERVER['REQUEST_URI'], '/'));
        $target = $match['target'] ?? null;
        $output = null;
        $params = [];
        $arguments = [];
        $callback = null;

        if (!$target) {
            $output = (new Response())
                ->setCode(404)
                ->setContent(['message' => 'Not Found']);
        }

        try {
            if (is_string($target)) {
                $result = explode('@', $target);
                $controllerName = $result[0];
                $methodName = $result[1];
                $reflectionMethod = new ReflectionMethod($controllerName, $methodName);
                $params = $reflectionMethod->getParameters();
                $callback = [new $controllerName, $methodName];
            } elseif (is_callable($target)) {
                $reflectionMethod = new ReflectionFunction($target);
                $params = $reflectionMethod->getParameters();
                $callback = $target;
            }
        } catch (Throwable $e) {

            $output = (new Response())
                ->setCode(500)
                ->setContent(['message' => $e->getMessage()]);
        }

        foreach ($params as $param) {
            $paramName = $param->getName();

            $value = $match['params'][$paramName] ?? null;
            $type = $param->getType() ? $param->getType()->getName() : null;

            if ($type === 'int') {
                $value = (int) $value;
            } elseif ($type === 'string') {
                $value = (string) $value;
            } else {
                // skip casting
            }

            if ($value === null) {
                $arguments[$paramName] = $param->isDefaultValueAvailable()
                    ? $param->getDefaultValue()
                    : $value;
            } else {
                $arguments[$paramName] = $value;
            }
        }

        if ($callback) {
            try {
                $output = call_user_func_array($callback, $arguments);
            } catch (Throwable $e){
                $output = (new Response())
                    ->setCode(500)
                    ->setContent(['message' => $e->getMessage()]);
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Authorization, Content-Type, Accept, Origin");

        if (is_string($output)) {
            die($output);
        } else if (is_array($output)) {
            $output = (new Response())->setContent($output);
        }

        if ($output instanceof Response) {
            echo $output->getOutput();
        } else if ($output instanceof View) {
            echo $output->render();
        }

    }

    /**
     * Add new route
     *
     * @param string $method GET|POST|DELETE etc,
     * @param string $route Route Path
     * @param string $name Set name
     * @param array $routes List of routes
     */
    public function route(string $method = 'GET', string $route = '/', $target = null, string $name = null)
    {
        $this->router->map($method, $route, $target, $name);
        return $this;
    }

    /**
     * Add new Route Group
     *
     * @param string $prefix URL Prefix to all sub-routes
     * @param string $controller Set controller for all sub-routes
     * @param array $routes List of routes
     */
    public function routeGroup(string $prefix = '/', string $controller = '', array $routes = [])
    {
        $this->router->group([
                'prefix' => $prefix,
                'controller' => $controller,
            ], $routes);
        return $this;
    }

    protected function loadSettings()
    {
        $dotenv = Dotenv::createImmutable($this->root);
        $dotenv->safeLoad();

        // Load config files into Registry
        foreach (glob($this->root . '/Config/*.php') as $filePath) {
            $settingsPrefix = pathinfo($filePath, PATHINFO_FILENAME);
            $settings = include($filePath);

            foreach ($settings as $name => $value) {
                Registry::set("${settingsPrefix}.${name}", $value);
            }
        }
    }
}
