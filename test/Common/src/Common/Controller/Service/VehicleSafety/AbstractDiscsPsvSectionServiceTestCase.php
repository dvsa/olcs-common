<?php

/**
 * Abstract Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\VehicleSafety;

use CommonTest\Controller\Service\AbstractSectionServiceTestCase;

/**
 * Abstract Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractDiscsPsvSectionServiceTestCase extends AbstractSectionServiceTestCase
{
    /**
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testGetFormTableData()
    {
        $id = 3;
        $table = '';

        // Ceased disc should be removed, and the 3rd disc should be Pending
        $expected = array(
            array(
                'id' => 2,
                'discNo' => '123AB',
                'issuedDate' => '2014-01-01 10:10:00',
                'ceasedDate' => null,
                'isCopy' => 'N'
            ),
            array(
                'id' => 4,
                'discNo' => 'Pending',
                'issuedDate' => null,
                'ceasedDate' => null,
                'isCopy' => 'N'
            ),
            array(
                'id' => 5,
                'discNo' => '',
                'issuedDate' => '2014-01-01 10:10:00',
                'ceasedDate' => null,
                'isCopy' => 'N'
            )
        );

        $this->attachRestHelperMock();
        $this->mockFormTableRestCall($id);

        $output = $this->sut->getFormTableData($id, $table);

        $this->assertEquals($expected, $output);
    }

    /**
     * Mock the form table rest call
     */
    protected function mockFormTableRestCall($id)
    {
        $response = array(
            'psvDiscs' => array(
                array(
                    'id' => 2,
                    'discNo' => '123AB',
                    'issuedDate' => '2014-01-01 10:10:00',
                    'ceasedDate' => null,
                    'isCopy' => 'N'
                ),
                array(
                    'id' => 3,
                    'discNo' => '123ABC',
                    'issuedDate' => '2014-01-01 10:10:00',
                    'ceasedDate' => '2014-02-01 10:10:00',
                    'isCopy' => 'N'
                ),
                array(
                    'id' => 4,
                    'discNo' => null,
                    'issuedDate' => null,
                    'ceasedDate' => null,
                    'isCopy' => 'N'
                ),
                // @NOTE this should never happen, if a disc is issued it should have a number
                //  but if for whatever reason this happens, the discNo is set to ''
                array(
                    'id' => 5,
                    'discNo' => null,
                    'issuedDate' => '2014-01-01 10:10:00',
                    'ceasedDate' => null,
                    'isCopy' => 'N'
                )
            )
        );

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Licence', 'GET', $id)
            ->will($this->returnValue($response));
    }

    /**
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testLoad()
    {
        $this->assertEquals(array(), $this->sut->load(3));
    }

    /**
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testProcessLoad()
    {
        $id = 2;
        $this->sut->setIdentifier($id);
        $expected = array(
            'data' => array(
                'validDiscs' => 2,
                'pendingDiscs' => 1
            )
        );

        $this->attachRestHelperMock();
        $this->mockFormTableRestCall($id);

        $output = $this->sut->processLoad(array());

        $this->assertEquals($expected, $output);
    }

    /**
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testProcessActionLoad()
    {
        $id = 3;
        $this->sut->setIdentifier($id);

        $response = array(
            'id' => 3,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 6,
            'totAuthLargeVehicles' => 9,
            'psvDiscs' => array(
                array(
                    'id' => 1,
                    'ceasedDate' => null
                ),
                array(
                    'id' => 2,
                    'ceasedDate' => '2014-01-01 10:10:00'
                ),
                array(
                    'id' => 3,
                    'ceasedDate' => '2014-01-02 10:10:00'
                )
            )
        );

        $this->attachRestHelperMock();
        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $expected = array(
            'data' => array(
                'id' => 3,
                'totalAuth' => 18,
                'discCount' => 1
            )
        );

        $output = $this->sut->processActionLoad(array());

        $this->assertEquals($expected, $output);
    }

    /**
     * @NOTE Make sure save doesn't do anything, we never save any info from the main form
     *
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testSave()
    {
        $this->assertNull($this->sut->save(array()));
    }

    /**
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testReplaceLoad()
    {
        $id = array(1, 2, 3, 76, 34);
        $expected = array('data' => array('id' => '1,2,3,76,34'));

        $this->assertEquals($expected, $this->sut->replaceLoad($id));
    }

    /**
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testVoidLoad()
    {
        $id = array(1, 2, 3, 76, 34);
        $expected = array('data' => array('id' => '1,2,3,76,34'));

        $this->assertEquals($expected, $this->sut->voidLoad($id));
    }

    /**
     * Check that alterform disables all elements of the data fieldset
     *
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testAlterForm()
    {
        $button1 = new \Zend\Form\Element\Submit('addAnother');
        $button2 = new \Zend\Form\Element\Submit('otherButton');

        $formActionsFieldset = new \Zend\Form\Fieldset('form-actions');
        $formActionsFieldset->add($button1)->add($button2);

        $input1 = new \Zend\Form\Element\Text('foo');
        $input2 = new \Zend\Form\Element\Text('bar');
        $input3 = new \Zend\Form\Element\Text('cake');

        $dataFieldset = new \Zend\Form\Fieldset('data');
        $dataFieldset->add($input1);
        $dataFieldset->add($input2);

        $otherFieldset = new \Zend\Form\Fieldset('other');
        $otherFieldset->add($input3);

        $form = new \Zend\Form\Form();
        $form->add($formActionsFieldset);
        $form->add($dataFieldset);
        $form->add($otherFieldset);

        $this->assertTrue($form->has('form-actions'));
        $this->assertNull($input1->getAttribute('disabled'));
        $this->assertNull($input2->getAttribute('disabled'));
        $this->assertNull($input3->getAttribute('disabled'));

        $this->sut->alterForm($form);

        $this->assertEquals('disabled', $input1->getAttribute('disabled'));
        $this->assertEquals('disabled', $input2->getAttribute('disabled'));
        $this->assertNull($input3->getAttribute('disabled'));

        return $form;
    }

    /**
     * Check that alter action form removes form-actions
     *
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testAlterActionForm()
    {
        $button1 = new \Zend\Form\Element\Submit('addAnother');
        $button2 = new \Zend\Form\Element\Submit('otherButton');

        $formActionsFieldset = new \Zend\Form\Fieldset('form-actions');
        $formActionsFieldset->add($button1)->add($button2);

        $form = new \Zend\Form\Form();
        $form->add($formActionsFieldset);

        $this->assertTrue($form->get('form-actions')->has('addAnother'));

        $this->sut->alterActionForm($form);

        $this->assertFalse($form->get('form-actions')->has('addAnother'));
    }

    /**
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testActionSave()
    {
        $data = array(
            'data' => array(
                'id' => 1,
                'additionalDiscs' => 3
            )
        );
        $service = null;

        $repeatResponse = array(
            array('licence' => 1, 'isCopy' => 'N'),
            array('licence' => 1, 'isCopy' => 'N'),
            array('licence' => 1, 'isCopy' => 'N')
        );

        $expectedData = array(
            array('licence' => 1, 'isCopy' => 'N'),
            array('licence' => 1, 'isCopy' => 'N'),
            array('licence' => 1, 'isCopy' => 'N'),
            '_OPTIONS_' => array('multiple' => true)
        );

        // Check we repeat the data
        $mockDataHelper = $this->getMock('\stdClass', array('arrayRepeat'));
        $mockDataHelper->expects($this->once())
            ->method('arrayRepeat')
            ->with(array('licence' => 1, 'isCopy' => 'N'), 3)
            ->will($this->returnValue($repeatResponse));

        $this->ensureSuccessMessageIsAdded();

        $this->mockHelperService('DataHelper', $mockDataHelper);

        $this->attachRestHelperMock();
        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('PsvDisc', 'POST', $expectedData);

        $this->sut->actionSave($data, $service);
    }

    /**
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testReplaceSave()
    {
        $id = 2;
        $date = date('Y-m-d H:i:s');

        $this->sut->setIdentifier($id);

        $data = array(
            'data' => array(
                'id' => '1,2,3'
            )
        );

        $repeatResponse = array(
            array('licence' => $id, 'isCopy' => 'Y'),
            array('licence' => $id, 'isCopy' => 'Y'),
            array('licence' => $id, 'isCopy' => 'Y')
        );

        $expectedCeaseData = array(
            array(
                'id' => 1,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            array(
                'id' => 2,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            array(
                'id' => 3,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            '_OPTIONS_' => array('multiple' => true)
        );

        $expectedData = array(
            array('licence' => $id, 'isCopy' => 'Y'),
            array('licence' => $id, 'isCopy' => 'Y'),
            array('licence' => $id, 'isCopy' => 'Y'),
            '_OPTIONS_' => array('multiple' => true)
        );

        // Check we repeat the data
        $mockDataHelper = $this->getMock('\stdClass', array('arrayRepeat'));
        $mockDataHelper->expects($this->once())
            ->method('arrayRepeat')
            ->with(array('licence' => $id, 'isCopy' => 'Y'), 3)
            ->will($this->returnValue($repeatResponse));

        $this->ensureSuccessMessageIsAdded();

        $this->mockHelperService('DataHelper', $mockDataHelper);

        $this->attachRestHelperMock();
        $this->mockRestHelper->expects($this->at(0))
            ->method('makeRestCall')
            ->with('PsvDisc', 'PUT', $expectedCeaseData);

        $this->mockRestHelper->expects($this->at(1))
            ->method('makeRestCall')
            ->with('PsvDisc', 'POST', $expectedData);

        $this->sut->replaceSave($data);
    }

    /**
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testVoidSave()
    {
        $id = 2;
        $date = date('Y-m-d H:i:s');

        $this->sut->setIdentifier($id);

        $data = array(
            'data' => array(
                'id' => '4,5,6'
            )
        );

        $expectedCeaseData = array(
            array(
                'id' => 4,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            array(
                'id' => 5,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            array(
                'id' => 6,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            '_OPTIONS_' => array('multiple' => true)
        );

        $this->ensureSuccessMessageIsAdded();

        $this->attachRestHelperMock();
        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('PsvDisc', 'PUT', $expectedCeaseData);

        $this->sut->voidSave($data);
    }

    /**
     * Shared logic to assert a success messages is added
     */
    protected function ensureSuccessMessageIsAdded()
    {
        // Check we add a success message
        $mockFlashMessenger = $this->getMock('\stdClass', array('addSuccessMessage'));
        $mockFlashMessenger->expects($this->once())
            ->method('addSuccessMessage');

        $this->mockHelperService('FlashMessengerHelper', $mockFlashMessenger);
    }
}
