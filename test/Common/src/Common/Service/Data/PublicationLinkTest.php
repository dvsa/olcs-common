<?php

namespace OlcsTest\Service\Data;

use Common\Data\Object\Publication;
use Common\Data\Object\PublicationPolice;
use Common\Service\Data\PublicationLink;
use Mockery as m;

/**
 * Class PublicationLinkTest
 * @package OlcsTest\Service\Data
 */
class PublicationLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests create empty
     */
    public function testCreateEmpty()
    {
        $sut = new PublicationLink();
        $this->assertEquals('Common\Data\Object\Publication', get_class($sut->createEmpty()));
    }

    /**
     * tests the save method
     */
    public function testSave()
    {
        $publicationLinkId = 1;

        $policeData = [
            0 => [
                'forename' => 'John',
                'familyName' => 'Smith',
                'birthDate' => '1976-09-06'
            ]
        ];

        $publicationData = [
            'id' => $publicationLinkId,
            'policeData' => $policeData
        ];

        $publication = new Publication($publicationData);

        $mockPoliceService = m::mock('Common\Service\Data\PublicationPolice');
        $mockPoliceService->shouldReceive('deleteList')->with(['publicationLink' => $publicationLinkId]);
        $mockPoliceService->shouldReceive('createEmpty')->andReturn(new PublicationPolice());
        $mockPoliceService->shouldReceive('save');

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')
            ->with('Common\Service\Data\PublicationPolice')->andReturn($mockPoliceService);

        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockRestClient->shouldReceive('update')->once()->andReturn([]);

        $sut = new PublicationLink();

        $sut->setRestClient($mockRestClient);
        $sut->setServiceLocator($mockServiceManager);

        $this->assertEquals($publicationLinkId, $sut->save($publication));
    }

    /**
     * Tests the fetchOne method
     */
    public function testFetchOne()
    {
        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with('/78', m::type('array'))->andReturn('Data');
        $sut = new PublicationLink();
        $sut->setRestClient($mockClient);

        $this->assertEquals('Data', $sut->fetchOne(78));
        //check caching
        $sut->fetchOne(78);
    }

    /**
     * tests the delete method
     */
    public function testDelete()
    {
        $sut = new PublicationLink();

        $id = 1;
        $mockRestClient = $this->getRestClientWithData($this->getMockPrintedPublicationRecord('pub_s_new'));
        $mockRestClient->shouldReceive('delete')->with($id)->andReturn([]);

        $sut->setRestClient($mockRestClient);

        $this->assertEquals(true, $sut->delete($id));
    }

    /**
     * tests the delete method
     */
    public function testUpdate()
    {
        $sut = new PublicationLink();

        $id = 1;
        $mockRestClient = $this->getRestClientWithData($this->getMockPrintedPublicationRecord('pub_s_new'));
        $mockRestClient->shouldReceive('update')->with('/' . $id, ['data' => '{"id":' . $id . '}'])->andReturn([]);
        $sut->setRestClient($mockRestClient);

        $this->assertEquals(true, $sut->update($id, ['id' => $id]));
    }

    /**
     * tests the delete method throws the correct exception when an attempt is made to delete something already printed
     * @expectedException \Common\Exception\DataServiceException
     */
    public function testDeleteServiceException()
    {
        $sut = new PublicationLink();
        $sut->setRestClient($this->getRestClientWithData($this->getMockPrintedPublicationRecord('pub_s_printed')));

        $sut->delete(1);
    }

    /**
     * tests the update method throws the correct exception when an attempt is made to delete something already printed
     * @expectedException \Common\Exception\DataServiceException
     */
    public function testUpdateServiceException()
    {
        $sut = new PublicationLink();
        $sut->setRestClient($this->getRestClientWithData($this->getMockPrintedPublicationRecord('pub_s_printed')));

        $sut->update(1, []);
    }

    /**
     * tests the delete method throws the correct exception when the record doesn't exist
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testDeleteResourceNotFound()
    {
        $sut = new PublicationLink();
        $sut->setRestClient($this->getRestClientWithData([]));

        $sut->delete(1);
    }

    /**
     * tests the update method throws the correct exception when the record doesn't exist
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testUpdateResourceNotFound()
    {
        $sut = new PublicationLink();
        $sut->setRestClient($this->getRestClientWithData([]));

        $sut->update(1, []);
    }

    /**
     * Returns a mock rest client with specified data
     *
     * @param $data
     * @return m\MockInterface
     */
    public function getRestClientWithData($data)
    {
        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockRestClient->shouldReceive('get')->once()->andReturn($data);

        return $mockRestClient;
    }

    /**
     * Returns data for a printed publication record
     *
     * @return array
     */
    public function getMockPrintedPublicationRecord($status)
    {
        return [
            'publication' => [
                'pubStatus' => [
                    'id' => $status
                ]
            ]
        ];
    }
}
