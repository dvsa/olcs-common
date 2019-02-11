<?php

namespace PermitsTest\Data\Mapper\Permits;

use Common\Form\Form;
use Common\Form\Elements\Custom\NoOfPermits as NoOfPermitsElement;
use Common\Form\Elements\Types\Html as HtmlElement;
use Common\Data\Mapper\Permits\NoOfPermits;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Common\Service\Helper\TranslationHelperService;
use Mockery as m;
use RuntimeException;

/**
 * NoOfPermitsTest
 */
class NoOfPermitsTest extends TestCase
{
    public function testMapForFormOptions()
    {
        $form = new Form();

        $translatedGuidanceText = 'translatedGuidanceText';
        $italy2020Hint = '12 is the maximum you can apply for.';
        $italy2019Hint = '11 is the maximum you can apply for. 1 permit has already been issued.';
        $france2018Hint = '4 is the maximum number you can apply for. 8 permits have already been issued.';
        $france2019Html = 'for 2019<br>You cannot request any more permits. All 12 have been issued.';

        $for2018Html = 'for 2018';
        $for2019Html = 'for 2019';
        $for2020Html = 'for 2020';

        $translationHelperService = m::mock(TranslationHelperService::class);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.bilateral.no-of-permits.guidance',
                [12, 45]
            )
            ->andReturn($translatedGuidanceText);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.bilateral.no-of-permits.none-issued',
                [12]
            )
            ->andReturn($italy2020Hint);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.bilateral.no-of-permits.one-issued',
                [11]
            )
            ->andReturn($italy2019Hint);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.bilateral.no-of-permits.multiple-issued',
                [4, 8]
            )
            ->andReturn($france2018Hint);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.bilateral.no-of-permits.all-issued',
                [2019, 12]
            )
            ->andReturn($france2019Html);

        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.bilateral.no-of-permits.for-year',
                [2018]
            )
            ->andReturn($for2018Html);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.bilateral.no-of-permits.for-year',
                [2019]
            )
            ->andReturn($for2019Html);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.bilateral.no-of-permits.for-year',
                [2020]
            )
            ->andReturn($for2020Html);

        $data = [
            'application' => [
                'irhpPermitType' => [
                    'id' => 4
                ],
                'licence' => [
                    'totAuthVehicles' => 12
                ],
                'irhpPermitApplications' => [
                    [
                        'permitsRequired' => 7,
                        'irhpPermitWindow' => [
                            'irhpPermitStock' => [
                                'id' => 7,
                                'validFrom' => '2020-03-30',
                                'country' => [
                                    'id' => 'IT',
                                    'countryDesc' => 'Italy'
                                ]
                            ]
                        ]
                    ],
                    [
                        'permitsRequired' => 7,
                        'irhpPermitWindow' => [
                            'irhpPermitStock' => [
                                'id' => 8,
                                'validFrom' => '2019-12-31',
                                'country' => [
                                    'id' => 'IT',
                                    'countryDesc' => 'Italy'
                                ]
                            ]
                        ]
                    ],
                    [
                        'permitsRequired' => null,
                        'irhpPermitWindow' => [
                            'irhpPermitStock' => [
                                'id' => 9,
                                'validFrom' => '2018-08-31',
                                'country' => [
                                    'id' => 'FR',
                                    'countryDesc' => 'France'
                                ]
                            ]
                        ]
                    ],
                    [
                        'permitsRequired' => 4,
                        'irhpPermitWindow' => [
                            'irhpPermitStock' => [
                                'id' => 10,
                                'validFrom' => '2019-12-01',
                                'country' => [
                                    'id' => 'FR',
                                    'countryDesc' => 'France'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'maxPermitsByStock' => [
                'result' => [
                    7 => 12,
                    8 => 11,
                    9 => 4,
                    10 => 0
                ]
            ]
        ];

        $data = NoOfPermits::mapForFormOptions(
            $data,
            $form,
            $translationHelperService,
            'application',
            'maxPermitsByStock'
        );

        $this->assertCount(0, $form->getElements());
        $formFieldsets = $form->getFieldsets();
        $this->assertCount(1, $formFieldsets);
        $this->assertArrayHasKey('fields', $formFieldsets);

        $fields = $formFieldsets['fields'];
        $this->assertCount(0, $fields->getElements());
        $fieldsFieldsets = $fields->getFieldsets();
        $this->assertCount(1, $fieldsFieldsets);
        $this->assertArrayHasKey('permitsRequired', $fieldsFieldsets);

        $permitsRequired = $fieldsFieldsets['permitsRequired'];
        $permitsRequiredFieldsets = $permitsRequired->getFieldsets();
        $this->assertEquals(['FR', 'IT'], array_keys($permitsRequiredFieldsets));

        $franceFieldset = $permitsRequiredFieldsets['FR'];
        $this->assertEquals('France', $franceFieldset->getLabel());
        $this->assertCount(0, $franceFieldset->getFieldsets());

        $franceElements = $franceFieldset->getElements();
        $this->assertEquals(['2018', '2019', 'FRhorizontalrule'], array_keys($franceElements));

        $franceElement2018 = $franceElements['2018'];
        $this->assertInstanceOf(NoOfPermitsElement::class, $franceElement2018);
        $this->assertEquals($for2018Html, $franceElement2018->getLabel());
        $this->assertEquals($france2018Hint, $franceElement2018->getOption('hint'));
        $this->assertEquals('govuk-hint', $franceElement2018->getOption('hint-class'));
        $this->assertNull($franceElement2018->getValue());

        $franceElement2018Attributes = $franceElement2018->getAttributes();
        $this->assertArrayHasKey('max', $franceElement2018Attributes);
        $this->assertEquals(4, $franceElement2018Attributes['max']);

        $franceElement2019 = $franceElements['2019'];
        $this->assertInstanceOf(HtmlElement::class, $franceElement2019);
        $this->assertEquals('<p class="no-more-available">' . $france2019Html. '</p>', $franceElement2019->getValue());

        $italyFieldset = $permitsRequiredFieldsets['IT'];
        $this->assertEquals('Italy', $italyFieldset->getLabel());
        $this->assertCount(0, $italyFieldset->getFieldsets());

        $franceElementHorizontalRule = $franceElements['FRhorizontalrule'];
        $this->assertEquals(
            '<hr class="govuk-section-break govuk-section-break--visible">',
            $franceElementHorizontalRule->getValue()
        );

        $italyElements = $italyFieldset->getElements();
        $this->assertEquals(['2019','2020','IThorizontalrule'], array_keys($italyElements));

        $italyElement2019 = $italyElements['2019'];
        $this->assertEquals($for2019Html, $italyElement2019->getLabel());
        $this->assertEquals($italy2019Hint, $italyElement2019->getOption('hint'));
        $this->assertEquals('govuk-hint', $italyElement2019->getOption('hint-class'));
        $this->assertEquals(7, $italyElement2019->getValue());

        $italyElement2019Attributes = $italyElement2019->getAttributes();
        $this->assertInstanceOf(NoOfPermitsElement::class, $italyElement2019);
        $this->assertArrayHasKey('max', $italyElement2019Attributes);
        $this->assertEquals(11, $italyElement2019Attributes['max']);

        $italyElement2020 = $italyElements['2020'];
        $this->assertEquals($for2020Html, $italyElement2020->getLabel());
        $this->assertEquals($italy2020Hint, $italyElement2020->getOption('hint'));
        $this->assertEquals('govuk-hint', $italyElement2020->getOption('hint-class'));
        $this->assertEquals(7, $italyElement2020->getValue());

        $italyElement2020Attributes = $italyElement2020->getAttributes();
        $this->assertInstanceOf(NoOfPermitsElement::class, $italyElement2020);
        $this->assertArrayHasKey('max', $italyElement2020Attributes);
        $this->assertEquals(12, $italyElement2020Attributes['max']);

        $italyElementHorizontalRule = $italyElements['IThorizontalrule'];
        $this->assertEquals(
            '<hr class="govuk-section-break govuk-section-break--visible">',
            $italyElementHorizontalRule->getValue()
        );

        $this->assertArrayHasKey('guidance', $data);

        $this->assertEquals(
            $data['guidance'],
            [
                'value' => $translatedGuidanceText,
                'disableHtmlEscape' => true
            ]
        );
    }

    public function testExceptionOnIncorrectPermitType()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Permit type 3 is not supported by this mapper');

        $data = [
            'application' => [
                'irhpPermitType' => [
                    'id' => 3
                ]
            ]
        ];

        $form = new Form();
        $translationHelperService = m::mock(TranslationHelperService::class);

        $data = NoOfPermits::mapForFormOptions($data, $form, $translationHelperService, 'application', 'maxPermitsByStock');
    }
}
