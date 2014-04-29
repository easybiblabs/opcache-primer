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
    private $env;

    /**
     * @var string
     */
    private $path;

    /**
     * @param string $env
     *
     * @return self
     */
    public function __construct($env)
    {
        $this->env = $env;
    }

    /**
     * @param $path
     *
     * @return int
     */
    public function setPath($path)
    {
        $path = realpath($path);
        if (empty($path)) {
            $this->error_out("Empty path parameter.");
        }

        $base = '/srv/www/easybib/releases';
        if ($this->env  == 'vagrant') {
            $base = '/vagrant_www';
        }

        if (strpos($base, $path) !== 0) {
            return $this->error_out("Incorrect path: {$path}");
        }

        if (!is_readable($path) || !is_dir($path)) {
            return $this->error_out("Could not open: {$path}");
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

    private function error_out($msg)
    {
        echo $msg . PHP_EOL;
        return 1;
    }
}
