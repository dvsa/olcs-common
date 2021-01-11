<?php

namespace CommonTest\Service\Qa\Custom\Ecmt;

use DMS\PHPUnitExtensions\ArraySubset\Assert;
use Common\Service\Qa\Custom\Ecmt\RestrictedCountriesMultiCheckboxFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Form\Element\MultiCheckbox;

/**
 * RestrictedCountriesMultiCheckboxFactoryTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RestrictedCountriesMultiCheckboxFactoryTest extends MockeryTestCase
{
    public function testCreate()
    {
        $name = 'yesContent';

        $expectedOptions = [
            'label' => 'markup-ecmt-restricted-countries-list-label',
            'label_attributes' => [
                'class' => 'govuk-label govuk-checkboxes__label'
            ]
        ];

        $expectedAttributes = [
            'class' => 'input--trips govuk-checkboxes__input',
            'id' => 'RestrictedCountriesList',
            'allowWrap' => true,
            'data-container-class' => 'form-control__container',
            'aria-label' => 'permits.page.restricted-countries.hint'
        ];

        $restrictedCountriesMultiCheckboxFactory = new RestrictedCountriesMultiCheckboxFactory();
        $multiCheckbox = $restrictedCountriesMultiCheckboxFactory->create($name);

        $this->assertInstanceOf(MultiCheckbox::class, $multiCheckbox);
        $this->assertEquals($name, $multiCheckbox->getName());
        Assert::assertArraySubset($expectedOptions, $multiCheckbox->getOptions());
        Assert::assertArraySubset($expectedAttributes, $multiCheckbox->getAttributes());
    }
}
