<?php

namespace Helpers;

class Tools {

    public static function cc(...$vars) {
        foreach ($vars as $var) {
          if (isset($var) && !is_null($var) && !empty($var)) return $var;
        }
        return '';
    }

    public static function sanitize(&$variable, string $rule = 'general') {
      $rules = [
          'general' => '/[^a-zA-Z0-9]/',
          'username' => '/[^a-zA-Z0-9]/',
          'name' => '/[^a-zA-Z0-9\- ]/',
          'email'  => '/[^a-z0-9@\.\-]/',
          'title' => '/[^a-zA-Z0-9\.\-\_\:\& ]/'
      ];
      if (array_key_exists($rule, $rules)) {
          $variable = preg_replace($rules[$rule], '', $variable);
      } else {
          $variable = preg_replace($rules['handle'], '', $variable);
      }
  }

  public static function randomString(int $length = 32, ?int $negative = 0) : string {
    $characterSet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $id = '';
    if ($negative > 0) {
        $length = random_int($length - $negative, $length);
    }
    for ($i = 0; $i < $length; $i++) {
        $id .= substr($characterSet, random_int(0, strlen($characterSet)), 1);
    }
    return $id;
}

}