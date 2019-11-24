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

function get_full_url()
{
    $config = app_config();
    return "{$config['site']['protocol']}://{$config['site']['host']}";
}