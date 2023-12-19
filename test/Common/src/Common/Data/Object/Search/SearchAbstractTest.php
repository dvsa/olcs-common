<?php

namespace CommonTest\Common\Data\Object\Search;

use Common\Data\Object\Search\InternalSearchAbstract;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class SearchAbstractTest
 * @package CommonTest\Data\Object\Search
 */
abstract class SearchAbstractTest extends MockeryTestCase
{
    protected $class = '';

    /** @var InternalSearchAbstract */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new $this->class;
    }

    public function testGetTableConfig()
    {
        $this->assertIsArray($this->sut->getTableConfig());
        $this->assertArrayHasKey('variables', $this->sut->getTableConfig());
        $this->assertArrayHasKey('settings', $this->sut->getTableConfig());
        $this->assertArrayHasKey('attributes', $this->sut->getTableConfig());
        $this->assertArrayHasKey('columns', $this->sut->getTableConfig());
    }

    public function testGetNavigation()
    {
        $this->assertIsArray($this->sut->getNavigation());
        $this->assertArrayHasKey('label', $this->sut->getNavigation());
        $this->assertArrayHasKey('route', $this->sut->getNavigation());
        $this->assertArrayHasKey('params', $this->sut->getNavigation());
    }

    public function testGetTitle()
    {
        $this->assertIsString($this->sut->getTitle());
    }

    public function testGetKey()
    {
        $this->assertIsString($this->sut->getKey());
    }

    public function testGetSearchIndices()
    {
        $this->assertIsString($this->sut->getSearchIndices());
    }

    public function testGetDisplayGroup()
    {
        $this->assertIsString($this->sut->getDisplayGroup());
    }

    public function testGetFilters()
    {
        $this->assertIsArray($this->sut->getFilters());
    }
}
