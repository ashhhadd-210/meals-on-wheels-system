<?php
// Local-dev router for PHP's built-in web server.
// Emulates the IIS clean-URL behavior the app expects (e.g. /login -> /login/index.php).
$root = __DIR__;
$uri  = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$path = $root . $uri;

// 1) Existing real file (css, js, images, explicit .php) -> let the server handle it.
if ($uri !== '/' && file_exists($path) && !is_dir($path)) {
    return false;
}

// 2) Directory request -> its index.php (handles /, /login, /logout, /viewReport, ...).
$candidate = is_dir($path)
    ? rtrim($path, '/') . '/index.php'
    : null;
if ($candidate && file_exists($candidate)) {
    require $candidate;
    return true;
}

// 3) Extensionless path -> /<path>/index.php
$idx = $root . rtrim($uri, '/') . '/index.php';
if (file_exists($idx)) {
    require $idx;
    return true;
}

// 4) Fallback to the front controller.
require $root . '/index.php';
