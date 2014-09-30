<?php

/**
 * Abstract Authorisation Section Service TestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\OperatingCentre;

use Common\Controller\Service\LicenceSectionService;
use CommonTest\Controller\Service\AbstractSectionServiceTestCase;

/**
 * Abstract Authorisation Section Service TestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractAuthorisationSectionServiceTestCase extends AbstractSectionServiceTestCase
{
    /**
     * @group section_service
     * @group operating_centre_section_service
     */
    public function testGetFormTableData()
    {
        $id = 4;
        $table = '';
        $response = array(
            'Count' => 2,
            'Results' => array(
                array(
                    'operatingCentre' => array(
                        'address' => array(
                            'id' => 1,
                            'version' => 2,
                            'addressLine1' => '123 Foo',
                            'addressLine2' => 'Bar way',
                            'postcode' => 'AB1 0AF'
                        )
                    )
                ),
                array(
                    'operatingCentre' => array(
                        'address' => array(
                            'id' => 2,
                            'version' => 2,
                            'addressLine1' => '124 Foo',
                            'addressLine2' => 'Bar way',
                            'postcode' => 'AB1 0AF'
                        )
                    )
                )
            )
        );
        $expected = array(
            array(
                'addressLine1' => '123 Foo',
                'addressLine2' => 'Bar way',
                'postcode' => 'AB1 0AF'
            ),
            array(
                'addressLine1' => '124 Foo',
                'addressLine2' => 'Bar way',
                'postcode' => 'AB1 0AF'
            )
        );
        $this->attachRestHelperMock();
        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->will($this->returnValue($response));

        $output = $this->sut->getFormTableData($id, $table);

        $this->assertEquals($expected, $output);
    }

    /**
     * @group section_service
     * @group operating_centre_section_service
     */
    public function testSaveWithTrafficArea()
    {
        $data = array(
            'trafficArea' => 'A'
        );

        $this->attachRestHelperMock();
        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall');

        $mockTrafficArea = $this->getMock(
            '\Common\Controller\Service\TrafficAreaSectionService',
            array('setTrafficArea')
        );

        $mockTrafficArea->expects($this->once())
            ->method('setTrafficArea')
            ->with('A');

        $this->mockSectionService('TrafficArea', $mockTrafficArea);

        $this->sut->save($data);
    }

    /**
     * @group section_service
     * @group operating_centre_section_service
     */
    public function testSaveWithoutTrafficArea()
    {
        $data = array(
            'foo' => 'bar'
        );

        $this->attachRestHelperMock();
        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall');

        $mockTrafficArea = $this->getMock(
            '\Common\Controller\Service\TrafficAreaSectionService',
            array('setTrafficArea')
        );

        $mockTrafficArea->expects($this->never())
            ->method('setTrafficArea');

        $this->mockSectionService('TrafficArea', $mockTrafficArea);

        $this->sut->save($data);
    }

    /**
     * @group section_service
     * @group operating_centre_section_service
     */
    public function testAlterFormForStandardPsv()
    {
        $id = 5;
        $this->sut->setIdentifier($id);
        $isPsv = true;
        $licenceType = LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL;
        $this->attachRestHelperMock();

        $mockLicenceService = $this->getMockLicenceSectionService();
        $mockLicenceService->expects($this->once())
            ->method('isPsv')
            ->will($this->returnValue($isPsv));
        $mockLicenceService->expects($this->once())
            ->method('getLicenceType')
            ->will($this->returnValue($licenceType));

        // Before alteration
        $form = $this->getAuthorisationForm();

        // After alteration
        $form = $this->sut->alterForm($form);
    }

    protected function getMockLicenceSectionService()
    {
        $mockLicenceService = $this->getMock(
            '\Common\Controller\Service\LicenceSectionService',
            array('isPsv', 'getLicenceType')
        );

        $this->mockSectionService('Licence', $mockLicenceService);

        return $mockLicenceService;
    }

    protected function getActionForm()
    {
        $formName = 'application_operating-centres_authorisation-sub-action';

        $form = $this->serviceManager->get('OlcsCustomForm')->createForm($formName);

        return $form;
    }

    protected function getAuthorisationForm()
    {
        $formName = 'application_operating-centres_authorisation';
        $tableName = 'authorisation_in_form';

        $data['url'] = $this->getMock('\stdClass', array('fromRoute'));
        $table = $this->serviceManager->get('Table')->buildTable($tableName, array(), $data, false);

        $form = $this->serviceManager->get('OlcsCustomForm')->createForm($formName);
        $form->get('table')->get('table')->setTable($table, 'table');

        $form->get('data')->setAttribute('unmappedName', 'data');
        $form->get('dataTrafficArea')->setAttribute('unmappedName', 'dataTrafficArea');
        $form->get('table')->setAttribute('unmappedName', 'table');

        return $form;
    }
}
