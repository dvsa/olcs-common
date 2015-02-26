<?php

namespace OlcsTest\Service\Data;

use Common\Service\Data\Publication;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class PublicationLinkTest
 * @package OlcsTest\Service\Data
 */
class PublicationTest extends MockeryTestCase
{

    /**
     * tests the generate method
     */
    public function testGenerate()
    {
        $id = 10;
        $newId = 22;
        $publicationNo = 9999;
        $newPublicationNo = $publicationNo + 1;
        $version = 2;
        $pubType = 'N&P';
        $trafficAreaId = 'N';
        $pubStatusId = 'pub_s_new';
        $pubDate = '2014-10-31';
        $newPubDate = '2014-11-14';

        $currentPublication = [
            'id' => $id,
            'publicationNo' => $publicationNo,
            'version' => $version,
            'pubType' => $pubType,
            'pubDate' => $pubDate,
            'pubStatus' => [
                'id' => $pubStatusId
            ],
            'trafficArea' => [
                'id' => $trafficAreaId
            ]
        ];

        $updateData = [
            'id' => $id,
            'pubStatus' => 'pub_s_generated',
            'version' => $version
        ];

        $newPublicationData = [
            'trafficArea' => $trafficAreaId,
            'pubStatus' => 'pub_s_new',
            'pubDate' => $newPubDate,
            'pubType' => $pubType,
            'publicationNo' => $newPublicationNo
        ];

        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with('/' . $id, m::type('array'))->andReturn($currentPublication);
        $mockClient->shouldReceive('put')->once()->with('/' . $id, ['data' => json_encode($updateData)])->andReturn([]);
        $mockClient->shouldReceive('post')
            ->once()
            ->with(
                '',
                ['data' => json_encode($newPublicationData)]
            )
            ->andReturn(['id' => $newId]);

        $sut = new Publication();
        $sut->setRestClient($mockClient);

        $this->assertEquals($newId, $sut->generate($id));
    }

    /**
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testGenerateWithMissingId()
    {
        $id = 10;

        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with('/' . $id, m::type('array'))->andReturn([]);

        $sut = new Publication();
        $sut->setRestClient($mockClient);

        $sut->generate($id);
    }

    /**
     * tests a record can't be published if the status is incorrect
     *
     * @dataProvider incorrectGenerateStatusProvider
     * @expectedException \Common\Exception\DataServiceException
     */
    public function testGenerateWithIncorrectStatus($pubStatusId)
    {
        $id = 10;

        $currentPublication = [
            'id' => $id,
            'pubStatus' => [
                'id' => $pubStatusId
            ],
        ];

        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with('/' . $id, m::type('array'))->andReturn($currentPublication);

        $sut = new Publication();
        $sut->setRestClient($mockClient);

        $sut->generate($id);
    }

    /**
     * Data provider for testGenerateWithIncorrectStatus
     *
     * @return array
     */
    public function incorrectGenerateStatusProvider()
    {
        return [
            ['pub_s_generated'],
            ['pub_s_printed']
        ];
    }

    /**
     * Tests the publish method
     */
    public function testPublish()
    {
        $id = 10;
        $version = 2;
        $pubStatusId = 'pub_s_generated';

        $currentPublication = [
            'id' => $id,
            'version' => $version,
            'pubStatus' => [
                'id' => $pubStatusId
            ],
        ];

        $updateData = [
            'id' => $id,
            'pubStatus' => 'pub_s_printed',
            'version' => $version
        ];

        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with('/' . $id, m::type('array'))->andReturn($currentPublication);
        $mockClient->shouldReceive('put')->once()->with('/' . $id, ['data' => json_encode($updateData)])->andReturn([]);

        $sut = new Publication();
        $sut->setRestClient($mockClient);

        $this->assertEquals($id, $sut->publish($id));
    }

    /**
     * Tests the publish method throws exception if record is not found
     *
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testPublishWithMissingId()
    {
        $id = 10;

        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with('/' . $id, m::type('array'))->andReturn([]);

        $sut = new Publication();
        $sut->setRestClient($mockClient);

        $sut->publish($id);
    }

    /**
     * tests a record can't be published if the status is incorrect
     *
     * @dataProvider incorrectPublishStatusProvider
     * @expectedException \Common\Exception\DataServiceException
     */
    public function testPublishWithIncorrectStatus($pubStatusId)
    {
        $id = 10;

        $currentPublication = [
            'id' => $id,
            'pubStatus' => [
                'id' => $pubStatusId
            ],
        ];

        $mockClient = m::mock('Common\Util\RestClient');
        $mockClient->shouldReceive('get')->once()->with('/' . $id, m::type('array'))->andReturn($currentPublication);

        $sut = new Publication();
        $sut->setRestClient($mockClient);

        $sut->publish($id);
    }

    /**
     * Data provider for testPublishWithIncorrectStatus
     *
     * @return array
     */
    public function incorrectPublishStatusProvider()
    {
        return [
            ['pub_s_new'],
            ['pub_s_printed']
        ];
    }
}
