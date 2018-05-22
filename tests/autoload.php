<?php

require __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function ($class) {
    if (0 === strpos($class, 'Runn\\')) {
        $sourceFile = __DIR__ . '/../src/' . str_replace('\\', '/', substr($class, 5)) . '.php';
        $testFile = __DIR__ . '/../' . str_replace('\\', '/', substr($class, 5)) . '.php';
        if (is_readable($sourceFile)) {
            require $sourceFile;
        } elseif (is_readable($testFile)) {
            require $testFile;
        }
    }
});
