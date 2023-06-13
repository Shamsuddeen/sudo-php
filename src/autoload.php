<?php

/**
 * Autoloads SudoAfrica classes
 * For use when library is being used without composer
 */

$sudo_autoloader = function ($class_name) {
    if (strpos($class_name, 'SudoAfrica\Sudo')===0) {
        $file = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $file .= str_replace([ 'SudoAfrica\\', '\\' ], ['', DIRECTORY_SEPARATOR ], $class_name) . '.php';
        include_once $file;
    }
};

spl_autoload_register($sudo_autoloader);

return $sudo_autoloader;