<?php


namespace CommonTest\Service\Data;

use Common\Data\Object\Bundle;
use Common\Service\Data\Generic;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;

/**
 * Class GenericTest
 * @package CommonTest\Service\Data
 */
class GenericTest extends TestCase
{
    public function testFetchOne()
    {
        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with('/78', m::type('array'))->andReturn('Data');
        $sut = new Generic();
        $sut->setRestClient($mockClient);

        $this->assertEquals('Data', $sut->fetchOne(78));
        //check caching
        $sut->fetchOne(78);
    }

    public function testFetchList()
    {
        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with('', m::type('array'))->andReturn(['Results' => 'Data']);
        $sut = new Generic();
        $sut->setRestClient($mockClient);

        $this->assertEquals('Data', $sut->fetchList());
        //check caching
        $sut->fetchList();
    }

    public function testFetchListNoData()
    {
        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with('', m::type('array'))->andReturn('error');
        $sut = new Generic();
        $sut->setRestClient($mockClient);

        $this->assertEquals(false, $sut->fetchList());
        //check caching
        $sut->fetchList();
    }

    public function testSaveNew()
    {
        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('post')->once()->with('', m::type('array'))->andReturn(['id' => 78]);
        $sut = new Generic();
        $sut->setRestClient($mockClient);

        $this->assertEquals(78, $sut->save(['data']));
    }

    public function testSaveExisting()
    {
        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('put')->once()->with('/78', m::type('array'))->andReturn(['id' => 78]);
        $sut = new Generic();
        $sut->setRestClient($mockClient);

        $this->assertEquals(78, $sut->save(['id' => 78]));
    }

    /**
     * @param $return
     * @param $exception
     * @dataProvider saveExceptionsProvider
     */
    public function testSaveExceptions($return, $exception)
    {
        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('put')->once()->with('/78', m::type('array'))->andReturn($return);
        $sut = new Generic();
        $sut->setRestClient($mockClient);

        $passed = false;
        try {
            $sut->save(['id' => 78]);
        } catch (\Exception $e) {
            $passed = ($e->getMessage() == $exception->getMessage() && get_class($e) == get_class($exception));
        }

        $this->assertTrue($passed, 'Expected exception not thrown');
    }

    public function saveExceptionsProvider()
    {
        return [
            [false, new \Common\Exception\BadRequestException('Record could not be saved')],
            [[], new \Common\Exception\BadRequestException('Saved record contained no id')],
        ];
    }

    public function testDelete()
    {
        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('delete')->once()->with('/78')->andReturn(true);
        $sut = new Generic();
        $sut->setRestClient($mockClient);

        $this->assertTrue($sut->delete(78));
    }


    public function testDeleteException()
    {
        $exception = new \Common\Exception\BadRequestException('Record could not be deleted');

        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('delete')->once()->with('/78')->andReturn(false);
        $sut = new Generic();
        $sut->setRestClient($mockClient);

        $passed = false;
        try {
            $sut->delete(78);
        } catch (\Exception $e) {
            $passed = ($e->getMessage() == $exception->getMessage() && get_class($e) == get_class($exception));
        }

        $this->assertTrue($passed, 'Expected exception not thrown');
    }

    public function testSetServiceName()
    {
        $sut = new Generic();
        $sut->setServiceName('Licence');

        $this->assertEquals('Licence', $sut->getServiceName());
    }

    public function testGetDefaultBundleName()
    {
        $sut = new Generic();
        $sut->setServiceName('Licence');

        $this->assertEquals('Licence', $sut->getDefaultBundleName());
    }

    public function testSetDefaultBundle()
    {
        $bundle = new Bundle();
        $bundle->addChild('test');

        $expected = ['bundle' => json_encode(['children' => ['test']])];

        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with('/78', $expected)->andReturn('Data');
        $sut = new Generic();
        $sut->setRestClient($mockClient);
        $sut->setDefaultBundle($bundle);


        $sut->fetchOne(78);
    }
}
