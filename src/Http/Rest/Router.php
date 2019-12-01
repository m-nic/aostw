<?php

namespace App\Http\Rest;

class Router
{
    const TYPE_STATIC = 'static';
    const TYPE_DYNAMIC = 'dynamic';

    private static $routesMap = [
        'static'  => [],
        'dynamic' => [],
    ];

    private static $containerMap = [];

    public static function useContainer($containerMap)
    {
        self::$containerMap = $containerMap;
    }

    public static function get($path, $closure)
    {
        self::request('get', $path, $closure);
    }

    public static function post($path, $closure)
    {
        self::request('post', $path, $closure);
    }

    public static function put($path, $closure)
    {
        self::request('put', $path, $closure);
    }

    public static function delete($path, $closure)
    {
        self::request('delete', $path, $closure);
    }

    public static function request($method, $path, $closure)
    {
        if (is_callable($closure)) {
            self::addToMap($method, $path, self::wrapClosure($closure));
            return;
        } elseif (is_string($closure)) {
            self::addToMap($method, $path, self::wrapMethodCall($closure));
            return;
        }

        throw new \Exception('The route format is not valid');
    }

    public static function handleRequests($prefix = '')
    {
        try {
            $requestedRoute = strtok($_SERVER['REQUEST_URI'], '?');
            $requestedRoute = str_replace('/' . trim($prefix, '/'), '', $requestedRoute);

            self::callMethod(
                strtolower($_SERVER['REQUEST_METHOD']),
                self::cleanRoutePath($requestedRoute)
            );
        } catch (\Exception $e) {
            ResponseWriter::of(Response::of($e))->write();
        }
    }

    private static function callMethod($method, $requestedRoute)
    {
        $routeKey = self::getRouteKey($method, $requestedRoute);

        if (self::isStaticRoute($routeKey)) {
            return self::handleStaticRoute($routeKey);
        } else {
            return self::handleDynamicRoute($method, $requestedRoute);
        }

        throw new \Exception('The called route does not exist');
    }

    private static function getRouteKey($method, $path)
    {
        return strtolower($method) . "-" . self::cleanRoutePath($path);
    }

    private static function prepareDynamicRoute($route)
    {
        return preg_replace('/\\\{.+?\\\}/i', '(.*)', preg_quote($route));
    }

    private static function cleanRoutePath($path)
    {
        return trim($path, '/');
    }

    private static function addToMap($method, $path, $fn)
    {
        $config = [
            'fn'     => $fn,
            'method' => $method,
        ];

        $dynamicPath = self::prepareDynamicRoute($path);
        $type = self::TYPE_STATIC;

        if ($dynamicPath !== $path) {
            $type = self::TYPE_DYNAMIC;

            $config['regex'] = str_replace('/', '\/', self::cleanRoutePath($dynamicPath));
        }

        $routeKey = self::getRouteKey($method, $path);

        self::$routesMap[$type][$routeKey] = $config;
    }

    private static function wrapClosure($closure)
    {
        return function ($urlParams = []) use ($closure) {
            $callbackResponse = $closure(Request::make(), ...$urlParams);

            ResponseWriter::of(
                Response::of($callbackResponse)
            )->write();
        };
    }


    private static function wrapMethodCall($closure)
    {
        list($class, $method) = explode('@', $closure);

        return function ($urlParams = []) use ($class, $method) {
            $dependencies = self::$containerMap[$class] ?? [];

            $instance = new $class(...$dependencies);
            $methodResponse = $instance->{$method}(...$urlParams);

            ResponseWriter::of(
                Response::of($methodResponse)
            )->write();
        };
    }

    private static function isStaticRoute($routeKey)
    {
        return array_key_exists($routeKey, self::$routesMap[self::TYPE_STATIC]);
    }

    private static function handleStaticRoute($routeKey)
    {
        return (self::$routesMap[self::TYPE_STATIC][$routeKey]['fn'])();
    }

    private static function handleDynamicRoute($method, $requestedRoute)
    {
        $dynamicRoutes = self::$routesMap[self::TYPE_DYNAMIC] ?? [];

        foreach ($dynamicRoutes as $config) {
            if (
                preg_match("/{$config['regex']}/i", $requestedRoute, $matches) &&
                $config['method'] === $method
            ) {
                $params = array_slice($matches, 1) ?? [];
                return ($config['fn'])($params);
            }
        }
    }
}