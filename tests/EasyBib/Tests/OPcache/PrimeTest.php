<?php
namespace EasyBib\Tests\OPcache;

use EasyBib\OPcache\Prime;
use org\bovigo\vfs\vfsStream;

class PrimeTest extends \PHPUnit_Framework_TestCase
{
    private $vfsRoot = '__VFS_TEST_ROOT__';

    /**
     * 0 = success
     * 1 = error
     * @return array
     */
    public function pathProvider()
    {
        return [
            [
                '/vagrant_www',
                '/vagrant_www/foo',
                0,
                [
                    'vagrant_www' => [
                        'foo' => [],
                    ],
                ],
            ],
            [
                '/srv/www/easybib',
                '/srv/www/easybib/releases/12345',
                0,
                [
                    'srv' => [
                        'www' => [
                            'easybib' => [
                                'releases' => [
                                    '12345' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testInvalidPath()
    {
        vfsStream::setup(
            $this->vfsRoot,
            null,
            [
                'var' => [
                    'www' => [],
                ],
                'root' => [],
            ]
        );

        $path = $this->getFakePath('/root');
        $base = $this->getFakePath('/var/www');

        $primer = new Prime($base);
        $primer->setPath($path);

        $this->assertSame(1, $primer->validate());
    }

    public function testPsrLogger()
    {
        $primer = new Prime('foo', new \Psr\Log\NullLogger);
        $this->assertInstanceOf('EasyBib\OPcache\Prime', $primer);
    }

    /**
     * @param string $base
     * @param string $path
     * @param int    $status
     * @param array  $structure
     *
     * @dataProvider pathProvider
     */
    public function testPathWorks($base, $path, $status, $structure)
    {
        $fakePath = $this->getFakePath($path);
        $fakeBase = $this->getFakePath($base);
        vfsStream::setup($this->vfsRoot, null, $structure);

        $primer = new Prime($fakeBase);
        $this->assertSame($status, $primer->setPath($fakePath));
    }


    public function testSimple()
    {
        vfsStream::setup('my-root', 'null', ['foo' => ['bar' => []]]);
        $this->assertTrue(file_exists(vfsStream::url('my-root/foo')));
    }

    private function getFakePath($path)
    {
        return vfsStream::url($this->vfsRoot . $path);
    }
}
