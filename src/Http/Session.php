<?php

namespace App\Http;

class Session
{
    public static function enable()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function has($key)
    {
        return !empty($_SESSION['app-data'][$key]);
    }

    public static function put($key, $value)
    {
        if (!$_SESSION['app-data']) {
            $_SESSION['app-data'] = [];
        }

        $_SESSION['app-data'][$key] = $value;
    }

    public static function pull($key)
    {
        $values = $_SESSION['app-data'][$key];
        unset($_SESSION['app-data'][$key]);
        return $values;
    }
}
