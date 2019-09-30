<?php

namespace CommonTest\Service\Qa;

use Common\Form\Elements\Types\RadioVertical;
use Common\Service\Qa\FieldsetFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;

/**
 * FieldsetFactoryTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FieldsetFactoryTest extends MockeryTestCase
{
    private $fieldsetFactory;

    public function setUp()
    {
        $this->fieldsetFactory = new FieldsetFactory();
    }

    public function testCreateForEcmtShortTermRestrictedCountries()
    {
        $name = 'fieldset45';
        $fieldset = $this->fieldsetFactory->create($name, 'ecmt_st_restricted_countries');
        $this->assertInstanceOf(RadioVertical::class, $fieldset);
        $this->assertEquals($name, $fieldset->getName());
    }

    public function testCreateForOther()
    {
        $name = 'fieldset62';
        $fieldset = $this->fieldsetFactory->create($name, 'radio');
        $this->assertInstanceOf(Fieldset::class, $fieldset);
        $this->assertEquals($name, $fieldset->getName());
    }
}
