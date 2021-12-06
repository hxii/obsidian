<?php

namespace Obsidian\Core;

use \Obsidian\Core\Logger;
use \Obsidian\Core\Configuration;

class Template {

  /**
   * How long to keep templates compiled
   *
   * @var string
   */
  public static $cacheTime = '1440';

  /**
   * The current active template
   *
   * @var string
   */
  private static $activeTemplate;

  /**
   * Template blocks
   *
   * @var array
   */
  private static $blocks = [];
  
  /**
   * Regex template block patterns
   *
   * @var array
   */
  private static $patterns = [
    'include' => '@{%\s?(?:extends|include)\s(.+?)\s?%}@i',
    'block'   => '@{%\s?block\s+(.+?)\s%}(.+?){%\s?endblock\s?%}@si',
    'yield'   => '@{%\s?yield\s(.+?)\s?%}@i',
    'php'     => '@{%\s?(.+?)\s?%}@i',
    'echo'    => '@{{\s?(.+?)\s?}}@i'
  ];

  /**
   * View/render template with the given $data
   *
   * @param string $template template filename
   * @param array $data
   * @return void
   */
  public static function view(string $template, array $data = []) {
    Logger::debug("Template $template requested");
    Logger::time("Started loading template $template");
    if (!Configuration::get('template','dir')) {
      Logger::error('Please configure Configuration => template => dir');
      die('Please configure Configuration => template => dir');
    }
    self::$activeTemplate = Configuration::get('template','dir').'/'.$template;
    $content = self::includes($template);
    $content = self::compile($content);
    $req = self::saveCompiledFile($template, $content);
    unset($content);
    extract($data, EXTR_PREFIX_SAME, 'data');
    require $req;
    Logger::time("Finished loading template $template");
    Logger::debug(Logger::last());
  }

  /**
   * Parse all the includes in the template
   *
   * @param string $filename
   * @return string
   */
  private static function includes(string $filename) : string {
    Logger::debug("Loading template $filename");
    $data = @file_get_contents(Configuration::get('template','dir').'/'.$filename);
    if (!$data) {
      Logger::error("Unable to load template $filename");
      die("Unable to load template $filename");
    }
    preg_match_all(self::$patterns['include'], $data, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
      Logger::debug("Including $match[1]");
      $data = str_replace($match[0], self::includes($match[1]), $data);
    }
    return $data;
  }

  /**
   * Compile all blocks
   *
   * @param string $data
   * @return string
   */
  private static function compile(string $data) : string {
    $data = self::compileEchoes($data);
    $data = self::compileBlocks($data);
    $data = self::compileYields($data);
    $data = self::compilePHP($data);
    return $data;
  }

  /**
   * Compile all echoes
   *
   * @param string $data
   * @return string
   */
  private static function compileEchoes($data) : string {
    Logger::debug('Compiling echoes');
    return preg_replace(self::$patterns['echo'], '<?php echo $1 ?? \'\' ?>', $data);
  }

  /**
   * Compile all block yields
   *
   * @param string $data
   * @return string
   */
  private static function compileYields($data) : string {
    Logger::debug('Compiling yields');
    return preg_replace_callback(self::$patterns['yield'], function($matches) {
      return self::$blocks[$matches[1]] ?? '';
    }, $data);
  }

  /**
   * Compile all blocks (add them to self::$blocks)
   *
   * @param string $data
   * @return string
   */
  private static function compileBlocks($data) : string {
    Logger::debug('Compiling blocks');
    preg_match_all(self::$patterns['block'], $data, $blocks, PREG_SET_ORDER);
    foreach ($blocks as $block) {
      self::$blocks[$block[1]] = $block[2];
      $data = str_replace($block[0], '', $data);
    }
    return $data;
  }

  /**
   * Compile PHP code
   *
   * @param string $data
   * @return string
   */
  private static function compilePHP($data) : string {
    Logger::debug('Compiling PHP');
    return preg_replace(self::$patterns['php'], '<?php $1 ?>', $data);
  }

  /**
   * Save compiled template and return it's path
   *
   * @param string $filename
   * @param string $content
   * @return string
   */
  private static function saveCompiledFile($filename, $content) : string {
    if (!Configuration::get('template','compiled')) {
      Logger::error('Please configure Configuration => template => compiled');
      die('Please configure Configuration => template => compiled');
    }
    if (!file_exists(Configuration::get('template','compiled'))) {
      Logger::error('Compiled directory ' . Configuration::get('template','compiled') . ' doesn\'t exist. Creating.');
      mkdir(Configuration::get('template','compiled'), 0777);
    }
    if (!Configuration::get('template','security')) {
      Logger::error('Please configure Configuration => template => security');
      die('Please configure Configuration => template => security');
    }
    $header     = '<?php defined("' . Configuration::get('template','security') . '") || die("Invalid access"); ?>';
    $filepath   = Configuration::get('template','compiled') . '/' . str_replace('.html', '.php', $filename);
    $c_modified = @filemtime($filepath);
    $t_modified = filemtime(self::$activeTemplate);
    if (defined('TEMPLATE_NOCACHE') || $t_modified > $c_modified || microtime(1) > ($c_modified + self::$cacheTime)) {
      Logger::debug("Saving compiled file $filepath");
      $write = @file_put_contents($filepath, $header . PHP_EOL . $content);
      if (!$write) {
        Logger::error("Failed writing $filepath");
      }
    }
    Logger::debug("Serving $filepath");
    return $filepath;
  }

}