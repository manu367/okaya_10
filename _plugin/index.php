<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path   = str_replace('.php', '', rtrim($path, '/'));
$query  = $_GET;

function GetMapping($route, $callback) {
    global $method, $path, $query;

    [$routePath, $routeQuery] = array_pad(explode('?', $route, 2), 2, null);
    $routePath = '/' . ltrim($routePath, '/');

    if ($method !== 'GET') return;
    if ($path !== $routePath) return;
    if ($routeQuery && !array_key_exists($routeQuery, $query)) return;

    $callback($_REQUEST);
    exit;
}

function index($req) {
    echo json_encode([
        "status" => "success",
        "data" => $req
    ]);
}

GetMapping("index.php?op", function ($req) {
    return index($req);
});

http_response_code(404);
echo json_encode(["error" => "No routes matched"]);
