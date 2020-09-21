<?php
declare(strict_types=1);

namespace CommonTest\Form\Elements\Types;

use Common\Form\Elements\Custom\VehicleVrm;
use Common\Form\Elements\Types\PlainText;
use Common\Form\Elements\Types\VrmSearch;
use PHPUnit\Framework\TestCase;
use Zend\Form\Element\Button;

class VrmSearchTest extends TestCase
{

    public function testVrmSearchCreate()
    {
        $sut = new VrmSearch();

        $this->assertInstanceOf(PlainText::class, $sut->get(VrmSearch::ELEMENT_HINT_NAME));
        $this->assertInstanceOf(VehicleVrm::class, $sut->get(VrmSearch::ELEMENT_INPUT_NAME));
        $this->assertInstanceOf(Button::class, $sut->get(VrmSearch::ELEMENT_SUBMIT_NAME));
    }
}
