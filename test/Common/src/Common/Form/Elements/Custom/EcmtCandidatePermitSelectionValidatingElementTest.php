<?php

namespace CommonTest\Form\Elements\Custom;

use Common\Form\Elements\Custom\EcmtCandidatePermitSelectionValidatingElement;
use Common\Form\Elements\Validators\EcmtCandidatePermitSelectionValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\Form\Element\Hidden;
use Laminas\Validator\Callback;

/**
 * EcmtCandidatePermitSelectionValidatingElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtCandidatePermitSelectionValidatingElementTest extends MockeryTestCase
{
    const ELEMENT_NAME = 'elementName123';

    private $ecmtCandidatePermitSelectionValidatingElement;

    public function setUp(): void
    {
        $this->ecmtCandidatePermitSelectionValidatingElement = new EcmtCandidatePermitSelectionValidatingElement(
            self::ELEMENT_NAME
        );
    }

    public function testGetInputSpecification()
    {
        $expectedInputSpecification = [
            'name' => self::ELEMENT_NAME,
            'continue_if_empty' => true,
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => [
                            EcmtCandidatePermitSelectionValidator::class,
                            'validate'
                        ],
                        'messages' => [
                            Callback::INVALID_VALUE => 'permits.page.irhp.candidate-permit-selection.error'
                        ]
                    ],
                ],
            ],
        ];

        $this->assertEquals(
            $expectedInputSpecification,
            $this->ecmtCandidatePermitSelectionValidatingElement->getInputSpecification()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(
            Hidden::class,
            $this->ecmtCandidatePermitSelectionValidatingElement
        );
    }
}
