<?php

/**
 * Vehicle List service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\VehicleList;

use PHPUnit_Framework_TestCase;
use Common\Service\VehicleList\Exception;
use CommonTest\Bootstrap;
use CommonTest\Traits\MockDateTrait;

/**
 * Vehicle List service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VehicleListTest extends PHPUnit_Framework_TestCase
{
    use MockDateTrait;

    /**
     * @var Common\Service\VehicleList\VehicleList
     */
    public $vehicleList;

    /**
     * @var ServiceLocatorInterface
     */
    public $sm;

    /**
     * @var bool
     */
    public $bookmarksFound = true;

    /**
     * @var bool
     */
    public $raiseExceptionWhileSavingDocument = false;

    /**
     * Set up the Vehicle List service
     *
     * @todo These tests require a real service manager to run, as they are not mocking all dependencies,
     * these tests should be addresses
     */
    public function setUp()
    {
        $this->vehicleList = $this->getMock('Common\Service\VehicleList\VehicleList', array('makeRestCall'));

        $this->vehicleList->expects($this->any())
            ->method('makeRestCall')
            ->will($this->returnCallback(array($this, 'mockRestCalls')));

        // the MockDateTrait expects `sm` instead of `serviceLocator`; this is just a quick fix
        $this->sm = Bootstrap::getRealServiceManager();
        $this->sm->setAllowOverride(true);

    }

    /**
     * Mock the rest call
     *
     * @param string $service
     * @param string $method
     * @param array $data
     */
    public function mockRestCalls($service, $method, $data = array())
    {
        if ($service == 'Document' && $method == 'POST') {
            if ($this->raiseExceptionWhileSavingDocument) {
                throw new Exception('Error saving document');
            }
            return true;
        }

        if ($service == 'BookmarkSearch' && $method == 'GET') {
            if ($this->bookmarksFound) {
                $retv = [['some bookmarks here']];
            } else {
                $retv = [];
            }
            return $retv;
        }

        $this->fail(
            'makeRestCall not mocked. service: ' . $service . ' method: ' . $method . ' data: ' . serialize($data)
        );
    }

    /**
     * Test set template
     *
     * @group vehicleList
     */
    public function testSetTemplate()
    {
        $this->vehicleList->setTemplate('foo');
        $this->assertEquals('foo', $this->vehicleList->getTemplate());
    }

    /**
     * Test set description
     *
     * @group vehicleList
     */
    public function testSetDescription()
    {
        $this->vehicleList->setDescription('bar');
        $this->assertEquals('bar', $this->vehicleList->getDescription());
    }

    /**
     * Test set query data
     *
     * @group vehicleList
     */
    public function testSetQueryData()
    {
        $this->vehicleList->setQueryData([1, 2]);
        $this->assertEquals([1, 2], $this->vehicleList->getQueryData());
    }

    /**
     * Test set bookmark data
     *
     * @group vehicleList
     */
    public function testSetBookmarkData()
    {
        $this->vehicleList->setBookmarkData('bar');
        $this->assertEquals('bar', $this->vehicleList->getBookmarkData());
    }

    /**
     * Test generate vehicle list with no query data provided
     *
     * @group vehicleList
     */
    public function testGenerateVehicleListNoQueryData()
    {
        $this->assertFalse(
            $this->vehicleList->generateVehicleList()
        );
    }

    /**
     * Test generate vehicle list with no template received
     *
     * @expectedException Common\Service\VehicleList\Exception
     * @group vehicleList
     */
    public function testGenerateVehicleListNoTemplate()
    {
        $this->vehicleList
            ->setQueryData([1])
            ->setTemplate('GVVehiclesList');

        $mockCategory = $this->getMock('\StdClass', ['getCategoryByDescription']);
        $mockDocument = $this->getMock('\StdClass', ['getBookmarkQueries', 'populateBookmarks']);

        $mockCategory->expects($this->any())
            ->method('getCategoryByDescription')
            ->will(
                $this->returnValueMap(
                    [
                        ['Licensing', null, 'category1'],
                        ['Vehicle List', 'Document', 'category2'],
                    ]
                )
            );

        $mockContentStore = $this->getMockContentStore();
        $mockContentStore->expects($this->once())
            ->method('read')
            ->with('/templates/GVVehiclesList.rtf')
            ->will($this->returnValue(null));

        $this->sm->setService('Document', $mockDocument);
        $this->sm->setService('category', $mockCategory);
        $this->sm->setService('ContentStore', $mockContentStore);

        $this->vehicleList->setServiceLocator($this->sm);
        $this->vehicleList->generateVehicleList();
    }

    /**
     * Test generate vehicle list with no bookmarks queries received
     *
     * @expectedException Common\Service\VehicleList\Exception
     * @group vehicleList
     */
    public function testGenerateVehicleListNoBookmarkQueries()
    {
        $this->vehicleList
            ->setQueryData([[1]])
            ->setTemplate('GVVehiclesList');

        $mockCategory = $this->getMock('\StdClass', ['getCategoryByDescription']);
        $mockDocument = $this->getMock('\StdClass', ['getBookmarkQueries', 'populateBookmarks']);

        $mockCategory->expects($this->any())
            ->method('getCategoryByDescription')
            ->will(
                $this->returnValueMap(
                    [
                        ['Licensing', null, 'category1'],
                        ['Vehicle List', 'Document', 'category2'],
                    ]
                )
            );

        $mockContentStore = $this->getMockContentStore();
        $mockContentStore->expects($this->once())
            ->method('read')
            ->with('/templates/GVVehiclesList.rtf')
            ->will($this->returnValue('file content'));

        $mockDocument->expects($this->once())
            ->method('getBookmarkQueries')
            ->with('file content', [1])
            ->will($this->returnValue(false));

        $this->sm->setService('Document', $mockDocument);
        $this->sm->setService('category', $mockCategory);
        $this->sm->setService('ContentStore', $mockContentStore);

        $this->vehicleList->setServiceLocator($this->sm);
        $this->vehicleList->generateVehicleList();
    }

    /**
     * Test generate vehicle list with no bookmarks received
     *
     * @expectedException Common\Service\VehicleList\Exception
     * @group vehicleList
     */
    public function testGenerateVehicleListNoBookmarks()
    {
        $this->vehicleList
            ->setQueryData([[1]])
            ->setTemplate('GVVehiclesList');

        $mockCategory = $this->getMock('\StdClass', ['getCategoryByDescription']);
        $mockDocument = $this->getMock('\StdClass', ['getBookmarkQueries', 'populateBookmarks']);

        $mockCategory->expects($this->any())
            ->method('getCategoryByDescription')
            ->will(
                $this->returnValueMap(
                    [
                        ['Licensing', null, 'category1'],
                        ['Vehicle List', 'Document', 'category2'],
                    ]
                )
            );

        $mockContentStore = $this->getMockContentStore();
        $mockContentStore->expects($this->once())
            ->method('read')
            ->with('/templates/GVVehiclesList.rtf')
            ->will($this->returnValue('file content'));

        $mockDocument->expects($this->once())
            ->method('getBookmarkQueries')
            ->with('file content', [1])
            ->will($this->returnValue(['Results' => ['some queries here']]));

        $this->bookmarksFound = false;

        $this->sm->setService('Document', $mockDocument);
        $this->sm->setService('category', $mockCategory);
        $this->sm->setService('ContentStore', $mockContentStore);

        $this->vehicleList->setServiceLocator($this->sm);
        $this->vehicleList->generateVehicleList();
    }

    /**
     * Test generate vehicle list with exception while saving document
     *
     * @expectedException Common\Service\VehicleList\Exception
     * @group vehicleList
     */
    public function testGenerateVehicleListWithExceptionWhilteSavingDocument()
    {
        $this->vehicleList
            ->setQueryData([['licence' => 1]])
            ->setTemplate('GVVehiclesList');

        $mockCategory = $this->getMock('\StdClass', ['getCategoryByDescription']);
        $mockDocument = $this->getMock('\StdClass', ['getBookmarkQueries', 'populateBookmarks']);

        $mockCategory->expects($this->any())
            ->method('getCategoryByDescription')
            ->will(
                $this->returnValueMap(
                    [
                        ['Licensing', null, ['id' => 1]],
                        ['Vehicle List', 'Document', ['id' => 2]],
                    ]
                )
            );

        $mockContentStore = $this->getMockContentStore();
        $mockContentStore->expects($this->once())
            ->method('read')
            ->with('/templates/GVVehiclesList.rtf')
            ->will($this->returnValue('file content'));

        $mockDocument->expects($this->once())
            ->method('populateBookmarks')
            ->with('file content', [['some bookmarks here']])
            ->will($this->returnValue('some content'));

        $mockDocument->expects($this->once())
            ->method('getBookmarkQueries')
            ->with('file content', ['licence' => 1])
            ->will($this->returnValue(['Results' => ['some queries here']]));

        $mockFileUploader = $this->getMock('\StdClass', ['getUploader', 'setFile', 'upload']);
        $mockFileUploader->expects($this->once())
            ->method('getUploader')
            ->will($this->returnSelf());

        $mockFileUploader->expects($this->once())
            ->method('setFile')
            ->with(['content' => 'some content'])
            ->will($this->returnValue(true));

        $mockUploadedFile = $this->getMock('\StdClass', ['getIdentifier', 'getSize']);
        $mockUploadedFile->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('id'));

        $mockUploadedFile->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(1000));

        $this->raiseExceptionWhileSavingDocument = true;

        $mockFileUploader->expects($this->once())
            ->method('upload')
            ->will($this->returnValue($mockUploadedFile));

        $this->sm->setService('Document', $mockDocument);
        $this->sm->setService('category', $mockCategory);
        $this->sm->setService('ContentStore', $mockContentStore);
        $this->sm->setService('FileUploader', $mockFileUploader);

        $this->vehicleList->setServiceLocator($this->sm);
        $this->vehicleList->generateVehicleList();
    }

    /**
     * Test generate vehicle list with serve file
     *
     * @group vehicleList
     */
    public function testGenerateVehicleListWithServeFile()
    {
        $this->vehicleList
            ->setQueryData([['licence' => 1]])
            ->setTemplate('GVVehiclesList')
            ->setDescription('Goods Vehicle List');

        $mockCategory = $this->getMock('\StdClass', ['getCategoryByDescription']);
        $mockDocument = $this->getMock('\StdClass', ['getBookmarkQueries', 'populateBookmarks']);

        $mockCategory->expects($this->any())
            ->method('getCategoryByDescription')
            ->will(
                $this->returnValueMap(
                    [
                        ['Licensing', null, ['id' => 1]],
                        ['Vehicle List', 'Document', ['id' => 2]],
                    ]
                )
            );

        $mockFile = $this->getMock('\StdClass', ['setContent']);

        $mockFile->expects($this->once())
            ->method('setContent')
            ->with('some content')
            ->will($this->returnValue(true));

        $mockContentStore = $this->getMockContentStore();
        $mockContentStore->expects($this->once())
            ->method('read')
            ->with('/templates/GVVehiclesList.rtf')
            ->will($this->returnValue($mockFile));

        $mockDocument->expects($this->once())
            ->method('populateBookmarks')
            ->with($mockFile, [['some bookmarks here']])
            ->will($this->returnValue('some content'));

        $mockDocument->expects($this->once())
            ->method('getBookmarkQueries')
            ->with($mockFile, ['licence' => 1])
            ->will($this->returnValue(['Results' => ['some queries here']]));

        $mockFileUploader = $this->getMock('\StdClass', ['getUploader', 'setFile', 'upload', 'serveFile']);
        $mockFileUploader->expects($this->once())
            ->method('getUploader')
            ->will($this->returnSelf());

        $mockFileUploader->expects($this->once())
            ->method('setFile')
            ->with(['content' => 'some content'])
            ->will($this->returnValue(true));

        $this->mockDate('20141208114500');

        $mockFileUploader->expects($this->once())
            ->method('serveFile')
            ->with($mockFile, '20141208114500_Goods_Vehicle_List.rtf')
            ->will($this->returnValue(true));

        $mockUploadedFile = $this->getMock('\StdClass', ['getIdentifier', 'getSize']);
        $mockUploadedFile->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('id'));

        $mockUploadedFile->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(1000));

        $mockFileUploader->expects($this->once())
            ->method('upload')
            ->will($this->returnValue($mockUploadedFile));

        $this->sm->setService('Document', $mockDocument);
        $this->sm->setService('category', $mockCategory);
        $this->sm->setService('ContentStore', $mockContentStore);
        $this->sm->setService('FileUploader', $mockFileUploader);

        $this->vehicleList->setServiceLocator($this->sm);
        $this->vehicleList->generateVehicleList(true);
    }

    /**
     * Test generate vehicle list
     *
     * @group vehicleList
     */
    public function testGenerateVehicleList()
    {
        $this->vehicleList
            ->setQueryData([['licence' => 1]])
            ->setTemplate('GVVehiclesList')
            ->setDescription('Goods Vehicle List');

        $mockCategory = $this->getMock('\StdClass', ['getCategoryByDescription']);
        $mockDocument = $this->getMock('\StdClass', ['getBookmarkQueries', 'populateBookmarks']);

        $mockCategory->expects($this->any())
            ->method('getCategoryByDescription')
            ->will(
                $this->returnValueMap(
                    [
                        ['Licensing', null, ['id' => 1]],
                        ['Vehicle List', 'Document', ['id' => 2]],
                    ]
                )
            );

        $mockFile = $this->getMock('\StdClass', ['setContent']);

        $mockContentStore = $this->getMockContentStore();
        $mockContentStore->expects($this->once())
            ->method('read')
            ->with('/templates/GVVehiclesList.rtf')
            ->will($this->returnValue($mockFile));

        $mockDocument->expects($this->once())
            ->method('populateBookmarks')
            ->with($mockFile, [['some bookmarks here']])
            ->will($this->returnValue('some content'));

        $mockDocument->expects($this->once())
            ->method('getBookmarkQueries')
            ->with($mockFile, ['licence' => 1])
            ->will($this->returnValue(['Results' => ['some queries here']]));

        $mockFileUploader = $this->getMock('\StdClass', ['getUploader', 'setFile', 'upload', 'serveFile']);
        $mockFileUploader->expects($this->once())
            ->method('getUploader')
            ->will($this->returnSelf());

        $mockFileUploader->expects($this->once())
            ->method('setFile')
            ->with(['content' => 'some content'])
            ->will($this->returnValue(true));

        $mockUploadedFile = $this->getMock('\StdClass', ['getIdentifier', 'getSize']);
        $mockUploadedFile->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('id'));

        $mockUploadedFile->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(1000));

        $mockFileUploader->expects($this->once())
            ->method('upload')
            ->will($this->returnValue($mockUploadedFile));

        $this->sm->setService('Document', $mockDocument);
        $this->sm->setService('category', $mockCategory);
        $this->sm->setService('ContentStore', $mockContentStore);
        $this->sm->setService('FileUploader', $mockFileUploader);

        $this->vehicleList->setServiceLocator($this->sm);
        $this->vehicleList->generateVehicleList();
    }

    /**
     * Create content store mock
     *
     * @return Mock
     */
    public function getMockContentStore()
    {
        $mockRequest = $this->getMock('\StdClass', ['setMethod']);
        $mockRequest->expects($this->once())
            ->method('setMethod')
            ->with($this->equalTo('GET'));

        $mockHttpClient = $this->getMock('\StdClass', ['getRequest']);
        $mockHttpClient->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($mockRequest));

        $mockContentStore = $this->getMock('\StdClass', ['getHttpClient', 'read', 'setContent']);
        $mockContentStore->expects($this->once())
            ->method('getHttpClient')
            ->will($this->returnValue($mockHttpClient));

        return $mockContentStore;
    }
}
