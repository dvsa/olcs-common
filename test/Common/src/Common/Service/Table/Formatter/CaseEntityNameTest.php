<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\CaseEntityName;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Service\Table\Formatter\CaseEntityName
 */
class CaseEntityNameTest extends MockeryTestCase
{
    /**
     * @dataProvider  dpTestFormat
     */
    public function testFormat($data, $expect)
    {
        static::assertSame($expect, CaseEntityName::format($data));
    }

    public function dpTestFormat()
    {
        return [
            'not-dta' => [
                'data' => [
                    'caseType' => [
                        'id' => \Common\RefData::CASE_TYPE_TM,
                    ],
                    'transportManager' => [
                        'homeCd' => [
                            'person' => null,
                        ],
                    ],
                ],
                'expect' => '',
            ],
            'tm' => [
                'data' => [
                    'caseType' => [
                        'id' => \Common\RefData::CASE_TYPE_TM,
                    ],
                    'transportManager' => [
                        'homeCd' => [
                            'person' => [
                                'title' => [
                                    'description' => 'unit_TitleDesc',
                                ],
                                'forename' => 'unit_ForeN',
                                'familyName' => 'unit_FamilyN',
                            ],
                        ],
                    ],
                ],
                'expect' => 'unit_TitleDesc unit_ForeN unit_FamilyN',
            ],
            'lic|app' => [
                'data' => [
                    'caseType' => [
                        'id' => \Common\RefData::CASE_TYPE_LICENCE,
                    ],
                    'organisation' => [
                        'name' => 'unit_Org',
                    ],
                ],
                'expect' => 'unit_Org',
            ],
        ];
    }
}
