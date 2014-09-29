<?php

/**
 * External Licence Authorisation Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\OperatingCentre;

use Common\Controller\Service\LicenceSectionService;
use Common\Controller\Service\OperatingCentre\ExternalLicenceAuthorisationSectionService;

/**
 * External Licence Authorisation Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ExternalLicenceAuthorisationSectionServiceTest extends AbstractAuthorisationSectionServiceTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Controller\Service\OperatingCentre\ExternalLicenceAuthorisationSectionServiceTest
     */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new ExternalLicenceAuthorisationSectionService();

        parent::setUp();
    }

    /**
     * @group section_service
     * @group operating_centre_section_service
     */
    public function testPostSetFormData()
    {
        $this->sut->setIsAction(false);

        $form = $this->getMock('\Zend\Form\Form', array('setData'));
        $form->expects($this->never())
            ->method('setData');

        $this->sut->postSetFormData($form);
    }

    /**
     * @group section_service
     * @group operating_centre_section_service
     */
    public function testPostSetFormDataFromAction()
    {
        $post = array();
        $actionId = 7;
        $response = array(
            'operatingCentre' => array(
                'address' => array(
                    'addressLine1' => '123 Foo',
                    'addressLine2' => 'Bar',
                    'postcode' => 'AB12AB',
                    'countryCode' => array(
                        'id' => 'GB'
                    )
                )
            )
        );
        $this->sut->setIsAction(true);
        $this->sut->setActionId($actionId);

        $this->attachRestHelperMock();
        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('LicenceOperatingCentre', 'GET', $actionId)
            ->will($this->returnValue($response));

        $mockRequest = $this->getMock('\stdClass', array('getPost'));
        $mockRequest->expects($this->once())
            ->method('getPost')
            ->will($this->returnValue($post));

        $this->sut->setRequest($mockRequest);

        $form = $this->getMock('\Zend\Form\Form', array('setData'));
        $form->expects($this->once())
            ->method('setData');

        $this->sut->postSetFormData($form);
    }

    /**
     * @group section_service
     * @group operating_centre_section_service
     */
    public function testMakeFormAlterationsForRestrictedPsv()
    {
        $options = array(
            'isPsv' => true,
            'isReview' => false,
            'data' => array(
                'licence' => array(
                    'licenceType' => array(
                        'id' => LicenceSectionService::LICENCE_TYPE_RESTRICTED
                    )
                )
            )
        );

        $form = $this->getAuthorisationForm();

        $this->assertTrue($form->get('data')->has('totAuthVehicles'));
        $this->assertTrue($form->get('data')->has('totAuthTrailers'));
        $this->assertTrue($form->get('data')->has('minTrailerAuth'));
        $this->assertTrue($form->get('data')->has('maxTrailerAuth'));
        $this->assertTrue(
            $form->get('table')->get('table')->getTable()->hasColumn('noOfTrailersPossessed')
        );

        $this->assertTrue($form->get('data')->has('totAuthLargeVehicles'));
        $this->assertTrue($form->get('data')->has('totCommunityLicences'));

        $form = $this->sut->makeFormAlterations($form, $options);

        // Goods/PSV Alterations
        $this->assertFalse($form->get('data')->has('totAuthVehicles'));
        $this->assertFalse($form->get('data')->has('totAuthTrailers'));
        $this->assertFalse($form->get('data')->has('minTrailerAuth'));
        $this->assertFalse($form->get('data')->has('maxTrailerAuth'));

        // Licence Type alterations
        $this->assertFalse($form->get('data')->has('totAuthLargeVehicles'));
        $this->assertTrue($form->get('data')->has('totCommunityLicences'));
        $this->assertFalse(
            $form->get('table')->get('table')->getTable()->hasColumn('noOfTrailersPossessed')
        );
    }

    /**
     * @group section_service
     * @group operating_centre_section_service
     */
    public function testMakeFormAlterationsForStandardPsv()
    {
        $options = array(
            'isPsv' => true,
            'isReview' => false,
            'data' => array(
                'licence' => array(
                    'licenceType' => array(
                        'id' => LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL
                    )
                )
            )
        );

        $form = $this->getAuthorisationForm();

        $this->assertTrue($form->get('data')->has('totAuthVehicles'));
        $this->assertTrue($form->get('data')->has('totAuthTrailers'));
        $this->assertTrue($form->get('data')->has('minTrailerAuth'));
        $this->assertTrue($form->get('data')->has('maxTrailerAuth'));
        $this->assertTrue(
            $form->get('table')->get('table')->getTable()->hasColumn('noOfTrailersPossessed')
        );

        $this->assertTrue($form->get('data')->has('totAuthLargeVehicles'));
        $this->assertTrue($form->get('data')->has('totCommunityLicences'));

        $form = $this->sut->makeFormAlterations($form, $options);

        // Goods/PSV Alterations
        $this->assertFalse($form->get('data')->has('totAuthVehicles'));
        $this->assertFalse($form->get('data')->has('totAuthTrailers'));
        $this->assertFalse($form->get('data')->has('minTrailerAuth'));
        $this->assertFalse($form->get('data')->has('maxTrailerAuth'));

        // Licence Type alterations
        $this->assertTrue($form->get('data')->has('totAuthLargeVehicles'));
        $this->assertFalse($form->get('data')->has('totCommunityLicences'));
        $this->assertFalse(
            $form->get('table')->get('table')->getTable()->hasColumn('noOfTrailersPossessed')
        );
    }

    /**
     * @group section_service
     * @group operating_centre_section_service
     */
    public function testMakeFormAlterationsForStandardPsvReview()
    {
        $appId = 4;

        $mockTrafficArea = $this->getMock(
            '\Common\Controller\Service\TrafficAreaSectionService',
            array('setIdentifier', 'getTrafficArea')
        );
        $mockTrafficArea->expects($this->once())
            ->method('getTrafficArea')
            ->will($this->returnValue(array('id' => 'A', 'name' => 'Foo')));

        $this->mockSectionService('TrafficArea', $mockTrafficArea);

        $options = array(
            'isPsv' => true,
            'isReview' => true,
            'data' => array(
                'id' => $appId,
                'licence' => array(
                    'licenceType' => array(
                        'id' => LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL
                    )
                )
            ),
            'fieldsets' => array(
                'dataTrafficArea',
                'data',
                'table'
            )
        );

        $form = $this->getAuthorisationForm();

        $this->assertTrue($form->get('data')->has('totAuthVehicles'));
        $this->assertTrue($form->get('data')->has('totAuthTrailers'));
        $this->assertTrue($form->get('data')->has('minTrailerAuth'));
        $this->assertTrue($form->get('data')->has('maxTrailerAuth'));
        $this->assertTrue(
            $form->get('table')->get('table')->getTable()->hasColumn('noOfTrailersPossessed')
        );

        $this->assertTrue($form->get('data')->has('totAuthLargeVehicles'));
        $this->assertTrue($form->get('data')->has('totCommunityLicences'));

        $this->assertTrue($form->get('dataTrafficArea')->has('trafficArea'));

        $form = $this->sut->makeFormAlterations($form, $options);

        // Goods/PSV Alterations
        $this->assertFalse($form->get('data')->has('totAuthVehicles'));
        $this->assertFalse($form->get('data')->has('totAuthTrailers'));
        $this->assertFalse($form->get('data')->has('minTrailerAuth'));
        $this->assertFalse($form->get('data')->has('maxTrailerAuth'));

        // Licence Type alterations
        $this->assertTrue($form->get('data')->has('totAuthLargeVehicles'));
        $this->assertFalse($form->get('data')->has('totCommunityLicences'));
        $this->assertFalse(
            $form->get('table')->get('table')->getTable()->hasColumn('noOfTrailersPossessed')
        );

        // Review
        $this->assertFalse($form->get('dataTrafficArea')->has('trafficArea'));
        $this->assertEquals(
            'Foo',
            $form->get('dataTrafficArea')->get('trafficAreaInfoNameExists')->getValue()
        );
    }

    /**
     * @group section_service
     * @group operating_centre_section_service
     */
    public function testMakeFormAlterationsForStandardPsvReviewWithoutTrafficArea()
    {
        $appId = 4;

        $mockTrafficArea = $this->getMock(
            '\Common\Controller\Service\TrafficAreaSectionService',
            array('setIdentifier', 'getTrafficArea')
        );
        $mockTrafficArea->expects($this->once())
            ->method('getTrafficArea')
            ->will($this->returnValue(null));

        $this->mockSectionService('TrafficArea', $mockTrafficArea);

        $options = array(
            'isPsv' => true,
            'isReview' => true,
            'data' => array(
                'id' => $appId,
                'licence' => array(
                    'licenceType' => array(
                        'id' => LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL
                    )
                )
            ),
            'fieldsets' => array(
                'dataTrafficArea',
                'data',
                'table'
            )
        );

        $form = $this->getAuthorisationForm();

        $this->assertTrue($form->get('data')->has('totAuthVehicles'));
        $this->assertTrue($form->get('data')->has('totAuthTrailers'));
        $this->assertTrue($form->get('data')->has('minTrailerAuth'));
        $this->assertTrue($form->get('data')->has('maxTrailerAuth'));
        $this->assertTrue(
            $form->get('table')->get('table')->getTable()->hasColumn('noOfTrailersPossessed')
        );

        $this->assertTrue($form->get('data')->has('totAuthLargeVehicles'));
        $this->assertTrue($form->get('data')->has('totCommunityLicences'));

        $this->assertTrue($form->get('dataTrafficArea')->has('trafficArea'));

        $form = $this->sut->makeFormAlterations($form, $options);

        // Goods/PSV Alterations
        $this->assertFalse($form->get('data')->has('totAuthVehicles'));
        $this->assertFalse($form->get('data')->has('totAuthTrailers'));
        $this->assertFalse($form->get('data')->has('minTrailerAuth'));
        $this->assertFalse($form->get('data')->has('maxTrailerAuth'));

        // Licence Type alterations
        $this->assertTrue($form->get('data')->has('totAuthLargeVehicles'));
        $this->assertFalse($form->get('data')->has('totCommunityLicences'));
        $this->assertFalse(
            $form->get('table')->get('table')->getTable()->hasColumn('noOfTrailersPossessed')
        );

        // Review
        $this->assertFalse($form->get('dataTrafficArea')->has('trafficArea'));
        $this->assertEquals(
            'unset',
            $form->get('dataTrafficArea')->get('trafficAreaInfoNameExists')->getValue()
        );
    }
}
