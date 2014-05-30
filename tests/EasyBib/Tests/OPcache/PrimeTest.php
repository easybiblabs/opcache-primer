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

    public function testValidate()
    {
        vfsStream::setup(
            $this->vfsRoot,
            null,
            [
                'var' => [
                    'www' => [
                        'app' => [
                            '12hdh3' => [],
                        ],
                    ],
                 ],
                 'root' => [],
            ]
        );

        $path = $this->getFakePath('/root');
        $base = $this->getFakePath('/var/www');

        $primer = new Prime($base);
        $primer->setPath($path);

        $this->assertSame(1, $primer->validate());

        $path = $this->getFakePath('/var/www/app/12hdh3');
        $primer->setPath($path);

        $this->assertSame(0, $primer->validate());
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

    public function testCacheFile()
    {
        vfsStream::setup(
            $this->vfsRoot,
            null,
            [
                'srv' => [
                    'www' => [
                        'app-name' => [
                            'rev-12345' => [
                                'var' => [],
                                'public' => [],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $base = $this->getFakePath('/srv/www');
        $appRoot = $base . '/app-name/rev-12345';

        // create some files
        foreach (
            [
                'index.php' => '<?php echo "hello world";',
                'phpinfo.php' => '<?php phpinfo(); ?>',
                'till.php' => '<?php class Till {} ?>',
            ]
            as $file => $content
        ) {

            $this->createFile($appRoot . '/public/' . $file, $content);
        }

        // create opcache.cache file

        $cacheContent  = $appRoot . '/public/index.php' . "\n";
        $cacheContent .= $appRoot . '/public/phpinfo.php' . "\n";
        $cacheContent .= $appRoot . '/public/till.php' . "\n";

        $cacheFile = $appRoot . '/var/opcache.cache';
        $this->createFile(
            $cacheFile,
            $cacheContent
        );

        $primer = new Prime($base);
        $primer->setPath($appRoot);

        $this->assertSame(0, $primer->setCacheFile($cacheFile));
        $this->assertSame(0, $primer->doPopulate());
    }

    public function testSimple()
    {
        vfsStream::setup('my-root', 'null', ['foo' => ['bar' => []]]);
        $this->assertTrue(file_exists(vfsStream::url('my-root/foo')));
    }

    private function createFile($path, $content)
    {
        file_put_contents($path, $content);
    }

    private function getFakePath($path)
    {
        return vfsStream::url($this->vfsRoot . $path);
    }
}
