<?php

/**
 * Class Cookie
 */
class Cookie
{

    public static function setCookie($name, $value)
    {
        setcookie($name, $value, time() + 3600);
    }

    public static function getCookie($name)
    {
        return $_COOKIE[$name];
    }

}
