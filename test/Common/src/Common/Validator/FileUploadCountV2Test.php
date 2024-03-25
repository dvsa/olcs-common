<?php

namespace CommonTest\Validator;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Validator\FileUploadCountV2;

/**
 * Class FileUploadCountTest
 * @package CommonTest\Validator
 */
class FileUploadCountV2Test extends MockeryTestCase
{
    /**
     *
     * @param type $expected
     * @param type $min
     * @param type $context
     *
     * @dataProvider dataProviderTestIsValid
     */
    public function testIsValid($expected, $min, $context): void
    {
        $sut = new FileUploadCountV2(['min' => $min]);

        $valid = $sut->isValid(null, ['list' => $context]);

        $this->assertSame($expected, $valid);
    }

    public function dataProviderTestIsValid()
    {
        return [
            // isValid, min, context
            [true, 0, []],
            [false, 1, []],
            [false, 2, []],
            [true, 0, ['file1']],
            [true, 1, ['file1']],
            [false, 2, ['file1']],
            [true, 0, ['file1', 'file2']],
            [true, 1, ['file1', 'file2']],
            [true, 2, ['file1', 'file2']],
            [true, 2, ['file1', 'file2', 'file3']],
        ];
    }
}
