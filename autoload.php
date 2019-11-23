<?php
declare(strict_types=1);

/**
 * PSR-4 compliant autoloader.
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
 spl_autoload_register(function($className) {
     $baseDir = __DIR__ . '/src/';
     $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
     $file = $baseDir . $className . '.php';

     if (file_exists($file)) {
         require $file;
     }
 });
