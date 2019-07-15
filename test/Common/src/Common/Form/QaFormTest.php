<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\QaForm;
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
    private $form;

    public function setUp()
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

        $this->form = new QaForm();
        $this->form->add($qaFieldset);
    }

    public function testSetDataQaElementPresent()
    {
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

        $this->form->setData($unprocessedData);
        $this->form->isValid();

        $this->assertEquals(
            $expectedProcessedData,
            $this->form->getData()
        );
    }

    public function testSetDataQaElementNotPresent()
    {
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

        $this->form->setData($unprocessedData);
        $this->form->isValid();

        $this->assertEquals(
            $expectedProcessedData,
            $this->form->getData()
        );
    }
}
