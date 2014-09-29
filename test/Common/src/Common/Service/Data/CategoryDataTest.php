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

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $this->sut = new CategoryData();
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
    public function testGetCategoryByDescription()
    {
        $description = 'SomeCategory';

        $this->sut->getCategoryByDescription();
    }
}
