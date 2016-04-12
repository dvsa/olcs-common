<?php

namespace CommonTest\Validator;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Validator\FileUploadCount;

/**
 * Class FileUploadCountTest
 * @package CommonTest\Validator
 */
class FileUploadCountTest extends MockeryTestCase
{
    public function testSetOptions()
    {
        $sut = new FileUploadCount(['min' => '23']);
        $this->assertEquals(23, $sut->getMin());
    }

    /**
     *
     * @param type $expected
     * @param type $min
     * @param type $context
     *
     * @dataProvider dataProviderTestIsValid
     */
    public function testIsValid($expected, $min, $context)
    {
        $sut = new FileUploadCount(['min' => $min]);

        $valid = $sut->isValid(null, ['files' => ['list' => $context]]);

        $this->assertSame($expected, $valid);

    }


    public function dataProviderTestIsValid()
    {
        return [
            // isValid, min, context
            [false, 2, []],
            [false, 2, [1]],
            [true, 2, [1, 2]],
            [true, 1, [1]],
            [true, 2, [1, 2, 3, 4, 5]],
        ];
    }
}
