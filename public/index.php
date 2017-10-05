<?php

function __autoload($classname) {
    $filename = "./../app/class/". $classname .".php";
    if (file_exists($filename)) {
        include_once($filename);
    }
}

$app = new App();

var_dump($app);
