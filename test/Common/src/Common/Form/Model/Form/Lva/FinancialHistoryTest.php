<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormTest;
use Olcs\TestHelpers\FormTester\Data\Object as F;
use CommonTest\Bootstrap;

/**
 * Class FinancialHistoryTest
 *
 * @package OlcsTest\FormTest
 * @group ComponentTests
 * @group FormTests
 */
class FinancialHistoryTest extends AbstractFormTest
{
    protected $formName = '\Common\Form\Model\Form\Lva\FinancialHistory';

    protected function getServiceManager()
    {
        return Bootstrap::getRealServiceManager();
    }

    protected function getFormData()
    {
        $bankruptContext = new F\Context(new F\Stack(['data', 'bankrupt']), 'Y');

        return [
            new F\Test(
                new F\Stack(['data', 'insolvencyDetails']),
                new F\Value(F\Value::VALID, ''),
                new F\Value(F\Value::VALID, 'foo'),
                new F\Value(F\Value::INVALID, '', $bankruptContext), // this was failing (OLCS-6899)
                new F\Value(F\Value::INVALID, 'not long enough', $bankruptContext),
                new F\Value(F\Value::VALID, str_pad('', 200, 'x'), $bankruptContext)
            ),
        ];
    }
}
