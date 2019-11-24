<?php

function dd(...$args)
{
    foreach ($args as $arg) {
        echo "<pre>";
        var_dump($arg);
        echo "</pre><br>";
    }
    exit;
}

function app_config()
{
    $path = getcwd() . '/config.json';
    $app_config = json_decode(file_get_contents($path), true);
    return $app_config;
}

function get_url()
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    return trim("{$protocol}://{$_SERVER['HTTP_HOST']}", ' /');
}

function get_uri()
{
    $baseUrl = get_url();
    $uri = trim($_SERVER['REQUEST_URI'], ' /');
    $actual_link = "{$baseUrl}/{$uri}";
    return $actual_link;
}

function web_base_path($path)
{
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    $currentDir = getcwd();

    $base_path = str_replace($documentRoot, '', $currentDir);
    $full_path = $base_path . DIRECTORY_SEPARATOR . trim($path, ' /');

    return get_url() . $full_path;
}