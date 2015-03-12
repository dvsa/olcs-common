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
        $trafficAreaId = 'B';
        $trafficAreaName = 'North East of England';
        $isNi = 'N';
        $pubStatusId = 'pub_s_new';
        $pubStatusDescription = 'New';
        $pubDate = '2014-10-31';
        $newPubDate = '2014-11-14';
        $docTemplateId = 642;
        $docIdentifier = 'documentIdentifier';
        $docDescription = 'documentDescription';
        $documentPath = 'gb/publications/2014/10/' . strtolower($pubStatusDescription);
        $docSize = 12345;
        $categoryId = 11;
        $subCategoryId = 113;
        $savedDocumentId = 700;

        $currentPublication = [
            'id' => $id,
            'publicationNo' => $publicationNo,
            'version' => $version,
            'pubType' => $pubType,
            'pubDate' => $pubDate,
            'pubStatus' => [
                'id' => $pubStatusId,
                'description' => $pubStatusDescription
            ],
            'trafficArea' => [
                'id' => $trafficAreaId,
                'isNi' => $isNi,
                'name' => $trafficAreaName
            ],
            'docTemplate' => [
                'id' => $docTemplateId,
                'document' => [
                    'identifier' => $docIdentifier,
                ],
                'description' => $docDescription,
                'category' => [
                    'id' => $categoryId
                ],
                'subCategory' => [
                    'id' => $subCategoryId
                ]
            ]
        ];

        $updateData = [
            'id' => $id,
            'pubStatus' => 'pub_s_generated',
            'version' => $version,
            'document' => $savedDocumentId
        ];

        $newPublicationData = [
            'trafficArea' => $trafficAreaId,
            'pubStatus' => 'pub_s_new',
            'pubDate' => $newPubDate,
            'pubType' => $pubType,
            'publicationNo' => $newPublicationNo,
            'docTemplate' => $docTemplateId
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

        $mockJackRabbitFile = m::mock('Dvsa\Jackrabbit\Data\Object\File');
        $mockJackRabbitFile->shouldReceive('getIdentifier')->andReturn($docIdentifier);
        $mockJackRabbitFile->shouldReceive('getSize')->andReturn($docSize);

        $mockContentStore = m::mock('Dvsa\Jackrabbit\Service\Client');
        $mockContentStore->shouldReceive('read')->with($docIdentifier)->andReturn($mockJackRabbitFile);

        $mockDocumentService = m::mock('Common\Service\Document\Document');
        $mockDocumentService->shouldReceive('getBookmarkQueries');
        $mockDocumentService->shouldReceive('populateBookmarks');
        $mockDocumentService->shouldReceive('getMetadataKey');
        $mockDocumentService->shouldReceive('getTimestampFormat');
        $mockDocumentService->shouldReceive('formatFilename');

        $mockFileUploader = m::mock('Common\Service\File\ContentStoreFileUploader');
        $mockFileUploader->shouldReceive('setFile');

        $mockFileUploader->shouldReceive('buildPathNamespace')->andReturn($documentPath);
        $mockFileUploader->shouldReceive('upload')->with($documentPath)->andReturn($mockJackRabbitFile);

        $mockFileFactory = m::mock('Common\Service\File\FileUploaderFactory');
        $mockFileFactory->shouldReceive('getUploader')->andReturn($mockFileUploader);

        $mockRestHelper = m::mock('RestHelper');
        $mockRestHelper->shouldReceive('makeRestCall');

        $mockDocumentDataService = m::mock('Generic\Service\Data\Document');
        $mockDocumentDataService->shouldReceive('save')->andReturn($savedDocumentId);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockServiceManager->shouldReceive('get')
            ->with('Generic\Service\Data\Document')
            ->andReturn($mockDocumentDataService);
        $mockServiceManager->shouldReceive('get')->with('Helper\Rest')->andReturn($mockRestHelper);
        $mockServiceManager->shouldReceive('get')->with('ContentStore')->andReturn($mockContentStore);
        $mockServiceManager->shouldReceive('get')->with('FileUploader')->andReturn($mockFileFactory);
        $mockServiceManager->shouldReceive('get')->with('Document')->andReturn($mockDocumentService);

        $sut = new Publication();
        $sut->setRestClient($mockClient);
        $sut->setServiceLocator($mockServiceManager);

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
