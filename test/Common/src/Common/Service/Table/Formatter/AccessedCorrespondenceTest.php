<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\AccessedCorrespondence;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\Service\Table\Formatter\AccessedCorrespondence
 */
class AccessedCorrespondenceTest extends MockeryTestCase
{
    protected $urlHelper;
    protected $translator;

    protected function setUp(): void
    {
        $this->urlHelper = m::mock(UrlHelperService::class);
        $this->translator = m::mock(TranslatorDelegator::class);
    }

    /**
     * @dataProvider formatProvider
     */
    public function testFormat($data, $isNew, $expected)
    {

        $this->urlHelper->shouldReceive('fromRoute')
            ->with(
                'correspondence/access',
                [
                    'correspondenceId' => $data['correspondence']['id'],
                ]
            )
            ->andReturn('LICENCE_URL');

        if ($isNew) {
            $this->translator->shouldReceive('translate')->once()->andReturn('unit_New');
        }

        $sut = new AccessedCorrespondence($this->urlHelper, $this->translator);
        static::assertEquals($expected, $sut->format($data, []));
    }


    public function formatProvider()
    {
        return [
            [
                'data' => [
                    'correspondence' => [
                        'id' => 1,
                        'accessed' => 'N',
                        'document' => [
                            'description' => 'Description',
                            'filename' => 'filename.doc'
                        ],
                    ],
                ],
                'isNew' => true,
                'expect' => '<a class="govuk-link" href="LICENCE_URL"><b>Description (doc)</b></a>' .
                    '<span class="status green">unit_New</span> ',
            ],
            [
                'data' => [
                    'correspondence' => [
                        'id' => 1,
                        'accessed' => 'Y',
                        'document' => [
                            'description' => 'Description',
                            'filename' => 'filename.doc'
                        ],
                    ],
                ],
                'isNew' => false,
                'expect' => '<a class="govuk-link" href="LICENCE_URL"><b>Description (doc)</b></a>',
            ],
            [
                'data' => [
                    'correspondence' => [
                        'id' => 1,
                        'accessed' => 'Y',
                        'document' => [
                            'description' => 'Description',
                            'filename' => 'filename'
                        ],
                    ],
                ],
                'isNew' => false,
                'expect' => '<a class="govuk-link" href="LICENCE_URL"><b>Description</b></a>',
            ],
        ];
    }
}
