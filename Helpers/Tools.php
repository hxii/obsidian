<?php

namespace Obsidian\Helpers;

class Tools {

    public static function cc(...$vars) {
        foreach ($vars as $var) {
          if (isset($var) && !is_null($var) && !empty($var)) return $var;
        }
        return '';
    }

}