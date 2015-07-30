<?php
namespace EasyBib\Tests\OPcache;

use EasyBib\OPcache\Juggler;

class JugglerTest extends \PHPUnit_Framework_TestCase
{
    public function testThatItInitializes()
    {
        $juggler = new Juggler('/foo');
        $this->assertInstanceOf(Juggler::class, $juggler);
    }
}
