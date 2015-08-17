<?php
namespace EasyBib\Tests\OPcache;

use EasyBib\OPcache\Prime\StaleFiles;

class StaleFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider filterProvider
     */
    public function testFilter($files, $prefix, $count)
    {
        $filter = new StaleFiles($files, $prefix);

        $files = $filter->filter();

        $this->assertInternalType('array', $files);
        $this->assertSame($count, count($files));
    }

    public function filterProvider()
    {
        return [
            [
                [
                    ['full_path' => '/var/www/site-a/index.php'],
                    ['full_path' => '/var/www/site-a/test.php'],
                    ['full_path' => '/var/www/site-b/index.php'],
                ],
                '/var/www/site-b',
                1,
            ],
            [
                [
                    ['full_path' => '/var/www/site-a/index.php'],
                    ['full_path' => '/var/www/site-a/test.php'],
                    ['full_path' => '/var/www/site-b/index.php'],
                ],
                '/var/www/site-a',
                2,
            ]
        ];
    }
}
