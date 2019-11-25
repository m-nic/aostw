<?php

define("PROTOCOL", isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : ((isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) ? 'https' : 'http'));

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
    return trim(PROTOCOL . "://{$_SERVER['HTTP_HOST']}", ' /');
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

function get_db_query($file_name)
{
    $currentDir = getcwd();

    $filename = "{$currentDir}/src/queries/{$file_name}.sql";

    if (file_exists($filename)) {
        return file_get_contents($filename);
    }
}

function makeProxyClass($instance)
{
    return new class($instance)
    {
        public function __construct($client)
        {
            $this->client = $client;
        }

        public function __call($name, $arrayOfArgs)
        {
            $args = [];

            foreach ($arrayOfArgs as $arg) {
                $args = array_merge($args, array_values($arg));
            }

            return $this->client->{$name}(...$args);
        }
    };
}

function convertSoapArray($array = [])
{
    $array = (array)($array ?? []);
    $firstEl = $array[0] ?? null;

    if (!empty($firstEl)) {
        if (
            isset($firstEl->key) &&
            isset($firstEl->value)
        ) {
            $out = [];
            foreach ($array as $line) {
                $out[$line->key] = $line->value;
            }

            return $out;
        }
    }

    return $array;
}

function convertSoapArrayCollection($array)
{
    return array_map(function ($row) {
        return convertSoapArray($row->item);
    }, $array);
}


function print_json($data)
{
    echo json_encode($data);
}

function prettify_XML($xml)
{
    $dom = new DOMDocument();

    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;

    $dom->loadXML($xml);
    $out = $dom->saveXML();
    return $out;
}