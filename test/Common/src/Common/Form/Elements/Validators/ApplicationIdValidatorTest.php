<?php

/**
 * Test Application ID Validator
 *
 * @author Alexander Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\ApplicationIdValidator;
use PHPUnit_Framework_TestCase;
use Common\Service\Entity\LicenceEntityService;

/**
 * Test Application ID Validator
 *
 * @author Alexander Peshkov <alex.peshkov@valtech.co.uk>
 */
class ApplicationIdValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test isValid
     *
     * @group applicationIdValidator
     * @dataProvider providerIsValid
     */
    public function testIsValid($appData, $expected)
    {
        $validator = new ApplicationIdValidator();
        $validator->setAppData($appData);
        $this->assertEquals($expected, $validator->isValid(1));
    }

    /**
     * Provider for isValid
     *
     * @return array
     */
    public function providerIsValid()
    {
        return [
            [
                [],
                false
            ],
            [
                [
                    'licenceType' => ['id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL]
                ],
                true
            ],
            [
                [
                    'licenceType' => ['id' => LicenceEntityService::LICENCE_TYPE_RESTRICTED]
                ],
                false
            ],
        ];
    }
}
