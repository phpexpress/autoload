<?php

/**
 * autoloader library will loads classes quickly
 * @version 1.0.0
 * @author Shahzada Modassir <codingmodassir@gmail.com>
 * @license MIT
 * @see https://github.com/phpexpress
 * Date: 05 June 2024 AT 02:51 PM
 */

declare(strict_types=1);

/**
 * Create External API intance Method
 * 
 * @param string {$namespace} A valid class or namespace
 */
function autoload(String $namespace)
{
  Autoloader::load($namespace);
}

/**
 * 
 */
spl_autoload_register('autoload');

final class Autoloader
{

  /**
   * 
   * 
   * @var rnamespace private property
   */
  private const rnamespace = '/namespace\s*((\w+\\\*)+)/';

  /**
   * 
   * 
   * @var $class private property
   */
  private static $class;

  /**
   * 
   * @var rslash private property
   */
  private const rslash = '/^\\\/';

  /**
   * 
   * 
   * @var null $namespace static property
   */
  private static $namespace;

  /**
   * 
   * 
   * @var  array $Files static property
   */
  private static $Files = [];

  /**
   * 
   */
  private static function quickLoad() : void
  {
    foreach(self::$Files as $File)
    {
      $data = file_get_contents($File);
      preg_match_all(self::rnamespace, $data, $matched);

      $namespace = $matched[1];

      if (empty($namespace))
      {
        $fpattern = '/'.self::$class.'\\.php$/';
        if (preg_match($fpattern, $File))
        {
          require $File;
          break;
        }
        continue;
      }

      if (in_array(self::$namespace,  $namespace))
      {
        require $File;
        break;
      }
    }
  }

  /**
   * Original Method
   * Quick Loader
   * load method will load all class with namespace
   * 
   * @param string {$namespace} A valid proper namespace or class
   */
  static function load(String $namespace) : void
  {
    self::extract($namespace);
    self::getAllFiles($_SERVER['DOCUMENT_ROOT'], self::$Files);
    self::quickLoad();
  }

  /**
   * extract method will extract namespace and class
   * 
   * @param string {$namespace} A valid proper namespace
   */
  private static function extract(String $namespace) : void
  {
    $nsArray = explode('\\', $namespace);
    self::$class = array_pop($nsArray);
    self::$namespace = self::format(implode('\\', $nsArray));
  }

  /**
   * format method will arrange namespace with format
   * 
   * @param string {$namespace} A valid proper namespace
   */
  private static function format(String $namespace) : String
  {
    return preg_replace(self::rslash, '', $namespace);
  }

  /**
   * getAllFiles method will get All files in specific dir
   * 
   * @param string {$dest} Destination of folder location
   * @param array  {$tmp} tmp support array
   */
  private static function getAllFiles(String $dest, array $tmp) : void
  {
    foreach(glob($dest . '/*') as $source)
    {
      is_dir($source) ? self::getAllFiles($source, self::$Files) : array_push(self::$Files, $source);
    }
  }
}
?>