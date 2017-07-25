<?php

namespace CommonTest\Data\Mapper\Lva;

use PHPUnit_Framework_TestCase;
use Common\Data\Mapper\Lva\FinancialEvidence;
use Common\RefData;

/**
 * Financial Evidence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FinancialEvidenceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider mapFromResultProvider
     */
    public function testMapFromResult($input, $expected)
    {
        $this->assertEquals($expected, FinancialEvidence::mapFromResult($input));
    }

    public function mapFromResultProvider()
    {
        return [
            [
                [
                    'financialEvidenceUploaded' => RefData::AD_UPLOAD_NOW,
                    'id' => 1,
                    'version' => 2
                ],
                [
                    'id'       => 1,
                    'version'  => 2,
                    'evidence' => [
                        'uploadNowRadio' => RefData::AD_UPLOAD_NOW,
                        'uploadLaterRadio' => null,
                        'sendByPostRadio' => null
                    ]
                ]
            ],
            [
                [
                    'financialEvidenceUploaded' => RefData::AD_UPLOAD_LATER,
                    'id' => 1,
                    'version' => 2
                ],
                [
                    'id'       => 1,
                    'version'  => 2,
                    'evidence' => [
                        'uploadNowRadio' => null,
                        'uploadLaterRadio' => RefData::AD_UPLOAD_LATER,
                        'sendByPostRadio' => null
                    ]
                ]
            ],
            [
                [
                    'financialEvidenceUploaded' => RefData::AD_POST,
                    'id' => 1,
                    'version' => 2
                ],
                [
                    'id'       => 1,
                    'version'  => 2,
                    'evidence' => [
                        'uploadNowRadio' => null,
                        'uploadLaterRadio' => null,
                        'sendByPostRadio' => RefData::AD_POST,
                    ]
                ]
            ],
            [
                [
                    'financialEvidenceUploaded' => null,
                    'id' => 1,
                    'version' => 2
                ],
                [
                    'id'       => 1,
                    'version'  => 2,
                    'evidence' => [
                        'uploadNowRadio' => RefData::AD_UPLOAD_NOW,
                        'uploadLaterRadio' => null,
                        'sendByPostRadio' => null,
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider mapFromPostProvider
     */
    public function testMapFromPost($input, $expected)
    {
        $this->assertEquals($expected, FinancialEvidence::mapFromPost($input));
    }

    public function mapFromPostProvider()
    {
        return [
            [
                [
                    'evidence' => [
                        'uploadNow' => RefData::AD_UPLOAD_NOW,
                        'files' => [
                            'list' => [
                                'foo'
                            ]
                        ],
                        'bar' => 'cake'
                    ]
                ],
                [
                    'evidence' => [
                        'uploadNowRadio' => RefData::AD_UPLOAD_NOW,
                        'uploadLaterRadio' => null,
                        'sendByPostRadio' => null,
                        'uploadedFileCount' => 1,
                        'uploadNow' => RefData::AD_UPLOAD_NOW,
                        'files' => [
                            'list' => [
                                'foo'
                            ]
                        ],
                        'bar' => 'cake'
                    ]
                ]
            ],
            [
                [
                    'evidence' => [
                        'uploadNow' => RefData::AD_UPLOAD_LATER,
                        'files' => [
                            'list' => [
                                'foo'
                            ]
                        ],
                        'bar' => 'cake'
                    ]
                ],
                [
                    'evidence' => [
                        'uploadNowRadio' => null,
                        'uploadLaterRadio' => RefData::AD_UPLOAD_LATER,
                        'sendByPostRadio' => null,
                        'uploadedFileCount' => 1,
                        'uploadNow' => RefData::AD_UPLOAD_LATER,
                        'files' => [
                            'list' => [
                                'foo'
                            ]
                        ],
                        'bar' => 'cake'
                    ]
                ]
            ],
            [
                [
                    'evidence' => [
                        'uploadNow' => RefData::AD_POST,
                        'files' => [
                            'list' => [
                                'foo'
                            ]
                        ],
                        'bar' => 'cake'
                    ]
                ],
                [
                    'evidence' => [
                        'uploadNowRadio' => null,
                        'uploadLaterRadio' => null,
                        'sendByPostRadio' => RefData::AD_POST,
                        'uploadedFileCount' => 1,
                        'uploadNow' => RefData::AD_POST,
                        'files' => [
                            'list' => [
                                'foo'
                            ]
                        ],
                        'bar' => 'cake'
                    ]
                ]
            ],
            [
                [],
                []
            ]
        ];
    }

    /**
     * @dataProvider mapFromFormProvider
     */
    public function testMapFromForm($input, $expected)
    {
        $this->assertEquals($expected, FinancialEvidence::mapFromForm($input));
    }

    public function mapFromFormProvider()
    {
        return [
            [
                [
                    'id' => 1,
                    'version' => 2,
                    'evidence' => [
                        'uploadNowRadio' => RefData::AD_UPLOAD_NOW,
                        'uploadLaterRadio' => null,
                        'sendByPost' => null
                    ]
                ],
                [
                    'id' => 1,
                    'version' => 2,
                    'financialEvidenceUploaded' => RefData::AD_UPLOAD_NOW
                ]
            ],
            [
                [
                    'id' => 1,
                    'version' => 2,
                    'evidence' => [
                        'uploadNowRadio' => null,
                        'uploadLaterRadio' => RefData::AD_UPLOAD_LATER,
                        'sendByPost' => null
                    ]
                ],
                [
                    'id' => 1,
                    'version' => 2,
                    'financialEvidenceUploaded' => RefData::AD_UPLOAD_LATER
                ]
            ],
            [
                [
                    'id' => 1,
                    'version' => 2,
                    'evidence' => [
                        'uploadNowRadio' => null,
                        'uploadLaterRadio' => null,
                        'sendByPost' => RefData::AD_POST
                    ]
                ],
                [
                    'id' => 1,
                    'version' => 2,
                    'financialEvidenceUploaded' => RefData::AD_POST
                ]
            ]
        ];
    }
}
