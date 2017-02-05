<?php

if (!class_exists('PHPUnit_Framework_TestCase')) {
    class_alias('\\PHPUnit\\Framework\\TestCase', 'PHPUnit_Framework_TestCase');
}

require_once(
    __DIR__ .
    DIRECTORY_SEPARATOR . '..' .
    DIRECTORY_SEPARATOR . 'vendor' .
    DIRECTORY_SEPARATOR . 'erebot' .
    DIRECTORY_SEPARATOR . 'testenv' .
    DIRECTORY_SEPARATOR . 'bootstrap.php'
);
