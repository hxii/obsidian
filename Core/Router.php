<?php

namespace Obsidian\Core;

class Router {

  public static $routes = [];

  public static $options;

  public static $queryparams;

  public static $current_query;

  /**
   * Add new route to the list
   *
   * @param string $method
   * @param string $route
   * @param callable $callback
   * @param integer|null $responseCode
   * @return bool TRUE if route added successfully, FALSE if it already exists.
   */
  public static function add(string $method, string $route, $callback, ?int $responseCode = 200) {
    if (isset(self::$routes[$method.$route])) return false;
    self::$routes[$method.$route] = [
      'method'   => $method,
      'name'     => $route,
      'route'    => self::parseParams($route),
      'callback' => $callback,
      'code'     => $responseCode,
    ];
    return true;
  }

  /**
   * Execute router against added routes
   *
   * @return void
   */
  public static function execute(?string $request = null) {
    $request = $request ?? $_SERVER['REQUEST_URI'];
    $query = $_SERVER['QUERY_STRING'] ?? '';
    parse_str($query, self::$queryparams);
    $requestURI = str_replace("?$query", '', $request);
    foreach (self::$routes as $route) {
      $pattern = "#^{$route['route']}";
      $pattern .= (isset(self::$options) && in_array('trailing', self::$options)) ? '/?$#' : '$#';
      if (preg_match($pattern, $requestURI, $params)) {
        self::$current_query = [
          'post'    => $_POST,
          'get'     => $_GET,
          'route'   => $route,
          'referer' => $_SERVER['HTTP_REFERER'] ?? '',
          // 'current' => ROOT_URL . $_SERVER['REQUEST_URI'],
          'current' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
        ];
        if ($_SERVER['REQUEST_METHOD'] !== $route['method']) continue;
        $params = array_filter($params, 'is_string', ARRAY_FILTER_USE_KEY);
        http_response_code($route['code']);
        \call_user_func_array($route['callback'], $params);
        return;
      }
    }
    self::invalidRoute();
  }

  /**
   * Redirect to other route
   *
   * @param string $route
   * @return void
   */
  public static function redirect($route, int $status = 200) {
    header('Location: ' . $route, 1, $status);
    return http_response_code($status);
  }

  public static function currentRoute() {
    return (object) self::$current_query;
  }

  /**
   * Parse dynamic route parameters
   *
   * @param string $route
   * @return string
   */
  private static function parseParams(string $route) {
    return str_replace(['{','}'], ['(?<','>[a-zA-Z0-9\-]+)'], $route);
  }

  /**
   * Remove domain from URL string
   *
   * @param string $url
   * @return string
   */
  public static function removeDomain(string $url) {
    return preg_replace('#^https?:\/\/[^/]+#', '', $url);
  }

  /**
   * Invalid route error (404)
   *
   * @return void
   */
  private static function invalidRoute() {
    echo 'Invalid Route';
    die(http_response_code(404));
  }

  /**
   * Invalid method error (405)
   *
   * @return void
   */
  private static function invalidMethod() {
    echo 'Invalid Method';
    die(http_response_code(405));
  }

  public static function options(array $options) {
    self::$options = $options;
  }

}

