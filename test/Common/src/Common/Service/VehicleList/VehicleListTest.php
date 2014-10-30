<?php

/**
 * Vehicle List service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\VehicleList;

use Common\Service\VehicleList\VehicleList;
use CommonTest\Bootstrap;

/**
 * Vehicle List service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VehicleListTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Common\Service\VehicleList\VehicleList
     */
    public $vehicleList;

    /**
     * @var ServiceLocatorInterface
     */
    public $serviceLocator;

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
     */
    public function setUp()
    {
        $this->vehicleList = $this->getMock('Common\Service\VehicleList\VehicleList', array('makeRestCall'));

        $this->vehicleList->expects($this->any())
            ->method('makeRestCall')
            ->will($this->returnCallback(array($this, 'mockRestCalls')));

        $this->serviceLocator = Bootstrap::getServiceManager();
        $this->serviceLocator->setAllowOverride(true);

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
                throw new \Exception('Error saving document');
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
     * Test set licence ids
     * 
     * @group vehicleList
     */
    public function testSetLicenceIds()
    {
        $licenceIds = [1,2,3];
        $this->vehicleList->setLicenceIds($licenceIds);
        $this->assertEquals($this->vehicleList->getLicenceIds(), $licenceIds);
    }

    /**
     * Test set logged in user
     * 
     * @group vehicleList
     */
    public function testSetLoggedInUser()
    {
        $loggedInUser = 1;
        $this->vehicleList->setLoggedInUser($loggedInUser);
        $this->assertEquals($this->vehicleList->getLoggedInUser(), $loggedInUser);
    }

    /**
     * Test generate vehicle list with no licence ids provided
     * 
     * @group vehicleList
     */
    public function testGenerateVehicleListNoLicenceIds()
    {
        $this->vehicleList->setLicenceIds([]);
        $retv = $this->vehicleList->generateVehicleList();
        $this->assertEquals($retv, false);
    }

    /**
     * Test generate vehicle list with no template received
     * 
     * @expectedException \Exception
     * @group vehicleList
     */
    public function testGenerateVehicleListNoTemplate()
    {
        $this->vehicleList->setLicenceIds([1]);

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

        $this->serviceLocator->setService('Document', $mockDocument);
        $this->serviceLocator->setService('category', $mockCategory);
        $this->serviceLocator->setService('ContentStore', $mockContentStore);

        $this->vehicleList->setServiceLocator($this->serviceLocator);
        $this->vehicleList->generateVehicleList();
    }

    /**
     * Test generate vehicle list with no bookmarks queries received
     * 
     * @expectedException \Exception
     * @group vehicleList
     */
    public function testGenerateVehicleListNoBookmarkQueries()
    {
        $this->vehicleList->setLicenceIds([1]);

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
            ->with('file content', ['licence' => 1, 'user' => null])
            ->will($this->returnValue(false));

        $this->serviceLocator->setService('Document', $mockDocument);
        $this->serviceLocator->setService('category', $mockCategory);
        $this->serviceLocator->setService('ContentStore', $mockContentStore);

        $this->vehicleList->setServiceLocator($this->serviceLocator);
        $this->vehicleList->generateVehicleList();
    }

    /**
     * Test generate vehicle list with no bookmarks received
     * 
     * @expectedException \Exception
     * @group vehicleList
     */
    public function testGenerateVehicleListNoBookmarks()
    {
        $this->vehicleList->setLicenceIds([1]);

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
            ->with('file content', ['licence' => 1, 'user' => null])
            ->will($this->returnValue(['Results' => ['some queries here']]));

        $this->bookmarksFound = false;

        $this->serviceLocator->setService('Document', $mockDocument);
        $this->serviceLocator->setService('category', $mockCategory);
        $this->serviceLocator->setService('ContentStore', $mockContentStore);

        $this->vehicleList->setServiceLocator($this->serviceLocator);
        $this->vehicleList->generateVehicleList();
    }

    /**
     * Test generate vehicle list with no bookmarks populated
     * 
     * @expectedException \Exception
     * @group vehicleList
     */
    public function testGenerateVehicleListNoBookmarksPopulated()
    {
        $this->vehicleList->setLicenceIds([1]);

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
            ->method('populateBookmarks')
            ->with('file content', [['some bookmarks here']])
            ->will($this->returnValue(null));

        $mockDocument->expects($this->once())
            ->method('getBookmarkQueries')
            ->with('file content', ['licence' => 1, 'user' => null])
            ->will($this->returnValue(['Results' => ['some queries here']]));

        $this->serviceLocator->setService('Document', $mockDocument);
        $this->serviceLocator->setService('category', $mockCategory);
        $this->serviceLocator->setService('ContentStore', $mockContentStore);

        $this->vehicleList->setServiceLocator($this->serviceLocator);
        $this->vehicleList->generateVehicleList();
    }

    /**
     * Test generate vehicle list with upload failed
     * 
     * @expectedException \Exception
     * @group vehicleList
     */
    public function testGenerateVehicleListUploadFiles()
    {
        $this->vehicleList->setLicenceIds([1]);

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
            ->method('populateBookmarks')
            ->with('file content', [['some bookmarks here']])
            ->will($this->returnValue('some content'));

        $mockDocument->expects($this->once())
            ->method('getBookmarkQueries')
            ->with('file content', ['licence' => 1, 'user' => null])
            ->will($this->returnValue(['Results' => ['some queries here']]));

        $mockFileUploader = $this->getMock('\StdClass', ['getUploader', 'setFile', 'upload']);
        $mockFileUploader->expects($this->once())
            ->method('getUploader')
            ->will($this->returnSelf());

        $mockFileUploader->expects($this->once())
            ->method('setFile')
            ->with(['content' => 'some content'])
            ->will($this->returnValue(true));

        $mockFileUploader->expects($this->once())
            ->method('upload')
            ->will($this->returnValue(false));

        $this->serviceLocator->setService('Document', $mockDocument);
        $this->serviceLocator->setService('category', $mockCategory);
        $this->serviceLocator->setService('ContentStore', $mockContentStore);
        $this->serviceLocator->setService('FileUploader', $mockFileUploader);

        $this->vehicleList->setServiceLocator($this->serviceLocator);
        $this->vehicleList->generateVehicleList();
    }

    /**
     * Test generate vehicle list with exception while saving document
     * 
     * @expectedException \Exception
     * @group vehicleList
     */
    public function testGenerateVehicleListWithExceptionWhilteSavingDocument()
    {
        $this->vehicleList->setLicenceIds([1]);

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
            ->with('file content', ['licence' => 1, 'user' => null])
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

        $this->serviceLocator->setService('Document', $mockDocument);
        $this->serviceLocator->setService('category', $mockCategory);
        $this->serviceLocator->setService('ContentStore', $mockContentStore);
        $this->serviceLocator->setService('FileUploader', $mockFileUploader);

        $this->vehicleList->setServiceLocator($this->serviceLocator);
        $this->vehicleList->generateVehicleList();
    }

    /**
     * Test generate vehicle list with serve file
     * 
     * @group vehicleList
     */
    public function testGenerateVehicleListWithServeFile()
    {
        $this->vehicleList->setLicenceIds([1]);

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
            ->with($mockFile, ['licence' => 1, 'user' => null])
            ->will($this->returnValue(['Results' => ['some queries here']]));

        $mockFileUploader = $this->getMock('\StdClass', ['getUploader', 'setFile', 'upload', 'serveFile']);
        $mockFileUploader->expects($this->once())
            ->method('getUploader')
            ->will($this->returnSelf());

        $mockFileUploader->expects($this->once())
            ->method('setFile')
            ->with(['content' => 'some content'])
            ->will($this->returnValue(true));

        $mockFileUploader->expects($this->once())
            ->method('serveFile')
            ->with($mockFile, date('YmdHi') . '_' . 'Goods_Vehicle_List.rtf')
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

        $this->serviceLocator->setService('Document', $mockDocument);
        $this->serviceLocator->setService('category', $mockCategory);
        $this->serviceLocator->setService('ContentStore', $mockContentStore);
        $this->serviceLocator->setService('FileUploader', $mockFileUploader);

        $this->vehicleList->setServiceLocator($this->serviceLocator);
        $this->vehicleList->generateVehicleList(true);
    }

    /**
     * Test generate vehicle list
     * 
     * @group vehicleList
     */
    public function testGenerateVehicleList()
    {
        $this->vehicleList->setLicenceIds([1]);

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
            ->with($mockFile, ['licence' => 1, 'user' => null])
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

        $this->serviceLocator->setService('Document', $mockDocument);
        $this->serviceLocator->setService('category', $mockCategory);
        $this->serviceLocator->setService('ContentStore', $mockContentStore);
        $this->serviceLocator->setService('FileUploader', $mockFileUploader);

        $this->vehicleList->setServiceLocator($this->serviceLocator);
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
