<?php

namespace CommonTest\Data\Object\Search;

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class SearchAbstractTest
 * @package CommonTest\Data\Object\Search
 */
abstract class SearchAbstractTest extends MockeryTestCase
{
    protected $class = '';

    /** @var \Common\Data\Object\Search\InternalSearchAbstract */
    protected $sut;

    public function setUp()
    {
        $this->sut = new $this->class;
    }

    public function testGetTableConfig()
    {
        $this->assertInternalType('array', $this->sut->getTableConfig());
        $this->assertArrayHasKey('variables', $this->sut->getTableConfig());
        $this->assertArrayHasKey('settings', $this->sut->getTableConfig());
        $this->assertArrayHasKey('attributes', $this->sut->getTableConfig());
        $this->assertArrayHasKey('columns', $this->sut->getTableConfig());
    }

    public function testGetNavigation()
    {
        $this->assertInternalType('array', $this->sut->getNavigation());
        $this->assertArrayHasKey('label', $this->sut->getNavigation());
        $this->assertArrayHasKey('route', $this->sut->getNavigation());
        $this->assertArrayHasKey('params', $this->sut->getNavigation());
    }

    public function testGetTitle()
    {
        $this->assertInternalType('string', $this->sut->getTitle());
    }

    public function testGetKey()
    {
        $this->assertInternalType('string', $this->sut->getKey());
    }

    public function testGetSearchIndices()
    {
        $this->assertInternalType('string', $this->sut->getSearchIndices());
    }

    public function testGetDisplayGroup()
    {
        $this->assertInternalType('string', $this->sut->getDisplayGroup());
    }

    public function testGetFilters()
    {
        $this->assertInternalType('array', $this->sut->getFilters());
    }
}
