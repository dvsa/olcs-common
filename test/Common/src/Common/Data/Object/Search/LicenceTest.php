<?php

namespace CommonTest\Data\Object\Search;

/**
 * Class LicenceTest
 * @package CommonTest\Data\Object\Search
 */
class LicenceTest extends SearchAbstractTest
{
    protected $class = 'Common\Data\Object\Search\Licence';

    public function testLicenceLink()
    {
        $formatter = $this->sut->getColumns()[0]['formatter'];
        $linkHtml = $formatter(['licId' => 876, 'licNo' => 'AB12345']);
        $this->assertSame('<a href="/licence/876?'. date('Y-m-d') .'">AB12345</a>', $linkHtml);
    }
}
