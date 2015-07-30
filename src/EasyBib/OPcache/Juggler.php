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
 * EasyBib\OPcache\Juggler
 *
 * Attempts to rotate the opcache to avoid overflow and hard reset.
 *
 * Inspiration:
 *  - https://tideways.io/profiler/blog/fine-tune-your-opcache-configuration-to-avoid-caching-suprises
 *  - https://codeascraft.com/2013/07/01/atomic-deploys-at-etsy/
 *
 * @category Cache
 * @package  EasyBib\OPcache
 * @author   Till Klampaeckel <till@lagged.biz>
 * @license  http://www.easybib.com/company/terms Terms of Service
 * @link     http://www.easybib.com
 */
class Juggler
{
    const ERROR_NEW_RELEASE = 1;
    const ERROR_OLD_RELEASE = 2;

    /**
     * @var string
     */
    private $base;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var string
     */
    private $newRelease;

    /**
     * @var string
     */
    private $oldRelease;

    /**
     * @param string $base
     * @param LoggerInterface|null $logger
     *
     * @return self
     */
    public function __construct($base, LoggerInterface $logger = null)
    {
        $this->base = $base;
        $this->logger = $logger;
    }

    /**
     * A wrapper around {@link Prime::doPopulate()} and {@link Prime::doClean()}
     * to rotate the opcache without resetting it completely.
     *
     * @param string $newRelease
     * @param string $oldRelease
     *
     * @return int
     */
    public function recycle($newRelease, $oldRelease)
    {
        $this->newRelease = $newRelease;
        $this->oldRelease = $oldRelease;

        $primer = new Prime($this->base, $this->logger);
        $primer->setPath($newRelease);

        if (1 === $primer->validate()) {
            return self::ERROR_NEW_RELEASE;
        }

        if (1 === $primer->doPopulate()) {
            return self::ERROR_NEW_RELEASE;
        }

        $primer->setPath();
        if (1 === $primer->validate()) {
            return self::ERROR_OLD_RELEASE;
        }

        return $primer->doClean();
    }
}
