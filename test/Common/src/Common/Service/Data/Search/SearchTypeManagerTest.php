<?php

namespace OlcsTest\Service\Data\Search;

use Common\Data\Object\Search\SearchAbstract;
use Common\Service\Data\Search\SearchTypeManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class SearchTypeManagerTest
 * @package OlcsTest\Service\Data\Search
 */
class SearchTypeManagerTest extends MockeryTestCase
{
    public function testValidate()
    {
        $plugin = m::mock(SearchAbstract::class);

        $sut = new SearchTypeManager();
        $this->assertNull($sut->validate($plugin));
    }

    public function testValidateInvalid()
    {
        $this->expectException(InvalidServiceException::class);

        $sut = new SearchTypeManager();
        $sut->validate(null);
    }

    /**
     * @todo To be removed as part of OLCS-28149
     */
    public function testValidatePluginInvalid()
    {
        $this->expectException(RuntimeException::class);

        $sut = new SearchTypeManager();
        $sut->validatePlugin(null);
    }

    /**
     * @todo To be removed as part of OLCS-28149
     */
    public function testValidatePlugin()
    {
        $plugin = m::mock(SearchAbstract::class);

        $sut = new SearchTypeManager();
        $this->assertNull($sut->validatePlugin($plugin));
    }
}
