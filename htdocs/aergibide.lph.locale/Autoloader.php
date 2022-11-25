<?php

//Class autoloader

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    public static function autoload($class): void
    {
            $class = str_replace('\\', '/', $class);
            require_once APP_ROOT . $class . '.php';
    }
}

Autoloader::register();