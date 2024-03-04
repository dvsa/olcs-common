<?php

/**
 * Cant Increase Validator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\CantIncreaseValidator;

/**
 * Cant Increase Validator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CantIncreaseValidatorTest extends \PHPUnit\Framework\TestCase
{
    protected $validator;

    public function setUp(): void
    {
        $this->validator = new CantIncreaseValidator();
    }

    /**
     * @group validator
     * @dataProvider providerIsValid
     */
    public function testIsValid($previousValue, $newValue, $expected)
    {
        $this->validator->setPreviousValue($previousValue);

        $this->assertEquals($expected, $this->validator->isValid($newValue));
    }

    /**
     * @group validator
     */
    public function testSetGenericMessage()
    {
        $message = 'test message';

        $this->validator->setGenericMessage($message);

        $this->validator->setPreviousValue(9);

        $this->assertFalse($this->validator->isValid(10));

        $messages = $this->validator->getMessages();

        $this->assertEquals(1, count($messages));

        $messageObject = current($messages);

        $this->assertInstanceOf(\Common\Form\Elements\Validators\Messages\ValidationMessageInterface::class, $messageObject);

        $this->assertEquals($message, $messageObject->getMessage());
        $this->assertFalse($messageObject->shouldTranslate());
        $this->assertFalse($messageObject->shouldEscape());
    }

    public function providerIsValid()
    {
        return [
            [
                10,
                10,
                true
            ],
            [
                10,
                11,
                false
            ],
            [
                10,
                9,
                true
            ],
            [
                10,
                '11',
                false
            ],
            [
                10,
                '9',
                true
            ],
            [
                0,
                1,
                false
            ],
            [
                0,
                0,
                true
            ]
        ];
    }
}
