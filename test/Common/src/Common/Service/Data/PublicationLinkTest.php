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
     * tests the delete method
     */
    public function testDelete()
    {
        $sut = new PublicationLink();
        $existingData = [
            'publication' => [
                'pubStatus' => [
                    'id' => 'pub_s_new'
                ]
            ]
        ];

        $id = 1;

        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockRestClient->shouldReceive('get')->once()->andReturn($existingData);
        $mockRestClient->shouldReceive('delete')->with($id)->andReturn([]);

        $sut->setRestClient($mockRestClient);

        $this->assertEquals(true, $sut->delete($id));
    }

    /**
     * tests the delete method throws the correct exception when an attempt is made to delete something already printed
     * @expectedException \Common\Exception\DataServiceException
     */
    public function testDeleteServiceException()
    {
        $sut = new PublicationLink();
        $existingData = [
            'publication' => [
                'pubStatus' => [
                    'id' => 'pub_s_printed'
                ]
            ]
        ];

        $id = 1;

        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockRestClient->shouldReceive('get')->once()->andReturn($existingData);

        $sut->setRestClient($mockRestClient);

        $sut->delete($id);
    }

    /**
     * tests the delete method throws the correct exception when the record doesn't exist
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testDeleteResourceNotFound()
    {
        $sut = new PublicationLink();
        $existingData = [];

        $id = 1;

        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockRestClient->shouldReceive('get')->once()->andReturn($existingData);

        $sut->setRestClient($mockRestClient);

        $sut->delete($id);
    }
}
