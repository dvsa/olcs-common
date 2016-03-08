<?php


namespace CommonTest\Service\Table\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Common\Service\Table\Formatter\ConvictionDescription;

/**
 * Class ConvictionDescriptionTest
 * @package CommonTest\Service\Table\Formatter
 */
class ConvictionDescriptionTest extends TestCase
{
    /**
     * @dataProvider convictionDataProvider
     */
    public function testFormat($convictionData, $expected)
    {
        $sut = new ConvictionDescription();
        $result = $sut->format(
            $convictionData,
            []
        );

        $this->assertEquals($expected, $result);
    }

    public function convictionDataProvider()
    {
        return [
            [
                /* Test User defined category - should use categoryText */
                [
                    'convictionCategory' => [
                        'id' => 'conv_c_cat_1144',
                        'description' => 'User defined',
                    ],
                    'categoryText' => 'userdefinedtext_01234567890123456789'
                ],
                'userdefinedtext_01234567890123...'
            ],
            [
                /* Test empty category - should use categoryText */
                [
                    'convictionCategory' => null,
                    'categoryText' => 'userdefinedtext_01234567890123456789'
                ],
                'userdefinedtext_01234567890123...'
            ],
            [
                /* Test other category - should use categoryDescription */
                [
                    'convictionCategory' => [
                        'id' => 'conv_c_cat_someother',
                        'description' => 'Some Other_012345678901234567890123456789',
                    ],
                    'categoryText' => 'userdefinedtext_01234567890123456789'
                ],
                'Some Other_0123456789012345678...'
            ]
        ];
    }
}
