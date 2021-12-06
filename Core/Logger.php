<?php

namespace Obsidian\Core;

class Logger {

  /**
   * Logging function enabled
   *
   * @var integer
   */
  private static $loggerEnabled = 0;

  /**
   * Logging level
   * 0 - off
   * 1 - errors
   * 2 - information
   * 3 - debug
   *
   * @var integer
   */
  private static $logLevel = 0;

  /**
   * File to log to
   *
   * @var stirng
   */
  private static $logFile;

  /**
   * Microtime when logging started
   *
   * @var integer
   */
  private static $startTime;

  /**
   * Logging timeline
   *
   * @var array
   */
  private static $times = [];

  /**
   * Enabled debugging
   *
   * @param integer $logLevel
   * @param string $logFile
   * @return void
   */
  public static function enable(int $logLevel = 1, string $logFile = 'log.txt') {
    self::$loggerEnabled = 1;
    self::$logLevel = $logLevel;
    self::$logFile = $logFile;
  }

  /**
   * Disable logging
   *
   * @return void
   */
  public static function disable() {
    self::$loggerEnabled = 0;
  }

  /**
   * Start time logging
   *
   * @param string $title
   * @param float $timestamp
   * @return void
   */
  public static function start($title = 'Started', $timestamp = null) {
    self::$startTime = $timestamp ?? microtime(1);
    self::time($title, self::$startTime);
  }

  /**
   * Add new timestamp
   *
   * @param string $title
   * @param float $timestamp
   * @return void
   */
  public static function time($title = '', $timestamp = null) {
    $curtime = $timestamp ?? microtime(1);
    $prevtime = end(self::$times)['curtime'] ?? 0;
    self::$times[] = [
      'title'   => $title,
      'curtime' => $curtime,
      'abstime' => round( ($curtime - self::$startTime) * 1000 ,2 ),
      'reltime' => round( ($curtime - $prevtime) * 1000 ,2 ),
    ];
  }

  /**
   * Get last run time
   *
   * @param boolean $relative true for relative time, false for absolute time
   * @return void
   */
  public static function last($relative = true) {
    $title = end(self::$times)['title'];
    $last = ($relative) ? end(self::$times)['reltime'] : end(self::$times)['abstime'];
    return "$title - {$last}ms";
  }

  /**
   * Get all run times
   *
   * @return array
   */
  public static function getTimes() {
    return self::$times;
  }

  /**
   * Log error message
   *
   * @param string $message
   * @return void
   */
  public static function error(string $message) {
    if (self::$logLevel >= 1) {
      return self::writeLog('error',$message);
    }
  }

  /**
   * Log info message
   *
   * @param string $message
   * @return void
   */
  public static function info(string $message) {
    if (self::$logLevel >= 2) {
      return self::writeLog('info',$message);
    }
  }

  /**
   * Log debug message
   *
   * @param string $message
   * @return void
   */
  public static function debug(string $message) {
    if (self::$logLevel >= 3) {
      return self::writeLog('debug',$message);
    }
  }

  /**
   * Write log message to logfile
   *
   * @param string $level
   * @param string $message
   * @return void
   */
  private static function writeLog(string $level, string $message) {
    $format = "[%s] - %s - %s (%s)\r\n";
    if (self::$loggerEnabled) {
      $date = date('Y-m-d h:i:s');
      $level = strtoupper($level);
      $backtrace = debug_backtrace()[2];
      $trace = $backtrace['class'].$backtrace['type'].$backtrace['function'].':'.$backtrace['line'];
      $fh = @fopen(self::$logFile, 'a+');
      $logMessage = sprintf($format, $date, $level, $message, $trace);
      fwrite($fh, $logMessage);
      fclose($fh);
      return $message;
    }
    return false;
  }

}