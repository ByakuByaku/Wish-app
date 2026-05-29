<?php

require_once __DIR__ . '/../../utils/Logger.php';
require_once __DIR__ . '/Response.php';

class Router
{
    private $routes = [];

    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }
    public function put($path, $callback)
    {
        $this->routes['PUT'][$path] = $callback;
    }
    public function delete($path, $callback)
    {
        $this->routes['DELETE'][$path] = $callback;
    }
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        Logger::info('Request', [
        'method' => $method,
        'uri'    => $url,
        'ip'     => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]);

        $apiPrefix = '/api/v1';
        $pos = strpos($url, $apiPrefix);
        if ($pos !== false) {
            $path = substr($url, $pos + strlen($apiPrefix));
        } else {
            $path = $url;
        }

        if ($path === '' || $path === false) {
            $path = '/';
        }

        if (isset($this->routes[$method][$path])) {
            call_user_func($this->routes[$method][$path]);
            return;
        }

        foreach ($this->routes[$method] ?? [] as $pattern => $callback) {
            if (preg_match("#^" . $pattern . "$#", $path, $matches)) {
                call_user_func($callback, $matches);
                return;
            }
        }
        Logger::error('Endpoint not found', [
            'method' => $method,
            'path'    => $path,
        ]);
        Response::error('Endpoint not found', [], 404);
}
}


?>