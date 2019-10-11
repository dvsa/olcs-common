<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\QaForm;
use Common\Service\Qa\IsValidHandlerInterface;
use Common\Service\Qa\DataHandlerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Element\Text;

/**
 * QaFormTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class QaFormTest extends MockeryTestCase
{
    public function testSetDataQaElementPresent()
    {
        $qaFieldset12 = new Fieldset('fieldset12');
        $qaFieldset12->add(new Text('qaElement'));

        $qaFieldset50 = new Fieldset('fieldset50');
        $qaFieldset50->add(new Text('qaElement'));

        $qaFieldset84 = new Fieldset('fieldset84');
        $qaFieldset84->add(new Text('qaElement'));

        $qaFieldset = new Fieldset('qa');

        $qaFieldset->add($qaFieldset12);
        $qaFieldset->add($qaFieldset50);
        $qaFieldset->add($qaFieldset84);

        $form = new QaForm();
        $form->add($qaFieldset);

        $unprocessedData = [
            'qa' => [
                'fieldset12' => [
                    'qaElement' => 'test'
                ]
            ]
        ];

        $expectedProcessedData = [
            'qa' => [
                'fieldset12' => [
                    'qaElement' => 'test'
                ],
                'fieldset50' => [
                    'qaElement' => ''
                ],
                'fieldset84' => [
                    'qaElement' => ''
                ]
            ]
        ];

        $form->setData($unprocessedData);
        $form->isValid();

        $this->assertEquals(
            $expectedProcessedData,
            $form->getData()
        );
    }

    public function testSetDataQaElementNotPresent()
    {
        $qaFieldset12 = new Fieldset('fieldset12');
        $qaFieldset12->add(new Text('qaElement'));

        $qaFieldset50 = new Fieldset('fieldset50');
        $qaFieldset50->add(new Text('qaElement'));

        $qaFieldset84 = new Fieldset('fieldset84');
        $qaFieldset84->add(new Text('qaElement'));

        $qaFieldset = new Fieldset('qa');

        $qaFieldset->add($qaFieldset12);
        $qaFieldset->add($qaFieldset50);
        $qaFieldset->add($qaFieldset84);

        $form = new QaForm();
        $form->add($qaFieldset);

        $unprocessedData = [];

        $expectedProcessedData = [
            'qa' => [
                'fieldset12' => [
                    'qaElement' => ''
                ],
                'fieldset50' => [
                    'qaElement' => ''
                ],
                'fieldset84' => [
                    'qaElement' => ''
                ]
            ]
        ];

        $form->setData($unprocessedData);
        $form->isValid();

        $this->assertEquals(
            $expectedProcessedData,
            $form->getData()
        );
    }

    public function testSetDataForRedisplayWithHandler()
    {
        $formControlType = 'form_control_type';

        $applicationStep = [
            'type' => $formControlType
        ];

        $data = [
            'prop1' => 'value1',
            'prop2' => 'value2'
        ];

        $qaForm = m::mock(QaForm::class)->makePartial();
        $qaForm->shouldReceive('setData')
            ->with($data)
            ->once()
            ->globally()
            ->ordered();

        $dataHandler = m::mock(DataHandlerInterface::class);
        $dataHandler->shouldReceive('setData')
            ->with($qaForm)
            ->once()
            ->globally()
            ->ordered();

        $qaForm->registerDataHandler($formControlType, $dataHandler);
        $qaForm->setApplicationStep($applicationStep);

        $qaForm->setDataForRedisplay($data);
    }

    public function testSetDataForRedisplayWithoutHandler()
    {
        $formControlType = 'form_control_type';

        $applicationStep = [
            'type' => $formControlType
        ];

        $data = [
            'prop1' => 'value1',
            'prop2' => 'value2'
        ];

        $qaForm = m::mock(QaForm::class)->makePartial();
        $qaForm->shouldReceive('setData')
            ->with($data)
            ->once()
            ->globally()
            ->ordered();

        $qaForm->setApplicationStep($applicationStep);
        $qaForm->setDataForRedisplay($data);
    }

    public function testIsValidParentReturnsFalse()
    {
        $qaForm = m::mock(QaForm::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $qaForm->shouldReceive('callParentIsValid')
            ->andReturn(false);

        $this->assertFalse($qaForm->isValid());
    }

    /**
     * @dataProvider dpIsValidParentReturnsTrueWithHandler
     */
    public function testIsValidParentReturnsTrueWithHandler($isValidHandlerResponse)
    {
        $formControlType = 'form_control_type';

        $applicationStep = [
            'type' => $formControlType
        ];

        $qaForm = m::mock(QaForm::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $qaForm->shouldReceive('callParentIsValid')
            ->andReturn(true);

        $isValidHandler = m::mock(IsValidHandlerInterface::class);
        $isValidHandler->shouldReceive('isValid')
            ->with($qaForm)
            ->once()
            ->andReturn($isValidHandlerResponse);

        $qaForm->setApplicationStep($applicationStep);
        $qaForm->registerIsValidHandler($formControlType, $isValidHandler);

        $this->assertEquals(
            $isValidHandlerResponse,
            $qaForm->isValid()
        );
    }

    public function dpIsValidParentReturnsTrueWithHandler()
    {
        return [
            [true],
            [false]
        ];
    }

    public function testIsValidParentReturnsTrueWithoutHandler()
    {
        $formControlType = 'form_control_type';

        $applicationStep = [
            'type' => $formControlType
        ];

        $qaForm = m::mock(QaForm::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $qaForm->shouldReceive('callParentIsValid')
            ->andReturn(true);

        $qaForm->setApplicationStep($applicationStep);

        $this->assertTrue($qaForm->isValid());
    }

    public function testSetGetApplicationStep()
    {
        $applicationStep = [
            'prop1' => 'value1',
            'prop2' => 'value2',
        ];

        $qaForm = new QaForm();
        $qaForm->setApplicationStep($applicationStep);

        $this->assertEquals(
            $applicationStep,
            $qaForm->getApplicationStep()
        );
    }

    public function testGetQuestionFieldsetData()
    {
        $fieldset87Data = [
            'qaElement' => 'qaElementValue',
            'fieldset87Prop2' => 'fieldset87Value2'
        ];

        $data = [
            'qa' => [
                'myname' => [
                    'mynameProp1' => 'mynameValue1',
                    'mynameProp2' => 'mynameValue2',
                ],
                'fieldset87' => $fieldset87Data,
                'Submit' => [
                    'fieldset87Prop1' => 'fieldset87Value1',
                    'fieldset87Prop2' => 'fieldset87Value2'
                ],
            ]
        ];

        $qaFieldset = new Fieldset('qa');

        $questionFieldset = new Fieldset('fieldset87');

        $qaFieldset->add(new Fieldset('myname'));
        $qaFieldset->add($questionFieldset);
        $qaFieldset->add(new Fieldset('Submit'));

        $qaForm = new QaForm();
        $qaForm->add($qaFieldset);
        $qaForm->setData($data);

        $this->assertEquals(
            $fieldset87Data,
            $qaForm->getQuestionFieldsetData()
        );
    }

    public function testGetQuestionFieldset()
    {
        $qaForm = new QaForm();

        $qaFieldset = new Fieldset('qa');

        $questionFieldset = new Fieldset('fieldset87');

        $qaFieldset->add(new Fieldset('myname'));
        $qaFieldset->add($questionFieldset);
        $qaFieldset->add(new Fieldset('Submit'));

        $qaForm->add($qaFieldset);

        $this->assertSame(
            $questionFieldset,
            $qaForm->getQuestionFieldset()
        );
    }
}
