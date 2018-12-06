<?php

/**
 * Generic Validation Message Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators\Messages;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Validators\Messages\GenericValidationMessage;

/**
 * Generic Validation Message Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericValidationMessageTest extends PHPUnit_Framework_TestCase
{
    protected $message;

    public function setUp()
    {
        $this->message = new GenericValidationMessage();
    }

    /**
     * @group validation_messages
     */
    public function testSetMessage()
    {
        $message = 'test message';

        $this->message->setMessage($message);
        $this->assertEquals($message, $this->message->getMessage());
    }

    /**
     * @group validation_messages
     */
    public function testToString()
    {
        $message = 'test message to string';

        $this->message->setMessage($message);

        $this->assertEquals($message, (string)$this->message);
    }

    /**
     * @group validation_messages
     * @dataProvider shouldProvider
     */
    public function testSetShouldTranslate($input, $expected)
    {
        $this->message->setShouldTranslate($input);

        $this->assertEquals($expected, $this->message->shouldTranslate());
    }

    /**
     * @group validation_messages
     * @dataProvider shouldProvider
     */
    public function testSetShouldEscape($input, $expected)
    {
        $this->message->setShouldEscape($input);

        $this->assertEquals($expected, $this->message->shouldEscape());
    }

    public function shouldProvider()
    {
        return array(
            array(
                true,
                true
            ),
            array(
                false,
                false
            ),
            array(
                1,
                true
            ),
            array(
                0,
                false
            ),
            array(
                '1',
                true
            ),
            array(
                '0',
                false
            ),
            array(
                'string',
                true
            ),
            array(
                '',
                false
            )
        );
    }
}
