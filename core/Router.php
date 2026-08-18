<?php
class Router {
    private array $routes = [];
    public function add(string $method, string $uri, string $action): void {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }
    public function dispatch(string $uri, string $method): void {
        $parsedUri = parse_url($uri, PHP_URL_PATH);
        if ($parsedUri !== '/') {
            $parsedUri = rtrim($parsedUri, '/');
        }
        if ($method === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!CSRF::validate($token)) {
                http_response_code(403);
                die("CSRF Token Validation Failed");
            }
        }
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['uri'] === $parsedUri) {
                [$controller, $methodName] = explode('@', $route['action']);
                require_once APP_DIR . '/Controllers/' . $controller . '.php';
                $controllerInstance = new $controller();
                $controllerInstance->$methodName();
                return;
            }
        }
        http_response_code(404);
        echo "404 Not Found";
    }
}
