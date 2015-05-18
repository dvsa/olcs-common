<?php

namespace CommonTest\Data\Object\Search;

/**
 * Class SearchAbstractTest
 * @package CommonTest\Data\Object\Search
 */
abstract class SearchAbstractTest extends \PHPUnit_Framework_TestCase
{
    protected $class = '';

    public function testGetTableConfig()
    {
        /** @var \Common\Data\Object\Search\InternalSearchAbstract $sut */
        $sut = new $this->class;
        $this->assertInternalType('array', $sut->getTableConfig());
        $this->assertArrayHasKey('variables', $sut->getTableConfig());
        $this->assertArrayHasKey('settings', $sut->getTableConfig());
        $this->assertArrayHasKey('attributes', $sut->getTableConfig());
        $this->assertArrayHasKey('columns', $sut->getTableConfig());
    }

    public function testGetNavigation()
    {
        /** @var \Common\Data\Object\Search\InternalSearchAbstract $sut */
        $sut = new $this->class;
        $this->assertInternalType('array', $sut->getNavigation());
        $this->assertArrayHasKey('label', $sut->getNavigation());
        $this->assertArrayHasKey('route', $sut->getNavigation());
        $this->assertArrayHasKey('params', $sut->getNavigation());
    }

    public function testGetTitle()
    {
        /** @var \Common\Data\Object\Search\InternalSearchAbstract $sut */
        $sut = new $this->class;
        $this->assertInternalType('string', $sut->getTitle());
    }

    public function testGetKey()
    {
        /** @var \Common\Data\Object\Search\InternalSearchAbstract $sut */
        $sut = new $this->class;
        $this->assertInternalType('string', $sut->getKey());
    }

    public function testGetSearchIndices()
    {
        /** @var \Common\Data\Object\Search\InternalSearchAbstract $sut */
        $sut = new $this->class;
        $this->assertInternalType('string', $sut->getSearchIndices());
    }

    public function testGetDisplayGroup()
    {
        /** @var \Common\Data\Object\Search\InternalSearchAbstract $sut */
        $sut = new $this->class;
        $this->assertInternalType('string', $sut->getDisplayGroup());
    }

    public function testGetFilters()
    {
        /** @var \Common\Data\Object\Search\InternalSearchAbstract $sut */
        $sut = new $this->class;
        $this->assertInternalType('array', $sut->getFilters());
    }
}
