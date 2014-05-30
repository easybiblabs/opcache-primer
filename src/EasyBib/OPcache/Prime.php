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

use Psr\Log\LoggerInterface;

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
    private $base;

    /**
     * @var string
     */
    private $cacheFile;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $path;

    /**
     * @param string          $base
     * @param LoggerInterface $logger
     *
     * @return self
     */
    public function __construct($base, LoggerInterface $logger = null)
    {
        $this->base = $base;
        $this->logger = $logger;
        $this->log("Base: {$base}", 'debug');
    }

    /**
     * This overrides the usage on {@link self::getFilesFromAutoload()}.
     *
     * @param string $path
     *
     * @return int
     */
    public function setCacheFile($path)
    {
        if (!file_exists($path)) {
            return $this->log("Path does not exist: {$path}", 'error');
        }

        $this->cacheFile = $path;
        return 0;
    }

    /**
     * @param $path
     *
     * @return int
     */
    public function setPath($path)
    {
        if (empty($path)) {
            return $this->log("Empty path parameter.", 'error');
        }

        if (!is_readable($path)) {
            return $this->log("Could not open: {$path}", 'error');
        }

        if (!is_dir($path)) {
            return $this->log("Path is not a directory: {$path}", 'error');
        }

        $this->log("Path: {$path}", 'debug');

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

        if (null === $this->cacheFile) {
            $files = $this->getFilesFromAutoload();
        } else {
            $files = array_unique(explode("\n", file_get_contents($this->cacheFile)));
        }

        foreach ($files as $file) {
            if (empty($file)) {
                continue;
            }

            $this->log("Priming {$file}.", 'info');
            if (@opcache_compile_file($file)) {
                $this->log("Success!", 'info');
                continue;
            }

            // ignore errors
            $this->log("Could not compile: {$file}", 'error');
        }

        return $return;
    }

    /**
     * @return int
     */
    public function validate()
    {
        if (strpos($this->path, $this->base) !== 0) {
            return $this->log("Incorrect path: {$this->path}", 'error');
        }
        return 0;
    }

    private function getFilesFromAutoload()
    {
        $files = array_unique(require $this->path . '/vendor/composer/autoload_classmap.php');
        return $files;
    }

    private function log($msg, $level = 'debug')
    {
        $returnCode = 0;
        if ('error' === $level) {
            $returnCode = 1;
        }


        if (null === $this->logger) {
            return $returnCode;
        }
        $this->logger->$level($msg);

        return $returnCode;
    }
}
