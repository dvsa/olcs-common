<?php

/**
 * Access Helper Tests
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\AccessHelperService;

/**
 * Access Helper Tests
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AccessHelperServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the sut
     *
     * @var \Common\Service\Helper\LvaAccessHelperService
     */
    private $sut;

    /**
     * Mock restriction helper
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRestrictionHelper;

    public function setUp()
    {
        $this->mockRestrictionHelper = $this->getMock(
            '\Common\Service\Helper\RestrictionHelperService',
            array('isRestrictionSatisfied')
        );

        $mockFactory = $this->getMock('\Common\Service\Helper\HelperServiceFactory', array('getHelperService'));
        $mockFactory->expects($this->any())
            ->method('getHelperService')
            ->with('RestrictionHelper')
            ->will($this->returnValue($this->mockRestrictionHelper));

        $this->sut = new AccessHelperService();
        $this->sut->setHelperServiceFactory($mockFactory);

        $sections = array(
            'no_restriction' => array(),
            'has_access' => array(
                'restricted' => array(
                    'access'
                )
            ),
            'hasnt_got_access' => array(
                'restricted' => array(
                    'no-access'
                )
            )
        );

        $this->sut->setSections($sections);
    }

    /**
     * @group helper_service
     * @group access_helper_service
     */
    public function testDoesHaveAccess()
    {
        $access = array('access');

        $this->setSharedMockRestrictionHelperExpectations();

        $this->assertTrue($this->sut->doesHaveAccess('no_restriction', $access));
        $this->assertTrue($this->sut->doesHaveAccess('has_access', $access));
        $this->assertFalse($this->sut->doesHaveAccess('hasnt_got_access', $access));
    }

    /**
     * @group helper_service
     * @group access_helper_service
     */
    public function testGetAccessibleSections()
    {
        $access = array('access');

        $expected = array(
            'no_restriction' => array(),
            'has_access' => array(
                'restricted' => array(
                    'access'
                )
            )
        );

        $this->setSharedMockRestrictionHelperExpectations();

        $output = $this->sut->getAccessibleSections($access);

        $this->assertEquals($expected, $output);
    }

    /**
     * Helper method to DRY up the test
     */
    private function setSharedMockRestrictionHelperExpectations()
    {
        $access = array('access');

        $this->mockRestrictionHelper->expects($this->at(0))
            ->method('isRestrictionSatisfied')
            ->with(array('access'), $access)
            ->willReturn(true);

        $this->mockRestrictionHelper->expects($this->at(1))
            ->method('isRestrictionSatisfied')
            ->with(array('no-access'), $access)
            ->willReturn(false);
    }
}
