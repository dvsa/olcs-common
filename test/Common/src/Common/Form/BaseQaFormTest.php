<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\BaseQaForm;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;
use Zend\Form\Element\Text;

/**
 * BaseQaFormTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class BaseQaFormTest extends MockeryTestCase
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

        $this->form = new BaseQaForm();
        $this->form->add($qaFieldset);
    }

    public function testUpdateDataForQaElementPresent()
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

        $processedData = $this->form->updateDataForQa($unprocessedData);
        $this->assertEquals($expectedProcessedData, $processedData);
    }

    public function testUpdateDataForQaElementNotPresent()
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

        $processedData = $this->form->updateDataForQa($unprocessedData);
        $this->assertEquals($expectedProcessedData, $processedData);
    }
}
