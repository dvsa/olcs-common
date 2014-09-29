<?php

/**
 * Licence Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use CommonTest\Bootstrap;
use Zend\Form\Element\Text;
use Common\Controller\Service\LicenceSectionService;

/**
 * Licence Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceSectionServiceTest extends AbstractSectionServiceTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Controller\Service\LicenceSectionService
     */
    private $sut;

    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $this->sut = new LicenceSectionService();
        $this->sut->setServiceLocator($this->serviceManager);
    }

    /**
     * @group section_service
     * @group licence_section_service
     */
    public function testAlterForm()
    {
        $form = new Form();

        $oldElement = new Text('foo');

        $oldFieldset = new Fieldset('form-actions');
        $oldFieldset->add($oldElement);

        $form->add($oldFieldset);

        $this->assertSame($oldFieldset, $form->get('form-actions'));

        $form = $this->sut->alterForm($form);

        $this->assertNotSame($oldFieldset, $form->get('form-actions'));
    }

    /**
     * @group section_service
     * @group licence_section_service
     */
    public function testGetLicenceData()
    {
        $id = 3;
        $licenceData = array(
            'id' => 3,
            'version' => 1,
            'niFlag' => 'Y',
            'licNo' => '234fgh',
            'goodsOrPsv' => array(
                'id' => 'lcat_gv'
            ),
            'licenceType' => array(
                'id' => 'ltyp_sn'
            ),
            'organisation' => array(
                'type' => array(
                    'id' => 'org_t_rc'
                )
            )
        );

        $this->attachRestHelperMock();
        $this->sut->setIdentifier($id);

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Licence', 'GET', $id)
            ->will($this->returnValue($licenceData));

        $output = $this->sut->getLicenceData();

        $this->assertEquals($licenceData, $output);

        // Test that when we get it from cache it doesn't make another rest call
        $output2 = $this->sut->getLicenceData();

        $this->assertEquals($licenceData, $output2);
    }

    /**
     * @group section_service
     * @group licence_section_service
     */
    public function testGetLicenceType()
    {

        $id = 3;
        $licenceData = array(
            'id' => 3,
            'version' => 1,
            'niFlag' => 'Y',
            'licNo' => '234fgh',
            'goodsOrPsv' => array(
                'id' => 'lcat_gv'
            ),
            'licenceType' => array(
                'id' => 'ltyp_sn'
            ),
            'organisation' => array(
                'type' => array(
                    'id' => 'org_t_rc'
                )
            )
        );

        $this->attachRestHelperMock();
        $this->sut->setIdentifier($id);

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Licence', 'GET', $id)
            ->will($this->returnValue($licenceData));

        $output = $this->sut->getLicenceType();

        $this->assertEquals('ltyp_sn', $output);

        // Test that when we get it from cache it doesn't make another rest call
        $output2 = $this->sut->getLicenceType();
        $this->assertEquals('ltyp_sn', $output2);
    }

    /**
     * @group section_service
     * @group licence_section_service
     */
    public function testIsPsv()
    {

        $id = 3;
        $licenceData = array(
            'id' => 3,
            'version' => 1,
            'niFlag' => 'Y',
            'licNo' => '234fgh',
            'goodsOrPsv' => array(
                'id' => 'lcat_gv'
            ),
            'licenceType' => array(
                'id' => 'ltyp_sn'
            ),
            'organisation' => array(
                'type' => array(
                    'id' => 'org_t_rc'
                )
            )
        );

        $this->attachRestHelperMock();
        $this->sut->setIdentifier($id);

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Licence', 'GET', $id)
            ->will($this->returnValue($licenceData));

        $output = $this->sut->isPsv();

        $this->assertEquals(false, $output);

        // Test that when we get it from cache it doesn't make another rest call
        $output2 = $this->sut->isPsv();
        $this->assertEquals(false, $output2);
    }

    /**
     * @group section_service
     * @group licence_section_service
     */
    public function testSingleRestCall()
    {
        $id = 3;
        $licenceData = array(
            'id' => 3,
            'version' => 1,
            'niFlag' => 'Y',
            'licNo' => '234fgh',
            'goodsOrPsv' => array(
                'id' => 'lcat_gv'
            ),
            'licenceType' => array(
                'id' => 'ltyp_sn'
            ),
            'organisation' => array(
                'type' => array(
                    'id' => 'org_t_rc'
                )
            )
        );

        $this->attachRestHelperMock();
        $this->sut->setIdentifier($id);

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Licence', 'GET', $id)
            ->will($this->returnValue($licenceData));

        $output = $this->sut->getLicenceData();

        $this->assertEquals($licenceData, $output);

        $output = $this->sut->getLicenceType();

        $this->assertEquals('ltyp_sn', $output);

        $output = $this->sut->isPsv();

        $this->assertEquals(false, $output);
    }
}
