<?php

/**
 * CategoryDataTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Data;

use CommonTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Common\Service\Data\CategoryData;

/**
 * CategoryDataTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CategoryDataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Data\CategoryData
     */
    private $sut;

    private $serviceManager;

    private $mockRestHelper;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $this->mockRestHelper = $this->getMock('\Common\Service\Helper\RestHelperService', array('makeRestCall'));

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('Helper\Rest', $this->mockRestHelper);

        $this->sut = new CategoryData();
        $this->sut->setServiceLocator($this->serviceManager);
    }

    /**
     * @group data_service
     * @group category_data_service
     */
    public function testCreateService()
    {
        $service = $this->sut->createService($this->serviceManager);

        $this->assertSame($service, $this->sut);
    }

    /**
     * @group data_service
     * @group category_data_service
     */
    public function testGetCategoryByDescriptionWith1Result()
    {
        $description = 'SomeCategory';

        $expected = array(
            'description' => $description
        );

        $response = array(
            'Count' => 1,
            'Results' => array(
                $expected
            )
        );

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Category', 'GET', array('description' => $description))
            ->will($this->returnValue($response));

        $output = $this->sut->getCategoryByDescription($description);

        $this->assertEquals($expected, $output);
    }

    /**
     * @group data_service
     * @group category_data_service
     */
    public function testGetCategoryByDescriptionWith1ResultCached()
    {
        $description = 'SomeCategory';

        $expected = array(
            'description' => $description
        );

        $response = array(
            'Count' => 1,
            'Results' => array(
                $expected
            )
        );

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Category', 'GET', array('description' => $description))
            ->will($this->returnValue($response));

        $output = $this->sut->getCategoryByDescription($description);
        $this->assertEquals($expected, $output);

        $output2 = $this->sut->getCategoryByDescription($description);
        $this->assertEquals($expected, $output2);
    }

    /**
     * @group data_service
     * @group category_data_service
     */
    public function testGetCategoryByDescriptionWith0Results()
    {
        $description = 'SomeCategory';

        $response = array(
            'Count' => 0,
            'Results' => array()
        );

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Category', 'GET', array('description' => $description))
            ->will($this->returnValue($response));

        $output = $this->sut->getCategoryByDescription($description);

        $this->assertEquals(null, $output);
    }

    /**
     * @group data_service
     * @group category_data_service
     */
    public function testGetCategoryByDescriptionWithMultipleResults()
    {
        $description = 'SomeCategory';

        $expected = array(
            array(
                'description' => $description
            ),
            array(
                'description' => $description
            )
        );

        $response = array(
            'Count' => 2,
            'Results' => $expected
        );

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('DocumentSubCategory', 'GET', array('description' => $description))
            ->will($this->returnValue($response));

        $output = $this->sut->getCategoryByDescription($description, 'Document');

        $this->assertEquals($expected, $output);
    }

    /**
     * @group data_service
     * @group category_data_service
     */
    public function testGetSubCategoryByDescriptionWith1Result()
    {
        $description = 'SomeCategory';

        $expected = array(
            'description' => $description
        );

        $response = array(
            'Count' => 1,
            'Results' => array(
                $expected
            )
        );

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('DocumentSubCategory', 'GET', array('description' => $description))
            ->will($this->returnValue($response));

        $output = $this->sut->getCategoryByDescription($description, 'Document');

        $this->assertEquals($expected, $output);
    }

    /**
     * @group data_service
     * @group category_data_service
     */
    public function testGetSubCategoryByDescriptionWith1ResultCached()
    {
        $description = 'SomeCategory';

        $expected = array(
            'description' => $description
        );

        $response = array(
            'Count' => 1,
            'Results' => array(
                $expected
            )
        );

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('DocumentSubCategory', 'GET', array('description' => $description))
            ->will($this->returnValue($response));

        $output = $this->sut->getCategoryByDescription($description, 'Document');
        $this->assertEquals($expected, $output);

        $output2 = $this->sut->getCategoryByDescription($description, 'Document');
        $this->assertEquals($expected, $output2);
    }

    /**
     * @group data_service
     * @group category_data_service
     */
    public function testGetSubCategoryByDescriptionWith0Results()
    {
        $description = 'SomeCategory';

        $response = array(
            'Count' => 0,
            'Results' => array()
        );

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('DocumentSubCategory', 'GET', array('description' => $description))
            ->will($this->returnValue($response));

        $output = $this->sut->getCategoryByDescription($description, 'Document');

        $this->assertEquals(null, $output);
    }

    /**
     * @group data_service
     * @group category_data_service
     */
    public function testGetSubCategoryByDescriptionWithMultipleResults()
    {
        $description = 'SomeCategory';

        $expected = array(
            array(
                'description' => $description
            ),
            array(
                'description' => $description
            )
        );

        $response = array(
            'Count' => 2,
            'Results' => $expected
        );

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('DocumentSubCategory', 'GET', array('description' => $description))
            ->will($this->returnValue($response));

        $output = $this->sut->getCategoryByDescription($description, 'Document');

        $this->assertEquals($expected, $output);
    }
}
