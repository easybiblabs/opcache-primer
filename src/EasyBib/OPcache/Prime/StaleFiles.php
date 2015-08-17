<?php
namespace EasyBib\OPcache\Prime;

/**
 * Simple object to filter through a list of files and to keep it testable.
 */
class StaleFiles
{

    private $files;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @param array $files
     * @param string $prefix
     *
     * @return self
     */
    public function __construct(array $files, $prefix)
    {
        $this->files = $files;
        $this->prefix = $prefix;
    }

    /**
     * @return array
     */
    public function filter()
    {
        $keep = [];

        foreach ($this->files as $file) {
            if ($this->prefix != substr($file['full_path'], 0, strlen($this->prefix))) {
                continue;
            }

            $keep[] = $file['full_path'];
        }

        return $keep;
    }
}
