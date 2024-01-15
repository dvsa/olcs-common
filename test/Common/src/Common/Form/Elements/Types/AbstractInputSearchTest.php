<?php
declare(strict_types=1);

namespace CommonTest\Form\Elements\Types;

use Common\Form\Elements\Custom\Vrm;
use Common\Form\Elements\Types\AbstractInputSearch;
use PHPUnit\Framework\TestCase;

class AbstractInputSearchTest extends TestCase
{

    public function testSetAndGetMessages()
    {
        $sut = new class extends AbstractInputSearch {
            public $hint;
            public $input;
            public $submit;

            protected function addHint()
            {
                $this->hint = 'hint_is_set';
            }

            protected function addInput()
            {
                $this->input = 'input_is_set';
            }

            protected function addSubmit()
            {
                $this->submit = 'submit_is_set';
            }
        };

        $sut->setMessages([
            0 => [
                'vrm' => 'error',
            ],
        ]);
        $messages = $sut->getMessages();
        $this->assertSame(['vrm' => 'error'], $messages);
        $this->assertSame(['class' => 'lookup'], $sut->getAttributes());
        $this->assertSame('hint_is_set', $sut->hint);
        $this->assertSame('input_is_set', $sut->input);
        $this->assertSame('submit_is_set', $sut->submit);
    }
}
