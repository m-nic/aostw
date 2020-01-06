<?php

define("PROTOCOL", isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : ((isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on") ? 'https' : 'http'));

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

    if (!file_exists($path)) {
        $path = getcwd() . '/config.json.example';
    }

    $app_config = json_decode(file_get_contents($path), true);
    return $app_config;
}

function get_url($port = null)
{
    $hostname = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'];
    $port = $port ?? $_SERVER['HTTP_X_FORWARDED_PORT'] ?? $_SERVER['SERVER_PORT'];

    $portPart = '';
    if ($port != 80) {
        $portPart = ":{$port}";
    }

    return trim(PROTOCOL . "://{$hostname}", ' /') . $portPart;
}

function get_uri()
{
    $baseUrl = get_url();
    $uri = trim($_SERVER['REQUEST_URI'], ' /');
    $actual_link = "{$baseUrl}/{$uri}";
    return $actual_link;
}

function web_base_path($path, $port = null)
{
    return get_url($port) . DIRECTORY_SEPARATOR . trim($path, ' /');
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
    return new class($instance) {
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

function prettify_JSON($jsonString)
{
    $decoded = json_decode($jsonString);

    if ($decoded) {
        return json_encode($decoded, JSON_PRETTY_PRINT);
    }

    return '';
}


function prettify_array_headers($headers)
{
    $out = [];

    foreach ($headers as $name => $values) {
        $out[] = (!is_numeric($name) ? "{$name}: " : '') . implode(',', $values);
    }

    return implode("\n", $out);
}

function isJsonSerializable($var)
{
    return is_array($var) ||
        is_object($var) &&
        get_class($var) === \stdClass::class;
}

function isSerializable($var)
{
    return is_object($var) && $var instanceof \Serializable;
}

function debug_dump(...$args)
{
    file_put_contents('debug', json_encode($args));
}

function formatGuzzleRequests($requestHistory)
{
    $requestsStack = [];
    foreach ($requestHistory as $entry) {
        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = $entry['request'];

        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $entry['response'];

        $apiMethod = $request->getUri()->getPath();
        $method = $request->getMethod();

        $requestHeaders = $request->getHeaders();
        $requestBody = $request->getBody();

        $requestHeaders = array_merge([
            ["{$method} {$apiMethod} HTTP " . $request->getProtocolVersion()]
        ], $requestHeaders);

        $responseHeaders = $response->getHeaders();
        $responseBody = $response->getBody();

        $requestsStack[] = [
            'method'   => $apiMethod,
            'request'  => [
                'headers' => prettify_array_headers($requestHeaders),
                'body'    => prettify_JSON($requestBody),
            ],
            'response' => [
                'headers' => prettify_array_headers($responseHeaders),
                'body'    => prettify_JSON($responseBody),
            ],
        ];
    }

    return $requestsStack;
}

function kebabCase($string)
{
    return strtolower(preg_replace('/([a-z]*+)[ -_]([a-z]*+)/i', '\1-\2', $string));
}

function runJsPrg(&$viewData)
{
    // @TODO Apply real PRG pattern and fix redirect issue from EIP http component
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        \App\Http\Session::put('viewData', $viewData);

        echo "<script>window.location.replace(window.location.href)</script>";
        exit;
    }

    if (\App\Http\Session::has('viewData')) {
        $viewData = \App\Http\Session::pull('viewData');
    }
}