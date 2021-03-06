<?php

function autoload($class) {
    $path = str_replace(['Obsidian\\','\\'],['','/'], $class) . '.php';
    if(file_exists(__DIR__ .'/'.$path)) {
        require_once(__DIR__ .'/'.$path);
    }
}

spl_autoload_register('autoload');
