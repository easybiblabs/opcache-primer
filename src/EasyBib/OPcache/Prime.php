<?php
/**
 * PHP Version 5.5
 *
 * @category Cache
 * @package  EasyBib\OPcache
 * @author   Till Klampaeckel <till@lagged.biz>
 * @license  http://www.easybib.com/company/terms Terms of Service
 * @link     http://www.easybib.com
 */
namespace EasyBib\OPcache;

/**
 * EasyBib\OPcache\Prime
 *
 * @category Cache
 * @package  EasyBib\OPcache
 * @author   Till Klampaeckel <till@lagged.biz>
 * @license  http://www.easybib.com/company/terms Terms of Service
 * @link     http://www.easybib.com
 */
class Prime
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $base
     *
     * @return self
     */
    public function __construct($base)
    {
        $this->base = $base;
    }

    /**
     * @param $path
     *
     * @return int
     */
    public function setPath($path)
    {
        if (empty($path)) {
            return $this->error_out("Empty path parameter.");
        }

        if (!is_readable($path)) {
            return $this->error_out("Could not open: {$path}");
        }

        if (!is_dir($path)) {
            return $this->error_out("Path is not a directory: {$path}");
        }

        $this->path = $path;
        return 0;
    }

    /**
     * Uses composer's classmap to prime all files.
     *
     * @return int
     */
    public function doPopulate()
    {
        $return = 0;

        $files = array_unique(require $this->path . '/vendor/composer/autoload_classmap.php');
        foreach ($files as $file) {
            if (@opcache_compile_file($file)) {
                continue;
            }
            // ignore errors
            //return $this->error_out("Could not compile: {$file}");
        }

        return $return;
    }

    public function validate()
    {
        if (strpos($this->path, $this->base) !== 0) {
            return $this->error_out("Incorrect path: {$this->path}");
        }
        return 0;
    }

    private function error_out($msg)
    {
        echo $msg . PHP_EOL;
        return 1;
    }
}
