<?php

namespace PermitsTest\Data\Mapper\Permits;

use Common\Form\Form;
use Common\Form\Elements\Custom\NoOfPermits as NoOfPermitsElement;
use Common\Form\Elements\Types\Html as HtmlElement;
use Common\Data\Mapper\Permits\MultilateralNoOfPermits;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Common\Service\Helper\TranslationHelperService;
use Mockery as m;
use RuntimeException;
use Zend\Form\Element\Submit;
use Zend\Form\Fieldset;

/**
 * MultilateralNoOfPermitsTest
 */
class MultilateralNoOfPermitsTest extends TestCase
{
    private $form;

    public function setUp()
    {
        $submitButton = new Submit();
        $submitButton->setName('SubmitButton');
        $submitButton->setValue('Save and continue');

        $saveAndReturnButton = new Submit();
        $saveAndReturnButton->setName('SaveAndReturnButton');
        $saveAndReturnButton->setValue('Save and return to overview');

        $submitFieldset = new Fieldset('Submit');
        $submitFieldset->add($submitButton);
        $submitFieldset->add($saveAndReturnButton);

        $this->form = new Form();
        $this->form->add($submitFieldset);
    }

    public function testMapForFormOptions()
    {
        $form = $this->form;

        $translatedGuidanceText = 'translatedGuidanceText';

        $for2018Hint = '4 is the maximum number you can apply for. 8 permits have already been issued.';
        $for2019Hint = '11 is the maximum you can apply for. 1 permit has already been issued.';
        $for2020Hint = '12 is the maximum you can apply for.';

        $label2018Html = 'Number of permits for 2018';
        $label2019Html = 'Number of permits for 2019';
        $label2020Html = 'Number of permits for 2020';

        $for2021Html = 'Number of permits for 2021<br>You cannot request any more permits. All 12 have been issued.';

        $translationHelperService = m::mock(TranslationHelperService::class);

        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.multilateral.no-of-permits.guidance',
                [12]
            )
            ->andReturn($translatedGuidanceText);

        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.no-of-permits.multiple-issued',
                [4, 8]
            )
            ->andReturn($for2018Hint);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.no-of-permits.one-issued',
                [11]
            )
            ->andReturn($for2019Hint);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.no-of-permits.none-issued',
                [12]
            )
            ->andReturn($for2020Hint);

        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.multilateral.no-of-permits.all-issued',
                [2021, 12]
            )
            ->andReturn($for2021Html);

        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.multilateral.no-of-permits.for-year',
                [2018]
            )
            ->andReturn($label2018Html);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.multilateral.no-of-permits.for-year',
                [2019]
            )
            ->andReturn($label2019Html);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.multilateral.no-of-permits.for-year',
                [2020]
            )
            ->andReturn($label2020Html);

        $data = [
            'feePerPermit' => [
                'feePerPermit' => 'Not applicable'
            ],
            'application' => [
                'irhpPermitType' => [
                    'id' => 5
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
                                'country' => null,
                            ]
                        ]
                    ],
                    [
                        'permitsRequired' => 3,
                        'irhpPermitWindow' => [
                            'irhpPermitStock' => [
                                'id' => 8,
                                'validFrom' => '2019-12-31',
                                'country' => null,
                            ]
                        ]
                    ],
                    [
                        'permitsRequired' => null,
                        'irhpPermitWindow' => [
                            'irhpPermitStock' => [
                                'id' => 9,
                                'validFrom' => '2018-08-31',
                                'country' => null,
                            ]
                        ]
                    ],
                    [
                        'permitsRequired' => 4,
                        'irhpPermitWindow' => [
                            'irhpPermitStock' => [
                                'id' => 10,
                                'validFrom' => '2021-12-01',
                                'country' => null,
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
            ],
        ];

        $data = MultilateralNoOfPermits::mapForFormOptions(
            $data,
            $form,
            $translationHelperService,
            'application',
            'maxPermitsByStock',
            'feePerPermit'
        );

        $this->assertCount(0, $form->getElements());
        $formFieldsets = $form->getFieldsets();
        $this->assertArrayHasKey('fields', $formFieldsets);

        $fields = $formFieldsets['fields'];
        $this->assertCount(0, $fields->getElements());
        $fieldsFieldsets = $fields->getFieldsets();
        $this->assertCount(1, $fieldsFieldsets);
        $this->assertArrayHasKey('permitsRequired', $fieldsFieldsets);

        $permitsRequiredElements = $fieldsFieldsets['permitsRequired']->getElements();
        $this->assertEquals(['2018', '2019', '2020', '2021'], array_keys($permitsRequiredElements));

        $element2018 = $permitsRequiredElements['2018'];
        $this->assertInstanceOf(NoOfPermitsElement::class, $element2018);
        $this->assertEquals($label2018Html, $element2018->getLabel());
        $this->assertEquals($for2018Hint, $element2018->getOption('hint'));
        $this->assertEquals('govuk-hint', $element2018->getOption('hint-class'));
        $this->assertNull($element2018->getValue());

        $element2019 = $permitsRequiredElements['2019'];
        $this->assertInstanceOf(NoOfPermitsElement::class, $element2019);
        $this->assertEquals($label2019Html, $element2019->getLabel());
        $this->assertEquals($for2019Hint, $element2019->getOption('hint'));
        $this->assertEquals('govuk-hint', $element2019->getOption('hint-class'));
        $this->assertEquals(3, $element2019->getValue());

        $element2020 = $permitsRequiredElements['2020'];
        $this->assertInstanceOf(NoOfPermitsElement::class, $element2020);
        $this->assertEquals($label2020Html, $element2020->getLabel());
        $this->assertEquals($for2020Hint, $element2020->getOption('hint'));
        $this->assertEquals('govuk-hint', $element2020->getOption('hint-class'));
        $this->assertEquals(7, $element2020->getValue());

        $element2021 = $permitsRequiredElements['2021'];
        $this->assertInstanceOf(HtmlElement::class, $element2021);
        $this->assertEquals('<p class="no-more-available">' . $for2021Html. '</p>', $element2021->getValue());

        $this->assertArrayHasKey('Submit', $formFieldsets);
        $submitFieldsetElements = $formFieldsets['Submit']->getElements();
        $this->assertEquals(['SubmitButton', 'SaveAndReturnButton'], array_keys($submitFieldsetElements));

        $submitButton = $submitFieldsetElements['SubmitButton'];
        $this->assertEquals(
            'SubmitButton',
            $submitButton->getName()
        );
        $this->assertEquals(
            'Save and continue',
            $submitButton->getValue()
        );

        $saveAndReturnButton = $submitFieldsetElements['SaveAndReturnButton'];
        $this->assertEquals(
            'SaveAndReturnButton',
            $saveAndReturnButton->getName()
        );
        $this->assertEquals(
            'Save and return to overview',
            $saveAndReturnButton->getValue()
        );

        $this->assertArrayHasKey('guidance', $data);
        $this->assertEquals(
            [
                'value' => $translatedGuidanceText,
                'disableHtmlEscape' => true
            ],
            $data['guidance']
        );

        $this->assertArrayHasKey('browserTitle', $data);
        $this->assertEquals(
            'permits.page.multilateral.no-of-permits.browser.title',
            $data['browserTitle']
        );

        $this->assertArrayHasKey('question', $data);
        $this->assertEquals(
            'permits.page.multilateral.no-of-permits.question',
            $data['question']
        );
    }

    public function testAllAllowablePermitsIssued()
    {
        $form = $this->form;

        $translatedGuidanceText = 'translatedGuidanceText';

        $for2018Html = 'Number of permits for 2018<br>You cannot request any more permits. All 12 have been issued.';
        $for2019Html = 'Number of permits for 2019<br>You cannot request any more permits. All 12 have been issued.';
        $for2020Html = 'Number of permits for 2020<br>You cannot request any more permits. All 12 have been issued.';

        $translationHelperService = m::mock(TranslationHelperService::class);

        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.multilateral.no-of-permits.guidance',
                [12]
            )
            ->andReturn($translatedGuidanceText);

        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.multilateral.no-of-permits.all-issued',
                [2018, 12]
            )
            ->andReturn($for2018Html);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.multilateral.no-of-permits.all-issued',
                [2019, 12]
            )
            ->andReturn($for2019Html);
        $translationHelperService->shouldReceive('translateReplace')
            ->with(
                'permits.page.multilateral.no-of-permits.all-issued',
                [2020, 12]
            )
            ->andReturn($for2020Html);

        $data = [
            'feePerPermit' => [
                'feePerPermit' => 'Not applicable'
            ],
            'application' => [
                'irhpPermitType' => [
                    'id' => 5
                ],
                'licence' => [
                    'totAuthVehicles' => 12
                ],
                'irhpPermitApplications' => [
                    [
                        'permitsRequired' => null,
                        'irhpPermitWindow' => [
                            'irhpPermitStock' => [
                                'id' => 7,
                                'validFrom' => '2020-03-30',
                                'country' => null,
                            ]
                        ]
                    ],
                    [
                        'permitsRequired' => null,
                        'irhpPermitWindow' => [
                            'irhpPermitStock' => [
                                'id' => 9,
                                'validFrom' => '2019-08-31',
                                'country' => null,
                            ]
                        ]
                    ],
                    [
                        'permitsRequired' => null,
                        'irhpPermitWindow' => [
                            'irhpPermitStock' => [
                                'id' => 10,
                                'validFrom' => '2018-12-01',
                                'country' => null,
                            ]
                        ]
                    ]
                ]
            ],
            'maxPermitsByStock' => [
                'result' => [
                    7 => 0,
                    9 => 0,
                    10 => 0
                ]
            ],
        ];

        $data = MultilateralNoOfPermits::mapForFormOptions(
            $data,
            $form,
            $translationHelperService,
            'application',
            'maxPermitsByStock',
            'feePerPermit'
        );

        $this->assertCount(0, $form->getElements());
        $formFieldsets = $form->getFieldsets();
        $this->assertArrayHasKey('fields', $formFieldsets);

        $fields = $formFieldsets['fields'];
        $this->assertCount(0, $fields->getElements());
        $fieldsFieldsets = $fields->getFieldsets();
        $this->assertCount(1, $fieldsFieldsets);
        $this->assertArrayHasKey('permitsRequired', $fieldsFieldsets);

        $permitsRequiredElements = $fieldsFieldsets['permitsRequired']->getElements();
        $this->assertEquals(['2018', '2019', '2020'], array_keys($permitsRequiredElements));

        $element2018 = $permitsRequiredElements['2018'];
        $this->assertInstanceOf(HtmlElement::class, $element2018);
        $this->assertEquals('<p class="no-more-available">' . $for2018Html. '</p>', $element2018->getValue());

        $element2019 = $permitsRequiredElements['2019'];
        $this->assertInstanceOf(HtmlElement::class, $element2019);
        $this->assertEquals('<p class="no-more-available">' . $for2019Html. '</p>', $element2019->getValue());

        $element2020 = $permitsRequiredElements['2020'];
        $this->assertInstanceOf(HtmlElement::class, $element2020);
        $this->assertEquals('<p class="no-more-available">' . $for2020Html. '</p>', $element2020->getValue());

        $this->assertArrayHasKey('Submit', $formFieldsets);
        $submitFieldsetElements = $formFieldsets['Submit']->getElements();
        $this->assertEquals(['SaveAndReturnButton'], array_keys($submitFieldsetElements));

        $saveAndReturnButton = $submitFieldsetElements['SaveAndReturnButton'];
        $this->assertEquals(
            'CancelButton',
            $saveAndReturnButton->getName()
        );
        $this->assertEquals(
            'permits.page.no-of-permits.button.cancel',
            $saveAndReturnButton->getValue()
        );
    }

    public function testExceptionOnIncorrectPermitType()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Permit type 7 is not supported by this mapper');

        $data = [
            'application' => [
                'irhpPermitType' => [
                    'id' => 7
                ]
            ]
        ];

        $form = new Form();
        $translationHelperService = m::mock(TranslationHelperService::class);

        $data = MultilateralNoOfPermits::mapForFormOptions(
            $data,
            $form,
            $translationHelperService,
            'application',
            'maxPermitsByStock',
            'feePerPermit'
        );
    }
}
