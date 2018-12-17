<?php

namespace CommonTest\Form\Elements\Types;

use Common\Form\Elements\Types\RadioYesNo;

/**
 * RadioYesNoTest
 */
class RadioYesNoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RadioYesNo
     */
    private $sut;

    public function setup()
    {
        $this->sut = new RadioYesNo();
    }

    public function testInit()
    {
        $this->sut->init();

        $this->assertArraySubset(
            ['Y' => ['label' => 'Yes', 'value' => 'Y'], 'N' => ['label' => 'No', 'value' => 'N']],
            $this->sut->getValueOptions()
        );
    }
}
