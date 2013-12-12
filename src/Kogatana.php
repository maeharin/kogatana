<?php

namespace Kogatana;

class Kogatana
{
    public static function autoload($class_name)
    {
        $load_dirs = array(
            __DIR__ . '/',
        );

        $parts = explode('\\', $class_name);
        $class_name = end($parts);

        foreach($load_dirs as $load_dir) {
            $file_name = $load_dir . $class_name . '.php';

            if (is_readable($file_name)) {
                require $file_name;
                return;
            }
        }
    }

    public static function registerAutoloader()
    {
        spl_autoload_register("\Kogatana\Kogatana::autoload");
    }
}

\Kogatana\Kogatana::registerAutoloader();
