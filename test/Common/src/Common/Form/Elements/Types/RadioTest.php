<?php

namespace CommonTest\Form\Elements\Types;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Types\Radio;

/**
 * RadioTest
 */
class RadioTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Radio
     */
    private $sut;

    public function setup()
    {
        $this->sut = new Radio();
    }

    public function testSetName()
    {
        $this->sut->setName('FOO');
        $this->assertSame('FOO', $this->sut->getAttribute('id'));
    }

    public function testSetNameIdAlreadySet()
    {
        $this->sut->setAttribute('id', 'NOT-FOO');
        $this->sut->setName('FOO');
        $this->assertSame('NOT-FOO', $this->sut->getAttribute('id'));
    }

    public function testSetValueOptions()
    {
        $this->sut->init();

        $this->sut->setValueOptions(
            ['A' => 'aaa', 'B' => 'bbb']
        );

        $valueOptions = $this->sut->getValueOptions();

        $this->assertArraySubset(
            [
                'A' => [
                    'label' => 'aaa',
                    'value' => 'A',
                    'wrapper_attributes' => [
                        'class' => 'govuk-radios__item',
                    ],
                    'attributes' => [
                        'class' => 'govuk-radios__input',
                    ],
                    'label_attributes' => [
                        'class' => 'govuk-label govuk-radios__label',
                    ],
                    'hint_attributes' => [
                        'class' => 'govuk-hint govuk-radios__hint',
                    ],
                ],
                'B' => [
                    'label' => 'bbb',
                    'value' => 'B',
                    'wrapper_attributes' => [
                        'class' => 'govuk-radios__item',
                    ],
                    'attributes' => [
                        'class' => 'govuk-radios__input',
                    ],
                    'label_attributes' => [
                        'class' => 'govuk-label govuk-radios__label',
                    ],
                    'hint_attributes' => [
                        'class' => 'govuk-hint govuk-radios__hint',
                    ],
                ],
            ],
            $valueOptions
        );

        $this->assertNotEmpty($valueOptions['A']['attributes']['id']);
        $this->assertNotEmpty($valueOptions['A']['attributes']['data-show-element']);
        $this->assertNotEmpty($valueOptions['A']['label_attributes']['for']);
        $this->assertStringEndsWith('_A', $valueOptions['A']['attributes']['id']);

        $this->assertNotEmpty($valueOptions['B']['attributes']['id']);
        $this->assertNotEmpty($valueOptions['B']['attributes']['data-show-element']);
        $this->assertNotEmpty($valueOptions['B']['label_attributes']['for']);
        $this->assertStringEndsWith('_B', $valueOptions['B']['attributes']['id']);

        $this->assertNotSame($valueOptions['A']['attributes']['id'], $valueOptions['B']['attributes']['id']);
    }
}
