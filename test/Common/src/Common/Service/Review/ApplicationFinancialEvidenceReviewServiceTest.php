<?php

/**
 * Application Financial Evidence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\ApplicationFinancialEvidenceReviewService;

/**
 * Application Financial Evidence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationFinancialEvidenceReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationFinancialEvidenceReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromDataWithoutDocs()
    {
        $data = [
            'id' => 123,
            'financialEvidenceUploaded' => 'N'
        ];

        $expected = [
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-financial-evidence-no-of-vehicles',
                        'value' => 987
                    ],
                    [
                        'label' => 'application-review-financial-evidence-required-finance',
                        'value' => '£123,456'
                    ],
                    [
                        'label' => 'application-review-financial-evidence-evidence',
                        'noEscape' => true,
                        'value' => 'application-review-financial-evidence-evidence-post-translated'
                    ]
                ]
            ]
        ];

        $mockFinancialEvidence = m::mock();
        $this->sm->setService('ApplicationFinancialEvidenceAdapter', $mockFinancialEvidence);
        $mockFinancialEvidence->shouldReceive('getTotalNumberOfAuthorisedVehicles')
            ->with(123)
            ->andReturn(987)
            ->shouldReceive('getRequiredFinance')
            ->with(123)
            ->andReturn(123456);

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataWithDocs()
    {
        $data = [
            'id' => 123,
            'financialEvidenceUploaded' => 'Y'
        ];

        $stubbedDocuments = [
            [
                'filename' => 'foo.txt'
            ],
            [
                'filename' => 'bar.txt'
            ]
        ];

        $expected = [
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-financial-evidence-no-of-vehicles',
                        'value' => 987
                    ],
                    [
                        'label' => 'application-review-financial-evidence-required-finance',
                        'value' => '£123,456'
                    ],
                    [
                        'label' => 'application-review-financial-evidence-evidence',
                        'noEscape' => true,
                        'value' => 'foo.txt<br>bar.txt'
                    ]
                ]
            ]
        ];

        $mockFinancialEvidence = m::mock();
        $this->sm->setService('ApplicationFinancialEvidenceAdapter', $mockFinancialEvidence);
        $mockFinancialEvidence->shouldReceive('getTotalNumberOfAuthorisedVehicles')
            ->with(123)
            ->andReturn(987)
            ->shouldReceive('getRequiredFinance')
            ->with(123)
            ->andReturn(123456)
            ->shouldReceive('getDocuments')
            ->with(123)
            ->andReturn($stubbedDocuments);

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
