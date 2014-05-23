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
        $this->logInfo("Base: {$base}");
    }

    /**
     * @param $path
     *
     * @return int
     */
    public function setPath($path)
    {
        if (empty($path)) {
            return $this->logError("Empty path parameter.");
        }

        if (!is_readable($path)) {
            return $this->logError("Could not open: {$path}");
        }

        if (!is_dir($path)) {
            return $this->logError("Path is not a directory: {$path}");
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
            $this->logInfo("Priming {$file}.");
            if (@opcache_compile_file($file)) {
                $this->logInfo("Success!");
                continue;
            }
            // ignore errors
            $this->logError("Could not compile: {$file}");
        }

        return $return;
    }

    /**
     * @return int
     */
    public function validate()
    {
        if (strpos($this->path, $this->base) !== 0) {
            return $this->logError("Incorrect path: {$this->path}");
        }
        return 0;
    }

    private function logInfo($msg)
    {
        if (null === $this->logger) {
            return;
        }
        $this->logger->info($msg);
    }

    private function logError($msg)
    {
        if (null === $this->logger) {
            // silence is golden
            return 1;
        }

        $this->logger->error($msg);
        return 1;
    }
}
